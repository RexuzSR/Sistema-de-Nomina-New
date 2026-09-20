<?php
/**
 * Servicio Centralizado de Auditoría e Historial de Actividades
 * Sistema de Gestión de Nómina
 */
class Audit {
    /**
     * Registra una acción en la bitácora del sistema
     *
     * @param string $modulo Módulo funcional (EMPLEADOS, NOMINA, PERMISOS, USUARIOS, TASAS, SEGURIDAD, PERFIL)
     * @param string $accion Tipo de acción (CREAR, MODIFICAR, ELIMINAR, LOGIN, LOGOUT, APROBAR, RECHAZAR, CALCULAR, EXPORTAR)
     * @param string $descripcion Descripción legible de la acción realizada
     * @param string|null $tabla Nombre de la tabla afectada (ej: tb_empleado, tb_nomina)
     * @param int|null $idRegistro ID del registro modificado
     * @param mixed|null $detalles Arreglo asociativo o string con datos antes/después o parámetros
     * @param int|null $idUsuario ID explícito del usuario responsable (opcional, por defecto de sesión)
     * @return bool
     */
    public static function log(
        string $modulo,
        string $accion,
        string $descripcion,
        ?string $tabla = null,
        ?int $idRegistro = null,
        mixed $detalles = null,
        ?int $idUsuario = null
    ): bool {
        try {
            $pdo = Database::getInstance();

            // Detectar ID de usuario de la sesión actual
            if ($idUsuario === null) {
                $idUsuario = $_SESSION['user_id'] ?? ($_SESSION['usuario_id'] ?? null);
            }

            if ($idUsuario === null && !empty($_SESSION['usuario'])) {
                $stmtUser = $pdo->prepare("SELECT id_usuario FROM tb_usuario WHERE nombre_usuario = ? LIMIT 1");
                $stmtUser->execute([$_SESSION['usuario']]);
                $idUsuario = $stmtUser->fetchColumn() ?: null;
            }

            // Fallback para procesos del sistema si no hay sesión
            if (!$idUsuario) {
                $idUsuario = 1; // Superadministrador / Sistema
            }

            $ip = class_exists('Security') ? Security::getClientIp() : ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
            
            $detallesJson = null;
            if ($detalles !== null) {
                $detallesJson = is_string($detalles) ? $detalles : json_encode($detalles, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            }

            $stmt = $pdo->prepare("
                INSERT INTO tb_auditoria 
                (id_usuario, modulo, accion, tabla_afectada, id_registro_afectado, ip, detalle, detalles_json, fecha_hora)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            return $stmt->execute([
                (int)$idUsuario,
                strtoupper(trim($modulo)),
                strtoupper(trim($accion)),
                $tabla ? trim($tabla) : 'tb_' . strtolower(trim($modulo)),
                $idRegistro !== null ? (int)$idRegistro : null,
                $ip,
                trim($descripcion),
                $detallesJson
            ]);
        } catch (Exception $e) {
            error_log("Error al registrar auditoría: " . $e->getMessage());
            return false;
        }
    }
}
