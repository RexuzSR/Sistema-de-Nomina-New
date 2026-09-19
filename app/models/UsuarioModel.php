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

    // ════════════════════════════════════════════════════════════
    // MÉTODOS DE ADMINISTRACIÓN DE USUARIOS (CRUD)
    // ════════════════════════════════════════════════════════════

    /**
     * Obtiene todos los usuarios con su rol y empleado vinculado.
     */
    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT u.id_usuario, u.nombre_usuario, u.correo, u.id_rol, u.id_empleado,
                   u.estado, u.fecha_creacion, u.foto_perfil,
                   r.nombre_rol,
                   CONCAT(e.primer_nombre, ' ', e.primer_apellido) AS nombre_empleado,
                   e.cedula AS cedula_empleado
            FROM tb_usuario u
            LEFT JOIN tb_rol r ON r.id_rol = u.id_rol
            LEFT JOIN tb_empleado e ON e.id_empleado = u.id_empleado
            ORDER BY u.fecha_creacion DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Obtiene todos los roles disponibles.
     */
    public function getRoles(): array {
        $stmt = $this->db->query("SELECT * FROM tb_rol ORDER BY id_rol");
        return $stmt->fetchAll();
    }

    /**
     * Crea un nuevo usuario con hash bcrypt.
     */
    public function crearUsuario(string $username, string $correo, string $password, int $idRol, ?int $idEmpleado = null): bool {
        try {
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = $this->db->prepare("
                INSERT INTO tb_usuario (nombre_usuario, correo, password_hash, id_rol, id_empleado, estado, fecha_creacion)
                VALUES (?, ?, ?, ?, ?, 1, NOW())
            ");
            return $stmt->execute([$username, $correo, $hash, $idRol, $idEmpleado]);
        } catch (Exception $e) {
            error_log("Error crearUsuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza datos de un usuario existente (sin cambiar contraseña).
     */
    public function actualizarUsuario(int $idUsuario, string $username, string $correo, int $idRol, ?int $idEmpleado = null): bool {
        try {
            $stmt = $this->db->prepare("
                UPDATE tb_usuario
                SET nombre_usuario = ?, correo = ?, id_rol = ?, id_empleado = ?
                WHERE id_usuario = ?
            ");
            return $stmt->execute([$username, $correo, $idRol, $idEmpleado, $idUsuario]);
        } catch (Exception $e) {
            error_log("Error actualizarUsuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambia el estado de un usuario (activar/desactivar).
     */
    public function cambiarEstado(int $idUsuario, int $nuevoEstado): bool {
        try {
            $stmt = $this->db->prepare("UPDATE tb_usuario SET estado = ? WHERE id_usuario = ?");
            return $stmt->execute([$nuevoEstado, $idUsuario]);
        } catch (Exception $e) {
            error_log("Error cambiarEstado usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene empleados activos que aún no tienen un usuario vinculado.
     */
    public function getEmpleadosSinUsuario(?int $excludeUsuarioId = null): array {
        $sql = "
            SELECT e.id_empleado, e.cedula,
                   CONCAT(e.primer_nombre, ' ', e.primer_apellido) AS nombre_completo
            FROM tb_empleado e
            WHERE e.estado = 1
              AND e.id_empleado NOT IN (
                  SELECT u.id_empleado FROM tb_usuario u WHERE u.id_empleado IS NOT NULL
        ";
        $params = [];
        if ($excludeUsuarioId) {
            $sql .= " AND u.id_usuario != ?";
            $params[] = $excludeUsuarioId;
        }
        $sql .= ") ORDER BY e.primer_apellido, e.primer_nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Cuenta total de usuarios activos.
     */
    public function contarUsuariosActivos(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM tb_usuario WHERE estado = 1");
        return (int)$stmt->fetchColumn();
    }
}
