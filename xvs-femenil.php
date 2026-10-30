<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - LIGA XVS FEMENIL
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

// --- DATOS SIMULADOS PARA LA LIGA XVS FEMENIL ---

// 1. Equipos Participantes (Puedes sustituirlos por los reales)
$equipos_xvs = [
    ["nombre" => "Pumas UNAM", "estado" => "Ciudad de México", "logo" => "P"],
    ["nombre" => "Lobos Occidente", "estado" => "Jalisco", "logo" => "L"],
    ["nombre" => "Black Thunder RC", "estado" => "Ciudad de México", "logo" => "B"],
    ["nombre" => "Legión Cuervos", "estado" => "Ciudad de México", "logo" => "L"],
    ["nombre" => "Abejas Guanajuato", "estado" => "Guanajuato", "logo" => "A"],
    ["nombre" => "ITESO Rugby", "estado" => "Jalisco", "logo" => "I"],
    ["nombre" => "Tiburones Cancún", "estado" => "Quintana Roo", "logo" => "T"],
    ["nombre" => "Templarios RC", "estado" => "Ciudad de México", "logo" => "T"]
];

// 2. Histórico de Campeonatos (Adaptado de la tabla histórica)
$historico_campeonas = [
    ["temporada" => "2024 - 2025", "campeon" => "Pumas UNAM", "subcampeon" => "Black Thunder RC", "mvp" => "Isabela González"],
    ["temporada" => "2023 - 2024", "campeon" => "Lobos Occidente", "subcampeon" => "Legión Cuervos", "mvp" => "Tamara Raya Cano"],
    ["temporada" => "2022 - 2023", "campeon" => "Pumas UNAM", "subcampeon" => "Abejas Guanajuato", "mvp" => "Verónica Estrada"],
    ["temporada" => "2021 - 2022", "campeon" => "Black Thunder RC", "subcampeon" => "Tiburones Cancún", "mvp" => "Karina Landeros"],
    ["temporada" => "2019 - 2020", "campeon" => "Pumas UNAM", "subcampeon" => "Templarios RC", "mvp" => "Cristina Uribe"]
];

// Define el título de la página
$page_title = "Inicio"; 

include 'includes/head.php';
include 'includes/navbar.php'; 
?>

    <!-- BANNER HERO -->
    <header class="hero-banner-xvs">
        <div class="container">
            <h1 class="display-4 fw-bold text-uppercase">Liga de Rugby XVs Femenil</h1>
            <p class="lead mb-0">La máxima categoría del rugby union femenil en México.</p>
        </div>
    </header>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="container my-5">
        
        <!-- SECCIÓN: INTRODUCCIÓN -->
        <section class="row mb-5 text-center">
            <div class="col-lg-8 mx-auto">
                <p class="lead text-muted">
                    El Campeonato Nacional de Rugby XVs Femenil es la competición de mayor nivel y exigencia física en nuestro país. Reúne a los mejores clubes de diversas regiones para disputar el título nacional, fomentando el desarrollo continuo del rugby femenino, el trabajo en equipo y la formación de futuras seleccionadas nacionales.
                </p>
            </div>
        </section>

        <!-- SECCIÓN: EQUIPOS PARTICIPANTES -->
        <section class="mb-5 pb-4 border-bottom">
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="page-title">Equipos Participantes (Temporada Actual)</h2>
                </div>
            </div>
            
            <div class="row g-4">
                <?php foreach($equipos_xvs as $equipo): ?>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="team-card">
                        <div class="team-logo-placeholder">
                            <?= e($equipo['logo']) ?>
                        </div>
                        <h4 class="team-name"><?= e($equipo['nombre']) ?></h4>
                        <div class="team-state"><i class="bi bi-geo-alt-fill text-danger me-1"></i><?= e($equipo['estado']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- SECCIÓN: HISTÓRICO DE CAMPEONATOS -->
        <section class="mb-5">
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="page-title">Histórico de Campeonas</h2>
                    <p class="text-muted">Registro de los clubes que han conquistado el título nacional a lo largo de los años.</p>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="historical-table-container table-responsive">
                        <table class="table table-borderless table-historic mb-0">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 60px;" class="text-center"><i class="bi bi-trophy-fill text-warning fs-5"></i></th>
                                    <th scope="col">Temporada</th>
                                    <th scope="col">Campeón (1er Lugar)</th>
                                    <th scope="col">Subcampeón (2do Lugar)</th>
                                    <th scope="col">Jugadora Destacada (MVP)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($historico_campeonas as $index => $historia): ?>
                                <tr>
                                    <td class="text-center fw-bold text-muted"><?= $index + 1 ?></td>
                                    <td class="fw-semibold text-dark"><?= e($historia['temporada']) ?></td>
                                    <td><span class="champ-name"><i class="bi bi-star-fill text-warning me-1 small"></i> <?= e($historia['campeon']) ?></span></td>
                                    <td><?= e($historia['subcampeon']) ?></td>
                                    <td class="fst-italic text-secondary"><?= e($historia['mvp']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- FOOTER MODULAR -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>