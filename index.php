<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - HOME PAGE
 * Arquitectura Segura - OWASP Compliance
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); 
ini_set('session.use_only_cookies', 1);
session_start();

require_once __DIR__ . '/admin/config/db.php';

/*try {
    // La consulta las trae ordenadas por fecha. Luego PHP las separará.
    $stmtNoticias = $pdo->query("SELECT * FROM fmru_noticias WHERE activo = 1 AND tipo != 'comunicado' ORDER BY fecha_publicacion DESC, id DESC LIMIT 4");
    $todas_noticias = $stmtNoticias->fetchAll(PDO::FETCH_ASSOC);

    // Separamos: La posición [0] es la más nueva (Principal), las otras 3 son secundarias
    $noticia_principal = !empty($todas_noticias) ? array_shift($todas_noticias) : null;
    $noticias_secundarias = $todas_noticias;

    // 3. Comunicados de Prensa (Los 4 más recientes)
    $stmtComunicados = $pdo->query("SELECT * FROM fmru_noticias WHERE activo = 1 AND tipo = 'comunicado' ORDER BY fecha_publicacion DESC, id DESC LIMIT 4");
    $comunicados = $stmtComunicados->fetchAll(PDO::FETCH_ASSOC);

    // Obtener Banners Activos
    $stmtBanners = $pdo->query("SELECT * FROM fmru_banners WHERE activo = 1 ORDER BY orden ASC");
    $banners_activos = $stmtBanners->fetchAll(PDO::FETCH_ASSOC);

    // Obtener Sponsors Activos
    $stmtSponsors = $pdo->query("SELECT * FROM fmru_sponsors WHERE activo = 1 ORDER BY orden ASC");
    $todos_sponsors = $stmtSponsors->fetchAll(PDO::FETCH_ASSOC);
    
    $sponsors_principales = [];
    $sponsors_tecnicos = [];
    $sponsors_proveedores = [];
    
    foreach ($todos_sponsors as $sp) {
        if ($sp['nivel'] === 'Patrocinador Principal') {
            $sponsors_principales[] = $sp;
        } elseif ($sp['nivel'] === 'Socio Técnico') {
            $sponsors_tecnicos[] = $sp;
        } else {
            $sponsors_proveedores[] = $sp;
        }
    }

} catch (PDOException $e) {
    die("Error de conexión"); // En producción, registra el error y muestra un mensaje amigable
}*/
try {
    // Obtenemos los datos con una sola línea por bloque
    $todas_noticias = $db->getNoticiasRecientes(4);
    $comunicados    = $db->getComunicadosRecientes(4);
    $banners_activos = $db->getBannersActivos();
    $todos_sponsors  = $db->getSponsorsActivos();

    // Separamos la noticia principal de las secundarias
    $noticia_principal = !empty($todas_noticias) ? array_shift($todas_noticias) : null;
    $noticias_secundarias = $todas_noticias;
    
    // Clasificamos los sponsors
    $sponsors_principales = [];
    $sponsors_tecnicos = [];
    $sponsors_proveedores = [];
    
    foreach ($todos_sponsors as $sp) {
        if ($sp['nivel'] === 'Patrocinador Principal') {
            $sponsors_principales[] = $sp;
        } elseif ($sp['nivel'] === 'Socio Técnico') {
            $sponsors_tecnicos[] = $sp;
        } else {
            $sponsors_proveedores[] = $sp;
        }
    }

} catch (Exception $e) {
    error_log("Error cargando el Home: " . $e->getMessage());
    die("Error interno cargando la plataforma."); 
}
 
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// Funciones Auxiliares para Fechas en Español
function fechaEsp($fecha) {
    $meses = ['01'=>'enero', '02'=>'febrero', '03'=>'marzo', '04'=>'abril', '05'=>'mayo', '06'=>'junio', '07'=>'julio', '08'=>'agosto', '09'=>'septiembre', '10'=>'octubre', '11'=>'noviembre', '12'=>'diciembre'];
    $timestamp = strtotime($fecha);
    return ltrim(date('d', $timestamp), '0') . " " . $meses[date('m', $timestamp)] . ", " . date('Y', $timestamp);
}

function fechaEspCorta($fecha) {
    $meses = ['01'=>'oct', '02'=>'feb', '03'=>'mar', '04'=>'abr', '05'=>'may', '06'=>'jun', '07'=>'jul', '08'=>'ago', '09'=>'sep', '10'=>'oct', '11'=>'nov', '12'=>'dic'];
    $timestamp = strtotime($fecha);
    return ltrim(date('d', $timestamp), '0') . " " . $meses[date('m', $timestamp)] . ", " . date('y', $timestamp);
}

// Datos Simulados
$proximos_eventos = [
    [
        "torneo" => "Torneo Nacional Mayor - Jornada 3", 
        "equipo_local" => "Black Thunder RC", 
        "escudo_local" => "assets/img/clubes/black_thunder.png",
        "equipo_visitante" => "Pumas UNAM", 
        "escudo_visitante" => "assets/img/clubes/pumas.png",
        "fecha" => "24 Octubre, 2026", 
        "hora" => "11:30 hrs", 
        "sede" => "Deportivo Xochimilco"
    ],
    [
        "torneo" => "Liga de Occidente Femenil - Semifinal", 
        "equipo_local" => "Lobos Occidente", 
        "escudo_local" => "assets/img/clubes/lobos.png",
        "equipo_visitante" => "Rhinos RC", 
        "escudo_visitante" => "assets/img/clubes/rhinos.png",
        "fecha" => "24 Octubre, 2026", 
        "hora" => "14:00 hrs", 
        "sede" => "Parque Metropolitano, GDL"
    ]
];

// Directorio Completo de Clubes (Basado en el listado de FMRU)
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
    ["nombre" => "Espartanos", "categoria" => "Mayor Femenil", "estado" => "Guanajuato", "entrenamiento" => "Mar y Jue: 20:30 hrs.", "contacto" => "espartanas@gmail.com", "redes" => ["instagram" => "#"], "lat" => 21.1213, "lng" => -101.6826],
    // -- Nuevos equipos integrados --
    ["nombre" => "Wallabies RC", "categoria" => "Mayor Varonil", "estado" => "Estado de México", "entrenamiento" => "Mar y Jue: 20:00 hrs.", "contacto" => "wallabies@mx.com", "redes" => ["facebook" => "#"], "lat" => 19.5303, "lng" => -99.2200],
    ["nombre" => "Templarios RC", "categoria" => "Mayor Varonil / Femenil", "estado" => "Ciudad de México", "entrenamiento" => "Lun y Mié: 19:30 hrs.", "contacto" => "templarios@rugby.com", "redes" => ["facebook" => "#", "instagram" => "#"], "lat" => 19.4326, "lng" => -99.1332],
    ["nombre" => "Cumiyais Monterrey", "categoria" => "Mayor Varonil", "estado" => "Nuevo León", "entrenamiento" => "Sáb: 10:00 hrs.", "contacto" => "cumiyais@mty.com", "redes" => ["facebook" => "#"], "lat" => 25.6866, "lng" => -100.3161],
    ["nombre" => "Roosters Querétaro", "categoria" => "Infantil / Juvenil", "estado" => "Querétaro", "entrenamiento" => "Mar y Jue: 18:00 hrs.", "contacto" => "roosters@qro.com", "redes" => ["instagram" => "#"], "lat" => 20.5881, "lng" => -100.3899],
    ["nombre" => "Celaya RC", "categoria" => "Mayor Varonil", "estado" => "Guanajuato", "entrenamiento" => "Mié y Vie: 19:00 hrs.", "contacto" => "celayarc@gmail.com", "redes" => ["facebook" => "#"], "lat" => 20.5223, "lng" => -100.8122],
    ["nombre" => "Bisontes Guadalajara", "categoria" => "Mayor Varonil / Femenil", "estado" => "Jalisco", "entrenamiento" => "Lun y Jue: 20:30 hrs.", "contacto" => "bisontes@gdl.com", "redes" => ["facebook" => "#"], "lat" => 20.6597, "lng" => -103.3496],
    ["nombre" => "Hammerheads Cancún", "categoria" => "Mayor Varonil", "estado" => "Quintana Roo", "entrenamiento" => "Mar y Jue: 19:00 hrs.", "contacto" => "hammerheads@cancun.com", "redes" => ["facebook" => "#", "instagram" => "#"], "lat" => 21.1610, "lng" => -86.8250],
    ["nombre" => "Jaguares Colima", "categoria" => "Universitario Varonil", "estado" => "Colima", "entrenamiento" => "Lun y Mié: 17:00 hrs.", "contacto" => "jaguares@colima.com", "redes" => ["instagram" => "#"], "lat" => 19.2433, "lng" => -103.7249],
    ["nombre" => "Legión Cuervos", "categoria" => "Mayor Varonil / Femenil", "estado" => "Ciudad de México", "entrenamiento" => "Mar y Jue: 21:00 hrs.", "contacto" => "cuervos@cdmx.com", "redes" => ["facebook" => "#"], "lat" => 19.3400, "lng" => -99.1500],
    ["nombre" => "Tazmania RC", "categoria" => "Infantil / Juvenil", "estado" => "Ciudad de México", "entrenamiento" => "Sáb: 08:30 hrs.", "contacto" => "tazmania@rc.com", "redes" => ["facebook" => "#"], "lat" => 19.3900, "lng" => -99.1600],
    ["nombre" => "Quetzales RC", "categoria" => "Mayor Varonil", "estado" => "Puebla", "entrenamiento" => "Lun y Mié: 20:00 hrs.", "contacto" => "quetzales@puebla.com", "redes" => ["instagram" => "#"], "lat" => 19.0414, "lng" => -98.2063],
    ["nombre" => "Borregos Puebla", "categoria" => "Universitario Varonil", "estado" => "Puebla", "entrenamiento" => "Mar y Jue: 16:00 hrs.", "contacto" => "borregos@puebla.mx", "redes" => ["facebook" => "#"], "lat" => 19.0196, "lng" => -98.2425],
    ["nombre" => "Abejas Guanajuato", "categoria" => "Universitario Femenil", "estado" => "Guanajuato", "entrenamiento" => "Lun y Jue: 18:00 hrs.", "contacto" => "abejas@ugto.mx", "redes" => ["instagram" => "#"], "lat" => 21.0190, "lng" => -101.2574],
    ["nombre" => "Saltillo RC", "categoria" => "Mayor Varonil", "estado" => "Coahuila", "entrenamiento" => "Mar y Jue: 20:30 hrs.", "contacto" => "saltillo@rc.com", "redes" => ["facebook" => "#"], "lat" => 25.4216, "lng" => -100.9990],
    ["nombre" => "Cimarrones UABC", "categoria" => "Universitario Varonil", "estado" => "Baja California", "entrenamiento" => "Lun y Mié: 17:30 hrs.", "contacto" => "cimarrones@uabc.mx", "redes" => ["facebook" => "#", "instagram" => "#"], "lat" => 32.5149, "lng" => -117.0382],
    ["nombre" => "ITESO Rugby", "categoria" => "Universitario Varonil / Femenil", "estado" => "Jalisco", "entrenamiento" => "Mar y Jue: 19:00 hrs.", "contacto" => "rugby@iteso.mx", "redes" => ["instagram" => "#"], "lat" => 20.6083, "lng" => -103.4146],
    ["nombre" => "Coyotes Coyoacán", "categoria" => "Mayor Varonil", "estado" => "Ciudad de México", "entrenamiento" => "Lun y Mié: 20:00 hrs.", "contacto" => "coyotes@cdmx.com", "redes" => ["facebook" => "#"], "lat" => 19.3500, "lng" => -99.1620],
    ["nombre" => "Ocelotes Aguascalientes", "categoria" => "Mayor Varonil", "estado" => "Aguascalientes", "entrenamiento" => "Mar y Jue: 19:30 hrs.", "contacto" => "ocelotes@ags.com", "redes" => ["facebook" => "#", "instagram" => "#"], "lat" => 21.8853, "lng" => -102.2916]
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

// Define el título de la página
$page_title = "Inicio"; 

include 'includes/head.php';
include 'includes/navbar.php'; 
?>

    <!-- P1 - BANNER ROTATIVO (DINÁMICO) -->
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        
        <!-- Indicadores Dinámicos -->
        <div class="carousel-indicators">
            <?php foreach ($banners_activos as $index => $banner): ?>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>" aria-label="Slide <?= $index + 1 ?>"></button>
            <?php endforeach; ?>
        </div>
        
        <!-- Imágenes Dinámicas -->
        <div class="carousel-inner">
            <?php foreach ($banners_activos as $index => $banner): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>" data-bs-interval="4000">
                    <img src="<?= e($banner['imagen_url']) ?>" class="d-block w-100 object-fit-cover" alt="<?= e($banner['titulo']) ?>" style="height: 65vh; min-height: 450px;">
                    <div class="carousel-caption d-flex flex-column h-100 align-items-center justify-content-center" style="background: rgba(0,0,0,0.5); top: 0; bottom: 0; left: 0; right: 0;">
                        <h1 class="display-3 text-uppercase text-white" style="font-weight: 800 !important;"><?= e($banner['titulo']) ?></h1>
                        <?php if (!empty($banner['subtitulo'])): ?>
                            <p class="lead fw-normal text-white"><?= e($banner['subtitulo']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
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

    <!-- P2 - PROXIMOS EVENTOS -->
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
                        <!-- Tab: Programación -->
                        <div class="tab-pane fade show active" id="programacion" role="tabpanel">
                            <?php foreach($proximos_eventos as $evento): ?>
                            <div class="event-row p-4 text-center">
                                <!-- Corrección: Eliminado text-muted, agregado text-white-50 -->
                                <div class="event-tournament mb-3 text-white-50 small fw-bold text-uppercase"><?= e($evento['torneo']) ?></div>
                                
                                <!-- Contenedor de Equipos y Escudos -->
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <!-- Local -->
                                    <div class="text-end d-flex align-items-center justify-content-end" style="width: 45%;">
                                        <h5 class="text-white fw-bold mb-0 me-3 d-none d-md-block"><?= e($evento['equipo_local']) ?></h5>
                                        <img src="<?= e($evento['escudo_local']) ?>" alt="Local" class="equipo-escudo" onerror="this.outerHTML='<div class=\'equipo-escudo-fallback\'><?= substr(e($evento['equipo_local']),0,1) ?></div>'">
                                    </div>
                                    
                                    <!-- VS -->
                                    <!-- Corrección: Eliminado text-secondary, agregado text-white-50 -->
                                    <div class="px-3 text-white-50 fw-bold small">VS</div>
                                    
                                    <!-- Visitante -->
                                    <div class="text-start d-flex align-items-center justify-content-start" style="width: 45%;">
                                        <img src="<?= e($evento['escudo_visitante']) ?>" alt="Visitante" class="equipo-escudo" onerror="this.outerHTML='<div class=\'equipo-escudo-fallback\'><?= substr(e($evento['equipo_visitante']),0,1) ?></div>'">
                                        <h5 class="text-white fw-bold mb-0 ms-3 d-none d-md-block"><?= e($evento['equipo_visitante']) ?></h5>
                                    </div>
                                </div>

                                <!-- Corrección: Eliminado text-muted, agregado text-white-50 -->
                                <div class="event-details text-white-50 small">
                                    <span><i class="bi bi-clock me-1"></i> <?= e($evento['fecha']) ?> <?= e($evento['hora']) ?></span>
                                    <span class="mx-2 opacity-50">|</span>
                                    <span><i class="bi bi-geo-alt-fill me-1 text-danger"></i> <?= e($evento['sede']) ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <!-- Botón redirigiendo a la página completa de jornadas -->
                            <a href="jornadas.php" class="btn btn-view-all-events d-block py-3 w-100 text-decoration-none">
                                VER TODAS LAS JORNADAS <i class="bi bi-arrow-right-circle ms-2 mt-1"></i>
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

    <!-- P3 - NOTICIAS -->
    <section class="container my-5">
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2" style="border-color: var(--verde-claro) !important;">
                    <h2 class="section-title-mag border-0 pb-0 mb-0">Últimas Noticias</h2>
                    <a href="noticias.php" class="btn btn-view-all btn-sm px-4 py-2 text-decoration-none">Ver Todo</a>
                </div>

                <!-- NOTICIA PRINCIPAL -->
                <?php if ($noticia_principal): ?>
                <div class="card main-article mb-4">
                    <div class="row g-0 flex-column-reverse flex-md-row">
                        <div class="col-md-5 p-4 d-flex flex-column justify-content-center">
                            <span class="text-categoria mb-2"><?= e($noticia_principal['categoria']) ?></span>
                            <h3 class="mb-3">
                                <a href="articulo.php?id=<?= $noticia_principal['id'] ?>" class="text-decoration-none" style="color: var(--rojo-institucional);">
                                    <?= e($noticia_principal['titulo']) ?>
                                </a>
                            </h3>
                            <p class="text-muted small mb-4"><?= e($noticia_principal['extracto']) ?></p>
                            <span class="text-fecha mt-auto">
                                <?= fechaEsp($noticia_principal['fecha_publicacion']) ?> 
                                <?= !empty($noticia_principal['tiempo_lectura']) ? " · Lectura: " . e($noticia_principal['tiempo_lectura']) : "" ?>
                            </span>
                        </div>
                        <div class="col-md-7 overflow-hidden">
                            <a href="articulo.php?id=<?= $noticia_principal['id'] ?>">
                                <img src="<?= e($noticia_principal['imagen_url']) ?>" class="img-fluid h-100 object-fit-cover w-100 transition-scale" alt="<?= e($noticia_principal['titulo']) ?>" style="min-height: 250px;">
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- NOTICIAS SECUNDARIAS -->
                <?php if (!empty($noticias_secundarias)): ?>
                <div class="row g-4 mt-2">
                    <?php foreach ($noticias_secundarias as $secundaria): ?>
                    <div class="col-md-4 small-article">
                        <div class="overflow-hidden rounded mb-3">
                            <a href="articulo.php?id=<?= $secundaria['id'] ?>">
                                <img src="<?= e($secundaria['imagen_url']) ?>" class="img-fluid w-100 transition-scale" alt="<?= e($secundaria['titulo']) ?>">
                            </a>
                        </div>
                        <span class="text-categoria d-block mb-1"><?= e($secundaria['categoria']) ?></span>
                        <h6>
                            <a href="articulo.php?id=<?= $secundaria['id'] ?>" class="text-decoration-none" style="color: var(--rojo-institucional);">
                                <?= e($secundaria['titulo']) ?>
                            </a>
                        </h6>
                        <span class="text-fecha mt-2 d-block"><?= fechaEsp($secundaria['fecha_publicacion']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- NOTAS DE PRENSA -->
            <div class="col-lg-4">
                <h2 class="section-title-mag mb-4">Notas de Prensa</h2>
                
                <?php if (!empty($comunicados)): ?>
                <ul class="list-group list-group-flush press-list">
                    <?php foreach ($comunicados as $index => $comunicado): ?>
                    <li class="list-group-item d-flex align-items-start <?= ($index === count($comunicados) - 1) ? 'border-bottom-0' : '' ?>">
                        <div class="press-number me-3 flex-shrink-0"><?= $index + 1 ?></div>
                        <div class="w-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-categoria"><?= e($comunicado['categoria']) ?></span>
                                <span class="text-fecha fw-bold"><?= fechaEspCorta($comunicado['fecha_publicacion']) ?></span>
                            </div>
                            <h6 class="mb-0">
                                <a href="comunicado.php?id=<?= $comunicado['id'] ?>" class="text-decoration-none text-dark custom-hover-link">
                                    <?= e($comunicado['titulo']) ?>
                                </a>
                            </h6>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                    <p class="text-muted small mt-3">No hay notas de prensa recientes.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- P4 - DIRECTORIO DE CLUBES (TABLA + MAPA) -->
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
                <!-- Filtros actualizados con todos los estados de la base de datos de muestra -->
                <select id="stateFilter" class="form-select">
                    <option value="all" selected>Todos los Estados</option>
                    <option value="Aguascalientes">Aguascalientes</option>
                    <option value="Baja California">Baja California</option>
                    <option value="Ciudad de México">Ciudad de México</option>
                    <option value="Coahuila">Coahuila</option>
                    <option value="Colima">Colima</option>
                    <option value="Estado de México">Estado de México</option>
                    <option value="Guanajuato">Guanajuato</option>
                    <option value="Jalisco">Jalisco</option>
                    <option value="Nuevo León">Nuevo León</option>
                    <option value="Puebla">Puebla</option>
                    <option value="Querétaro">Querétaro</option>
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

    <!-- P5 - PATROCINADORES -->
    <section class="seccion-sponsors text-center">
        <!-- 1. NIVEL 1: PATROCINADORES PRINCIPALES (Fijos) -->
        <?php if (!empty($sponsors_principales)): ?>
        <div class="container mb-5">
            <div class="row justify-content-center align-items-center g-4">
                <?php foreach($sponsors_principales as $sponsor): ?>
                    <div class="col-6 col-md-4 col-lg-3 text-center transition-scale">
                        <?php if($sponsor['enlace'] != '#'): ?><a href="<?= e($sponsor['enlace']) ?>" target="_blank" rel="noopener noreferrer" class="text-decoration-none"><?php endif; ?>
                        
                        <img src="<?= e($sponsor['imagen_url']) ?>" alt="<?= e($sponsor['nombre']) ?>" title="<?= e($sponsor['nombre']) ?>" class="img-fluid mb-2" style="max-height: 90px; object-fit: contain;">
                        
                        <?php if($sponsor['enlace'] != '#'): ?></a><?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="container"><hr class="border-secondary mb-5 opacity-25"></div>
        <?php endif; ?>

        <!-- 2. NIVEL 2: SOCIOS TÉCNICOS (Carrusel hacia la IZQUIERDA) -->
        <?php if (!empty($sponsors_tecnicos)): ?>
        <div class="marquee-contenedor mb-5">
            <div class="marquee-contenido">
                <?php 
                // UX Fix: Calculamos cuántas veces clonar para llenar cualquier pantalla 
                // (Si hay pocos logos, los repetimos 6 veces; si hay muchos, 2 veces).
                $repeticiones_tecnicos = (count($sponsors_tecnicos) < 5) ? 6 : 2;
                for ($i = 0; $i < $repeticiones_tecnicos; $i++): 
                    foreach($sponsors_tecnicos as $sponsor): ?>
                        <?php if($sponsor['enlace'] != '#'): ?><a href="<?= e($sponsor['enlace']) ?>" target="_blank"><?php endif; ?>
                        <img src="<?= e($sponsor['imagen_url']) ?>" alt="<?= e($sponsor['nombre']) ?>" class="img-fluid marquee-item" style="max-height: 65px;">
                        <?php if($sponsor['enlace'] != '#'): ?></a><?php endif; ?>
                    <?php endforeach; 
                endfor; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- 3. NIVEL 3: PROVEEDORES OFICIALES (Carrusel hacia la DERECHA) -->
        <?php if (!empty($sponsors_proveedores)): ?>
        <div class="marquee-contenedor">
            <div class="marquee-contenido-reverse">
                <?php 
                // Misma lógica matemática para la fila inferior
                $repeticiones_proveedores = (count($sponsors_proveedores) < 5) ? 6 : 2;
                for ($i = 0; $i < $repeticiones_proveedores; $i++): 
                    foreach($sponsors_proveedores as $sponsor): ?>
                        <?php if($sponsor['enlace'] != '#'): ?><a href="<?= e($sponsor['enlace']) ?>" target="_blank"><?php endif; ?>
                        <img src="<?= e($sponsor['imagen_url']) ?>" alt="<?= e($sponsor['nombre']) ?>" class="img-fluid marquee-item" style="max-height: 55px;">
                        <?php if($sponsor['enlace'] != '#'): ?></a><?php endif; ?>
                    <?php endforeach; 
                endfor; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="contenedor-boton mt-5 pt-3">
            <a href="mailto:comunicacion@mexrugby.com" class="btn-quiero-sponsor text-decoration-none">QUIERO SER SPONSOR <i class="bi bi-arrow-right"></i></a>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

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
                    // CAMBIO CLAVE: Ahora leemos una clase lógica ('match-filter') en lugar de la visual ('d-none')
                    // Esto permite que el mapa muestre equipos aunque estén ocultos por la paginación de la tabla
                    if (row.classList.contains('match-filter')) {
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

                // Ajustar el zoom del mapa según los puntos encontrados
                if (bounds.length > 0) {
                    map.fitBounds(bounds, {padding: [50, 50], maxZoom: 10});
                } else {
                    map.setView([23.6345, -102.5528], 5); // Vista general de México
                }

                // Manejo UI del Botón "Cargar Más" / "Mostrar Menos"
                const searchInput = document.getElementById('searchInput');
                const stateFilter = document.getElementById('stateFilter');
                
                if (matchCount === 0) {
                    noResultsMsg.classList.remove('d-none');
                    loadMoreContainer.classList.add('d-none');
                } else {
                    noResultsMsg.classList.add('d-none');
                    
                    if (searchInput.value.trim() !== '' || stateFilter.value !== 'all') {
                        // Ocultar botón si hay filtros (la tabla muestra todo lo encontrado sin paginar)
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
                        row.classList.add('match-filter'); // Etiqueta lógica para el mapa
                        
                        // Lógica visual de paginación para la tabla
                        if (searchTerm !== '' || selectedState !== 'all' || isShowingAll) {
                             row.classList.remove('d-none');
                        } else {
                            if (visibleCount < visibleLimit) {
                                row.classList.remove('d-none');
                                visibleCount++;
                            } else {
                                row.classList.add('d-none'); // Oculto en tabla, pero el mapa lo leerá
                            }
                        }
                    } else {
                        row.classList.remove('match-filter'); // Lo quitamos del mapa
                        row.classList.add('d-none');          // Lo quitamos de la tabla
                    }
                });
                
                updateMapAndUI(matchCount);
            }

            filterTable();

            if(searchInput && stateFilter) {
                searchInput.addEventListener('input', filterTable);
                stateFilter.addEventListener('change', filterTable);
            }

            if (btnLoadMore) {
                btnLoadMore.addEventListener('click', function(e) {
                    e.preventDefault(); // Evita cualquier salto nativo del botón
                    
                    if (isShowingAll) {
                        // Estado "Mostrar Menos"
                        visibleLimit = inicialLimit;
                        isShowingAll = false;
                        
                        // Refiltramos la tabla para ocultar los registros
                        filterTable();
                        
                        // [UX FIX]: En lugar de scrollear a toda la sección, scrolleamos suavemente 
                        // directo al inicio de la TABLA, restando un pequeño offset (ej. 100px) 
                        // para que el título "ENCUENTRA TU CLUB" y los filtros queden a la vista.
                        const tableHeaderOffset = document.getElementById('directorio-section').getBoundingClientRect().top + window.scrollY - 100;
                        
                        window.scrollTo({
                            top: tableHeaderOffset,
                            behavior: 'smooth'
                        });

                    } else {
                        // Estado "Cargar Más"
                        visibleLimit += increment;
                        filterTable();
                    }
                });
            }
        });
    </script>
</body>
</html>