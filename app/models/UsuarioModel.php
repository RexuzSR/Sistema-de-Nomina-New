<?php

require_once __DIR__ . '/../../core/Model.php';

class UsuarioModel extends Model {
    public function getUsuarioByUsername(string $username): ?array {
        $stmt = $this->db->prepare("
            SELECT u.*, r.nombre_rol
            FROM tb_usuario u
            LEFT JOIN tb_rol r ON r.id_rol = u.id_rol
            WHERE u.nombre_usuario = ? AND u.estado = 1
        ");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function login(string $username, string $password): ?array {
        $user = $this->getUsuarioByUsername($username);
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return null;
    }
}
