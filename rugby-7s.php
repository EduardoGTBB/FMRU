<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - RUGBY 7S
 * Arquitectura Segura - OWASP Compliance
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); 
ini_set('session.use_only_cookies', 1);
session_start();

require_once __DIR__ . '/admin/config/db.php';
global $db;

$settings = $db->getAllSettings();
$tarjetas = json_decode($settings['r7s_tarjetas'] ?? '[]', true) ?: [];

// Adaptación automática del grid según el número de tarjetas
$col_class = (count($tarjetas) === 1) ? 'col-lg-8' : ((count($tarjetas) === 2 || count($tarjetas) === 4) ? 'col-lg-6' : 'col-lg-4');
$border_colors = ['var(--verde-principal)', 'var(--rojo-institucional)', 'var(--azul-oscuro)', 'var(--verde-claro)'];
$icon_colors   = ['var(--rojo-institucional)', 'var(--verde-principal)', 'var(--azul-oscuro)', 'var(--verde-claro)'];

$page_title = "Rugby 7s"; 

include 'includes/head.php';
include 'includes/navbar.php'; 
?>

    <!-- BANNER HERO DINÁMICO -->
    <header class="position-relative" style="background-color: var(--verde-oscuro); background-image: url('<?= e($settings['r7s_hero_img'] ?? 'assets/img/banner/Mesa-de-trabajo-25.png') ?>'); background-size: cover; background-position: center; min-height: 60vh; display: flex; align-items: center;">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.35);"></div>
        <div class="container position-relative z-2">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="glass-panel text-white shadow-lg">
                        <span class="badge bg-success mb-3 px-3 py-2 fw-bold text-uppercase" style="letter-spacing: 2px;">
                            <?= e($settings['r7s_hero_badge'] ?? 'COMPETENCIAS') ?>
                        </span>
                        <h1 class="display-3 fw-bold text-uppercase mb-3 text-shadow">
                            <?= e($settings['r7s_hero_titulo'] ?? 'Rugby 7s México') ?>
                        </h1>
                        <p class="lead mb-0 fw-semibold opacity-75">
                            <?= e($settings['r7s_hero_sub'] ?? 'Velocidad, destreza y la máxima intensidad olímpica en el terreno de juego.') ?>
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
                    <?= e($settings['r7s_intro_titulo'] ?? '¿Qué es el Rugby 7s?') ?>
                </h2>
                <div class="fs-5 text-muted" style="line-height: 1.8;">
                    <?= $settings['r7s_intro'] ?? '<p>El <strong>Rugby Sevens (7s)</strong> es la modalidad olímpica más dinámica y vertiginosa de nuestro deporte.</p>' ?>
                </div>
            </div>
        </section>

        <!-- BLOQUES DESTACADOS (TARJETAS DINÁMICAS) -->
        <section class="row g-4 justify-content-center mb-5">
            <?php foreach($tarjetas as $i => $t): ?>
            <div class="<?= $col_class ?> col-md-6">
                <div class="variant-card" style="border-top-color: <?= $border_colors[$i % count($border_colors)] ?>;">
                    <div class="variant-icon" style="color: <?= $icon_colors[$i % count($icon_colors)] ?>;">
                        <i class="bi <?= e($t['icono'] ?? 'bi-trophy-fill') ?>"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-3"><?= e($t['titulo']) ?></h3>
                    <div class="text-muted small" style="line-height: 1.6;">
                        <?= $t['texto'] ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </section>

        <!-- CALL TO ACTION -->
        <section class="bg-light rounded-4 p-5 text-center shadow-sm border">
            <h3 class="fw-bold mb-3 text-dark">¿Listo para competir en el circuito de 7s?</h3>
            <p class="text-muted mb-4">Consulta el calendario de torneos oficiales o encuentra un club afiliado cerca de ti para sumarte a la acción.</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="jornadas.php" class="btn btn-rojo rounded-pill px-4"><i class="bi bi-calendar-event me-2"></i> Ver Jornadas</a>
                <a href="index.php#directorio-section" class="btn btn-outline-dark rounded-pill px-4 fw-bold">Buscar Club</a>
            </div>
        </section>

    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>