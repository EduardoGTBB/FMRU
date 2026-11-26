<?php
// admin/gestor_contenidos.php
require_once __DIR__ . '/config/seguridad.php';
exigir_sesion_admin();
require_once __DIR__ . '/config/db.php';
global $db;

$custom_title = 'Gestor de Contenidos';
$tabs_permitidas = ['blog', 'prensa', 'landing'];
$tab_activa = (isset($_GET['tab']) && in_array($_GET['tab'], $tabs_permitidas, true)) ? $_GET['tab'] : 'blog';

$mensaje = $_SESSION['flash_mensaje'] ?? '';
$tipo_mensaje = $_SESSION['flash_tipo'] ?? '';
unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'], $_POST['tipo_contenido'])) {
    $tipo_contenido = (string)$_POST['tipo_contenido'];
    $tab_destino = ($tipo_contenido === 'prensa') ? 'prensa' : (($tipo_contenido === 'landing') ? 'landing' : 'blog');

    if (!validar_csrf()) {
        $_SESSION['flash_mensaje'] = 'Token de seguridad inválido. Intenta nuevamente.';
        $_SESSION['flash_tipo'] = 'danger';
    } else {
        $id_eliminar = (int)$_POST['eliminar_id'];
        try {
            if ($tipo_contenido === 'noticia' || $tipo_contenido === 'prensa') {
                $imgTarget = $db->getImagenNoticia($id_eliminar);
                if ($db->eliminarNoticia($id_eliminar)) {
                    if ($imgTarget) {
                        $rutaFisica = dirname(__DIR__) . '/' . ltrim($imgTarget, '/');
                        if (is_file($rutaFisica)) {
                            unlink($rutaFisica);
                        }
                    }
                    $_SESSION['flash_mensaje'] = 'Publicación eliminada correctamente.';
                    $_SESSION['flash_tipo'] = 'success';
                }
            } elseif ($tipo_contenido === 'landing') {
                if ($db->eliminarLanding($id_eliminar)) {
                    $_SESSION['flash_mensaje'] = 'Landing Page eliminada correctamente.';
                    $_SESSION['flash_tipo'] = 'success';
                }
            }
        } catch (Throwable $e) {
            $_SESSION['flash_mensaje'] = 'Error al eliminar el registro.';
            $_SESSION['flash_tipo'] = 'danger';
        }
    }

    header('Location: gestor_contenidos.php?tab=' . urlencode($tab_destino));
    exit;
}

try {
    $articulos   = $db->getNoticiasAdmin();
    $comunicados = $db->getComunicadosAdmin();
    $landings    = $db->getLandingsAdmin();
} catch (Throwable $e) {
    $articulos = $comunicados = $landings = [];
    $mensaje = 'Error al cargar los listados de contenido.';
    $tipo_mensaje = 'warning';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($custom_title) ?> | Admin FMRU</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>
<body class="admin-panel">
<div class="d-flex">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="flex-grow-1">
        <?php include __DIR__ . '/partials/topbar.php'; ?>

        <div class="container-fluid p-4">
            <?php if ($mensaje !== ''): ?>
                <?php $clase_alerta = in_array($tipo_mensaje, ['success', 'danger', 'warning', 'info'], true) ? $tipo_mensaje : 'info'; ?>
                <div class="alert alert-<?= $clase_alerta ?> alert-dismissible fade show shadow-sm d-flex align-items-center" role="alert">
                    <i class="bi <?= $clase_alerta === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> me-2 fs-5"></i>
                    <div><?= e($mensaje) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>

            <h3 class="fw-bold mb-4" style="color: #bf2333;">Gestor Central de Contenidos</h3>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <ul class="nav nav-tabs nav-tabs-custom flex-nowrap overflow-x-auto overflow-y-hidden" id="contentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= $tab_activa === 'blog' ? 'active' : '' ?> text-nowrap" id="blog-tab" data-bs-toggle="tab" data-bs-target="#blog" type="button" role="tab">
                                <i class="bi bi-file-richtext me-2"></i>Artículos de Blog
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= $tab_activa === 'prensa' ? 'active' : '' ?> text-nowrap" id="prensa-tab" data-bs-toggle="tab" data-bs-target="#prensa" type="button" role="tab">
                                <i class="bi bi-megaphone me-2"></i>Notas de Prensa
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= $tab_activa === 'landing' ? 'active' : '' ?> text-nowrap" id="landing-tab" data-bs-toggle="tab" data-bs-target="#landing" type="button" role="tab">
                                <i class="bi bi-layout-wtf me-2"></i>Landing Pages
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-0">
                    <div class="tab-content" id="contentTabsContent">

                        <!-- PESTAÑA: ARTÍCULOS DE BLOG -->
                        <div class="tab-pane fade <?= $tab_activa === 'blog' ? 'show active' : '' ?> p-4" id="blog" role="tabpanel">
                            <div class="d-flex justify-content-end mb-3">
                                <a href="noticia_form.php?tipo=noticia" class="btn btn-sm btn-success border-0" style="background-color: #116436;"><i class="bi bi-plus-lg me-1"></i> Nuevo Artículo</a>
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
                                        <?php if (empty($articulos)): ?>
                                            <tr><td colspan="5" class="text-center text-muted py-4">No hay artículos publicados.</td></tr>
                                        <?php endif; ?>
                                        <?php foreach ($articulos as $art): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($art['imagen_url'])): ?>
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
                                            <td class="text-nowrap"><?= e($art['fecha_publicacion']) ?></td>
                                            <td class="text-center text-nowrap">
                                                <a href="../articulo.php?id=<?= (int)$art['id'] ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-info me-1" title="Previsualizar"><i class="bi bi-eye"></i></a>
                                                <a href="noticia_form.php?id=<?= (int)$art['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar"><i class="bi bi-pencil"></i></a>
                                                <form method="POST" action="" class="d-inline" data-no-resilience="true" onsubmit="return confirm('¿Seguro que deseas eliminar este artículo?');">
                                                    <?= csrf_campo() ?>
                                                    <input type="hidden" name="eliminar_id" value="<?= (int)$art['id'] ?>">
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
                        <div class="tab-pane fade <?= $tab_activa === 'prensa' ? 'show active' : '' ?> p-4" id="prensa" role="tabpanel">
                            <div class="d-flex justify-content-end mb-3">
                                <a href="noticia_form.php?tipo=comunicado" class="btn btn-sm btn-dark border-0"><i class="bi bi-plus-lg me-1"></i> Nueva Nota de Prensa</a>
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
                                        <?php if (empty($comunicados)): ?>
                                            <tr><td colspan="5" class="text-center text-muted py-4">No hay notas de prensa publicadas.</td></tr>
                                        <?php endif; ?>
                                        <?php foreach ($comunicados as $com): ?>
                                        <tr>
                                            <td><span class="badge bg-light text-dark border">COM-<?= (int)$com['id'] ?></span></td>
                                            <td class="fw-bold"><?= e($com['titulo']) ?></td>
                                            <td><span class="badge badge-prensa">Comunicado Oficial</span></td>
                                            <td class="text-nowrap"><?= e($com['fecha_publicacion']) ?></td>
                                            <td class="text-center text-nowrap">
                                                <a href="../comunicado.php?id=<?= (int)$com['id'] ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-info me-1" title="Previsualizar"><i class="bi bi-eye"></i></a>
                                                <a href="noticia_form.php?id=<?= (int)$com['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar"><i class="bi bi-pencil"></i></a>
                                                <form method="POST" action="" class="d-inline" data-no-resilience="true" onsubmit="return confirm('¿Seguro que deseas eliminar este comunicado?');">
                                                    <?= csrf_campo() ?>
                                                    <input type="hidden" name="eliminar_id" value="<?= (int)$com['id'] ?>">
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
                        <div class="tab-pane fade <?= $tab_activa === 'landing' ? 'show active' : '' ?> p-4" id="landing" role="tabpanel">
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
                                        <?php if (empty($landings)): ?>
                                            <tr><td colspan="5" class="text-center text-muted py-4">No hay landing pages configuradas.</td></tr>
                                        <?php endif; ?>
                                        <?php foreach ($landings as $landing): ?>
                                        <tr>
                                            <td><span class="text-primary fw-semibold">/<?= e($landing['slug']) ?></span></td>
                                            <td class="fw-bold"><?= e($landing['titulo']) ?></td>
                                            <td><span class="badge badge-landing text-capitalize"><i class="bi bi-palette me-1"></i> <?= e($landing['plantilla']) ?></span></td>
                                            <td><span class="badge <?= $landing['activo'] ? 'bg-success' : 'bg-secondary' ?>"><?= $landing['activo'] ? 'Activo' : 'Borrador' ?></span></td>
                                            <td class="text-center text-nowrap">
                                                <a href="../evento.php?slug=<?= e($landing['slug']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-info me-1" title="Previsualizar"><i class="bi bi-eye"></i></a>
                                                <a href="landing_form.php?id=<?= (int)$landing['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar Estructura"><i class="bi bi-pencil"></i></a>
                                                <form method="POST" action="" class="d-inline" data-no-resilience="true" onsubmit="return confirm('¿Seguro que deseas eliminar esta Landing Page?');">
                                                    <?= csrf_campo() ?>
                                                    <input type="hidden" name="eliminar_id" value="<?= (int)$landing['id'] ?>">
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
<!-- <script>if(!window.bootstrap){document.write('<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"><\/script>');}</script> -->
</body>
</html>