<?php

require_once __DIR__ . '/../../core/Model.php';
require_once __DIR__ . '/../../core/Security.php';

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

    public function updatePasswordHash(int $idUsuario, string $newHash): void {
        try {
            $stmt = $this->db->prepare("UPDATE tb_usuario SET password_hash = ? WHERE id_usuario = ?");
            $stmt->execute([$newHash, $idUsuario]);
        } catch (Exception $e) {
            error_log("Error updatePasswordHash: " . $e->getMessage());
        }
    }

    public function getUsuarioById(int $idUsuario): ?array {
        $stmt = $this->db->prepare("
            SELECT u.*, r.nombre_rol
            FROM tb_usuario u
            LEFT JOIN tb_rol r ON r.id_rol = u.id_rol
            WHERE u.id_usuario = ? AND u.estado = 1
        ");
        $stmt->execute([$idUsuario]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function isUsernameTaken(string $username, int $excludeId = 0): bool {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM tb_usuario
            WHERE LOWER(nombre_usuario) = LOWER(?) AND id_usuario != ?
        ");
        $stmt->execute([$username, $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function isEmailTaken(string $email, int $excludeId = 0): bool {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM tb_usuario
            WHERE LOWER(correo) = LOWER(?) AND id_usuario != ?
        ");
        $stmt->execute([$email, $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function actualizarDatosBasicos(int $idUsuario, string $username, string $correo): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE tb_usuario
                SET nombre_usuario = ?, correo = ?
                WHERE id_usuario = ?
            ");
            return $stmt->execute([$username, $correo, $idUsuario]);
        } catch (Exception $e) {
            error_log("Error actualizarDatosBasicos: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarPassword(int $idUsuario, string $newPassword): bool {
        try {
            $newHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = $this->db->prepare("UPDATE tb_usuario SET password_hash = ? WHERE id_usuario = ?");
            return $stmt->execute([$newHash, $idUsuario]);
        } catch (Exception $e) {
            error_log("Error actualizarPassword: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarFoto(int $idUsuario, ?string $fotoPath): bool {
        try {
            $stmt = $this->db->prepare("UPDATE tb_usuario SET foto_perfil = ? WHERE id_usuario = ?");
            return $stmt->execute([$fotoPath, $idUsuario]);
        } catch (Exception $e) {
            error_log("Error actualizarFoto: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarPreferencias(int $idUsuario, string $preferenciasJson): bool {
        try {
            $stmt = $this->db->prepare("UPDATE tb_usuario SET preferencias = ? WHERE id_usuario = ?");
            return $stmt->execute([$preferenciasJson, $idUsuario]);
        } catch (Exception $e) {
            error_log("Error actualizarPreferencias: " . $e->getMessage());
            return false;
        }
    }

    public function login(string $username, string $password): ?array {
        $user = $this->getUsuarioByUsername($username);
        if ($user && password_verify($password, $user['password_hash'])) {
            // Auto-rehash transparente si el costo es inferior a 12 o el algoritmo ha evolucionado
            if (password_needs_rehash($user['password_hash'], PASSWORD_BCRYPT, ['cost' => 12])) {
                $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $this->updatePasswordHash((int)$user['id_usuario'], $newHash);
                $user['password_hash'] = $newHash;
            }
            return $user;
        }
        return null;
    }
}

