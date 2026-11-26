<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - ESCOLARES / UNIVERSITARIOS
 * Arquitectura Segura - OWASP Compliance
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); 
ini_set('session.use_only_cookies', 1);
session_start();

require_once __DIR__ . '/admin/config/db.php';
global $db;

$settings = $db->getAllSettings();
$page_title = "Escolares y Universitarios"; 

// Recuperar tarjetas en formato JSON
$tarjetas_json = $settings['esc_tarjetas'] ?? '[]';
$tarjetas = json_decode($tarjetas_json, true) ?: [];

// Adaptación inteligente del Grid de Bootstrap según el total de tarjetas
$col_class = (count($tarjetas) === 1) ? 'col-lg-8' : ((count($tarjetas) === 2 || count($tarjetas) === 4) ? 'col-lg-6' : 'col-lg-4');
$border_colors = ['var(--verde-principal)', 'var(--rojo-institucional)', 'var(--azul-oscuro)', '#ffc107', '#17a2b8'];

include 'includes/head.php';
include 'includes/navbar.php'; 
?>

    <!-- BANNER HERO DINÁMICO (ESTILO GLASSMORPHISM) -->
    <header class="position-relative" style="background-color: var(--verde-oscuro); background-image: url('<?= e($settings['esc_hero_img'] ?? 'assets/img/banner/06_competencias_escolares_universitarias.jpg') ?>'); background-size: cover; background-position: center; min-height: 60vh; display: flex; align-items: center;">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.3);"></div>
        <div class="container position-relative z-2">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="glass-panel text-white shadow-lg">
                        <?php $badge_esc = trim($settings['esc_hero_badge'] ?? 'Competencias'); ?>
                        <?php if ($badge_esc !== ''): ?>
                            <span class="badge bg-success mb-3 px-3 py-2 fw-bold text-uppercase" style="letter-spacing: 2px;">
                                <?= e($badge_esc) ?>
                            </span>
                        <?php endif; ?>
                        <h1 class="display-3 fw-bold text-uppercase mb-3 text-shadow">
                            <?= e($settings['esc_hero_titulo'] ?? 'DESARROLLO ACADÉMICO') ?>
                        </h1>
                        <p class="lead mb-0 fw-semibold opacity-75">
                            <?= e($settings['esc_hero_sub'] ?? 'Forjando valores desde las escuelas hasta la universidad.') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container my-5 pb-5">
        
        <!-- TEXTO INTRODUCTORIO (HTML ENRIQUECIDO CON TINYMCE) -->
        <section class="row justify-content-center mb-5">
            <div class="col-lg-10 fs-5 text-dark" style="line-height: 1.8;">
                <?= $settings['esc_intro'] ?? '<p class="text-center">En la federación creemos que los espacios escolares son idóneos para practicar el rugby.</p>' ?>
            </div>
        </section>

        <!-- TARJETAS DE COMPETENCIAS (Bucle Dinámico) -->
        <section class="row g-4 justify-content-center mb-5 pb-5 border-bottom border-secondary border-opacity-25">
            <?php foreach($tarjetas as $i => $t): ?>
            <div class="<?= $col_class ?> col-md-6 transition-scale">
                <div class="card h-100 border-0 shadow-sm rounded-4 text-center px-4 py-5" style="border-top: 5px solid <?= $border_colors[$i % count($border_colors)] ?> !important;">
                    <h4 class="fw-bold mb-4 text-dark"><?= e($t['titulo']) ?></h4>
                    <div class="card-text text-muted" style="line-height: 1.6;">
                        <!-- Se imprime el texto respetando el HTML del panel (negritas, alineación, etc) -->
                        <?= $t['texto'] ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </section>

        <!-- CALL TO ACTION FINAL -->
        <section class="text-center pt-3">
            <h3 class="fw-bold mb-4" style="color: var(--azul-oscuro);">¿Te interesa participar en alguno?</h3>
            <a href="mailto:desarrollo@mexrugby.com" class="btn btn-rojo rounded-pill px-5 py-3 fw-bold shadow">
                <i class="bi bi-envelope-fill me-2"></i> Escríbenos a desarrollo@mexrugby.com
            </a>
        </section>

    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>