<?php
// admin/partials/topbar.php
// Protección OWASP
if (basename($_SERVER['PHP_SELF']) == 'topbar.php') {
    die('Acceso denegado');
}

// Permitir inyectar un título personalizado desde la vista principal
$titulo_topbar = isset($custom_title) ? $custom_title : "Resumen del Sistema";
$nombre_admin = htmlspecialchars($_SESSION['admin_nombre'], ENT_QUOTES, 'UTF-8');
$inicial_admin = strtoupper(substr($nombre_admin, 0, 1));
?>
<div class="top-navbar">
    <h5 class="mb-0 fw-bold" style="color: #111111;"><?= $titulo_topbar ?></h5>
    <div class="d-flex align-items-center">
        <div class="me-3 text-end">
            <div class="fw-bold" style="font-size: 0.9rem;"><?= $nombre_admin ?></div>
            <div class="text-muted" style="font-size: 0.75rem;">Administrador</div>
        </div>
        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px;">
            <?= $inicial_admin ?>
        </div>
    </div>
</div>