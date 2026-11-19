<?php
// admin/noticia_form.php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header("Location: index.php"); exit; }
require_once __DIR__ . '/config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$accion = $id ? 'Editar' : 'Agregar';
$custom_title = "$accion Publicación";
$mensaje = ''; $tipo_mensaje = '';

// Valores default cambiados (solo existe 'noticia' y 'comunicado')
$tipo = 'noticia'; $categoria = ''; $titulo = ''; $extracto = ''; $contenido = '';
$fecha_publicacion = date('Y-m-d'); $tiempo_lectura = ''; $activo = 1; 
$imagen_url_actual = ''; $imagenes_adicionales_json = '[]';

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM fmru_noticias WHERE id = ?");
    $stmt->execute([$id]);
    $n = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($n) {
        $tipo = $n['tipo']; $categoria = $n['categoria']; $titulo = $n['titulo'];
        $extracto = $n['extracto']; $contenido = $n['contenido'] ?? '';
        $fecha_publicacion = $n['fecha_publicacion']; $tiempo_lectura = $n['tiempo_lectura']; 
        $activo = $n['activo']; $imagen_url_actual = $n['imagen_url'];
        $imagenes_adicionales_json = $n['imagenes_adicionales'] ?? '[]';
    } else { header("Location: gestor_contenidos.php"); exit; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_post = recibir_post('tipo');
    $categoria_post = recibir_post('categoria');
    $titulo_post = recibir_post('titulo');
    $extracto_post = recibir_post('extracto');
    $contenido_post = recibir_post('contenido', true);
    $fecha_post = recibir_post('fecha_publicacion');
    $tiempo_post = recibir_post('tiempo_lectura');
    $activo_post = isset($_POST['activo']) ? 1 : 0;
    
    $imagen_final = $imagen_url_actual;
    $imagenes_extra_actuales = json_decode($imagenes_adicionales_json, true) ?: [];

    if (isset($_POST['limpiar_galeria']) && $_POST['limpiar_galeria'] === '1') {
        $imagenes_extra_actuales = [];
    }

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imagen']['tmp_name'];
        $fileName = $_FILES['imagen']['name'];
        $fileSize = $_FILES['imagen']['size'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $image_info = getimagesize($fileTmpPath);
        if ($image_info !== false && in_array($fileExtension, ['jpg', 'jpeg', 'png', 'webp'])) {
            if ($fileSize < 2 * 1024 * 1024) {
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $uploadFileDir = dirname(__DIR__) . '/assets/img/noticias/';
                if (!is_dir($uploadFileDir)) mkdir($uploadFileDir, 0755, true);
                if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                    $imagen_final = 'assets/img/noticias/' . $newFileName;
                }
            } else { $mensaje = 'Límite de 2MB excedido en portada.'; $tipo_mensaje = 'danger'; }
        }
    } elseif (!$id && empty($_FILES['imagen']['name']) && $tipo_post !== 'comunicado') {
        $mensaje = 'Las noticias requieren imagen de portada.'; $tipo_mensaje = 'danger';
    }

    if (empty($mensaje) && isset($_FILES['imagenes_adicionales']) && $_FILES['imagenes_adicionales']['error'][0] !== UPLOAD_ERR_NO_FILE) {
        $total_files = count($_FILES['imagenes_adicionales']['name']);
        for ($i = 0; $i < $total_files; $i++) {
            if ($_FILES['imagenes_adicionales']['error'][$i] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['imagenes_adicionales']['tmp_name'][$i];
                $fName = $_FILES['imagenes_adicionales']['name'][$i];
                $fSize = $_FILES['imagenes_adicionales']['size'][$i];
                $fExt = strtolower(pathinfo($fName, PATHINFO_EXTENSION));

                $img_info = getimagesize($tmpName);
                if ($img_info !== false && in_array($fExt, ['jpg', 'jpeg', 'png', 'webp']) && $fSize < 2 * 1024 * 1024) {
                    $newName = md5(time() . $fName . $i) . '.' . $fExt;
                    $dest = dirname(__DIR__) . '/assets/img/noticias/' . $newName;
                    if (move_uploaded_file($tmpName, $dest)) {
                        $imagenes_extra_actuales[] = 'assets/img/noticias/' . $newName;
                    }
                }
            }
        }
    }
    
    $imagenes_adicionales_final = json_encode($imagenes_extra_actuales);

    if (empty($mensaje)) {
        try {
            if ($id) {
                $sql = "UPDATE fmru_noticias SET tipo=?, categoria=?, titulo=?, extracto=?, contenido=?, fecha_publicacion=?, tiempo_lectura=?, imagen_url=?, imagenes_adicionales=?, activo=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$tipo_post, $categoria_post, $titulo_post, $extracto_post, $contenido_post, $fecha_post, $tiempo_post, $imagen_final, $imagenes_adicionales_final, $activo_post, $id]);
                $mensaje = 'Publicación actualizada.';
                $imagen_url_actual = $imagen_final;
                $imagenes_adicionales_json = $imagenes_adicionales_final;
            } else {
                $sql = "INSERT INTO fmru_noticias (tipo, categoria, titulo, extracto, contenido, fecha_publicacion, tiempo_lectura, imagen_url, imagenes_adicionales, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$tipo_post, $categoria_post, $titulo_post, $extracto_post, $contenido_post, $fecha_post, $tiempo_post, $imagen_final, $imagenes_adicionales_final, $activo_post]);
                $mensaje = 'Publicación creada exitosamente.';
                
                $tipo_post = 'noticia'; $categoria_post = ''; $titulo_post = ''; $extracto_post = ''; $contenido_post = '';
                $fecha_post = date('Y-m-d'); $tiempo_post = ''; $activo_post = 1; $imagen_url_actual = ''; $imagenes_adicionales_json = '[]';
            }
            $tipo_mensaje = 'success';
            $tipo = $tipo_post; $categoria = $categoria_post; $titulo = $titulo_post; $extracto = $extracto_post; $contenido = $contenido_post;
            $fecha_publicacion = $fecha_post; $tiempo_lectura = $tiempo_post; $activo = $activo_post;
        } catch (PDOException $e) { $mensaje = "Error: " . $e->getMessage(); $tipo_mensaje = 'danger'; }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $custom_title ?> | Admin FMRU</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SE AÑADIÓ BOOTSTRAP ICONS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>
<body class="admin-panel">
<div class="d-flex">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-grow-1">
        <?php include __DIR__ . '/partials/topbar.php'; ?>
        
        <div class="container-fluid p-4">
            <div class="mb-4"><a href="gestor_contenidos.php" class="btn btn-light border btn-sm shadow-sm">Volver</a></div>
            <?php if ($mensaje): ?><div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible"><?= $mensaje ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="form-card">
                        <form method="POST" enctype="multipart/form-data">
                            
                            <h5 class="fw-bold border-bottom pb-2 mb-4">Información Principal</h5>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tipo de Publicación</label>
                                    <select name="tipo" class="form-select" id="tipoSelect">
                                        <option value="noticia" <?= in_array($tipo, ['noticia','principal','secundaria']) ? 'selected' : '' ?>>Noticia</option>
                                        <option value="comunicado" <?= $tipo == 'comunicado' ? 'selected' : '' ?>>Nota de Prensa</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Categoría / Etiqueta</label>
                                    <input type="text" name="categoria" class="form-control" value="<?= e($categoria) ?>" required placeholder="Ej. SVNS, RWC 2027">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Título</label>
                                <input type="text" name="titulo" class="form-control" value="<?= e($titulo) ?>" required>
                            </div>

                            <div class="mb-4" id="extractoGroup">
                                <label class="form-label fw-bold">Extracto (Resumen corto)</label>
                                <textarea name="extracto" class="form-control" rows="2"><?= e($extracto) ?></textarea>
                            </div>

                            <h5 class="fw-bold border-bottom pb-2 mb-4">Cuerpo del Artículo</h5>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted">Contenido Completo de la Noticia</label>
                                <textarea name="contenido" class="form-control bg-light" rows="12" placeholder="Escribe o pega aquí el contenido completo."><?= e($contenido) ?></textarea>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Fecha de Publicación</label>
                                    <input type="date" name="fecha_publicacion" class="form-control" value="<?= e($fecha_publicacion) ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Tiempo de Lectura (Opcional)</label>
                                    <input type="text" name="tiempo_lectura" class="form-control" value="<?= e($tiempo_lectura) ?>" placeholder="Ej. 3'">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="activo" <?= $activo ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-bold">Publicar</label>
                                    </div>
                                </div>
                            </div>

                            <h5 class="fw-bold border-bottom pb-2 mb-4 mt-5">Imágenes y Multimedia</h5>

                            <div class="mb-4" id="imagenGroup">
                                <label class="form-label fw-bold">Imagen de Portada</label>
                                <?php if ($imagen_url_actual): ?>
                                    <div class="mb-3 bg-light p-3 text-center border rounded">
                                        <img src="../<?= e($imagen_url_actual) ?>" class="current-image-noticia shadow-sm" alt="Portada">
                                    </div>
                                <?php endif; ?>
                                <input class="form-control" type="file" name="imagen" id="imagenFile" accept="image/jpeg, image/png, image/webp">
                            </div>

                            <div class="mb-4 bg-light p-4 rounded border">
                                <label class="form-label fw-bold text-dark"><i class="bi bi-images me-2"></i>Galería Adicional (Fotos al final del artículo)</label>
                                
                                <?php 
                                $galeria = json_decode($imagenes_adicionales_json, true) ?: [];
                                if(!empty($galeria)): 
                                ?>
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <?php foreach($galeria as $img): ?>
                                            <img src="../<?= e($img) ?>" class="img-thumbnail object-fit-cover" style="width: 80px; height: 80px;">
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="limpiar_galeria" value="1" id="limpiarGaleria">
                                        <label class="form-check-label text-danger fw-bold" for="limpiarGaleria">
                                            Eliminar imágenes adicionales actuales al guardar
                                        </label>
                                    </div>
                                <?php endif; ?>
                                
                                <input class="form-control" type="file" name="imagenes_adicionales[]" multiple accept="image/jpeg, image/png, image/webp">
                            </div>

                            <div class="d-grid"><button type="submit" class="btn btn-success btn-lg mt-4" style="background-color:#116436;">Guardar Contenido</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('tipoSelect').addEventListener('change', function() {
        document.getElementById('imagenGroup').style.display = (this.value === 'comunicado') ? 'none' : 'block';
    });
    document.getElementById('tipoSelect').dispatchEvent(new Event('change'));
</script>
</body>
</html>