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
    ["nombre" => "Max Douek", "foto" => "assets/img/jugadores/default.jpg"],
    ["nombre" => "Alejandro Pradilla", "foto" => "assets/img/jugadores/default.jpg"],
    ["nombre" => "Jorge Cortés", "foto" => "assets/img/jugadores/default.jpg"],
    ["nombre" => "Manuel Garrido", "foto" => "assets/img/jugadores/default.jpg"],
    ["nombre" => "Borja Mandatumiz", "foto" => "assets/img/jugadores/default.jpg"],
    ["nombre" => "Mauricio Moreno", "foto" => "assets/img/jugadores/default.jpg"],
    ["nombre" => "Camilo Falcón", "foto" => "assets/img/jugadores/default.jpg"],
    ["nombre" => "Luis Ángel Galindo", "foto" => "assets/img/jugadores/default.jpg"],
    ["nombre" => "Jesús Rico", "foto" => "assets/img/jugadores/default.jpg"],
    ["nombre" => "Mizael Loredo", "foto" => "assets/img/jugadores/default.jpg"],
    ["nombre" => "Abubacar Conde", "foto" => "assets/img/jugadores/default.jpg"],
    ["nombre" => "Tomás Allen", "foto" => "assets/img/jugadores/default.jpg"],
    ["nombre" => "Tobías Hernández", "foto" => "assets/img/jugadores/default.jpg"],
    ["nombre" => "Nicolás Falcón", "foto" => "assets/img/jugadores/default.jpg"]
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
$page_title = "Inicio"; 

include 'includes/head.php';
include 'includes/navbar.php'; 
?>
    <!-- BANNER HERO -->
    <header class="hero-banner-serpientes">
        <div class="container">
            <span class="badge bg-danger mb-3 px-3 py-2 fw-bold text-uppercase tracking-wide" style="letter-spacing: 2px;">Las Serpientes</span>
            <h1 class="display-4 fw-bold text-uppercase mb-3">Selección Varonil de XVs</h1>
            <p class="lead mb-0">El máximo representativo nacional de Rugby 15s en México.</p>
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
                        <!-- Placeholder de imagen: Reemplazar src por la ruta real -->
                        <div class="player-photo d-flex align-items-center justify-content-center bg-light">
                            <i class="bi bi-person-fill text-secondary opacity-25" style="font-size: 5rem;"></i>
                        </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>