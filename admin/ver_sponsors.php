<?php
// admin/ver_sponsors.php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/config/db.php';

$mensaje = '';
$tipo_mensaje = '';
$custom_title = "Gestión de Sponsors";

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
        $id_eliminar = (int)$_POST['eliminar_id'];
        
        $stmtImg = $pdo->prepare("SELECT imagen_url FROM fmru_sponsors WHERE id = ?");
        $stmtImg->execute([$id_eliminar]);
        $imgTarget = $stmtImg->fetchColumn();

        $stmtDel = $pdo->prepare("DELETE FROM fmru_sponsors WHERE id = ?");
        if ($stmtDel->execute([$id_eliminar])) {
            if ($imgTarget && file_exists("../" . $imgTarget)) {
                unlink("../" . $imgTarget);
            }
            $mensaje = "Sponsor eliminado correctamente.";
            $tipo_mensaje = "success";
        }
    }

    // AGRUPACIÓN AVANZADA: Primero por jerarquía de Nivel, luego por Orden numérico
    $query = "
        SELECT * FROM fmru_sponsors 
        ORDER BY 
            FIELD(nivel, 'Patrocinador Principal', 'Socio Técnico', 'Proveedor Oficial'), 
            orden ASC
    ";
    $stmt = $pdo->query($query);
    $sponsors = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold" style="color: #bf2333;">Sponsors Oficiales</h3>
                <a href="sponsor_form.php" class="btn btn-success shadow-sm" style="background-color: #116436; border-color: #116436;">
                    <i class="bi bi-plus-lg me-1"></i> Agregar Sponsor
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
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
                                <?php if(empty($sponsors)): ?>
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
                                        <?php if($sponsor['rotativo']): ?>
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
                                    <td class="text-center pe-4">
                                        <a href="sponsor_form.php?id=<?= $sponsor['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar"><i class="bi bi-pencil"></i></a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?= $sponsor['id'] ?>" data-nombre="<?= e($sponsor['nombre']) ?>" title="Eliminar">
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

<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-muted">
        ¿Estás seguro que deseas eliminar el sponsor <strong id="modalSponsorNombre" class="text-dark"></strong>? Esta acción borrará permanentemente su logo del servidor.
      </div>
      <div class="modal-footer border-top-0 pt-0">
        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
        <form method="POST" action="">
            <input type="hidden" name="eliminar_id" id="modalSponsorId">
            <button type="submit" class="btn btn-danger">Sí, eliminar</button>
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
                deleteModal.querySelector('#modalSponsorId').value = button.getAttribute('data-id');
                deleteModal.querySelector('#modalSponsorNombre').textContent = '"' + button.getAttribute('data-nombre') + '"';
            });
        }
    });
</script>
</body>
</html>