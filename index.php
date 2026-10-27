<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - HOME PAGE (PROPUESTA 2 - ACTUALIZADA)
 * Decisiones del 23/Oct/2026: Calendario arriba, Sponsors, Mapa + Tabla.
 * Arquitectura Segura - OWASP Compliance
 * ** ACTUALIZADO CON MANUAL DE IDENTIDAD FMRU **
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); 
ini_set('session.use_only_cookies', 1);
session_start();

function e($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// Datos Simulados
$proximos_eventos = [
    ["torneo" => "Torneo Nacional Mayor - Jornada 3", "equipo_local" => "Black Thunder RC", "equipo_visitante" => "Pumas UNAM", "fecha" => "24 Octubre, 2026", "hora" => "11:30 hrs", "sede" => "Deportivo Xochimilco"],
    ["torneo" => "Liga de Occidente Femenil - Semifinal", "equipo_local" => "Lobos Occidente", "equipo_visitante" => "Rhinos RC", "fecha" => "24 Octubre, 2026", "hora" => "14:00 hrs", "sede" => "Parque Metropolitano, GDL"],
];

// Datos simulados para el directorio (Con coordenadas para el mapa)
$clubes_muestra = [
    ["nombre" => "Black Thunder RC", "categoria" => "Mayor Varonil / Femenil", "estado" => "Ciudad de México", "entrenamiento" => "Mar y Jue: 20:00 hrs. Deportivo Xochimilco", "contacto" => "contacto@blackthunder.com", "redes" => ["facebook" => "#", "instagram" => "#"], "lat" => 19.2644, "lng" => -99.1036],
    ["nombre" => "Lobos Occidente", "categoria" => "Infantil / Juvenil", "estado" => "Jalisco", "entrenamiento" => "Lun y Mié: 18:00 hrs. Parque Metropolitano", "contacto" => "info@lobosrugby.mx", "redes" => ["facebook" => "#"], "lat" => 20.6720, "lng" => -103.4357],
    ["nombre" => "Titanes MTY", "categoria" => "Mayor Varonil", "estado" => "Nuevo León", "entrenamiento" => "Mar y Jue: 21:00 hrs. Estadio Universitario", "contacto" => "81 1234 5678", "redes" => ["instagram" => "#"], "lat" => 25.7275, "lng" => -100.3121],
    ["nombre" => "Pumas UNAM", "categoria" => "Universitario Varonil / Femenil", "estado" => "Ciudad de México", "entrenamiento" => "Lun, Mié, Vie: 16:00 hrs. C.U.", "contacto" => "rugby@unam.mx", "redes" => ["facebook" => "#", "instagram" => "#"], "lat" => 19.3302, "lng" => -99.1892],
    ["nombre" => "Rhinos RC", "categoria" => "Mayor Varonil", "estado" => "Jalisco", "entrenamiento" => "Mar y Jue: 19:30 hrs.", "contacto" => "rhinos@gmail.com", "redes" => ["facebook" => "#"], "lat" => 20.6800, "lng" => -103.3500],
    ["nombre" => "Borregos MTY", "categoria" => "Universitario Varonil", "estado" => "Nuevo León", "entrenamiento" => "Lun a Vie: 07:00 hrs.", "contacto" => "deportes@tec.mx", "redes" => ["instagram" => "#"], "lat" => 25.6514, "lng" => -100.2902],
    ["nombre" => "Dragones Celestes", "categoria" => "Infantil", "estado" => "Estado de México", "entrenamiento" => "Sáb: 09:00 hrs.", "contacto" => "dragones@hotmail.com", "redes" => ["facebook" => "#"], "lat" => 19.4978, "lng" => -99.2367],
    ["nombre" => "Tiburones Cancún", "categoria" => "Mayor Varonil / Femenil", "estado" => "Quintana Roo", "entrenamiento" => "Mar y Jue: 20:00 hrs.", "contacto" => "tiburones@rugby.com", "redes" => ["facebook" => "#", "instagram" => "#"], "lat" => 21.1619, "lng" => -86.8515],
    ["nombre" => "Jaguares Fes", "categoria" => "Universitario Varonil", "estado" => "Estado de México", "entrenamiento" => "Lun y Jue: 15:00 hrs. FES Acatlán", "contacto" => "jaguares@unam.mx", "redes" => ["facebook" => "#"], "lat" => 19.4891, "lng" => -99.2452],
    ["nombre" => "Leones RC", "categoria" => "Infantil / Juvenil", "estado" => "Ciudad de México", "entrenamiento" => "Sab: 10:00 hrs. Bosque de Tlalpan", "contacto" => "contacto@leones.mx", "redes" => ["instagram" => "#", "facebook" => "#"], "lat" => 19.2900, "lng" => -99.1900],
    ["nombre" => "Gladiadores del Sur", "categoria" => "Mayor Varonil", "estado" => "Yucatán", "entrenamiento" => "Mie y Vie: 19:00 hrs.", "contacto" => "gladiadores@sur.com", "redes" => ["facebook" => "#"], "lat" => 20.9674, "lng" => -89.6236],
    ["nombre" => "Espartanos", "categoria" => "Mayor Femenil", "estado" => "Guanajuato", "entrenamiento" => "Mar y Jue: 20:30 hrs.", "contacto" => "espartanas@gmail.com", "redes" => ["instagram" => "#"], "lat" => 21.1213, "lng" => -101.6826]
];

// Simulamos logos de patrocinadores (Imágenes Placeholder)
$sponsors = [
    "assets/img/sponsors/Celtic_Banco.png",
    "assets/img/sponsors/FlashPrint.png",
    "assets/img/sponsors/GoodFood.png",
    "assets/img/sponsors/HydroLife.png",
    "assets/img/sponsors/NexaFit.png",
    "assets/img/sponsors/ProTech.png",
    "assets/img/sponsors/RugbyMax.png",
    "assets/img/sponsors/SportLine.png",
    "assets/img/sponsors/Summit_Sports.png",
    "assets/img/sponsors/TierraVerde.png",
];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rugby México | Federación Mexicana de Rugby</title>
    
    <!-- Integración Tipografía Oficial: Open Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Integración Leaflet CSS (Propuesta para mapa interactivo seguro y gratuito) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    <style>
        :root {
            /* COLORES PRIMARIOS FMRU */
            --verde-principal: #116436; /* Abundant Green */
            --rojo-institucional: #bf2333; /* True Red */
            
            /* COLORES SECUNDARIOS FMRU */
            --verde-claro: #7fb069;     /* Grass Green */
            --verde-oscuro: #004323;    /* Formal Garden */
            --rojo-oscuro: #792128;     /* Dahlia */
            
            /* NEUTRALES */
            --negro-nav: #000000;       /* Process Black */
            --azul-oscuro: #111111;     /* Oscuro general para títulos */
            --gris-fondo: #f8f9fa;
        }

        body { 
            font-family: 'Open Sans', Arial, sans-serif; 
            background-color: var(--gris-fondo); 
        }

        .bg-verde-principal { background-color: var(--verde-principal) !important; color: white; }
        
        .btn-rojo { background-color: var(--rojo-institucional); color: white; font-weight: 600; border: none; transition: all 0.3s ease; }
        /* Efecto Hover usando Dahlia (Rojo oscuro FMRU) */
        .btn-rojo:hover { background-color: var(--rojo-oscuro); color: white; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(121, 33, 40, 0.3); }

        .navbar-custom { background-color: var(--verde-principal); }
        .navbar-custom .nav-link { color: white; text-transform: uppercase; font-weight: 600; font-size: 0.9rem;}
        .navbar-custom .nav-link:hover { color: var(--verde-claro); }
        
        .carousel-item { height: 65vh; min-height: 450px; background-color: var(--negro-nav); color: white; }
        
        /* Títulos en Extrabold (800) según manual */
        .section-title-main { font-weight: 800; text-transform: uppercase; border-bottom: 4px solid var(--verde-claro); padding-bottom: 5px; margin-bottom: 2rem; color: var(--verde-principal) !important; }

        /* Estilos Noticias (P2) */
        .section-title-mag { font-weight: 800; color: var(--rojo-institucional); text-transform: uppercase; border-bottom: 2px solid var(--verde-claro); padding-bottom: 10px; margin-bottom: 0;}
        .btn-view-all { background-color: var(--rojo-institucional); color: white; font-weight: 600; border-radius: 2px; transition: all 0.3s ease; }
        .btn-view-all:hover { background-color: var(--rojo-oscuro); color: white; }
        .text-categoria { color: var(--verde-claro); font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .text-fecha { color: #6c757d; font-size: 0.8rem; }
        .main-article { background: white; transition: 0.2s; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .main-article h3 { font-size: 1.8rem; color: var(--rojo-institucional); font-weight: 800; }
        .small-article img { height: 160px; object-fit: cover; border-radius: 4px; }
        .small-article h6 { font-size: 1.1rem; color: var(--rojo-institucional); font-weight: 800; line-height: 1.3; }
        .press-list .list-group-item { background: transparent; border-color: #e9ecef; padding: 15px 0; }
        .press-number { width: 28px; height: 28px; background-color: var(--rojo-institucional); color: white; font-weight: bold; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; }
        .press-list h6 { font-size: 0.95rem; font-weight: 600; color: var(--negro-nav); line-height: 1.4; margin-top: 4px; transition: all 0.2s;}
        .press-list h6:hover { color: var(--rojo-institucional); cursor: pointer; }

        /* Estilos Próximos Eventos (P3) */
        .events-container { background-color: #2b2b2b; border-radius: 8px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
        .events-nav-tabs .nav-link { color: #888; font-weight: 800; text-transform: uppercase; border: none; border-radius: 0; padding: 1rem; background-color: #1a1a1a; letter-spacing: 1px; transition: all 0.3s ease;}
        .events-nav-tabs .nav-link:hover { color: var(--rojo-institucional); background-color: #222; }
        .events-nav-tabs .nav-link.active { background-color: var(--rojo-institucional); color: white; border: none; }
        .event-row { border-bottom: 1px solid #404040; transition: background-color 0.2s; }
        .event-row:last-child { border-bottom: none; }
        .event-row:hover { background-color: #333; }
        .event-tournament { font-size: 0.8rem; color: #aaa; text-transform: uppercase; letter-spacing: 0.5px; }
        .event-teams { font-size: 1.25rem; font-weight: 800; color: white; }
        .event-details { font-size: 0.85rem; color: #bbb; }
        .btn-view-all-events { background-color: var(--rojo-institucional); color: white; border-radius: 0; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; transition: all 0.3s; }
        .btn-view-all-events:hover { background-color: var(--rojo-oscuro); color: white; }

        /* Estilos Directorio (P4 - Tabla) */
        .section-title { font-weight: 800; color: var(--azul-oscuro); display: inline-block; margin-bottom: 0; text-transform: uppercase; border-bottom: 4px solid var(--verde-claro); padding-bottom: 5px; }
        .directorio-table th { background-color: var(--verde-principal); color: white; font-weight: 800; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; border: none; }
        .directorio-table td { vertical-align: middle; font-size: 0.9rem; font-weight: 400;}
        .club-logo-placeholder { width: 45px; height: 45px; background-color: var(--gris-fondo); border: 2px solid var(--verde-claro); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--verde-principal); font-weight: 800; }

        /* Mapa */
        #map { height: 400px; width: 100%; border-radius: 8px; z-index: 1; border: 2px solid var(--verde-claro); }

        /* Contenedor principal de sponsors */
        .seccion-sponsors { background-color: #212529; padding: 60px 0 20px 0; text-align: center; overflow: hidden; }
        .titulo-sponsors { color: var(--verde-claro); font-size: 14px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 40px; font-weight: 800;}
        .sponsors-principales { display: flex; justify-content: center; align-items: center; gap: 60px; margin-bottom: 40px; }
        .sponsor-texto { color: #ffffff; font-size: 18px; font-weight: 600; }
        .divisor-sponsors { border: none; border-top: 1px solid rgba(255,255,255,0.1); width: 80%; margin: 0 auto 40px auto; }
        .marquee-contenedor { width: 100%; overflow: hidden; white-space: nowrap; position: relative; padding: 20px 0; }
        .marquee-contenido { display: inline-flex; align-items: center; gap: 40px; padding-right: 40px; animation: scrollSponsors 35s linear infinite; }
        .marquee-item { flex-shrink: 0; max-width: 140px; opacity: 0.7; filter: grayscale(100%); transition: all 0.3s ease; }
        .marquee-item:hover { filter: grayscale(0%); opacity: 1; }
        @keyframes scrollSponsors { from { transform: translateX(0); } to { transform: translateX(-50%); } }
        .contenedor-boton { margin-top: 50px; }
        .btn-quiero-sponsor { background-color: transparent; border: 1px solid rgba(255,255,255,0.3); color: #ffffff; padding: 12px 30px; text-decoration: none; font-size: 14px; font-weight: 600; border-radius: 6px; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 10px; }
        .btn-quiero-sponsor:hover { background-color: rgba(255,255,255,0.1); border-color: #ffffff; color: #ffffff; }

        /* P5: Efectos Footer */
        .custom-hover-link { transition: color 0.2s ease-in-out; }
        .custom-hover-link:hover { color: var(--verde-claro) !important; }
        .hover-white:hover { color: white !important; }
        .transition-scale { transition: transform 0.2s ease-in-out; display: inline-block; }
        .transition-scale:hover { transform: scale(1.15); color: var(--verde-claro) !important; }
    </style>
</head>
<body>

    <!-- BARRA SUPERIOR Y MENÚ -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top shadow-sm">
        <div class="container">
            <!-- Logo con márgenes seguros según manual de identidad (padding y object-fit) -->
            <a class="navbar-brand bg-white p-2 rounded" href="#">
                <img src="rugby-mexico-logo.png" alt="Logo FMRU" height="50" style="object-fit: contain;">
            </a>
            <button class="navbar-toggler border-white text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="bi bi-list fs-1"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item"><a class="nav-link" href="#">Inicio</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Federación</a>
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
                    <!-- Redirección directa a Sport Plus -->
                    <a href="URL_REGISTRO_SPORTPLUS" class="btn btn-rojo rounded-pill px-4"><i class="bi bi-person-circle me-1"></i> Regístrate / Ingresar</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- P1: BANNER ROTATIVO (HERO) -->
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="/Lupware/FMRU/assets/img/banner/BloodFest26-1589.jpg" class="d-block w-100 object-fit-cover" alt="Banner 1" style="height: 65vh; min-height: 450px;">
                <div class="carousel-caption d-flex flex-column h-100 align-items-center justify-content-center" style="background: rgba(0,0,0,0.5); top: 0; bottom: 0; left: 0; right: 0;">
                    <h1 class="display-3 text-uppercase text-white" style="font-weight: 800 !important;">La Fuerza de las Serpientes</h1>
                    <p class="lead fw-normal text-white">Apoya a nuestras selecciones en su camino internacional.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="/Lupware/FMRU/assets/img/banner/Noticias_NC_FMRU.jpg" class="d-block w-100 object-fit-cover" alt="Banner 2" style="height: 65vh; min-height: 450px;">
                <div class="carousel-caption d-flex flex-column h-100 align-items-center justify-content-center" style="background: rgba(0,0,0,0.5); top: 0; bottom: 0; left: 0; right: 0;">
                    <h1 class="display-3 text-uppercase text-white" style="font-weight: 800 !important;">Únete a la Familia</h1>
                    <p class="lead fw-normal text-white">Encuentra un club cerca de ti y comienza a jugar.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="/Lupware/FMRU/assets/img/banner/Mesa-de-trabajo-25.png" class="d-block w-100 object-fit-cover" alt="Banner 3" style="height: 65vh; min-height: 450px;">
                <div class="carousel-caption d-flex flex-column h-100 align-items-center justify-content-center" style="background: rgba(0,0,0,0.5); top: 0; bottom: 0; left: 0; right: 0;">
                    <h1 class="display-3 text-uppercase text-white" style="font-weight: 800 !important;">Desarrollo y Competencia</h1>
                    <p class="lead fw-normal text-white">Conoce nuestros programas infantiles y juveniles.</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>

    <!-- CAMBIO DE ORDEN: P3 (CALENDARIO) AHORA VA PRIMERO -->
    <section class="container my-5">
        <h2 class="section-title-main">Calendario FMRU</h2>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="events-container">
                    <ul class="nav nav-fill events-nav-tabs" id="eventTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active w-100" id="programacion-tab" data-bs-toggle="tab" data-bs-target="#programacion" type="button" role="tab">
                                <i class="bi bi-calendar-event me-2"></i> Programación
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link w-100" id="resultados-tab" data-bs-toggle="tab" data-bs-target="#resultados" type="button" role="tab">
                                <i class="bi bi-trophy me-2"></i> Resultados
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="eventTabContent">
                        <div class="tab-pane fade show active" id="programacion" role="tabpanel">
                            <?php foreach($proximos_eventos as $evento): ?>
                            <div class="event-row p-4 text-center">
                                <div class="event-tournament mb-2"><?= e($evento['torneo']) ?></div>
                                <div class="event-teams mb-2">
                                    <span><?= e($evento['equipo_local']) ?></span>
                                    <span class="mx-3 text-muted">—</span>
                                    <span><?= e($evento['equipo_visitante']) ?></span>
                                </div>
                                <div class="event-details">
                                    <span><?= e($evento['fecha']) ?> <?= e($evento['hora']) ?></span>
                                    <span class="mx-2 text-muted">|</span>
                                    <span><i class="bi bi-geo-alt-fill text-secondary"></i> <?= e($evento['sede']) ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <a href="https://sportsplus.app/public/6859/calendar" target="_blank" class="btn btn-view-all-events d-block py-3 w-100 text-decoration-none">
                                Ver Calendario Completo en SportPlus <i class="bi bi-box-arrow-up-right float-end mt-1"></i>
                            </a>
                        </div>
                        
                        <div class="tab-pane fade p-5 text-center" id="resultados" role="tabpanel">
                            <i class="bi bi-clipboard-data display-3 text-secondary mb-3"></i>
                            <h5 class="text-white">Aún no hay resultados publicados</h5>
                            <p class="text-muted">Los marcadores se actualizarán aquí tras finalizar los encuentros.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CAMBIO DE ORDEN: P2 (NOTICIAS) AHORA VA DESPUÉS -->
    <section class="container my-5">
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2" style="border-color: var(--verde-claro) !important;">
                    <h2 class="section-title-mag border-0 pb-0 mb-0">Últimas Noticias</h2>
                    <a href="#" class="btn btn-view-all btn-sm px-4 py-2 text-decoration-none">Ver Todo</a>
                </div>

                <div class="card main-article mb-4">
                    <div class="row g-0 flex-column-reverse flex-md-row">
                        <div class="col-md-5 p-4 d-flex flex-column justify-content-center">
                            <span class="text-categoria mb-2">RWC 2027</span>
                            <h3 class="mb-3">Nuevo Colaborador Oficial de la Copa Mundial</h3>
                            <p class="text-muted small mb-4">World Rugby ha anunciado hoy el principal grupo minorista que será el Supermercado Oficial Colaborador de la Rugby World Cup...</p>
                            <span class="text-fecha mt-auto">19 octubre, 2026 · Lectura: 3'</span>
                        </div>
                        <div class="col-md-7">
                            <img src="assets/img/noticias/rugby.png" class="img-fluid h-100 object-fit-cover" alt="Noticia Principal">
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-2">
                    <div class="col-md-4 small-article">
                        <img src="assets/img/noticias/Jugada Rugby.png" class="img-fluid w-100 mb-3" alt="SVNS">
                        <span class="text-categoria d-block mb-1">SVNS</span>
                        <h6>Fechas y sedes confirmados del HSBC SVNS 2 para 2027</h6>
                        <span class="text-fecha mt-2 d-block">13 octubre, 2026</span>
                    </div>
                    <div class="col-md-4 small-article">
                        <img src="assets/img/noticias/equipo rugby.png" class="img-fluid w-100 mb-3" alt="Bienestar">
                        <span class="text-categoria d-block mb-1">Bienestar de los Jugadores</span>
                        <h6>World Rugby clarifica la Ley sobre tackles peligrosos</h6>
                        <span class="text-fecha mt-2 d-block">7 octubre, 2026</span>
                    </div>
                    <div class="col-md-4 small-article">
                        <img src="assets/img/noticias/Balón rugby.png" class="img-fluid w-100 mb-3" alt="Gobernanza">
                        <span class="text-categoria d-block mb-1">Gobernanza</span>
                        <h6>Executive Board election process confirmed</h6>
                        <span class="text-fecha mt-2 d-block">2 octubre, 2026</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <h2 class="section-title-mag mb-4">Gacetillas de Prensa</h2>
                <ul class="list-group list-group-flush press-list">
                    <li class="list-group-item d-flex align-items-start">
                        <div class="press-number me-3 flex-shrink-0">1</div>
                        <div class="w-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-categoria">RWC 2027</span>
                                <span class="text-fecha">19 oct, 2026</span>
                            </div>
                            <h6>Nuevo Colaborador Oficial de la Copa Mundial de Rugby...</h6>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-start border-bottom-0">
                        <div class="press-number me-3 flex-shrink-0">2</div>
                        <div class="w-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-categoria">SVNS</span>
                                <span class="text-fecha">13 oct, 2026</span>
                            </div>
                            <h6>Fechas y sedes confirmados del HSBC SVNS 2...</h6>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- P4: DIRECTORIO DE CLUBES (TABLA + MAPA + PAGINACIÓN COMPLETA) -->
    <section class="container my-5" id="directorio-section">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 border-bottom pb-3" style="border-color: var(--verde-claro) !important;">
            <div class="mb-3 mb-lg-0">
                <h2 class="section-title border-0 pb-0 mb-1">ENCUENTRA TU CLUB</h2>
                <p class="text-muted mb-0">Directorio oficial de equipos afiliados a la FMRU.</p>
            </div>
            
            <div class="d-flex flex-column flex-sm-row gap-2">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Buscar equipo..." aria-label="Buscar equipo">
                </div>
                <select id="stateFilter" class="form-select">
                    <option value="all" selected>Todos los Estados</option>
                    <option value="Ciudad de México">Ciudad de México</option>
                    <option value="Estado de México">Estado de México</option>
                    <option value="Guanajuato">Guanajuato</option>
                    <option value="Jalisco">Jalisco</option>
                    <option value="Nuevo León">Nuevo León</option>
                    <option value="Quintana Roo">Quintana Roo</option>
                    <option value="Yucatán">Yucatán</option>
                </select>
            </div>
        </div>

        <div class="row g-4">
            <!-- Columna del Mapa (Leaflet) -->
            <div class="col-lg-12 mb-2">
                <div id="map" class="shadow-sm"></div>
            </div>

            <div class="col-lg-12">
                <div class="table-responsive shadow-sm border rounded bg-white">
                    <table class="table table-hover align-middle mb-0 directorio-table" id="clubsTable">
                        <thead>
                            <tr>
                                <th scope="col" class="py-3 px-4">Equipo</th>
                                <th scope="col" class="py-3">Categoría</th>
                                <th scope="col" class="py-3">Estado</th>
                                <th scope="col" class="py-3">Sede y Entrenamientos</th>
                                <th scope="col" class="py-3 text-center">Contacto</th>
                            </tr>
                        </thead>
                        <tbody id="clubsTableBody">
                            <?php foreach($clubes_muestra as $index => $club): ?>
                            <!-- Usamos d-none para la paginación inicial (mostrar 4) -->
                            <tr class="club-row bg-white border-bottom <?= $index >= 4 ? 'd-none' : '' ?>" 
                                data-estado="<?= e(strtolower($club['estado'])) ?>" 
                                data-nombre="<?= e(strtolower($club['nombre'])) ?>"
                                data-lat="<?= e($club['lat']) ?>"
                                data-lng="<?= e($club['lng']) ?>">
                                <td class="px-4 py-3 border-0">
                                    <div class="d-flex align-items-center">
                                        <div class="club-logo-placeholder me-3 bg-white">
                                            <?= substr(e($club['nombre']), 0, 1) ?>
                                        </div>
                                        <span class="fw-bold text-dark club-name-text"><?= e($club['nombre']) ?></span>
                                    </div>
                                </td>
                                <td class="border-0"><span class="badge bg-secondary rounded-pill px-3 py-2 fw-normal text-light"><?= e($club['categoria']) ?></span></td>
                                <td class="border-0"><span class="fw-semibold text-dark"><?= e($club['estado']) ?></span></td>
                                <td class="border-0">
                                    <small class="text-muted">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                        <?= e($club['entrenamiento']) ?>
                                    </small>
                                </td>
                                <td class="text-center border-0">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="mailto:<?= e($club['contacto']) ?>" class="text-secondary text-decoration-none" title="<?= e($club['contacto']) ?>"><i class="bi bi-envelope-fill fs-5"></i></a>
                                        <?php if(isset($club['redes']['facebook'])): ?>
                                            <a href="<?= e($club['redes']['facebook']) ?>" target="_blank" rel="noopener noreferrer" class="text-primary text-decoration-none"><i class="bi bi-facebook fs-5"></i></a>
                                        <?php endif; ?>
                                        <?php if(isset($club['redes']['instagram'])): ?>
                                            <a href="<?= e($club['redes']['instagram']) ?>" target="_blank" rel="noopener noreferrer" class="text-danger text-decoration-none"><i class="bi bi-instagram fs-5"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div id="noResultsMsg" class="text-center py-5 d-none bg-white">
                        <i class="bi bi-search text-muted display-4"></i>
                        <p class="text-muted mt-3 fw-bold">No se encontraron equipos con esos criterios.</p>
                        <button class="btn btn-outline-secondary btn-sm mt-2" onclick="window.resetFilters()">Limpiar filtros</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4" id="loadMoreContainer">
            <button id="btnLoadMore" class="btn btn-outline-dark px-4 py-2 rounded-pill shadow-sm bg-white text-dark fw-semibold" style="border-color: #dee2e6;">
                Cargar más equipos <i class="bi bi-arrow-down-short"></i>
            </button>
        </div>
    </section>

    <!-- SECCIÓN: PATROCINADORES -->
    <section class="seccion-sponsors">
        <h3 class="titulo-sponsors">NUESTROS SPONSORS</h3>
        <div class="sponsors-principales">
            <span class="sponsor-texto">Kingspan</span>
            <span class="sponsor-texto">Antel</span>
        </div>
        <hr class="divisor-sponsors">
        <div class="marquee-contenedor">
            <div class="marquee-contenido">
                <?php foreach($sponsors as $index => $sponsor_img): ?>
                    <img src="<?= e($sponsor_img) ?>" alt="Sponsor <?= $index + 1 ?>" class="img-fluid marquee-item">
                <?php endforeach; ?>
                <?php foreach($sponsors as $index => $sponsor_img): ?>
                    <img src="<?= e($sponsor_img) ?>" alt="Sponsor Clon <?= $index + 1 ?>" class="img-fluid marquee-item">
                <?php endforeach; ?>
            </div>
        </div>
        <div class="contenedor-boton">
            <a href="#" class="btn-quiero-sponsor">QUIERO SER SPONSOR <i class="bi bi-arrow-right"></i></a>
        </div>
    </section>

    <!-- P5: FOOTER -->
    <footer class="bg-dark text-white pt-5 pb-3 border-top" style="border-color: var(--verde-claro) !important; border-width: 5px !important;">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-md-6 mb-4">
                    <img src="rugby-mexico-logo.png" alt="Logo Rugby México" height="60" class="mb-3 bg-white p-2 rounded" style="object-fit: contain;">
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
                <div class="col-md-6 text-center text-md-end small text-muted mt-2 mt-md-0">
                    <a href="#" class="text-muted text-decoration-none me-3 hover-white">Políticas de Privacidad</a>
                    <a href="#" class="text-muted text-decoration-none hover-white">Términos Legales</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Integración Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('currentYear').textContent = new Date().getFullYear();
            
            // --- INICIALIZAR MAPA ---
            var map = L.map('map').setView([23.6345, -102.5528], 5);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            var markersLayer = new L.LayerGroup().addTo(map);
            const tableRows = document.querySelectorAll('.club-row');
            
            // Ícono de Rugby (rojo)
            var rugbyIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            // Función combinada: Actualiza Mapa Y Paginación UI
            function updateMapAndUI(matchCount) {
                markersLayer.clearLayers();
                let bounds = [];

                tableRows.forEach(row => {
                    if (!row.classList.contains('d-none')) {
                        let lat = parseFloat(row.getAttribute('data-lat'));
                        let lng = parseFloat(row.getAttribute('data-lng'));
                        let nombre = row.getAttribute('data-nombre');

                        if(!isNaN(lat) && !isNaN(lng)) {
                            let marker = L.marker([lat, lng], {icon: rugbyIcon}).bindPopup(`<b>${nombre.toUpperCase()}</b>`);
                            markersLayer.addLayer(marker);
                            bounds.push([lat, lng]);
                        }
                    }
                });

                if (bounds.length > 0) {
                    map.fitBounds(bounds, {padding: [50, 50], maxZoom: 10});
                } else {
                    map.setView([23.6345, -102.5528], 5);
                }

                const searchInput = document.getElementById('searchInput');
                const stateFilter = document.getElementById('stateFilter');
                
                if (matchCount === 0) {
                    noResultsMsg.classList.remove('d-none');
                    loadMoreContainer.classList.add('d-none');
                } else {
                    noResultsMsg.classList.add('d-none');
                    
                    if (searchInput.value.trim() !== '' || stateFilter.value !== 'all') {
                        loadMoreContainer.classList.add('d-none');
                    } else {
                        loadMoreContainer.classList.remove('d-none');
                        
                        if (visibleLimit >= matchCount || isShowingAll) {
                            btnLoadMore.innerHTML = 'Mostrar menos <i class="bi bi-arrow-up-short"></i>';
                            isShowingAll = true;
                        } else {
                            btnLoadMore.innerHTML = 'Cargar más equipos <i class="bi bi-arrow-down-short"></i>';
                            isShowingAll = false;
                        }
                    }
                }
            }

            // --- LÓGICA DE FILTRADO Y PAGINACIÓN ---
            const searchInput = document.getElementById('searchInput');
            const stateFilter = document.getElementById('stateFilter');
            const noResultsMsg = document.getElementById('noResultsMsg');
            const btnLoadMore = document.getElementById('btnLoadMore');
            const loadMoreContainer = document.getElementById('loadMoreContainer');
            
            const inicialLimit = 4;
            const increment = 4;
            let visibleLimit = inicialLimit;
            let isShowingAll = false;

            window.resetFilters = function() {
                searchInput.value = '';
                stateFilter.value = 'all';
                visibleLimit = inicialLimit;
                isShowingAll = false;
                filterTable();
            };

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                const selectedState = stateFilter.value.toLowerCase();
                let matchCount = 0;
                let visibleCount = 0;

                tableRows.forEach(row => {
                    const rowName = row.getAttribute('data-nombre');
                    const rowState = row.getAttribute('data-estado');
                    
                    const matchesSearch = searchTerm === '' || rowName.includes(searchTerm) || rowState.includes(searchTerm);
                    const matchesState = selectedState === 'all' || rowState === selectedState;

                    if (matchesSearch && matchesState) {
                        matchCount++;
                        
                        if (searchTerm !== '' || selectedState !== 'all' || isShowingAll) {
                             row.classList.remove('d-none');
                        } else {
                            if (visibleCount < visibleLimit) {
                                row.classList.remove('d-none');
                                visibleCount++;
                            } else {
                                row.classList.add('d-none');
                            }
                        }
                    } else {
                        row.classList.add('d-none');
                    }
                });
                
                updateMapAndUI(matchCount);
            }

            filterTable();

            if(searchInput && stateFilter) {
                searchInput.addEventListener('input', filterTable);
                stateFilter.addEventListener('change', filterTable);
            }

            if(btnLoadMore) {
                btnLoadMore.addEventListener('click', function() {
                    if (isShowingAll) {
                        visibleLimit = inicialLimit;
                        isShowingAll = false;
                        document.getElementById('directorio-section').scrollIntoView({ behavior: 'smooth' });
                    } else {
                        visibleLimit += increment;
                    }
                    filterTable();
                });
            }
        });
    </script>
</body>
</html>