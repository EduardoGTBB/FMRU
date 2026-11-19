<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - JORNADAS Y PARTIDOS (Alternativa Tarjetas)
 * Arquitectura Segura - OWASP Compliance
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); 
ini_set('session.use_only_cookies', 1);
session_start();

function e($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// === DATOS SIMULADOS DE JORNADAS (Agrupados por número de jornada) ===
$jornadas_datos = [
    1 => [ // JORNADA 1
        "Centro" => [
            [
                "estado" => "PARTIDO FINALIZADO", 
                "equipo_local" => "Tazmania RC", 
                "escudo_local" => "assets/img/escudos/Emblema de rugby Tazmania RC 42.png",
                "marcador_local" => 42,
                "equipo_visitante" => "Wallabies",
                "escudo_visitante" => "assets/img/escudos/Wallabies.png",
                "marcador_visitante" => 14,
                "fecha_hora" => "26 MAR 2026 / 12:00 HRS",
                "campo" => "CAMPO: FELIX",
                "arbitro" => "ARBITRO: CARLOS CORDERO",
                "detalles_liga" => ""
            ],
            [
                "estado" => "PARTIDO FINALIZADO", 
                "equipo_local" => "Legion Cuervos", 
                "escudo_local" => "assets/img/escudos/Legion_Cuervos.png", 
                "marcador_local" => 22,
                "equipo_visitante" => "Pumas UNAM",
                "escudo_visitante" => "assets/img/escudos/Pumas_UNAM.png",
                "marcador_visitante" => 60,
                "fecha_hora" => "9 ABR 2026",
                "campo" => "CAMPO: LA CAMPANA",
                "arbitro" => "ARBITRO: RICARDO BONILLA",
                "detalles_liga" => "SEGUNDA FUERZA"
            ]
        ],
        "Occidente" => [
            [
                "estado" => "PARTIDO FINALIZADO", 
                "equipo_local" => "Rhinos", 
                "escudo_local" => "assets/img/escudos/Rhinos.png", 
                "marcador_local" => 77,
                "equipo_visitante" => "Lobos",
                "escudo_visitante" => "assets/img/escudos/Lobos.png",
                "marcador_visitante" => 7,
                "fecha_hora" => "12 MAR 2026",
                "campo" => "CAMPO: TLAQUEPAQUE",
                "arbitro" => "ARBITRO: DAVID GUZMAN",
                "detalles_liga" => ""
            ]
        ]
    ],
    2 => [ // JORNADA 2
        "Centro" => [
            [
                "estado" => "PRÓXIMO PARTIDO", 
                "equipo_local" => "Tazmania RC", 
                "escudo_local" => "assets/img/escudos/Tazmania_RC_alternativo.png", 
                "marcador_local" => "-",
                "equipo_visitante" => "Pumas UNAM",
                "escudo_visitante" => "assets/img/escudos/Pumas_UNAM.png",
                "marcador_visitante" => "-",
                "fecha_hora" => "28 MAY 2026",
                "campo" => "CAMPO: POR DEFINIR",
                "arbitro" => "",
                "detalles_liga" => ""
            ]
        ]
    ]
];

$page_title = "Jornadas y Partidos";
include 'includes/head.php';
include 'includes/navbar.php'; 
?>

<!-- Estilos específicos para la alternativa de Tarjetas -->
<style>
    .hero-banner-jornadas { background-color: var(--negro-nav); background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/img/banner/Mesa-de-trabajo-25.png'); background-size: cover; background-position: center 30%; color: white; padding: 60px 0; }
    
    .category-tabs .nav-link { color: white; background-color: #333; font-weight: 800; text-transform: uppercase; border-radius: 0; padding: 15px; font-size: 0.9rem; }
    .category-tabs .nav-link:hover { background-color: #444; }
    .category-tabs .nav-link.active { background-color: var(--verde-principal); color: white; }
    .category-tabs .nav-item:nth-child(2) .nav-link.active { background-color: var(--rojo-institucional); }
    .category-tabs .nav-item:nth-child(3) .nav-link.active { background-color: var(--azul-oscuro); }

    .sub-tabs .nav-link { color: #999; font-weight: 800; text-transform: uppercase; font-size: 1.2rem; border: none; background: transparent; padding: 10px 30px; }
    .sub-tabs .nav-link.active { color: var(--verde-claro); }

    /* Paginación */
    .jornada-pagination .page-link { color: #555; border: none; background: transparent; font-weight: 600; padding: 8px 12px; margin: 0 2px; border-radius: 4px; cursor: pointer; transition: all 0.2s;}
    .jornada-pagination .page-item.active .page-link { background-color: var(--gris-fondo); color: var(--verde-principal); font-weight: 800; }
    .jornada-title { font-weight: 800; color: var(--azul-oscuro); text-transform: uppercase; font-size: 1rem; margin-bottom: 10px; }

    /* === DISEÑO DE TARJETAS (MATCH CARDS) === */
    .zona-titulo { font-weight: 800; color: var(--verde-principal); text-transform: uppercase; border-bottom: 3px solid var(--verde-claro); padding-bottom: 5px; margin-bottom: 1.5rem; font-size: 1.25rem;}
    
    .match-card { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #eaeaea; transition: transform 0.2s, box-shadow 0.2s; height: 100%; display: flex; flex-direction: column;}
    .match-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
    
    .match-header { background-color: var(--gris-fondo); padding: 10px 15px; border-bottom: 1px solid #eaeaea; display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; }
    .match-status { color: #888; }
    .match-status.proximo { color: var(--verde-claro); }
    .match-league { color: var(--rojo-institucional); }
    
    .match-body { padding: 25px 15px; display: flex; align-items: center; justify-content: center; flex-grow: 1; }
    
    /* Equipos y Marcador */
    .team-block { width: 35%; text-align: center; }
    .team-logo { width: 70px; height: 70px; object-fit: contain; margin-bottom: 10px; }
    .team-name-card { font-weight: 800; color: var(--azul-oscuro); font-size: 0.95rem; line-height: 1.2; margin: 0; }
    .team-label { font-size: 0.65rem; color: #999; text-transform: uppercase; font-weight: 700; margin-bottom: 5px; display: block; letter-spacing: 1px;}
    
    .score-block { width: 30%; display: flex; justify-content: center; align-items: center; gap: 10px; }
    .score-number { font-size: 2rem; font-weight: 800; color: var(--verde-principal); background-color: var(--gris-fondo); padding: 5px 15px; border-radius: 8px; }
    .score-number.pendiente { color: #bbb; background-color: transparent; font-size: 1.5rem; padding: 0; }
    .score-divider { font-weight: 800; color: #ccc; font-size: 0.9rem; }
    
    .match-footer { background-color: var(--verde-principal); color: white; padding: 12px 15px; font-size: 0.8rem; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; }
    .match-footer i { color: var(--verde-claro); margin-right: 5px; }
    .match-footer span { display: block; margin-bottom: 3px; }
    .match-footer span:last-child { margin-bottom: 0; }
</style>

<!-- BANNER HERO -->
<header class="hero-banner-jornadas">
    <div class="container">
        <h1 class="display-4 fw-bold text-uppercase mb-0">Mayor Varonil</h1>
    </div>
</header>

<main class="container my-5">

    <!-- 1. PESTAÑAS DE CATEGORÍAS -->
    <ul class="nav nav-fill category-tabs mb-4">
        <li class="nav-item"><a class="nav-link active" href="#">Mayor Varonil</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Femenil</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Universitario</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Juvenil</a></li>
        <li class="nav-item"><a class="nav-link" href="#" style="background-color: #aed581; color: #333;">Infantil</a></li>
    </ul>

    <!-- 2. SUB-PESTAÑAS -->
    <div class="d-flex justify-content-center mb-4 border-bottom">
        <ul class="nav sub-tabs">
            <li class="nav-item"><a class="nav-link active" href="#">Partidos</a></li>
            <!-- <li class="nav-item"><a class="nav-link" href="#">Tablas</a></li> -->
        </ul>
    </div>

    <!-- 3. SELECTOR DE JORNADA DINÁMICO -->
    <div class="text-center mb-5">
        <div class="jornada-title">Jornada</div>
        <ul class="pagination justify-content-center jornada-pagination">
            <?php 
            $primera_jornada = true;
            foreach (array_keys($jornadas_datos) as $num_jornada): ?>
                <li class="page-item <?= $primera_jornada ? 'active' : '' ?>">
                    <a class="page-link btn-jornada" data-target="<?= $num_jornada ?>"><?= $num_jornada ?></a>
                </li>
            <?php 
                $primera_jornada = false;
            endforeach; ?>
        </ul>
    </div>

    <!-- 4. LISTADO DE PARTIDOS (DISEÑO TARJETAS) -->
    <div>
        <?php 
        $primera_jornada = true;
        foreach ($jornadas_datos as $num_jornada => $zonas): ?>
            
            <div class="contenedor-jornada <?= $primera_jornada ? '' : 'd-none' ?>" id="jornada-<?= $num_jornada ?>">
                
                <?php foreach ($zonas as $zona => $partidos): ?>
                    
                    <!-- Título de la Zona -->
                    <h3 class="zona-titulo mt-4">Zona <?= e($zona) ?></h3>
                    
                    <div class="row g-4 mb-5">
                        <?php foreach ($partidos as $partido): ?>
                            <?php 
                                $es_proximo = ($partido['estado'] === "PRÓXIMO PARTIDO");
                                $clase_estado = $es_proximo ? "proximo" : "";
                                $clase_marcador = $es_proximo ? "pendiente" : "";
                            ?>
                            
                            <!-- Tarjeta de Partido -->
                            <div class="col-lg-6 col-xl-4">
                                <div class="match-card">
                                    
                                    <!-- Cabecera de Tarjeta -->
                                    <div class="match-header">
                                        <span class="match-status <?= $clase_estado ?>"><?= e($partido['estado']) ?></span>
                                        <?php if(!empty($partido['detalles_liga'])): ?>
                                            <span class="match-league"><?= e($partido['detalles_liga']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Cuerpo (Equipos y Marcador) -->
                                    <div class="match-body">
                                        
                                        <!-- Local -->
                                        <div class="team-block">
                                            <span class="team-label">Local</span>
                                            <img src="<?= e($partido['escudo_local']) ?>" alt="Local" class="team-logo">
                                            <h5 class="team-name-card"><?= e($partido['equipo_local']) ?></h5>
                                        </div>
                                        
                                        <!-- Marcador Central -->
                                        <div class="score-block">
                                            <div class="score-number <?= $clase_marcador ?>"><?= e($partido['marcador_local']) ?></div>
                                            <div class="score-divider">VS</div>
                                            <div class="score-number <?= $clase_marcador ?>"><?= e($partido['marcador_visitante']) ?></div>
                                        </div>
                                        
                                        <!-- Visitante -->
                                        <div class="team-block">
                                            <span class="team-label">Visitante</span>
                                            <img src="<?= e($partido['escudo_visitante']) ?>" alt="Visitante" class="team-logo">
                                            <h5 class="team-name-card"><?= e($partido['equipo_visitante']) ?></h5>
                                        </div>
                                        
                                    </div>
                                    
                                    <!-- Pie de Tarjeta (Detalles) -->
                                    <div class="match-footer">
                                        <span><i class="bi bi-calendar-event"></i> <?= e($partido['fecha_hora']) ?></span>
                                        <?php if(!empty($partido['campo'])): ?>
                                            <span><i class="bi bi-geo-alt-fill"></i> <?= e($partido['campo']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                </div>
                            </div>
                            
                        <?php endforeach; ?>
                    </div>
                    
                <?php endforeach; ?>
            </div>
        <?php 
            $primera_jornada = false;
        endforeach; ?>
    </div>

</main>

<?php include 'includes/footer.php'; ?>

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> -->

<!-- Script de Paginación para Jornadas -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnJornadas = document.querySelectorAll('.btn-jornada');
    const contenedores = document.querySelectorAll('.contenedor-jornada');

    btnJornadas.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const targetJornada = this.getAttribute('data-target');

            // 1. Quitar 'active' de todos los botones
            btnJornadas.forEach(b => b.parentElement.classList.remove('active'));
            this.parentElement.classList.add('active');

            // 2. Mostrar contenedor correspondiente
            contenedores.forEach(contenedor => {
                if (contenedor.id === 'jornada-' + targetJornada) {
                    contenedor.classList.remove('d-none');
                } else {
                    contenedor.classList.add('d-none');
                }
            });
        });
    });
});
</script>
</body>
</html>