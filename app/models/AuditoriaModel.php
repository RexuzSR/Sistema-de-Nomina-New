<?php

require_once __DIR__ . '/../../core/Model.php';

class AuditoriaModel extends Model {

    /**
     * Consulta registros de auditoría con filtros avanzados y paginación
     */
    public function listar(array $filtros = [], int $limit = 50, int $offset = 0): array {
        $where = [];
        $params = [];

        // Filtro por usuario específico (o forzado para usuarios no administradores)
        if (!empty($filtros['id_usuario'])) {
            $where[] = "a.id_usuario = ?";
            $params[] = (int)$filtros['id_usuario'];
        }

        // Filtro por módulo
        if (!empty($filtros['modulo'])) {
            $where[] = "a.modulo = ?";
            $params[] = strtoupper(trim($filtros['modulo']));
        }

        // Filtro por tipo de acción
        if (!empty($filtros['accion'])) {
            $where[] = "a.accion = ?";
            $params[] = strtoupper(trim($filtros['accion']));
        }

        // Búsqueda por texto (detalle, IP, usuario o tabla)
        if (!empty($filtros['q'])) {
            $searchTerm = '%' . trim($filtros['q']) . '%';
            $where[] = "(a.detalle LIKE ? OR u.nombre_usuario LIKE ? OR a.tabla_afectada LIKE ? OR a.ip LIKE ?)";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        // Filtro por rango de fechas
        if (!empty($filtros['fecha_desde'])) {
            $where[] = "a.fecha_hora >= ?";
            $params[] = trim($filtros['fecha_desde']) . ' 00:00:00';
        }

        if (!empty($filtros['fecha_hasta'])) {
            $where[] = "a.fecha_hora <= ?";
            $params[] = trim($filtros['fecha_hasta']) . ' 23:59:59';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT 
                a.*,
                u.nombre_usuario,
                u.correo AS usuario_correo,
                u.foto_perfil AS usuario_avatar,
                r.nombre_rol
            FROM tb_auditoria a
            LEFT JOIN tb_usuario u ON u.id_usuario = a.id_usuario
            LEFT JOIN tb_rol r ON r.id_rol = u.id_rol
            {$whereClause}
            ORDER BY a.fecha_hora DESC, a.id_auditoria DESC
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->db->prepare($sql);

        // Bind params
        $i = 1;
        foreach ($params as $param) {
            $stmt->bindValue($i++, $param);
        }
        $stmt->bindValue($i++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($i++, $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta el total de registros que coinciden con los filtros
     */
    public function contarTotal(array $filtros = []): int {
        $where = [];
        $params = [];

        if (!empty($filtros['id_usuario'])) {
            $where[] = "a.id_usuario = ?";
            $params[] = (int)$filtros['id_usuario'];
        }

        if (!empty($filtros['modulo'])) {
            $where[] = "a.modulo = ?";
            $params[] = strtoupper(trim($filtros['modulo']));
        }

        if (!empty($filtros['accion'])) {
            $where[] = "a.accion = ?";
            $params[] = strtoupper(trim($filtros['accion']));
        }

        if (!empty($filtros['q'])) {
            $searchTerm = '%' . trim($filtros['q']) . '%';
            $where[] = "(a.detalle LIKE ? OR u.nombre_usuario LIKE ? OR a.tabla_afectada LIKE ? OR a.ip LIKE ?)";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filtros['fecha_desde'])) {
            $where[] = "a.fecha_hora >= ?";
            $params[] = trim($filtros['fecha_desde']) . ' 00:00:00';
        }

        if (!empty($filtros['fecha_hasta'])) {
            $where[] = "a.fecha_hora <= ?";
            $params[] = trim($filtros['fecha_hasta']) . ' 23:59:59';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT COUNT(*)
            FROM tb_auditoria a
            LEFT JOIN tb_usuario u ON u.id_usuario = a.id_usuario
            {$whereClause}
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Obtiene el detalle completo de un registro de auditoría por su ID
     */
    public function obtenerPorId(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT 
                a.*,
                u.nombre_usuario,
                u.correo AS usuario_correo,
                u.foto_perfil AS usuario_avatar,
                r.nombre_rol
            FROM tb_auditoria a
            LEFT JOIN tb_usuario u ON u.id_usuario = a.id_usuario
            LEFT JOIN tb_rol r ON r.id_rol = u.id_rol
            WHERE a.id_auditoria = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Calcula estadísticas KPI para el dashboard de auditoría
     */
    public function obtenerEstadisticas(?int $idUsuario = null): array {
        $userFilter = $idUsuario ? "WHERE id_usuario = " . (int)$idUsuario : "";
        $userAnd = $idUsuario ? "AND id_usuario = " . (int)$idUsuario : "";

        // Total hoy
        $totalHoy = (int)$this->db->query("
            SELECT COUNT(*) FROM tb_auditoria 
            WHERE DATE(fecha_hora) = CURDATE() {$userAnd}
        ")->fetchColumn();

        // Total últimos 7 días
        $totalSemana = (int)$this->db->query("
            SELECT COUNT(*) FROM tb_auditoria 
            WHERE fecha_hora >= (NOW() - INTERVAL 7 DAY) {$userAnd}
        ")->fetchColumn();

        // Usuarios activos hoy con acciones
        $usuariosHoy = (int)$this->db->query("
            SELECT COUNT(DISTINCT id_usuario) FROM tb_auditoria 
            WHERE DATE(fecha_hora) = CURDATE() {$userAnd}
        ")->fetchColumn();

        // Total histórico de movimientos
        $totalMovimientos = (int)$this->db->query("
            SELECT COUNT(*) FROM tb_auditoria {$userFilter}
        ")->fetchColumn();

        return [
            'total_hoy' => $totalHoy,
            'total_semana' => $totalSemana,
            'usuarios_hoy' => $usuariosHoy,
            'total_movimientos' => $totalMovimientos
        ];
    }

    /**
     * Obtiene la lista de usuarios para el filtro selector
     */
    public function obtenerUsuariosConActividad(): array {
        $stmt = $this->db->query("
            SELECT DISTINCT u.id_usuario, u.nombre_usuario, r.nombre_rol
            FROM tb_usuario u
            INNER JOIN tb_auditoria a ON a.id_usuario = u.id_usuario
            LEFT JOIN tb_rol r ON r.id_rol = u.id_rol
            ORDER BY u.nombre_usuario ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene la lista de módulos únicos presentes en la auditoría
     */
    public function obtenerModulos(): array {
        $stmt = $this->db->query("
            SELECT DISTINCT modulo 
            FROM tb_auditoria 
            WHERE modulo IS NOT NULL AND modulo != ''
            ORDER BY modulo ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
