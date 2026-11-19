<?php
// admin/ver_banners.php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

// Requerir conexión a BD y funciones globales (incluye $pdo y function e() )
require_once __DIR__ . '/config/db.php';

$mensaje = '';
$tipo_mensaje = '';
$custom_title = "Gestión de Banners";

try {
    // === LÓGICA DE ELIMINACIÓN SEGURA (POST) ===
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
        $id_eliminar = (int)$_POST['eliminar_id'];
        
        $stmtImg = $pdo->prepare("SELECT imagen_url FROM fmru_banners WHERE id = ?");
        $stmtImg->execute([$id_eliminar]);
        $imgTarget = $stmtImg->fetchColumn();

        $stmtDel = $pdo->prepare("DELETE FROM fmru_banners WHERE id = ?");
        if ($stmtDel->execute([$id_eliminar])) {
            if ($imgTarget && file_exists("../" . $imgTarget)) {
                unlink("../" . $imgTarget);
            }
            $mensaje = "Banner eliminado correctamente.";
            $tipo_mensaje = "success";
        }
    }

    // === OBTENER LOS BANNERS PARA LA TABLA ===
    $stmt = $pdo->query("SELECT * FROM fmru_banners ORDER BY orden ASC");
    $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                <h3 class="fw-bold" style="color: #bf2333;">Banners del Carrusel</h3>
                <a href="banner_form.php" class="btn btn-success shadow-sm" style="background-color: #116436; border-color: #116436;">
                    <i class="bi bi-plus-lg me-1"></i> Agregar Banner
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-custom">
                            <thead>
                                <tr>
                                    <th class="ps-4">Imagen</th>
                                    <th>Título</th>
                                    <th>Subtítulo</th>
                                    <th>Orden</th>
                                    <th>Estado</th>
                                    <th class="text-center pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($banners)): ?>
                                    <tr><td colspan="6" class="text-center py-4 text-muted">No hay banners registrados.</td></tr>
                                <?php endif; ?>

                                <?php foreach ($banners as $banner): ?>
                                <tr>
                                    <td class="ps-4">
                                        <img src="../<?= e($banner['imagen_url']) ?>" alt="Banner" class="banner-preview">
                                    </td>
                                    <td class="fw-bold text-dark"><?= e($banner['titulo']) ?></td>
                                    <td class="text-muted small"><?= e($banner['subtitulo']) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= (int)$banner['orden'] ?></span></td>
                                    <td>
                                        <?php if ($banner['activo']): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center pe-4">
                                        <a href="banner_form.php?id=<?= $banner['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar"><i class="bi bi-pencil"></i></a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?= $banner['id'] ?>" data-nombre="<?= e($banner['titulo']) ?>" title="Eliminar">
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
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-muted">
        ¿Estás seguro que deseas eliminar el banner <strong id="modalBannerNombre" class="text-dark"></strong>? Esta acción no se puede deshacer y borrará la imagen del servidor.
      </div>
      <div class="modal-footer border-top-0 pt-0">
        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
        <form method="POST" action="">
            <input type="hidden" name="eliminar_id" id="modalBannerId" value="">
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
                const id = button.getAttribute('data-id');
                const nombre = button.getAttribute('data-nombre');
                
                deleteModal.querySelector('#modalBannerId').value = id;
                deleteModal.querySelector('#modalBannerNombre').textContent = '"' + nombre + '"';
            });
        }
    });
</script>
</body>
</html>