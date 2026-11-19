<?php
// admin/banner_form.php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$accion = $id ? 'Editar' : 'Agregar';
$custom_title = "$accion Banner";

$mensaje = '';
$tipo_mensaje = '';

// Variables por defecto
$titulo = '';
$subtitulo = '';
$activo = 1;
$imagen_url_actual = '';

// Lógica para obtener el último orden
if (!$id) {
    $stmtMax = $pdo->query("SELECT MAX(orden) FROM fmru_banners");
    $maxOrden = $stmtMax->fetchColumn();
    $orden = $maxOrden ? (int)$maxOrden + 1 : 1;
} else {
    $orden = 0; 
}

// Cargar datos si es edición
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM fmru_banners WHERE id = ?");
    $stmt->execute([$id]);
    $banner = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($banner) {
        $titulo = $banner['titulo'];
        $subtitulo = $banner['subtitulo'];
        $orden = (int)$banner['orden']; // Guardamos el orden original
        $activo = $banner['activo'];
        $imagen_url_actual = $banner['imagen_url'];
    } else {
        header("Location: ver_banners.php");
        exit;
    }
}

// Procesar Formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // OWASP: Recibir datos preservando acentos
    $titulo_post = recibir_post('titulo');
    $subtitulo_post = recibir_post('subtitulo');
    $orden_post = (int)$_POST['orden'];
    $activo_post = isset($_POST['activo']) ? 1 : 0;
    
    $imagen_final = $imagen_url_actual;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imagen']['tmp_name'];
        $fileName = $_FILES['imagen']['name'];
        $fileSize = $_FILES['imagen']['size'];
        
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        
        $image_info = getimagesize($fileTmpPath);
        
        if ($image_info !== false) {
            $mime = $image_info['mime'];
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];

            if (in_array($fileExtension, $allowedfileExtensions) && in_array($mime, $allowedMimeTypes)) {
                if ($fileSize < 2 * 1024 * 1024) { 
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = dirname(__DIR__) . '/assets/img/banner/';
                    
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }

                    $dest_path = $uploadFileDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $imagen_final = 'assets/img/banner/' . $newFileName;
                    } else {
                        $mensaje = 'Error al guardar la imagen. Verifica los permisos de escritura.';
                        $tipo_mensaje = 'danger';
                    }
                } else {
                    $mensaje = 'El archivo supera el límite de 2MB.';
                    $tipo_mensaje = 'danger';
                }
            } else {
                $mensaje = 'Formato no permitido. Solo JPG, PNG y WEBP.';
                $tipo_mensaje = 'danger';
            }
        } else {
            $mensaje = 'El archivo subido no es una imagen válida o está corrupto.';
            $tipo_mensaje = 'danger';
        }
    } elseif (!$id && empty($_FILES['imagen']['name'])) {
        $mensaje = 'Debes subir una imagen para el nuevo banner.';
        $tipo_mensaje = 'danger';
    }

    if (empty($mensaje)) {
        try {
            // INICIO DE TRANSACCIÓN SEGURA (OWASP)
            $pdo->beginTransaction(); 

            if ($id) {
                // 1. Lógica de Intercambio (Swap) para Edición
                if ($orden !== $orden_post) {
                    $stmtConflicto = $pdo->prepare("SELECT id FROM fmru_banners WHERE orden = ? AND id != ?");
                    $stmtConflicto->execute([$orden_post, $id]);
                    $id_conflicto = $stmtConflicto->fetchColumn();

                    if ($id_conflicto) {
                        $stmtSwap = $pdo->prepare("UPDATE fmru_banners SET orden = ? WHERE id = ?");
                        $stmtSwap->execute([$orden, $id_conflicto]);
                    }
                }

                $sql = "UPDATE fmru_banners SET titulo=?, subtitulo=?, imagen_url=?, orden=?, activo=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$titulo_post, $subtitulo_post, $imagen_final, $orden_post, $activo_post, $id]);
                
                $mensaje = 'Banner actualizado exitosamente.';
                $imagen_url_actual = $imagen_final;
            } else {
                // 2. Lógica de Desplazamiento (Shift) para Nuevos
                $stmtConflicto = $pdo->prepare("SELECT id FROM fmru_banners WHERE orden = ?");
                $stmtConflicto->execute([$orden_post]);
                
                if ($stmtConflicto->fetchColumn()) {
                    $stmtShift = $pdo->prepare("UPDATE fmru_banners SET orden = orden + 1 WHERE orden >= ?");
                    $stmtShift->execute([$orden_post]);
                }

                $sql = "INSERT INTO fmru_banners (titulo, subtitulo, imagen_url, orden, activo) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$titulo_post, $subtitulo_post, $imagen_final, $orden_post, $activo_post]);
                $mensaje = 'Banner creado exitosamente.';
                
                // Limpieza
                $titulo_post = ''; 
                $subtitulo_post = ''; 
                $activo_post = 1;
                $imagen_url_actual = ''; 
                
                // Recalcular orden futuro
                $stmtMax = $pdo->query("SELECT MAX(orden) FROM fmru_banners");
                $maxOrdenPost = $stmtMax->fetchColumn();
                $orden_post = $maxOrdenPost ? (int)$maxOrdenPost + 1 : 1;
            }
            
            $pdo->commit(); 
            $tipo_mensaje = 'success';
            
            $titulo = $titulo_post;
            $subtitulo = $subtitulo_post;
            $orden = $orden_post;
            $activo = $activo_post;

        } catch (PDOException $e) {
            $pdo->rollBack();
            $mensaje = "Error en base de datos al guardar: " . e($e->getMessage());
            $tipo_mensaje = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $custom_title ?> | Admin FMRU</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- CSS Centralizado -->
    <link href="css/admin.css" rel="stylesheet">
</head>
<body class="admin-panel">

<div class="d-flex">
    
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="flex-grow-1">
        
        <?php include __DIR__ . '/partials/topbar.php'; ?>

        <div class="container-fluid p-4">
            
            <div class="mb-4">
                <a href="ver_banners.php" class="btn btn-light border btn-sm shadow-sm">
                    <i class="bi bi-arrow-left"></i> Volver a la lista
                </a>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show shadow-sm" role="alert">
                    <?= $mensaje ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="form-card">
                        <form method="POST" enctype="multipart/form-data">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Título Principal</label>
                                <input type="text" name="titulo" class="form-control" value="<?= e($titulo) ?>" required maxlength="150" placeholder="Ej. La Fuerza de las Serpientes">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Subtítulo (Opcional)</label>
                                <input type="text" name="subtitulo" class="form-control" value="<?= e($subtitulo) ?>" maxlength="255" placeholder="Ej. Apoya a nuestras selecciones...">
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Orden de Aparición</label>
                                    <input type="number" name="orden" class="form-control" value="<?= e($orden) ?>" min="1">
                                    <div class="form-text">Si cambias el número, los demás se reordenarán.</div>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="activo" id="activoSwitch" <?= $activo ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-bold text-dark" for="activoSwitch">Publicar (Activo)</label>
                                    </div>
                                </div>
                            </div>

                            <hr class="text-muted">

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">Imagen del Banner</label>
    
                                <?php if ($imagen_url_actual): ?>
                                    <div class="mb-3 text-center bg-light p-3 rounded border overflow-hidden">
                                        <p class="text-muted small mb-2 fw-bold text-uppercase">Imagen Actual</p>
                                        <!-- Clases corregidas para evitar desbordamiento -->
                                        <img src="../<?= e($imagen_url_actual) ?>" alt="Banner Actual" class="current-image-banner img-fluid shadow-sm border">
                                    </div>
                                <?php endif; ?>
                                
                                <input class="form-control" type="file" name="imagen" id="imagenFile" accept="image/jpeg, image/png, image/webp" <?= !$id ? 'required' : '' ?>>
                                <div class="form-text">
                                    <span class="text-danger fw-bold"><?= $id ? 'Sube una nueva imagen solo si deseas reemplazar la actual.' : 'Requerido.' ?></span> 
                                    Formatos: JPG, PNG, WEBP. Max: 2MB. Dimensiones recomendadas: 1920x1080 px.
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm" style="background-color: #116436; border-color: #116436;">
                                    <i class="bi bi-save me-2"></i> Guardar Banner
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    <?php if ($tipo_mensaje === 'success' && !$id): ?>
        document.getElementById('imagenFile').value = '';
    <?php endif; ?>
</script>
</body>
</html>