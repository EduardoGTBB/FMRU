<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - SELECCIÓN VARONIL XVs (SERPIENTES)
 * Diseño Élite (Modo Oscuro) - OWASP Compliance
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); 
ini_set('session.use_only_cookies', 1);
session_start();

require_once __DIR__ . '/admin/config/db.php';

// 1. Datos del Roster
$seleccionados = [
    ["nombre" => "Max Douek", "foto" => "assets/img/users/rugby_avatar_04.png", "posicion" => "Forward"],
    ["nombre" => "Alejandro Pradilla", "foto" => "assets/img/users/rugby_avatar_04.png", "posicion" => "Back"],
    ["nombre" => "Jorge Cortés", "foto" => "assets/img/users/rugby_avatar_04.png", "posicion" => "Forward"],
    ["nombre" => "Manuel Garrido", "foto" => "assets/img/users/rugby_avatar_04.png", "posicion" => "Back"]
];

// 2. Staff Técnico
$staff = [
    ["rol" => "Head Coach", "nombre" => "Rubén Duque", "icono" => "bi-person-vcard"],
    ["rol" => "Prep. Físico", "nombre" => "Franco Garcia", "icono" => "bi-activity"],
    ["rol" => "Médico", "nombre" => "Cristóbal Flores", "icono" => "bi-heart-pulse"]
];

// 3. Histórico de CAPS
$historico = [
    ["nombre" => "Miguel Carner", "caps" => "35", "status" => "Leyenda"],
    ["nombre" => "Víctor Año", "caps" => "28", "status" => "Veterano"],
    ["nombre" => "Pablo Testas", "caps" => "22", "status" => "Activo"]
];

$page_title = "Selección Varonil XVs"; 
include 'includes/head.php';
include 'includes/navbar.php'; 
?>
    <!-- SECCIÓN OSCURA GLOBAL -->
    <div style="background-color: #111111; color: white;">

        <!-- HERO SERPIENTES -->
        <header class="position-relative" style="background-image: url('assets/img/banner/Mesa-de-trabajo-25.png'); background-size: cover; background-position: center; min-height: 60vh; display: flex; align-items: center;">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(17,17,17,0.4) 0%, #111111 100%);"></div>
            <div class="container position-relative z-2 text-center pt-5">
                <div class="glass-panel-dark d-inline-block px-5 py-4">
                    <span class="badge bg-danger mb-3 px-4 py-2 fs-6 fw-bold text-uppercase" style="letter-spacing: 3px;">Las Serpientes</span>
                    <h1 class="display-3 fw-bolder text-uppercase mb-0 text-white" style="letter-spacing: -1px;">XV Varonil</h1>
                </div>
            </div>
        </header>

        <main class="container py-5">
            
            <!-- ROSTER DE JUGADORES (Tarjetas PRO) -->
            <section class="mb-5 pb-5">
                <div class="d-flex justify-content-between align-items-end mb-5 border-bottom border-dark pb-3">
                    <h2 class="fw-bold text-uppercase text-white mb-0">Roster <span style="color: var(--rojo-institucional);">Oficial</span></h2>
                </div>
                
                <div class="row g-4 justify-content-center">
                    <?php foreach($seleccionados as $jugador): ?>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                        <div class="pro-player-card shadow-lg">
                            <!-- FIX: Etiqueta img limpia para que el CSS tome el control total -->
                            <img src="<?= e($jugador['foto']) ?>" class="pro-player-img" alt="<?= e($jugador['nombre']) ?>">
                            
                            <div class="pro-player-info text-center">
                                <span class="badge text-uppercase mb-2" style="background-color: var(--rojo-institucional); letter-spacing: 1px;"><?= e($jugador['posicion']) ?></span>
                                <h4 class="fw-bold text-white mb-0"><?= e($jugador['nombre']) ?></h4>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- STAFF Y CAPS (Dividido en dos columnas) -->
            <section class="row g-5 mb-5 pb-5">
                
                <!-- STAFF TÉCNICO -->
                <div class="col-lg-5">
                    <h3 class="fw-bold text-uppercase mb-4" style="color: var(--verde-claro);">Cuerpo Técnico</h3>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach($staff as $miembro): ?>
                        <div class="d-flex align-items-center bg-dark rounded-3 p-3 border border-secondary border-opacity-25">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background: rgba(255,255,255,0.05);">
                                <i class="bi <?= e($miembro['icono']) ?> fs-3" style="color: var(--verde-claro);"></i>
                            </div>
                            <div>
                                <div class="text-uppercase small text-white-50 fw-bold tracking-wide"><?= e($miembro['rol']) ?></div>
                                <h5 class="text-white mb-0"><?= e($miembro['nombre']) ?></h5>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- LÍDERES DE CAPS (HISTÓRICO) -->
                <div class="col-lg-7">
                    <h3 class="fw-bold text-uppercase mb-4" style="color: var(--rojo-institucional);">Líderes de Caps</h3>
                    
                    <div class="d-flex flex-column gap-2">
                        <?php foreach($historico as $index => $jugador): ?>
                        <div class="honor-row honor-row-dark">
                            <div class="d-flex align-items-center gap-3 w-100">
                                <h4 class="mb-0 text-white-50 fw-bold" style="width: 30px;">#<?= $index + 1 ?></h4>
                                <div class="flex-grow-1">
                                    <h5 class="text-white fw-bold mb-1"><?= e($jugador['nombre']) ?></h5>
                                    <span class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-50"><?= e($jugador['status']) ?></span>
                                </div>
                                <div class="text-end">
                                    <div class="display-6 fw-bolder" style="color: var(--verde-claro); line-height: 1;"><?= e($jugador['caps']) ?></div>
                                    <div class="text-uppercase small text-white-50 fw-bold" style="letter-spacing: 1px;">Tests</div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </section>

        </main>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>