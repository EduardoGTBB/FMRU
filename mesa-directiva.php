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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesa Directiva | Federación Mexicana de Rugby</title>
    
    <!-- Tipografía Oficial: Open Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Enlace a estilos globales -->
    <link rel="stylesheet" href="assets/css/styles.css">
    
    <!-- <style>
        /* Títulos */
        .page-title {
            font-weight: 800;
            color: var(--verde-principal);
            text-transform: uppercase;
            border-bottom: 4px solid var(--verde-claro);
            padding-bottom: 5px;
            display: inline-block;
            margin-bottom: 1.5rem;
        }
        
        .sub-title {
            font-weight: 800;
            color: var(--rojo-institucional);
            text-transform: uppercase;
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }

        /* Banner Hero */
        .hero-banner-directiva {
            background-color: var(--negro-nav);
            background-image: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('assets/img/banner/Mesa-de-trabajo-25.png');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        /* Tarjetas de Información */
        .info-card {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            height: 100%;
            border-top: 4px solid var(--verde-claro);
        }

        /* Tabla de Directiva */
        .directiva-table th { 
            background-color: var(--verde-principal); 
            color: white; 
            font-weight: 800; 
            text-transform: uppercase; 
            font-size: 0.9rem; 
            letter-spacing: 0.5px; 
            border: none; 
            padding: 15px;
        }
        .directiva-table td { 
            vertical-align: middle; 
            font-size: 1rem; 
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }
        .directiva-table tr:hover {
            background-color: rgba(127, 176, 105, 0.1);
        }
        .cargo-text {
            font-weight: 600;
            color: var(--verde-oscuro);
        }
        .persona-text {
            font-weight: 800;
            color: var(--negro-nav);
        }
    </style> -->
</head>
<body>

    <!-- BARRA SUPERIOR Y MENÚ 
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand bg-white p-2 rounded" href="index.php">
                <img src="assets/img/logo/rugby-mexico-logo.png" alt="Logo FMRU" height="50" style="object-fit: contain;">
            </a>
            <button class="navbar-toggler border-white text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="bi bi-list fs-1"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" data-bs-toggle="dropdown">Federación</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="quienes-somos.php">Quiénes Somos (Misión/Visión)</a></li>
                            <li><a class="dropdown-item" href="historia-presidencia.php">Historia + Presidencia</a></li>
                            <li><a class="dropdown-item active" href="mesa-directiva.php">Mesa Directiva + Plan</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Competencias</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">XVs Varonil</a></li>
                            <li><a class="dropdown-item" href="#">XVs Femenil</a></li>
                            <li><a class="dropdown-item" href="#">Game On</a></li>
                            <li><a class="dropdown-item" href="#">Escolares / Universitarios</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Serpientes</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Femenil</a></li>
                            <li><a class="dropdown-item" href="#">Varonil</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#">Documentos</a></li>
                </ul>
                <div class="d-flex">
                    <a href="URL_REGISTRO_SPORTPLUS" class="btn btn-rojo rounded-pill px-4"><i class="bi bi-person-circle me-1"></i> Regístrate / Ingresar</a>
                </div>
            </div>
        </div>
    </nav>-->

    <?php include 'includes/navbar.php'; ?>

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

    <!-- FOOTER 
    <footer class="bg-dark text-white pt-5 pb-3 border-top" style="border-color: var(--verde-claro) !important; border-width: 5px !important;">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-md-6 mb-4">
                    <img src="assets/img/logo/rugby-mexico-logo.png" alt="Logo Rugby México" height="60" class="mb-3 bg-white p-2 rounded" style="object-fit: contain;">
                    <h6 class="fw-bold mb-2">FEDERACIÓN MEXICANA DE RUGBY, A.C.</h6>
                    <p class="small text-light text-opacity-75 pe-md-4 lh-lg">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                        Vicente Segura 10 Casa 2, Lomas de Sotelo,<br>
                        Naucalpan de Juárez, Estado de México, C.P. 53390
                    </p>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="text-uppercase fw-bold mb-3" style="color: var(--verde-claro);">Información Adicional</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="#" class="text-light text-decoration-none custom-hover-link">Antidoping</a></li>
                        <li class="mb-2"><a href="https://www.world.rugby/" target="_blank" rel="noopener noreferrer" class="text-light text-decoration-none custom-hover-link">World Rugby</a></li>
                        <li class="mb-2"><a href="https://rugbyamericasnorth.com/" target="_blank" rel="noopener noreferrer" class="text-light text-decoration-none custom-hover-link">Rugby Americas North</a></li>
                        <li class="mb-2"><a href="#" class="text-light text-decoration-none custom-hover-link">Políticas de Privacidad</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-12 mb-4">
                    <h6 class="text-uppercase fw-bold mb-3" style="color: var(--rojo-institucional);">Contacto</h6>
                    <ul class="list-unstyled small text-light text-opacity-75 mb-4">
                        <li class="mb-2">
                            <a href="mailto:comunicacion@mexrugby.com" class="text-light text-decoration-none custom-hover-link">
                                <i class="bi bi-envelope-fill me-2 text-danger"></i> comunicacion@mexrugby.com
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="tel:+525555761274" class="text-light text-decoration-none custom-hover-link">
                                <i class="bi bi-telephone-fill me-2 text-danger"></i> (+52 55) 5576 1274
                            </a>
                        </li>
                    </ul>
                    
                    <h6 class="text-uppercase fw-bold mb-3" style="color: var(--verde-claro);">Redes Sociales</h6>
                    <div class="d-flex align-items-center gap-3">
                        <a href="https://www.facebook.com/RugbyMexico" class="text-white d-flex align-items-center justify-content-center transition-scale" target="_blank" aria-label="Facebook" style="width: 32px; height: 32px;"><i class="bi bi-facebook fs-4"></i></a>
                        <a href="https://www.instagram.com/rugby_mexico/?hl=es" class="text-white d-flex align-items-center justify-content-center transition-scale" target="_blank" aria-label="Instagram" style="width: 32px; height: 32px;"><i class="bi bi-instagram fs-4"></i></a>
                        <a href="https://x.com/Rugby_Mexico" class="text-white d-flex align-items-center justify-content-center transition-scale" target="_blank" aria-label="Twitter / X" style="width: 32px; height: 32px;"><i class="bi bi-twitter-x fs-4"></i></a>
                        <a href="https://www.youtube.com/user/FMRUOficial" class="text-white d-flex align-items-center justify-content-center transition-scale" target="_blank" aria-label="YouTube" style="width: 32px; height: 32px;"><i class="bi bi-youtube fs-3"></i></a>
                    </div>
                </div>
            </div>
            <hr class="border-secondary mt-3 opacity-25">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start small text-muted">
                    &copy; <span id="currentYear"></span> Federación Mexicana de Rugby, A.C. Todos los derechos reservados.
                </div>
            </div>
        </div>
    </footer>-->

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