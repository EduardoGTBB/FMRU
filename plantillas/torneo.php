<?php
// plantillas/torneo.php
if(!isset($pagina) || !isset($config)) { die('Acceso denegado'); }

// Fallbacks seguros
$hero = !empty($config['imagen_hero']) ? $config['imagen_hero'] : 'assets/img/banner/Mesa-de-trabajo-25.png';
$historia = $config['historia'] ?? '';
$precios = $config['precios'] ?? [];
$galeria = $config['imagenes_adicionales'] ?? [];
?>

<main class="torneo-bg-arena pb-5">
    
    <!-- 1. Hero / Portada del Torneo -->
    <!-- FIX: Aumentamos a 85vh y eliminamos background-attachment: fixed para evitar el recorte extremo del póster -->
    <section class="landing-hero shadow" style="background-image: url('<?= e($hero) ?>'); min-height: 85vh; background-position: center center; background-repeat: no-repeat;">
        <!-- Oscurecemos ligeramente el fondo para que el título H1 blanco siempre sea legible -->
        <div class="articulo-overlay" style="background: rgba(17,17,17,0.4);"></div>
        
        <div class="container position-relative text-white d-flex flex-column justify-content-center h-100" style="z-index: 2; padding-top: 100px;">
            <h1 class="display-2 fw-bold text-uppercase mb-3 text-shadow text-center"><?= e($pagina['titulo']) ?></h1>
        </div>
    </section>

    <!-- 2. Historia / Descripción Central -->
    <?php if(!empty($historia)): ?>
    <section class="container py-5 my-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-4">
                    <h5 class="fw-bold text-uppercase" style="color: var(--verde-principal); letter-spacing: 2px;">
                        <i class="bi bi-gem me-2 text-warning"></i> Sobre el Torneo <i class="bi bi-gem ms-2 text-warning"></i>
                    </h5>
                </div>
                <div class="torneo-historia-texto">
                    <?= nl2br(e($historia)) ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 3. Tarjetas de Fases Dinámicas -->
    <?php if(!empty($precios)): ?>
    <section class="container py-5">
        <div class="text-center mb-5">
            <h3 class="fw-bold text-uppercase" style="color: var(--verde-principal); letter-spacing: 2px;">
                Fases de Inscripción
            </h3>
        </div>
        
        <!-- OWASP/Bootstrap: row-cols autoajusta el diseño. Centra las tarjetas si son 1, 2 o 3. Las rompe abajo si son más de 4 -->
        <div class="row g-4 justify-content-center row-cols-1 row-cols-md-2 row-cols-lg-<?= count($precios) > 3 ? '4' : '3' ?>">
            
            <?php foreach($precios as $precio): 
                $activa = ($precio['estado'] === 'activa');
            ?>
            <div class="col">
                <div class="price-card-torneo text-center pb-4 shadow-sm <?= $activa ? 'fase-activa' : '' ?>">
                    <div class="price-badge-torneo shadow-sm">
                        <?= e($precio['nombre']) ?>
                    </div>
                    
                    <div class="price-amount-torneo">
                        $<?= number_format((float)$precio['monto'], 0) ?> <span class="fs-5 text-muted fw-bold">MXN</span>
                    </div>
                    
                    <ul class="list-unstyled text-start px-4 mt-auto mb-2 text-muted fw-semibold">
                        <li class="mb-3 border-bottom pb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Inscripción al torneo</li>
                        <li class="mb-3 border-bottom pb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Asistencia médica en campo</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Tercer Tiempo</li>
                    </ul>
                </div>
            </div>
            <?php endforeach; ?>
            
        </div>
    </section>
    <?php endif; ?>

    <!-- 4. Iframe de Registro (Space) -->
    <?php if(!empty($config['link_iframe'])): ?>
    <section class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
                <h4 class="fw-bold mb-4 text-uppercase" style="color: var(--verde-principal); letter-spacing: 2px;">
                    <i class="bi bi-cursor-fill me-2 text-warning"></i> ¡Regístrate!
                </h4>
                <div class="iframe-container ratio ratio-16x9 bg-white p-3 rounded-4 shadow-lg border">
                    <iframe 
                        src="<?= e($config['link_iframe']) ?>" 
                        title="Formulario de Registro" 
                        sandbox="allow-scripts allow-same-origin allow-forms"
                        loading="lazy"
                        style="border: none; border-radius: 12px;">
                    </iframe>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 5. Galería de Imágenes Inferior (Mosaico) -->
    <?php if(!empty($galeria)): ?>
    <section class="container py-5 mt-4 border-top border-dark border-opacity-10">
        <div class="row g-2 justify-content-center">
            <?php foreach($galeria as $img): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="overflow-hidden rounded shadow-sm h-100">
                    <img src="<?= e($img) ?>" class="img-fluid w-100 transition-scale object-fit-cover" alt="Galería Torneo" style="height: 250px; cursor: zoom-in;" data-bs-toggle="modal" data-bs-target="#imageLightboxModal" data-img-url="<?= e($img) ?>">
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

</main>

<!-- MODAL LIGHTBOX BOOTSTRAP 5 -->
<div class="modal fade" id="imageLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-header border-0 pb-0 justify-content-end position-absolute top-0 end-0 z-3 p-3">
                <button type="button" class="btn btn-dark rounded-circle shadow d-flex align-items-center justify-content-center transition-scale" data-bs-dismiss="modal" aria-label="Cerrar" style="width: 45px; height: 45px; border: 2px solid white;">
                    <i class="bi bi-x-lg fs-5 text-white"></i>
                </button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="lightboxImage" src="" class="img-fluid rounded shadow-lg" alt="Imagen maximizada" style="max-height: 90vh;">
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lightboxModal = document.getElementById('imageLightboxModal');
        if (lightboxModal) {
            lightboxModal.addEventListener('show.bs.modal', function (event) {
                const triggerImage = event.relatedTarget;
                const imageUrl = triggerImage.getAttribute('data-img-url');
                document.getElementById('lightboxImage').src = imageUrl;
            });
        }
    });
</script>