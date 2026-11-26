<?php
// admin/ver_documentos.php
require_once __DIR__ . '/config/seguridad.php';
exigir_sesion_admin();
require_once __DIR__ . '/config/db.php';
global $db;

$custom_title = 'Repositorio de Documentos';
$tabs_permitidas = ['lista', 'portada'];
$tab_solicitada = $_POST['tab_origen'] ?? ($_GET['tab'] ?? 'lista');
$tab_activa = in_array($tab_solicitada, $tabs_permitidas, true) ? $tab_solicitada : 'lista';

$mensaje = $_SESSION['flash_mensaje'] ?? '';
$tipo_mensaje = $_SESSION['flash_tipo'] ?? '';
unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validar_csrf()) {
        $_SESSION['flash_mensaje'] = 'Sesión de formulario inválida. Intenta de nuevo.';
        $_SESSION['flash_tipo'] = 'danger';
        header('Location: ver_documentos.php?tab=' . urlencode($tab_activa));
        exit;
    }

    try {
        // ACCIÓN 1: Eliminar documento
        if (isset($_POST['eliminar_id'])) {
            $id_eliminar = (int)$_POST['eliminar_id'];
            $docTarget = $db->getDocumentoById($id_eliminar);

            if ($docTarget && $db->eliminarDocumento($id_eliminar)) {
                if (($docTarget['tipo_origen'] ?? '') === 'archivo' && !empty($docTarget['archivo_path'])) {
                    $rutaFisica = dirname(__DIR__) . '/' . ltrim($docTarget['archivo_path'], '/');
                    if (is_file($rutaFisica)) {
                        unlink($rutaFisica);
                    }
                }
                $_SESSION['flash_mensaje'] = 'Documento eliminado correctamente.';
                $_SESSION['flash_tipo'] = 'success';
            }
        }
        // ACCIÓN 2: Actualizar Banner Hero de Documentos
        elseif (isset($_POST['guardar_portada_docs'])) {
            $db->beginTransaction();
            $db->updateSetting('docs_hero_badge', recibir_post('docs_hero_badge'));
            $db->updateSetting('docs_hero_titulo', recibir_post('docs_hero_titulo'));
            $db->updateSetting('docs_hero_sub', recibir_post('docs_hero_sub'));

            if ($nueva_img = procesar_imagen_segura('docs_hero_img_file', 'assets/img/banner', 'docs')) {
                $db->updateSetting('docs_hero_img', $nueva_img);
            }

            $db->commit();
            $_SESSION['flash_mensaje'] = 'Portada de Documentos actualizada correctamente.';
            $_SESSION['flash_tipo'] = 'success';
        }
    } catch (RuntimeException $e) {
        $db->rollBack();
        $_SESSION['flash_mensaje'] = $e->getMessage();
        $_SESSION['flash_tipo'] = 'danger';
    } catch (Throwable $e) {
        $db->rollBack();
        $_SESSION['flash_mensaje'] = 'Error al procesar la solicitud.';
        $_SESSION['flash_tipo'] = 'danger';
    }

    header('Location: ver_documentos.php?tab=' . urlencode($tab_activa));
    exit;
}

try {
    $documentos = $db->getDocumentosAdmin();
    $settings   = $db->getAllSettings();
} catch (Throwable $e) {
    die('Error al cargar el repositorio de documentos.');
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

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <h3 class="fw-bold mb-0" style="color: #bf2333;">Repositorio Oficial (Docs)</h3>
                <a href="documento_form.php" class="btn btn-success shadow-sm" style="background-color: #116436; border-color: #116436;">
                    <i class="bi bi-plus-lg me-1"></i> Agregar Documento
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <ul class="nav nav-tabs nav-tabs-custom flex-nowrap overflow-x-auto overflow-y-hidden" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link <?= $tab_activa === 'lista' ? 'active' : '' ?> text-nowrap" data-bs-toggle="tab" data-bs-target="#tab_lista" role="tab">
                                <i class="bi bi-files me-2"></i>Listado de Documentos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link <?= $tab_activa === 'portada' ? 'active' : '' ?> text-nowrap" data-bs-toggle="tab" data-bs-target="#tab_portada" role="tab">
                                <i class="bi bi-image me-2"></i>Banner Superior (Hero)
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-0">
                    <div class="tab-content">

                        <!-- PESTAÑA 1: TABLA DE DOCUMENTOS -->
                        <div class="tab-pane fade <?= $tab_activa === 'lista' ? 'show active' : '' ?>" id="tab_lista" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 table-custom">
                                    <thead>
                                        <tr>
                                            <th class="ps-4" style="width: 60px;">Tipo</th>
                                            <th>Documento / Categoría</th>
                                            <th>Modificación</th>
                                            <th>Tamaño</th>
                                            <th>Orden</th>
                                            <th>Estado</th>
                                            <th class="text-center pe-4">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($documentos)): ?>
                                            <tr><td colspan="7" class="text-center py-4 text-muted">No hay documentos registrados en el repositorio.</td></tr>
                                        <?php endif; ?>

                                        <?php foreach ($documentos as $doc): ?>
                                            <?php
                                            $icono_actual = $doc['icono'] ?? 'bi-file-earmark-pdf-fill';
                                            $color_icono  = ($icono_actual === 'bi-folder-fill') ? 'text-warning' : (($icono_actual === 'bi-file-earmark-excel-fill') ? 'text-success' : 'text-danger');
                                            $es_url_segura = preg_match('/^https?:\/\//i', $doc['enlace'] ?? '');
                                            ?>
                                            <tr>
                                                <td class="ps-4 text-center">
                                                    <i class="bi <?= e($icono_actual) ?> fs-4 <?= $color_icono ?>"></i>
                                                </td>
                                                <td>
                                                    <div class="text-uppercase fw-bold" style="font-size: 0.72rem; color: #116436; letter-spacing: 0.5px;">
                                                        <?= e($doc['categoria']) ?>
                                                    </div>
                                                    <div class="fw-bold text-dark"><?= e($doc['nombre']) ?></div>
                                                </td>
                                                <td class="text-muted small text-nowrap"><?= e($doc['fecha_modificacion']) ?></td>
                                                <td><span class="badge bg-light text-secondary border"><?= e($doc['peso']) ?></span></td>
                                                <td><span class="badge bg-light text-dark border fw-bold"><?= (int)$doc['orden'] ?></span></td>
                                                <td>
                                                    <?php if ($doc['activo']): ?>
                                                        <span class="badge bg-success">Activo</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Oculto</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center pe-4 text-nowrap">
                                                    <?php if ($es_url_segura): ?>
                                                        <a href="<?= e($doc['enlace']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary me-1" title="Probar enlace externo">
                                                            <i class="bi bi-box-arrow-up-right"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="documento_form.php?id=<?= (int)$doc['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?= (int)$doc['id'] ?>" data-nombre="<?= e($doc['nombre']) ?>" title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- PESTAÑA 2: CONFIGURACIÓN DEL BANNER HERO -->
                        <div class="tab-pane fade <?= $tab_activa === 'portada' ? 'show active' : '' ?> p-4 p-md-5 bg-light" id="tab_portada" role="tabpanel">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <?= csrf_campo() ?>
                                <input type="hidden" name="tab_origen" value="portada">
                                <input type="hidden" name="guardar_portada_docs" value="1">

                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Banner Superior de Documentos</h5>
                                <div class="row mb-4">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label fw-bold">Etiqueta (Badge)</label>
                                                <input type="text" name="docs_hero_badge" class="form-control" maxlength="50" value="<?= e($settings['docs_hero_badge'] ?? 'TRANSPARENCIA Y NORMATIVIDAD') ?>">
                                            </div>
                                            <div class="col-md-8 mb-3">
                                                <label class="form-label fw-bold">Título del Banner</label>
                                                <input type="text" name="docs_hero_titulo" class="form-control fw-bold" maxlength="150" value="<?= e($settings['docs_hero_titulo'] ?? 'Documentos Oficiales') ?>" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Subtítulo</label>
                                            <input type="text" name="docs_hero_sub" class="form-control" maxlength="255" value="<?= e($settings['docs_hero_sub'] ?? 'Temporada 2026 - 2027') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center bg-white p-3 border rounded shadow-sm">
                                        <label class="form-label fw-bold text-muted small">Imagen de Fondo</label>
                                        <?php if (!empty($settings['docs_hero_img'])): ?>
                                            <div class="mb-2"><img src="../<?= e($settings['docs_hero_img']) ?>" class="img-thumbnail" style="max-height: 100px;" alt="Portada Docs"></div>
                                        <?php endif; ?>
                                        <input type="file" name="docs_hero_img_file" class="form-control form-control-sm" accept="image/jpeg, image/png, image/webp">
                                        <div class="form-text small">JPG, PNG o WEBP (Máx. 2MB).</div>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm" style="background-color: #116436; border-color: #116436; border-radius: 50px;">
                                        <i class="bi bi-save me-2"></i> Guardar Portada
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-muted">
                ¿Estás seguro que deseas eliminar el documento <strong id="modalDocNombre" class="text-dark"></strong> del repositorio público?
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="" data-no-resilience="true">
                    <?= csrf_campo() ?>
                    <input type="hidden" name="tab_origen" value="lista">
                    <input type="hidden" name="eliminar_id" id="modalDocId" value="">
                    <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- <script>if(!window.bootstrap){document.write('<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"><\/script>');}</script> -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                deleteModal.querySelector('#modalDocId').value = button.getAttribute('data-id');
                deleteModal.querySelector('#modalDocNombre').textContent = '"' + button.getAttribute('data-nombre') + '"';
            });
        }
    });
</script>
</body>
</html>