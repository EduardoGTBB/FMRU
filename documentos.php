<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - DOCUMENTOS OFICIALES
 * Arquitectura Segura - OWASP Compliance
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); 
ini_set('session.use_only_cookies', 1);
session_start();

require_once __DIR__ . '/admin/config/db.php';
global $db;

$settings   = $db->getAllSettings();
$documentos = $db->getDocumentosPublicos();

/**
 * Formatea YYYY-MM-DD a formato legible en español (ej. "7 de octubre, 2026")
 */
function formatearFechaDoc($fechaIso) {
    $meses = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
    ];
    $dt = DateTime::createFromFormat('Y-m-d', (string)$fechaIso);
    if (!$dt) {
        return $fechaIso;
    }
    $dia  = (int)$dt->format('j');
    $mes  = $meses[(int)$dt->format('n')] ?? '';
    $anio = $dt->format('Y');
    return "{$dia} de {$mes}, {$anio}";
}

$page_title = "Documentos Oficiales";
include 'includes/head.php';
include 'includes/navbar.php'; 
?>

    <!-- BANNER HERO DINÁMICO (ESTILO GLASSMORPHISM) -->
    <header class="position-relative" style="background-color: var(--verde-oscuro); background-image: url('<?= e($settings['docs_hero_img'] ?? 'assets/img/banner/Mesa-de-trabajo-25.png') ?>'); background-size: cover; background-position: center; min-height: 60vh; display: flex; align-items: center;">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.35);"></div>
        <div class="container position-relative z-2">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="glass-panel text-white shadow-lg">
                        <?php $badge_docs = trim($settings['docs_hero_badge'] ?? 'TRANSPARENCIA Y NORMATIVIDAD'); ?>
                        <?php if ($badge_docs !== ''): ?>
                            <span class="badge bg-success mb-3 px-3 py-2 fw-bold text-uppercase" style="letter-spacing: 2px;">
                                <?= e($badge_docs) ?>
                            </span>
                        <?php endif; ?>
                        <h1 class="display-3 fw-bold text-uppercase mb-3 text-shadow">
                            <?= e($settings['docs_hero_titulo'] ?? 'Documentos Oficiales') ?>
                        </h1>
                        <p class="lead mb-0 fw-semibold opacity-75">
                            <?= e($settings['docs_hero_sub'] ?? 'Temporada 2026 - 2027') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="container my-5">
        
        <div class="row mb-4 align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <h2 class="section-title border-0 pb-0 mb-1">Repositorio FMRU</h2>
                <p class="text-muted mb-0">Accede a reglamentos, anexos y comunicados oficiales alojados en nuestro portal seguro.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="input-group" style="max-width: 350px; margin-left: auto;">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="buscadorDocs" class="form-control border-start-0 ps-0" placeholder="Buscar documento..." maxlength="100">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <!-- Contenedor exterior conserva el diseño; contenedor interior habilita scroll en móviles -->
                <div class="docs-table-container">
                    <div class="table-responsive">
                        <table class="table table-borderless table-docs mb-0 align-middle" id="tablaDocumentos">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 55px;"></th>
                                    <th scope="col" style="min-width: 240px;">Documento</th>
                                    <th scope="col" class="text-nowrap">Modificación</th>
                                    <th scope="col" class="text-nowrap">Tamaño</th>
                                    <th scope="col" class="text-end pe-4">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($documentos)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted fw-semibold">
                                            Por el momento no hay documentos publicados.
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($documentos as $doc): ?>
                                    <?php
                                    $icono_doc = $doc['icono'] ?? 'bi-file-earmark-pdf-fill';
                                    $clase_color_icono = ($icono_doc === 'bi-folder-fill') ? 'text-warning' : '';
                                    
                                    // OWASP: Defensa en profundidad sobre el destino del enlace
                                    $url_destino = '#';
                                    if (($doc['tipo_origen'] ?? 'enlace') === 'archivo' && !empty($doc['archivo_path'])) {
                                        $url_destino = ltrim($doc['archivo_path'], '/');
                                    } elseif (preg_match('/^https?:\/\//i', $doc['enlace'] ?? '')) {
                                        $url_destino = $doc['enlace'];
                                    }
                                    ?>
                                    <tr class="doc-row">
                                        <td class="text-center ps-3">
                                            <i class="bi <?= e($icono_doc) ?> doc-icon <?= $clase_color_icono ?>"></i>
                                        </td>
                                        <td>
                                            <div class="doc-category"><?= e($doc['categoria']) ?></div>
                                            <h5 class="doc-title"><?= e($doc['nombre']) ?></h5>
                                        </td>
                                        <td class="text-nowrap"><?= e(formatearFechaDoc($doc['fecha_modificacion'])) ?></td>
                                        <td class="text-nowrap"><?= e($doc['peso']) ?></td>
                                        <td class="text-end pe-4 text-nowrap">
                                            <a href="<?= e($url_destino) ?>" class="btn-descargar" target="_blank" rel="noopener noreferrer">
                                                Abrir <i class="bi bi-box-arrow-up-right ms-1"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div id="noResultsDocs" class="text-center py-5 d-none">
                        <i class="bi bi-folder-x text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 fw-bold">No se encontraron documentos que coincidan con tu búsqueda.</p>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buscadorDocs = document.getElementById('buscadorDocs');
            const docRows = document.querySelectorAll('.doc-row');
            const noResultsDocs = document.getElementById('noResultsDocs');
            const tablaDocumentos = document.getElementById('tablaDocumentos');

            if (buscadorDocs && docRows.length > 0) {
                buscadorDocs.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    let matchCount = 0;

                    docRows.forEach(row => {
                        const docTitle = row.querySelector('.doc-title').textContent.toLowerCase();
                        const docCategory = row.querySelector('.doc-category').textContent.toLowerCase();
                        
                        if (docTitle.includes(searchTerm) || docCategory.includes(searchTerm)) {
                            row.style.display = '';
                            matchCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    if (matchCount === 0) {
                        noResultsDocs.classList.remove('d-none');
                        tablaDocumentos.querySelector('thead').style.display = 'none';
                    } else {
                        noResultsDocs.classList.add('d-none');
                        tablaDocumentos.querySelector('thead').style.display = '';
                    }
                });
            }
        });
    </script>
</body>
</html>