<?php
// evento.php (Front Controller público)
require_once 'admin/config/db.php';

// 1. Sanitización estricta del parámetro GET (OWASP)
$slug = filter_input(INPUT_GET, 'slug', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if (!$slug) {
    header("HTTP/1.0 404 Not Found");
    die("<div style='text-align:center; padding: 50px; font-family:sans-serif;'><h2>Página no encontrada.</h2></div>");
}

// 2. Consulta segura mediante PDO
$stmt = $pdo->prepare("SELECT * FROM fmru_paginas_landing WHERE slug = ? AND activo = 1");
$stmt->execute([$slug]);
$pagina = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pagina) {
    header("HTTP/1.0 404 Not Found");
    die("<div style='text-align:center; padding: 50px; font-family:sans-serif;'><h2>El evento no existe o no está disponible.</h2></div>");
}

// 3. Desempaquetar el JSON de configuración
$config = json_decode($pagina['configuracion_json'], true);
if (!$config) {
    $config = []; // Fallback seguro si el JSON está vacío
}

// 4. Variables globales para la plantilla
$page_title = $pagina['titulo'];

// Incluir cabeceras públicas
include 'includes/head.php';
include 'includes/navbar.php';

// 5. Enrutamiento de Plantillas (Previene Local File Inclusion - LFI)
$plantilla_permitida = $pagina['plantilla'];
$ruta_plantilla = 'plantillas/' . $plantilla_permitida . '.php';

if (in_array($plantilla_permitida, ['torneo', 'informativa']) && file_exists($ruta_plantilla)) {
    include $ruta_plantilla;
} else {
    echo "<div class='container my-5 text-center'><h4>Error: Plantilla no válida o en construcción.</h4></div>";
}

// Incluir pie de página
include 'includes/footer.php';
?>