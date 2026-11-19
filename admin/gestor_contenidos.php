<?php
// admin/gestor_contenidos.php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/config/db.php';
$custom_title = "Gestor de Contenidos";
$mensaje = '';
$tipo_mensaje = '';

try {
    // --- LÓGICA DE ELIMINACIÓN SEGURA (OWASP) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id']) && isset($_POST['tipo_contenido'])) {
        $id_eliminar = (int)$_POST['eliminar_id'];
        $tipo_contenido = $_POST['tipo_contenido'];

        if ($tipo_contenido === 'noticia' || $tipo_contenido === 'prensa') {
            // Eliminar imagen asociada si existe
            $stmtImg = $pdo->prepare("SELECT imagen_url FROM fmru_noticias WHERE id = ?");
            $stmtImg->execute([$id_eliminar]);
            $imgTarget = $stmtImg->fetchColumn();

            $stmtDel = $pdo->prepare("DELETE FROM fmru_noticias WHERE id = ?");
            if ($stmtDel->execute([$id_eliminar])) {
                if ($imgTarget && file_exists("../" . $imgTarget)) {
                    unlink("../" . $imgTarget);
                }
                $mensaje = "Publicación eliminada correctamente.";
                $tipo_mensaje = "success";
            }
        } elseif ($tipo_contenido === 'landing') {
            $stmtDel = $pdo->prepare("DELETE FROM fmru_paginas_landing WHERE id = ?");
            if ($stmtDel->execute([$id_eliminar])) {
                $mensaje = "Landing Page eliminada correctamente.";
                $tipo_mensaje = "success";
            }
        }
    }

    // --- CARGA DE DATOS ---
    // 1. Obtener Artículos de Blog (Noticias Principales y Secundarias)
    // $stmtBlog = $pdo->query("SELECT * FROM fmru_noticias WHERE tipo IN ('principal', 'secundaria') ORDER BY fecha_publicacion DESC");
    $stmtBlog = $pdo->query("SELECT * FROM fmru_noticias WHERE tipo != 'comunicado' ORDER BY fecha_publicacion DESC");
    $articulos = $stmtBlog->fetchAll(PDO::FETCH_ASSOC);

    // 2. Obtener Notas de Prensa (Comunicados)
    $stmtPrensa = $pdo->query("SELECT * FROM fmru_noticias WHERE tipo = 'comunicado' ORDER BY fecha_publicacion DESC");
    $comunicados = $stmtPrensa->fetchAll(PDO::FETCH_ASSOC);

    // 3. Obtener Landing Pages (Torneos)
    $stmtLandings = $pdo->query("SELECT * FROM fmru_paginas_landing ORDER BY id DESC");
    $landings = $stmtLandings->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si la tabla de landings no existe aún, evitamos que se rompa la página
    $landings = [];
    $mensaje = "Aviso: " . e($e->getMessage());
    $tipo_mensaje = "warning";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $custom_title ?> | Admin FMRU</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>
<body class="admin-panel">
<div class="d-flex">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    
    <div class="flex-grow-1">
        <?php include __DIR__ . '/partials/topbar.php'; ?>
        
        <div class="container-fluid p-4">
            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show shadow-sm" role="alert">
                    <?= $mensaje ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <h3 class="fw-bold mb-4" style="color: #bf2333;">Gestor Central de Contenidos</h3>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <ul class="nav nav-tabs nav-tabs-custom" id="contentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="blog-tab" data-bs-toggle="tab" data-bs-target="#blog" type="button" role="tab">
                                <i class="bi bi-file-richtext me-2"></i>Artículos de Blog
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="prensa-tab" data-bs-toggle="tab" data-bs-target="#prensa" type="button" role="tab">
                                <i class="bi bi-megaphone me-2"></i>Notas de Prensa
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="landing-tab" data-bs-toggle="tab" data-bs-target="#landing" type="button" role="tab">
                                <i class="bi bi-layout-wtf me-2"></i>Landing Pages
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body p-0">
                    <div class="tab-content" id="contentTabsContent">
                        
                        <!-- PESTAÑA: ARTÍCULOS DE BLOG -->
                        <div class="tab-pane fade show active p-4" id="blog" role="tabpanel">
                            <div class="d-flex justify-content-end mb-3">
                                <a href="noticia_form.php" class="btn btn-sm btn-success bg-verde-principal border-0"><i class="bi bi-plus-lg me-1"></i> Nuevo Artículo</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle table-custom">
                                    <thead>
                                        <tr>
                                            <th>Portada</th>
                                            <th>Título y Categoría</th>
                                            <th>Formato</th>
                                            <th>Publicación</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($articulos)): ?>
                                            <tr><td colspan="5" class="text-center text-muted py-4">No hay artículos publicados.</td></tr>
                                        <?php endif; ?>
                                        <?php foreach ($articulos as $art): ?>
                                        <tr>
                                            <td>
                                                <?php if($art['imagen_url']): ?>
                                                    <img src="../<?= e($art['imagen_url']) ?>" class="news-preview" alt="Portada">
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Sin imagen</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="fw-bold"><?= e($art['titulo']) ?></div>
                                                <small class="text-muted text-uppercase"><?= e($art['categoria']) ?></small>
                                            </td>
                                            <td><span class="badge badge-blog"><?= e($art['tipo']) ?></span></td>
                                            <td><?= e($art['fecha_publicacion']) ?></td>
                                            <td class="text-center">
                                                <a href="../articulo.php?id=<?= $art['id'] ?>" target="_blank" class="btn btn-sm btn-outline-info me-1" title="Previsualizar"><i class="bi bi-eye"></i></a>
                                                <a href="noticia_form.php?id=<?= $art['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar"><i class="bi bi-pencil"></i></a>
                                                <form method="POST" action="" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este artículo?');">
                                                    <input type="hidden" name="eliminar_id" value="<?= $art['id'] ?>">
                                                    <input type="hidden" name="tipo_contenido" value="noticia">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- PESTAÑA: NOTAS DE PRENSA -->
                        <div class="tab-pane fade p-4" id="prensa" role="tabpanel">
                            <div class="d-flex justify-content-end mb-3">
                                <a href="noticia_form.php" class="btn btn-sm btn-dark border-0"><i class="bi bi-plus-lg me-1"></i> Nueva Nota de Prensa</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle table-custom">
                                    <thead>
                                        <tr>
                                            <th>Referencia</th>
                                            <th>Título Oficial</th>
                                            <th>Tipo</th>
                                            <th>Emisión</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($comunicados)): ?>
                                            <tr><td colspan="5" class="text-center text-muted py-4">No hay notas de prensa publicadas.</td></tr>
                                        <?php endif; ?>
                                        <?php foreach ($comunicados as $com): ?>
                                        <tr>
                                            <td><span class="badge bg-light text-dark border">COM-<?= $com['id'] ?></span></td>
                                            <td class="fw-bold"><?= e($com['titulo']) ?></td>
                                            <td><span class="badge badge-prensa">Comunicado Oficial</span></td>
                                            <td><?= e($com['fecha_publicacion']) ?></td>
                                            <td class="text-center">
                                                <a href="../comunicado.php?id=<?= $com['id'] ?>" target="_blank" class="btn btn-sm btn-outline-info me-1" title="Previsualizar"><i class="bi bi-eye"></i></a>
                                                <a href="noticia_form.php?id=<?= $com['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar"><i class="bi bi-pencil"></i></a>
                                                <form method="POST" action="" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este comunicado?');">
                                                    <input type="hidden" name="eliminar_id" value="<?= $com['id'] ?>">
                                                    <input type="hidden" name="tipo_contenido" value="prensa">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- PESTAÑA: LANDING PAGES -->
                        <div class="tab-pane fade p-4" id="landing" role="tabpanel">
                            <div class="d-flex justify-content-end mb-3">
                                <a href="landing_form.php" class="btn btn-sm btn-info text-dark fw-bold border-0"><i class="bi bi-plus-lg me-1"></i> Crear Landing Page</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle table-custom">
                                    <thead>
                                        <tr>
                                            <th>Ruta (Slug)</th>
                                            <th>Nombre del Evento</th>
                                            <th>Diseño Asignado</th>
                                            <th>Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($landings)): ?>
                                            <tr><td colspan="5" class="text-center text-muted py-4">No hay landing pages configuradas.</td></tr>
                                        <?php endif; ?>
                                        <?php foreach ($landings as $landing): ?>
                                        <tr>
                                            <td><span class="text-primary fw-semibold">/<?= e($landing['slug']) ?></span></td>
                                            <td class="fw-bold"><?= e($landing['titulo']) ?></td>
                                            <td><span class="badge badge-landing text-capitalize"><i class="bi bi-palette me-1"></i> <?= e($landing['plantilla']) ?></span></td>
                                            <td><span class="badge <?= $landing['activo'] ? 'bg-success' : 'bg-secondary' ?>"><?= $landing['activo'] ? 'Activo' : 'Borrador' ?></span></td>
                                            <td class="text-center">
                                                <a href="../evento.php?slug=<?= e($landing['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-info me-1" title="Previsualizar"><i class="bi bi-eye"></i></a>
                                                <a href="landing_form.php?id=<?= $landing['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar Estructura"><i class="bi bi-pencil"></i></a>
                                                <form method="POST" action="" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar esta Landing Page?');">
                                                    <input type="hidden" name="eliminar_id" value="<?= $landing['id'] ?>">
                                                    <input type="hidden" name="tipo_contenido" value="landing">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>