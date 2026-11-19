<?php
// admin/logout.php

// 1. Inicializar la sesión para poder destruirla
session_start();

// 2. Vaciar todas las variables de sesión actuales
$_SESSION = array();

// 3. Destruir la cookie de sesión en el navegador del usuario (OWASP)
// Esto asegura que el ID de sesión anterior no pueda ser reutilizado
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Destruir la sesión físicamente en el servidor
session_destroy();

// 5. Redirigir a la página principal pública (saliendo de la carpeta admin)
header("Location: ../index.php");
exit;
?>