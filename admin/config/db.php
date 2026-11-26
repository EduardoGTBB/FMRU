<?php
// admin/config/db.php
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    http_response_code(403);
    exit('Acceso denegado');
}

$host    = getenv('FMRU_DB_HOST') ?: 'localhost';
$dbname  = getenv('FMRU_DB_NAME') ?: 'fmru';
$db_user = getenv('FMRU_DB_USER') ?: 'root';
$db_pass = getenv('FMRU_DB_PASS') ?: 'root'; 

$opciones_pdo = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Manejo estricto de errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Ahorra 50% de RAM al no duplicar índices numéricos
    PDO::ATTR_EMULATE_PREPARES   => false,                  // OWASP: Sentencias preparadas nativas en MySQL
];

try {
    $pdo = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8mb4", $db_user, $db_pass, $opciones_pdo);
} catch (PDOException $e) {
    // OWASP A05: Registrar detalle técnico en log privado sin exponer credenciales en pantalla
    error_log('Error PDO FMRU: ' . $e->getMessage());
    http_response_code(503);
    exit('Servicio temporalmente no disponible.');
}

// Función global ligera para escape de salidas contra XSS (usada en público y admin)
if (!function_exists('e')) {
    function e($string): string {
        return htmlspecialchars((string)$string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }
}

// Inyección de la capa de consultas
require_once __DIR__ . '/consultas.php';
$db = new GestorConsultas($pdo);

// Carga contextual: solo incluye seguridad administrativa si el script en ejecución vive dentro de /admin
$script_ejecutado = realpath($_SERVER['SCRIPT_FILENAME'] ?? '') ?: '';
$directorio_admin = realpath(dirname(__DIR__)) ?: '';

if ($directorio_admin !== '' && str_starts_with($script_ejecutado, $directorio_admin . DIRECTORY_SEPARATOR)) {
    require_once __DIR__ . '/seguridad.php';
}