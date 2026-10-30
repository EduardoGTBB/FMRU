<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - MESA DIRECTIVA
 * Arquitectura Segura - OWASP Compliance
 * BASADO EN MANUAL DE IDENTIDAD FMRU 2026
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); 
ini_set('session.use_only_cookies', 1);
session_start();

function e($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// Datos de la Mesa Directiva 2025-2029
$mesa_directiva = [
    ["cargo" => "Presidente", "persona" => "Pablo J. Septien"],
    ["cargo" => "Vicepresidente administrativo", "persona" => "Ernesto Sainz"],
    ["cargo" => "Vicepresidente deportivo", "persona" => "Camilon Falcón"],
    ["cargo" => "Tesorero", "persona" => "Rafael Díaz"],
    ["cargo" => "Secretario", "persona" => "Francisco Echeguren"],
    ["cargo" => "ProSecretario", "persona" => "Jeffrey Clarke"],
    ["cargo" => "Vocal 1", "persona" => "Cristina Flores"],
    ["cargo" => "Vocal 2", "persona" => "Gabriela Garcia"],
    ["cargo" => "Vocal 3", "persona" => "Michelle Farah"],
    ["cargo" => "Vocal Atleta", "persona" => "Zoé Tuvu"],
    ["cargo" => "Vocal Atleta", "persona" => "Erik Castillo"]
];

// Define el título de la página
$page_title = "Inicio"; 

include 'includes/head.php';
include 'includes/navbar.php'; 
?>

    <!-- BANNER HERO -->
    <header class="hero-banner-directiva">
        <div class="container">
            <h1 class="display-4 fw-bold text-uppercase">Mesa Directiva</h1>
            <p class="lead">Dirigimos, planificamos y construimos nuestro deporte.</p>
        </div>
    </header>

    <main class="container my-5">
        
        <!-- INTRODUCCIÓN -->
        <section class="row mb-5">
            <div class="col-lg-12 text-center mb-4">
                <h2 class="page-title">Nuestro Propósito</h2>
                <p class="lead text-muted mx-auto" style="max-width: 900px;">
                    Como Mesa Directiva nosotros, Dirigimos, Planificamos, Promovemos, Administramos, Gestionamos y construimos a nuestro deporte, desde lo más básico hasta las selecciones nacionales y por toda la República Mexicana.
                </p>
                <p class="text-muted mx-auto" style="max-width: 900px;">
                    La Mesa Directiva es la encargada del marco estratégico y de establecer el rumbo y políticas a seguir dentro del Rugby en México. Somos también, los responsables de las decisiones y acciones que se tomen por la dirección y equipo de apoyo de la Federación. Como Directivos, nuestra principal conducta y responsabilidad gira entorno al liderazgo y la meritocracia.
                </p>
            </div>
        </section>

        <!-- MISIÓN Y VISIÓN DIRECTIVA -->
        <section class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="info-card">
                    <h3 class="sub-title">Nuestra Misión</h3>
                    <p class="mb-0 text-muted">Unir, Consolidar, Desarrollar y hacer Trascender al Rugby en la República Mexicana.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-card" style="border-top-color: var(--rojo-institucional);">
                    <h3 class="sub-title">Nuestra Visión</h3>
                    <p class="mb-0 text-muted">Transformar el funcionamiento de nuestra organización a una estructura sólida de servicios que generará un constante crecimiento de asociados y aumentará el nivel de competencia.</p>
                </div>
            </div>
        </section>

        <!-- CONSTITUCIÓN E INTEGRANTES -->
        <section class="row mb-5">
            <div class="col-lg-5 mb-4 mb-lg-0">
                <h2 class="page-title">Constitución</h2>
                <p class="text-muted">La Mesa Directiva está constituida por: Presidente, Vicepresidente Administrativo, Vicepresidente Deportivo, Secretario, Tesorero y Vocales.</p>
                <p class="text-muted">Todos ellos voluntarios y electos a través de votación por los miembros que estén vigentes al momento. La Mesa Directiva se vota cada cuatro años y se puede reelegir solamente una sola vez, es decir, que el periodo máximo de cada presidente puede ser de ocho años.</p>
                
                <h2 class="page-title mt-4">Nuestros Miembros</h2>
                <p class="text-muted">Se consideran Miembros a todos los Asociados de la Federación; a todos aquellos Clubes y Organismos afines que satisfagan los requisitos establecidos por los Estatutos para su afiliación.</p>
                <p class="text-muted">La Federación considera como Clubes a las Asociaciones Civiles de las Entidades Federativas y considera como Organismo Afín a la Asociación Civil que realizan actividades cuyo fin no implique la competencia deportiva, pero que tenga por objeto realizar actividades vinculadas y en favor del Rugby. Las Asociaciones Estatales de Rugby pueden ser Asociados de la Federación, pero serán consideradas como Organismos Afines.</p>
            </div>
            
            <div class="col-lg-7">
                <div class="bg-white p-4 rounded shadow-sm border">
                    <h3 class="sub-title mb-4 text-center text-dark">Periodo 2025 - 2029</h3>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 directiva-table">
                            <thead>
                                <tr>
                                    <th scope="col" style="border-top-left-radius: 6px;">Cargo</th>
                                    <th scope="col" style="border-top-right-radius: 6px;">Persona</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($mesa_directiva as $miembro): ?>
                                <tr>
                                    <td class="cargo-text"><?= e($miembro['cargo']) ?></td>
                                    <td class="persona-text"><?= e($miembro['persona']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- ASAMBLEA DE CLUBES (Mención Visual/Placeholder) -->
        <section class="row mb-5 text-center">
            <div class="col-12">
                <h2 class="page-title">Asamblea de Clubes y Asociaciones</h2>
                <p class="text-muted mb-4">La asamblea de clubes está constituida por entidades deportivas a nivel nacional y la Federación reconoce diversas asociaciones estatales.</p>
                <div class="d-flex justify-content-center flex-wrap gap-4 opacity-75">
                    <!-- Aquí se integrarán los logos de clubes y asociaciones mencionados en el PDF -->
                    <span class="badge bg-light text-dark border p-3 fs-6">Jaguares</span>
                    <span class="badge bg-light text-dark border p-3 fs-6">Cumiyais</span>
                    <span class="badge bg-light text-dark border p-3 fs-6">Coyotes</span>
                    <span class="badge bg-light text-dark border p-3 fs-6">Pumas UNAM</span>
                    <span class="badge bg-light text-dark border p-3 fs-6">Rugby Querétaro</span>
                </div>
            </div>
        </section>

    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('currentYear').textContent = new Date().getFullYear();
        });
    </script>
</body>
</html>