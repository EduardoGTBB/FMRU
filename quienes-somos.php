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

// Define el título de la página
$page_title = "Inicio"; 

include 'includes/head.php';
include 'includes/navbar.php'; 
?>

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