<?php
// admin/noticia_form.php
require_once __DIR__ . '/config/seguridad.php';
exigir_sesion_admin();
require_once __DIR__ . '/config/db.php';
global $db;

$id = isset($_GET['id']) ? max(0, (int)$_GET['id']) : 0;
$accion = $id ? 'Editar' : 'Agregar';
$mensaje = '';
$tipo_mensaje = '';

// OWASP: Lista blanca estricta para el tipo de publicación
$tipos_permitidos = ['noticia', 'comunicado'];
$tipo = (isset($_GET['tipo']) && in_array($_GET['tipo'], $tipos_permitidos, true)) ? $_GET['tipo'] : 'noticia';

$categoria = '';
$titulo = '';
$extracto = '';
$contenido = '';
$fecha_publicacion = date('Y-m-d');
$tiempo_lectura = '';
$activo = 1;
$imagen_url_actual = '';
$imagenes_adicionales_json = '[]';

if ($id > 0) {
    $n = $db->getNoticiaById($id);
    if ($n) {
        $tipo                      = $n['tipo'];
        $categoria                 = $n['categoria'];
        $titulo                    = $n['titulo'];
        $extracto                  = $n['extracto'] ?? '';
        $contenido                 = $n['contenido'] ?? '';
        $fecha_publicacion         = $n['fecha_publicacion'];
        $tiempo_lectura            = $n['tiempo_lectura'] ?? '';
        $activo                    = (int)$n['activo'];
        $imagen_url_actual         = $n['imagen_url'] ?? '';
        $imagenes_adicionales_json = $n['imagenes_adicionales'] ?? '[]';
    } else {
        header('Location: gestor_contenidos.php');
        exit;
    }
}

$custom_title = ($tipo === 'comunicado') ? "$accion Nota de Prensa" : "$accion Publicación";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_recibido  = recibir_post('tipo');
    $tipo_post      = in_array($tipo_recibido, $tipos_permitidos, true) ? $tipo_recibido : 'noticia';
    $categoria_post = recibir_post('categoria');
    $titulo_post    = recibir_post('titulo');
    $extracto_post  = recibir_post('extracto', true);
    $contenido_post = recibir_post('contenido', true);
    $fecha_post     = recibir_post('fecha_publicacion');
    $tiempo_post    = recibir_post('tiempo_lectura');
    $activo_post    = isset($_POST['activo']) ? 1 : 0;

    $imagen_final = $imagen_url_actual;
    $imagenes_extra_actuales = json_decode($imagenes_adicionales_json, true) ?: [];
    $fecha_valida = DateTime::createFromFormat('Y-m-d', $fecha_post) !== false;

    if (!validar_csrf()) {
        $mensaje = 'Token de seguridad inválido. Intenta guardar nuevamente.';
        $tipo_mensaje = 'danger';
    } elseif ($categoria_post === '' || $titulo_post === '') {
        $mensaje = 'La categoría y el título son obligatorios.';
        $tipo_mensaje = 'danger';
    } elseif (!$fecha_valida) {
        $mensaje = 'La fecha de publicación no tiene un formato válido.';
        $tipo_mensaje = 'danger';
    } else {
        $nueva_portada = null;
        try {
            if (isset($_POST['limpiar_galeria']) && $_POST['limpiar_galeria'] === '1') {
                $imagenes_extra_actuales = [];
            }

            if ($tipo_post !== 'comunicado') {
                $nueva_portada = procesar_imagen_segura('imagen', 'assets/img/noticias', 'portada');
                if ($nueva_portada !== null) {
                    $imagen_final = $nueva_portada;
                } elseif (!$id && $imagen_final === '') {
                    throw new RuntimeException('Las noticias requieren una imagen de portada.');
                }
            }

            $nuevas_extra = procesar_galeria_segura('imagenes_adicionales', 'assets/img/noticias');
            if (!empty($nuevas_extra)) {
                $imagenes_extra_actuales = array_merge($imagenes_extra_actuales, $nuevas_extra);
            }

            $imagenes_adicionales_final = json_encode($imagenes_extra_actuales, JSON_UNESCAPED_UNICODE);

            if ($id > 0) {
                $db->updateNoticia($id, $tipo_post, $categoria_post, $titulo_post, $extracto_post, $contenido_post, $fecha_post, $tiempo_post, $imagen_final, $imagenes_adicionales_final, $activo_post);
                $mensaje = 'Publicación actualizada correctamente.';
                $imagen_url_actual = $imagen_final;
                $imagenes_adicionales_json = $imagenes_adicionales_final;
            } else {
                $db->insertNoticia($tipo_post, $categoria_post, $titulo_post, $extracto_post, $contenido_post, $fecha_post, $tiempo_post, $imagen_final, $imagenes_adicionales_final, $activo_post);
                $mensaje = ($tipo_post === 'comunicado') ? 'Nota de prensa creada exitosamente.' : 'Publicación creada exitosamente.';
                // Conservar $tipo_post para no regresar el selector a "Noticia" por accidente
                $categoria_post = '';
                $titulo_post = '';
                $extracto_post = '';
                $contenido_post = '';
                $fecha_post = date('Y-m-d');
                $tiempo_post = '';
                $activo_post = 1;
                $imagen_url_actual = '';
                $imagenes_adicionales_json = '[]';
            }

            $tipo_mensaje = 'success';
            $tipo = $tipo_post;
            $categoria = $categoria_post;
            $titulo = $titulo_post;
            $extracto = $extracto_post;
            $contenido = $contenido_post;
            $fecha_publicacion = $fecha_post;
            $tiempo_lectura = $tiempo_post;
            $activo = $activo_post;
            $custom_title = ($tipo === 'comunicado') ? "$accion Nota de Prensa" : "$accion Publicación";

        } catch (RuntimeException $e) {
            if ($nueva_portada !== null) {
                $rutaPortada = dirname(__DIR__) . '/' . ltrim($nueva_portada, '/');
                if (is_file($rutaPortada)) unlink($rutaPortada);
            }
            $mensaje = $e->getMessage();
            $tipo_mensaje = 'danger';
        } catch (Throwable $e) {
            $mensaje = 'Error interno al guardar la publicación.';
            $tipo_mensaje = 'danger';
        }
    }

    if ($tipo_mensaje === 'danger') {
        $tipo = $tipo_post;
        $categoria = $categoria_post;
        $titulo = $titulo_post;
        $extracto = $extracto_post;
        $contenido = $contenido_post;
        $fecha_publicacion = $fecha_post;
        $tiempo_lectura = $tiempo_post;
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
                <a href="gestor_contenidos.php<?= ($tipo === 'comunicado') ? '?tab=prensa' : '?tab=blog' ?>" id="btnVolverListado" class="btn btn-light border btn-sm shadow-sm">
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
                <div class="col-lg-10">
                    <div class="form-card">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <?= csrf_campo() ?>

                            <h5 class="fw-bold border-bottom pb-2 mb-4">Información Principal</h5>

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label fw-bold">Tipo de Publicación</label>
                                    <select name="tipo" class="form-select" id="tipoSelect">
                                        <option value="noticia" <?= in_array($tipo, ['noticia', 'principal', 'secundaria'], true) ? 'selected' : '' ?>>Noticia</option>
                                        <option value="comunicado" <?= $tipo === 'comunicado' ? 'selected' : '' ?>>Nota de Prensa</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Categoría / Etiqueta</label>
                                    <input type="text" name="categoria" class="form-control" value="<?= e($categoria) ?>" required maxlength="50" placeholder="Ej. SVNS, RWC 2027">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Título</label>
                                <input type="text" name="titulo" class="form-control" value="<?= e($titulo) ?>" required maxlength="255">
                            </div>

                            <div class="mb-4" id="extractoGroup">
                                <label class="form-label fw-bold">Extracto (Resumen corto)</label>
                                <textarea name="extracto" class="form-control editor-texto" rows="2"><?= e($extracto) ?></textarea>
                            </div>

                            <h5 class="fw-bold border-bottom pb-2 mb-4">Cuerpo del Artículo</h5>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted">Contenido Completo</label>
                                <textarea name="contenido" class="form-control editor-texto bg-light" rows="12" placeholder="Escribe o pega aquí el contenido completo."><?= e($contenido) ?></textarea>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label class="form-label fw-bold">Fecha de Publicación</label>
                                    <input type="date" name="fecha_publicacion" class="form-control" value="<?= e($fecha_publicacion) ?>" required>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label class="form-label fw-bold">Tiempo de Lectura (Opcional)</label>
                                    <input type="text" name="tiempo_lectura" class="form-control" value="<?= e($tiempo_lectura) ?>" maxlength="20" placeholder="Ej. 3'">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="activo" id="activoSwitch" <?= $activo ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-bold" for="activoSwitch">Publicar</label>
                                    </div>
                                </div>
                            </div>

                            <h5 class="fw-bold border-bottom pb-2 mb-4 mt-5">Imágenes y Multimedia</h5>

                            <div class="mb-4" id="imagenGroup">
                                <label class="form-label fw-bold">Imagen de Portada</label>
                                <?php if ($imagen_url_actual): ?>
                                    <div class="mb-3 bg-light p-3 text-center border rounded">
                                        <img src="../<?= e($imagen_url_actual) ?>" class="current-image-noticia shadow-sm" alt="Portada">
                                    </div>
                                <?php endif; ?>
                                <input class="form-control" type="file" name="imagen" id="imagenFile" accept="image/jpeg, image/png, image/webp">
                                <div class="form-text">Formatos: JPG, PNG, WEBP. Máximo 2 MB.</div>
                            </div>

                            <div class="mb-4 bg-light p-4 rounded border">
                                <label class="form-label fw-bold text-dark"><i class="bi bi-images me-2"></i>Galería Adicional (Fotos al final del artículo)</label>

                                <?php
                                $galeria = json_decode($imagenes_adicionales_json, true) ?: [];
                                if (!empty($galeria)):
                                ?>
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <?php foreach ($galeria as $img): ?>
                                            <img src="../<?= e($img) ?>" class="img-thumbnail object-fit-cover" style="width: 80px; height: 80px;" alt="Galería">
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="limpiar_galeria" value="1" id="limpiarGaleria">
                                        <label class="form-check-label text-danger fw-bold" for="limpiarGaleria">
                                            Eliminar imágenes adicionales actuales al guardar
                                        </label>
                                    </div>
                                <?php endif; ?>

                                <input class="form-control" type="file" name="imagenes_adicionales[]" multiple accept="image/jpeg, image/png, image/webp">
                                <div class="form-text">Máximo 2 MB por imagen.</div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg mt-4 shadow-sm" style="background-color:#116436; border-color:#116436;">
                                    <i class="bi bi-save me-2"></i> Guardar Contenido
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
<script>
    const tipoSelect = document.getElementById('tipoSelect');
    const btnVolver = document.getElementById('btnVolverListado');

    tipoSelect.addEventListener('change', function() {
        const esComunicado = (this.value === 'comunicado');
        document.getElementById('imagenGroup').style.display = esComunicado ? 'none' : 'block';
        if (btnVolver) {
            btnVolver.href = esComunicado ? 'gestor_contenidos.php?tab=prensa' : 'gestor_contenidos.php?tab=blog';
        }
    });
    tipoSelect.dispatchEvent(new Event('change'));
</script>
</body>
</html>