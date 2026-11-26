<?php
// admin/editar_serpientes.php
require_once __DIR__ . '/config/seguridad.php';
exigir_sesion_admin();
require_once __DIR__ . '/config/db.php';
global $db;

$custom_title = 'Gestión de Serpientes';

$nombres_pestanas = [
    'femenil' => 'Serpientes Femenil',
    'varonil' => 'Serpientes Varonil'
];
$pestanas_permitidas = array_keys($nombres_pestanas);
$tab_solicitada = $_POST['seccion_serpientes'] ?? ($_GET['tab'] ?? $pestanas_permitidas[0]);
$tab_activa = in_array($tab_solicitada, $pestanas_permitidas, true) ? $tab_solicitada : $pestanas_permitidas[0];

$mensaje = $_SESSION['flash_mensaje'] ?? '';
$tipo_mensaje = $_SESSION['flash_tipo'] ?? '';
unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validar_csrf()) {
        $_SESSION['flash_mensaje'] = 'Token de seguridad inválido. Intenta guardar nuevamente.';
        $_SESSION['flash_tipo'] = 'danger';
        header('Location: editar_serpientes.php?tab=' . urlencode($tab_activa));
        exit;
    }

    try {
        $db->beginTransaction();

        if ($tab_activa === 'femenil') {
            $db->updateSetting('sptf_hero_badge', recibir_post('sptf_hero_badge'));
            $db->updateSetting('sptf_hero_titulo', recibir_post('sptf_hero_titulo'));
            $db->updateSetting('sptf_hero_sub', recibir_post('sptf_hero_sub'));
            $db->updateSetting('sptf_intro', recibir_post('sptf_intro', true));

            $equipos_nuevos = [];
            $nombres = $_POST['sptf_equipo_nombre'] ?? [];
            $estados = $_POST['sptf_equipo_estado'] ?? [];
            for ($i = 0; $i < count($nombres); $i++) {
                $nombre_limpio = strip_tags(trim((string)$nombres[$i]));
                $estado_limpio = strip_tags(trim((string)($estados[$i] ?? '')));
                if ($nombre_limpio !== '') {
                    $equipos_nuevos[] = [
                        'nombre' => $nombre_limpio,
                        'estado' => $estado_limpio,
                        'logo'   => mb_strtoupper(mb_substr($nombre_limpio, 0, 1, 'UTF-8'), 'UTF-8')
                    ];
                }
            }
            $db->updateSetting('sptf_equipos', json_encode($equipos_nuevos, JSON_UNESCAPED_UNICODE));

            $staff_nuevo = [];
            $staff_roles   = $_POST['sptf_staff_rol'] ?? [];
            $staff_nombres = $_POST['sptf_staff_nombre'] ?? [];
            $staff_iconos  = $_POST['sptf_staff_icono'] ?? [];
            for ($i = 0; $i < count($staff_nombres); $i++) {
                $nombre_limpio = strip_tags(trim((string)$staff_nombres[$i]));
                $rol_limpio    = strip_tags(trim((string)($staff_roles[$i] ?? '')));
                $icono_limpio  = preg_replace('/[^a-zA-Z0-9\-]/', '', trim((string)($staff_iconos[$i] ?? 'bi-person-badge-fill')));
                if ($nombre_limpio !== '') {
                    $staff_nuevo[] = [
                        'rol'    => $rol_limpio,
                        'nombre' => $nombre_limpio,
                        'icono'  => $icono_limpio ?: 'bi-person-badge-fill'
                    ];
                }
            }
            $db->updateSetting('sptf_staff', json_encode($staff_nuevo, JSON_UNESCAPED_UNICODE));

            $historico_nuevo = [];
            $h_temp = $_POST['sptf_hist_temporada'] ?? [];
            $h_camp = $_POST['sptf_hist_campeon'] ?? [];
            $h_sub  = $_POST['sptf_hist_subcampeon'] ?? [];
            $h_mvp  = $_POST['sptf_hist_mvp'] ?? [];
            for ($i = 0; $i < count($h_temp); $i++) {
                $temp_limpio = strip_tags(trim((string)$h_temp[$i]));
                if ($temp_limpio !== '') {
                    $historico_nuevo[] = [
                        'temporada'  => $temp_limpio,
                        'campeon'    => strip_tags(trim((string)($h_camp[$i] ?? ''))),
                        'subcampeon' => strip_tags(trim((string)($h_sub[$i] ?? ''))),
                        'mvp'        => strip_tags(trim((string)($h_mvp[$i] ?? '')))
                    ];
                }
            }
            $db->updateSetting('sptf_historico', json_encode($historico_nuevo, JSON_UNESCAPED_UNICODE));

            if ($img = procesar_imagen_segura('sptf_hero_img_file', 'assets/img/banner', 'sptf')) {
                $db->updateSetting('sptf_hero_img', $img);
            }
        }

        elseif ($tab_activa === 'varonil') {
            $db->updateSetting('sptv_hero_badge', recibir_post('sptv_hero_badge'));
            $db->updateSetting('sptv_hero_titulo', recibir_post('sptv_hero_titulo'));
            $db->updateSetting('sptv_hero_sub', recibir_post('sptv_hero_sub'));
            $db->updateSetting('sptv_intro', recibir_post('sptv_intro', true));

            $equipos_v_nuevos = [];
            $nombres_v = $_POST['sptv_equipo_nombre'] ?? [];
            $estados_v = $_POST['sptv_equipo_estado'] ?? [];
            for ($i = 0; $i < count($nombres_v); $i++) {
                $nombre_limpio = strip_tags(trim((string)$nombres_v[$i]));
                $estado_limpio = strip_tags(trim((string)($estados_v[$i] ?? '')));
                if ($nombre_limpio !== '') {
                    $equipos_v_nuevos[] = [
                        'nombre' => $nombre_limpio,
                        'estado' => $estado_limpio,
                        'logo'   => mb_strtoupper(mb_substr($nombre_limpio, 0, 1, 'UTF-8'), 'UTF-8')
                    ];
                }
            }
            $db->updateSetting('sptv_equipos', json_encode($equipos_v_nuevos, JSON_UNESCAPED_UNICODE));

            $staff_v_nuevo = [];
            $sv_roles   = $_POST['sptv_staff_rol'] ?? [];
            $sv_nombres = $_POST['sptv_staff_nombre'] ?? [];
            $sv_iconos  = $_POST['sptv_staff_icono'] ?? [];
            for ($i = 0; $i < count($sv_nombres); $i++) {
                $nombre_limpio = strip_tags(trim((string)$sv_nombres[$i]));
                $rol_limpio    = strip_tags(trim((string)($sv_roles[$i] ?? '')));
                $icono_limpio  = preg_replace('/[^a-zA-Z0-9\-]/', '', trim((string)($sv_iconos[$i] ?? 'bi-person-badge-fill')));
                if ($nombre_limpio !== '') {
                    $staff_v_nuevo[] = [
                        'rol'    => $rol_limpio,
                        'nombre' => $nombre_limpio,
                        'icono'  => $icono_limpio ?: 'bi-person-badge-fill'
                    ];
                }
            }
            $db->updateSetting('sptv_staff', json_encode($staff_v_nuevo, JSON_UNESCAPED_UNICODE));

            $hist_v_nuevo = [];
            $hv_num     = $_POST['sptv_hist_no'] ?? [];
            $hv_nombre  = $_POST['sptv_hist_nombre'] ?? [];
            $hv_equipo  = $_POST['sptv_hist_equipo'] ?? [];
            $hv_torneos = $_POST['sptv_hist_torneos'] ?? [];
            for ($i = 0; $i < count($hv_nombre); $i++) {
                $nombre_limpio = strip_tags(trim((string)$hv_nombre[$i]));
                if ($nombre_limpio !== '') {
                    $hist_v_nuevo[] = [
                        'no_serpiente' => strip_tags(trim((string)($hv_num[$i] ?? ''))),
                        'nombre'       => $nombre_limpio,
                        'equipo'       => strip_tags(trim((string)($hv_equipo[$i] ?? '-'))),
                        'torneos'      => strip_tags(trim((string)($hv_torneos[$i] ?? '0')))
                    ];
                }
            }
            $db->updateSetting('sptv_historico', json_encode($hist_v_nuevo, JSON_UNESCAPED_UNICODE));

            if ($img = procesar_imagen_segura('sptv_hero_img_file', 'assets/img/banner', 'sptv')) {
                $db->updateSetting('sptv_hero_img', $img);
            }
        }

        $db->commit();
        $nombre_seccion = $nombres_pestanas[$tab_activa] ?? 'Serpientes';
        $_SESSION['flash_mensaje'] = "Sección «{$nombre_seccion}» actualizada correctamente.";
        $_SESSION['flash_tipo'] = 'success';
    } catch (RuntimeException $e) {
        $db->rollBack();
        $_SESSION['flash_mensaje'] = $e->getMessage();
        $_SESSION['flash_tipo'] = 'danger';
    } catch (Throwable $e) {
        $db->rollBack();
        $_SESSION['flash_mensaje'] = 'Error al actualizar la configuración.';
        $_SESSION['flash_tipo'] = 'danger';
    }

    header('Location: editar_serpientes.php?tab=' . urlencode($tab_activa));
    exit;
}

try {
    $settings       = $db->getAllSettings();
    $equipos_sptf   = json_decode($settings['sptf_equipos'] ?? '[]', true) ?: [];
    $staff_sptf     = json_decode($settings['sptf_staff'] ?? '[]', true) ?: [];
    $historico_sptf = json_decode($settings['sptf_historico'] ?? '[]', true) ?: [];
    $equipos_sptv   = json_decode($settings['sptv_equipos'] ?? '[]', true) ?: [];
    $staff_sptv     = json_decode($settings['sptv_staff'] ?? '[]', true) ?: [];
    $historico_sptv = json_decode($settings['sptv_historico'] ?? '[]', true) ?: [];
} catch (Throwable $e) {
    die('Error al cargar datos.');
}

$iconos_staff = [
    'bi-person-badge-fill'    => 'Entrenador / Coach',
    'bi-heart-pulse-fill'     => 'Preparador Físico',
    'bi-bandaid-fill'         => 'Médico / Fisioterapeuta',
    'bi-clipboard-check-fill' => 'Manager / Coordinador',
    'bi-person-fill'          => 'Staff General'
];
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

            <h3 class="fw-bold mb-4" style="color: #bf2333;">Gestor de Selecciones (Serpientes)</h3>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <ul class="nav nav-tabs nav-tabs-custom flex-nowrap overflow-x-auto overflow-y-hidden" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link <?= $tab_activa === 'femenil' ? 'active' : '' ?> text-nowrap" data-bs-toggle="tab" data-bs-target="#spt_femenil" role="tab">Serpientes Femenil</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link <?= $tab_activa === 'varonil' ? 'active' : '' ?> text-nowrap" data-bs-toggle="tab" data-bs-target="#spt_varonil" role="tab">Serpientes Varonil</button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4 p-md-5 bg-light">
                    <div class="tab-content">

                        <!-- PESTAÑA 1: SERPIENTES FEMENIL -->
                        <div class="tab-pane fade <?= $tab_activa === 'femenil' ? 'show active' : '' ?>" id="spt_femenil" role="tabpanel">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <?= csrf_campo() ?>
                                <input type="hidden" name="seccion_serpientes" value="femenil">

                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">1. Banner Superior (Hero)</h5>
                                <div class="row mb-5">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label fw-bold">Etiqueta (Badge)</label>
                                                <input type="text" name="sptf_hero_badge" class="form-control" maxlength="50" value="<?= e($settings['sptf_hero_badge'] ?? 'Serpientes') ?>">
                                            </div>
                                            <div class="col-md-8 mb-3">
                                                <label class="form-label fw-bold">Título del Banner</label>
                                                <input type="text" name="sptf_hero_titulo" class="form-control fw-bold" maxlength="150" value="<?= e($settings['sptf_hero_titulo'] ?? 'Liga de Rugby Femenil') ?>">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Subtítulo</label>
                                            <input type="text" name="sptf_hero_sub" class="form-control" maxlength="255" value="<?= e($settings['sptf_hero_sub'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center bg-white p-3 border rounded shadow-sm">
                                        <label class="form-label fw-bold text-muted small">Imagen de Fondo</label>
                                        <?php if (!empty($settings['sptf_hero_img'])): ?>
                                            <div class="mb-2"><img src="../<?= e($settings['sptf_hero_img']) ?>" class="img-thumbnail" style="max-height: 100px;" alt="Banner Serpientes Femenil"></div>
                                        <?php endif; ?>
                                        <input type="file" name="sptf_hero_img_file" class="form-control form-control-sm" accept="image/jpeg, image/png, image/webp">
                                    </div>
                                </div>

                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">2. Introducción</h5>
                                <div class="mb-5">
                                    <label class="form-label fw-bold">Texto Principal</label>
                                    <textarea name="sptf_intro" class="form-control editor-texto" rows="4"><?= e($settings['sptf_intro'] ?? '') ?></textarea>
                                </div>

                                <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
                                    <h5 class="fw-bold text-dark mb-0">3. Equipos Participantes</h5>
                                    <button type="button" class="btn btn-sm btn-outline-dark fw-bold" onclick="agregarEquipoSptF()"><i class="bi bi-plus-lg me-1"></i> Añadir Equipo</button>
                                </div>

                                <div class="row g-3 mb-5" id="equipos-sptf-container">
                                    <?php foreach ($equipos_sptf as $equipo): ?>
                                    <div class="col-xl-3 col-lg-4 col-md-6 equipo-sptf-item">
                                        <div class="bg-white p-3 border rounded shadow-sm h-100 position-relative border-top border-4 border-success">
                                            <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" onclick="this.closest('.equipo-sptf-item').remove()" title="Eliminar equipo">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <div class="mb-2 pe-4">
                                                <label class="form-label small fw-bold text-muted mb-1">Nombre del Equipo</label>
                                                <input type="text" name="sptf_equipo_nombre[]" class="form-control form-control-sm fw-bold" value="<?= e($equipo['nombre']) ?>" required>
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold text-muted mb-1">Estado / Ciudad</label>
                                                <input type="text" name="sptf_equipo_estado[]" class="form-control form-control-sm" value="<?= e($equipo['estado']) ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
                                    <h5 class="fw-bold text-dark mb-0">4. Staff Técnico</h5>
                                    <button type="button" class="btn btn-sm btn-outline-dark fw-bold" onclick="agregarStaffSptF()"><i class="bi bi-plus-lg me-1"></i> Añadir Staff</button>
                                </div>

                                <div class="row g-3 mb-5" id="staff-sptf-container">
                                    <?php foreach ($staff_sptf as $miembro): ?>
                                    <div class="col-lg-4 col-md-6 staff-sptf-item">
                                        <div class="bg-white p-3 border rounded shadow-sm h-100 position-relative border-top border-4 border-danger">
                                            <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" onclick="this.closest('.staff-sptf-item').remove()" title="Eliminar miembro">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <div class="mb-2 pe-4">
                                                <label class="form-label small fw-bold text-muted mb-1">Rol / Cargo</label>
                                                <input type="text" name="sptf_staff_rol[]" class="form-control form-control-sm fw-bold text-uppercase" value="<?= e($miembro['rol']) ?>" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold text-muted mb-1">Nombre Completo</label>
                                                <input type="text" name="sptf_staff_nombre[]" class="form-control form-control-sm" value="<?= e($miembro['nombre']) ?>" required>
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold text-muted mb-1">Ícono</label>
                                                <select name="sptf_staff_icono[]" class="form-select form-select-sm">
                                                    <?php foreach ($iconos_staff as $clase => $etiqueta): ?>
                                                        <option value="<?= e($clase) ?>" <?= ($miembro['icono'] ?? '') === $clase ? 'selected' : '' ?>><?= e($etiqueta) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
                                    <h5 class="fw-bold text-dark mb-0">5. Histórico Seleccionados / Campeonas</h5>
                                    <button type="button" class="btn btn-sm btn-outline-dark fw-bold" onclick="agregarHistoricoSptF()"><i class="bi bi-plus-lg me-1"></i> Añadir Registro</button>
                                </div>

                                <div class="table-responsive bg-white rounded border shadow-sm mb-5">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead class="table-light small text-uppercase">
                                            <tr>
                                                <th>Temporada</th>
                                                <th>Campeón (1er Lugar)</th>
                                                <th>Subcampeón (2do Lugar)</th>
                                                <th>Jugadora Destacada (MVP)</th>
                                                <th class="text-center" style="width: 60px;">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="historico-sptf-container">
                                            <?php foreach ($historico_sptf as $hist): ?>
                                            <tr class="historico-sptf-row">
                                                <td><input type="text" name="sptf_hist_temporada[]" class="form-control form-control-sm fw-bold" value="<?= e($hist['temporada']) ?>" required></td>
                                                <td><input type="text" name="sptf_hist_campeon[]" class="form-control form-control-sm" value="<?= e($hist['campeon']) ?>" required></td>
                                                <td><input type="text" name="sptf_hist_subcampeon[]" class="form-control form-control-sm" value="<?= e($hist['subcampeon']) ?>" required></td>
                                                <td><input type="text" name="sptf_hist_mvp[]" class="form-control form-control-sm" value="<?= e($hist['mvp']) ?>" required></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()" title="Eliminar fila"><i class="bi bi-trash"></i></button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-center mt-5 mb-3">
                                    <button type="submit" class="btn btn-primary px-5 py-3 shadow-lg fs-5" style="background-color: #116436; border-color: #116436; border-radius: 50px;">
                                        <i class="bi bi-save me-2"></i> Guardar Serpientes Femenil
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- PESTAÑA 2: SERPIENTES VARONIL -->
                        <div class="tab-pane fade <?= $tab_activa === 'varonil' ? 'show active' : '' ?>" id="spt_varonil" role="tabpanel">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <?= csrf_campo() ?>
                                <input type="hidden" name="seccion_serpientes" value="varonil">

                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">1. Banner Superior (Hero)</h5>
                                <div class="row mb-5">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label fw-bold">Etiqueta (Badge)</label>
                                                <input type="text" name="sptv_hero_badge" class="form-control" maxlength="50" value="<?= e($settings['sptv_hero_badge'] ?? 'Serpientes') ?>">
                                            </div>
                                            <div class="col-md-8 mb-3">
                                                <label class="form-label fw-bold">Título del Banner</label>
                                                <input type="text" name="sptv_hero_titulo" class="form-control fw-bold" maxlength="150" value="<?= e($settings['sptv_hero_titulo'] ?? 'Liga de Rugby Varonil') ?>">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Subtítulo</label>
                                            <input type="text" name="sptv_hero_sub" class="form-control" maxlength="255" value="<?= e($settings['sptv_hero_sub'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center bg-white p-3 border rounded shadow-sm">
                                        <label class="form-label fw-bold text-muted small">Imagen de Fondo</label>
                                        <?php if (!empty($settings['sptv_hero_img'])): ?>
                                            <div class="mb-2"><img src="../<?= e($settings['sptv_hero_img']) ?>" class="img-thumbnail" style="max-height: 100px;" alt="Banner Serpientes Varonil"></div>
                                        <?php endif; ?>
                                        <input type="file" name="sptv_hero_img_file" class="form-control form-control-sm" accept="image/jpeg, image/png, image/webp">
                                    </div>
                                </div>

                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">2. Introducción</h5>
                                <div class="mb-5">
                                    <label class="form-label fw-bold">Texto Principal</label>
                                    <textarea name="sptv_intro" class="form-control editor-texto" rows="4"><?= e($settings['sptv_intro'] ?? '') ?></textarea>
                                </div>

                                <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
                                    <h5 class="fw-bold text-dark mb-0">3. Equipos Participantes</h5>
                                    <button type="button" class="btn btn-sm btn-outline-dark fw-bold" onclick="agregarEquipoSptV()"><i class="bi bi-plus-lg me-1"></i> Añadir Equipo</button>
                                </div>

                                <div class="row g-3 mb-5" id="equipos-sptv-container">
                                    <?php foreach ($equipos_sptv as $equipo): ?>
                                    <div class="col-xl-3 col-lg-4 col-md-6 equipo-sptv-item">
                                        <div class="bg-white p-3 border rounded shadow-sm h-100 position-relative border-top border-4 border-success">
                                            <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" onclick="this.closest('.equipo-sptv-item').remove()" title="Eliminar equipo">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <div class="mb-2 pe-4">
                                                <label class="form-label small fw-bold text-muted mb-1">Nombre del Equipo</label>
                                                <input type="text" name="sptv_equipo_nombre[]" class="form-control form-control-sm fw-bold" value="<?= e($equipo['nombre']) ?>" required>
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold text-muted mb-1">Estado / Ciudad</label>
                                                <input type="text" name="sptv_equipo_estado[]" class="form-control form-control-sm" value="<?= e($equipo['estado']) ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
                                    <h5 class="fw-bold text-dark mb-0">4. Staff Técnico</h5>
                                    <button type="button" class="btn btn-sm btn-outline-dark fw-bold" onclick="agregarStaffSptV()"><i class="bi bi-plus-lg me-1"></i> Añadir Staff</button>
                                </div>

                                <div class="row g-3 mb-5" id="staff-sptv-container">
                                    <?php foreach ($staff_sptv as $miembro): ?>
                                    <div class="col-lg-4 col-md-6 staff-sptv-item">
                                        <div class="bg-white p-3 border rounded shadow-sm h-100 position-relative border-top border-4 border-danger">
                                            <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" onclick="this.closest('.staff-sptv-item').remove()" title="Eliminar miembro">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <div class="mb-2 pe-4">
                                                <label class="form-label small fw-bold text-muted mb-1">Rol / Cargo</label>
                                                <input type="text" name="sptv_staff_rol[]" class="form-control form-control-sm fw-bold text-uppercase" value="<?= e($miembro['rol']) ?>" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold text-muted mb-1">Nombre Completo</label>
                                                <input type="text" name="sptv_staff_nombre[]" class="form-control form-control-sm" value="<?= e($miembro['nombre']) ?>" required>
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold text-muted mb-1">Ícono</label>
                                                <select name="sptv_staff_icono[]" class="form-select form-select-sm">
                                                    <?php foreach ($iconos_staff as $clase => $etiqueta): ?>
                                                        <option value="<?= e($clase) ?>" <?= ($miembro['icono'] ?? '') === $clase ? 'selected' : '' ?>><?= e($etiqueta) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
                                    <h5 class="fw-bold text-dark mb-0">5. Histórico Seleccionados Nacionales</h5>
                                    <button type="button" class="btn btn-sm btn-outline-dark fw-bold" onclick="agregarHistoricoSptV()"><i class="bi bi-plus-lg me-1"></i> Añadir Jugador</button>
                                </div>

                                <div class="table-responsive bg-white rounded border shadow-sm mb-5">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead class="table-light small text-uppercase">
                                            <tr>
                                                <th style="width: 130px;">No. Serpiente</th>
                                                <th>Nombre</th>
                                                <th>Equipo</th>
                                                <th style="width: 130px;">No. Torneos</th>
                                                <th class="text-center" style="width: 60px;">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="historico-sptv-container">
                                            <?php foreach ($historico_sptv as $hist): ?>
                                            <tr class="historico-sptv-row">
                                                <td><input type="text" name="sptv_hist_no[]" class="form-control form-control-sm fw-bold text-center" value="<?= e($hist['no_serpiente'] ?? '') ?>" required></td>
                                                <td><input type="text" name="sptv_hist_nombre[]" class="form-control form-control-sm" value="<?= e($hist['nombre'] ?? '') ?>" required></td>
                                                <td><input type="text" name="sptv_hist_equipo[]" class="form-control form-control-sm" value="<?= e($hist['equipo'] ?? '-') ?>" required></td>
                                                <td><input type="text" name="sptv_hist_torneos[]" class="form-control form-control-sm text-center" value="<?= e($hist['torneos'] ?? '0') ?>" required></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()" title="Eliminar fila"><i class="bi bi-trash"></i></button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-center mt-5 mb-3">
                                    <button type="submit" class="btn btn-primary px-5 py-3 shadow-lg fs-5" style="background-color: #116436; border-color: #116436; border-radius: 50px;">
                                        <i class="bi bi-save me-2"></i> Guardar Serpientes Varonil
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

<!-- <script>if(!window.bootstrap){document.write('<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"><\/script>');}</script> -->
<script>
    const opcionesIconosStaff = `
        <option value="bi-person-badge-fill">Entrenador / Coach</option>
        <option value="bi-heart-pulse-fill">Preparador Físico</option>
        <option value="bi-bandaid-fill">Médico / Fisioterapeuta</option>
        <option value="bi-clipboard-check-fill">Manager / Coordinador</option>
        <option value="bi-person-fill">Staff General</option>
    `;

    function agregarEquipoSptF() {
        const container = document.getElementById('equipos-sptf-container');
        const html = `
            <div class="col-xl-3 col-lg-4 col-md-6 equipo-sptf-item">
                <div class="bg-white p-3 border rounded shadow-sm h-100 position-relative border-top border-4 border-info">
                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" onclick="this.closest('.equipo-sptf-item').remove()" title="Eliminar equipo">
                        <i class="bi bi-trash"></i>
                    </button>
                    <div class="mb-2 pe-4">
                        <label class="form-label small fw-bold text-info mb-1">Nombre del Equipo</label>
                        <input type="text" name="sptf_equipo_nombre[]" class="form-control form-control-sm fw-bold" placeholder="Ej. Pumas UNAM" required>
                    </div>
                    <div>
                        <label class="form-label small fw-bold text-info mb-1">Estado / Ciudad</label>
                        <input type="text" name="sptf_equipo_estado[]" class="form-control form-control-sm" placeholder="Ej. Ciudad de México" required>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function agregarEquipoSptV() {
        const container = document.getElementById('equipos-sptv-container');
        const html = `
            <div class="col-xl-3 col-lg-4 col-md-6 equipo-sptv-item">
                <div class="bg-white p-3 border rounded shadow-sm h-100 position-relative border-top border-4 border-info">
                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" onclick="this.closest('.equipo-sptv-item').remove()" title="Eliminar equipo">
                        <i class="bi bi-trash"></i>
                    </button>
                    <div class="mb-2 pe-4">
                        <label class="form-label small fw-bold text-info mb-1">Nombre del Equipo</label>
                        <input type="text" name="sptv_equipo_nombre[]" class="form-control form-control-sm fw-bold" placeholder="Ej. Black Thunder RC" required>
                    </div>
                    <div>
                        <label class="form-label small fw-bold text-info mb-1">Estado / Ciudad</label>
                        <input type="text" name="sptv_equipo_estado[]" class="form-control form-control-sm" placeholder="Ej. Ciudad de México" required>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function agregarStaffSptF() {
        const container = document.getElementById('staff-sptf-container');
        const html = `
            <div class="col-lg-4 col-md-6 staff-sptf-item">
                <div class="bg-white p-3 border rounded shadow-sm h-100 position-relative border-top border-4 border-info">
                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" onclick="this.closest('.staff-sptf-item').remove()" title="Eliminar miembro">
                        <i class="bi bi-trash"></i>
                    </button>
                    <div class="mb-2 pe-4">
                        <label class="form-label small fw-bold text-info mb-1">Rol / Cargo</label>
                        <input type="text" name="sptf_staff_rol[]" class="form-control form-control-sm fw-bold text-uppercase" placeholder="Ej. Entrenador" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-info mb-1">Nombre Completo</label>
                        <input type="text" name="sptf_staff_nombre[]" class="form-control form-control-sm" placeholder="Ej. Alessandro Cordone" required>
                    </div>
                    <div>
                        <label class="form-label small fw-bold text-info mb-1">Ícono</label>
                        <select name="sptf_staff_icono[]" class="form-select form-select-sm">${opcionesIconosStaff}</select>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function agregarHistoricoSptF() {
        const tbody = document.getElementById('historico-sptf-container');
        const html = `
            <tr class="historico-sptf-row">
                <td><input type="text" name="sptf_hist_temporada[]" class="form-control form-control-sm fw-bold" placeholder="Ej. 2025 - 2026" required></td>
                <td><input type="text" name="sptf_hist_campeon[]" class="form-control form-control-sm" placeholder="Equipo Campeón" required></td>
                <td><input type="text" name="sptf_hist_subcampeon[]" class="form-control form-control-sm" placeholder="Subcampeón" required></td>
                <td><input type="text" name="sptf_hist_mvp[]" class="form-control form-control-sm" placeholder="Nombre MVP" required></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()" title="Eliminar fila"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', html);
    }

    function agregarStaffSptV() {
        const container = document.getElementById('staff-sptv-container');
        const html = `
            <div class="col-lg-4 col-md-6 staff-sptv-item">
                <div class="bg-white p-3 border rounded shadow-sm h-100 position-relative border-top border-4 border-info">
                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" onclick="this.closest('.staff-sptv-item').remove()" title="Eliminar miembro">
                        <i class="bi bi-trash"></i>
                    </button>
                    <div class="mb-2 pe-4">
                        <label class="form-label small fw-bold text-info mb-1">Rol / Cargo</label>
                        <input type="text" name="sptv_staff_rol[]" class="form-control form-control-sm fw-bold text-uppercase" placeholder="Ej. Entrenador" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-info mb-1">Nombre Completo</label>
                        <input type="text" name="sptv_staff_nombre[]" class="form-control form-control-sm" placeholder="Ej. Pablo Guerrero" required>
                    </div>
                    <div>
                        <label class="form-label small fw-bold text-info mb-1">Ícono</label>
                        <select name="sptv_staff_icono[]" class="form-select form-select-sm">${opcionesIconosStaff}</select>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function agregarHistoricoSptV() {
        const tbody = document.getElementById('historico-sptv-container');
        const nextNum = tbody.querySelectorAll('tr').length + 1;
        const html = `
            <tr class="historico-sptv-row">
                <td><input type="text" name="sptv_hist_no[]" class="form-control form-control-sm fw-bold text-center" value="${nextNum}" required></td>
                <td><input type="text" name="sptv_hist_nombre[]" class="form-control form-control-sm" placeholder="Nombre del Jugador" required></td>
                <td><input type="text" name="sptv_hist_equipo[]" class="form-control form-control-sm" value="-" required></td>
                <td><input type="text" name="sptv_hist_torneos[]" class="form-control form-control-sm text-center" value="1" required></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()" title="Eliminar fila"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', html);
    }
</script>
</body>
</html>