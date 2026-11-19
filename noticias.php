<?php
/**
 * noticias.php (Página pública del Blog)
 * Implementa paginación segura - OWASP
 */
require_once 'admin/config/db.php';

// 1. Configuración de Paginación Segura
$noticias_por_pagina = 6;

// Validar que la página sea un entero positivo. Si falla o está vacío, por defecto es 1.
$pagina_actual = filter_input(INPUT_GET, 'p', FILTER_VALIDATE_INT);
if (!$pagina_actual || $pagina_actual < 1) {
    $pagina_actual = 1;
}

$offset = ($pagina_actual - 1) * $noticias_por_pagina;

try {
    // 2. Obtener el total de noticias activas
    $stmtTotal = $pdo->query("SELECT COUNT(*) FROM fmru_noticias WHERE activo = 1 AND tipo != 'comunicado'");
    $total_noticias = $stmtTotal->fetchColumn();
    $total_paginas = ceil($total_noticias / $noticias_por_pagina);

    // 3. Consulta PDO protegida contra SQLi en LIMIT y OFFSET
    $sql = "SELECT * FROM fmru_noticias WHERE activo = 1 AND tipo != 'comunicado' ORDER BY fecha_publicacion DESC, id DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $noticias_por_pagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("<div class='container mt-5 text-center'><h2>Error al cargar el blog. Intente más tarde.</h2></div>");
}

function fechaEsp($fecha) {
    $meses = ['01'=>'enero', '02'=>'febrero', '03'=>'marzo', '04'=>'abril', '05'=>'mayo', '06'=>'junio', '07'=>'julio', '08'=>'agosto', '09'=>'septiembre', '10'=>'octubre', '11'=>'noviembre', '12'=>'diciembre'];
    $timestamp = strtotime($fecha);
    return ltrim(date('d', $timestamp), '0') . " de " . $meses[date('m', $timestamp)] . ", " . date('Y', $timestamp);
}

$page_title = "Noticias";
include 'includes/head.php';
include 'includes/navbar.php';
?>

<main style="background-color: #f8f9fa;">
    <!-- Cabecera de la Sección -->
    <header class="py-5 bg-dark text-white text-center" style="background-image: linear-gradient(rgba(17,100,54,0.85), rgba(17,100,54,0.85)), url('assets/img/banner/Mesa-de-trabajo-25.png'); background-size: cover; background-position: center;">
        <div class="container py-4">
            <h1 class="display-4 fw-bold text-uppercase" style="letter-spacing: 2px;">Noticias FMRU</h1>
            <p class="lead mb-0">La actualidad del Rugby Mexicano en un solo lugar.</p>
        </div>
    </header>

    <div class="container py-5">
        <?php if (empty($noticias)): ?>
            <div class="text-center py-5">
                <i class="bi bi-newspaper display-1 text-muted mb-3"></i>
                <h3 class="text-muted">No hay noticias publicadas.</h3>
            </div>
        <?php else: ?>
            
            <!-- Grid de Noticias -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
                <?php foreach ($noticias as $noticia): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm transition-scale small-article" style="transition: transform 0.2s; cursor:pointer;" onclick="window.location='articulo.php?id=<?= $noticia['id'] ?>'">
                        <div class="overflow-hidden" style="height: 200px;">
                            <img src="<?= e($noticia['imagen_url']) ?>" class="card-img-top w-100 h-100 object-fit-cover" alt="<?= e($noticia['titulo']) ?>">
                        </div>
                        <div class="card-body d-flex flex-column p-4">
                            <span class="text-categoria d-block mb-2"><?= e($noticia['categoria']) ?></span>
                            <h5 class="card-title fw-bold" style="color: var(--rojo-institucional); line-height: 1.3;">
                                <a href="articulo.php?id=<?= $noticia['id'] ?>" class="text-decoration-none text-reset">
                                    <?= e($noticia['titulo']) ?>
                                </a>
                            </h5>
                            <!-- Limitamos el extracto a 100 caracteres para no romper la altura de las tarjetas -->
                            <p class="card-text text-muted small mt-2 mb-4">
                                <?= e(mb_strimwidth($noticia['extracto'], 0, 100, "...")) ?>
                            </p>
                            <span class="text-fecha mt-auto border-top pt-3 d-block">
                                <i class="bi bi-calendar3 me-1"></i> <?= fechaEsp($noticia['fecha_publicacion']) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Controles de Paginación Bootstrap 5 -->
            <?php if ($total_paginas > 1): ?>
            <nav aria-label="Navegación de páginas de noticias">
                <ul class="pagination custom-pagination justify-content-center">
                    
                    <!-- Botón Anterior -->
                    <li class="page-item <?= ($pagina_actual <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?p=<?= $pagina_actual - 1 ?>" <?= ($pagina_actual <= 1) ? 'tabindex="-1" aria-disabled="true"' : '' ?>>
                            <i class="bi bi-chevron-left"></i> Anterior
                        </a>
                    </li>
                    
                    <!-- Números de Página -->
                    <?php for($i = 1; $i <= $total_paginas; $i++): ?>
                        <li class="page-item <?= ($pagina_actual == $i) ? 'active' : '' ?>">
                            <a class="page-link" href="?p=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <!-- Botón Siguiente -->
                    <li class="page-item <?= ($pagina_actual >= $total_paginas) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?p=<?= $pagina_actual + 1 ?>" <?= ($pagina_actual >= $total_paginas) ? 'tabindex="-1" aria-disabled="true"' : '' ?>>
                            Siguiente <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>