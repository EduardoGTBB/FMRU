<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - HISTORIA Y PRESIDENCIA
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

// Datos de Presidentes
$presidentes = [
    ["periodo" => "2020-2025", "nombre" => "Ernesto Sainz"],
    ["periodo" => "2011-2020", "nombre" => "Francisco Echeguren"],
    ["periodo" => "2000-2011", "nombre" => "Miguel Carner"],
    ["periodo" => "1998-2000", "nombre" => "Ricardo Ganem"],
    ["periodo" => "1997-1998", "nombre" => "Carlos Gerhard"],
    ["periodo" => "1996-1997", "nombre" => "Pierre Gisquet"],
    ["periodo" => "1995-1996", "nombre" => "Armando Rosete"],
    ["periodo" => "1988-1995", "nombre" => "Pierre Gisquet"],
    ["periodo" => "1987-1988", "nombre" => "Raúl Monroy"],
    ["periodo" => "1984-1987", "nombre" => "Armando Rosete"],
    ["periodo" => "1978-1984", "nombre" => "Andrés Irvine"],
    ["periodo" => "1971-1978", "nombre" => "Walter B. Irvine"],
];

// Datos de Hitos Históricos
$hitos = [
    ["ano" => "2021", "titulo" => "PARTICIPACIÓN DE LAS SERPIENTES EN REPECHAJE OLÍMPICO", "desc" => "Las selecciones varonil y femenil participan en el repechaje para los Juegos Olímpicos de Tokio 2020. NACIONALES CONADE Torreón, Coahuila."],
    ["ano" => "2018", "titulo" => "DEBUT MUNDIAL DE RUGBY", "desc" => "Las Serpientes femenil debutó en un Mundial contra Nueva Zelanda y con tan solo 9 años de existir el rugby femenil en México."],
    ["ano" => "2017", "titulo" => "GANADORAS DEL RAN 7S", "desc" => "La selección femenil de 7s ganó el RAN 7s que se jugó en el Campo Marte de la Ciudad de México y con ello consiguió su pase al Mundial de Rugby Sevens 2018 que se llevó a cabo en San Francisco."],
    ["ano" => "2016", "titulo" => "JUEGOS OLÍMPICOS RÍO DE JANEIRO", "desc" => "Las selecciones mexicanas femenil y varonil de 7s consiguieron su pase al repechaje, último torneo en dar una plaza olímpica. La selección femenil compitió en Dublin y la selección varonil en Monaco, siendo España el ganador en ambas ramas de esa última plaza olímpica."],
    ["ano" => "2011", "titulo" => "XVI JUEGOS PANAMERICANOS EN GUADALAJARA", "desc" => "El rugby debuta como deporte panamericano y México obtuvo el sexto lugar. Reforma Athletic Club."],
    ["ano" => "2010", "titulo" => "JUEGOS CENTROAMERICANOS Y DEL CARIBE / OLIMPIADA NACIONAL", "desc" => "México participa por primera vez y gana la medalla de bronce. El rugby ingresa por primera vez a la Olimpiada Nacional. Reforma Athletic Club."],
    ["ano" => "2004", "titulo" => "AFILIACIONES OFICIALES", "desc" => "FMRU se incorpora oficialmente a la CODEME, CONADE y se une al Comité Olímpico Mexicano (COM)."],
    ["ano" => "2003", "titulo" => "FEDERACIÓN MEXICANA DE RUGBY / INTERNATIONAL RUGBY BOARD", "desc" => "Formación de Federación Mexicana de Rugby (FMRU) y aceptación de la FMRU como miembro de la IRB."],
    ["ano" => "1973", "titulo" => "UNIÓN MEXICANA DE RUGBY", "desc" => "Constitución de la Unión Mexicana de Rugby A.C. (UMR)."],
    ["ano" => "1971", "titulo" => "REFORMA ATHLETIC CLUB", "desc" => "Introducción del Rugby en México por el Mr. Walter Irvine que lo introduce en el Reforma Athletic Club."],
    ["ano" => "1930", "titulo" => "COMPAÑÍAS DE PETRÓLEO", "desc" => "Se jugó por primera vez en México en las compañías inglesas de petróleo."],
];

// Define el título de la página
$page_title = "Inicio"; 

include 'includes/head.php';
include 'includes/navbar.php'; 
?>

    <!-- BANNER HERO -->
    <header class="hero-banner-historia">
        <div class="container">
            <h1 class="display-4 fw-bold text-uppercase">Nuestra Historia</h1>
            <p class="lead">El legado del Rugby en México.</p>
        </div>
    </header>

    <main class="container my-5">
        
        <!-- SECCIÓN: HISTORIA -->
        <section class="mb-5 pb-4">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="page-title">Historia de la FMRU</h2>
                    <p class="lead mb-5 text-muted">
                        La Federación es la autoridad máxima del Rugby en la República Mexicana y está reconocida por la World Rugby (WR), la Confederación Deportiva Mexicana, A.C. (CODEME), y el Comité Olímpico Mexicano, A.C. (COM). Esta FMRU es un equipo de entusiastas voluntarios comprometidos en asegurar que nuestro deporte sea jugado por todos, a todos los niveles y por toda la República Mexicana.
                    </p>
                    
                    <div class="timeline">
                        <?php foreach($hitos as $hito): ?>
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <h4 class="fw-bold mb-2" style="color: var(--verde-principal);"><?= e($hito['ano']) ?> <span class="fs-5 text-dark ms-2">- <?= e($hito['titulo']) ?></span></h4>
                                <p class="mb-0 text-muted"><?= e($hito['desc']) ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECCIÓN: PRESIDENCIA -->
        <section class="mb-5 pt-4 border-top">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="page-title mx-auto">Presidencia</h2>
                    <p class="text-muted mt-2">Es un honor para nosotros presentarles a los presidentes que han liderado el Rugby en México.</p>
                </div>
            </div>
            
            <div class="row g-4">
                <?php foreach($presidentes as $presidente): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="president-card">
                        <div class="president-year"><?= e($presidente['periodo']) ?></div>
                        <h3 class="president-name"><?= e($presidente['nombre']) ?></h3>
                    </div>
                </div>
                <?php endforeach; ?>
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