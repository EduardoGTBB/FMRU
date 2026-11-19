<?php
// comunicado.php (Pública)
require_once 'admin/config/db.php';

// 1. Sanitización estricta del ID (OWASP)
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("HTTP/1.0 404 Not Found");
    die("<div class='container mt-5 text-center'><h2>Nota de prensa no encontrada.</h2></div>");
}

// 2. Consulta segura PDO
$stmt = $pdo->prepare("SELECT * FROM fmru_noticias WHERE id = ? AND activo = 1 AND tipo = 'comunicado'");
$stmt->execute([$id]);
$noticia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$noticia) {
    header("HTTP/1.0 404 Not Found");
    die("<div class='container mt-5 text-center'><h2>La nota de prensa no existe o fue retirada.</h2></div>");
}

function fechaEsp($fecha) {
    $meses = ['01'=>'enero', '02'=>'febrero', '03'=>'marzo', '04'=>'abril', '05'=>'mayo', '06'=>'junio', '07'=>'julio', '08'=>'agosto', '09'=>'septiembre', '10'=>'octubre', '11'=>'noviembre', '12'=>'diciembre'];
    $timestamp = strtotime($fecha);
    return ltrim(date('d', $timestamp), '0') . " de " . $meses[date('m', $timestamp)] . " de " . date('Y', $timestamp);
}

$page_title = "Nota de Prensa: " . e($noticia['titulo']);

include 'includes/head.php';
include 'includes/navbar.php';
?>

<!-- Añadimos padding-bottom gigante (pb-5 mb-5) para separar del Footer -->
<main class="prensa-container mb-5 pb-5" style="background-color: #f4f6f9; padding: 60px 0;">
    <div class="container mb-5 pb-4">
        <article class="prensa-box bg-white rounded-3 shadow-sm mx-auto" style="border-top: 6px solid var(--rojo-institucional); padding: 50px; max-width: 900px;">
            <!-- Membrete -->
            <div class="text-center mb-5">
                <img src="assets/img/logo/rugby-mexico-logo.png" alt="FMRU Logo" style="height: 75px;">
                <!-- Texto cambiado a Nota de Prensa -->
                <h6 class="mt-3 fw-bold text-danger text-uppercase" style="letter-spacing: 2.5px;">Nota de Prensa Oficial</h6>
            </div>

            <!-- Título y Meta -->
            <h2 class="fw-bold text-dark mb-4 text-center lh-base px-md-4"><?= e($noticia['titulo']) ?></h2>
            
            <div class="prensa-meta d-flex flex-column flex-md-row justify-content-between align-items-md-center text-muted" style="border-bottom: 2px solid #e9ecef; padding-bottom: 20px; margin-bottom: 30px; font-family: monospace; font-size: 0.95rem;">
                <!-- Cambiado de COM- a NP- -->
                <span class="mb-2 mb-md-0"><strong>REF:</strong> NP-FMRU-<?= str_pad(e($noticia['id']), 4, '0', STR_PAD_LEFT) ?></span>
                <span><strong>FECHA:</strong> <?= fechaEsp($noticia['fecha_publicacion']) ?></span>
            </div>

            <!-- Cuerpo Oficial -->
            <div class="articulo-contenido" style="text-align: justify; font-size: 1.1rem; line-height: 1.8; color: #333;">
                <p class="fw-bold text-dark">A toda la comunidad del Rugby Mexicano, medios de comunicación y público en general:</p>
                
                <p>Por este medio, la Federación Mexicana de Rugby, A.C. hace de su conocimiento lo siguiente:</p>
                
                <?php if(!empty($noticia['extracto'])): ?>
                    <p><?= nl2br(e($noticia['extracto'])) ?></p>
                <?php endif; ?>
                
                <p class="mt-5 pt-3">Atentamente,<br><strong class="text-dark">Área de Comunicación Institucional</strong><br>Federación Mexicana de Rugby, A.C.</p>
            </div>
            
            <!-- Herramientas (Con margen top amplio) -->
            <div class="text-center mt-5 pt-5 border-top d-print-none">
                <button onclick="window.print();" class="btn btn-outline-dark fw-bold me-2 px-4 shadow-sm"><i class="bi bi-printer me-2"></i>Imprimir Nota</button>
                <a href="index.php" class="btn btn-dark fw-bold px-4 shadow-sm"><i class="bi bi-house me-2"></i>Volver al Inicio</a>
            </div>
        </article>
    </div>
</main>

<!-- Estilo exclusivo para impresión física -->
<style>
@media print {
    body { background-color: white !important; }
    .prensa-container { padding: 0 !important; background-color: white !important; margin: 0 !important; }
    .prensa-box { box-shadow: none !important; border: none !important; padding: 0 !important; max-width: 100% !important; }
    header, nav, footer, .d-print-none { display: none !important; }
}
</style>

<?php include 'includes/footer.php'; ?>