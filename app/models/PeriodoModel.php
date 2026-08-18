<?php

require_once __DIR__ . '/../../core/Model.php';

class PeriodoModel extends Model {
    public function getAll(): array {
        return $this->db->query("SELECT * FROM tb_periodo_nomina ORDER BY fecha_inicio DESC")->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM tb_periodo_nomina WHERE id_periodo = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getUltimo(): ?array {
        return $this->db->query("SELECT * FROM tb_periodo_nomina ORDER BY fecha_inicio DESC LIMIT 1")->fetch() ?: null;
    }

    public function create(array $data): void {
        $stmt = $this->db->prepare("
            INSERT INTO tb_periodo_nomina (descripcion, tipo_periodo, fecha_inicio, fecha_fin, anio, mes, numero_quincena)
            VALUES (?,?,?,?,?,?,?)
        ");
        $stmt->execute([
            $data['descripcion'] ?? null,
            $data['tipo_periodo'],
            $data['fecha_inicio'],
            $data['fecha_fin'],
            $data['anio'],
            $data['mes'],
            $data['numero_quincena'] !== null && $data['numero_quincena'] !== '' ? (int)$data['numero_quincena'] : null
        ]);
    }
}
