<?php
// admin/documento_form.php
require_once __DIR__ . '/config/seguridad.php';
exigir_sesion_admin();
require_once __DIR__ . '/config/db.php';
global $db;

$id = isset($_GET['id']) ? max(0, (int)$_GET['id']) : 0;
$accion = $id ? 'Editar' : 'Agregar';
$custom_title = "$accion Documento";

$mensaje = $_SESSION['flash_mensaje'] ?? '';
$tipo_mensaje = $_SESSION['flash_tipo'] ?? '';
unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);

$iconos_permitidos = [
    'bi-file-earmark-pdf-fill'   => 'Documento PDF (Rojo)',
    'bi-folder-fill'             => 'Carpeta Compartida (Amarillo)',
    'bi-file-earmark-word-fill'  => 'Documento Word',
    'bi-file-earmark-excel-fill' => 'Hoja de Cálculo Excel',
    'bi-link-45deg'              => 'Enlace Externo General'
];

$categoria = '';
$nombre = '';
$fecha_modificacion = date('Y-m-d');
$peso = '';
$icono = 'bi-file-earmark-pdf-fill';
$enlace = '';
$activo = 1;

if (!$id) {
    $maxOrden = $db->getMaxOrdenDocumento();
    $orden = $maxOrden ? (int)$maxOrden + 1 : 1;
} else {
    $doc = $db->getDocumentoById($id);
    if ($doc) {
        $categoria          = $doc['categoria'];
        $nombre             = $doc['nombre'];
        $fecha_modificacion = $doc['fecha_modificacion'];
        $peso               = $doc['peso'];
        $icono              = array_key_exists($doc['icono'], $iconos_permitidos) ? $doc['icono'] : 'bi-file-earmark-pdf-fill';
        $enlace             = $doc['enlace'];
        $orden              = (int)$doc['orden'];
        $activo             = (int)$doc['activo'];
    } else {
        header('Location: ver_documentos.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoria_post = recibir_post('categoria');
    $nombre_post    = recibir_post('nombre');
    $fecha_post     = recibir_post('fecha_modificacion');
    $peso_post      = recibir_post('peso') ?: 'Enlace';
    $icono_raw      = trim((string)($_POST['icono'] ?? ''));
    $icono_post     = array_key_exists($icono_raw, $iconos_permitidos) ? $icono_raw : 'bi-file-earmark-pdf-fill';
    $enlace_post    = trim((string)($_POST['enlace'] ?? ''));
    $orden_post     = max(1, (int)($_POST['orden'] ?? 1));
    $activo_post    = isset($_POST['activo']) ? 1 : 0;

    $es_url_valida = filter_var($enlace_post, FILTER_VALIDATE_URL) !== false;
    $esquema_url   = strtolower((string)parse_url($enlace_post, PHP_URL_SCHEME));
    $fecha_valida  = DateTime::createFromFormat('Y-m-d', $fecha_post) !== false;

    if (!validar_csrf()) {
        $mensaje = 'Token de seguridad inválido. Intenta nuevamente.';
        $tipo_mensaje = 'danger';
    } elseif ($categoria_post === '' || $nombre_post === '') {
        $mensaje = 'La categoría y el nombre del documento son obligatorios.';
        $tipo_mensaje = 'danger';
    } elseif (!$es_url_valida || !in_array($esquema_url, ['https', 'http'], true)) {
        $mensaje = 'Ingresa un enlace válido que inicie con https:// o http:// (ej. OneDrive o SharePoint).';
        $tipo_mensaje = 'danger';
    } elseif (!$fecha_valida) {
        $mensaje = 'La fecha de modificación no tiene un formato válido.';
        $tipo_mensaje = 'danger';
    } else {
        try {
            $db->beginTransaction();

            if ($id) {
                if ($orden !== $orden_post) {
                    $id_conflicto = $db->getDocumentoIdByOrden($orden_post, $id);
                    if ($id_conflicto) {
                        $db->updateDocumentoOrden($id_conflicto, $orden);
                    }
                }
                $db->updateDocumento($id, $categoria_post, $nombre_post, $fecha_post, $peso_post, $icono_post, $enlace_post, $orden_post, $activo_post);
                $db->commit();

                $_SESSION['flash_mensaje'] = 'Documento actualizado exitosamente.';
                $_SESSION['flash_tipo'] = 'success';
                header('Location: documento_form.php?id=' . $id);
                exit;
            } else {
                if ($db->getDocumentoIdByOrden($orden_post)) {
                    $db->shiftDocumentosOrden($orden_post);
                }
                $db->insertDocumento($categoria_post, $nombre_post, $fecha_post, $peso_post, $icono_post, $enlace_post, $orden_post, $activo_post);
                $db->commit();

                $_SESSION['flash_mensaje'] = 'Documento agregado exitosamente al repositorio.';
                $_SESSION['flash_tipo'] = 'success';
                header('Location: ver_documentos.php');
                exit;
            }
        } catch (Throwable $e) {
            $db->rollBack();
            $mensaje = 'Error en base de datos al guardar el documento.';
            $tipo_mensaje = 'danger';
        }
    }

    $categoria          = $categoria_post;
    $nombre             = $nombre_post;
    $fecha_modificacion = $fecha_post;
    $peso               = $peso_post;
    $icono              = $icono_post;
    $enlace             = $enlace_post;
    $orden              = $orden_post;
    $activo             = $activo_post;
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
            <div class="mb-4">
                <a href="ver_documentos.php" class="btn btn-light border btn-sm shadow-sm">
                    <i class="bi bi-arrow-left"></i> Volver al repositorio
                </a>
            </div>

            <?php if ($mensaje !== ''): ?>
                <?php $clase_alerta = in_array($tipo_mensaje, ['success', 'danger', 'warning', 'info'], true) ? $tipo_mensaje : 'info'; ?>
                <div class="alert alert-<?= $clase_alerta ?> alert-dismissible fade show shadow-sm d-flex align-items-center" role="alert">
                    <i class="bi <?= $clase_alerta === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> me-2 fs-5"></i>
                    <div><?= e($mensaje) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="form-card">
                        <form method="POST" action="">
                            <?= csrf_campo() ?>

                            <h5 class="fw-bold border-bottom pb-2 mb-4 text-dark">Datos del Documento</h5>

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label fw-bold text-dark">Categoría</label>
                                    <input type="text" name="categoria" list="listaCategorias" class="form-control" value="<?= e($categoria) ?>" required maxlength="80" placeholder="Selecciona o escribe una categoría">
                                    <datalist id="listaCategorias">
                                        <option value="Reglamentos">
                                        <option value="Antidopaje">
                                        <option value="Anexos Técnicos">
                                        <option value="Comunicados">
                                        <option value="Convocatorias">
                                        <option value="Estatutos">
                                    </datalist>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Ícono Visual</label>
                                    <select name="icono" class="form-select">
                                        <?php foreach ($iconos_permitidos as $clase_ic => $etiqueta_ic): ?>
                                            <option value="<?= e($clase_ic) ?>" <?= $icono === $clase_ic ? 'selected' : '' ?>><?= e($etiqueta_ic) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Nombre Oficial del Documento</label>
                                <input type="text" name="nombre" class="form-control fw-semibold" value="<?= e($nombre) ?>" required maxlength="255" placeholder="Ej. Reglamento Deportivo FMRU 2026 1.1">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">Enlace Externo (SharePoint / OneDrive / Drive)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-link-45deg"></i></span>
                                    <input type="url" name="enlace" class="form-control" value="<?= e($enlace) ?>" required maxlength="500" placeholder="https://onedrive.live.com/...">
                                </div>
                                <div class="form-text">Debe iniciar con <strong>https://</strong>. Se abrirá de forma segura en una pestaña nueva.</div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label class="form-label fw-bold text-dark">Fecha de Modificación</label>
                                    <input type="date" name="fecha_modificacion" class="form-control" value="<?= e($fecha_modificacion) ?>" required>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label class="form-label fw-bold text-dark">Tamaño / Peso</label>
                                    <input type="text" name="peso" class="form-control" value="<?= e($peso) ?>" maxlength="30" placeholder="Ej. 1.84 MB o Varios">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark">Orden de Aparición</label>
                                    <input type="number" name="orden" class="form-control" value="<?= (int)$orden ?>" min="1" required>
                                </div>
                            </div>

                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" name="activo" id="activoSwitch" <?= $activo ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold text-dark" for="activoSwitch">Visible en el portal público (Activo)</label>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm" style="background-color: #116436; border-color: #116436;">
                                    <i class="bi bi-save me-2"></i> <?= $id ? 'Actualizar Documento' : 'Guardar Documento' ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- <script>if(!window.bootstrap){document.write('<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"><\/script>');}</script> -->
</body>
</html>