<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - DOCUMENTOS OFICIALES
 * Arquitectura Segura - OWASP Compliance
 * Enlaces externos a SharePoint / OneDrive
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); 
ini_set('session.use_only_cookies', 1);
session_start();

function e($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// Datos de los documentos apuntando a SharePoint / OneDrive
$documentos = [
    [
        "categoria" => "Reglamentos",
        "nombre" => "Reglamento Deportivo FMRU 2026 1.1",
        "fecha" => "7 de octubre",
        "peso" => "1.84 MB",
        "enlace" => "https://onedrive.live.com/ejemplo-reglamento" 
    ],
    [
        "categoria" => "Antidopaje",
        "nombre" => "FMR Regulación 21 Antidopaje WADA 2027",
        "fecha" => "24 de septiembre",
        "peso" => "184 KB",
        "enlace" => "https://onedrive.live.com/ejemplo-antidopaje"
    ],
    [
        "categoria" => "Anexos Técnicos",
        "nombre" => "Anexo Técnico Xs GAME ON Varonil y Femenil Mayor 2026",
        "fecha" => "31 de agosto",
        "peso" => "1.33 MB",
        "enlace" => "https://onedrive.live.com/ejemplo-anexo-gameon"
    ],
    [
        "categoria" => "Anexos Técnicos",
        "nombre" => "Anexo Técnico Xvs Varonil Mayor 2026-2027",
        "fecha" => "31 de agosto",
        "peso" => "1.41 MB",
        "enlace" => "https://onedrive.live.com/ejemplo-anexo-xvs"
    ],
    [
        "categoria" => "Comunicados",
        "nombre" => "Comunicados y Adendums (Carpeta Compartida)",
        "fecha" => "24 de septiembre",
        "peso" => "Varios",
        "enlace" => "https://onedrive.live.com/ejemplo-carpeta-comunicados"
    ]
];

// === 1. INCLUIR HEAD Y NAVBAR ===
$page_title = "Documentos Oficiales";
include 'includes/head.php';
include 'includes/navbar.php'; 
?>

    <!-- BANNER HERO -->
    <header class="hero-banner-docs">
        <div class="container">
            <h1 class="display-4 fw-bold text-uppercase">Documentos Oficiales</h1>
            <p class="lead mb-0">Temporada 2026 - 2027</p>
        </div>
    </header>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="container my-5">
        
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h2 class="section-title border-0 pb-0 mb-1">Repositorio FMRU</h2>
                <p class="text-muted">Accede a reglamentos, anexos y comunicados oficiales alojados en nuestro portal seguro.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="input-group" style="max-width: 350px; margin-left: auto;">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="buscadorDocs" class="form-control border-start-0 ps-0" placeholder="Buscar documento...">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="docs-table-container table-responsive">
                    <table class="table table-borderless table-docs mb-0" id="tablaDocumentos">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 50px;"></th>
                                <th scope="col">Documento</th>
                                <th scope="col">Modificación</th>
                                <th scope="col">Tamaño</th>
                                <th scope="col" class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($documentos as $doc): ?>
                            <tr class="doc-row">
                                <td class="text-center">
                                    <?php if($doc['categoria'] == 'Comunicados'): ?>
                                        <i class="bi bi-folder-fill doc-icon text-warning"></i>
                                    <?php else: ?>
                                        <i class="bi bi-file-earmark-pdf-fill doc-icon"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="doc-category"><?= e($doc['categoria']) ?></div>
                                    <h5 class="doc-title"><?= e($doc['nombre']) ?></h5>
                                </td>
                                <td><?= e($doc['fecha']) ?></td>
                                <td><?= e($doc['peso']) ?></td>
                                <td class="text-end">
                                    <!-- Enlace OWASP Safe para recursos externos -->
                                    <a href="<?= e($doc['enlace']) ?>" class="btn-descargar" target="_blank" rel="noopener noreferrer">
                                        Abrir <i class="bi bi-box-arrow-up-right ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div id="noResultsDocs" class="text-center py-5 d-none">
                        <i class="bi bi-folder-x text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 fw-bold">No se encontraron documentos.</p>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- FOOTER MODULAR -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buscadorDocs = document.getElementById('buscadorDocs');
            const docRows = document.querySelectorAll('.doc-row');
            const noResultsDocs = document.getElementById('noResultsDocs');
            const tablaDocumentos = document.getElementById('tablaDocumentos');

            if (buscadorDocs) {
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