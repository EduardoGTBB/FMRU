<?php
// admin/config/seguridad.php
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    http_response_code(403);
    exit('Acceso denegado');
}

// 1. Cabeceras HTTP de defensa OWASP (0 ms de costo en servidor)
if (!headers_sent()) {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
}

/**
 * Inicia la sesión con cookies endurecidas si aún no está activa.
 */
if (!function_exists('iniciar_sesion_segura')) {
    function iniciar_sesion_segura(): void {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.use_strict_mode', '1');
            ini_set('session.cookie_samesite', 'Lax');
            if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
                ini_set('session.cookie_secure', '1');
            }
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}

/**
 * Verifica autenticación y rol opcional (compatible con peticiones AJAX de admin-resiliencia.js).
 */
if (!function_exists('exigir_sesion_admin')) {
    function exigir_sesion_admin(?string $rol_requerido = null): void {
        iniciar_sesion_segura();
        $es_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            if ($es_ajax) {
                http_response_code(401);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'status'  => 'danger',
                    'message' => 'Tu sesión expiró. Inicia sesión en una nueva pestaña y vuelve a presionar Guardar.'
                ]);
                exit;
            }
            header('Location: index.php');
            exit;
        }

        if ($rol_requerido !== null && isset($_SESSION['admin_rol']) && $_SESSION['admin_rol'] !== $rol_requerido) {
            if ($es_ajax) {
                http_response_code(403);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'status'  => 'danger',
                    'message' => 'No cuentas con permisos suficientes para esta sección.'
                ]);
                exit;
            }
            header('Location: inicio.php');
            exit;
        }
    }
}

/**
 * Helpers rápidos para protección anti-CSRF en formularios.
 */
if (!function_exists('csrf_campo')) {
    function csrf_campo(): string {
        iniciar_sesion_segura();
        return '<input type="hidden" name="csrf_token" value="' . e($_SESSION['csrf_token']) . '">';
    }
}

if (!function_exists('validar_csrf')) {
    function validar_csrf(): bool {
        iniciar_sesion_segura();
        $token_post = (string)($_POST['csrf_token'] ?? '');
        return $token_post !== '' && hash_equals($_SESSION['csrf_token'], $token_post);
    }
}

/**
 * Sanitiza entradas POST preservando acentos y filtrando XSS en HTML enriquecido (TinyMCE).
 */
if (!function_exists('recibir_post')) {
    function recibir_post(string $campo, bool $permitir_html = false): string {
        if (!isset($_POST[$campo])) {
            return '';
        }
        $valor = trim((string)$_POST[$campo]);
        if ($valor === '') {
            return '';
        }

        if (!$permitir_html) {
            return strip_tags($valor);
        }

        // Lista blanca de etiquetas seguras generadas por TinyMCE
        $etiquetas_seguras = '<p><br><b><strong><i><em><u><s><ul><ol><li><a><h1><h2><h3><h4><h5><h6><blockquote><span><div><sub><sup>';
        $limpio = strip_tags($valor, $etiquetas_seguras);

        // Remover atributos de eventos JS (onload, onerror, onclick, etc.)
        $limpio = preg_replace('/\s+on[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $limpio);
        // Bloquear esquemas peligrosos (javascript:, vbscript:, data:) en href o src
        $limpio = preg_replace('/\b(href|src)\s*=\s*(["\']?)\s*(?:javascript|vbscript|data)\s*:[^"\'\s>]*/i', '$1=$2#', $limpio);
        // Bloquear expresiones ejecutables dentro de atributos style
        $limpio = preg_replace('/style\s*=\s*(["\'])[^"\']*(?:expression\s*\(|javascript\s*:|behavior\s*:)[^"\']*\1/i', '', $limpio);

        return $limpio;
    }
}

/**
 * Valida y mueve de forma segura una imagen subida bajo normas OWASP (Máx. 2 MB).
 */
if (!function_exists('procesar_imagen_segura')) {
    function procesar_imagen_segura(string $input_name, string $carpeta_relativa, string $sufijo_hash = '', bool $permitir_svg = false): ?string {
        if (!isset($_FILES[$input_name]) || $_FILES[$input_name]['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $err = $_FILES[$input_name]['error'];
        if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
            throw new RuntimeException('La imagen supera el límite máximo permitido por el servidor (2 MB).');
        }
        if ($err !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Ocurrió un fallo durante la transferencia de la imagen. Intenta de nuevo.');
        }

        $tmp  = $_FILES[$input_name]['tmp_name'];
        $name = $_FILES[$input_name]['name'];
        $size = (int)$_FILES[$input_name]['size'];
        $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        if ($size > 2 * 1024 * 1024) {
            throw new RuntimeException('La imagen supera el límite máximo de 2 MB.');
        }

        $ext_permitidas  = ['jpg', 'jpeg', 'png', 'webp'];
        $mime_permitidos = ['image/jpeg', 'image/png', 'image/webp'];

        if ($permitir_svg) {
            $ext_permitidas[]  = 'svg';
            $mime_permitidos[] = 'image/svg+xml';
        }

        $finfo     = new finfo(FILEINFO_MIME_TYPE);
        $mime_real = $finfo->file($tmp);

        if (!in_array($ext, $ext_permitidas, true) || !in_array($mime_real, $mime_permitidos, true)) {
            throw new RuntimeException('Formato de imagen no válido. Usa únicamente JPG, PNG o WEBP.');
        }

        if ($ext === 'svg') {
            // OWASP: Prevenir Stored XSS dentro de archivos vectoriales SVG
            $contenido_svg = (string)file_get_contents($tmp);
            if (preg_match('/<script\b|on[a-z]+\s*=|javascript\s*:|<foreignObject\b/i', $contenido_svg)) {
                throw new RuntimeException('El archivo SVG contiene elementos activos no permitidos por seguridad.');
            }
        } elseif (getimagesize($tmp) === false) {
            throw new RuntimeException('El archivo de imagen está dañado o no es válido.');
        }

        $sufijo_limpio    = $sufijo_hash !== '' ? '_' . preg_replace('/[^a-z0-9]/i', '', $sufijo_hash) : '';
        $nuevo_nombre     = bin2hex(random_bytes(16)) . $sufijo_limpio . '.' . $ext;
        $carpeta_limpia   = trim($carpeta_relativa, '/');
        $directorio_final = dirname(__DIR__, 2) . '/' . $carpeta_limpia . '/';

        if (!is_dir($directorio_final) && !mkdir($directorio_final, 0755, true) && !is_dir($directorio_final)) {
            throw new RuntimeException('No se pudo preparar el directorio de imágenes.');
        }

        if (!move_uploaded_file($tmp, $directorio_final . $nuevo_nombre)) {
            throw new RuntimeException('No fue posible guardar la imagen en el servidor.');
        }

        return $carpeta_limpia . '/' . $nuevo_nombre;
    }
}

/**
 * Filtra HTML enriquecido (TinyMCE) eliminando vectores XSS (eventos on*, javascript:, iframes/scripts).
 */
if (!function_exists('limpiar_html_seguro')) {
    function limpiar_html_seguro(string $valor): string {
        $valor = trim($valor);
        if ($valor === '') {
            return '';
        }
        $etiquetas_seguras = '<p><br><b><strong><i><em><u><s><ul><ol><li><a><h1><h2><h3><h4><h5><h6><blockquote><span><div><sub><sup>';
        $limpio = strip_tags($valor, $etiquetas_seguras);
        $limpio = preg_replace('/\s+on[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $limpio);
        $limpio = preg_replace('/\b(href|src)\s*=\s*(["\']?)\s*(?:javascript|vbscript|data)\s*:[^"\'\s>]*/i', '$1=$2#', $limpio);
        $limpio = preg_replace('/style\s*=\s*(["\'])[^"\']*(?:expression\s*\(|javascript\s*:|behavior\s*:)[^"\']*\1/i', '', $limpio);
        return $limpio;
    }
}

/**
 * Procesa de forma segura múltiples imágenes (input multiple name="campo[]") bajo normas OWASP.
 */
if (!function_exists('procesar_galeria_segura')) {
    function procesar_galeria_segura(string $input_name, string $carpeta_relativa): array {
        if (!isset($_FILES[$input_name]) || !is_array($_FILES[$input_name]['name'])) {
            return [];
        }
        if (($_FILES[$input_name]['error'][0] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE && count($_FILES[$input_name]['name']) === 1) {
            return [];
        }

        $rutas_guardadas = [];
        $total = count($_FILES[$input_name]['name']);
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $carpeta_limpia = trim($carpeta_relativa, '/');
        $directorio_final = dirname(__DIR__, 2) . '/' . $carpeta_limpia . '/';

        if (!is_dir($directorio_final) && !mkdir($directorio_final, 0755, true) && !is_dir($directorio_final)) {
            throw new RuntimeException('No se pudo preparar el directorio para la galería.');
        }

        for ($i = 0; $i < $total; $i++) {
            $err = $_FILES[$input_name]['error'][$i];
            if ($err === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
                throw new RuntimeException('Una de las imágenes de la galería supera el límite permitido (2 MB).');
            }
            if ($err !== UPLOAD_ERR_OK) {
                throw new RuntimeException('Error al transferir una imagen de la galería.');
            }

            $tmp  = $_FILES[$input_name]['tmp_name'][$i];
            $name = $_FILES[$input_name]['name'][$i];
            $size = (int)$_FILES[$input_name]['size'][$i];
            $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $mime = $finfo->file($tmp);

            if ($size > 2 * 1024 * 1024) {
                throw new RuntimeException("La imagen «{$name}» supera los 2 MB permitidos.");
            }
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true) || !in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true) || getimagesize($tmp) === false) {
                throw new RuntimeException("El archivo «{$name}» no es una imagen JPG, PNG o WEBP válida.");
            }

            $nuevo_nombre = bin2hex(random_bytes(16)) . '_' . $i . '.' . $ext;
            if (!move_uploaded_file($tmp, $directorio_final . $nuevo_nombre)) {
                throw new RuntimeException('Error al guardar una imagen de la galería en el servidor.');
            }
            $rutas_guardadas[] = $carpeta_limpia . '/' . $nuevo_nombre;
        }

        return $rutas_guardadas;
    }
}