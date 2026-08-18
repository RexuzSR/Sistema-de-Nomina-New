<?php

require_once __DIR__ . '/../../core/Model.php';

class PermisoModel extends Model {
    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT p.*, e.primer_nombre, e.segundo_nombre, e.primer_apellido, e.segundo_apellido
            FROM tb_permiso_empleado p
            JOIN tb_empleado e ON e.id_empleado = p.id_empleado
            ORDER BY p.fecha_registro DESC
        ");
        return $stmt->fetchAll();
    }

    public function create(int $idEmpleado, string $fechaInicio, string $fechaFin, float $dias, string $motivo): void {
        $stmt = $this->db->prepare("
            INSERT INTO tb_permiso_empleado (id_empleado, fecha_inicio, fecha_fin, dias_solicitados, motivo, estado)
            VALUES (?,?,?,?,?,'Pendiente')
        ");
        $stmt->execute([$idEmpleado, $fechaInicio, $fechaFin, $dias, $motivo]);
    }

    public function updateEstado(int $idPermiso, string $estado, int $idUsuarioAprueba): void {
        if (in_array($estado, ['Aprobado', 'Rechazado'], true)) {
            $stmt = $this->db->prepare("UPDATE tb_permiso_empleado SET estado = ?, id_usuario_aprueba = ? WHERE id_permiso = ?");
            $stmt->execute([$estado, $idUsuarioAprueba, $idPermiso]);
        }
    }
}
