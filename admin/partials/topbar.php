<?php
// admin/partials/topbar.php
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    http_response_code(403);
    exit('Acceso denegado');
}

$escape_fn = static fn($val): string => htmlspecialchars((string)$val, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');

$nombre_crudo  = trim((string)($_SESSION['admin_nombre'] ?? 'Admin'));
$inicial_cruda = mb_strtoupper(mb_substr($nombre_crudo !== '' ? $nombre_crudo : 'A', 0, 1, 'UTF-8'), 'UTF-8');
$rol_crudo     = ($_SESSION['admin_rol'] ?? '') === 'SuperAdmin' ? 'Super Admin' : (isset($_SESSION['admin_rol']) ? 'Editor' : 'Administrador');
$id_sesion     = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : 0;

$titulo_topbar = $escape_fn($custom_title ?? 'Resumen del Sistema');
$nombre_admin  = $escape_fn($nombre_crudo !== '' ? $nombre_crudo : 'Admin');
$inicial_admin = $escape_fn($inicial_cruda);
$rol_admin     = $escape_fn($rol_crudo);
?>
<header class="top-navbar">
    <div class="d-flex align-items-center min-w-0 flex-grow-1 me-3">
        <!-- Botón hamburguesa visible solo en móvil y tablet -->
        <button class="btn btn-outline-success d-lg-none me-3 px-2 py-1 flex-shrink-0"
                id="btnToggleSidebar"
                type="button"
                aria-controls="adminSidebar"
                aria-expanded="false"
                aria-label="Abrir menú de navegación">
            <i class="bi bi-list fs-4" aria-hidden="true"></i>
        </button>
        <h5 class="mb-0 fw-bold topbar-title"><?= $titulo_topbar ?></h5>
    </div>

    <div class="d-flex align-items-center gap-2 gap-md-3 flex-shrink-0">
        <!-- Acceso directo al portal público en escritorio -->
        <<!-- a href="../index.php"
           target="_blank"
           rel="noopener noreferrer"
           class="btn btn-sm btn-light border text-secondary fw-semibold d-none d-md-inline-flex align-items-center shadow-sm"
           title="Abrir portal público en nueva pestaña">
            <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i> Ver sitio
        </a> -->

        <!-- Menú de perfil Bootstrap 5.3 -->
        <div class="dropdown">
            <button class="btn p-0 border-0 d-flex align-items-center text-start bg-transparent"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    aria-label="Menú de cuenta de <?= $nombre_admin ?>">
                <div class="me-3 text-end d-none d-sm-block">
                    <div class="fw-bold text-dark lh-sm" style="font-size: 0.9rem;"><?= $nombre_admin ?></div>
                    <div class="text-muted" style="font-size: 0.75rem;"><?= $rol_admin ?></div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0 shadow-sm"
                     style="width: 40px; height: 40px; background-color: #116436;"
                     aria-hidden="true">
                    <?= $inicial_admin ?>
                </div>
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                <li class="px-3 py-2 d-sm-none border-bottom">
                    <div class="fw-bold text-dark small"><?= $nombre_admin ?></div>
                    <div class="text-muted" style="font-size: 0.75rem;"><?= $rol_admin ?></div>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="../index.php" target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-globe2 me-2 text-secondary" aria-hidden="true"></i>Ver portal público
                    </a>
                </li>
                <?php if ($id_sesion > 0 && ($_SESSION['admin_rol'] ?? '') === 'SuperAdmin'): ?>
                <li>
                    <a class="dropdown-item py-2" href="usuario_form.php?id=<?= $id_sesion ?>">
                        <i class="bi bi-person-gear me-2 text-secondary" aria-hidden="true"></i>Mi cuenta
                    </a>
                </li>
                <?php endif; ?>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <a class="dropdown-item py-2 text-danger fw-semibold" href="logout.php">
                        <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Cerrar sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>