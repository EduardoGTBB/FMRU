<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - DESARROLLO
 * Arquitectura Segura - OWASP Compliance
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); 
ini_set('session.use_only_cookies', 1);
session_start();

require_once __DIR__ . '/admin/config/db.php';
global $db;

// Obtener los datos de la base de datos de forma dinámica
$settings = $db->getAllSettings();

$page_title = "Desarrollo"; 

include 'includes/head.php';
include 'includes/navbar.php'; 
?>

    <!-- BANNER HERO DINÁMICO (ESTILO GLASSMORPHISM) -->
    <header class="position-relative" style="background-color: var(--verde-oscuro); background-image: url('<?= e($settings['desarrollo_hero_img'] ?? 'assets/img/banner/Mesa-de-trabajo-25.png') ?>'); background-size: cover; background-position: center; min-height: 60vh; display: flex; align-items: center;">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.3);"></div>
        <div class="container position-relative z-2">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="glass-panel text-white shadow-lg">
                        <span class="badge bg-success mb-3 px-3 py-2 fw-bold text-uppercase" style="letter-spacing: 2px;">Desarrollo</span>
                        <h1 class="display-3 fw-bold text-uppercase mb-3 text-shadow">
                            <?= e($settings['desarrollo_hero_titulo'] ?? 'DESARROLLO') ?>
                        </h1>
                        <p class="lead mb-0 fw-semibold opacity-75">
                            <?= e($settings['desarrollo_hero_subtitulo'] ?? 'Mostrando que el rugby es para todos en todo México.') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container my-5">
        
        <!-- INTRODUCCIÓN -->
        <section class="row mb-5 text-center justify-content-center">
            <div class="col-lg-10">
                <!-- FIX: Se cambió .lead por .fs-5 para que Bootstrap no anule las negritas -->
                <div class="fs-5 text-muted" style="line-height: 1.8;">
                    <?= $settings['desarrollo_intro'] ?? 'La Federación Mexicana de Rugby tiene como objetivo promover el rugby en el país.' ?>
                </div>
            </div>
        </section>

        <!-- FESTIVALES INFANTILES -->
        <div class="desarrollo-section">
            <div class="row align-items-center">
                <div class="col-md-7 mb-4 mb-md-0">
                    <h2 class="desarrollo-title" style="color: var(--verde-principal);">Festivales Infantiles</h2>
                    <div class="text-muted" style="line-height: 1.8;">
                        <?= $settings['festivales_texto'] ?? 'Encuentros diseñados para que los más pequeños descubran el rugby.' ?>
                    </div>
                </div>
                <div class="col-md-5">
                    <img src="<?= e($settings['festivales_img'] ?? 'assets/img/banner/Mesa-de-trabajo-25.png') ?>" alt="Festivales Infantiles" class="desarrollo-img">
                </div>
            </div>
        </div>

        <!-- RUGBY RISING PLAY (Invertido) -->
        <div class="desarrollo-section reverse">
            <div class="row align-items-center flex-md-row-reverse">
                <div class="col-md-7 mb-4 mb-md-0 ps-md-5">
                    <h2 class="desarrollo-title" style="color: var(--rojo-institucional);">Rugby Rising Play</h2>
                    <div class="text-muted" style="line-height: 1.8;">
                        <?= $settings['rising_texto'] ?? 'Programa estratégico enfocado en potenciar el talento emergente.' ?>
                    </div>
                </div>
                <div class="col-md-5">
                    <img src="<?= e($settings['rising_img'] ?? 'assets/img/banner/Mesa-de-trabajo-25.png') ?>" alt="Rugby Rising Play" class="desarrollo-img">
                </div>
            </div>
        </div>

        <!-- GET INTO RUGBY -->
        <div class="desarrollo-section">
            <div class="row align-items-center">
                <div class="col-md-7 mb-4 mb-md-0">
                    <h2 class="desarrollo-title" style="color: var(--verde-principal);">Get Into Rugby</h2>
                    <div class="text-muted" style="line-height: 1.8;">
                        <?= $settings['gir_texto'] ?? 'Iniciativa global de World Rugby que implementamos a nivel nacional.' ?>
                    </div>
                </div>
                <div class="col-md-5">
                    <img src="<?= e($settings['gir_img'] ?? 'assets/img/banner/Mesa-de-trabajo-25.png') ?>" alt="Get Into Rugby" class="desarrollo-img">
                </div>
            </div>
        </div>

        <!-- GRASSROOTS TO GLOBAL (Invertido) -->
        <div class="desarrollo-section reverse">
            <div class="row align-items-center flex-md-row-reverse">
                <div class="col-md-7 mb-4 mb-md-0 ps-md-5">
                    <h2 class="desarrollo-title" style="color: var(--rojo-institucional);">Grassroots to Global</h2>
                    <div class="text-muted" style="line-height: 1.8;">
                        <?= $settings['grassroots_texto'] ?? 'Creando un camino claro desde las bases hasta la competencia internacional.' ?>
                    </div>
                </div>
                <div class="col-md-5">
                    <img src="<?= e($settings['grassroots_img'] ?? 'assets/img/banner/Mesa-de-trabajo-25.png') ?>" alt="Grassroots to Global" class="desarrollo-img">
                </div>
            </div>
        </div>

        <!-- CALL TO ACTION FINAL -->
<!--         <section class="bg-light rounded-4 p-5 text-center shadow-sm border mt-5">
            <h3 class="fw-bold mb-3 text-dark">¿Tienes alguna iniciativa que te gustaría exponenciar?</h3>
            <p class="text-muted mb-4">Estamos buscando aliados estratégicos para llevar el rugby a cada rincón del país.</p>
            <a href="mailto:desarrollo@mexrugby.com" class="btn btn-rojo rounded-pill px-5 py-3 fw-bold shadow-sm">
                <i class="bi bi-envelope-paper-fill me-2"></i> Escríbenos a desarrollo@mexrugby.com
            </a>
        </section>
 -->
        <!-- CALL TO ACTION FINAL -->
        <section class="text-center pt-3">
            <h3 class="fw-bold mb-4" style="color: var(--azul-oscuro);">¿Tienes alguna iniciativa que te gustaría exponenciar?</h3>
            <p class="text-muted mb-4">Estamos buscando aliados estratégicos para llevar el rugby a cada rincón del país.</p>
            <a href="mailto:desarrollo@mexrugby.com" class="btn btn-rojo rounded-pill px-5 py-3 fw-bold shadow-sm">
                <i class="bi bi-envelope-paper-fill me-2"></i> Escríbenos a desarrollo@mexrugby.com
            </a>
        </section>

    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>