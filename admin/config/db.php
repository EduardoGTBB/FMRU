<?php
// admin/config/db.php
if (basename($_SERVER['PHP_SELF']) == 'db.php') {
    die('Acceso denegado');
}

$host = 'localhost';
$dbname = 'fmru';
$db_user = 'root'; 
$db_pass = 'root'; // Ajusta según tu servidor

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión con la base de datos.");
}

// Función global para limpiar salidas (XSS)
if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars((string)$string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
// Función global para recibir POST de forma segura preservando acentos
if (!function_exists('recibir_post')) {
    function recibir_post($campo, $permitir_html = false) {
        if (!isset($_POST[$campo])) return '';
        $valor = trim($_POST[$campo]);
        // OWASP: Si no permitimos HTML (ej. títulos, nombres), removemos las etiquetas
        return $permitir_html ? $valor : strip_tags($valor);
    }
}
?>