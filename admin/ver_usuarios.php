<?php
// admin/ver_usuarios.php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/config/db.php';

$mensaje = '';
$tipo_mensaje = '';
$custom_title = "Gestión de Usuarios";

try {
    // === LÓGICA DE ELIMINACIÓN SEGURA (OWASP) ===
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
        $id_eliminar = (int)$_POST['eliminar_id'];
        
        // Regla de negocio: Un usuario no puede eliminarse a sí mismo en la sesión activa
        if ($id_eliminar === (int)$_SESSION['admin_id']) {
            $mensaje = "Acción denegada: No puedes eliminar tu propio usuario mientras tienes la sesión activa.";
            $tipo_mensaje = "danger";
        } else {
            $stmtDel = $pdo->prepare("DELETE FROM admin_usuarios WHERE id = ?");
            if ($stmtDel->execute([$id_eliminar])) {
                $mensaje = "Usuario eliminado correctamente del sistema.";
                $tipo_mensaje = "success";
            }
        }
    }

    // === OBTENER LOS USUARIOS PARA LA TABLA ===
    $stmt = $pdo->query("SELECT id, nombre, email, rol, fecha_creacion FROM admin_usuarios ORDER BY id ASC");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error de base de datos: " . e($e->getMessage()));
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
    <link href="css/admin.css" rel="stylesheet">
</head>
<body class="admin-panel">

<div class="d-flex">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="flex-grow-1">
        <?php include __DIR__ . '/partials/topbar.php'; ?>

        <div class="container-fluid p-4">
            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show shadow-sm" role="alert">
                    <?= $mensaje ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold" style="color: #bf2333;">Administradores del Sistema</h3>
                <a href="usuario_form.php" class="btn btn-success shadow-sm" style="background-color: #116436; border-color: #116436;">
                    <i class="bi bi-person-plus-fill me-1"></i> Nuevo Usuario
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-custom">
                            <thead>
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>Nombre del Administrador</th>
                                    <th>Correo Electrónico (Acceso)</th>
                                    <th>Nivel de Acceso</th>
                                    <th class="text-center pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($usuarios)): ?>
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No hay usuarios registrados.</td></tr>
                                <?php endif; ?>

                                <?php foreach ($usuarios as $user): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">#<?= e($user['id']) ?></td>
                                    <td class="fw-bold text-dark">
                                        <i class="bi bi-person-circle text-secondary me-2 fs-5"></i> 
                                        <?= e($user['nombre']) ?>
                                    </td>
                                    <td><a href="mailto:<?= e($user['email']) ?>" class="text-decoration-none text-muted"><?= e($user['email']) ?></a></td>
                                    <td>
                                        <span class="badge <?= $user['rol'] === 'SuperAdmin' ? 'bg-danger' : 'bg-primary' ?>">
                                            <?= e($user['rol']) ?>
                                        </span>
                                    </td>
                                    <td class="pe-4">
                                        <div class="d-flex justify-content-center flex-nowrap gap-2">
                                            <a href="usuario_form.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></a>
                                            <!-- Validar que no se bloquee el botón de sí mismo visualmente, aunque por backend está protegido -->
                                            <button type="button" class="btn btn-sm <?= ($user['id'] == $_SESSION['admin_id']) ? 'btn-outline-secondary disabled' : 'btn-outline-danger' ?>" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?= $user['id'] ?>" data-nombre="<?= e($user['nombre']) ?>" title="<?= ($user['id'] == $_SESSION['admin_id']) ? 'No puedes borrarte a ti mismo' : 'Eliminar' ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
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
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-muted">
        ¿Estás seguro que deseas revocar el acceso y eliminar al usuario <strong id="modalUserNombre" class="text-dark"></strong> del sistema? Esta acción es irreversible.
      </div>
      <div class="modal-footer border-top-0 pt-0">
        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
        <form method="POST" action="">
            <input type="hidden" name="eliminar_id" id="modalUserId" value="">
            <button type="submit" class="btn btn-danger">Sí, revocar acceso</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                document.getElementById('modalUserId').value = button.getAttribute('data-id');
                document.getElementById('modalUserNombre').textContent = '"' + button.getAttribute('data-nombre') + '"';
            });
        }
    });
</script>
</body>
</html>