<?php
// admin/editar_competencias.php
require_once __DIR__ . '/config/seguridad.php';
exigir_sesion_admin();
require_once __DIR__ . '/config/db.php';
global $db;

$custom_title = 'Gestión de Competencias';

$nombres_pestanas = [
    'xvs_varonil' => 'XVs Varonil',
    'xvs_femenil' => 'XVs Femenil',
    'escolares'   => 'Estudiantiles',
    'gameon'      => 'Game On',
    'rugby7s'     => 'Rugby 7s'
];
$pestanas_permitidas = array_keys($nombres_pestanas);
$tab_solicitada = $_POST['seccion_competencia'] ?? ($_GET['tab'] ?? $pestanas_permitidas[0]);
$tab_activa = in_array($tab_solicitada, $pestanas_permitidas, true) ? $tab_solicitada : $pestanas_permitidas[0];

$mensaje = $_SESSION['flash_mensaje'] ?? '';
$tipo_mensaje = $_SESSION['flash_tipo'] ?? '';
unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validar_csrf()) {
        $_SESSION['flash_mensaje'] = 'Token de seguridad inválido. Intenta guardar nuevamente.';
        $_SESSION['flash_tipo'] = 'danger';
        header('Location: editar_competencias.php?tab=' . urlencode($tab_activa));
        exit;
    }

    try {
        $db->beginTransaction();

        if ($tab_activa === 'xvs_varonil') {
            $db->updateSetting('xvsv_hero_badge', recibir_post('xvsv_hero_badge'));
            $db->updateSetting('xvsv_hero_titulo', recibir_post('xvsv_hero_titulo'));
            $db->updateSetting('xvsv_hero_sub', recibir_post('xvsv_hero_sub'));
            $db->updateSetting('xvsv_intro', recibir_post('xvsv_intro', true));

            $clubes_nuevos = [];
            $nombres_club = $_POST['xvsv_club_nombre'] ?? [];
            $estados_club = $_POST['xvsv_club_estado'] ?? [];
            for ($i = 0; $i < count($nombres_club); $i++) {
                $nombre_limpio = strip_tags(trim((string)$nombres_club[$i]));
                $estado_limpio = strip_tags(trim((string)($estados_club[$i] ?? '')));
                if ($nombre_limpio !== '') {
                    $clubes_nuevos[] = [
                        'nombre' => $nombre_limpio,
                        'estado' => $estado_limpio,
                        'logo'   => mb_strtoupper(mb_substr($nombre_limpio, 0, 1, 'UTF-8'), 'UTF-8')
                    ];
                }
            }
            $db->updateSetting('xvsv_clubes', json_encode($clubes_nuevos, JSON_UNESCAPED_UNICODE));

            if ($img = procesar_imagen_segura('xvsv_hero_img_file', 'assets/img/banner', 'xvsv')) {
                $db->updateSetting('xvsv_hero_img', $img);
            }
        }

        elseif ($tab_activa === 'xvs_femenil') {
            $db->updateSetting('xvsf_hero_badge', recibir_post('xvsf_hero_badge'));
            $db->updateSetting('xvsf_hero_titulo', recibir_post('xvsf_hero_titulo'));
            $db->updateSetting('xvsf_hero_sub', recibir_post('xvsf_hero_sub'));
            $db->updateSetting('xvsf_intro', recibir_post('xvsf_intro', true));

            $clubes_nuevos = [];
            $nombres_club = $_POST['xvsf_club_nombre'] ?? [];
            $estados_club = $_POST['xvsf_club_estado'] ?? [];
            for ($i = 0; $i < count($nombres_club); $i++) {
                $nombre_limpio = strip_tags(trim((string)$nombres_club[$i]));
                $estado_limpio = strip_tags(trim((string)($estados_club[$i] ?? '')));
                if ($nombre_limpio !== '') {
                    $clubes_nuevos[] = [
                        'nombre' => $nombre_limpio,
                        'estado' => $estado_limpio,
                        'logo'   => mb_strtoupper(mb_substr($nombre_limpio, 0, 1, 'UTF-8'), 'UTF-8')
                    ];
                }
            }
            $db->updateSetting('xvsf_clubes', json_encode($clubes_nuevos, JSON_UNESCAPED_UNICODE));

            if ($img = procesar_imagen_segura('xvsf_hero_img_file', 'assets/img/banner', 'xvsf')) {
                $db->updateSetting('xvsf_hero_img', $img);
            }
        }

        elseif ($tab_activa === 'escolares') {
            $db->updateSetting('esc_hero_badge', recibir_post('esc_hero_badge'));
            $db->updateSetting('esc_hero_titulo', recibir_post('esc_hero_titulo'));
            $db->updateSetting('esc_hero_sub', recibir_post('esc_hero_sub'));
            $db->updateSetting('esc_intro', recibir_post('esc_intro', true));

            $tarjetas_nuevas = [];
            $titulos_post = $_POST['tarjeta_titulo'] ?? [];
            $textos_post  = $_POST['tarjeta_texto'] ?? [];
            for ($i = 0; $i < count($titulos_post); $i++) {
                $titulo_limpio = strip_tags(trim((string)$titulos_post[$i]));
                if ($titulo_limpio !== '') {
                    $tarjetas_nuevas[] = [
                        'titulo' => $titulo_limpio,
                        'texto'  => limpiar_html_seguro((string)($textos_post[$i] ?? ''))
                    ];
                }
            }
            $db->updateSetting('esc_tarjetas', json_encode($tarjetas_nuevas, JSON_UNESCAPED_UNICODE));

            if ($img = procesar_imagen_segura('esc_hero_img_file', 'assets/img/banner', 'esc')) {
                $db->updateSetting('esc_hero_img', $img);
            }
        }

        elseif ($tab_activa === 'gameon') {
            $db->updateSetting('go_hero_badge', recibir_post('go_hero_badge'));
            $db->updateSetting('go_hero_titulo', recibir_post('go_hero_titulo'));
            $db->updateSetting('go_hero_sub', recibir_post('go_hero_sub'));
            $db->updateSetting('go_intro_titulo', recibir_post('go_intro_titulo'));
            $db->updateSetting('go_intro', recibir_post('go_intro', true));

            $variantes_nuevas = [];
            $go_titulos = $_POST['go_tarjeta_titulo'] ?? [];
            $go_iconos  = $_POST['go_tarjeta_icono'] ?? [];
            $go_textos  = $_POST['go_tarjeta_texto'] ?? [];
            for ($i = 0; $i < count($go_titulos); $i++) {
                $titulo_limpio = strip_tags(trim((string)$go_titulos[$i]));
                $icono_limpio  = preg_replace('/[^a-zA-Z0-9\-]/', '', trim((string)($go_iconos[$i] ?? 'bi-trophy-fill')));
                if ($titulo_limpio !== '') {
                    $variantes_nuevas[] = [
                        'titulo' => $titulo_limpio,
                        'icono'  => $icono_limpio ?: 'bi-trophy-fill',
                        'texto'  => limpiar_html_seguro((string)($go_textos[$i] ?? ''))
                    ];
                }
            }
            $db->updateSetting('go_tarjetas', json_encode($variantes_nuevas, JSON_UNESCAPED_UNICODE));

            if ($img = procesar_imagen_segura('go_hero_img_file', 'assets/img/banner', 'gameon')) {
                $db->updateSetting('go_hero_img', $img);
            }
        }

        elseif ($tab_activa === 'rugby7s') {
            $db->updateSetting('r7s_hero_badge', recibir_post('r7s_hero_badge'));
            $db->updateSetting('r7s_hero_titulo', recibir_post('r7s_hero_titulo'));
            $db->updateSetting('r7s_hero_sub', recibir_post('r7s_hero_sub'));
            $db->updateSetting('r7s_intro_titulo', recibir_post('r7s_intro_titulo'));
            $db->updateSetting('r7s_intro', recibir_post('r7s_intro', true));

            $tarjetas_r7s_nuevas = [];
            $r7s_titulos = $_POST['r7s_tarjeta_titulo'] ?? [];
            $r7s_iconos  = $_POST['r7s_tarjeta_icono'] ?? [];
            $r7s_textos  = $_POST['r7s_tarjeta_texto'] ?? [];
            for ($i = 0; $i < count($r7s_titulos); $i++) {
                $titulo_limpio = strip_tags(trim((string)$r7s_titulos[$i]));
                $icono_limpio  = preg_replace('/[^a-zA-Z0-9\-]/', '', trim((string)($r7s_iconos[$i] ?? 'bi-trophy-fill')));
                if ($titulo_limpio !== '') {
                    $tarjetas_r7s_nuevas[] = [
                        'titulo' => $titulo_limpio,
                        'icono'  => $icono_limpio ?: 'bi-trophy-fill',
                        'texto'  => limpiar_html_seguro((string)($r7s_textos[$i] ?? ''))
                    ];
                }
            }
            $db->updateSetting('r7s_tarjetas', json_encode($tarjetas_r7s_nuevas, JSON_UNESCAPED_UNICODE));

            if ($img = procesar_imagen_segura('r7s_hero_img_file', 'assets/img/banner', 'r7s')) {
                $db->updateSetting('r7s_hero_img', $img);
            }
        }

        $db->commit();
        $nombre_seccion = $nombres_pestanas[$tab_activa] ?? 'Competencias';
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

    header('Location: editar_competencias.php?tab=' . urlencode($tab_activa));
    exit;
}

try {
    $settings          = $db->getAllSettings();
    $tarjetas_actuales = json_decode($settings['esc_tarjetas'] ?? '[]', true) ?: [];
    $clubes_xvsf       = json_decode($settings['xvsf_clubes'] ?? '[]', true) ?: [];
    $clubes_xvsv       = json_decode($settings['xvsv_clubes'] ?? '[]', true) ?: [];
    $tarjetas_gameon   = json_decode($settings['go_tarjetas'] ?? '[]', true) ?: [];
    $tarjetas_r7s      = json_decode($settings['r7s_tarjetas'] ?? '[]', true) ?: [];
} catch (Throwable $e) {
    die('Error al cargar datos.');
}

$iconos_competencias = [
    'bi-trophy-fill'           => 'Trofeo / Torneo',
    'bi-lightning-charge-fill' => 'Rayo / Velocidad',
    'bi-flag-fill'             => 'Bandera / Selección',
    'bi-hand-index-thumb'      => 'Mano (Touch)',
    'bi-tags-fill'             => 'Cintas / Tags',
    'bi-diagram-3-fill'        => 'Esquema / Reglas',
    'bi-people-fill'           => 'Equipo / Mixto',
    'bi-shield-check'          => 'Seguridad',
    'bi-stopwatch-fill'        => 'Tiempo Flexible',
    'bi-globe-americas'        => 'Internacional'
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

            <h3 class="fw-bold mb-4" style="color: #bf2333;">Gestor Central de Competencias</h3>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <ul class="nav nav-tabs nav-tabs-custom flex-nowrap overflow-x-auto overflow-y-hidden" role="tablist">
                        <li class="nav-item" role="presentation"><button type="button" class="nav-link <?= $tab_activa === 'xvs_varonil' ? 'active' : '' ?> text-nowrap" data-bs-toggle="tab" data-bs-target="#xvs_v" role="tab">XVs Varonil</button></li>
                        <li class="nav-item" role="presentation"><button type="button" class="nav-link <?= $tab_activa === 'xvs_femenil' ? 'active' : '' ?> text-nowrap" data-bs-toggle="tab" data-bs-target="#xvs_f" role="tab">XVs Femenil</button></li>
                        <li class="nav-item" role="presentation"><button type="button" class="nav-link <?= $tab_activa === 'escolares' ? 'active' : '' ?> text-nowrap" data-bs-toggle="tab" data-bs-target="#escolares" role="tab">Estudiantiles</button></li>
                        <li class="nav-item" role="presentation"><button type="button" class="nav-link <?= $tab_activa === 'gameon' ? 'active' : '' ?> text-nowrap" data-bs-toggle="tab" data-bs-target="#gameon" role="tab">Game On</button></li>
                        <li class="nav-item" role="presentation"><button type="button" class="nav-link <?= $tab_activa === 'rugby7s' ? 'active' : '' ?> text-nowrap" data-bs-toggle="tab" data-bs-target="#rugby7s" role="tab">Rugby 7s</button></li>
                    </ul>
                </div>

                <div class="card-body p-4 p-md-5 bg-light">
                    <div class="tab-content">

                        <!-- PESTAÑA 1: XVs VARONIL -->
                        <div class="tab-pane fade <?= $tab_activa === 'xvs_varonil' ? 'show active' : '' ?>" id="xvs_v" role="tabpanel">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <?= csrf_campo() ?>
                                <input type="hidden" name="seccion_competencia" value="xvs_varonil">

                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">1. Banner Superior (Hero)</h5>
                                <div class="row mb-5">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label fw-bold">Etiqueta (Badge)</label>
                                                <input type="text" name="xvsv_hero_badge" class="form-control" maxlength="50" value="<?= e($settings['xvsv_hero_badge'] ?? 'Competencias') ?>">
                                            </div>
                                            <div class="col-md-8 mb-3">
                                                <label class="form-label fw-bold">Título del Banner</label>
                                                <input type="text" name="xvsv_hero_titulo" class="form-control fw-bold" maxlength="150" value="<?= e($settings['xvsv_hero_titulo'] ?? 'Liga XVs Varonil') ?>">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Subtítulo</label>
                                            <input type="text" name="xvsv_hero_sub" class="form-control" maxlength="255" value="<?= e($settings['xvsv_hero_sub'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center bg-white p-3 border rounded shadow-sm">
                                        <label class="form-label fw-bold text-muted small">Imagen de Fondo</label>
                                        <?php if (!empty($settings['xvsv_hero_img'])): ?>
                                            <div class="mb-2"><img src="../<?= e($settings['xvsv_hero_img']) ?>" class="img-thumbnail" style="max-height: 100px;" alt="Banner XVs Varonil"></div>
                                        <?php endif; ?>
                                        <input type="file" name="xvsv_hero_img_file" class="form-control form-control-sm" accept="image/jpeg, image/png, image/webp">
                                    </div>
                                </div>

                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">2. Introducción</h5>
                                <div class="mb-5">
                                    <label class="form-label fw-bold">Texto Principal</label>
                                    <textarea name="xvsv_intro" class="form-control editor-texto" rows="4"><?= e($settings['xvsv_intro'] ?? '') ?></textarea>
                                </div>

                                <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
                                    <h5 class="fw-bold text-dark mb-0">3. Clubes Participantes</h5>
                                    <button type="button" class="btn btn-sm btn-outline-dark fw-bold" onclick="agregarClubXvsV()"><i class="bi bi-plus-lg me-1"></i> Añadir Club</button>
                                </div>

                                <div class="row g-3 mb-5" id="clubes-xvsv-container">
                                    <?php foreach ($clubes_xvsv as $club): ?>
                                    <div class="col-xl-3 col-lg-4 col-md-6 club-xvsv-item">
                                        <div class="bg-white p-3 border rounded shadow-sm h-100 position-relative border-top border-4 border-success">
                                            <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" onclick="this.closest('.club-xvsv-item').remove()" title="Eliminar club">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <div class="mb-2 pe-4">
                                                <label class="form-label small fw-bold text-muted mb-1">Nombre del Club</label>
                                                <input type="text" name="xvsv_club_nombre[]" class="form-control form-control-sm fw-bold" value="<?= e($club['nombre']) ?>" required>
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold text-muted mb-1">Estado / Ciudad</label>
                                                <input type="text" name="xvsv_club_estado[]" class="form-control form-control-sm" value="<?= e($club['estado']) ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="text-center mt-5 mb-3">
                                    <button type="submit" class="btn btn-primary px-5 py-3 shadow-lg fs-5" style="background-color: #116436; border-color: #116436; border-radius: 50px;">
                                        <i class="bi bi-save me-2"></i> Guardar XVs Varonil
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- PESTAÑA 2: XVs FEMENIL -->
                        <div class="tab-pane fade <?= $tab_activa === 'xvs_femenil' ? 'show active' : '' ?>" id="xvs_f" role="tabpanel">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <?= csrf_campo() ?>
                                <input type="hidden" name="seccion_competencia" value="xvs_femenil">

                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">1. Banner Superior (Hero)</h5>
                                <div class="row mb-5">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label fw-bold">Etiqueta (Badge)</label>
                                                <input type="text" name="xvsf_hero_badge" class="form-control" maxlength="50" value="<?= e($settings['xvsf_hero_badge'] ?? 'Primera Fuerza') ?>">
                                            </div>
                                            <div class="col-md-8 mb-3">
                                                <label class="form-label fw-bold">Título del Banner</label>
                                                <input type="text" name="xvsf_hero_titulo" class="form-control fw-bold" maxlength="150" value="<?= e($settings['xvsf_hero_titulo'] ?? 'Liga XVs Femenil') ?>">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Subtítulo</label>
                                            <input type="text" name="xvsf_hero_sub" class="form-control" maxlength="255" value="<?= e($settings['xvsf_hero_sub'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center bg-white p-3 border rounded shadow-sm">
                                        <label class="form-label fw-bold text-muted small">Imagen de Fondo</label>
                                        <?php if (!empty($settings['xvsf_hero_img'])): ?>
                                            <div class="mb-2"><img src="../<?= e($settings['xvsf_hero_img']) ?>" class="img-thumbnail" style="max-height: 100px;" alt="Banner XVs Femenil"></div>
                                        <?php endif; ?>
                                        <input type="file" name="xvsf_hero_img_file" class="form-control form-control-sm" accept="image/jpeg, image/png, image/webp">
                                    </div>
                                </div>

                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">2. Introducción</h5>
                                <div class="mb-5">
                                    <label class="form-label fw-bold">Texto Principal</label>
                                    <textarea name="xvsf_intro" class="form-control editor-texto" rows="4"><?= e($settings['xvsf_intro'] ?? '') ?></textarea>
                                </div>

                                <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
                                    <h5 class="fw-bold text-dark mb-0">3. Clubes Participantes</h5>
                                    <button type="button" class="btn btn-sm btn-outline-dark fw-bold" onclick="agregarClubXvsF()"><i class="bi bi-plus-lg me-1"></i> Añadir Club</button>
                                </div>

                                <div class="row g-3 mb-5" id="clubes-xvsf-container">
                                    <?php foreach ($clubes_xvsf as $club): ?>
                                    <div class="col-xl-3 col-lg-4 col-md-6 club-xvsf-item">
                                        <div class="bg-white p-3 border rounded shadow-sm h-100 position-relative border-top border-4 border-success">
                                            <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" onclick="this.closest('.club-xvsf-item').remove()" title="Eliminar club">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <div class="mb-2 pe-4">
                                                <label class="form-label small fw-bold text-muted mb-1">Nombre del Club</label>
                                                <input type="text" name="xvsf_club_nombre[]" class="form-control form-control-sm fw-bold" value="<?= e($club['nombre']) ?>" required>
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold text-muted mb-1">Estado / Ciudad</label>
                                                <input type="text" name="xvsf_club_estado[]" class="form-control form-control-sm" value="<?= e($club['estado']) ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="text-center mt-5 mb-3">
                                    <button type="submit" class="btn btn-primary px-5 py-3 shadow-lg fs-5" style="background-color: #116436; border-color: #116436; border-radius: 50px;">
                                        <i class="bi bi-save me-2"></i> Guardar XVs Femenil
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- PESTAÑA 3: ESTUDIANTILES -->
                        <div class="tab-pane fade <?= $tab_activa === 'escolares' ? 'show active' : '' ?>" id="escolares" role="tabpanel">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <?= csrf_campo() ?>
                                <input type="hidden" name="seccion_competencia" value="escolares">

                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">1. Banner Superior (Hero)</h5>
                                <div class="row mb-5">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label fw-bold">Etiqueta (Badge)</label>
                                                <input type="text" name="esc_hero_badge" class="form-control" maxlength="50" value="<?= e($settings['esc_hero_badge'] ?? 'Competencias') ?>">
                                            </div>
                                            <div class="col-md-8 mb-3">
                                                <label class="form-label fw-bold">Título del Banner</label>
                                                <input type="text" name="esc_hero_titulo" class="form-control fw-bold" maxlength="150" value="<?= e($settings['esc_hero_titulo'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Subtítulo</label>
                                            <input type="text" name="esc_hero_sub" class="form-control" maxlength="255" value="<?= e($settings['esc_hero_sub'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center bg-white p-3 border rounded shadow-sm">
                                        <label class="form-label fw-bold text-muted small">Imagen de Fondo</label>
                                        <?php if (!empty($settings['esc_hero_img'])): ?>
                                            <div class="mb-2"><img src="../<?= e($settings['esc_hero_img']) ?>" class="img-thumbnail" style="max-height: 100px;" alt="Banner Estudiantiles"></div>
                                        <?php endif; ?>
                                        <input type="file" name="esc_hero_img_file" class="form-control form-control-sm" accept="image/jpeg, image/png, image/webp">
                                    </div>
                                </div>

                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">2. Introducción</h5>
                                <div class="mb-5">
                                    <label class="form-label fw-bold">Texto Principal</label>
                                    <textarea name="esc_intro" class="form-control editor-texto" rows="4"><?= e($settings['esc_intro'] ?? '') ?></textarea>
                                </div>

                                <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
                                    <h5 class="fw-bold text-dark mb-0">3. Tarjetas Informativas Dinámicas</h5>
                                    <button type="button" class="btn btn-sm btn-outline-dark fw-bold" onclick="agregarTarjeta()"><i class="bi bi-plus-lg me-1"></i> Añadir Tarjeta</button>
                                </div>

                                <div class="row g-4 mb-5" id="tarjetas-container">
                                    <?php
                                    $colores = ['primary', 'danger', 'dark', 'success', 'warning'];
                                    foreach ($tarjetas_actuales as $index => $t):
                                        $color = $colores[$index % count($colores)];
                                        $uniqueId = 'editor_tarjeta_' . $index;
                                    ?>
                                    <div class="col-lg-4 col-md-6 tarjeta-item">
                                        <div class="bg-white p-3 border rounded shadow-sm h-100 position-relative border-top border-4 border-<?= $color ?>">
                                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 shadow-sm" onclick="this.closest('.tarjeta-item').remove()" title="Eliminar tarjeta">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <label class="form-label fw-bold text-<?= $color ?> small text-uppercase">Tarjeta <?= $index + 1 ?></label>
                                            <input type="text" name="tarjeta_titulo[]" class="form-control fw-bold mb-2 text-center" value="<?= e($t['titulo']) ?>" placeholder="Título" required>
                                            <textarea name="tarjeta_texto[]" id="<?= $uniqueId ?>" class="form-control editor-dinamico" rows="6"><?= e($t['texto']) ?></textarea>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="text-center mt-5 mb-3">
                                    <button type="submit" class="btn btn-primary px-5 py-3 shadow-lg fs-5" style="background-color: #116436; border-color: #116436; border-radius: 50px;">
                                        <i class="bi bi-save me-2"></i> Guardar Estudiantiles
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- PESTAÑA 4: GAME ON -->
                        <div class="tab-pane fade <?= $tab_activa === 'gameon' ? 'show active' : '' ?>" id="gameon" role="tabpanel">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <?= csrf_campo() ?>
                                <input type="hidden" name="seccion_competencia" value="gameon">

                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">1. Banner Superior (Hero)</h5>
                                <div class="row mb-5">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label fw-bold">Etiqueta (Badge)</label>
                                                <input type="text" name="go_hero_badge" class="form-control" maxlength="50" value="<?= e($settings['go_hero_badge'] ?? 'COMPETENCIAS') ?>">
                                            </div>
                                            <div class="col-md-8 mb-3">
                                                <label class="form-label fw-bold">Título del Banner</label>
                                                <input type="text" name="go_hero_titulo" class="form-control fw-bold" maxlength="150" value="<?= e($settings['go_hero_titulo'] ?? 'Game On México') ?>">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Subtítulo</label>
                                            <input type="text" name="go_hero_sub" class="form-control" maxlength="255" value="<?= e($settings['go_hero_sub'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center bg-white p-3 border rounded shadow-sm">
                                        <label class="form-label fw-bold text-muted small">Imagen de Fondo</label>
                                        <?php if (!empty($settings['go_hero_img'])): ?>
                                            <div class="mb-2"><img src="../<?= e($settings['go_hero_img']) ?>" class="img-thumbnail" style="max-height: 100px;" alt="Banner Game On"></div>
                                        <?php endif; ?>
                                        <input type="file" name="go_hero_img_file" class="form-control form-control-sm" accept="image/jpeg, image/png, image/webp">
                                    </div>
                                </div>

                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">2. Introducción</h5>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Título de la Sección</label>
                                    <input type="text" name="go_intro_titulo" class="form-control fw-bold" value="<?= e($settings['go_intro_titulo'] ?? '¿Qué es Game On?') ?>">
                                </div>
                                <div class="mb-5">
                                    <label class="form-label fw-bold">Texto Principal</label>
                                    <textarea name="go_intro" class="form-control editor-texto" rows="4"><?= e($settings['go_intro'] ?? '') ?></textarea>
                                </div>

                                <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
                                    <h5 class="fw-bold text-dark mb-0">3. Variantes de Juego (Tarjetas Dinámicas)</h5>
                                    <button type="button" class="btn btn-sm btn-outline-dark fw-bold" onclick="agregarTarjetaGameOn()"><i class="bi bi-plus-lg me-1"></i> Añadir Variante</button>
                                </div>

                                <div class="row g-4 mb-5" id="tarjetas-gameon-container">
                                    <?php
                                    $colores_go = ['success', 'danger', 'dark', 'primary', 'warning'];
                                    foreach ($tarjetas_gameon as $index => $t):
                                        $color = $colores_go[$index % count($colores_go)];
                                        $uniqueId = 'editor_go_' . $index;
                                    ?>
                                    <div class="col-lg-4 col-md-6 tarjeta-go-item">
                                        <div class="bg-white p-3 border rounded shadow-sm h-100 position-relative border-top border-4 border-<?= $color ?>">
                                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 shadow-sm" onclick="this.closest('.tarjeta-go-item').remove()" title="Eliminar variante">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <label class="form-label fw-bold text-<?= $color ?> small text-uppercase">Variante <?= $index + 1 ?></label>
                                            <input type="text" name="go_tarjeta_titulo[]" class="form-control fw-bold mb-2 text-center" value="<?= e($t['titulo']) ?>" placeholder="Título" required>
                                            <select name="go_tarjeta_icono[]" class="form-select form-select-sm mb-2">
                                                <?php foreach ($iconos_competencias as $clase => $etiqueta): ?>
                                                    <option value="<?= e($clase) ?>" <?= ($t['icono'] ?? '') === $clase ? 'selected' : '' ?>><?= e($etiqueta) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <textarea name="go_tarjeta_texto[]" id="<?= $uniqueId ?>" class="form-control editor-dinamico" rows="6"><?= e($t['texto']) ?></textarea>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="text-center mt-5 mb-3">
                                    <button type="submit" class="btn btn-primary px-5 py-3 shadow-lg fs-5" style="background-color: #116436; border-color: #116436; border-radius: 50px;">
                                        <i class="bi bi-save me-2"></i> Guardar Game On
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- PESTAÑA 5: RUGBY 7S -->
                        <div class="tab-pane fade <?= $tab_activa === 'rugby7s' ? 'show active' : '' ?>" id="rugby7s" role="tabpanel">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <?= csrf_campo() ?>
                                <input type="hidden" name="seccion_competencia" value="rugby7s">

                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">1. Banner Superior (Hero)</h5>
                                <div class="row mb-5">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label fw-bold">Etiqueta (Badge)</label>
                                                <input type="text" name="r7s_hero_badge" class="form-control" maxlength="50" value="<?= e($settings['r7s_hero_badge'] ?? 'COMPETENCIAS') ?>">
                                            </div>
                                            <div class="col-md-8 mb-3">
                                                <label class="form-label fw-bold">Título del Banner</label>
                                                <input type="text" name="r7s_hero_titulo" class="form-control fw-bold" maxlength="150" value="<?= e($settings['r7s_hero_titulo'] ?? 'Rugby 7s México') ?>">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Subtítulo</label>
                                            <input type="text" name="r7s_hero_sub" class="form-control" maxlength="255" value="<?= e($settings['r7s_hero_sub'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center bg-white p-3 border rounded shadow-sm">
                                        <label class="form-label fw-bold text-muted small">Imagen de Fondo</label>
                                        <?php if (!empty($settings['r7s_hero_img'])): ?>
                                            <div class="mb-2"><img src="../<?= e($settings['r7s_hero_img']) ?>" class="img-thumbnail" style="max-height: 100px;" alt="Banner Rugby 7s"></div>
                                        <?php endif; ?>
                                        <input type="file" name="r7s_hero_img_file" class="form-control form-control-sm" accept="image/jpeg, image/png, image/webp">
                                    </div>
                                </div>

                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">2. Introducción</h5>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Título de la Sección</label>
                                    <input type="text" name="r7s_intro_titulo" class="form-control fw-bold" value="<?= e($settings['r7s_intro_titulo'] ?? '¿Qué es el Rugby 7s?') ?>">
                                </div>
                                <div class="mb-5">
                                    <label class="form-label fw-bold">Texto Principal</label>
                                    <textarea name="r7s_intro" class="form-control editor-texto" rows="4"><?= e($settings['r7s_intro'] ?? '') ?></textarea>
                                </div>

                                <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
                                    <h5 class="fw-bold text-dark mb-0">3. Bloques Destacados (Tarjetas Dinámicas)</h5>
                                    <button type="button" class="btn btn-sm btn-outline-dark fw-bold" onclick="agregarTarjetaR7s()"><i class="bi bi-plus-lg me-1"></i> Añadir Tarjeta</button>
                                </div>

                                <div class="row g-4 mb-5" id="tarjetas-r7s-container">
                                    <?php
                                    $colores_r7s = ['success', 'danger', 'dark', 'primary', 'warning'];
                                    foreach ($tarjetas_r7s as $index => $t):
                                        $color = $colores_r7s[$index % count($colores_r7s)];
                                        $uniqueId = 'editor_r7s_' . $index;
                                    ?>
                                    <div class="col-lg-4 col-md-6 tarjeta-r7s-item">
                                        <div class="bg-white p-3 border rounded shadow-sm h-100 position-relative border-top border-4 border-<?= $color ?>">
                                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 shadow-sm" onclick="this.closest('.tarjeta-r7s-item').remove()" title="Eliminar tarjeta">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <label class="form-label fw-bold text-<?= $color ?> small text-uppercase">Tarjeta <?= $index + 1 ?></label>
                                            <input type="text" name="r7s_tarjeta_titulo[]" class="form-control fw-bold mb-2 text-center" value="<?= e($t['titulo']) ?>" placeholder="Título" required>
                                            <select name="r7s_tarjeta_icono[]" class="form-select form-select-sm mb-2">
                                                <?php foreach ($iconos_competencias as $clase => $etiqueta): ?>
                                                    <option value="<?= e($clase) ?>" <?= ($t['icono'] ?? '') === $clase ? 'selected' : '' ?>><?= e($etiqueta) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <textarea name="r7s_tarjeta_texto[]" id="<?= $uniqueId ?>" class="form-control editor-dinamico" rows="6"><?= e($t['texto']) ?></textarea>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="text-center mt-5 mb-3">
                                    <button type="submit" class="btn btn-primary px-5 py-3 shadow-lg fs-5" style="background-color: #116436; border-color: #116436; border-radius: 50px;">
                                        <i class="bi bi-save me-2"></i> Guardar Rugby 7s
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
    const opcionesIconosHtml = `
        <option value="bi-trophy-fill">Trofeo / Torneo</option>
        <option value="bi-lightning-charge-fill">Rayo / Velocidad</option>
        <option value="bi-flag-fill">Bandera / Selección</option>
        <option value="bi-hand-index-thumb">Mano (Touch)</option>
        <option value="bi-tags-fill">Cintas / Tags</option>
        <option value="bi-diagram-3-fill">Esquema / Reglas</option>
        <option value="bi-people-fill">Equipo / Mixto</option>
        <option value="bi-shield-check">Seguridad</option>
        <option value="bi-stopwatch-fill">Tiempo Flexible</option>
        <option value="bi-globe-americas">Internacional</option>
    `;

    function iniciarEditor(selector) {
        if (typeof tinymce === 'undefined') return;
        tinymce.init({
            selector: selector,
            height: 220,
            menubar: false,
            plugins: 'lists link',
            toolbar: 'bold italic | alignleft aligncenter alignright | bullist numlist | removeformat',
            branding: false,
            promotion: false,
            language: 'es',
            language_url: 'https://cdn.jsdelivr.net/npm/tinymce-i18n@23.10.9/langs6/es.min.js'
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        iniciarEditor('.editor-dinamico');
    });

    function agregarTarjeta() {
        const container = document.getElementById('tarjetas-container');
        const uniqueId = 'editor_nuevo_' + Date.now();
        const num = container.querySelectorAll('.tarjeta-item').length + 1;
        const cardHtml = `
            <div class="col-lg-4 col-md-6 tarjeta-item">
                <div class="bg-white p-3 border rounded shadow-sm h-100 position-relative border-top border-4 border-info">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 shadow-sm" onclick="this.closest('.tarjeta-item').remove()" title="Eliminar tarjeta">
                        <i class="bi bi-trash"></i>
                    </button>
                    <label class="form-label fw-bold text-info small text-uppercase">Nueva Tarjeta ${num}</label>
                    <input type="text" name="tarjeta_titulo[]" class="form-control fw-bold mb-2 text-center" placeholder="Título de la tarjeta" required>
                    <textarea name="tarjeta_texto[]" id="${uniqueId}" class="form-control" rows="6"></textarea>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', cardHtml);
        iniciarEditor('#' + uniqueId);
    }

    function agregarTarjetaGameOn() {
        const container = document.getElementById('tarjetas-gameon-container');
        const uniqueId = 'editor_go_nuevo_' + Date.now();
        const num = container.querySelectorAll('.tarjeta-go-item').length + 1;
        const cardHtml = `
            <div class="col-lg-4 col-md-6 tarjeta-go-item">
                <div class="bg-white p-3 border rounded shadow-sm h-100 position-relative border-top border-4 border-info">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 shadow-sm" onclick="this.closest('.tarjeta-go-item').remove()" title="Eliminar variante">
                        <i class="bi bi-trash"></i>
                    </button>
                    <label class="form-label fw-bold text-info small text-uppercase">Nueva Variante ${num}</label>
                    <input type="text" name="go_tarjeta_titulo[]" class="form-control fw-bold mb-2 text-center" placeholder="Título de la variante" required>
                    <select name="go_tarjeta_icono[]" class="form-select form-select-sm mb-2">${opcionesIconosHtml}</select>
                    <textarea name="go_tarjeta_texto[]" id="${uniqueId}" class="form-control" rows="6"></textarea>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', cardHtml);
        iniciarEditor('#' + uniqueId);
    }

    function agregarTarjetaR7s() {
        const container = document.getElementById('tarjetas-r7s-container');
        const uniqueId = 'editor_r7s_nuevo_' + Date.now();
        const num = container.querySelectorAll('.tarjeta-r7s-item').length + 1;
        const cardHtml = `
            <div class="col-lg-4 col-md-6 tarjeta-r7s-item">
                <div class="bg-white p-3 border rounded shadow-sm h-100 position-relative border-top border-4 border-info">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 shadow-sm" onclick="this.closest('.tarjeta-r7s-item').remove()" title="Eliminar tarjeta">
                        <i class="bi bi-trash"></i>
                    </button>
                    <label class="form-label fw-bold text-info small text-uppercase">Nueva Tarjeta ${num}</label>
                    <input type="text" name="r7s_tarjeta_titulo[]" class="form-control fw-bold mb-2 text-center" placeholder="Título de la tarjeta" required>
                    <select name="r7s_tarjeta_icono[]" class="form-select form-select-sm mb-2">${opcionesIconosHtml}</select>
                    <textarea name="r7s_tarjeta_texto[]" id="${uniqueId}" class="form-control" rows="6"></textarea>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', cardHtml);
        iniciarEditor('#' + uniqueId);
    }

    function agregarClubXvsV() {
        const container = document.getElementById('clubes-xvsv-container');
        const clubHtml = `
            <div class="col-xl-3 col-lg-4 col-md-6 club-xvsv-item">
                <div class="bg-white p-3 border rounded shadow-sm h-100 position-relative border-top border-4 border-info">
                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" onclick="this.closest('.club-xvsv-item').remove()" title="Eliminar club">
                        <i class="bi bi-trash"></i>
                    </button>
                    <div class="mb-2 pe-4">
                        <label class="form-label small fw-bold text-info mb-1">Nombre del Club</label>
                        <input type="text" name="xvsv_club_nombre[]" class="form-control form-control-sm fw-bold" placeholder="Ej. Black Thunder RC" required>
                    </div>
                    <div>
                        <label class="form-label small fw-bold text-info mb-1">Estado / Ciudad</label>
                        <input type="text" name="xvsv_club_estado[]" class="form-control form-control-sm" placeholder="Ej. Ciudad de México" required>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', clubHtml);
    }

    function agregarClubXvsF() {
        const container = document.getElementById('clubes-xvsf-container');
        const clubHtml = `
            <div class="col-xl-3 col-lg-4 col-md-6 club-xvsf-item">
                <div class="bg-white p-3 border rounded shadow-sm h-100 position-relative border-top border-4 border-info">
                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" onclick="this.closest('.club-xvsf-item').remove()" title="Eliminar club">
                        <i class="bi bi-trash"></i>
                    </button>
                    <div class="mb-2 pe-4">
                        <label class="form-label small fw-bold text-info mb-1">Nombre del Club</label>
                        <input type="text" name="xvsf_club_nombre[]" class="form-control form-control-sm fw-bold" placeholder="Ej. Pumas UNAM" required>
                    </div>
                    <div>
                        <label class="form-label small fw-bold text-info mb-1">Estado / Ciudad</label>
                        <input type="text" name="xvsf_club_estado[]" class="form-control form-control-sm" placeholder="Ej. Ciudad de México" required>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', clubHtml);
    }
</script>
</body>
</html>