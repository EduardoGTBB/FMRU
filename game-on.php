<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - GAME ON
 * Arquitectura Segura - OWASP Compliance
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); 
ini_set('session.use_only_cookies', 1);
session_start();

function e($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

$page_title = "Game On"; 

include 'includes/head.php';
include 'includes/navbar.php'; 
?>

    <!-- BANNER HERO -->
    <!-- <header class="hero-banner-gameon">
        <div class="container">
            <span class="badge bg-warning text-dark mb-3 px-3 py-2 fw-bold text-uppercase" style="letter-spacing: 2px;">World Rugby Initiative</span>
            <h1 class="display-4 fw-bold text-uppercase mb-3">Game On México</h1>
            <p class="lead mb-0">Rugby flexible y accesible para todos, en cualquier lugar.</p>
        </div>
    </header> -->
    <header class="position-relative" style="background-color: var(--verde-oscuro); background-image: url('assets/img/banner/GAME-ON-X´s-217.jpg'); background-size: cover; background-position: center; min-height: 60vh; display: flex; align-items: center;">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.3);"></div>
        <div class="container position-relative z-2">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="glass-panel text-white shadow-lg">
                        <span class="badge bg-success mb-3 px-3 py-2 fw-bold text-uppercase" style="letter-spacing: 2px;">COMPETENCIAS</span>
                        <h1 class="display-3 fw-bold text-uppercase mb-3 text-shadow">Game On México</h1>
                        <p class="lead mb-0 fw-semibold opacity-75">Rugby flexible y accesible para todos, en cualquier lugar.</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container my-5">
        
        <!-- INTRODUCCIÓN -->
        <section class="row mb-5 text-center">
            <div class="col-lg-10 mx-auto">
                <h2 class="section-title border-0 pb-0 mb-3">¿Qué es Game On?</h2>
                <p class="lead text-muted">
                    <strong>Game On</strong> es una iniciativa global adoptada por la FMRU para garantizar que el rugby siga siendo divertido, seguro y accesible. Proporciona flexibilidad en las reglas (tamaño de la cancha, número de jugadores, tipo de contacto) para asegurar que los partidos siempre puedan jugarse, sin importar los recursos disponibles.
                </p>
            </div>
        </section>

        <!-- VARIANTES (TARJETAS) -->
        <section class="row g-4 mb-5">
            <!-- Modalidad 1: Touch -->
            <div class="col-md-4">
                <div class="variant-card">
                    <div class="variant-icon"><i class="bi bi-hand-index-thumb"></i></div>
                    <h3 class="fw-bold text-dark mb-3">Touch Rugby</h3>
                    <p class="text-muted small">
                        Sin contacto físico pesado. El placaje se sustituye por un toque con ambas manos por debajo de la cintura. Ideal para principiantes, veteranos o para jugar en equipos mixtos.
                    </p>
                </div>
            </div>
            
            <!-- Modalidad 2: Tag -->
            <div class="col-md-4">
                <div class="variant-card" style="border-top-color: var(--rojo-institucional);">
                    <div class="variant-icon" style="color: var(--verde-principal);"><i class="bi bi-tags-fill"></i></div>
                    <h3 class="fw-bold text-dark mb-3">Tag Rugby</h3>
                    <p class="text-muted small">
                        Cada jugador lleva cintas (tags) con velcro en la cintura. La defensa detiene el avance rival arrancando una de las cintas. Es la modalidad de introducción perfecta para nivel infantil escolar.
                    </p>
                </div>
            </div>

            <!-- Modalidad 3: Reglas Flexibles -->
            <div class="col-md-4">
                <div class="variant-card" style="border-top-color: var(--azul-oscuro);">
                    <div class="variant-icon" style="color: var(--verde-claro);"><i class="bi bi-diagram-3-fill"></i></div>
                    <h3 class="fw-bold text-dark mb-3">10s & Reglas Flexibles</h3>
                    <p class="text-muted small">
                        ¿No completan 15 jugadores? Game On permite adaptar partidos oficiales a formatos de 10 o 12 jugadores, acortar el tiempo de juego, o hacer scrums simulados para priorizar la seguridad.
                    </p>
                </div>
            </div>
        </section>

        <!-- CALL TO ACTION -->
        <section class="bg-light rounded-4 p-5 text-center shadow-sm border">
            <h3 class="fw-bold mb-3 text-dark">¡No dejes que el juego se detenga!</h3>
            <p class="text-muted mb-4">Descarga el manual completo de adaptaciones de Game On o busca un club cerca de ti para empezar a jugar hoy mismo.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="documentos.php" class="btn btn-rojo rounded-pill px-4"><i class="bi bi-download me-2"></i> Descargar Manual</a>
                <a href="index.php#directorio-section" class="btn btn-outline-dark rounded-pill px-4 fw-bold">Buscar Club</a>
            </div>
        </section>

    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>