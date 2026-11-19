<?php
// admin/partials/sidebar.php
if (basename($_SERVER['PHP_SELF']) == 'sidebar.php') {
    die('Acceso denegado');
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="logo-container">
        <img src="../assets/img/logo/rugby-mexico-logo.png" alt="Logo FMRU">
    </div>
    <div class="p-3 small text-uppercase fw-bold opacity-75" style="letter-spacing: 1px;">Menú Principal</div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'inicio.php') ? 'active' : '' ?>" href="inicio.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'ver_banners.php' || $current_page == 'banner_form.php') ? 'active' : '' ?>" href="ver_banners.php">
                <i class="bi bi-images"></i> Banners Inicio
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'gestor_contenidos.php' || $current_page == 'noticia_form.php') ? 'active' : '' ?>" href="gestor_contenidos.php">
                <i class="bi bi-newspaper"></i> Gestor de Contenidos
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'ver_sponsors.php' || $current_page == 'sponsor_form.php') ? 'active' : '' ?>" href="ver_sponsors.php">
                <i class="bi bi-shield-shaded"></i> Sponsors
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'ver_jornadas.php') ? 'active' : '' ?>" href="#">
                <i class="bi bi-calendar-week"></i> Partidos y Jornadas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'editar_institucional.php') ? 'active' : '' ?>" href="editar_institucional.php">
                <i class="bi bi-bank"></i> Misión y Visión
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'ver_documentos.php') ? 'active' : '' ?>" href="#">
                <i class="bi bi-file-earmark-pdf"></i> Repositorio (Docs)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'ver_usuarios.php' || $current_page == 'usuario_form.php') ? 'active' : '' ?>" href="ver_usuarios.php">
                <i class="bi bi-people"></i> Usuarios
            </a>
        </li>
        <li class="nav-item mt-4">
            <a class="nav-link text-danger" href="logout.php">
                <i class="bi bi-box-arrow-left"></i> Cerrar Sesión
            </a>
        </li>
    </ul>
</div>