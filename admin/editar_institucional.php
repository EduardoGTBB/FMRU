<?php
// admin/editar_institucional.php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/config/db.php';

$mensaje = '';
$tipo_mensaje = '';
$custom_title = "Misión y Visión";

// Procesar Formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // OWASP: Recibir datos preservando acentos
    $quienes = recibir_post('quienes_somos_intro');
    $mision_titulo = recibir_post('mision_titulo');
    $mision = recibir_post('mision_texto');
    $vision_titulo = recibir_post('vision_titulo');
    $vision = recibir_post('vision_texto');

    try {
        $stmt = $pdo->prepare("UPDATE fmru_settings SET setting_value = ? WHERE setting_key = ?");
        
        $stmt->execute([$quienes, 'quienes_somos_intro']);
        $stmt->execute([$mision_titulo, 'mision_titulo']);
        $stmt->execute([$mision, 'mision_texto']);
        $stmt->execute([$vision_titulo, 'vision_titulo']);
        $stmt->execute([$vision, 'vision_texto']);
        
        $mensaje = 'Textos institucionales actualizados correctamente.';
        $tipo_mensaje = 'success';
    } catch (PDOException $e) {
        $mensaje = "Error al actualizar: " . e($e->getMessage());
        $tipo_mensaje = 'danger';
    }
}

// Cargar datos actuales
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM fmru_settings");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    die("Error al cargar datos: " . e($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $custom_title ?? 'Dashboard' ?> | Admin FMRU</title>
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

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="form-card">
                        <h4 class="fw-bold mb-4" style="color: #bf2333;">Información Institucional</h4>
                        
                        <form method="POST" action="">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark"><i class="bi bi-info-circle me-1 text-primary"></i> Texto Introductorio (Quiénes Somos)</label>
                                <textarea name="quienes_somos_intro" class="form-control" rows="5" required><?= e($settings['quienes_somos_intro'] ?? '') ?></textarea>
                            </div>
                            
                            <hr class="text-muted my-4">

                            <!-- SECCIÓN MISIÓN -->
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark"><i class="bi bi-bullseye me-1 text-success"></i> Título No.1</label>
                                <input type="text" name="mision_titulo" class="form-control mb-2 fw-bold" value="<?= e($settings['mision_titulo'] ?? 'Misión') ?>" required>
                                <textarea name="mision_texto" class="form-control" rows="4" required><?= e($settings['mision_texto'] ?? '') ?></textarea>
                            </div>

                            <!-- SECCIÓN VISIÓN -->
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark"><i class="bi bi-eye me-1 text-warning"></i> Título No.2</label>
                                <input type="text" name="vision_titulo" class="form-control mb-2 fw-bold" value="<?= e($settings['vision_titulo'] ?? 'Visión') ?>" required>
                                <textarea name="vision_texto" class="form-control" rows="4" required><?= e($settings['vision_texto'] ?? '') ?></textarea>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>