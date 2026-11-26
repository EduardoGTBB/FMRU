<?php
// admin/sponsor_form.php
require_once __DIR__ . '/config/seguridad.php';
exigir_sesion_admin();
require_once __DIR__ . '/config/db.php';
global $db;

$id = isset($_GET['id']) ? max(0, (int)$_GET['id']) : 0;
$accion = $id ? 'Editar' : 'Agregar';
$custom_title = "$accion Sponsor";

$mensaje = '';
$tipo_mensaje = '';

$niveles_permitidos = ['Patrocinador Principal', 'Socio Técnico', 'Proveedor Oficial'];
$nombre = '';
$enlace = '#';
$nivel = 'Proveedor Oficial';
$activo = 1;
$imagen_url_actual = '';

if (!$id) {
    $max_val = $db->getMaxOrdenSponsor();
    $orden = $max_val ? (int)$max_val + 1 : 1;
} else {
    $sponsor = $db->getSponsorById($id);
    if ($sponsor) {
        $nombre            = $sponsor['nombre'];
        $enlace            = $sponsor['enlace'];
        $nivel             = in_array($sponsor['nivel'] ?? '', $niveles_permitidos, true) ? $sponsor['nivel'] : 'Proveedor Oficial';
        $orden             = (int)$sponsor['orden'];
        $activo            = (int)$sponsor['activo'];
        $imagen_url_actual = $sponsor['imagen_url'];
    } else {
        header('Location: ver_sponsors.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_post = recibir_post('nombre');
    $enlace_raw  = trim((string)($_POST['enlace'] ?? '#'));
    $nivel_raw   = recibir_post('nivel');
    $nivel_post  = in_array($nivel_raw, $niveles_permitidos, true) ? $nivel_raw : 'Proveedor Oficial';
    $rotativo_post = ($nivel_post === 'Patrocinador Principal') ? 0 : 1;
    $orden_post  = max(1, (int)($_POST['orden'] ?? 1));
    $activo_post = isset($_POST['activo']) ? 1 : 0;
    $imagen_final = $imagen_url_actual;

    // OWASP: Validar enlace externo permitiendo únicamente '#' o esquemas http/https
    if ($enlace_raw === '' || $enlace_raw === '#' || $enlace_raw === 'http://' || $enlace_raw === 'https://') {
        $enlace_post = '#';
    } else {
        $esquema = strtolower((string)parse_url($enlace_raw, PHP_URL_SCHEME));
        if (filter_var($enlace_raw, FILTER_VALIDATE_URL) && in_array($esquema, ['https', 'http'], true)) {
            $enlace_post = $enlace_raw;
        } else {
            $enlace_post = $enlace_raw;
            $mensaje = 'El enlace web debe iniciar con https:// o http://, o dejarse con "#".';
            $tipo_mensaje = 'danger';
        }
    }

    if (!validar_csrf()) {
        $mensaje = 'Token de seguridad inválido. Intenta guardar nuevamente.';
        $tipo_mensaje = 'danger';
    } elseif ($nombre_post === '') {
        $mensaje = 'El nombre de la empresa es obligatorio.';
        $tipo_mensaje = 'danger';
    }

    if ($mensaje === '') {
        try {
            // El 4to parámetro en true habilita SVG con escaneo anti-XSS de seguridad.php
            $nuevo_logo = procesar_imagen_segura('imagen', 'assets/img/sponsors', 'sponsor', true);
            if ($nuevo_logo !== null) {
                $imagen_final = $nuevo_logo;
            } elseif (!$id) {
                throw new RuntimeException('Debes subir un logotipo para el nuevo sponsor.');
            }

            $db->beginTransaction();

            if ($id) {
                if ($orden !== $orden_post) {
                    $id_conflicto = $db->getSponsorIdByOrden($orden_post, $id);
                    if ($id_conflicto) {
                        $db->updateSponsorOrden($id_conflicto, $orden);
                    }
                }
                $db->updateSponsor($id, $nombre_post, $enlace_post, $nivel_post, $rotativo_post, $imagen_final, $orden_post, $activo_post);
                $mensaje = 'Sponsor actualizado correctamente.';
                $imagen_url_actual = $imagen_final;
            } else {
                if ($db->getSponsorIdByOrden($orden_post)) {
                    $db->shiftSponsorsOrden($orden_post);
                }
                $db->insertSponsor($nombre_post, $enlace_post, $nivel_post, $rotativo_post, $imagen_final, $orden_post, $activo_post);
                $mensaje = 'Sponsor creado exitosamente.';

                $nombre_post = '';
                $enlace_post = '#';
                $nivel_post = 'Proveedor Oficial';
                $activo_post = 1;
                $imagen_url_actual = '';
                $nuevoMax = $db->getMaxOrdenSponsor();
                $orden_post = $nuevoMax ? (int)$nuevoMax + 1 : 1;
            }

            $db->commit();
            $tipo_mensaje = 'success';
            $nombre = $nombre_post;
            $enlace = $enlace_post;
            $nivel  = $nivel_post;
            $orden  = $orden_post;
            $activo = $activo_post;

        } catch (RuntimeException $e) {
            $db->rollBack();
            $mensaje = $e->getMessage();
            $tipo_mensaje = 'danger';
        } catch (Throwable $e) {
            $db->rollBack();
            $mensaje = 'Error en base de datos al guardar el sponsor.';
            $tipo_mensaje = 'danger';
        }
    }

    if ($tipo_mensaje === 'danger') {
        $nombre = $nombre_post;
        $enlace = $enlace_post;
        $nivel  = $nivel_post;
        $orden  = $orden_post;
        $activo = $activo_post;
    }
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
                <a href="ver_sponsors.php" class="btn btn-light border btn-sm shadow-sm">
                    <i class="bi bi-arrow-left"></i> Volver al listado
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
                        <form method="POST" action="" enctype="multipart/form-data">
                            <?= csrf_campo() ?>

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label fw-bold text-dark">Nombre de la Empresa</label>
                                    <input type="text" name="nombre" class="form-control" value="<?= e($nombre) ?>" required maxlength="100">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Enlace Web (URL)</label>
                                    <input type="text" name="enlace" class="form-control" value="<?= e($enlace) ?>" maxlength="255" placeholder="https://www.ejemplo.com">
                                    <div class="form-text">Si no tiene sitio, déjalo vacío o con "#"</div>
                                </div>
                            </div>

                            <div class="row mb-4 bg-light p-3 rounded border mx-0 align-items-center">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark mb-1">Nivel del Patrocinador</label>
                                    <select name="nivel" class="form-select">
                                        <option value="Patrocinador Principal" <?= $nivel === 'Patrocinador Principal' ? 'selected' : '' ?>>Patrocinador Principal (No rota)</option>
                                        <option value="Socio Técnico" <?= $nivel === 'Socio Técnico' ? 'selected' : '' ?>>Socio Técnico (Fila 1)</option>
                                        <option value="Proveedor Oficial" <?= $nivel === 'Proveedor Oficial' ? 'selected' : '' ?>>Proveedor Oficial (Fila 2)</option>
                                    </select>
                                </div>
                                <div class="col-md-6 text-muted small mt-2 mt-md-0">
                                    <i class="bi bi-info-circle-fill me-1"></i> La posición de la imagen y su animación (rotativa o estática) se asignarán automáticamente según el nivel seleccionado.
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label fw-bold text-dark">Orden de Aparición</label>
                                    <input type="number" name="orden" class="form-control" value="<?= (int)$orden ?>" min="1" required>
                                    <div class="form-text">Calculado automáticamente al agregar nuevos.</div>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="activo" id="activoSwitch" <?= $activo ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-bold text-dark" for="activoSwitch">Publicar</label>
                                    </div>
                                </div>
                            </div>

                            <hr class="text-muted">

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">Logo del Patrocinador</label>
                                <?php if ($imagen_url_actual): ?>
                                    <div class="mb-3 text-center bg-light p-3 rounded border">
                                        <img src="../<?= e($imagen_url_actual) ?>" class="current-image-sponsor shadow-sm" alt="Logo Sponsor">
                                    </div>
                                <?php endif; ?>
                                <input class="form-control" type="file" name="imagen" id="imagenFile" accept="image/jpeg, image/png, image/webp, image/svg+xml" <?= !$id ? 'required' : '' ?>>
                                <div class="form-text"><span class="text-danger fw-bold"><?= $id ? 'Opcional.' : 'Requerido.' ?></span> Formatos: PNG, JPG, WEBP, SVG. Max: 2MB.</div>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm" style="background-color: #116436; border-color: #116436;">
                                    <i class="bi bi-save me-2"></i> Guardar Sponsor
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