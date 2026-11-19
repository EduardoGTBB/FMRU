<?php
// plantillas/informativa.php
// Protección OWASP: Evita acceso directo al archivo
if(!isset($pagina) || !isset($config)) { die('Acceso denegado'); }

// 1. Validaciones seguras compatibles con cualquier versión de PHP
$hero = 'assets/img/banner/Mesa-de-trabajo-25.png'; // Imagen por defecto
if (isset($config['imagen_hero']) && $config['imagen_hero'] !== '') {
    $hero = $config['imagen_hero'];
}

$contenido = '';
if (isset($config['historia']) && $config['historia'] !== '') {
    $contenido = $config['historia'];
}

$galeria = array();
if (isset($config['imagenes_adicionales']) && is_array($config['imagenes_adicionales'])) {
    $galeria = $config['imagenes_adicionales'];
}
?>

<main class="bg-light pb-5">
    
    <!-- Hero / Portada Institucional -->
    <section class="landing-hero shadow-sm" style="background-image: url('<?= e($hero) ?>'); min-height: 50vh; background-position: center center; background-size: cover; background-repeat: no-repeat;">
        <div class="articulo-overlay" style="background: rgba(17,17,17,0.6);"></div>
        <div class="container position-relative text-white d-flex flex-column justify-content-center h-100" style="z-index: 2;">
            <h1 class="display-3 fw-bold text-uppercase mb-3 text-shadow text-center"><?= e($pagina['titulo']) ?></h1>
        </div>
    </section>

    <!-- Contenido Principal -->
    <?php if ($contenido !== ''): ?>
    <section class="container py-5 my-4" style="margin-top: -60px; position: relative; z-index: 3;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="bg-white p-4 p-md-5 rounded-4 shadow border-top border-4" style="border-top-color: var(--verde-principal) !important;">
                    <div class="fs-5 text-secondary" style="line-height: 1.9; color: #4a4a4a;">
                        <?= nl2br(e($contenido)) ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Galería de Imágenes Inferior (Mosaico) -->
    <?php if (count($galeria) > 0): ?>
    <section class="container py-5">
        <h4 class="fw-bold mb-4 text-uppercase text-center" style="color: var(--verde-principal); letter-spacing: 1px;">Galería</h4>
        <div class="row g-2 justify-content-center">
            <?php foreach($galeria as $img): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="overflow-hidden rounded shadow-sm h-100">
                    <img src="<?= e($img) ?>" class="img-fluid w-100 transition-scale object-fit-cover" alt="Galería Informativa" style="height: 250px; cursor: zoom-in;" data-bs-toggle="modal" data-bs-target="#imageLightboxModal" data-img-url="<?= e($img) ?>">
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