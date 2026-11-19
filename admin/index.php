<?php
// admin/index.php
session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: inicio.php");
    exit;
}

require_once __DIR__ . '/config/db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        try {
            $stmt = $pdo->prepare("SELECT id, nombre, rol, password_hash FROM admin_usuarios WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_nombre'] = $user['nombre'];
                $_SESSION['admin_rol'] = $user['rol'];
                
                header("Location: inicio.php");
                exit;
            } else {
                $error = "Credenciales incorrectas.";
            }
        } catch (PDOException $e) {
            $error = "Error de conexión con la base de datos."; 
        }
    } else {
        $error = "Por favor, completa todos los campos.";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>

<body class="login-page">
<div class="login-card">
    <div class="login-header">
        <img src="../assets/img/logo/rugby-mexico-logo.png" alt="Logo FMRU">
        <h4 class="fw-bold" style="color: #111111;">Panel de Administración</h4>
        <p class="text-muted small">Ingresa tus credenciales para continuar</p>
    </div>
    
    <div class="card-body p-4 pt-2">
        <?php if($error): ?>
            <div class="alert alert-danger small fw-bold text-center"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary">Correo Electrónico</label>
                <input type="email" name="email" class="form-control form-control-lg bg-light" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold text-secondary">Contraseña</label>
                <input type="password" name="password" class="form-control form-control-lg bg-light" required>
            </div>
            <button type="submit" class="btn btn-fmru w-100 rounded-3">INICIAR SESIÓN</button>
        </form>
    </div>
</div>

</body>
</html>