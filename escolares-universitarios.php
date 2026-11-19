<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - ESCOLARES / UNIVERSITARIOS
 * Arquitectura Segura - OWASP Compliance
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); 
ini_set('session.use_only_cookies', 1);
session_start();

function e($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

$page_title = "Escolares y Universitarios"; 

include 'includes/head.php';
include 'includes/navbar.php'; 
?>

    <!-- BANNER HERO 
    <header class="hero-banner-escolar">
        <div class="container">
            <h1 class="display-4 fw-bold text-uppercase mb-3">Desarrollo Académico</h1>
            <p class="lead mb-0">Forjando valores desde las escuelas hasta la universidad.</p>
        </div>
    </header>-->
    <header class="position-relative" style="background-color: var(--verde-oscuro); background-image: url('assets/img/banner/06_competencias_escolares_universitarias.jpg'); background-size: cover; background-position: center; min-height: 60vh; display: flex; align-items: center;">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.3);"></div>
        <div class="container position-relative z-2">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="glass-panel text-white shadow-lg">
                        <span class="badge bg-success mb-3 px-3 py-2 fw-bold text-uppercase" style="letter-spacing: 2px;">Competencias</span>
                        <h1 class="display-3 fw-bold text-uppercase mb-3 text-shadow">Desarrollo Académico</h1>
                        <p class="lead mb-0 fw-semibold opacity-75">Forjando valores desde las escuelas hasta la universidad.</p> 
                    </div>
                </div>
            </div>
        </div>
    </header>


    <main class="container my-5">
        
        <!-- SECCIÓN: RUGBY ESCOLAR -->
        <section class="row align-items-center mb-5 pb-5 border-bottom">
            <div class="col-lg-6 mb-4 mb-lg-0 pe-lg-5">
                <span class="badge bg-success mb-2 px-3 py-2 text-uppercase">Nivel Básico y Medio</span>
                <h2 class="fw-bold mb-4" style="color: var(--verde-principal);">Programa de Rugby Escolar</h2>
                <p class="text-muted" style="line-height: 1.8;">
                    El rugby es la herramienta perfecta para la educación física escolar. Promueve la inclusión (hay una posición para cada tipo de cuerpo), el respeto absoluto a la autoridad (el árbitro) y el compañerismo.
                </p>
                <p class="text-muted" style="line-height: 1.8;">
                    Nuestros programas de iniciación (Tag Rugby) permiten a los colegios integrar este deporte de forma 100% segura, sin contacto, desarrollando habilidades motrices y valores fundamentales en niños y niñas por igual.
                </p>
                <ul class="list-unstyled mt-4 fw-semibold text-dark">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Capacitación gratuita para profesores de E.F.</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Kits de iniciación (Balones y Tags)</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Torneos interescolares sin contacto</li>
                </ul>
            </div>
            <div class="col-lg-6">
                <!-- Imagen de contexto: usa una general del sitio por ahora -->
                <div class="rounded-4 overflow-hidden shadow-lg border border-4" style="border-color: var(--verde-claro) !important;">
                    <img src="assets/img/banner/930e3ac546bbc894c9ac9d9c1527d3a8.png" alt="Rugby Escolar" class="img-fluid w-100 object-fit-cover" style="height: 400px;">
                </div>
            </div>
        </section>

        <!-- SECCIÓN: RUGBY UNIVERSITARIO -->
        <section class="row align-items-center mb-5 flex-lg-row-reverse">
            <div class="col-lg-6 mb-4 mb-lg-0 ps-lg-5">
                <span class="badge mb-2 px-3 py-2 text-uppercase" style="background-color: var(--rojo-institucional);">Nivel Superior (CONDDE)</span>
                <h2 class="fw-bold mb-4" style="color: var(--rojo-institucional);">Liga Universitaria</h2>
                <p class="text-muted" style="line-height: 1.8;">
                    El Campeonato Universitario es el puente entre el rugby juvenil y la primera fuerza nacional. Formamos parte del programa del <strong>Consejo Nacional del Deporte de la Educación (CONDDE)</strong>, teniendo al Rugby Sevens como deporte oficial en la Universiada Nacional.
                </p>
                <p class="text-muted" style="line-height: 1.8;">
                    Las mejores instituciones de educación superior del país compiten año con año, ofreciendo a los estudiantes-atletas la posibilidad de representar a su Alma Máter mientras continúan con su desarrollo académico.
                </p>
                <a href="mailto:comunicacion@mexrugby.com" class="btn btn-outline-dark fw-bold mt-3 rounded-pill px-4">
                    Afiliar a mi Universidad <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="col-lg-6">
                <div class="rounded-4 overflow-hidden shadow-lg border border-4" style="border-color: var(--rojo-oscuro) !important;">
                    <img src="assets/img/banner/05_competencias_game_on.jpg" alt="Rugby Universitario" class="img-fluid w-100 object-fit-cover" style="height: 400px; filter: grayscale(20%);">
                </div>
            </div>
        </section>

    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>