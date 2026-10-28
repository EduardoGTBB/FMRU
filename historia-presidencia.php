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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historia y Presidencia | Federación Mexicana de Rugby</title>
    
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
            margin-bottom: 2rem;
        }

        /* Timeline CSS */
        .timeline {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
            padding-left: 50px;
        }
        
        .timeline::after {
            content: '';
            position: absolute;
            width: 4px;
            background-color: #e9ecef;
            top: 0;
            bottom: 0;
            left: 23px;
            margin-left: -2px;
        }

        .timeline-item {
            position: relative;
            background-color: inherit;
            width: 100%;
            margin-bottom: 30px;
        }

        .timeline-item::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            right: -10px;
            background-color: white;
            border: 4px solid var(--rojo-institucional);
            top: 15px;
            border-radius: 50%;
            z-index: 1;
            left: -37px;
        }
        
        /* Alternar colores de los nodos del timeline */
        .timeline-item:nth-child(even)::after {
            border-color: var(--verde-principal);
        }
        .timeline-item:nth-child(3n)::after {
            border-color: var(--verde-claro);
        }

        .timeline-content {
            padding: 20px 30px;
            background-color: white;
            position: relative;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-left: 4px solid var(--verde-principal);
        }
        
        /* Estilos de Presidentes */
        .president-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            height: 100%;
            border-top: 4px solid var(--rojo-institucional);
            text-align: center;
            transition: transform 0.3s ease;
        }
        .president-card:hover {
            transform: translateY(-5px);
        }
        .president-year {
            color: #6c757d;
            font-weight: 800;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        .president-name {
            color: var(--verde-principal);
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0;
        }

        /* Banner Hero */
        .hero-banner-historia {
            background-color: var(--negro-nav);
            color: white;
            padding: 60px 0;
            text-align: center;
            /* Aquí puedes agregar una de las fotos históricas de fondo si lo deseas */
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
                            <li><a class="dropdown-item active" href="historia-presidencia.php">Historia + Presidencia</a></li>
                            <li><a class="dropdown-item" href="#">Mesa Directiva + Plan</a></li>
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
                
                !-- (El resto del footer es idéntico a index.php para mantener consistencia) --
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="text-uppercase fw-bold mb-3" style="color: var(--verde-claro);">Información Adicional</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="#" class="text-light text-decoration-none" style="transition: color 0.2s ease-in-out;" onmouseover="this.style.color='#7fb069'" onmouseout="this.style.color='white'">Antidoping</a></li>
                        <li class="mb-2"><a href="https://www.world.rugby/" target="_blank" rel="noopener noreferrer" class="text-light text-decoration-none" style="transition: color 0.2s ease-in-out;" onmouseover="this.style.color='#7fb069'" onmouseout="this.style.color='white'">World Rugby</a></li>
                        <li class="mb-2"><a href="https://rugbyamericasnorth.com/" target="_blank" rel="noopener noreferrer" class="text-light text-decoration-none" style="transition: color 0.2s ease-in-out;" onmouseover="this.style.color='#7fb069'" onmouseout="this.style.color='white'">Rugby Americas North</a></li>
                        <li class="mb-2"><a href="#" class="text-light text-decoration-none" style="transition: color 0.2s ease-in-out;" onmouseover="this.style.color='#7fb069'" onmouseout="this.style.color='white'">Políticas de Privacidad</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-12 mb-4">
                    <h6 class="text-uppercase fw-bold mb-3" style="color: var(--rojo-institucional);">Contacto</h6>
                    <ul class="list-unstyled small text-light text-opacity-75 mb-4">
                        <li class="mb-2"><i class="bi bi-envelope-fill me-2 text-danger"></i> comunicacion@mexrugby.com</li>
                        <li class="mb-2"><i class="bi bi-telephone-fill me-2 text-danger"></i> (+52 55) 5576 1274</li>
                    </ul>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('currentYear').textContent = new Date().getFullYear();
        });
    </script>
</body>
</html>