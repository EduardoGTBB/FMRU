<?php
// admin/landing_form.php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header("Location: index.php"); exit; }
require_once __DIR__ . '/config/db.php';

if (!function_exists('e')) { function e($string) { return htmlspecialchars((string)$string, ENT_QUOTES | ENT_HTML5, 'UTF-8'); } }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$accion = $id ? 'Editar' : 'Crear';
$custom_title = "$accion Landing Page";
$mensaje = ''; $tipo_mensaje = '';

$slug = ''; $titulo = ''; $plantilla = 'torneo'; $activo = 1;
// Estructura JSON por defecto
$config = [
    'imagen_hero' => '',
    'historia' => '',
    'imagenes_adicionales' => [],
    'precios' => [],
    'link_iframe' => ''
];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM fmru_paginas_landing WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $slug = $row['slug']; $titulo = $row['titulo']; $plantilla = $row['plantilla']; $activo = $row['activo'];
        if (!empty($row['configuracion_json'])) {
            $json_decodificado = json_decode($row['configuracion_json'], true) ?: [];
            $config = array_merge($config, $json_decodificado);
        }
    } else { header("Location: gestor_contenidos.php"); exit; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo_post = recibir_post('titulo');
    $slug_post = strtolower(preg_replace('/[^a-zA-Z0-9-]/', '-', trim($_POST['slug'])));
    $plantilla_post = recibir_post('plantilla');
    $activo_post = isset($_POST['activo']) ? 1 : 0;
    
    // Recuperamos los datos del JSON (Historia e Iframe)
    $config_actualizada = $config;
    $config_actualizada['historia'] = recibir_post('historia', true);
    $config_actualizada['link_iframe'] = filter_input(INPUT_POST, 'link_iframe', FILTER_SANITIZE_URL);

    // Procesar Precios Dinámicos
    $precios_nuevos = [];
    $p_nombres = $_POST['precio_nombre'] ?? [];
    $p_montos = $_POST['precio_monto'] ?? [];
    $p_estados = $_POST['precio_estado'] ?? [];

    for ($i = 0; $i < count($p_nombres); $i++) {
        if (!empty(trim($p_nombres[$i])) && is_numeric($p_montos[$i])) {
            $precios_nuevos[] = [
                'nombre' => strip_tags(trim($p_nombres[$i])),
                'monto' => (float)$p_montos[$i],
                'estado' => strip_tags(trim($p_estados[$i]))
            ];
        }
    }
    $config_actualizada['precios'] = $precios_nuevos;

    // OWASP: Validar Limpieza de Galería
    if (isset($_POST['limpiar_galeria']) && $_POST['limpiar_galeria'] === '1') {
        $config_actualizada['imagenes_adicionales'] = [];
    }

    // 1. Subida Imagen Principal (Hero)
    if (isset($_FILES['imagen_hero']) && $_FILES['imagen_hero']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['imagen_hero']['tmp_name'];
        $name = $_FILES['imagen_hero']['name'];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && $_FILES['imagen_hero']['size'] < 2 * 1024 * 1024) {
            $new_name = md5(time() . $name) . '.' . $ext;
            $dir = dirname(__DIR__) . '/assets/img/banner/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            if (move_uploaded_file($tmp, $dir . $new_name)) {
                $config_actualizada['imagen_hero'] = 'assets/img/banner/' . $new_name;
            }
        } else { $mensaje = 'Imagen Hero inválida o mayor a 2MB.'; $tipo_mensaje = 'danger'; }
    }

    // 2. Subida Imágenes Adicionales (Múltiples nativo)
    if (empty($mensaje) && isset($_FILES['img_adicionales']) && $_FILES['img_adicionales']['error'][0] !== UPLOAD_ERR_NO_FILE) {
        $total = count($_FILES['img_adicionales']['name']);
        for ($i = 0; $i < $total; $i++) {
            if ($_FILES['img_adicionales']['error'][$i] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['img_adicionales']['tmp_name'][$i];
                $name = $_FILES['img_adicionales']['name'][$i];
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && $_FILES['img_adicionales']['size'][$i] < 2 * 1024 * 1024) {
                    $new_name = md5(time() . $name . $i) . '.' . $ext;
                    $dir = dirname(__DIR__) . '/assets/img/noticias/';
                    if (move_uploaded_file($tmp, $dir . $new_name)) {
                        $config_actualizada['imagenes_adicionales'][] = 'assets/img/noticias/' . $new_name;
                    }
                }
            }
        }
    }

    // Guardado Final
    if (empty($mensaje)) {
        $json_final = json_encode($config_actualizada);
        try {
            if ($id) {
                $sql = "UPDATE fmru_paginas_landing SET slug=?, titulo=?, plantilla=?, configuracion_json=?, activo=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$slug_post, $titulo_post, $plantilla_post, $json_final, $activo_post, $id]);
                $mensaje = 'Landing Page actualizada correctamente.';
            } else {
                $sql = "INSERT INTO fmru_paginas_landing (slug, titulo, plantilla, configuracion_json, activo) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$slug_post, $titulo_post, $plantilla_post, $json_final, $activo_post]);
                $mensaje = 'Landing Page creada exitosamente.';
                $slug_post = ''; $titulo_post = ''; $config_actualizada = $config; // Limpiar
            }
            $tipo_mensaje = 'success';
            $slug = $slug_post; $titulo = $titulo_post; $plantilla = $plantilla_post; $activo = $activo_post; $config = $config_actualizada;
        } catch (PDOException $e) {
            $mensaje = ($e->getCode() == 23000) ? "Error: El Slug (URL) ya existe." : "Error: " . $e->getMessage();
            $tipo_mensaje = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
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
            <div class="mb-4"><a href="gestor_contenidos.php" class="btn btn-light border btn-sm shadow-sm"><i class="bi bi-arrow-left"></i> Volver al listado</a></div>
            
            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show shadow-sm">
                    <?= $mensaje ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="form-card">
                        <form method="POST" enctype="multipart/form-data">
                            
                            <!-- DATOS GENERALES -->
                            <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Datos Generales</h5>
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Título de la Página</label>
                                    <input type="text" name="titulo" class="form-control" value="<?= e($titulo) ?>" required placeholder="Ej. Programa Desarrollo 2026">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">URL Amigable (Slug)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">/</span>
                                        <input type="text" name="slug" class="form-control" value="<?= e($slug) ?>" required placeholder="programa-2026">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Plantilla</label>
                                    <select name="plantilla" class="form-select" id="plantillaSelect">
                                        <option value="torneo" <?= $plantilla == 'torneo' ? 'selected' : '' ?>>Torneo</option>
                                        <option value="informativa" <?= $plantilla == 'informativa' ? 'selected' : '' ?>>Informativa</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="activo" <?= $activo ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold text-success">Página Pública y Activa</label>
                                </div>
                            </div>

                            <!-- CONTENIDO Y MULTIMEDIA (PARA AMBAS PLANTILLAS) -->
                            <h5 class="fw-bold text-dark mb-4 border-bottom pb-2 mt-5">Contenido y Multimedia</h5>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted">Texto Principal de la Página</label>
                                <textarea name="historia" class="form-control bg-light" rows="8" placeholder="Escribe el texto informativo..."><?= e($config['historia']) ?></textarea>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted">1. Imagen de Portada (Banner Superior)</label>
                                    <?php if (!empty($config['imagen_hero'])): ?>
                                        <div class="mb-2"><img src="../<?= e($config['imagen_hero']) ?>" class="img-thumbnail" style="height:80px;"></div>
                                    <?php endif; ?>
                                    <input class="form-control" type="file" name="imagen_hero" accept="image/jpeg, image/png, image/webp">
                                </div>
                                
                                <div class="col-md-6 border-start">
                                    <label class="form-label fw-bold text-muted">2. Galería (Fotos adicionales al final)</label>
                                    <?php if(!empty($config['imagenes_adicionales'])): ?>
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            <?php foreach($config['imagenes_adicionales'] as $img): ?>
                                                <img src="../<?= e($img) ?>" class="img-thumbnail object-fit-cover" style="width: 50px; height: 50px;">
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="limpiar_galeria" value="1">
                                            <label class="form-check-label text-danger small fw-bold">Borrar galería actual al guardar</label>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <input class="form-control" type="file" name="img_adicionales[]" multiple accept="image/jpeg, image/png, image/webp">
                                    <div class="form-text mt-2">Puedes seleccionar varios archivos a la vez manteniendo presionada la tecla Ctrl (o Cmd en Mac). Las nuevas imágenes se <b>sumarán</b> a las existentes si no seleccionas la casilla de eliminar. Máx 2MB por imagen.</div>
                                </div>
                            </div>

                            <!-- CONFIGURACIÓN ESPECÍFICA DE TORNEOS (SE OCULTA EN INFORMATIVA) -->
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
                                        <tbody id="preciosContainer">
                                            <!-- Los precios se inyectarán por JS -->
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-dark mb-4 fw-bold" onclick="agregarPrecio()"><i class="bi bi-plus-lg me-1"></i> Agregar Tarjeta de Precio</button>

                                <div class="mb-4">
                                    <label class="form-label text-muted fw-bold">Link Externo de Registro (Space u otro Iframe)</label>
                                    <input type="url" name="link_iframe" class="form-control" value="<?= e($config['link_iframe']) ?>" placeholder="https://app.space...">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Lógica para mostrar/ocultar secciones específicas
    document.getElementById('plantillaSelect').addEventListener('change', function() {
        document.getElementById('configTorneo').style.display = (this.value === 'torneo') ? 'block' : 'none';
    });
    // Disparar evento para ajustar la vista al cargar la página
    document.getElementById('plantillaSelect').dispatchEvent(new Event('change'));

    // Lógica del Generador Dinámico de Precios
    const container = document.getElementById('preciosContainer');
    const preciosActuales = <?= json_encode($config['precios']) ?>;

    function agregarPrecio(nombre = '', monto = '', estado = 'normal') {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="precio_nombre[]" class="form-control text-center fw-bold" value="${nombre}" placeholder="Ej. Early Bird" required></td>
            <td>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" name="precio_monto[]" class="form-control text-center" value="${monto}" required>
                </div>
            </td>
            <td>
                <select name="precio_estado[]" class="form-select text-center">
                    <option value="normal" ${estado==='normal'?'selected':''}>Fase Inactiva / Pasada</option>
                    <option value="activa" ${estado==='activa'?'selected':''}>Fase Activa (Destacada)</option>
                </select>
            </td>
            <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button></td>
        `;
        container.appendChild(tr);
    }

    if (preciosActuales.length > 0) {
        preciosActuales.forEach(p => agregarPrecio(p.nombre, p.monto, p.estado));
    } else {
        agregarPrecio('Fase 1', '10000', 'normal');
        agregarPrecio('Fase 2', '11500', 'activa');
        agregarPrecio('Fase 3', '13000', 'normal');
    }
</script>
</body>
</html>