<?php
// admin/editar_institucional.php
require_once __DIR__ . '/config/seguridad.php';
exigir_sesion_admin();
require_once __DIR__ . '/config/db.php';
global $db;

$custom_title = 'Misión y Visión';
$mensaje = $_SESSION['flash_mensaje'] ?? '';
$tipo_mensaje = $_SESSION['flash_tipo'] ?? '';
unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validar_csrf()) {
        $_SESSION['flash_mensaje'] = 'Token de seguridad inválido. Intenta guardar nuevamente.';
        $_SESSION['flash_tipo'] = 'danger';
        header('Location: editar_institucional.php');
        exit;
    }

    try {
        $db->beginTransaction();

        $db->updateSetting('quienes_somos_intro', recibir_post('quienes_somos_intro'));
        $db->updateSetting('mision_titulo', recibir_post('mision_titulo'));
        $db->updateSetting('mision_texto', recibir_post('mision_texto'));
        $db->updateSetting('vision_titulo', recibir_post('vision_titulo'));
        $db->updateSetting('vision_texto', recibir_post('vision_texto'));

        $db->commit();
        $_SESSION['flash_mensaje'] = 'Textos institucionales actualizados correctamente.';
        $_SESSION['flash_tipo'] = 'success';
    } catch (Throwable $e) {
        $db->rollBack();
        $_SESSION['flash_mensaje'] = 'Error al actualizar la configuración.';
        $_SESSION['flash_tipo'] = 'danger';
    }

    header('Location: editar_institucional.php');
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
                <div class="col-lg-8">
                    <div class="form-card">
                        <h4 class="fw-bold mb-4" style="color: #bf2333;">Información Institucional</h4>

                        <form method="POST" action="">
                            <?= csrf_campo() ?>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark"><i class="bi bi-info-circle me-1 text-primary"></i> Texto Introductorio (Quiénes Somos)</label>
                                <textarea name="quienes_somos_intro" class="form-control editor-texto" rows="5" required><?= e($settings['quienes_somos_intro'] ?? '') ?></textarea>
                            </div>

                            <hr class="text-muted my-4">

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark"><i class="bi bi-bullseye me-1 text-success"></i> Título No.1</label>
                                <input type="text" name="mision_titulo" class="form-control mb-2 fw-bold" maxlength="100" value="<?= e($settings['mision_titulo'] ?? 'Misión') ?>" required>
                                <textarea name="mision_texto" class="form-control editor-texto" rows="4" required><?= e($settings['mision_texto'] ?? '') ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark"><i class="bi bi-eye me-1 text-warning"></i> Título No.2</label>
                                <input type="text" name="vision_titulo" class="form-control mb-2 fw-bold" maxlength="100" value="<?= e($settings['vision_titulo'] ?? 'Visión') ?>" required>
                                <textarea name="vision_texto" class="form-control editor-texto" rows="4" required><?= e($settings['vision_texto'] ?? '') ?></textarea>
                            </div>

                            <div class="d-grid gap-2 mt-5">
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