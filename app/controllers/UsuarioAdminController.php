<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Security.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

class UsuarioAdminController extends Controller {

    private UsuarioModel $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Lista todos los usuarios del sistema.
     * Solo accesible para Administradores.
     */
    public function index(): void {
        $this->requireRole(['Administrador']);

        $usuarios = $this->usuarioModel->getAll();
        $roles = $this->usuarioModel->getRoles();
        $empleadosSinUsuario = $this->usuarioModel->getEmpleadosSinUsuario();

        $this->render('usuarios/index', [
            'usuarios' => $usuarios,
            'roles' => $roles,
            'empleadosSinUsuario' => $empleadosSinUsuario,
        ], 'Gestión de Usuarios');
    }

    /**
     * Crea un nuevo usuario vía AJAX POST.
     */
    public function crear(): void {
        $this->requireRole(['Administrador']);
        $this->validateCsrf();

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
            exit;
        }

        $username = trim($_POST['nombre_usuario'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $password = $_POST['password'] ?? '';
        $idRol = (int)($_POST['id_rol'] ?? 0);
        $idEmpleado = !empty($_POST['id_empleado']) ? (int)$_POST['id_empleado'] : null;

        // Validaciones
        $errors = [];
        if (empty($username) || strlen($username) < 3) {
            $errors[] = 'El nombre de usuario debe tener al menos 3 caracteres.';
        }
        if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Ingresa un correo electrónico válido.';
        }
        if (empty($password) || strlen($password) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
        }
        if ($idRol < 1 || $idRol > 3) {
            $errors[] = 'Selecciona un rol válido.';
        }
        if ($this->usuarioModel->isUsernameTaken($username)) {
            $errors[] = 'Ese nombre de usuario ya está en uso.';
        }
        if ($this->usuarioModel->isEmailTaken($correo)) {
            $errors[] = 'Ese correo electrónico ya está registrado.';
        }

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'error' => implode(' ', $errors)]);
            exit;
        }

        // Subida desde dispositivo o selección de preset
        $subida = $this->procesarSubidaAvatar(0);
        if ($subida !== null) {
            $fotoPerfil = $subida;
        } else {
            $fotoPerfil = trim($_POST['foto_perfil'] ?? '');
            // RESTRICCIÓN: El logo descargado de Multitel está reservado exclusivamente para los 2 usuarios principales
            if (strpos($fotoPerfil, 'logo_multitel') !== false) {
                echo json_encode([
                    'success' => false,
                    'error' => 'El logo institucional predeterminado de Multitel está reservado exclusivamente para los usuarios principales del sistema.'
                ]);
                exit;
            }
            if ($fotoPerfil === '') {
                $fotoPerfil = 'assets/images/avatars/preset_1.svg';
            }
        }

        $result = $this->usuarioModel->crearUsuario($username, $correo, $password, $idRol, $idEmpleado, $fotoPerfil);

        if ($result) {
            Audit::log('USUARIOS', 'CREAR', "Creó al nuevo usuario '{$username}' ({$correo}) con rol ID {$idRol}", 'tb_usuario', is_numeric($result) ? (int)$result : null, [
                'username' => $username,
                'correo' => $correo,
                'rol_id' => $idRol,
                'empleado_id' => $idEmpleado,
                'foto_perfil' => $fotoPerfil
            ]);
            echo json_encode(['success' => true, 'message' => 'Usuario creado exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al crear el usuario. Intenta nuevamente.']);
        }
        exit;
    }

    /**
     * Edita un usuario existente vía AJAX POST.
     */
    public function editar(): void {
        $this->requireRole(['Administrador']);
        $this->validateCsrf();

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
            exit;
        }

        $idUsuario = (int)($_POST['id_usuario'] ?? 0);
        $username = trim($_POST['nombre_usuario'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $idRol = (int)($_POST['id_rol'] ?? 0);
        $idEmpleado = !empty($_POST['id_empleado']) ? (int)$_POST['id_empleado'] : null;
        $newPassword = $_POST['password'] ?? '';
        
        $isProtegido = $this->usuarioModel->isUsuarioProtegido($idUsuario);

        // Subida desde dispositivo o selección de preset
        $subida = $this->procesarSubidaAvatar($idUsuario);
        if ($subida !== null) {
            $fotoPerfil = $subida;
        } else {
            $fotoPerfil = isset($_POST['foto_perfil']) ? trim($_POST['foto_perfil']) : null;
            // RESTRICCIÓN: Solo los usuarios protegidos del sistema pueden ponerse el logo pre-instalado
            if ($fotoPerfil !== null && strpos($fotoPerfil, 'logo_multitel') !== false && !$isProtegido) {
                echo json_encode([
                    'success' => false,
                    'error' => 'El logo institucional pre-instalado de Multitel está reservado exclusivamente para los administradores del sistema. Para utilizar este u otro logo, debes subirlo desde tu dispositivo.'
                ]);
                exit;
            }
        }

        if ($idUsuario < 1) {
            echo json_encode(['success' => false, 'error' => 'ID de usuario inválido.']);
            exit;
        }

        // Validaciones
        $errors = [];
        if (empty($username) || strlen($username) < 3) {
            $errors[] = 'El nombre de usuario debe tener al menos 3 caracteres.';
        }
        if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Ingresa un correo electrónico válido.';
        }
        if ($idRol < 1 || $idRol > 3) {
            $errors[] = 'Selecciona un rol válido.';
        }
        if ($this->usuarioModel->isUsernameTaken($username, $idUsuario)) {
            $errors[] = 'Ese nombre de usuario ya está en uso.';
        }
        if ($this->usuarioModel->isEmailTaken($correo, $idUsuario)) {
            $errors[] = 'Ese correo electrónico ya está registrado.';
        }

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'error' => implode(' ', $errors)]);
            exit;
        }

        $result = $this->usuarioModel->actualizarUsuario($idUsuario, $username, $correo, $idRol, $idEmpleado, $fotoPerfil);

        // Actualizar contraseña si se proporcionó una nueva
        if ($result && !empty($newPassword)) {
            if (strlen($newPassword) < 6) {
                echo json_encode(['success' => false, 'error' => 'La nueva contraseña debe tener al menos 6 caracteres.']);
                exit;
            }
            $this->usuarioModel->actualizarPassword($idUsuario, $newPassword);
        }

        if ($result) {
            Audit::log('USUARIOS', 'MODIFICAR', "Modificó los datos del usuario ID {$idUsuario} ('{$username}')" . (!empty($newPassword) ? ' (con nueva contraseña)' : ''), 'tb_usuario', $idUsuario, [
                'username' => $username,
                'correo' => $correo,
                'rol_id' => $idRol,
                'empleado_id' => $idEmpleado,
                'foto_perfil' => $fotoPerfil,
                'cambio_password' => !empty($newPassword)
            ]);
            echo json_encode(['success' => true, 'message' => 'Usuario actualizado exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar el usuario.']);
        }
        exit;
    }

    /**
     * Cambia el estado de un usuario (activar/desactivar) vía AJAX POST.
     */
    public function cambiarEstado(): void {
        $this->requireRole(['Administrador']);
        $this->validateCsrf();

        header('Content-Type: application/json');

        $idUsuario = (int)($_POST['id_usuario'] ?? 0);
        $nuevoEstado = (int)($_POST['estado'] ?? 0);
        $currentUserId = (int)($_SESSION['user_id'] ?? 0);

        // Prevenir que el admin se desactive a sí mismo
        if ($idUsuario === $currentUserId) {
            echo json_encode(['success' => false, 'error' => 'No puedes desactivar tu propia cuenta.']);
            exit;
        }

        // Prevenir que los usuarios protegidos del sistema sean desactivados
        if ($this->usuarioModel->isUsuarioProtegido($idUsuario)) {
            echo json_encode(['success' => false, 'error' => 'Este usuario principal del sistema está protegido y no puede ser desactivado.']);
            exit;
        }

        if ($idUsuario < 1) {
            echo json_encode(['success' => false, 'error' => 'ID de usuario inválido.']);
            exit;
        }

        $result = $this->usuarioModel->cambiarEstado($idUsuario, $nuevoEstado);

        if ($result) {
            $msg = $nuevoEstado === 1 ? 'Usuario activado exitosamente.' : 'Usuario desactivado exitosamente.';
            Audit::log('USUARIOS', 'CAMBIAR_ESTADO', "Cambió el estado del usuario ID {$idUsuario} a " . ($nuevoEstado === 1 ? 'Activado' : 'Desactivado'), 'tb_usuario', $idUsuario, [
                'nuevo_estado' => $nuevoEstado
            ]);
            echo json_encode(['success' => true, 'message' => $msg]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al cambiar el estado del usuario.']);
        }
        exit;
    }

    /**
     * Elimina un usuario del sistema (los usuarios principales del sistema están protegidos contra eliminación).
     */
    public function eliminar(): void {
        $this->requireRole(['Administrador']);
        $this->validateCsrf();

        header('Content-Type: application/json');

        $idUsuario = (int)($_POST['id_usuario'] ?? 0);
        $currentUserId = (int)($_SESSION['user_id'] ?? 0);

        if ($idUsuario === $currentUserId) {
            echo json_encode(['success' => false, 'error' => 'No puedes eliminar tu propia cuenta de usuario.']);
            exit;
        }

        if ($this->usuarioModel->isUsuarioProtegido($idUsuario)) {
            echo json_encode(['success' => false, 'error' => 'Este usuario principal del sistema está protegido y nunca puede ser eliminado.']);
            exit;
        }

        $res = $this->usuarioModel->eliminarUsuario($idUsuario);
        if ($res) {
            Audit::log('USUARIOS', 'ELIMINAR', "Eliminó al usuario ID {$idUsuario}", 'tb_usuario', $idUsuario);
            echo json_encode(['success' => true, 'message' => 'Usuario eliminado exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo eliminar el usuario. Intenta nuevamente.']);
        }
        exit;
    }

    /**
     * Procesa la subida de un archivo de foto desde el dispositivo del usuario.
     */
    private function procesarSubidaAvatar(int $idUsuario = 0): ?string {
        if (isset($_FILES['foto_archivo']) && $_FILES['foto_archivo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['foto_archivo'];
            $maxSize = 4 * 1024 * 1024; // 4MB
            if ($file['size'] > $maxSize) {
                return null;
            }

            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, $allowedMimes, true)) {
                return null;
            }

            $ext = match($mime) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'image/gif' => 'gif',
                default => 'png'
            };

            $uploadDir = __DIR__ . '/../../assets/uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = 'avatar_usr_' . ($idUsuario > 0 ? $idUsuario : 'new_' . bin2hex(random_bytes(4))) . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destPath = $uploadDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $destPath)) {
                return 'assets/uploads/avatars/' . $fileName;
            }
        }
        return null;
    }
}
