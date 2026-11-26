<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - GAME ON
 * Arquitectura Segura - OWASP Compliance
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); 
ini_set('session.use_only_cookies', 1);
session_start();

require_once __DIR__ . '/admin/config/db.php';
global $db;

$settings = $db->getAllSettings();
$variantes = json_decode($settings['go_tarjetas'] ?? '[]', true) ?: [];

// Adaptación automática del grid según el número de variantes
$col_class = (count($variantes) === 1) ? 'col-lg-8' : ((count($variantes) === 2 || count($variantes) === 4) ? 'col-lg-6' : 'col-lg-4');
$border_colors = ['var(--verde-claro)', 'var(--rojo-institucional)', 'var(--azul-oscuro)', 'var(--verde-principal)'];
$icon_colors   = ['var(--rojo-institucional)', 'var(--verde-principal)', 'var(--verde-claro)', 'var(--azul-oscuro)'];

$page_title = "Game On"; 

include 'includes/head.php';
include 'includes/navbar.php'; 
?>

    <!-- BANNER HERO DINÁMICO -->
    <header class="position-relative" style="background-color: var(--verde-oscuro); background-image: url('<?= e($settings['go_hero_img'] ?? 'assets/img/banner/GAME-ON-X´s-217.jpg') ?>'); background-size: cover; background-position: center; min-height: 60vh; display: flex; align-items: center;">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.3);"></div>
        <div class="container position-relative z-2">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="glass-panel text-white shadow-lg">
                        <span class="badge bg-success mb-3 px-3 py-2 fw-bold text-uppercase" style="letter-spacing: 2px;">
                            <?= e($settings['go_hero_badge'] ?? 'COMPETENCIAS') ?>
                        </span>
                        <h1 class="display-3 fw-bold text-uppercase mb-3 text-shadow">
                            <?= e($settings['go_hero_titulo'] ?? 'Game On México') ?>
                        </h1>
                        <p class="lead mb-0 fw-semibold opacity-75">
                            <?= e($settings['go_hero_sub'] ?? 'Rugby flexible y accesible para todos, en cualquier lugar.') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container my-5">
        
        <!-- INTRODUCCIÓN (HTML ENRIQUECIDO) -->
        <section class="row mb-5 text-center">
            <div class="col-lg-10 mx-auto">
                <h2 class="section-title border-0 pb-0 mb-3">
                    <?= e($settings['go_intro_titulo'] ?? '¿Qué es Game On?') ?>
                </h2>
                <div class="fs-5 text-muted" style="line-height: 1.8;">
                    <?= $settings['go_intro'] ?? '<p><strong>Game On</strong> es una iniciativa global adoptada por la FMRU para garantizar que el rugby siga siendo divertido, seguro y accesible.</p>' ?>
                </div>
            </div>
        </section>

        <!-- VARIANTES (TARJETAS DINÁMICAS) -->
        <section class="row g-4 justify-content-center mb-5">
            <?php foreach($variantes as $i => $v): ?>
            <div class="<?= $col_class ?> col-md-6">
                <div class="variant-card" style="border-top-color: <?= $border_colors[$i % count($border_colors)] ?>;">
                    <div class="variant-icon" style="color: <?= $icon_colors[$i % count($icon_colors)] ?>;">
                        <i class="bi <?= e($v['icono'] ?? 'bi-trophy-fill') ?>"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-3"><?= e($v['titulo']) ?></h3>
                    <div class="text-muted small" style="line-height: 1.6;">
                        <?= $v['texto'] ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </section>

        <!-- CALL TO ACTION -->
        <section class="bg-light rounded-4 p-5 text-center shadow-sm border">
            <h3 class="fw-bold mb-3 text-dark">¡No dejes que el juego se detenga!</h3>
            <p class="text-muted mb-4">Descarga el manual completo de adaptaciones de Game On o busca un club cerca de ti para empezar a jugar hoy mismo.</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="documentos.php" class="btn btn-rojo rounded-pill px-4"><i class="bi bi-download me-2"></i> Descargar Manual</a>
                <a href="index.php#directorio-section" class="btn btn-outline-dark rounded-pill px-4 fw-bold">Buscar Club</a>
            </div>
        </section>

    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>