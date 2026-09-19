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

        $result = $this->usuarioModel->crearUsuario($username, $correo, $password, $idRol, $idEmpleado);

        if ($result) {
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

        $result = $this->usuarioModel->actualizarUsuario($idUsuario, $username, $correo, $idRol, $idEmpleado);

        // Actualizar contraseña si se proporcionó una nueva
        if ($result && !empty($newPassword)) {
            if (strlen($newPassword) < 6) {
                echo json_encode(['success' => false, 'error' => 'La nueva contraseña debe tener al menos 6 caracteres.']);
                exit;
            }
            $this->usuarioModel->actualizarPassword($idUsuario, $newPassword);
        }

        if ($result) {
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

        if ($idUsuario < 1) {
            echo json_encode(['success' => false, 'error' => 'ID de usuario inválido.']);
            exit;
        }

        $result = $this->usuarioModel->cambiarEstado($idUsuario, $nuevoEstado);

        if ($result) {
            $msg = $nuevoEstado === 1 ? 'Usuario activado exitosamente.' : 'Usuario desactivado exitosamente.';
            echo json_encode(['success' => true, 'message' => $msg]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al cambiar el estado del usuario.']);
        }
        exit;
    }
}
