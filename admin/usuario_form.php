<?php
// admin/usuario_form.php
require_once __DIR__ . '/config/seguridad.php';
exigir_sesion_admin('SuperAdmin');
require_once __DIR__ . '/config/db.php';
global $db;

$es_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$id = isset($_GET['id']) ? max(0, (int)$_GET['id']) : 0;
$accion = $id ? 'Editar' : 'Agregar';
$custom_title = "$accion Usuario";

$mensaje = $_SESSION['flash_mensaje'] ?? '';
$tipo_mensaje = $_SESSION['flash_tipo'] ?? '';
unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);

$roles_permitidos = ['Editor', 'SuperAdmin'];
$nombre = '';
$email  = '';
$rol    = 'Editor';

if ($id > 0) {
    $usuario = $db->getUsuarioById($id);
    if ($usuario) {
        $nombre = $usuario['nombre'];
        $email  = $usuario['email'];
        $rol    = in_array($usuario['rol'], $roles_permitidos, true) ? $usuario['rol'] : 'Editor';
    } else {
        header('Location: ver_usuarios.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_post  = recibir_post('nombre');
    $email_raw    = strtolower(trim((string)($_POST['email'] ?? '')));
    $email_valido = filter_var($email_raw, FILTER_VALIDATE_EMAIL);
    $rol_raw      = (string)($_POST['rol'] ?? 'Editor');
    $rol_post     = in_array($rol_raw, $roles_permitidos, true) ? $rol_raw : 'Editor';

    // OWASP ASVS 2.1.4: No recortar ni alterar contraseñas antes de aplicar hash
    $password_post    = (string)($_POST['password'] ?? '');
    $password_confirm = (string)($_POST['password_confirm'] ?? '');

    if (!validar_csrf()) {
        $mensaje = 'Token de seguridad inválido. Recarga la página o intenta guardar nuevamente.';
        $tipo_mensaje = 'danger';
    } elseif ($nombre_post === '' || mb_strlen($nombre_post, 'UTF-8') < 3 || mb_strlen($nombre_post, 'UTF-8') > 100) {
        $mensaje = 'El nombre completo debe tener entre 3 y 100 caracteres.';
        $tipo_mensaje = 'danger';
    } elseif (!$email_valido || mb_strlen($email_raw, 'UTF-8') > 100) {
        $mensaje = 'Ingresa un correo electrónico válido (máximo 100 caracteres).';
        $tipo_mensaje = 'danger';
    } elseif ($id > 0 && isset($_SESSION['admin_id']) && (int)$_SESSION['admin_id'] === $id && $rol_post !== 'SuperAdmin') {
        $mensaje = 'Por seguridad, no puedes retirar el nivel Super Admin de tu propia sesión activa.';
        $tipo_mensaje = 'danger';
    } elseif (!$id && $password_post === '') {
        $mensaje = 'Debes asignar una contraseña para el nuevo usuario.';
        $tipo_mensaje = 'danger';
    } elseif ($password_post !== '') {
        if (mb_strlen($password_post, 'UTF-8') < 8) {
            $mensaje = 'La contraseña debe contener al menos 8 caracteres.';
            $tipo_mensaje = 'danger';
        } elseif (strlen($password_post) > 72) {
            $mensaje = 'La contraseña no debe exceder 72 caracteres.';
            $tipo_mensaje = 'danger';
        } elseif (!preg_match('/[A-Za-z]/', $password_post) || !preg_match('/\d/', $password_post)) {
            $mensaje = 'La contraseña debe incluir al menos una letra y un número.';
            $tipo_mensaje = 'danger';
        } elseif (!hash_equals($password_post, $password_confirm)) {
            $mensaje = 'La confirmación de la contraseña no coincide.';
            $tipo_mensaje = 'danger';
        }
    }

    if ($mensaje === '') {
        try {
            $db->beginTransaction();

            if ($id > 0) {
                if ($password_post !== '') {
                    $hash = password_hash($password_post, PASSWORD_DEFAULT);
                    $db->updateUsuario($id, $nombre_post, $email_valido, $rol_post, $hash);
                    $mensaje = 'Usuario y contraseña actualizados correctamente.';
                } else {
                    $db->updateUsuarioSinPassword($id, $nombre_post, $email_valido, $rol_post);
                    $mensaje = 'Datos del usuario actualizados (contraseña actual conservada).';
                }

                if (isset($_SESSION['admin_id']) && (int)$_SESSION['admin_id'] === $id) {
                    $_SESSION['admin_nombre'] = $nombre_post;
                }

                $db->commit();
                $tipo_mensaje = 'success';

                if ($es_ajax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['status' => 'success', 'message' => $mensaje]);
                    exit;
                }

                $_SESSION['flash_mensaje'] = $mensaje;
                $_SESSION['flash_tipo'] = 'success';
                header('Location: usuario_form.php?id=' . $id);
                exit;

            } else {
                $hash = password_hash($password_post, PASSWORD_DEFAULT);
                $db->insertUsuario($nombre_post, $email_valido, $rol_post, $hash);
                $db->commit();

                $mensaje = 'Usuario creado exitosamente.';
                $tipo_mensaje = 'success';

                if ($es_ajax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode([
                        'status'   => 'success',
                        'message'  => $mensaje,
                        'redirect' => 'ver_usuarios.php'
                    ]);
                    exit;
                }

                $nombre = '';
                $email  = '';
                $rol    = 'Editor';
            }

        } catch (PDOException $e) {
            $db->rollBack();
            $es_duplicado = ((string)$e->getCode() === '23000') || (($e->errorInfo[1] ?? 0) === 1062);
            $mensaje = $es_duplicado
                ? 'Error: Ese correo electrónico ya está asignado a otro usuario.'
                : 'Error interno al guardar en la base de datos.';
            $tipo_mensaje = 'danger';
        } catch (Throwable $e) {
            $db->rollBack();
            $mensaje = 'Error inesperado al procesar la solicitud.';
            $tipo_mensaje = 'danger';
        }
    }

    if ($tipo_mensaje === 'danger') {
        if ($es_ajax) {
            http_response_code(422);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'danger', 'message' => $mensaje]);
            exit;
        }
        $nombre = $nombre_post;
        $email  = $email_raw;
        $rol    = $rol_post;
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
                <a href="ver_usuarios.php" class="btn btn-light border btn-sm shadow-sm">
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
                        <form method="POST" action="" autocomplete="off" id="usuarioForm">
                            <?= csrf_campo() ?>

                            <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Información del Administrador</h5>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark" for="nombreInput">Nombre Completo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-secondary"></i></span>
                                    <input type="text" name="nombre" id="nombreInput" class="form-control border-start-0" value="<?= e($nombre) ?>" required minlength="3" maxlength="100" placeholder="Ej. Juan Pérez">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-7 mb-3 mb-md-0">
                                    <label class="form-label fw-bold text-dark" for="emailInput">Correo Electrónico (Login)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-secondary"></i></span>
                                        <input type="email" name="email" id="emailInput" class="form-control border-start-0" value="<?= e($email) ?>" required maxlength="100" placeholder="correo@mexrugby.com">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-bold text-dark" for="rolSelect">Nivel de Privilegios</label>
                                    <select name="rol" id="rolSelect" class="form-select">
                                        <option value="Editor" <?= $rol === 'Editor' ? 'selected' : '' ?>>Editor (Contenido)</option>
                                        <option value="SuperAdmin" <?= $rol === 'SuperAdmin' ? 'selected' : '' ?>>Super Admin (Total)</option>
                                    </select>
                                </div>
                            </div>

                            <hr class="text-muted my-4">

                            <h6 class="fw-bold text-dark mb-3">
                                <i class="bi bi-shield-lock me-1 text-success"></i> Credenciales de Seguridad
                            </h6>

                            <div class="row mb-2">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label fw-bold text-dark" for="passwordInput">
                                        <?= $id ? 'Nueva Contraseña (Opcional)' : 'Contraseña de Acceso' ?>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-secondary"></i></span>
                                        <input type="password" name="password" id="passwordInput" class="form-control border-start-0" <?= !$id ? 'required' : '' ?> minlength="8" maxlength="72" autocomplete="new-password" data-no-draft="true" placeholder="<?= $id ? 'Dejar vacío para conservar...' : 'Mínimo 8 caracteres...' ?>">
                                        <button class="btn btn-outline-secondary btn-toggle-pwd" type="button" data-target="passwordInput" aria-label="Mostrar u ocultar contraseña">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark" for="passwordConfirmInput">Confirmar Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-check2-circle text-secondary"></i></span>
                                        <input type="password" name="password_confirm" id="passwordConfirmInput" class="form-control border-start-0" <?= !$id ? 'required' : '' ?> minlength="8" maxlength="72" autocomplete="new-password" data-no-draft="true" placeholder="Repite la contraseña...">
                                        <button class="btn btn-outline-secondary btn-toggle-pwd" type="button" data-target="passwordConfirmInput" aria-label="Mostrar u ocultar confirmación">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback d-none" id="pwdMatchError">Las contraseñas no coinciden.</div>
                                </div>
                            </div>

                            <div class="mb-3 d-none" id="pwdStrengthWrapper">
                                <div class="progress" style="height: 6px;">
                                    <div id="pwdStrengthBar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
                                </div>
                                <small id="pwdStrengthText" class="text-muted d-block mt-1"></small>
                            </div>

                            <?php if ($id): ?>
                                <div class="form-text text-primary fw-semibold mt-2">
                                    <i class="bi bi-info-circle-fill me-1"></i> Si dejas los campos de contraseña vacíos, el usuario conservará su contraseña actual.
                                </div>
                            <?php else: ?>
                                <div class="form-text text-muted mt-2">
                                    Usa mínimo 8 caracteres combinando letras y números. Por seguridad OWASP, las contraseñas nunca se almacenan en borradores locales.
                                </div>
                            <?php endif; ?>

                            <div class="d-grid gap-2 mt-5">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm" style="background-color: #116436; border-color: #116436;">
                                    <i class="bi bi-save me-2"></i> <?= $id ? 'Actualizar Usuario' : 'Guardar Usuario' ?>
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
document.addEventListener('DOMContentLoaded', function () {
    const pwdInput = document.getElementById('passwordInput');
    const pwdConfirm = document.getElementById('passwordConfirmInput');
    const matchError = document.getElementById('pwdMatchError');
    const strengthWrap = document.getElementById('pwdStrengthWrapper');
    const strengthBar = document.getElementById('pwdStrengthBar');
    const strengthText = document.getElementById('pwdStrengthText');
    const esEdicion = <?= $id > 0 ? 'true' : 'false' ?>;

    document.querySelectorAll('.btn-toggle-pwd').forEach(btn => {
        btn.addEventListener('click', function () {
            const target = document.getElementById(this.dataset.target);
            const icon = this.querySelector('i');
            if (!target) return;

            if (target.type === 'password') {
                target.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                target.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    });

    [pwdInput, pwdConfirm].forEach(inp => {
        inp.addEventListener('input', e => e.stopPropagation());
        inp.addEventListener('change', e => e.stopPropagation());

        inp.addEventListener('blur', function (e) {
            if (e.relatedTarget && e.relatedTarget.classList.contains('btn-toggle-pwd')) return;
            this.type = 'password';
            const btnIcon = this.parentElement.querySelector('.btn-toggle-pwd i');
            if (btnIcon) btnIcon.classList.replace('bi-eye-slash', 'bi-eye');
        });
    });

    function validarCredencialesUI() {
        const val = pwdInput.value;
        const conf = pwdConfirm.value;

        if (esEdicion) {
            pwdConfirm.required = (val.length > 0);
        }

        if (val.length === 0) {
            strengthWrap.classList.add('d-none');
            pwdConfirm.setCustomValidity('');
            pwdConfirm.classList.remove('is-invalid');
            matchError.classList.add('d-none');
            return;
        }

        strengthWrap.classList.remove('d-none');
        let score = 0;
        if (val.length >= 8) score += 35;
        if (/[A-Za-z]/.test(val) && /\d/.test(val)) score += 35;
        if (/[^A-Za-z0-9]/.test(val) || val.length >= 12) score += 30;

        strengthBar.style.width = score + '%';
        if (score < 70) {
            strengthBar.className = 'progress-bar bg-danger';
            strengthText.textContent = 'Débil: incluye mínimo 8 caracteres con letras y números.';
        } else if (score < 100) {
            strengthBar.className = 'progress-bar bg-warning';
            strengthText.textContent = 'Aceptable: cumple los requisitos mínimos.';
        } else {
            strengthBar.className = 'progress-bar bg-success';
            strengthText.textContent = 'Fuerte: excelente nivel de seguridad.';
        }

        if (conf.length > 0 && val !== conf) {
            pwdConfirm.setCustomValidity('Las contraseñas no coinciden.');
            pwdConfirm.classList.add('is-invalid');
            matchError.classList.remove('d-none');
        } else {
            pwdConfirm.setCustomValidity('');
            pwdConfirm.classList.remove('is-invalid');
            matchError.classList.add('d-none');
        }
    }

    pwdInput.addEventListener('input', validarCredencialesUI);
    pwdConfirm.addEventListener('input', validarCredencialesUI);

    const toastContainer = document.getElementById('fmruToastContainer');
    if (toastContainer && window.MutationObserver) {
        new MutationObserver(mutations => {
            mutations.forEach(m => {
                m.addedNodes.forEach(node => {
                    if (node.nodeType === 1 && node.classList.contains('text-bg-success')) {
                        pwdInput.value = '';
                        pwdConfirm.value = '';
                        validarCredencialesUI();
                    }
                });
            });
        }).observe(toastContainer, { childList: true });
    }
});
</script>
</body>
</html>