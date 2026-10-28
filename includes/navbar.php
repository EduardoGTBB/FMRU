<?php
// includes/navbar.php
// Se asegura de que no se pueda acceder directamente a este archivo por seguridad (OWASP)
if (basename($_SERVER['PHP_SELF']) == 'navbar.php') {
    die('Acceso denegado');
}
?>
<!-- BARRA SUPERIOR Y MENÚ -->
<nav class="navbar navbar-expand-lg navbar-custom sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand bg-white p-2 rounded" href="index.php">
            <img src="assets/img/logo/rugby-mexico-logo.png" alt="Logo FMRU" height="50" style="object-fit: contain;">
        </a>
        <button class="navbar-toggler border-white text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="bi bi-list fs-1"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Federación</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="quienes-somos.php">Quiénes Somos (Misión/Visión)</a></li>
                        <li><a class="dropdown-item" href="historia-presidencia.php">Historia + Presidencia</a></li>
                        <li><a class="dropdown-item" href="mesa-directiva.php">Mesa Directiva + Plan</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Competencias</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="xvs-varonil.php">XVs Varonil</a></li>
                        <li><a class="dropdown-item" href="xvs-femenil.php">XVs Femenil</a></li>
                        <li><a class="dropdown-item" href="#">Game On</a></li>
                        <li><a class="dropdown-item" href="#">Escolares / Universitarios</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Serpientes</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Femenil</a></li>
                        <li><a class="dropdown-item" href="#">Varonil</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="documentos.php">Documentos</a></li>
            </ul>
            <div class="d-flex">
                <a href="https://sportsplus.app/public/6859/registration" target="_blank" class="btn btn-rojo rounded-pill px-4"><i class="bi bi-person-circle me-1"></i> Regístrate / Ingresar</a>
            </div>
        </div>
    </div>
</nav>