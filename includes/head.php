<?php
// includes/head.php
if (basename($_SERVER['PHP_SELF']) == 'head.php') {
    die('Acceso denegado');
}

// Configuración de título dinámico (Si no se pasa la variable $page_title, usa el por defecto)
$title = isset($page_title) ? e($page_title) . " | Federación Mexicana de Rugby" : "Rugby México | Federación Mexicana de Rugby";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>

    <!-- Ícono de la pestaña (Favicon) FMRU -->
    <link rel="icon" type="image/png" href="assets/img/logo/rugby-mexico-logo.png">
    <!-- Soporte para dispositivos móviles de Apple -->
    <link rel="apple-touch-icon" href="assets/img/logo/rugby-mexico-logo.png">
    
    <!-- Integración Tipografía Oficial: Open Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS y Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Integración Leaflet CSS (Para mapas, cargado globalmente por simplicidad) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <!-- Enlace a estilos globales -->
    <link rel="stylesheet" href="assets/css/styles.css">

    <!-- CSS adicional específico de la página (inyectado dinámicamente si existe) -->
    <?php if (isset($extra_css)): ?>
        <style>
            <?= $extra_css ?>
        </style>
    <?php endif; ?>
</head>
<body>