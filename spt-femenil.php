<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - SERPIENTES FEMENIL
 * Arquitectura Segura - OWASP Compliance
 * BASADO EN MANUAL DE IDENTIDAD FMRU 2026
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); 
ini_set('session.use_only_cookies', 1);
session_start();

require_once __DIR__ . '/admin/config/db.php';
global $db;

$settings = $db->getAllSettings();

// 1. Equipos Participantes (Dinámicos desde BD)
$equipos_xvs = json_decode($settings['sptf_equipos'] ?? '[]', true) ?: [];

// 2. Staff Técnico (Dinámico desde BD)
$staff = json_decode($settings['sptf_staff'] ?? '[]', true) ?: [];

// 3. Histórico de Campeonas (Dinámico desde BD)
$historico_campeonas = json_decode($settings['sptf_historico'] ?? '[]', true) ?: [];

$page_title = "Serpientes Femenil"; 

include 'includes/head.php';
include 'includes/navbar.php'; 
?>

    <!-- BANNER HERO DINÁMICO -->
    <header class="position-relative" style="background-color: var(--verde-oscuro); background-image: url('<?= e($settings['sptf_hero_img'] ?? 'assets/img/banner/BloodFest26-1589.jpg') ?>'); background-size: cover; background-position: center; min-height: 60vh; display: flex; align-items: center;">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.3);"></div>
        <div class="container position-relative z-2">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="glass-panel text-white shadow-lg">
                        <span class="badge bg-success mb-3 px-3 py-2 fw-bold text-uppercase" style="letter-spacing: 2px;">
                            <?= e($settings['sptf_hero_badge'] ?? 'Serpientes') ?>
                        </span>
                        <h1 class="display-3 fw-bold text-uppercase mb-3 text-shadow">
                            <?= e($settings['sptf_hero_titulo'] ?? 'Liga de Rugby Femenil') ?>
                        </h1>
                        <p class="lead mb-0 fw-semibold opacity-75">
                            <?= e($settings['sptf_hero_sub'] ?? 'La máxima categoría del rugby union femenil en México.') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="container my-5">
        
        <!-- SECCIÓN: INTRODUCCIÓN (HTML ENRIQUECIDO) -->
        <section class="row mb-5 text-center">
            <div class="col-lg-8 mx-auto">
                <div class="fs-5 text-muted" style="line-height: 1.8;">
                    <?= $settings['sptf_intro'] ?? '<p>El Campeonato Nacional de Rugby XVs Femenil es la competición de mayor nivel y exigencia física en nuestro país.</p>' ?>
                </div>
            </div>
        </section>

        <!-- SECCIÓN: EQUIPOS PARTICIPANTES -->
        <section class="mb-5 pb-4 border-bottom">
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="page-title">Equipos Participantes (Temporada Actual)</h2>
                </div>
            </div>
            
            <div class="row g-4">
                <?php foreach($equipos_xvs as $equipo): ?>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="team-card">
                        <div class="team-logo-placeholder">
                            <?= e($equipo['logo'] ?? mb_strtoupper(mb_substr($equipo['nombre'], 0, 1, 'UTF-8'), 'UTF-8')) ?>
                        </div>
                        <h4 class="team-name"><?= e($equipo['nombre']) ?></h4>
                        <div class="team-state"><i class="bi bi-geo-alt-fill text-danger me-1"></i><?= e($equipo['estado']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- SECCIÓN: STAFF TÉCNICO (DINÁMICO) -->
        <?php if (!empty($staff)): ?>
        <section class="mb-5 pb-5 border-bottom">
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="section-title border-0 pb-0 mb-2">Staff Técnico</h2>
                </div>
            </div>
            
            <div class="row g-3">
                <?php foreach($staff as $miembro): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="staff-card shadow-sm bg-white">
                        <div class="staff-icon">
                            <i class="bi <?= e($miembro['icono'] ?? 'bi-person-badge-fill') ?>"></i>
                        </div>
                        <div>
                            <div class="staff-role"><?= e($miembro['rol']) ?></div>
                            <h4 class="staff-name"><?= e($miembro['nombre']) ?></h4>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- SECCIÓN: HISTÓRICO DE CAMPEONATOS (DINÁMICO) -->
        <section class="mb-5">
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="page-title">Histórico de Campeonas</h2>
                    <p class="text-muted">Registro de los clubes que han conquistado el título nacional a lo largo de los años.</p>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="historical-table-container table-responsive">
                        <table class="table table-borderless table-historic mb-0">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 60px;" class="text-center"><i class="bi bi-trophy-fill text-warning fs-5"></i></th>
                                    <th scope="col">Temporada</th>
                                    <th scope="col">Campeón (1er Lugar)</th>
                                    <th scope="col">Subcampeón (2do Lugar)</th>
                                    <th scope="col">Jugadora Destacada (MVP)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($historico_campeonas as $index => $historia): ?>
                                <tr>
                                    <td class="text-center fw-bold text-muted"><?= $index + 1 ?></td>
                                    <td class="fw-semibold text-dark"><?= e($historia['temporada']) ?></td>
                                    <td><span class="champ-name"><i class="bi bi-star-fill text-warning me-1 small"></i> <?= e($historia['campeon']) ?></span></td>
                                    <td><?= e($historia['subcampeon']) ?></td>
                                    <td class="fst-italic text-secondary"><?= e($historia['mvp']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- FOOTER MODULAR -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>