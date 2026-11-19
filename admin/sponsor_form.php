<?php
// admin/sponsor_form.php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/config/db.php';

if (!function_exists('e')) {
    function e($string) { return htmlspecialchars((string)$string, ENT_QUOTES | ENT_HTML5, 'UTF-8'); }
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$accion = $id ? 'Editar' : 'Agregar';
$custom_title = "$accion Sponsor";

$mensaje = ''; $tipo_mensaje = '';

// Variables por defecto
$nombre = ''; 
$enlace = '#'; 
$nivel = 'Proveedor Oficial'; 
$activo = 1; 
$imagen_url_actual = '';

// Lógica para obtener el último orden al cargar la página por primera vez
if (!$id) {
    $stmtMax = $pdo->query("SELECT MAX(orden) FROM fmru_sponsors");
    $max_val = $stmtMax->fetchColumn();
    $orden = $max_val ? (int)$max_val + 1 : 1;
} else {
    $orden = 0; 
}

// Cargar datos si es edición
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM fmru_sponsors WHERE id = ?");
    $stmt->execute([$id]);
    $sponsor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($sponsor) {
        $nombre = $sponsor['nombre'];
        $enlace = $sponsor['enlace'];
        $nivel = $sponsor['nivel'] ?? 'Proveedor Oficial';
        $orden = (int)$sponsor['orden']; // Se sobreescribe con el orden real del registro
        $activo = $sponsor['activo'];
        $imagen_url_actual = $sponsor['imagen_url'];
    } else {
        header("Location: ver_sponsors.php"); exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitización OWASP
    $nombre_post = trim($_POST['nombre'] ?? '');
    
    $enlace_post = trim($_POST['enlace'] ?? '#');
    if (empty($enlace_post) || $enlace_post === 'http://' || $enlace_post === 'https://') {
        $enlace_post = '#';
    }
    
    $niveles_permitidos = ['Patrocinador Principal', 'Socio Técnico', 'Proveedor Oficial'];
    $nivel_post = in_array($_POST['nivel'] ?? '', $niveles_permitidos) ? $_POST['nivel'] : 'Proveedor Oficial';
    
    // Lógica Automática: Si es Principal, no rota (0). Si es otro, rota (1).
    $rotativo_post = ($nivel_post === 'Patrocinador Principal') ? 0 : 1;
    
    $orden_post = (int)$_POST['orden'];
    $activo_post = isset($_POST['activo']) ? 1 : 0;
    
    $imagen_final = $imagen_url_actual;

    // Validación segura de imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imagen']['tmp_name'];
        $fileName = $_FILES['imagen']['name'];
        $fileSize = $_FILES['imagen']['size'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $image_info = getimagesize($fileTmpPath);
        $is_valid_image = false;
        
        if ($fileExtension === 'svg') {
            $mime = mime_content_type($fileTmpPath);
            if ($mime === 'image/svg+xml') $is_valid_image = true;
        } elseif ($image_info !== false) {
            $is_valid_image = true;
        }

        if ($is_valid_image) {
            if ($fileSize < 2 * 1024 * 1024) { 
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $uploadFileDir = dirname(__DIR__) . '/assets/img/sponsors/';
                if (!is_dir($uploadFileDir)) mkdir($uploadFileDir, 0755, true);

                if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                    $imagen_final = 'assets/img/sponsors/' . $newFileName;
                } else {
                    $mensaje = 'Error al guardar el logo.'; $tipo_mensaje = 'danger';
                }
            } else {
                $mensaje = 'El logo supera el límite de 2MB.'; $tipo_mensaje = 'danger';
            }
        } else {
            $mensaje = 'Archivo inválido.'; $tipo_mensaje = 'danger';
        }
    } elseif (!$id && empty($_FILES['imagen']['name'])) {
        $mensaje = 'Debes subir un logo.'; $tipo_mensaje = 'danger';
    }

    if (empty($mensaje)) {
        try {
            $pdo->beginTransaction();

            if ($id) {
                // Lógica Swap (Intercambio)
                if ($orden !== $orden_post) {
                    $stmtConflicto = $pdo->prepare("SELECT id FROM fmru_sponsors WHERE orden = ? AND id != ?");
                    $stmtConflicto->execute([$orden_post, $id]);
                    if ($id_conflicto = $stmtConflicto->fetchColumn()) {
                        $pdo->prepare("UPDATE fmru_sponsors SET orden = ? WHERE id = ?")->execute([$orden, $id_conflicto]);
                    }
                }
                $sql = "UPDATE fmru_sponsors SET nombre=?, enlace=?, nivel=?, rotativo=?, imagen_url=?, orden=?, activo=? WHERE id=?";
                $pdo->prepare($sql)->execute([$nombre_post, $enlace_post, $nivel_post, $rotativo_post, $imagen_final, $orden_post, $activo_post, $id]);
                $mensaje = 'Sponsor actualizado.';
                $imagen_url_actual = $imagen_final;
            } else {
                // Lógica Shift (Desplazamiento)
                $stmtConflicto = $pdo->prepare("SELECT id FROM fmru_sponsors WHERE orden = ?");
                $stmtConflicto->execute([$orden_post]);
                if ($stmtConflicto->fetchColumn()) {
                    $pdo->prepare("UPDATE fmru_sponsors SET orden = orden + 1 WHERE orden >= ?")->execute([$orden_post]);
                }
                $sql = "INSERT INTO fmru_sponsors (nombre, enlace, nivel, rotativo, imagen_url, orden, activo) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $pdo->prepare($sql)->execute([$nombre_post, $enlace_post, $nivel_post, $rotativo_post, $imagen_final, $orden_post, $activo_post]);
                $mensaje = 'Sponsor creado exitosamente.';
                
                // Reiniciar los valores POST para limpiar el formulario de cara a un nuevo registro
                $nombre_post = ''; 
                $enlace_post = '#'; 
                $nivel_post = 'Proveedor Oficial'; 
                $activo_post = 1; 
                $imagen_url_actual = '';
                
                // Calcular inteligentemente el próximo orden disponible post-inserción
                $nuevoMax = $pdo->query("SELECT MAX(orden) FROM fmru_sponsors")->fetchColumn();
                $orden_post = $nuevoMax ? (int)$nuevoMax + 1 : 1;
            }
            
            $pdo->commit(); 
            $tipo_mensaje = 'success';
            
            // Asignar los valores a las variables de la vista
            $nombre = $nombre_post; 
            $enlace = $enlace_post; 
            $nivel = $nivel_post; 
            $orden = $orden_post; 
            $activo = $activo_post;
            
        } catch (PDOException $e) {
            $pdo->rollBack(); 
            $mensaje = "Error: " . $e->getMessage(); 
            $tipo_mensaje = 'danger';
        }
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>
<body class="admin-panel">
<div class="d-flex">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-grow-1">
        <?php include __DIR__ . '/partials/topbar.php'; ?>
        <div class="container-fluid p-4">
            <div class="mb-4"><a href="ver_sponsors.php" class="btn btn-light border btn-sm shadow-sm"><i class="bi bi-arrow-left"></i> Volver a la lista</a></div>
            
            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show shadow-sm">
                    <?= $mensaje ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="form-card">
                        <form method="POST" enctype="multipart/form-data">
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Nombre de la Empresa</label>
                                    <input type="text" name="nombre" class="form-control" value="<?= e($nombre) ?>" required maxlength="100">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Enlace Web (URL)</label>
                                    <input type="text" name="enlace" class="form-control" value="<?= e($enlace) ?>" placeholder="https://www.ejemplo.com">
                                    <div class="form-text">Si no tiene sitio, déjalo vacío o con "#"</div>
                                </div>
                            </div>

                            <div class="row mb-4 bg-light p-3 rounded border mx-0 align-items-center">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark mb-1">Nivel del Patrocinador</label>
                                    <select name="nivel" class="form-select">
                                        <option value="Patrocinador Principal" <?= $nivel === 'Patrocinador Principal' ? 'selected' : '' ?>>Patrocinador Principal (No rota)</option>
                                        <option value="Socio Técnico" <?= $nivel === 'Socio Técnico' ? 'selected' : '' ?>>Socio Técnico (Fila 1)</option>
                                        <option value="Proveedor Oficial" <?= $nivel === 'Proveedor Oficial' ? 'selected' : '' ?>>Proveedor Oficial (Fila 2)</option>
                                    </select>
                                </div>
                                <div class="col-md-6 text-muted small mt-2 mt-md-0">
                                    <i class="bi bi-info-circle-fill me-1"></i> La posición de la imagen y su animación (rotativa o estática) se asignarán automáticamente según el nivel seleccionado.
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Orden de Aparición</label>
                                    <input type="number" name="orden" class="form-control" value="<?= e($orden) ?>" min="1">
                                    <div class="form-text">Calculado automáticamente al agregar nuevos.</div>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="activo" id="activoSwitch" <?= $activo ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-bold text-dark" for="activoSwitch">Publicar</label>
                                    </div>
                                </div>
                            </div>

                            <hr class="text-muted">

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">Logo del Patrocinador</label>
                                <?php if ($imagen_url_actual): ?>
                                    <div class="mb-3 text-center bg-light p-3 rounded border">
                                        <img src="../<?= e($imagen_url_actual) ?>" class="current-image-sponsor shadow-sm">
                                    </div>
                                <?php endif; ?>
                                <input class="form-control" type="file" name="imagen" id="imagenFile" accept="image/jpeg, image/png, image/webp, image/svg+xml" <?= !$id ? 'required' : '' ?>>
                                <div class="form-text"><span class="text-danger fw-bold"><?= $id ? 'Opcional.' : 'Requerido.' ?></span> Formatos: PNG, JPG, SVG. Max: 2MB.</div>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm" style="background-color: #116436; border-color: #116436;">
                                    Guardar Sponsor
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
    // Limpia el input de archivo después de agregar uno nuevo exitosamente
    <?php if ($tipo_mensaje === 'success' && !$id): ?> document.getElementById('imagenFile').value = ''; <?php endif; ?>
</script>
</body>
</html>