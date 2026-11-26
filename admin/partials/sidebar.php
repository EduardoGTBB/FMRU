<?php
// admin/partials/sidebar.php
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    http_response_code(403);
    exit('Acceso denegado');
}

$current_page = basename($_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF']);
$es_super_admin = !isset($_SESSION['admin_rol']) || $_SESSION['admin_rol'] === 'SuperAdmin';

// Vistas que NO utilizan editor enriquecido (ahorra ~400 KB de red y memoria RAM)
$paginas_sin_tinymce = [
    'inicio.php',
    'gestor_contenidos.php',
    'ver_banners.php',
    'banner_form.php',
    'ver_sponsors.php',
    'sponsor_form.php',
    'ver_documentos.php',
    'documento_form.php',
    'ver_usuarios.php',
    'usuario_form.php'
];
$requiere_tinymce = !in_array($current_page, $paginas_sin_tinymce, true);
?>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar d-flex flex-column" id="adminSidebar" aria-label="Menú principal de administración">
    <div class="logo-container position-relative">
        <img src="../assets/img/logo/rugby-mexico-logo.png" alt="Logo FMRU">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2 d-lg-none" id="btnCloseSidebar" aria-label="Cerrar menú"></button>
    </div>

    <div class="p-3 small text-uppercase fw-bold opacity-75" style="letter-spacing: 1px;">Menú Principal</div>

    <ul class="nav flex-column flex-grow-1">
        <li class="nav-item">
            <?php $activo = ($current_page === 'inicio.php'); ?>
            <a class="nav-link <?= $activo ? 'active' : '' ?>" href="inicio.php" <?= $activo ? 'aria-current="page"' : '' ?>>
                <i class="bi bi-speedometer2" aria-hidden="true"></i> Dashboard
            </a>
        </li>

        <li class="nav-item">
            <?php $activo = in_array($current_page, ['ver_banners.php', 'banner_form.php'], true); ?>
            <a class="nav-link <?= $activo ? 'active' : '' ?>" href="ver_banners.php" <?= $activo ? 'aria-current="page"' : '' ?>>
                <i class="bi bi-images" aria-hidden="true"></i> Banners Inicio
            </a>
        </li>

        <li class="nav-item">
            <?php $activo = in_array($current_page, ['gestor_contenidos.php', 'noticia_form.php', 'landing_form.php'], true); ?>
            <a class="nav-link <?= $activo ? 'active' : '' ?>" href="gestor_contenidos.php" <?= $activo ? 'aria-current="page"' : '' ?>>
                <i class="bi bi-newspaper" aria-hidden="true"></i> Gestor de Contenidos
            </a>
        </li>

        <li class="nav-item">
            <?php $activo = in_array($current_page, ['ver_sponsors.php', 'sponsor_form.php'], true); ?>
            <a class="nav-link <?= $activo ? 'active' : '' ?>" href="ver_sponsors.php" <?= $activo ? 'aria-current="page"' : '' ?>>
                <i class="bi bi-shield-shaded" aria-hidden="true"></i> Sponsors
            </a>
        </li>

        <li class="nav-item">
            <?php $activo = ($current_page === 'editar_institucional.php'); ?>
            <a class="nav-link <?= $activo ? 'active' : '' ?>" href="editar_institucional.php" <?= $activo ? 'aria-current="page"' : '' ?>>
                <i class="bi bi-bank" aria-hidden="true"></i> Misión y Visión
            </a>
        </li>

        <li class="nav-item">
            <?php $activo = ($current_page === 'editar_serpientes.php'); ?>
            <a class="nav-link <?= $activo ? 'active' : '' ?>" href="editar_serpientes.php" <?= $activo ? 'aria-current="page"' : '' ?>>
                <i class="bi bi-flag-fill" aria-hidden="true"></i> Serpientes
            </a>
        </li>

        <li class="nav-item">
            <?php $activo = ($current_page === 'editar_competencias.php'); ?>
            <a class="nav-link <?= $activo ? 'active' : '' ?>" href="editar_competencias.php" <?= $activo ? 'aria-current="page"' : '' ?>>
                <i class="bi bi-trophy" aria-hidden="true"></i> Competencias
            </a>
        </li>

        <li class="nav-item">
            <?php $activo = ($current_page === 'editar_desarrollo.php'); ?>
            <a class="nav-link <?= $activo ? 'active' : '' ?>" href="editar_desarrollo.php" <?= $activo ? 'aria-current="page"' : '' ?>>
                <i class="bi bi-graph-up-arrow" aria-hidden="true"></i> Desarrollo
            </a>
        </li>

        <li class="nav-item">
            <?php $activo = in_array($current_page, ['ver_documentos.php', 'documento_form.php'], true); ?>
            <a class="nav-link <?= $activo ? 'active' : '' ?>" href="ver_documentos.php" <?= $activo ? 'aria-current="page"' : '' ?>>
                <i class="bi bi-file-earmark-pdf" aria-hidden="true"></i> Repositorio (Docs)
            </a>
        </li>

        <?php if ($es_super_admin): ?>
        <li class="nav-item">
            <?php $activo = in_array($current_page, ['ver_usuarios.php', 'usuario_form.php'], true); ?>
            <a class="nav-link <?= $activo ? 'active' : '' ?>" href="ver_usuarios.php" <?= $activo ? 'aria-current="page"' : '' ?>>
                <i class="bi bi-people" aria-hidden="true"></i> Usuarios
            </a>
        </li>
        <?php endif; ?>

        <li class="nav-item mt-auto pt-3 border-top border-white border-opacity-25">
            <a class="nav-link fw-bold" href="logout.php" style="color: #ffd6db;">
                <i class="bi bi-box-arrow-left" aria-hidden="true"></i> Cerrar Sesión
            </a>
        </li>
    </ul>
</aside>

<!-- SCRIPTS CENTRALIZADOS NO BLOQUEANTES (HTML5 defer + OWASP SRI) -->
<?php if ($requiere_tinymce): ?>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
<?php endif; ?>
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script defer src="js/admin-resiliencia.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Inicializar TinyMCE solo si la librería y el selector están presentes
    if (typeof window.tinymce !== 'undefined' && document.querySelector('.editor-texto')) {
        window.tinymce.init({
            selector: '.editor-texto',
            height: 300,
            menubar: false,
            plugins: 'lists link wordcount',
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link | removeformat',
            branding: false,
            promotion: false,
            language: 'es',
            language_url: 'https://cdn.jsdelivr.net/npm/tinymce-i18n@23.10.9/langs6/es.min.js'
        });
    }

    // 2. Control accesible del Sidebar en dispositivos móviles y cierre con Escape
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const btnOpen = document.getElementById('btnToggleSidebar');
    const btnClose = document.getElementById('btnCloseSidebar');

    function setSidebarState(abierto) {
        if (!sidebar || !overlay) return;
        sidebar.classList.toggle('show', abierto);
        overlay.classList.toggle('show', abierto);
        document.body.style.overflow = abierto ? 'hidden' : '';
        if (btnOpen) {
            btnOpen.setAttribute('aria-expanded', abierto ? 'true' : 'false');
        }
    }

    if (btnOpen) btnOpen.addEventListener('click', () => setSidebarState(!sidebar.classList.contains('show')));
    if (btnClose) btnClose.addEventListener('click', () => setSidebarState(false));
    if (overlay) overlay.addEventListener('click', () => setSidebarState(false));

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('show')) {
            setSidebarState(false);
        }
    });

    // 3. Gestión inteligente de alertas Bootstrap 5.3
    function cerrarAlertaBootstrap(alertEl) {
        if (!document.body.contains(alertEl)) return;
        if (typeof window.bootstrap !== 'undefined' && window.bootstrap.Alert) {
            window.bootstrap.Alert.getOrCreateInstance(alertEl).close();
        } else {
            alertEl.classList.remove('show');
            setTimeout(() => alertEl.remove(), 200);
        }
    }

    document.querySelectorAll('.alert.alert-success.alert-dismissible').forEach(function (alertEl) {
        setTimeout(() => cerrarAlertaBootstrap(alertEl), 3500);
    });

    document.querySelectorAll('[data-bs-toggle="tab"], [data-bs-toggle="pill"]').forEach(function (tabBtn) {
        tabBtn.addEventListener('show.bs.tab', function () {
            document.querySelectorAll('.alert.alert-dismissible').forEach(cerrarAlertaBootstrap);
        });
    });
});
</script>