<?php
// admin/usuario_form.php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$accion = $id ? 'Editar' : 'Agregar';
$custom_title = "$accion Usuario";

$mensaje = ''; $tipo_mensaje = '';

// Variables por defecto
$nombre = ''; 
$email = ''; 
$rol = 'Editor';

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM admin_usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $nombre = $usuario['nombre'];
        $email = $usuario['email'];
        $rol = $usuario['rol'];
    } else {
        header("Location: ver_usuarios.php"); exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitización
    $nombre_post = recibir_post('nombre');
    $email_post = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $rol_post = ($_POST['rol'] === 'SuperAdmin') ? 'SuperAdmin' : 'Editor';
    $password_post = $_POST['password'] ?? '';

    if (!$email_post) {
        $mensaje = 'El formato del correo electrónico no es válido.';
        $tipo_mensaje = 'danger';
    } elseif (!$id && empty($password_post)) {
        $mensaje = 'Debes asignar una contraseña para el nuevo usuario.';
        $tipo_mensaje = 'danger';
    }

    if (empty($mensaje)) {
        try {
            if ($id) {
                // Modo Edición: Actualizar contraseña solo si el campo no viene vacío
                if (!empty($password_post)) {
                    $hash = password_hash($password_post, PASSWORD_DEFAULT);
                    $sql = "UPDATE admin_usuarios SET nombre=?, email=?, rol=?, password_hash=? WHERE id=?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$nombre_post, $email_post, $rol_post, $hash, $id]);
                    $mensaje = 'Usuario y contraseña actualizados correctamente.';
                } else {
                    $sql = "UPDATE admin_usuarios SET nombre=?, email=?, rol=? WHERE id=?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$nombre_post, $email_post, $rol_post, $id]);
                    $mensaje = 'Datos del usuario actualizados correctamente (contraseña conservada).';
                }
            } else {
                // Modo Creación
                $hash = password_hash($password_post, PASSWORD_DEFAULT);
                $sql = "INSERT INTO admin_usuarios (nombre, email, rol, password_hash) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nombre_post, $email_post, $rol_post, $hash]);
                $mensaje = 'Usuario creado exitosamente.';
                
                $nombre_post = ''; $email_post = ''; $rol_post = 'Editor';
            }
            
            $tipo_mensaje = 'success';
            $nombre = $nombre_post; $email = $email_post; $rol = $rol_post;

        } catch (PDOException $e) {
            // Manejar error de correo duplicado a nivel de motor de Base de Datos
            if ($e->getCode() == 23000) {
                $mensaje = "Error: El correo '$email_post' ya está asignado a otro administrador.";
            } else {
                $mensaje = "Error de base de datos: " . $e->getMessage();
            }
            $tipo_mensaje = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            
            <div class="mb-4">
                <a href="ver_usuarios.php" class="btn btn-light border btn-sm shadow-sm">
                    <i class="bi bi-arrow-left"></i> Volver a la lista
                </a>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show shadow-sm" role="alert">
                    <?= $mensaje ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="form-card">
                        <form method="POST" autocomplete="off">
                            <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Información del Administrador</h5>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">Nombre Completo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-secondary"></i></span>
                                    <input type="text" name="nombre" class="form-control border-start-0" value="<?= e($nombre) ?>" required placeholder="Ej. Juan Pérez">
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-7">
                                    <label class="form-label fw-bold text-dark">Correo Electrónico (Login)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-secondary"></i></span>
                                        <input type="email" name="email" class="form-control border-start-0" value="<?= e($email) ?>" required placeholder="correo@ejemplo.com">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-bold text-dark">Nivel de Privilegios</label>
                                    <select name="rol" class="form-select">
                                        <option value="Editor" <?= $rol === 'Editor' ? 'selected' : '' ?>>Editor (Normal)</option>
                                        <option value="SuperAdmin" <?= $rol === 'SuperAdmin' ? 'selected' : '' ?>>Super Admin (Total)</option>
                                    </select>
                                </div>
                            </div>

                            <hr class="text-muted my-4">

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">Contraseña de Acceso</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-secondary"></i></span>
                                    <!-- autocomplete="new-password" evita que el navegador llene los campos por error -->
                                    <input type="password" name="password" class="form-control border-start-0" <?= !$id ? 'required' : '' ?> autocomplete="new-password" placeholder="<?= $id ? 'Escribe aquí solo si deseas cambiarla...' : 'Asigna una contraseña...' ?>">
                                </div>
                                <?php if ($id): ?>
                                    <div class="form-text text-primary fw-semibold"><i class="bi bi-info-circle-fill me-1"></i> Como estás editando, si dejas este campo vacío se mantendrá la contraseña actual.</div>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid gap-2 mt-5">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm" style="background-color: #116436; border-color: #116436;">
                                    <i class="bi bi-save me-2"></i> Guardar Usuario
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
</body>
</html>