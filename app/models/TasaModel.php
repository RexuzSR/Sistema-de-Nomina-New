<?php

require_once __DIR__ . '/../../core/Model.php';

class TasaModel extends Model {
    public function getAll(int $limit = 30): array {
        $stmt = $this->db->prepare("SELECT * FROM tb_tasa_cambio ORDER BY fecha DESC, id_tasa DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUltima(): ?array {
        $stmt = $this->db->query("SELECT * FROM tb_tasa_cambio ORDER BY fecha DESC, id_tasa DESC LIMIT 1");
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(string $fecha, float $valor, string $fuente = 'BCV'): void {
        $stmt = $this->db->prepare("INSERT INTO tb_tasa_cambio (fecha, valor_tasa, fuente) VALUES (?,?,?)");
        $stmt->execute([$fecha, $valor, $fuente ?: 'BCV']);
    }
}
