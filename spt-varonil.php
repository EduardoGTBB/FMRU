<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - SERPIENTES VARONIL
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
$equipos_xvs = json_decode($settings['sptv_equipos'] ?? '[]', true) ?: [];

// 2. Staff Técnico (Dinámico desde BD)
$staff = json_decode($settings['sptv_staff'] ?? '[]', true) ?: [];

// 3. Histórico de Seleccionados Nacionales (Dinámico desde BD)
$historico = json_decode($settings['sptv_historico'] ?? '[]', true) ?: [];

$page_title = "Serpientes Varonil"; 

include 'includes/head.php';
include 'includes/navbar.php'; 
?>

    <!-- BANNER HERO DINÁMICO -->
    <header class="position-relative" style="background-color: var(--verde-oscuro); background-image: url('<?= e($settings['sptv_hero_img'] ?? 'assets/img/banner/01_competencia_xv_varonil.jpg') ?>'); background-size: cover; background-position: center; min-height: 60vh; display: flex; align-items: center;">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.3);"></div>
        <div class="container position-relative z-2">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="glass-panel text-white shadow-lg">
                        <span class="badge bg-success mb-3 px-3 py-2 fw-bold text-uppercase" style="letter-spacing: 2px;">
                            <?= e($settings['sptv_hero_badge'] ?? 'Serpientes') ?>
                        </span>
                        <h1 class="display-3 fw-bold text-uppercase mb-3 text-shadow">
                            <?= e($settings['sptv_hero_titulo'] ?? 'Liga de Rugby Varonil') ?>
                        </h1>
                        <p class="lead mb-0 fw-semibold opacity-75">
                            <?= e($settings['sptv_hero_sub'] ?? 'El máximo representativo nacional de Rugby 15s en México.') ?>
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
                    <?= $settings['sptv_intro'] ?? '<p>Conoce a los seleccionados que portan la camiseta tricolor representando a México en competencias internacionales.</p>' ?>
                </div>
            </div>
        </section>

        <!-- SECCIÓN: EQUIPOS PARTICIPANTES (DINÁMICO) -->
        <?php if (!empty($equipos_xvs)): ?>
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
        <?php endif; ?>

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

        <!-- SECCIÓN: HISTÓRICO SELECCIONADOS NACIONALES (DINÁMICO) -->
        <section class="mb-5">
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="page-title">Histórico Seleccionados Nacionales</h2>
                    <p class="text-muted">Jugadores que han dejado su marca en la historia de Las Serpientes.</p>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <!-- Contenedor exterior: mantiene bordes redondeados y sombra -->
                    <div class="bg-white rounded-3 shadow-sm border overflow-hidden">
                        <!-- Contenedor interior: habilita el scroll horizontal en móviles -->
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless table-historic mb-0 align-middle">
                                <thead style="background-color: var(--gris-fondo);">
                                    <tr>
                                        <th scope="col" class="text-center py-3 text-uppercase small fw-bold text-nowrap" style="color: var(--verde-principal); min-width: 120px;">No. Serpiente</th>
                                        <th scope="col" class="py-3 text-uppercase small fw-bold text-nowrap" style="color: var(--verde-principal); min-width: 180px;">Nombre</th>
                                        <th scope="col" class="py-3 text-uppercase small fw-bold text-nowrap" style="color: var(--verde-principal); min-width: 140px;">Equipo</th>
                                        <th scope="col" class="text-center py-3 text-uppercase small fw-bold text-nowrap" style="color: var(--verde-principal); min-width: 130px;">No. Torneos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($historico as $jugador): ?>
                                    <tr class="border-bottom">
                                        <td class="text-center fw-bold text-muted py-3"><?= e($jugador['no_serpiente'] ?? '') ?></td>
                                        <td class="fw-bold text-dark py-3 text-nowrap"><?= e($jugador['nombre'] ?? '') ?></td>
                                        <td class="py-3 text-muted fw-semibold text-nowrap"><?= e($jugador['equipo'] ?? '-') ?></td>
                                        <td class="text-center fw-bold text-secondary py-3">
                                            <span class="badge bg-light text-dark border px-3 py-2 fs-6 shadow-sm"><?= e($jugador['torneos'] ?? '0') ?></span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Indicador sutil visible solo en pantallas pequeñas -->
                    <div class="text-end mt-2 d-md-none">
                        <small class="text-muted"><i class="bi bi-arrows-expand-vertical me-1" style="transform: rotate(90deg); display: inline-block;"></i> Desliza la tabla para ver más</small>
                    </div>
                </div>
            </div>

            <!-- <div class="row">
                <div class="col-12">
                    <div class="bg-white rounded-3 shadow-sm border overflow-hidden table-responsive">
                        <table class="table table-hover table-borderless table-historic mb-0 align-middle">
                            <thead style="background-color: var(--gris-fondo);">
                                <tr>
                                    <th scope="col" class="text-center py-3 text-uppercase small fw-bold" style="color: var(--verde-principal); width: 140px;">No. Serpiente</th>
                                    <th scope="col" class="py-3 text-uppercase small fw-bold" style="color: var(--verde-principal);">Nombre</th>
                                    <th scope="col" class="py-3 text-uppercase small fw-bold" style="color: var(--verde-principal);">Equipo</th>
                                    <th scope="col" class="text-center py-3 text-uppercase small fw-bold" style="color: var(--verde-principal); width: 150px;">No. Torneos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <php foreach($historico as $jugador): ?>
                                <tr class="border-bottom">
                                    <td class="text-center fw-bold text-muted py-3"><= e($jugador['no_serpiente']  '') ?></td>
                                    <td class="fw-bold text-dark py-3"><= e($jugador['nombre'] ?? '') ?></td>
                                    <td class="py-3 text-muted fw-semibold"><= e($jugador['equipo'] ?? '-') ?></td>
                                    <td class="text-center fw-bold text-secondary py-3">
                                        <span class="badge bg-light text-dark border px-3 py-2 fs-6 shadow-sm"><= e($jugador['torneos'] ?? '0') ?></span>
                                    </td>
                                </tr>
                                <php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> -->
        </section>

    </main>

    <!-- FOOTER MODULAR -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>