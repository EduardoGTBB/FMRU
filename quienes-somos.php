<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - QUIÉNES SOMOS
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiénes Somos | Federación Mexicana de Rugby</title>
    
    <!-- Tipografía Oficial: Open Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Enlace a estilos globales -->
    <link rel="stylesheet" href="assets/css/styles.css">
    
    <!-- <style>
        /* 1. Contenedor principal: Fondo blanco y posición relativa */
        .hero-background-quienes {
            background-color: #ffffff; /* Fondo limpio garantizado */
            min-height: calc(100vh - 100px); 
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden; /* Evita desbordamientos de la imagen */
        }

        /* 2. Inyectamos la imagen en una capa independiente para bajar su opacidad */
        .hero-background-quienes::before {
            content: "";
            position: absolute;
            top: 0; right: 0; bottom: 0; left: 0;
            background-image: url('assets/img/banner/Emblema_verde.png');
            background-repeat: no-repeat;
            background-position: right center; /* Alineado a la derecha */
            background-size: 80vh auto;
            background-attachment: fixed; /* Mantiene el sutil efecto Parallax */
            opacity: 0.12; /* MAGIA AQUÍ: 12% de opacidad. Ajusta entre 0.08 y 0.20 según tu gusto */
            z-index: 0; /* Lo mandamos al fondo */
        }

        /* 3. Garantizamos que el texto quede por encima de la imagen */
        .institucional-header {
            padding: 50px 0 60px;
            width: 100%;
            position: relative;
            z-index: 1; /* Obliga al texto a estar sobre el ::before */
        }


        /* Ajuste de colores de texto a oscuro (sobreescribiendo estilos en línea con !important) */
        .hero-background-quienes .titulo-principal,
        .hero-background-quienes .titulo-seccion {
            color: #111111 !important;
        }

        .hero-background-quienes .texto-cuerpo {
            color: #333333 !important; 
        }

        .hero-background-quienes .badge-institucional {
            color: var(--verde-principal) !important;
        }

        /* Ajuste del botón para contraste en fondo claro */
        .hero-background-quienes .btn-conoce-mas {
            border: 2px solid var(--verde-principal);
            color: var(--verde-principal) !important;
        }

        .hero-background-quienes .btn-conoce-mas:hover {
            background-color: var(--verde-principal);
            color: #ffffff !important;
        }

        /* Ajuste de los tags de valores para fondo claro */
        .hero-background-quienes .valor-tag {
            border: 1px solid #cccccc;
            color: #111111;
            background-color: #f8f9fa;
        }

        .hero-background-quienes .valor-tag:hover {
            border-color: var(--rojo-institucional);
            background-color: rgba(191, 35, 51, 0.1);
            color: var(--rojo-institucional);
        }
        
        .badge-institucional {
            color: #8bc34a; 
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 2px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            display: inline-block;
        }

        .titulo-principal {
            font-weight: 800;
            font-size: 3.5rem;
            color: white;
            text-transform: uppercase;
            line-height: 1.1;
            margin-bottom: 1.5rem;
        }

        .texto-cuerpo {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #b0bec5;
            font-weight: 400;
        }

        .titulo-seccion {
            font-weight: 800;
            font-size: 2rem;
            color: white;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .btn-conoce-mas {
            background-color: transparent;
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            padding: 12px 25px;
            border-radius: 4px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .btn-conoce-mas:hover {
            border-color: var(--verde-claro);
            color: var(--verde-claro);
        }

        /* --- DRIPS: VALORES DEL RUGBY --- */
        .valores-container {
            margin-top: 60px;
            padding-bottom: 80px;
        }

        .drips-wrapper {
            display: flex;
            align-items: flex-end;
            height: 150px; 
            overflow: hidden;
            position: relative;
            background-color: transparent;
            margin-top: 30px;
        }

        .drip-item {
            flex: 1;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            border-top-right-radius: 100px;
            box-shadow: -10px 0 20px rgba(0,0,0,0.5);
        }
        
        .drip-item span {
            color: white;
            font-size: 3rem;
            font-weight: 800;
            font-family: 'Open Sans', sans-serif;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        /* Colores del manual FMRU a cada letra */
        .drip-d { background-color: var(--verde-claro); z-index: 5; } 
        .drip-r { background-color: var(--verde-principal); z-index: 4; margin-left: -30px; } 
        .drip-i { background-color: var(--verde-oscuro); z-index: 3; margin-left: -30px; } 
        .drip-p { background-color: var(--rojo-oscuro); z-index: 2; margin-left: -30px; } 
        .drip-s { background-color: var(--rojo-institucional); z-index: 1; margin-left: -30px; } 

        .valores-lista {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 30px;
        }

        .valor-tag {
            border: 1px solid rgba(255,255,255,0.1);
            padding: 10px 20px;
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
            background-color: rgba(255,255,255,0.02);
            transition: all 0.3s ease;
        }

        .valor-tag:hover {
            border-color: var(--rojo-institucional);
            background-color: rgba(191, 35, 51, 0.1);
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
                            <li><a class="dropdown-item" href="#">Quiénes Somos (Misión/Visión)</a></li>
                            <li><a class="dropdown-item" href="#">Historia + Presidencia</a></li>
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

    <!-- CONTENIDO PRINCIPAL INSTITUCIONAL (SIN CAPAS OSCURAS ENCIMA DE LA IMAGEN) -->
    <main class="hero-background-quienes">
        <div class="container institucional-header">
            <div class="row gx-5">
                <!-- Columna Izquierda: Introducción FMRU -->
                <div class="col-lg-6 mb-5 mb-lg-0 pe-lg-5">
                    <span class="badge-institucional">Quiénes Somos</span>
                    <h1 class="titulo-principal">FEDERACIÓN MEXICANA DE RUGBY</h1>
                    <p class="texto-cuerpo mb-4" style="color: #e0e0e0;">
                        Somos el organismo rector del rugby en México. Afiliados a World Rugby y Rugby Americas North, 
                        trabajamos incansablemente para desarrollar, promover y administrar el deporte en todos sus niveles; 
                        desde las categorías infantiles y de base, hasta nuestras selecciones nacionales (Las Serpientes) 
                        que representan a México con orgullo en el mundo.
                    </p>
                    <a href="#" class="btn-conoce-mas mt-3">
                        Conoce la FMRU <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <!-- Columna Derecha: Misión, Visión y Valores -->
                <div class="col-lg-6">
                    <!-- MISIÓN -->
                    <div class="mb-5">
                        <h2 class="titulo-seccion">Misión</h2>
                        <p class="texto-cuerpo" style="color: #e0e0e0;">
                            Liderar el ecosistema del rugby mexicano mediante la profesionalización de estructuras, 
                            la diversificación de ingresos y la expansión comunitaria, generando experiencias deportivas 
                            de calidad y oportunidades de desarrollo para todos.
                        </p>
                    </div>

                    <!-- VISIÓN -->
                    <div class="mb-5">
                        <h2 class="titulo-seccion">Visión</h2>
                        <p class="texto-cuerpo" style="color: #e0e0e0;">
                            Posicionar al rugby mexicano como referente mediante una gestión profesional y sostenible, 
                            construyendo alianzas estratégicas, diversificando ingresos y extendiendo la práctica del 
                            deporte a todas las regiones. Inspirar a nuevos actores y consolidar el camino hacia torneos 
                            internacionales de élite.
                        </p>
                    </div>

                    <!-- VALORES DRIPS -->
                    <div class="valores-container">
                        <h2 class="titulo-seccion">Valores del Rugby</h2>
                        
                        <!-- Gráfica DRIPS construida con CSS -->
                        <div class="drips-wrapper">
                            <div class="drip-item drip-d"><span>D</span></div>
                            <div class="drip-item drip-r"><span>R</span></div>
                            <div class="drip-item drip-i"><span>I</span></div>
                            <div class="drip-item drip-p"><span>P</span></div>
                            <div class="drip-item drip-s"><span>S</span></div>
                        </div>

                        <!-- Etiquetas de los valores desplegados -->
                        <div class="valores-lista">
                            <div class="valor-tag">Disciplina</div>
                            <div class="valor-tag">Respeto</div>
                            <div class="valor-tag">Integridad</div>
                            <div class="valor-tag">Pasión</div>
                            <div class="valor-tag">Solidaridad</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- P5: FOOTER 
    <footer class="bg-dark text-white pt-5 pb-3 border-top" style="border-color: var(--verde-claro) !important; border-width: 5px !important; margin-top: auto;">
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