<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - LIGA XVS FEMENIL
 * Diseño Moderno - OWASP Compliance
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); 
ini_set('session.use_only_cookies', 1);
session_start();

require_once __DIR__ . '/admin/config/db.php';

// 1. Equipos Participantes
$equipos_xvs = [
    ["nombre" => "Pumas UNAM", "estado" => "Ciudad de México", "logo" => "P"],
    ["nombre" => "Lobos Occidente", "estado" => "Jalisco", "logo" => "L"],
    ["nombre" => "Black Thunder RC", "estado" => "Ciudad de México", "logo" => "B"],
    ["nombre" => "Legión Cuervos", "estado" => "Ciudad de México", "logo" => "L"],
    ["nombre" => "Abejas Guanajuato", "estado" => "Guanajuato", "logo" => "A"],
    ["nombre" => "ITESO Rugby", "estado" => "Jalisco", "logo" => "I"],
    ["nombre" => "Tiburones Cancún", "estado" => "Quintana Roo", "logo" => "T"],
    ["nombre" => "Templarios RC", "estado" => "Ciudad de México", "logo" => "T"]
];

// 2. Histórico de Campeonatos (Salón de la Fama)
$historico_campeonas = [
    ["temporada" => "2024 - 2025", "campeon" => "Pumas UNAM", "subcampeon" => "Black Thunder RC", "mvp" => "Isabela González"],
    ["temporada" => "2023 - 2024", "campeon" => "Lobos Occidente", "subcampeon" => "Legión Cuervos", "mvp" => "Tamara Raya Cano"],
    ["temporada" => "2022 - 2023", "campeon" => "Pumas UNAM", "subcampeon" => "Abejas Guanajuato", "mvp" => "Verónica Estrada"],
    ["temporada" => "2021 - 2022", "campeon" => "Black Thunder RC", "subcampeon" => "Tiburones Cancún", "mvp" => "Karina Landeros"]
];

$page_title = "Liga XVs Femenil"; 
include 'includes/head.php';
include 'includes/navbar.php'; 
?>

    <!-- HERO EFECTO CRISTAL CON FALLBACK SEGURO -->
    <!-- FIX: Añadimos un backgroundColor corporativo de respaldo por si la imagen falla -->
    <header class="position-relative" style="background-color: var(--verde-oscuro); background-image: url('assets/img/banner/BloodFest26-1589.jpg'); background-size: cover; background-position: center; min-height: 60vh; display: flex; align-items: center;">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.3);"></div>
        <div class="container position-relative z-2">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="glass-panel text-white shadow-lg">
                        <span class="badge bg-success mb-3 px-3 py-2 fw-bold text-uppercase" style="letter-spacing: 2px;">Primera Fuerza</span>
                        <h1 class="display-3 fw-bold text-uppercase mb-3 text-shadow">Liga XVs Femenil</h1>
                        <p class="lead mb-0 fw-semibold opacity-75">La élite del rugby union femenino en México.</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container my-5 pb-5">
        
        <!-- INTRODUCCIÓN -->
        <section class="row mb-5 justify-content-center text-center">
            <div class="col-lg-8">
                <p class="lead text-muted" style="line-height: 1.8;">
                    Reuniendo a los clubes más fuertes del país en un formato de alta exigencia física y táctica. El semillero oficial de las futuras seleccionadas nacionales.
                </p>
            </div>
        </section>

        <!-- CLUBES: GRID MODERNO -->
        <section class="mb-5 pb-5 border-bottom border-secondary border-opacity-25">
            <h3 class="fw-bold text-uppercase mb-5 text-center" style="color: var(--verde-principal); letter-spacing: 1px;">Clubes Participantes</h3>
            
            <div class="row g-4 justify-content-center">
                <?php foreach($equipos_xvs as $equipo): ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="club-card-modern p-4 text-center h-100">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3 border" style="width: 80px; height: 80px; font-size: 2rem; color: var(--verde-principal); font-weight: 900;">
                            <?= e($equipo['logo']) ?>
                        </div>
                        <h5 class="fw-bold text-dark mb-1"><?= e($equipo['nombre']) ?></h5>
                        <span class="badge bg-light text-secondary border px-3 py-1"><i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= e($equipo['estado']) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- SALÓN DE LA FAMA (Timeline/Leaderboard) -->
        <section class="mb-5">
            <div class="text-center mb-5">
                <h3 class="fw-bold text-uppercase" style="color: var(--rojo-institucional); letter-spacing: 1px;">Salón de la Fama</h3>
                <p class="text-muted">Las campeonas que han forjado nuestra historia.</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <?php foreach($historico_campeonas as $index => $historia): ?>
                    <div class="honor-row">
                        <div class="d-flex align-items-center gap-4 w-100">
                            <!-- Trofeo / Año -->
                            <div class="text-center border-end pe-4" style="min-width: 120px;">
                                <i class="bi bi-trophy-fill fs-4 <?= $index === 0 ? 'text-warning' : 'text-secondary opacity-50' ?>"></i>
                                <div class="fw-bold text-dark mt-1"><?= e($historia['temporada']) ?></div>
                            </div>
                            
                            <!-- Campeón y Subcampeón -->
                            <div class="flex-grow-1">
                                <h4 class="fw-bold mb-1" style="color: var(--verde-principal);"><?= e($historia['campeon']) ?> <i class="bi bi-patch-check-fill ms-1 text-primary fs-6"></i></h4>
                                <div class="text-muted small">Subcampeón: <strong><?= e($historia['subcampeon']) ?></strong></div>
                            </div>
                            
                            <!-- MVP -->
                            <div class="text-end d-none d-md-block">
                                <div class="text-uppercase small fw-bold text-muted" style="letter-spacing: 1px;">MVP Final</div>
                                <div class="fw-semibold text-dark"><i class="bi bi-star-fill text-warning me-1"></i> <?= e($historia['mvp']) ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>