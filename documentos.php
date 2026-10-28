<?php
/**
 * FEDERACIÓN MEXICANA DE RUGBY - DOCUMENTOS OFICIALES
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

// Datos de los documentos (Basados en la estructura de OneDrive Temporada 26-27)
$documentos = [
    [
        "categoria" => "Reglamentos",
        "nombre" => "Reglamento Deportivo FMRU 2026 1.1",
        "fecha" => "7 de octubre, 2026",
        "peso" => "1.84 MB",
        "archivo" => "assets/docs/Reglamento_Deportivo_FMRU_2026_1.1.pdf"
    ],
    [
        "categoria" => "Antidopaje",
        "nombre" => "FMR Regulación 21 Antidopaje WADA 2027",
        "fecha" => "24 de septiembre, 2026",
        "peso" => "184 KB",
        "archivo" => "assets/docs/FMR_Regulacion_21_Antidopaje_WADA_2027.pdf"
    ],
    [
        "categoria" => "Anexos Técnicos",
        "nombre" => "Anexo Técnico Xs GAME ON Varonil y Femenil Mayor 2026",
        "fecha" => "31 de agosto, 2026",
        "peso" => "1.33 MB",
        "archivo" => "assets/docs/Anexo_Tecnico_Xs_GAME_ON_2026.pdf"
    ],
    [
        "categoria" => "Anexos Técnicos",
        "nombre" => "Anexo Técnico Xvs Varonil Mayor 2026-2027",
        "fecha" => "31 de agosto, 2026",
        "peso" => "1.41 MB",
        "archivo" => "assets/docs/Anexo_Tecnico_Xvs_Varonil_2026_2027.pdf"
    ],
    [
        "categoria" => "Comunicados",
        "nombre" => "Comunicados y Adendums Temporada 26-27 (Carpeta ZIP)",
        "fecha" => "24 de septiembre, 2026",
        "peso" => "Varios",
        "archivo" => "assets/docs/Comunicados_Adendums.zip"
    ]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos Oficiales | Federación Mexicana de Rugby</title>
    
    <!-- Tipografía Oficial: Open Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Estilos globales homologados -->
    <link rel="stylesheet" href="assets/css/styles.css">

    <style>
        /* Estilos específicos para la vista de documentos */
        .hero-banner-docs {
            background-color: var(--verde-oscuro);
            background-image: linear-gradient(rgba(0, 67, 35, 0.85), rgba(0, 67, 35, 0.85)), url('assets/img/banner/Mesa-de-trabajo-25.png');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 70px 0;
            text-align: center;
        }

        .docs-table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            overflow: hidden;
            border-top: 4px solid var(--rojo-institucional);
        }

        .table-docs th {
            background-color: var(--gris-fondo);
            color: var(--verde-principal);
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 18px 15px;
            border-bottom: 2px solid #dee2e6;
        }

        .table-docs td {
            vertical-align: middle;
            padding: 15px;
            color: #495057;
            border-bottom: 1px solid #f1f3f5;
        }

        .table-docs tr:hover td {
            background-color: #f8f9fa;
        }

        .doc-icon {
            font-size: 1.5rem;
            color: var(--rojo-institucional);
        }

        .doc-title {
            font-weight: 600;
            color: var(--azul-oscuro);
            margin-bottom: 0;
            font-size: 1.05rem;
        }

        .doc-category {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 800;
            color: var(--verde-claro);
            letter-spacing: 0.5px;
        }

        .btn-descargar {
            background-color: white;
            color: var(--rojo-institucional);
            border: 2px solid var(--rojo-institucional);
            font-weight: 600;
            padding: 6px 15px;
            border-radius: 4px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-descargar:hover {
            background-color: var(--rojo-institucional);
            color: white;
        }
    </style>
</head>
<body>

    <!-- NAVBAR MODULAR -->
    <?php include 'includes/navbar.php'; ?>

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
                <p class="text-muted">Descarga reglamentos, anexos y comunicados oficiales.</p>
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
                                    <i class="bi bi-file-earmark-pdf-fill doc-icon"></i>
                                </td>
                                <td>
                                    <div class="doc-category"><?= e($doc['categoria']) ?></div>
                                    <h5 class="doc-title"><?= e($doc['nombre']) ?></h5>
                                </td>
                                <td><?= e($doc['fecha']) ?></td>
                                <td><?= e($doc['peso']) ?></td>
                                <td class="text-end">
                                    <!-- Atributo download OWASP Safe para forzar descarga segura en lugar de ejecución -->
                                    <a href="<?= e($doc['archivo']) ?>" class="btn-descargar" download>
                                        <i class="bi bi-download"></i> Descargar
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <!-- Mensaje de no resultados (oculto por defecto) -->
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
    
    <!-- Script Vanilla JS para el buscador de documentos en tiempo real -->
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

                    // Mostrar mensaje si no hay resultados
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