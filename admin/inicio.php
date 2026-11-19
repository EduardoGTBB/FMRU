<?php
// admin/inicio.php
session_start();

// OWASP: Verificación estricta de sesión
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

// 1. Incluir conexión segura a la base de datos
require_once __DIR__ . '/config/db.php';

// 2. Consultas dinámicas para las tarjetas del Dashboard
$total_noticias = 0;
$partidos_pendientes = 0;

try {
    // Contar Noticias Activas (Excluyendo comunicados para esta métrica)
    $stmtNoticias = $pdo->query("SELECT COUNT(*) FROM fmru_noticias WHERE activo = 1 AND tipo != 'comunicado'");
    $total_noticias = $stmtNoticias->fetchColumn();

    // Contar Partidos Pendientes
    $stmtPartidos = $pdo->query("SELECT COUNT(*) FROM fmru_partidos WHERE estado = 'PRÓXIMO PARTIDO'");
    $partidos_pendientes = $stmtPartidos->fetchColumn();

} catch (PDOException $e) {
    // Manejo silencioso en caso de que alguna tabla falte o falle
    $total_noticias = 0;
    $partidos_pendientes = 0;
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

    <!-- CONTENIDO PRINCIPAL -->
    <div class="flex-grow-1">

        <?php include __DIR__ . '/partials/topbar.php'; ?>

        <!-- DASHBOARD CARDS -->
        <div class="container-fluid p-4">
            <h3 class="fw-bold mb-4" style="color: #bf2333;">Bienvenido al Panel FMRU</h3>
            
            <div class="row g-4">
                
                <!-- Tarjeta: Noticias Activas (DINÁMICA) -->
                <div class="col-md-3">
                    <div class="card stat-card h-100 p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted fw-bold small text-uppercase">Noticias Activas</div>
                                <!-- Inyección del conteo en tiempo real -->
                                <h2 class="fw-bold mb-0 mt-1 text-dark"><?= (int)$total_noticias ?></h2>
                            </div>
                            <i class="bi bi-newspaper stat-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta: Partidos Pendientes (DINÁMICA) -->
                <div class="col-md-3">
                    <div class="card stat-card h-100 p-3" style="border-top-color: #bf2333;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted fw-bold small text-uppercase">Partidos Pendientes</div>
                                <!-- Inyección del conteo en tiempo real -->
                                <h2 class="fw-bold mb-0 mt-1 text-dark"><?= (int)$partidos_pendientes ?></h2>
                            </div>
                            <i class="bi bi-calendar-event stat-icon" style="color: #bf2333;"></i>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta: Clubes Registrados (ESTÁTICA POR AHORA) -->
                <div class="col-md-3">
                    <div class="card stat-card h-100 p-3" style="border-top-color: #004323;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted fw-bold small text-uppercase">Clubes Registrados</div>
                                <h2 class="fw-bold mb-0 mt-1 text-dark">34</h2>
                            </div>
                            <i class="bi bi-shield-shaded stat-icon" style="color: #004323;"></i>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta: Documentos PDF (ESTÁTICA POR AHORA) -->
                <div class="col-md-3">
                    <div class="card stat-card h-100 p-3" style="border-top-color: #792128;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted fw-bold small text-uppercase">Documentos PDF</div>
                                <h2 class="fw-bold mb-0 mt-1 text-dark">5</h2>
                            </div>
                            <i class="bi bi-file-earmark-pdf stat-icon" style="color: #792128;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actividad Reciente -->
            <div class="card mt-5 border-0 shadow-sm rounded-3">
                <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold" style="color: #116436;"><i class="bi bi-clock-history me-2"></i> Actividad Reciente</h6>
                </div>
                <div class="card-body p-4 text-center text-muted">
                    <p class="mb-0">El módulo de listados (CRUD) se cargará aquí.</p>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>