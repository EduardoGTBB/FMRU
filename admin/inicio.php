<?php
// admin/inicio.php
require_once __DIR__ . '/config/seguridad.php';
exigir_sesion_admin();
require_once __DIR__ . '/config/db.php';
global $db;

$custom_title = 'Resumen del Sistema';
$resumen = ['noticias' => 0, 'partidos' => 0, 'documentos' => 0];

try {
    $resumen = $db->getResumenDashboard();
} catch (Throwable $e) {
    // Fallback seguro si alguna tabla aún no está creada
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
            <h3 class="fw-bold mb-4" style="color: #bf2333;">Bienvenido al Panel FMRU</h3>

            <div class="row g-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card h-100 p-3 position-relative">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted fw-bold small text-uppercase">Noticias Activas</div>
                                <h2 class="fw-bold mb-0 mt-1 text-dark"><?= $resumen['noticias'] ?></h2>
                            </div>
                            <i class="bi bi-newspaper stat-icon" aria-hidden="true"></i>
                        </div>
                        <a href="gestor_contenidos.php" class="stretched-link" aria-label="Ir al Gestor de Contenidos"></a>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card h-100 p-3" style="border-top-color: #bf2333;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted fw-bold small text-uppercase">Partidos Pendientes</div>
                                <h2 class="fw-bold mb-0 mt-1 text-dark"><?= $resumen['partidos'] ?></h2>
                            </div>
                            <i class="bi bi-calendar-event stat-icon" style="color: #bf2333;" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card h-100 p-3" style="border-top-color: #004323;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted fw-bold small text-uppercase">Clubes Registrados</div>
                                <h2 class="fw-bold mb-0 mt-1 text-dark">34</h2>
                            </div>
                            <i class="bi bi-shield-shaded stat-icon" style="color: #004323;" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card h-100 p-3 position-relative" style="border-top-color: #792128;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted fw-bold small text-uppercase">Documentos Activos</div>
                                <h2 class="fw-bold mb-0 mt-1 text-dark"><?= $resumen['documentos'] ?></h2>
                            </div>
                            <i class="bi bi-file-earmark-pdf stat-icon" style="color: #792128;" aria-hidden="true"></i>
                        </div>
                        <a href="ver_documentos.php" class="stretched-link" aria-label="Ir al Repositorio de Documentos"></a>
                    </div>
                </div>
            </div>

            <div class="card mt-5 border-0 shadow-sm rounded-3">
                <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold" style="color: #116436;"><i class="bi bi-clock-history me-2"></i> Actividad Reciente</h6>
                </div>
                <div class="card-body p-4 text-center text-muted">
                    <p class="mb-0">Selecciona un módulo en el menú lateral para gestionar el contenido del portal.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <script>if(!window.bootstrap){document.write('<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"><\/script>');}</script> -->
</body>
</html>