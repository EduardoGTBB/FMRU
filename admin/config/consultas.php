<?php
// admin/config/consultas.php
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    http_response_code(403);
    exit('Acceso denegado');
}

class GestorConsultas {
    private PDO $pdo;
    private ?array $settingsCache = null;
    private ?PDOStatement $stmtUpdateSetting = null;

    // OWASP: Inyección de Dependencias. Recibe la conexión segura PDO.
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // ================== TRANSACCIONES SEGURAS ==================
    public function beginTransaction(): bool {
        return !$this->pdo->inTransaction() ? $this->pdo->beginTransaction() : true;
    }

    public function commit(): bool {
        $this->settingsCache = null;
        return $this->pdo->inTransaction() ? $this->pdo->commit() : false;
    }

    public function rollBack(): bool {
        $this->settingsCache = null;
        return $this->pdo->inTransaction() ? $this->pdo->rollBack() : false;
    }

    // ================== USUARIOS ==================
    public function getAdminByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT id, nombre, rol, password_hash FROM admin_usuarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTodosUsuarios() {
        return $this->pdo->query("SELECT id, nombre, email, rol, fecha_creacion FROM admin_usuarios ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUsuarioById($id) {
        $stmt = $this->pdo->prepare("SELECT id, nombre, email, rol, fecha_creacion FROM admin_usuarios WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertUsuario($nombre, $email, $rol, $hash) {
        $stmt = $this->pdo->prepare("INSERT INTO admin_usuarios (nombre, email, rol, password_hash) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$nombre, $email, $rol, $hash]);
    }

    public function updateUsuario($id, $nombre, $email, $rol, $hash) {
        $stmt = $this->pdo->prepare("UPDATE admin_usuarios SET nombre = ?, email = ?, rol = ?, password_hash = ? WHERE id = ?");
        return $stmt->execute([$nombre, $email, $rol, $hash, (int)$id]);
    }

    public function updateUsuarioSinPassword($id, $nombre, $email, $rol) {
        $stmt = $this->pdo->prepare("UPDATE admin_usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?");
        return $stmt->execute([$nombre, $email, $rol, (int)$id]);
    }

    public function eliminarUsuario($id) {
        $stmt = $this->pdo->prepare("DELETE FROM admin_usuarios WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }

    // ================== DASHBOARD ==================
    public function contarNoticiasActivas(): int {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM fmru_noticias WHERE activo = 1 AND tipo != 'comunicado'")->fetchColumn();
    }

    public function contarPartidosPendientes(): int {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM fmru_partidos WHERE estado = 'PRÓXIMO PARTIDO'")->fetchColumn();
    }

    public function contarDocumentosActivos(): int {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM fmru_documentos WHERE activo = 1")->fetchColumn();
    }

    /**
     * Obtiene las 3 métricas del Dashboard en una sola consulta SQL (opcional para máxima velocidad).
     */
    public function getResumenDashboard(): array {
        $sql = "SELECT
                    (SELECT COUNT(*) FROM fmru_noticias WHERE activo = 1 AND tipo != 'comunicado') AS noticias,
                    (SELECT COUNT(*) FROM fmru_partidos WHERE estado = 'PRÓXIMO PARTIDO') AS partidos,
                    (SELECT COUNT(*) FROM fmru_documentos WHERE activo = 1) AS documentos";
        $row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC) ?: [];
        return [
            'noticias'   => (int)($row['noticias'] ?? 0),
            'partidos'   => (int)($row['partidos'] ?? 0),
            'documentos' => (int)($row['documentos'] ?? 0),
        ];
    }

    // ================== BANNERS ==================
    public function getBannersAdmin() {
        return $this->pdo->query("SELECT id, titulo, subtitulo, imagen_url, orden, activo FROM fmru_banners ORDER BY orden ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBannerById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM fmru_banners WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getImagenBanner($id) {
        $stmt = $this->pdo->prepare("SELECT imagen_url FROM fmru_banners WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$id]);
        return $stmt->fetchColumn();
    }

    public function getMaxOrdenBanner() {
        return $this->pdo->query("SELECT MAX(orden) FROM fmru_banners")->fetchColumn();
    }

    public function getBannerIdByOrden($orden, $exclude_id = 0) {
        $stmt = $this->pdo->prepare("SELECT id FROM fmru_banners WHERE orden = ? AND id != ? LIMIT 1");
        $stmt->execute([(int)$orden, (int)$exclude_id]);
        return $stmt->fetchColumn();
    }

    public function updateBannerOrden($id, $orden) {
        return $this->pdo->prepare("UPDATE fmru_banners SET orden = ? WHERE id = ?")->execute([(int)$orden, (int)$id]);
    }

    public function shiftBannersOrden($orden) {
        return $this->pdo->prepare("UPDATE fmru_banners SET orden = orden + 1 WHERE orden >= ?")->execute([(int)$orden]);
    }

    public function insertBanner($titulo, $subtitulo, $imagen, $orden, $activo) {
        return $this->pdo->prepare("INSERT INTO fmru_banners (titulo, subtitulo, imagen_url, orden, activo) VALUES (?, ?, ?, ?, ?)")
            ->execute([$titulo, $subtitulo, $imagen, (int)$orden, (int)$activo]);
    }

    public function updateBanner($id, $titulo, $subtitulo, $imagen, $orden, $activo) {
        return $this->pdo->prepare("UPDATE fmru_banners SET titulo = ?, subtitulo = ?, imagen_url = ?, orden = ?, activo = ? WHERE id = ?")
            ->execute([$titulo, $subtitulo, $imagen, (int)$orden, (int)$activo, (int)$id]);
    }

    public function eliminarBanner($id) {
        $stmt = $this->pdo->prepare("DELETE FROM fmru_banners WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }

    // ================== SPONSORS ==================
    public function getSponsorsAdmin() {
        return $this->pdo->query("SELECT id, nombre, imagen_url, enlace, nivel, rotativo, orden, activo FROM fmru_sponsors ORDER BY FIELD(nivel, 'Patrocinador Principal', 'Socio Técnico', 'Proveedor Oficial'), orden ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSponsorById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM fmru_sponsors WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getImagenSponsor($id) {
        $stmt = $this->pdo->prepare("SELECT imagen_url FROM fmru_sponsors WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$id]);
        return $stmt->fetchColumn();
    }

    public function getMaxOrdenSponsor() {
        return $this->pdo->query("SELECT MAX(orden) FROM fmru_sponsors")->fetchColumn();
    }

    public function getSponsorIdByOrden($orden, $exclude_id = 0) {
        $stmt = $this->pdo->prepare("SELECT id FROM fmru_sponsors WHERE orden = ? AND id != ? LIMIT 1");
        $stmt->execute([(int)$orden, (int)$exclude_id]);
        return $stmt->fetchColumn();
    }

    public function updateSponsorOrden($id, $orden) {
        return $this->pdo->prepare("UPDATE fmru_sponsors SET orden = ? WHERE id = ?")->execute([(int)$orden, (int)$id]);
    }

    public function shiftSponsorsOrden($orden) {
        return $this->pdo->prepare("UPDATE fmru_sponsors SET orden = orden + 1 WHERE orden >= ?")->execute([(int)$orden]);
    }

    public function insertSponsor($nombre, $enlace, $nivel, $rotativo, $imagen, $orden, $activo) {
        return $this->pdo->prepare("INSERT INTO fmru_sponsors (nombre, enlace, nivel, rotativo, imagen_url, orden, activo) VALUES (?, ?, ?, ?, ?, ?, ?)")
            ->execute([$nombre, $enlace, $nivel, (int)$rotativo, $imagen, (int)$orden, (int)$activo]);
    }

    public function updateSponsor($id, $nombre, $enlace, $nivel, $rotativo, $imagen, $orden, $activo) {
        return $this->pdo->prepare("UPDATE fmru_sponsors SET nombre = ?, enlace = ?, nivel = ?, rotativo = ?, imagen_url = ?, orden = ?, activo = ? WHERE id = ?")
            ->execute([$nombre, $enlace, $nivel, (int)$rotativo, $imagen, (int)$orden, (int)$activo, (int)$id]);
    }

    public function eliminarSponsor($id) {
        $stmt = $this->pdo->prepare("DELETE FROM fmru_sponsors WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }

    // ================== INSTITUCIONAL Y MÓDULOS DINÁMICOS (SETTINGS CON CACHÉ) ==================
    public function getAllSettings(): array {
        if ($this->settingsCache === null) {
            $this->settingsCache = $this->pdo->query("SELECT setting_key, setting_value FROM fmru_settings")->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
        }
        return $this->settingsCache;
    }

    public function updateSetting($key, $value): bool {
        // Reutiliza el statement preparado cuando se guardan múltiples llaves seguidas y crea la llave si no existe
        if ($this->stmtUpdateSetting === null) {
            $this->stmtUpdateSetting = $this->pdo->prepare(
                "INSERT INTO fmru_settings (setting_key, setting_value)
                 VALUES (?, ?)
                 ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
            );
        }
        $this->settingsCache = null;
        return $this->stmtUpdateSetting->execute([(string)$key, (string)$value]);
    }

    // ================== GESTOR DE CONTENIDOS (SIN CARGAR LONGTEXT NI JSON PESADO) ==================
    public function getNoticiasAdmin() {
        return $this->pdo->query("SELECT id, tipo, categoria, titulo, fecha_publicacion, imagen_url, activo FROM fmru_noticias WHERE tipo != 'comunicado' ORDER BY fecha_publicacion DESC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getComunicadosAdmin() {
        return $this->pdo->query("SELECT id, tipo, categoria, titulo, fecha_publicacion, activo FROM fmru_noticias WHERE tipo = 'comunicado' ORDER BY fecha_publicacion DESC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLandingsAdmin() {
        return $this->pdo->query("SELECT id, slug, titulo, plantilla, activo, fecha_creacion FROM fmru_paginas_landing ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getImagenNoticia($id) {
        $stmt = $this->pdo->prepare("SELECT imagen_url FROM fmru_noticias WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$id]);
        return $stmt->fetchColumn();
    }

    public function eliminarNoticia($id) {
        $stmt = $this->pdo->prepare("DELETE FROM fmru_noticias WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }

    public function eliminarLanding($id) {
        $stmt = $this->pdo->prepare("DELETE FROM fmru_paginas_landing WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }

    // ================== NOTICIAS (FORMULARIO COMPLETO) ==================
    public function getNoticiaById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM fmru_noticias WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertNoticia($tipo, $categoria, $titulo, $extracto, $contenido, $fecha, $tiempo, $imagen, $imagenes_adicionales, $activo) {
        $stmt = $this->pdo->prepare("INSERT INTO fmru_noticias (tipo, categoria, titulo, extracto, contenido, fecha_publicacion, tiempo_lectura, imagen_url, imagenes_adicionales, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$tipo, $categoria, $titulo, $extracto, $contenido, $fecha, $tiempo, $imagen, $imagenes_adicionales, (int)$activo]);
    }

    public function updateNoticia($id, $tipo, $categoria, $titulo, $extracto, $contenido, $fecha, $tiempo, $imagen, $imagenes_adicionales, $activo) {
        $stmt = $this->pdo->prepare("UPDATE fmru_noticias SET tipo = ?, categoria = ?, titulo = ?, extracto = ?, contenido = ?, fecha_publicacion = ?, tiempo_lectura = ?, imagen_url = ?, imagenes_adicionales = ?, activo = ? WHERE id = ?");
        return $stmt->execute([$tipo, $categoria, $titulo, $extracto, $contenido, $fecha, $tiempo, $imagen, $imagenes_adicionales, (int)$activo, (int)$id]);
    }

    // ================== LANDINGS (FORMULARIO COMPLETO) ==================
    public function getLandingById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM fmru_paginas_landing WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertLanding($slug, $titulo, $plantilla, $json, $activo) {
        $stmt = $this->pdo->prepare("INSERT INTO fmru_paginas_landing (slug, titulo, plantilla, configuracion_json, activo) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$slug, $titulo, $plantilla, $json, (int)$activo]);
    }

    public function updateLanding($id, $slug, $titulo, $plantilla, $json, $activo) {
        $stmt = $this->pdo->prepare("UPDATE fmru_paginas_landing SET slug = ?, titulo = ?, plantilla = ?, configuracion_json = ?, activo = ? WHERE id = ?");
        return $stmt->execute([$slug, $titulo, $plantilla, $json, (int)$activo, (int)$id]);
    }

    // ================== FRONT-END (PÁGINA PÚBLICA) ==================
    public function getNoticiasRecientes($limit = 4) {
        $stmt = $this->pdo->prepare("SELECT * FROM fmru_noticias WHERE activo = 1 AND tipo != 'comunicado' ORDER BY fecha_publicacion DESC, id DESC LIMIT :limit");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getComunicadosRecientes($limit = 4) {
        $stmt = $this->pdo->prepare("SELECT * FROM fmru_noticias WHERE activo = 1 AND tipo = 'comunicado' ORDER BY fecha_publicacion DESC, id DESC LIMIT :limit");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBannersActivos() {
        return $this->pdo->query("SELECT id, titulo, subtitulo, imagen_url, orden FROM fmru_banners WHERE activo = 1 ORDER BY orden ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSponsorsActivos() {
        return $this->pdo->query("SELECT id, nombre, imagen_url, enlace, nivel, rotativo, orden FROM fmru_sponsors WHERE activo = 1 ORDER BY orden ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLandingBySlug($slug) {
        $stmt = $this->pdo->prepare("SELECT * FROM fmru_paginas_landing WHERE slug = ? AND activo = 1 LIMIT 1");
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ================== REPOSITORIO DE DOCUMENTOS ==================
    public function getDocumentosAdmin() {
        return $this->pdo->query("SELECT * FROM fmru_documentos ORDER BY orden ASC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDocumentosPublicos() {
        return $this->pdo->query("SELECT * FROM fmru_documentos WHERE activo = 1 ORDER BY orden ASC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDocumentoById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM fmru_documentos WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getMaxOrdenDocumento() {
        return $this->pdo->query("SELECT MAX(orden) FROM fmru_documentos")->fetchColumn();
    }

    public function getDocumentoIdByOrden($orden, $excludeId = 0) {
        if ($excludeId) {
            $stmt = $this->pdo->prepare("SELECT id FROM fmru_documentos WHERE orden = ? AND id != ? LIMIT 1");
            $stmt->execute([(int)$orden, (int)$excludeId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT id FROM fmru_documentos WHERE orden = ? LIMIT 1");
            $stmt->execute([(int)$orden]);
        }
        return $stmt->fetchColumn();
    }

    public function updateDocumentoOrden($id, $orden) {
        $stmt = $this->pdo->prepare("UPDATE fmru_documentos SET orden = ? WHERE id = ?");
        return $stmt->execute([(int)$orden, (int)$id]);
    }

    public function shiftDocumentosOrden($orden) {
        $stmt = $this->pdo->prepare("UPDATE fmru_documentos SET orden = orden + 1 WHERE orden >= ?");
        return $stmt->execute([(int)$orden]);
    }

    public function insertDocumento($categoria, $nombre, $fecha, $peso, $icono, $enlace, $orden, $activo) {
        $sql = "INSERT INTO fmru_documentos (categoria, nombre, fecha_modificacion, peso, icono, tipo_origen, enlace, orden, activo)
                VALUES (?, ?, ?, ?, ?, 'enlace', ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$categoria, $nombre, $fecha, $peso, $icono, $enlace, (int)$orden, (int)$activo]);
    }

    public function updateDocumento($id, $categoria, $nombre, $fecha, $peso, $icono, $enlace, $orden, $activo) {
        $sql = "UPDATE fmru_documentos
                SET categoria = ?, nombre = ?, fecha_modificacion = ?, peso = ?, icono = ?, enlace = ?, orden = ?, activo = ?
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$categoria, $nombre, $fecha, $peso, $icono, $enlace, (int)$orden, (int)$activo, (int)$id]);
    }

    public function eliminarDocumento($id) {
        $stmt = $this->pdo->prepare("DELETE FROM fmru_documentos WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }
}