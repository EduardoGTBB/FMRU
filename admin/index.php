<?php
// admin/index.php
require_once __DIR__ . '/config/seguridad.php';
iniciar_sesion_segura();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: inicio.php');
    exit;
}

require_once __DIR__ . '/config/db.php';
global $db;

$error = '';
$email_previo = '';

// OWASP ASVS: Control de fuerza bruta ligero en sesión (5 intentos -> 5 min de espera)
$_SESSION['login_intentos'] = $_SESSION['login_intentos'] ?? 0;
$_SESSION['login_bloqueo_hasta'] = $_SESSION['login_bloqueo_hasta'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_previo = trim((string)($_POST['email'] ?? ''));
    $email = filter_var(strtolower($email_previo), FILTER_VALIDATE_EMAIL);
    $password = (string)($_POST['password'] ?? '');

    if (time() < $_SESSION['login_bloqueo_hasta']) {
        $segundos_restantes = $_SESSION['login_bloqueo_hasta'] - time();
        $minutos = (int)ceil($segundos_restantes / 60);
        $error = "Demasiados intentos fallidos. Intenta nuevamente en {$minutos} min.";
    } elseif (!validar_csrf()) {
        $error = 'La sesión del formulario caducó. Intenta iniciar sesión nuevamente.';
    } elseif ($email && $password !== '') {
        try {
            $user = $db->getAdminByEmail($email);
            // Hash señuelo constante para evitar enumeración de correos por diferencia de tiempo (Timing Attack)
            $hash_objetivo = $user['password_hash'] ?? '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
            $credenciales_validas = password_verify($password, $hash_objetivo) && !empty($user);

            if ($credenciales_validas) {
                session_regenerate_id(true);
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                $_SESSION['login_intentos'] = 0;
                $_SESSION['login_bloqueo_hasta'] = 0;

                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id']        = (int)$user['id'];
                $_SESSION['admin_nombre']    = $user['nombre'];
                $_SESSION['admin_rol']       = $user['rol'];

                header('Location: inicio.php');
                exit;
            } else {
                $_SESSION['login_intentos']++;
                if ($_SESSION['login_intentos'] >= 5) {
                    $_SESSION['login_bloqueo_hasta'] = time() + 300;
                    $error = 'Cuenta protegida temporalmente por múltiples intentos. Espera 5 minutos.';
                } else {
                    $error = 'Credenciales incorrectas.';
                }
            }
        } catch (Throwable $e) {
            $error = 'Servicio de autenticación temporalmente no disponible.';
        }
    } else {
        $error = 'Por favor, ingresa un correo válido y tu contraseña.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Admin FMRU</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>
<body class="login-page px-3">
<div class="login-card">
    <div class="login-header">
        <img src="../assets/img/logo/rugby-mexico-logo.png" alt="Logo FMRU">
        <h4 class="fw-bold" style="color: #111111;">Panel de Administración</h4>
        <p class="text-muted small">Ingresa tus credenciales para continuar</p>
    </div>

    <div class="card-body p-4 pt-2">
        <?php if ($error !== ''): ?>
            <div class="alert alert-danger small fw-bold text-center" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="" autocomplete="on">
            <?= csrf_campo() ?>
            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary" for="loginEmail">Correo Electrónico</label>
                <input type="email" name="email" id="loginEmail" class="form-control form-control-lg bg-light" value="<?= e($email_previo) ?>" required maxlength="100" autocomplete="username">
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold text-secondary" for="loginPassword">Contraseña</label>
                <input type="password" name="password" id="loginPassword" class="form-control form-control-lg bg-light" required maxlength="72" autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-fmru w-100 rounded-3">INICIAR SESIÓN</button>
        </form>
    </div>
</div>
</body>
</html>