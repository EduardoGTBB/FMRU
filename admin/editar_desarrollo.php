<?php
// admin/editar_desarrollo.php
require_once __DIR__ . '/config/seguridad.php';
exigir_sesion_admin();
require_once __DIR__ . '/config/db.php';
global $db;

$custom_title = 'Página de Desarrollo';
$mensaje = $_SESSION['flash_mensaje'] ?? '';
$tipo_mensaje = $_SESSION['flash_tipo'] ?? '';
unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validar_csrf()) {
        $_SESSION['flash_mensaje'] = 'Token de seguridad inválido. Intenta guardar nuevamente.';
        $_SESSION['flash_tipo'] = 'danger';
        header('Location: editar_desarrollo.php');
        exit;
    }

    try {
        $db->beginTransaction();

        $db->updateSetting('desarrollo_hero_titulo', recibir_post('desarrollo_hero_titulo'));
        $db->updateSetting('desarrollo_hero_subtitulo', recibir_post('desarrollo_hero_subtitulo'));

        $db->updateSetting('desarrollo_intro', recibir_post('desarrollo_intro', true));
        $db->updateSetting('festivales_texto', recibir_post('festivales_texto', true));
        $db->updateSetting('rising_texto', recibir_post('rising_texto', true));
        $db->updateSetting('gir_texto', recibir_post('gir_texto', true));
        $db->updateSetting('grassroots_texto', recibir_post('grassroots_texto', true));

        $mapa_imagenes = [
            'desarrollo_hero_img_file' => 'desarrollo_hero_img',
            'festivales_img_file'      => 'festivales_img',
            'rising_img_file'          => 'rising_img',
            'gir_img_file'             => 'gir_img',
            'grassroots_img_file'      => 'grassroots_img'
        ];

        foreach ($mapa_imagenes as $input_file => $setting_key) {
            if ($nueva_ruta = procesar_imagen_segura($input_file, 'assets/img/banner', $setting_key)) {
                $db->updateSetting($setting_key, $nueva_ruta);
            }
        }

        $db->commit();
        $_SESSION['flash_mensaje'] = 'Contenidos e imágenes de Desarrollo actualizados correctamente.';
        $_SESSION['flash_tipo'] = 'success';
    } catch (RuntimeException $e) {
        $db->rollBack();
        $_SESSION['flash_mensaje'] = $e->getMessage();
        $_SESSION['flash_tipo'] = 'danger';
    } catch (Throwable $e) {
        $db->rollBack();
        $_SESSION['flash_mensaje'] = 'Error al actualizar la configuración.';
        $_SESSION['flash_tipo'] = 'danger';
    }

    header('Location: editar_desarrollo.php');
    exit;
}

try {
    $settings = $db->getAllSettings();
} catch (Throwable $e) {
    die('Error al cargar datos.');
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

            <?php if ($mensaje !== ''): ?>
                <?php $clase_alerta = in_array($tipo_mensaje, ['success', 'danger', 'warning', 'info'], true) ? $tipo_mensaje : 'info'; ?>
                <div class="alert alert-<?= $clase_alerta ?> alert-dismissible fade show shadow-sm d-flex align-items-center" role="alert">
                    <i class="bi <?= $clase_alerta === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> me-2 fs-5"></i>
                    <div><?= e($mensaje) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="form-card">
                        <h4 class="fw-bold mb-4" style="color: #bf2333;">Gestión: Página de Desarrollo</h4>

                        <form method="POST" action="" enctype="multipart/form-data">
                            <?= csrf_campo() ?>

                            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">1. Banner Principal (Hero)</h5>
                            <div class="row mb-5 align-items-center">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-dark">Título del Banner</label>
                                        <input type="text" name="desarrollo_hero_titulo" class="form-control fw-bold" maxlength="150" value="<?= e($settings['desarrollo_hero_titulo'] ?? 'DESARROLLO') ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-dark">Subtítulo del Banner</label>
                                        <input type="text" name="desarrollo_hero_subtitulo" class="form-control" maxlength="255" value="<?= e($settings['desarrollo_hero_subtitulo'] ?? 'Mostrando que el rugby es para todos en todo México.') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4 text-center bg-light p-3 rounded border">
                                    <label class="form-label fw-bold text-muted small">Imagen de Fondo (Banner)</label>
                                    <?php if (!empty($settings['desarrollo_hero_img'])): ?>
                                        <div class="mb-2"><img src="../<?= e($settings['desarrollo_hero_img']) ?>" class="img-thumbnail" style="max-height: 120px;" alt="Banner Desarrollo"></div>
                                    <?php endif; ?>
                                    <input type="file" name="desarrollo_hero_img_file" class="form-control form-control-sm" accept="image/jpeg, image/png, image/webp">
                                </div>
                            </div>

                            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">2. Contenidos de Programas</h5>
                            <div class="mb-5">
                                <label class="form-label fw-bold text-dark">Texto Introductorio General</label>
                                <textarea name="desarrollo_intro" class="form-control editor-texto" rows="4"><?= e($settings['desarrollo_intro'] ?? '') ?></textarea>
                            </div>

                            <div class="row mb-5 align-items-center">
                                <div class="col-md-8 mb-3 mb-md-0">
                                    <label class="form-label fw-bold text-dark">Texto: Festivales Infantiles</label>
                                    <textarea name="festivales_texto" class="form-control editor-texto" rows="4"><?= e($settings['festivales_texto'] ?? '') ?></textarea>
                                </div>
                                <div class="col-md-4 text-center bg-light p-3 rounded border">
                                    <label class="form-label fw-bold text-muted small">Imagen Festivales</label>
                                    <?php if (!empty($settings['festivales_img'])): ?>
                                        <div class="mb-2"><img src="../<?= e($settings['festivales_img']) ?>" class="img-thumbnail" style="max-height: 120px;" alt="Festivales"></div>
                                    <?php endif; ?>
                                    <input type="file" name="festivales_img_file" class="form-control form-control-sm" accept="image/jpeg, image/png, image/webp">
                                </div>
                            </div>

                            <div class="row mb-5 align-items-center">
                                <div class="col-md-8 mb-3 mb-md-0">
                                    <label class="form-label fw-bold text-dark">Texto: Rugby Rising Play</label>
                                    <textarea name="rising_texto" class="form-control editor-texto" rows="4"><?= e($settings['rising_texto'] ?? '') ?></textarea>
                                </div>
                                <div class="col-md-4 text-center bg-light p-3 rounded border">
                                    <label class="form-label fw-bold text-muted small">Imagen Rising Play</label>
                                    <?php if (!empty($settings['rising_img'])): ?>
                                        <div class="mb-2"><img src="../<?= e($settings['rising_img']) ?>" class="img-thumbnail" style="max-height: 120px;" alt="Rising Play"></div>
                                    <?php endif; ?>
                                    <input type="file" name="rising_img_file" class="form-control form-control-sm" accept="image/jpeg, image/png, image/webp">
                                </div>
                            </div>

                            <div class="row mb-5 align-items-center">
                                <div class="col-md-8 mb-3 mb-md-0">
                                    <label class="form-label fw-bold text-dark">Texto: Get Into Rugby</label>
                                    <textarea name="gir_texto" class="form-control editor-texto" rows="4"><?= e($settings['gir_texto'] ?? '') ?></textarea>
                                </div>
                                <div class="col-md-4 text-center bg-light p-3 rounded border">
                                    <label class="form-label fw-bold text-muted small">Imagen Get Into Rugby</label>
                                    <?php if (!empty($settings['gir_img'])): ?>
                                        <div class="mb-2"><img src="../<?= e($settings['gir_img']) ?>" class="img-thumbnail" style="max-height: 120px;" alt="Get Into Rugby"></div>
                                    <?php endif; ?>
                                    <input type="file" name="gir_img_file" class="form-control form-control-sm" accept="image/jpeg, image/png, image/webp">
                                </div>
                            </div>

                            <div class="row mb-5 align-items-center">
                                <div class="col-md-8 mb-3 mb-md-0">
                                    <label class="form-label fw-bold text-dark">Texto: Grassroots to Global</label>
                                    <textarea name="grassroots_texto" class="form-control editor-texto" rows="4"><?= e($settings['grassroots_texto'] ?? '') ?></textarea>
                                </div>
                                <div class="col-md-4 text-center bg-light p-3 rounded border">
                                    <label class="form-label fw-bold text-muted small">Imagen Grassroots</label>
                                    <?php if (!empty($settings['grassroots_img'])): ?>
                                        <div class="mb-2"><img src="../<?= e($settings['grassroots_img']) ?>" class="img-thumbnail" style="max-height: 120px;" alt="Grassroots"></div>
                                    <?php endif; ?>
                                    <input type="file" name="grassroots_img_file" class="form-control form-control-sm" accept="image/jpeg, image/png, image/webp">
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm" style="background-color: #116436; border-color: #116436;">
                                    <i class="bi bi-save me-2"></i> Guardar Cambios
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