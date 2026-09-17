<?php

require_once __DIR__ . '/../../core/Model.php';

class EmpleadoModel extends Model {
    public function __construct() {
        parent::__construct();
        $this->ensureFotoColumn();
    }

    private function ensureFotoColumn(): void {
        try {
            $cols = $this->db->query("SHOW COLUMNS FROM tb_empleado LIKE 'foto'")->fetchAll();
            if (empty($cols)) {
                $this->db->exec("ALTER TABLE tb_empleado ADD COLUMN foto LONGTEXT NULL DEFAULT NULL");
            } else {
                $this->db->exec("ALTER TABLE tb_empleado MODIFY COLUMN foto LONGTEXT NULL DEFAULT NULL");
            }
        } catch (\Throwable $e) {
            // Silently ignore if column already exists or table issue
        }
    }

    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT 
                e.*,
                t.nombre_tipo,
                j.nombre_jornada,
                c.nombre_cargo,
                b.nombre_banco,
                b.codigo_banco,
                cb.numero_cuenta,
                cb.tipo_cuenta,
                u.nombre_usuario,
                r.nombre_rol,
                (SELECT COUNT(*) FROM tb_nomina_detalle nd WHERE nd.id_empleado = e.id_empleado) as total_nominas,
                (SELECT COUNT(*) FROM tb_permiso_empleado p WHERE p.id_empleado = e.id_empleado) as total_permisos,
                (SELECT COUNT(*) FROM tb_permiso_empleado p WHERE p.id_empleado = e.id_empleado AND p.estado = 'Aprobado') as permisos_aprobados,
                (SELECT COUNT(*) FROM tb_permiso_empleado p WHERE p.id_empleado = e.id_empleado AND p.estado = 'Pendiente') as permisos_pendientes
            FROM tb_empleado e
            LEFT JOIN tb_tipo_empleado t ON t.id_tipo_empleado = e.id_tipo_empleado
            LEFT JOIN tb_jornada j ON j.id_jornada = e.id_jornada
            LEFT JOIN tb_empleado_cargo ec ON ec.id_empleado = e.id_empleado AND ec.es_actual = 1
            LEFT JOIN tb_cargo c ON c.id_cargo = ec.id_cargo
            LEFT JOIN tb_cuenta_bancaria cb ON cb.id_empleado = e.id_empleado
            LEFT JOIN tb_banco b ON b.id_banco = cb.id_banco
            LEFT JOIN tb_usuario u ON u.id_empleado = e.id_empleado
            LEFT JOIN tb_rol r ON r.id_rol = u.id_rol
            ORDER BY e.primer_apellido, e.primer_nombre
        ");
        return $stmt->fetchAll();
    }


    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM tb_empleado WHERE id_empleado = ?");
        $stmt->execute([$id]);
        $emp = $stmt->fetch();
        return $emp ?: null;
    }

    public function getTipos(): array {
        return $this->db->query("SELECT * FROM tb_tipo_empleado ORDER BY id_tipo_empleado")->fetchAll();
    }

    public function getJornadas(): array {
        return $this->db->query("SELECT * FROM tb_jornada ORDER BY id_jornada")->fetchAll();
    }

    public function getCargos(): array {
        return $this->db->query("SELECT * FROM tb_cargo ORDER BY nombre_cargo")->fetchAll();
    }

    public function getCargoActual(int $idEmpleado): ?array {
        $stmt = $this->db->prepare("
            SELECT c.id_cargo, c.nombre_cargo
            FROM tb_empleado_cargo ec
            JOIN tb_cargo c ON c.id_cargo = ec.id_cargo
            WHERE ec.id_empleado = ? AND ec.es_actual = 1
            LIMIT 1
        ");
        $stmt->execute([$idEmpleado]);
        $cargo = $stmt->fetch();
        return $cargo ?: null;
    }

    public function setCargoActual(int $idEmpleado, int $idCargo): void {
        $actual = $this->db->prepare("SELECT id_empleado_cargo FROM tb_empleado_cargo WHERE id_empleado = ? AND es_actual = 1");
        $actual->execute([$idEmpleado]);
        $fila = $actual->fetch();

        if ($fila) {
            $stmt = $this->db->prepare("UPDATE tb_empleado_cargo SET id_cargo = ?, fecha_asignacion = CURDATE() WHERE id_empleado_cargo = ?");
            $stmt->execute([$idCargo, $fila['id_empleado_cargo']]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO tb_empleado_cargo (id_empleado, id_cargo, fecha_asignacion, es_actual) VALUES (?,?,CURDATE(),1)");
            $stmt->execute([$idEmpleado, $idCargo]);
        }
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO tb_empleado
                (cedula, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, email, foto,
                 id_tipo_empleado, id_jornada, sueldo_divisa, fecha_ingreso, estado)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,1)
        ");
        $stmt->execute([
            $data['cedula'],
            $data['primer_nombre'],
            $data['segundo_nombre'] ?: null,
            $data['primer_apellido'],
            $data['segundo_apellido'] ?: null,
            $data['email'] ?: null,
            $data['foto'] ?? null,
            $data['id_tipo_empleado'],
            $data['id_jornada'],
            $data['sueldo_divisa'],
            $data['fecha_ingreso']
        ]);
        $idEmpleado = (int)$this->db->lastInsertId();
        $this->setCargoActual($idEmpleado, (int)$data['id_cargo']);
        return $idEmpleado;
    }

    public function update(int $id, array $data): void {
        $stmt = $this->db->prepare("
            UPDATE tb_empleado SET
                cedula=?, primer_nombre=?, segundo_nombre=?, primer_apellido=?, segundo_apellido=?, email=?, foto=?,
                id_tipo_empleado=?, id_jornada=?, sueldo_divisa=?, fecha_ingreso=?
            WHERE id_empleado=?
        ");
        $stmt->execute([
            $data['cedula'],
            $data['primer_nombre'],
            $data['segundo_nombre'] ?: null,
            $data['primer_apellido'],
            $data['segundo_apellido'] ?: null,
            $data['email'] ?: null,
            $data['foto'] ?? null,
            $data['id_tipo_empleado'],
            $data['id_jornada'],
            $data['sueldo_divisa'],
            $data['fecha_ingreso'],
            $id
        ]);
        $this->setCargoActual($id, (int)$data['id_cargo']);
    }

    public function toggleEstado(int $id): bool {
        $emp = $this->getById($id);
        if (!$emp) return false;

        $nuevoEstado = $emp['estado'] ? 0 : 1;
        $stmt = $this->db->prepare("UPDATE tb_empleado SET estado = ? WHERE id_empleado = ?");
        $stmt->execute([$nuevoEstado, $id]);
        return true;
    }

    public function getEmpleadosAgregadosMesActualCount(): int {
        $s = $this->db->query("
            SELECT COUNT(*) 
            FROM tb_empleado 
            WHERE MONTH(fecha_ingreso) = MONTH(CURRENT_DATE()) 
              AND YEAR(fecha_ingreso) = YEAR(CURRENT_DATE())
        ");
        return (int)$s->fetchColumn();
    }
}
