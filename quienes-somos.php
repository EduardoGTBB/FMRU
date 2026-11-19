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

// 1. Inclusión correcta de la base de datos (importa $pdo y la función e() automáticamente)
require_once __DIR__ . '/admin/config/db.php';

try {
    // 2. Uso directo de $pdo sin volver a conectarse
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM fmru_settings");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    // Fallback en caso de que la tabla aún no tenga los datos
    $settings = [
        'quienes_somos_intro' => 'Somos el organismo rector del rugby en México...',
        'mision_texto' => 'Liderar el ecosistema del rugby mexicano...',
        'vision_texto' => 'Posicionar al rugby mexicano como referente...'
    ];
}

$page_title = "Quiénes Somos"; 

include 'includes/head.php';
include 'includes/navbar.php'; 
?>

    <!-- CONTENIDO PRINCIPAL INSTITUCIONAL -->
    <main class="hero-background-quienes">
        <div class="container institucional-header">
            <div class="row gx-5">
                <!-- Columna Izquierda: Introducción FMRU -->
                <div class="col-lg-6 mb-5 mb-lg-0 pe-lg-5">
                    <span class="badge-institucional">Quiénes Somos</span>
                    <h1 class="titulo-principal">FEDERACIÓN MEXICANA DE RUGBY</h1>
                    <!-- nl2br() respeta los saltos de línea que el admin ponga en el textarea -->
                    <p class="texto-cuerpo mb-4" style="color: #e0e0e0;">
                        <?= nl2br(e($settings['quienes_somos_intro'] ?? '')) ?>
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
                            <?= nl2br(e($settings['mision_texto'] ?? '')) ?>
                        </p>
                    </div>

                    <!-- VISIÓN -->
                    <div class="mb-5">
                        <h2 class="titulo-seccion">Visión</h2>
                        <p class="texto-cuerpo" style="color: #e0e0e0;">
                            <?= nl2br(e($settings['vision_texto'] ?? '')) ?>
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

    <?php include 'includes/footer.php'; ?>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('currentYear').textContent = new Date().getFullYear();
        });
    </script>
</body>
</html>