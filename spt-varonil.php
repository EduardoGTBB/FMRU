<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - SELECCIÓN VARONIL XVs (SERPIENTES)
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

// 1. Datos del Roster Actual (Basado en el PDF proporcionado)
$seleccionados = [
    ["nombre" => "Max Douek", "foto" => "assets/img/users/rugby_avatar_04.png"],
    ["nombre" => "Alejandro Pradilla", "foto" => "assets/img/users/rugby_avatar_04.png"],
    ["nombre" => "Jorge Cortés", "foto" => "assets/img/users/rugby_avatar_04.png"],
    ["nombre" => "Manuel Garrido", "foto" => "assets/img/users/rugby_avatar_04.png"],
    ["nombre" => "Borja Mandatumiz", "foto" => "assets/img/users/rugby_avatar_04.png"],
    ["nombre" => "Mauricio Moreno", "foto" => "assets/img/users/rugby_avatar_04.png"],
    ["nombre" => "Camilo Falcón", "foto" => "assets/img/users/rugby_avatar_04.png"],
    ["nombre" => "Luis Ángel Galindo", "foto" => "assets/img/users/rugby_avatar_04.png"],
    ["nombre" => "Jesús Rico", "foto" => "assets/img/users/rugby_avatar_04.png"],
    ["nombre" => "Mizael Loredo", "foto" => "assets/img/users/rugby_avatar_04.png"],
    ["nombre" => "Abubacar Conde", "foto" => "assets/img/users/rugby_avatar_04.png"],
    ["nombre" => "Tomás Allen", "foto" => "assets/img/users/rugby_avatar_04.png"],
    ["nombre" => "Tobías Hernández", "foto" => "assets/img/users/rugby_avatar_04.png"],
    ["nombre" => "Nicolás Falcón", "foto" => "assets/img/users/rugby_avatar_04.png"]
];

// 2. Datos del Staff Técnico
$staff = [
    ["rol" => "Entrenador", "nombre" => "Rubén Duque", "icono" => "bi-person-badge-fill"],
    ["rol" => "Preparador Físico", "nombre" => "Franco Garcia", "icono" => "bi-heart-pulse-fill"],
    ["rol" => "Médico", "nombre" => "Cristóbal Flores", "icono" => "bi-bandaid-fill"],
    ["rol" => "Médico", "nombre" => "Santiago Aguirre", "icono" => "bi-bandaid-fill"],
    ["rol" => "Manager", "nombre" => "Guillermo Bergn", "icono" => "bi-clipboard-check-fill"]
];

// 3. Histórico de Seleccionados (Muestra representativa)
$historico = [
    ["nombre" => "Víctor Año", "club" => "Black Thunder", "caps" => "28"],
    ["nombre" => "Miguel Carner", "club" => "Pumas UNAM", "caps" => "35"],
    ["nombre" => "Pablo Testas", "club" => "Wallabies", "caps" => "22"],
    ["nombre" => "Arturo Maltos", "club" => "Lobos", "caps" => "19"],
    ["nombre" => "Tomás Fernández", "club" => "Tazmania", "caps" => "15"]
];

// Define el título de la página
$page_title = "Selección Varonil"; 

include 'includes/head.php';
include 'includes/navbar.php'; 
?>
    <header class="position-relative" style="background-color: var(--verde-oscuro); background-image: url('assets/img/banner/01_competencia_xv_varonil.jpg'); background-size: cover; background-position: center; min-height: 60vh; display: flex; align-items: center;">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.3);"></div>
        <div class="container position-relative z-2">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="glass-panel text-white shadow-lg">
                        <span class="badge bg-success mb-3 px-3 py-2 fw-bold text-uppercase" style="letter-spacing: 2px;">Serpientes</span>
                        <h1 class="display-3 fw-bold text-uppercase mb-3 text-shadow">Liga de Rugby Varonil</h1>
                        <p class="lead mb-0 fw-semibold opacity-75">El máximo representativo nacional de Rugby 15s en México.</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container my-5">
        
        <!-- SECCIÓN: ROSTER (JUGADORES) -->
        <section class="mb-5 pb-5 border-bottom">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h2 class="section-title border-0 pb-0 mb-2">Roster Actual</h2>
                    <p class="text-muted">Conoce a los seleccionados que portan la camiseta tricolor.</p>
                </div>
            </div>
            
            <div class="row g-4 justify-content-center">
                <?php foreach($seleccionados as $jugador): ?>
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                    <div class="player-card">
                        
                        <!-- FIX: Etiqueta <img> dinámica insertada aquí -->
                        <img src="<?= e($jugador['foto']) ?>" class="player-photo" alt="<?= e($jugador['nombre']) ?>">
                        
                        <div class="player-info">
                            <span class="player-badge">Seleccionado</span>
                            <h4 class="player-name"><?= e($jugador['nombre']) ?></h4>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- SECCIÓN: STAFF TÉCNICO -->
        <section class="mb-5 pb-5 border-bottom">
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="section-title border-0 pb-0 mb-2">Staff Técnico</h2>
                </div>
            </div>
            
            <div class="row g-3">
                <?php foreach($staff as $miembro): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="staff-card shadow-sm">
                        <div class="staff-icon">
                            <i class="bi <?= e($miembro['icono']) ?>"></i>
                        </div>
                        <div>
                            <div class="staff-role"><?= e($miembro['rol']) ?></div>
                            <h4 class="staff-name"><?= e($miembro['nombre']) ?></h4>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- SECCIÓN: HISTÓRICO DE SELECCIONADOS -->
        <section class="mb-5">
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="section-title border-0 pb-0 mb-2">Histórico Seleccionados Nacionales</h2>
                    <p class="text-muted">Jugadores que han dejado su marca en la historia de Las Serpientes.</p>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="bg-white rounded-3 shadow-sm border overflow-hidden table-responsive">
                        <table class="table table-borderless table-historic mb-0">
                            <thead>
                                <tr>
                                    <th scope="col" class="ps-4">Nombre del Jugador</th>
                                    <th scope="col">Club / Asociación</th>
                                    <th scope="col" class="text-center">CAPS (Partidos Oficiales)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($historico as $jugador): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark"><?= e($jugador['nombre']) ?></td>
                                    <td><?= e($jugador['club']) ?></td>
                                    <td class="text-center fw-bold text-secondary">
                                        <span class="badge bg-light text-dark border px-3 py-2"><?= e($jugador['caps']) ?></span>
                                    </td>
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

</body>
</html>