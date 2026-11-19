<?php
// articulo.php (Pública)
require_once 'admin/config/db.php';

// 1. Sanitización estricta del ID (OWASP)
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("HTTP/1.0 404 Not Found");
    die("<div class='container mt-5 text-center'><h2>Artículo no encontrado.</h2></div>");
}

// 2. Consulta segura PDO 
/*$stmt = $pdo->prepare("SELECT * FROM fmru_noticias WHERE id = ? AND activo = 1 AND tipo IN ('principal', 'secundaria')");*/
$stmt = $pdo->prepare("SELECT * FROM fmru_noticias WHERE id = ? AND activo = 1 AND tipo != 'comunicado'");
$stmt->execute([$id]);
$noticia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$noticia) {
    header("HTTP/1.0 404 Not Found");
    die("<div class='container mt-5 text-center'><h2>El artículo no existe o fue retirado.</h2></div>");
}

// Función auxiliar para fechas
function fechaEsp($fecha) {
    $meses = ['01'=>'enero', '02'=>'febrero', '03'=>'marzo', '04'=>'abril', '05'=>'mayo', '06'=>'junio', '07'=>'julio', '08'=>'agosto', '09'=>'septiembre', '10'=>'octubre', '11'=>'noviembre', '12'=>'diciembre'];
    $timestamp = strtotime($fecha);
    return ltrim(date('d', $timestamp), '0') . " de " . $meses[date('m', $timestamp)] . " de " . date('Y', $timestamp);
}

$page_title = e($noticia['titulo']);

// Incluir cabeceras de la web pública
include 'includes/head.php';
include 'includes/navbar.php';
?>

<main style="background-color: #f8f9fa;">
    <!-- Cabecera Inmersiva (Hero) -->
    <header class="articulo-header" style="background-image: url('<?= e($noticia['imagen_url']) ?>'); padding: 160px 0 140px; position: relative; background-size: cover; background-position: center;">
        <div class="articulo-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to bottom, rgba(17,17,17,0.4) 0%, rgba(17,17,17,0.85) 100%);"></div>
        
        <div class="container position-relative" style="z-index: 2;">
            <div class="row justify-content-center text-center">
                <div class="col-lg-9">
                    <span class="badge bg-success mb-3 px-3 py-2 text-uppercase" style="letter-spacing: 1.5px; font-size: 0.85rem;">
                        <?= e($noticia['categoria']) ?>
                    </span>
                    <h1 class="display-4 fw-bold mb-4 text-white lh-sm"><?= e($noticia['titulo']) ?></h1>
                    
                    <div class="d-flex justify-content-center align-items-center gap-4 text-white fw-semibold">
                        <span><i class="bi bi-calendar-event me-2 text-danger"></i><?= fechaEsp($noticia['fecha_publicacion']) ?></span>
                        <?php if(!empty($noticia['tiempo_lectura'])): ?>
                            <span><i class="bi bi-clock me-2 text-danger"></i>Lectura: <?= e($noticia['tiempo_lectura']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Cuerpo Principal de la Noticia -->
    <div class="container mb-5 pb-5" style="margin-top: -80px; position: relative; z-index: 3;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <article class="articulo-contenido bg-white p-4 p-md-5 rounded-4 shadow border-top border-4" style="border-top-color: var(--rojo-institucional) !important;">
                    
                    <!-- Extracto / Entradilla -->
                    <?php if(!empty($noticia['extracto'])): ?>
                        <p class="lead fw-bold text-dark mb-5 border-start border-4 border-danger ps-4" style="font-size: 1.25rem;">
                            <?= nl2br(e($noticia['extracto'])) ?>
                        </p>
                    <?php endif; ?>
                    
                    <!-- TEXTO COMPLETO DINÁMICO (Con saltos de línea preservados) -->
                    <div class="fs-5 text-secondary" style="line-height: 1.8;">
                        <?php if(!empty($noticia['contenido'])): ?>
                            <?= nl2br(e($noticia['contenido'])) ?>
                        <?php else: ?>
                            <!-- Fallback si es una noticia antigua que aún no tiene cuerpo -->
                            <p>El cuerpo completo de esta noticia aún no ha sido redactado.</p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- GALERÍA DINÁMICA DE IMÁGENES -->
                    <?php 
                    $galeria = [];
                    if (!empty($noticia['imagenes_adicionales'])) {
                        $galeria = json_decode($noticia['imagenes_adicionales'], true) ?: [];
                    }
                    
                    if(!empty($galeria)): 
                    ?>
                    <div class="mt-5 pt-4">
                        <h4 class="fw-bold mb-4" style="color: var(--verde-principal);">Galería de Imágenes</h4>
                        <div class="row g-3">
                            <?php foreach($galeria as $img): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="overflow-hidden rounded shadow-sm h-100">
                                    <!-- AÑADIDOS LOS ATRIBUTOS MODAL DE BOOTSTRAP 5 Y CURSOR POINTER -->
                                    <img src="<?= e($img) ?>" class="img-fluid w-100 transition-scale object-fit-cover" alt="Imagen adicional" style="height: 220px; cursor: zoom-in;" data-bs-toggle="modal" data-bs-target="#imageLightboxModal" data-img-url="<?= e($img) ?>">
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <!-- FIN GALERÍA -->

                    <!-- Controles -->
                    <div class="text-center mt-5 pt-5 border-top d-print-none">
                        <a href="noticias.php" class="btn btn-outline-dark fw-bold px-4 py-2 shadow-sm"><i class="bi bi-arrow-left me-2"></i> Volver a Noticias</a>
                    </div>
                </article>
            </div>
        </div>
    </div>
</main>

<!-- MODAL  -->
<div class="modal fade" id="imageLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0">
            <!-- Header con botón corregido -->
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
            // Escuchar el evento nativo de Bootstrap justo antes de que el modal se muestre
            lightboxModal.addEventListener('show.bs.modal', function (event) {
                // Elemento que disparó el evento (la imagen en miniatura a la que le dimos clic)
                const triggerImage = event.relatedTarget;
                
                // Extraer la ruta de la imagen desde el atributo seguro data-img-url
                const imageUrl = triggerImage.getAttribute('data-img-url');
                
                // Inyectar la ruta en la imagen gigante del modal
                document.getElementById('lightboxImage').src = imageUrl;
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>