<?php
// admin/landing_form.php
require_once __DIR__ . '/config/seguridad.php';
exigir_sesion_admin();
require_once __DIR__ . '/config/db.php';
global $db;

$id = isset($_GET['id']) ? max(0, (int)$_GET['id']) : 0;
$accion = $id ? 'Editar' : 'Crear';
$custom_title = "$accion Landing Page";

$mensaje = $_SESSION['flash_mensaje'] ?? '';
$tipo_mensaje = $_SESSION['flash_tipo'] ?? '';
unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);

$plantillas_permitidas = ['torneo', 'informativa'];
$slug = '';
$titulo = '';
$plantilla = 'torneo';
$activo = 1;

$config_default = [
    'imagen_hero' => '',
    'historia' => '',
    'imagenes_adicionales' => [],
    'precios' => [],
    'link_iframe' => ''
];
$config = $config_default;

if ($id > 0) {
    $row = $db->getLandingById($id);
    if ($row) {
        $slug      = $row['slug'];
        $titulo    = $row['titulo'];
        $plantilla = in_array($row['plantilla'], $plantillas_permitidas, true) ? $row['plantilla'] : 'torneo';
        $activo    = (int)$row['activo'];
        if (!empty($row['configuracion_json'])) {
            $config = array_merge($config_default, json_decode($row['configuracion_json'], true) ?: []);
        }
    } else {
        header('Location: gestor_contenidos.php?tab=landing');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo_post    = recibir_post('titulo');
    $slug_post      = trim(strtolower(preg_replace('/[^a-zA-Z0-9-]+/', '-', (string)($_POST['slug'] ?? ''))), '-');
    $plantilla_raw  = recibir_post('plantilla');
    $plantilla_post = in_array($plantilla_raw, $plantillas_permitidas, true) ? $plantilla_raw : 'torneo';
    $activo_post    = isset($_POST['activo']) ? 1 : 0;

    $config_actualizada = $config;
    $config_actualizada['historia'] = recibir_post('historia', true);

    // OWASP: Validar protocolo seguro en iframe externo (bloquea javascript: o data:)
    $link_raw = trim((string)($_POST['link_iframe'] ?? ''));
    if ($link_raw !== '') {
        $esquema = strtolower((string)parse_url($link_raw, PHP_URL_SCHEME));
        if (filter_var($link_raw, FILTER_VALIDATE_URL) && in_array($esquema, ['https', 'http'], true)) {
            $config_actualizada['link_iframe'] = $link_raw;
        } else {
            $mensaje = 'El enlace externo de registro debe iniciar con https:// o http://.';
            $tipo_mensaje = 'danger';
        }
    } else {
        $config_actualizada['link_iframe'] = '';
    }

    $precios_nuevos = [];
    $p_nombres = $_POST['precio_nombre'] ?? [];
    $p_montos  = $_POST['precio_monto'] ?? [];
    $p_estados = $_POST['precio_estado'] ?? [];
    for ($i = 0; $i < count($p_nombres); $i++) {
        $nom_fase = strip_tags(trim((string)$p_nombres[$i]));
        $est_fase = (($p_estados[$i] ?? 'normal') === 'activa') ? 'activa' : 'normal';
        if ($nom_fase !== '' && isset($p_montos[$i]) && is_numeric($p_montos[$i])) {
            $precios_nuevos[] = [
                'nombre' => $nom_fase,
                'monto'  => (float)$p_montos[$i],
                'estado' => $est_fase
            ];
        }
    }
    $config_actualizada['precios'] = $precios_nuevos;

    if (!validar_csrf()) {
        $mensaje = 'Token de seguridad inválido. Intenta guardar nuevamente.';
        $tipo_mensaje = 'danger';
    } elseif ($titulo_post === '' || $slug_post === '') {
        $mensaje = 'El título y el slug amigable son obligatorios.';
        $tipo_mensaje = 'danger';
    }

    if ($mensaje === '') {
        try {
            if (isset($_POST['limpiar_galeria']) && $_POST['limpiar_galeria'] === '1') {
                $config_actualizada['imagenes_adicionales'] = [];
            }

            $hero_nuevo = procesar_imagen_segura('imagen_hero', 'assets/img/banner', 'landing');
            if ($hero_nuevo !== null) {
                $config_actualizada['imagen_hero'] = $hero_nuevo;
            }

            $galeria_nueva = procesar_galeria_segura('img_adicionales', 'assets/img/noticias');
            if (!empty($galeria_nueva)) {
                $config_actualizada['imagenes_adicionales'] = array_merge($config_actualizada['imagenes_adicionales'], $galeria_nueva);
            }

            $json_final = json_encode($config_actualizada, JSON_UNESCAPED_UNICODE);

            if ($id > 0) {
                $db->updateLanding($id, $slug_post, $titulo_post, $plantilla_post, $json_final, $activo_post);
                $mensaje = 'Landing Page actualizada correctamente.';
                $slug = $slug_post;
                $titulo = $titulo_post;
                $plantilla = $plantilla_post;
                $activo = $activo_post;
                $config = $config_actualizada;
            } else {
                $db->insertLanding($slug_post, $titulo_post, $plantilla_post, $json_final, $activo_post);
                $mensaje = 'Landing Page creada exitosamente.';
                $slug = '';
                $titulo = '';
                $plantilla = 'torneo';
                $activo = 1;
                $config = $config_default;
            }
            $tipo_mensaje = 'success';
        } catch (RuntimeException $e) {
            $mensaje = $e->getMessage();
            $tipo_mensaje = 'danger';
        } catch (PDOException $e) {
            $mensaje = ((string)$e->getCode() === '23000') ? 'Error: Esa URL amigable (Slug) ya está en uso.' : 'Error al guardar en la base de datos.';
            $tipo_mensaje = 'danger';
        }
    }

    if ($tipo_mensaje === 'danger') {
        $slug      = $slug_post;
        $titulo    = $titulo_post;
        $plantilla = $plantilla_post;
        $activo    = $activo_post;
        $config    = $config_actualizada;
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
                <a href="gestor_contenidos.php?tab=landing" class="btn btn-light border btn-sm shadow-sm">
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

                            <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Datos Generales</h5>
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label class="form-label fw-bold">Título de la Página</label>
                                    <input type="text" name="titulo" class="form-control" value="<?= e($titulo) ?>" required maxlength="255" placeholder="Ej. Programa Desarrollo 2026">
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label class="form-label fw-bold">URL Amigable (Slug)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">/</span>
                                        <input type="text" name="slug" class="form-control" value="<?= e($slug) ?>" required maxlength="100" placeholder="programa-2026">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Plantilla</label>
                                    <select name="plantilla" class="form-select" id="plantillaSelect">
                                        <option value="torneo" <?= $plantilla === 'torneo' ? 'selected' : '' ?>>Torneo</option>
                                        <option value="informativa" <?= $plantilla === 'informativa' ? 'selected' : '' ?>>Informativa</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="activo" id="activoLanding" <?= $activo ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold text-success" for="activoLanding">Página Pública y Activa</label>
                                </div>
                            </div>

                            <h5 class="fw-bold text-dark mb-4 border-bottom pb-2 mt-5">Contenido y Multimedia</h5>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted">Texto Principal de la Página</label>
                                <textarea name="historia" class="form-control editor-texto bg-light" rows="8" placeholder="Escribe el texto informativo..."><?= e($config['historia']) ?></textarea>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-4 mb-md-0">
                                    <label class="form-label fw-bold text-muted">1. Imagen de Portada (Banner Superior)</label>
                                    <?php if (!empty($config['imagen_hero'])): ?>
                                        <div class="mb-2"><img src="../<?= e($config['imagen_hero']) ?>" class="img-thumbnail" style="height:80px;" alt="Hero"></div>
                                    <?php endif; ?>
                                    <input class="form-control" type="file" name="imagen_hero" accept="image/jpeg, image/png, image/webp">
                                </div>

                                <div class="col-md-6 border-md-start">
                                    <label class="form-label fw-bold text-muted">2. Galería (Fotos adicionales al final)</label>
                                    <?php if (!empty($config['imagenes_adicionales'])): ?>
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            <?php foreach ($config['imagenes_adicionales'] as $img): ?>
                                                <img src="../<?= e($img) ?>" class="img-thumbnail object-fit-cover" style="width: 50px; height: 50px;" alt="Galería">
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="limpiar_galeria" value="1" id="limpiarGaleriaLanding">
                                            <label class="form-check-label text-danger small fw-bold" for="limpiarGaleriaLanding">Borrar galería actual al guardar</label>
                                        </div>
                                    <?php endif; ?>

                                    <input class="form-control" type="file" name="img_adicionales[]" multiple accept="image/jpeg, image/png, image/webp">
                                    <div class="form-text mt-2">Puedes seleccionar varios archivos con Ctrl/Cmd. Las nuevas imágenes se sumarán a las existentes si no marcas borrar. Máx 2 MB por imagen.</div>
                                </div>
                            </div>

                            <div id="configTorneo">
                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2 mt-5">Tarjetas de Precios y Fases</h5>
                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered align-middle text-center">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nombre de la Fase (Ej. Fase 1)</th>
                                                <th>Monto (MXN)</th>
                                                <th>Estado (Destacar)</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="preciosContainer"></tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-dark mb-4 fw-bold" onclick="agregarPrecio()"><i class="bi bi-plus-lg me-1"></i> Agregar Tarjeta de Precio</button>

                                <div class="mb-4">
                                    <label class="form-label text-muted fw-bold">Link Externo de Registro (Forms / Iframe)</label>
                                    <input type="url" name="link_iframe" class="form-control" value="<?= e($config['link_iframe']) ?>" placeholder="https://forms.office.com/...">
                                    <div class="form-text">Si dejas esto en blanco, el formulario de registro se ocultará automáticamente.</div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-5">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm" style="background-color: #116436; border-color: #116436;">
                                    <i class="bi bi-save me-2"></i> Guardar Landing Page
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
    document.getElementById('plantillaSelect').addEventListener('change', function() {
        document.getElementById('configTorneo').style.display = (this.value === 'torneo') ? 'block' : 'none';
    });
    document.getElementById('plantillaSelect').dispatchEvent(new Event('change'));

    const container = document.getElementById('preciosContainer');
    const preciosActuales = <?= json_encode($config['precios'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    // OWASP: Asignación por propiedades DOM para prevenir inyección por comillas o HTML
    function agregarPrecio(nombre = '', monto = '', estado = 'normal') {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="precio_nombre[]" class="form-control text-center fw-bold inp-nombre" placeholder="Ej. Early Bird" required></td>
            <td>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" name="precio_monto[]" class="form-control text-center inp-monto" required>
                </div>
            </td>
            <td>
                <select name="precio_estado[]" class="form-select text-center sel-estado">
                    <option value="normal">Fase Inactiva / Pasada</option>
                    <option value="activa">Fase Activa (Destacada)</option>
                </select>
            </td>
            <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button></td>
        `;
        tr.querySelector('.inp-nombre').value = nombre;
        tr.querySelector('.inp-monto').value = monto;
        tr.querySelector('.sel-estado').value = (estado === 'activa') ? 'activa' : 'normal';
        container.appendChild(tr);
    }

    if (Array.isArray(preciosActuales) && preciosActuales.length > 0) {
        preciosActuales.forEach(p => agregarPrecio(p.nombre, p.monto, p.estado));
    } else {
        agregarPrecio('Fase 1', '10000', 'normal');
        agregarPrecio('Fase 2', '11500', 'activa');
        agregarPrecio('Fase 3', '13000', 'normal');
    }
</script>
</body>
</html>