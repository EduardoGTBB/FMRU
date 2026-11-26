<?php
// admin/ver_sponsors.php
require_once __DIR__ . '/config/seguridad.php';
exigir_sesion_admin();
require_once __DIR__ . '/config/db.php';
global $db;

$custom_title = 'Gestión de Sponsors';
$mensaje = $_SESSION['flash_mensaje'] ?? '';
$tipo_mensaje = $_SESSION['flash_tipo'] ?? '';
unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    if (!validar_csrf()) {
        $_SESSION['flash_mensaje'] = 'Token de seguridad inválido. Intenta nuevamente.';
        $_SESSION['flash_tipo'] = 'danger';
    } else {
        try {
            $id_eliminar = (int)$_POST['eliminar_id'];
            $imgTarget = $db->getImagenSponsor($id_eliminar);

            if ($db->eliminarSponsor($id_eliminar)) {
                if ($imgTarget) {
                    $rutaFisica = dirname(__DIR__) . '/' . ltrim($imgTarget, '/');
                    if (is_file($rutaFisica)) {
                        unlink($rutaFisica);
                    }
                }
                $_SESSION['flash_mensaje'] = 'Sponsor eliminado correctamente.';
                $_SESSION['flash_tipo'] = 'success';
            }
        } catch (Throwable $e) {
            $_SESSION['flash_mensaje'] = 'Error al intentar eliminar el sponsor.';
            $_SESSION['flash_tipo'] = 'danger';
        }
    }

    header('Location: ver_sponsors.php');
    exit;
}

try {
    $sponsors = $db->getSponsorsAdmin();
} catch (Throwable $e) {
    die('Error de base de datos.');
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

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <h3 class="fw-bold mb-0" style="color: #bf2333;">Sponsors Oficiales</h3>
                <a href="sponsor_form.php" class="btn btn-success shadow-sm" style="background-color: #116436; border-color: #116436;">
                    <i class="bi bi-plus-lg me-1"></i> Agregar Sponsor
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-custom">
                            <thead>
                                <tr>
                                    <th class="ps-4">Logo</th>
                                    <th>Empresa / Nivel</th>
                                    <th>Orden Global</th>
                                    <th>Estado</th>
                                    <th class="text-center pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($sponsors)): ?>
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No hay sponsors registrados.</td></tr>
                                <?php endif; ?>

                                <?php foreach ($sponsors as $sponsor): ?>
                                <tr>
                                    <td class="ps-4 text-center bg-dark bg-opacity-10">
                                        <img src="../<?= e($sponsor['imagen_url']) ?>" alt="Logo" class="sponsor-preview bg-transparent border-0">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-6"><?= e($sponsor['nombre']) ?></div>
                                        <div class="text-muted small mb-1"><?= e($sponsor['nivel'] ?? 'Proveedor Oficial') ?></div>
                                        <?php if ($sponsor['rotativo']): ?>
                                            <span class="badge bg-info text-dark" style="font-size: 0.70rem;">Rotativo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary" style="font-size: 0.70rem;">Logo Fijo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-light text-dark border fw-bold"><?= (int)$sponsor['orden'] ?></span></td>
                                    <td>
                                        <?php if ($sponsor['activo']): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center pe-4 text-nowrap">
                                        <a href="sponsor_form.php?id=<?= (int)$sponsor['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar"><i class="bi bi-pencil"></i></a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?= (int)$sponsor['id'] ?>" data-nombre="<?= e($sponsor['nombre']) ?>" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-muted">
                ¿Estás seguro que deseas eliminar el sponsor <strong id="modalSponsorNombre" class="text-dark"></strong>? Esta acción borrará permanentemente su logo del servidor.
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="" data-no-resilience="true">
                    <?= csrf_campo() ?>
                    <input type="hidden" name="eliminar_id" id="modalSponsorId" value="">
                    <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- <script>if(!window.bootstrap){document.write('<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"><\/script>');}</script> -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                deleteModal.querySelector('#modalSponsorId').value = button.getAttribute('data-id');
                deleteModal.querySelector('#modalSponsorNombre').textContent = '"' + button.getAttribute('data-nombre') + '"';
            });
        }
    });
</script>
</body>
</html>