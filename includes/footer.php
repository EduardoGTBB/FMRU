<?php
// includes/footer.php
if (basename($_SERVER['PHP_SELF']) == 'footer.php') {
    die('Acceso denegado');
}
?>
<!-- P5: FOOTER -->
<footer class="bg-dark text-white pt-5 pb-3 border-top" style="border-color: var(--verde-claro) !important; border-width: 5px !important;">
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
            <div class="col-md-6 text-center text-md-end small text-muted mt-2 mt-md-0">
                <a href="#" class="text-muted text-decoration-none me-3 hover-white">Políticas de Privacidad</a>
                <a href="#" class="text-muted text-decoration-none hover-white">Términos Legales</a>
            </div>
        </div>
    </div>
</footer>

<!-- <footer class="bg-dark text-white pt-5 pb-3 border-top" style="border-color: var(--verde-claro) !important; border-width: 5px !important;">
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
                    <li class="mb-2"><a href="mailto:comunicacion@mexrugby.com" class="text-light text-decoration-none custom-hover-link"><i class="bi bi-envelope-fill me-2 text-danger"></i> comunicacion@mexrugby.com</a></li>
                    <li class="mb-2"><a href="tel:+525555761274" class="text-light text-decoration-none custom-hover-link"><i class="bi bi-telephone-fill me-2 text-danger"></i> (+52 55) 5576 1274</a></li>
                </ul>
                <h6 class="text-uppercase fw-bold mb-3" style="color: var(--verde-claro);">Redes Sociales</h6>
                <div class="d-flex align-items-center gap-3">
                    <a href="https://www.facebook.com/RugbyMexico" class="text-white d-flex align-items-center justify-content-center transition-scale" target="_blank" style="width: 32px; height: 32px;"><i class="bi bi-facebook fs-4"></i></a>
                    <a href="https://www.instagram.com/rugby_mexico/?hl=es" class="text-white d-flex align-items-center justify-content-center transition-scale" target="_blank" style="width: 32px; height: 32px;"><i class="bi bi-instagram fs-4"></i></a>
                    <a href="https://x.com/Rugby_Mexico" class="text-white d-flex align-items-center justify-content-center transition-scale" target="_blank" style="width: 32px; height: 32px;"><i class="bi bi-twitter-x fs-4"></i></a>
                    <a href="https://www.youtube.com/user/FMRUOficial" class="text-white d-flex align-items-center justify-content-center transition-scale" target="_blank" style="width: 32px; height: 32px;"><i class="bi bi-youtube fs-3"></i></a>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('currentYear').textContent = new Date().getFullYear();
    });
</script> -->