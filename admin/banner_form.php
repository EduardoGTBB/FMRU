<?php
// admin/banner_form.php
require_once __DIR__ . '/config/seguridad.php';
exigir_sesion_admin();
require_once __DIR__ . '/config/db.php';
global $db;

$id = isset($_GET['id']) ? max(0, (int)$_GET['id']) : 0;
$accion = $id ? 'Editar' : 'Agregar';
$custom_title = "$accion Banner";

$mensaje = '';
$tipo_mensaje = '';

$titulo = '';
$subtitulo = '';
$activo = 1;
$imagen_url_actual = '';

if (!$id) {
    $maxOrden = $db->getMaxOrdenBanner();
    $orden = $maxOrden ? (int)$maxOrden + 1 : 1;
} else {
    $banner = $db->getBannerById($id);
    if ($banner) {
        $titulo            = $banner['titulo'];
        $subtitulo         = $banner['subtitulo'];
        $orden             = (int)$banner['orden'];
        $activo            = (int)$banner['activo'];
        $imagen_url_actual = $banner['imagen_url'];
    } else {
        header('Location: ver_banners.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo_post    = recibir_post('titulo');
    $subtitulo_post = recibir_post('subtitulo');
    $orden_post     = max(1, (int)($_POST['orden'] ?? 1));
    $activo_post    = isset($_POST['activo']) ? 1 : 0;
    $imagen_final   = $imagen_url_actual;

    if (!validar_csrf()) {
        $mensaje = 'Token de seguridad inválido. Intenta guardar nuevamente.';
        $tipo_mensaje = 'danger';
    } elseif ($titulo_post === '') {
        $mensaje = 'El título principal es obligatorio.';
        $tipo_mensaje = 'danger';
    } else {
        try {
            $nueva_img = procesar_imagen_segura('imagen', 'assets/img/banner', 'banner');
            if ($nueva_img !== null) {
                $imagen_final = $nueva_img;
            } elseif (!$id) {
                throw new RuntimeException('Debes seleccionar una imagen para el nuevo banner.');
            }

            $db->beginTransaction();

            if ($id) {
                if ($orden !== $orden_post) {
                    $id_conflicto = $db->getBannerIdByOrden($orden_post, $id);
                    if ($id_conflicto) {
                        $db->updateBannerOrden($id_conflicto, $orden);
                    }
                }
                $db->updateBanner($id, $titulo_post, $subtitulo_post, $imagen_final, $orden_post, $activo_post);
                $mensaje = 'Banner actualizado correctamente.';
                $imagen_url_actual = $imagen_final;
            } else {
                if ($db->getBannerIdByOrden($orden_post)) {
                    $db->shiftBannersOrden($orden_post);
                }
                $db->insertBanner($titulo_post, $subtitulo_post, $imagen_final, $orden_post, $activo_post);
                $mensaje = 'Banner creado exitosamente.';
                $titulo_post = '';
                $subtitulo_post = '';
                $activo_post = 1;
                $imagen_url_actual = '';

                $maxOrdenPost = $db->getMaxOrdenBanner();
                $orden_post = $maxOrdenPost ? (int)$maxOrdenPost + 1 : 1;
            }

            $db->commit();
            $tipo_mensaje = 'success';
            $titulo = $titulo_post;
            $subtitulo = $subtitulo_post;
            $orden = $orden_post;
            $activo = $activo_post;

        } catch (RuntimeException $e) {
            $db->rollBack();
            $mensaje = $e->getMessage();
            $tipo_mensaje = 'danger';
        } catch (Throwable $e) {
            $db->rollBack();
            $mensaje = 'Error en base de datos al guardar el banner.';
            $tipo_mensaje = 'danger';
        }
    }

    if ($tipo_mensaje === 'danger') {
        $titulo    = $titulo_post;
        $subtitulo = $subtitulo_post;
        $orden     = $orden_post;
        $activo    = $activo_post;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($custom_title) ?> | Admin FMRU</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
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
                    <i class="bi bi-arrow-left"></i> Volver al listado
                </a>
            </div>

            <?php if ($mensaje !== ''): ?>
                <?php $clase_alerta = in_array($tipo_mensaje, ['success', 'danger', 'warning', 'info'], true) ? $tipo_mensaje : 'info'; ?>
                <div class="alert alert-<?= $clase_alerta ?> alert-dismissible fade show shadow-sm d-flex align-items-center" role="alert">
                    <i class="bi <?= $clase_alerta === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> me-2 fs-5"></i>
                    <div><?= e($mensaje) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="form-card">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <?= csrf_campo() ?>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Título Principal</label>
                                <input type="text" name="titulo" class="form-control" value="<?= e($titulo) ?>" required maxlength="150" placeholder="Ej. La Fuerza de las Serpientes">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Subtítulo (Opcional)</label>
                                <input type="text" name="subtitulo" class="form-control" value="<?= e($subtitulo) ?>" maxlength="255" placeholder="Ej. Apoya a nuestras selecciones...">
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label fw-bold text-dark">Orden de Aparición</label>
                                    <input type="number" name="orden" class="form-control" value="<?= (int)$orden ?>" min="1" required>
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

<!-- <script>if(!window.bootstrap){document.write('<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"><\/script>');}</script> -->
</body>
</html>