<?php
// admin/ver_usuarios.php
require_once __DIR__ . '/config/seguridad.php';
exigir_sesion_admin('SuperAdmin');
require_once __DIR__ . '/config/db.php';
global $db;

$custom_title = 'Gestión de Usuarios';
$mensaje = $_SESSION['flash_mensaje'] ?? '';
$tipo_mensaje = $_SESSION['flash_tipo'] ?? '';
unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    $id_eliminar = (int)$_POST['eliminar_id'];

    if (!validar_csrf()) {
        $_SESSION['flash_mensaje'] = 'Token de seguridad inválido. Intenta nuevamente.';
        $_SESSION['flash_tipo'] = 'danger';
    } else {
        try {
            $usuarioTarget = $db->getUsuarioById($id_eliminar);
            $todos = $db->getTodosUsuarios();

            $totalSuperAdmins = 0;
            foreach ($todos as $usr) {
                if (($usr['rol'] ?? '') === 'SuperAdmin') {
                    $totalSuperAdmins++;
                }
            }

            $esMismoUsuario = (isset($_SESSION['admin_id']) && (int)$_SESSION['admin_id'] === $id_eliminar)
                || (!isset($_SESSION['admin_id']) && isset($_SESSION['admin_nombre']) && $usuarioTarget && $_SESSION['admin_nombre'] === $usuarioTarget['nombre']);

            if (!$usuarioTarget) {
                $_SESSION['flash_mensaje'] = 'El usuario seleccionado ya no existe.';
                $_SESSION['flash_tipo'] = 'warning';
            } elseif ($esMismoUsuario) {
                $_SESSION['flash_mensaje'] = 'Por seguridad, no puedes eliminar tu propia cuenta en sesión.';
                $_SESSION['flash_tipo'] = 'danger';
            } elseif (($usuarioTarget['rol'] ?? '') === 'SuperAdmin' && $totalSuperAdmins <= 1) {
                $_SESSION['flash_mensaje'] = 'Acción bloqueada: Debe existir al menos un Super Admin activo en el sistema.';
                $_SESSION['flash_tipo'] = 'danger';
            } else {
                if ($db->eliminarUsuario($id_eliminar)) {
                    $_SESSION['flash_mensaje'] = 'Usuario eliminado correctamente.';
                    $_SESSION['flash_tipo'] = 'success';
                }
            }
        } catch (Throwable $e) {
            $_SESSION['flash_mensaje'] = 'Error interno al intentar eliminar el usuario.';
            $_SESSION['flash_tipo'] = 'danger';
        }
    }

    header('Location: ver_usuarios.php');
    exit;
}

try {
    $usuarios = $db->getTodosUsuarios();
} catch (Throwable $e) {
    die('Error al cargar la lista de usuarios.');
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
                <h3 class="fw-bold mb-0" style="color: #bf2333;">Administradores del Sistema</h3>
                <a href="usuario_form.php" class="btn btn-success shadow-sm" style="background-color: #116436; border-color: #116436;">
                    <i class="bi bi-person-plus-fill me-1"></i> Agregar Usuario
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-custom">
                            <thead>
                                <tr>
                                    <th class="ps-4">Administrador</th>
                                    <th>Correo Electrónico</th>
                                    <th>Nivel de Acceso</th>
                                    <th>Registro</th>
                                    <th class="text-center pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($usuarios)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No hay usuarios registrados.</td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($usuarios as $u): ?>
                                    <?php
                                    $inicial = mb_strtoupper(mb_substr(trim((string)$u['nombre']), 0, 1, 'UTF-8'), 'UTF-8') ?: 'U';
                                    $esActual = (isset($_SESSION['admin_id']) && (int)$_SESSION['admin_id'] === (int)$u['id'])
                                        || (!isset($_SESSION['admin_id']) && isset($_SESSION['admin_nombre']) && $_SESSION['admin_nombre'] === $u['nombre']);
                                    $esSuper = ($u['rol'] === 'SuperAdmin');
                                    $fechaFmt = !empty($u['fecha_creacion']) ? date('d/m/Y', strtotime($u['fecha_creacion'])) : '-';
                                    ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold me-3 flex-shrink-0"
                                                     style="width: 38px; height: 38px; background-color: <?= $esSuper ? '#bf2333' : '#116436' ?>;" aria-hidden="true">
                                                    <?= e($inicial) ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">
                                                        <?= e($u['nombre']) ?>
                                                        <?php if ($esActual): ?>
                                                            <span class="badge bg-secondary-subtle text-secondary border ms-1" style="font-size: 0.7rem;">Tú</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-muted text-nowrap"><?= e($u['email']) ?></td>
                                        <td>
                                            <?php if ($esSuper): ?>
                                                <span class="badge text-bg-danger"><i class="bi bi-shield-lock-fill me-1"></i>Super Admin</span>
                                            <?php else: ?>
                                                <span class="badge text-bg-success" style="background-color: #116436 !important;"><i class="bi bi-pencil-square me-1"></i>Editor</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted small text-nowrap"><?= e($fechaFmt) ?></td>
                                        <td class="text-center pe-4 text-nowrap">
                                            <a href="usuario_form.php?id=<?= (int)$u['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar usuario">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php if ($esActual): ?>
                                                <button type="button" class="btn btn-sm btn-outline-secondary opacity-50" disabled title="Sesión activa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal"
                                                        data-id="<?= (int)$u['id'] ?>"
                                                        data-nombre="<?= e($u['nombre']) ?>"
                                                        title="Eliminar usuario">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?>
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
                <h5 class="modal-title fw-bold text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-muted">
                ¿Estás seguro que deseas revocar el acceso y eliminar al usuario <strong id="modalUserNombre" class="text-dark"></strong>? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="" data-no-resilience="true">
                    <?= csrf_campo() ?>
                    <input type="hidden" name="eliminar_id" id="modalUserId" value="">
                    <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- <script>if(!window.bootstrap){document.write('<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"><\/script>');}</script> -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                deleteModal.querySelector('#modalUserId').value = button.getAttribute('data-id');
                deleteModal.querySelector('#modalUserNombre').textContent = '"' + button.getAttribute('data-nombre') + '"';
            });
        }
    });
</script>
</body>
</html>