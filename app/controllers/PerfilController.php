<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Security.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

class PerfilController extends Controller {

    private UsuarioModel $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
    }

    private function getLoggedInUser(): array {
        $this->requireLogin();
        $id = (int)($_SESSION['user_id'] ?? 0);
        $user = null;
        if ($id > 0) {
            $user = $this->usuarioModel->getUsuarioById($id);
        }
        if (!$user && !empty($_SESSION['usuario'])) {
            $user = $this->usuarioModel->getUsuarioByUsername($_SESSION['usuario']);
        }
        if (!$user) {
            $this->redirect('index.php?r=auth/logout');
        }
        return $user;
    }

    public function index(): void {
        $user = $this->getLoggedInUser();

        $preferencias = [];
        if (!empty($user['preferencias'])) {
            $decoded = json_decode($user['preferencias'], true);
            if (is_array($decoded)) {
                $preferencias = $decoded;
            }
        }

        $headerColor = $preferencias['header_color'] ?? '#0d251e';
        $primaryColor = $preferencias['primary_color'] ?? '#0f766e';
        $navLayout = $preferencias['nav_layout'] ?? 'header';

        $this->render('perfil/index', [
            'user' => $user,
            'preferencias' => $preferencias,
            'headerColor' => $headerColor,
            'primaryColor' => $primaryColor,
            'navLayout' => $navLayout,
            'msgSuccess' => $_SESSION['flash_success'] ?? null,
            'msgError' => $_SESSION['flash_error'] ?? null,
        ], 'Editar Perfil - Sistema de Nómina');

        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
    }

    public function actualizar(): void {
        $this->requireLogin();
        $this->validateCsrf();
        $user = $this->getLoggedInUser();
        $idUsuario = (int)$user['id_usuario'];

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
               || (isset($_GET['format']) && $_GET['format'] === 'json');

        $username = trim($_POST['nombre_usuario'] ?? '');
        $correo = trim($_POST['correo'] ?? '');

        if (empty($username) || strlen($username) < 3 || strlen($username) > 50) {
            $error = 'El nombre de usuario debe tener entre 3 y 50 caracteres.';
            if ($isAjax) {
                header('Content-Type: application/json', true, 422);
                echo json_encode(['success' => false, 'error' => $error]);
                exit;
            }
            $_SESSION['flash_error'] = $error;
            $this->redirect('index.php?r=perfil');
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $error = 'Por favor ingresa una dirección de correo electrónico válida.';
            if ($isAjax) {
                header('Content-Type: application/json', true, 422);
                echo json_encode(['success' => false, 'error' => $error]);
                exit;
            }
            $_SESSION['flash_error'] = $error;
            $this->redirect('index.php?r=perfil');
        }

        if ($this->usuarioModel->isUsernameTaken($username, $idUsuario)) {
            $error = "El nombre de usuario '{$username}' ya está en uso por otra cuenta.";
            if ($isAjax) {
                header('Content-Type: application/json', true, 409);
                echo json_encode(['success' => false, 'error' => $error]);
                exit;
            }
            $_SESSION['flash_error'] = $error;
            $this->redirect('index.php?r=perfil');
        }

        if ($this->usuarioModel->isEmailTaken($correo, $idUsuario)) {
            $error = "El correo electrónico '{$correo}' ya se encuentra registrado.";
            if ($isAjax) {
                header('Content-Type: application/json', true, 409);
                echo json_encode(['success' => false, 'error' => $error]);
                exit;
            }
            $_SESSION['flash_error'] = $error;
            $this->redirect('index.php?r=perfil');
        }

        $ok = $this->usuarioModel->actualizarDatosBasicos($idUsuario, $username, $correo);
        if ($ok) {
            $_SESSION['usuario'] = $username;
            $_SESSION['user_nombre'] = $username;
            $_SESSION['user_correo'] = $correo;

            Security::logSecurityEvent(
                'PROFILE_UPDATED',
                "Usuario {$idUsuario} actualizó sus datos básicos a username='{$username}', email='{$correo}'",
                $idUsuario,
                $username
            );

            Audit::log(
                'PERFIL',
                'MODIFICAR',
                "Actualizó sus datos básicos de perfil (Usuario: {$username}, Correo: {$correo})",
                'tb_usuario',
                $idUsuario,
                ['nombre_usuario' => $username, 'correo' => $correo],
                $idUsuario
            );

            $msg = '¡Perfil actualizado exitosamente!';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => $msg, 'username' => $username, 'correo' => $correo]);
                exit;
            }
            $_SESSION['flash_success'] = $msg;
            $this->redirect('index.php?r=perfil');
        } else {
            $error = 'Ocurrió un error al guardar los cambios en la base de datos.';
            if ($isAjax) {
                header('Content-Type: application/json', true, 500);
                echo json_encode(['success' => false, 'error' => $error]);
                exit;
            }
            $_SESSION['flash_error'] = $error;
            $this->redirect('index.php?r=perfil');
        }
    }

    public function actualizarPassword(): void {
        $this->requireLogin();
        $this->validateCsrf();
        $user = $this->getLoggedInUser();
        $idUsuario = (int)$user['id_usuario'];

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
               || (isset($_GET['format']) && $_GET['format'] === 'json');

        $passActual = $_POST['password_actual'] ?? '';
        $passNueva = $_POST['password_nueva'] ?? '';
        $passConfirmar = $_POST['password_confirmar'] ?? '';

        if (!password_verify($passActual, $user['password_hash'])) {
            $error = 'La contraseña actual no es correcta.';
            if ($isAjax) {
                header('Content-Type: application/json', true, 422);
                echo json_encode(['success' => false, 'error' => $error]);
                exit;
            }
            $_SESSION['flash_error'] = $error;
            $this->redirect('index.php?r=perfil');
        }

        if (strlen($passNueva) < 8) {
            $error = 'La nueva contraseña debe tener al menos 8 caracteres.';
            if ($isAjax) {
                header('Content-Type: application/json', true, 422);
                echo json_encode(['success' => false, 'error' => $error]);
                exit;
            }
            $_SESSION['flash_error'] = $error;
            $this->redirect('index.php?r=perfil');
        }

        if ($passNueva !== $passConfirmar) {
            $error = 'La confirmación de la contraseña no coincide.';
            if ($isAjax) {
                header('Content-Type: application/json', true, 422);
                echo json_encode(['success' => false, 'error' => $error]);
                exit;
            }
            $_SESSION['flash_error'] = $error;
            $this->redirect('index.php?r=perfil');
        }

        $ok = $this->usuarioModel->actualizarPassword($idUsuario, $passNueva);
        if ($ok) {
            Security::rotateSessionId(true);
            Security::logSecurityEvent(
                'PASSWORD_CHANGED',
                "Usuario {$idUsuario} modificó exitosamente su contraseña",
                $idUsuario,
                $user['nombre_usuario']
            );

            Audit::log(
                'PERFIL',
                'PASSWORD_CHANGE',
                "Actualizó su contraseña de acceso",
                'tb_usuario',
                $idUsuario,
                null,
                $idUsuario
            );

            $msg = '¡Contraseña actualizada correctamente!';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => $msg]);
                exit;
            }
            $_SESSION['flash_success'] = $msg;
            $this->redirect('index.php?r=perfil');
        } else {
            $error = 'Error al actualizar la contraseña.';
            if ($isAjax) {
                header('Content-Type: application/json', true, 500);
                echo json_encode(['success' => false, 'error' => $error]);
                exit;
            }
            $_SESSION['flash_error'] = $error;
            $this->redirect('index.php?r=perfil');
        }
    }

    public function actualizarFoto(): void {
        $this->requireLogin();
        $this->validateCsrf();
        $user = $this->getLoggedInUser();
        $idUsuario = (int)$user['id_usuario'];

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
               || (isset($_GET['format']) && $_GET['format'] === 'json');

        // Opción 1: Selección de Avatar Preset
        if (!empty($_POST['avatar_preset'])) {
            $preset = trim($_POST['avatar_preset']);
            if ($preset === 'multitel') {
                if ($this->usuarioModel->isUsuarioProtegido($idUsuario)) {
                    $fotoPath = 'assets/images/logo_multitel.png';
                    $this->usuarioModel->actualizarFoto($idUsuario, $fotoPath);
                    $_SESSION['user_avatar'] = $fotoPath;

                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'foto_perfil' => $fotoPath, 'message' => '¡Emblema Multitel seleccionado con éxito!']);
                        exit;
                    }
                    $_SESSION['flash_success'] = '¡Emblema Multitel actualizado!';
                    $this->redirect('index.php?r=perfil');
                } else {
                    $error = 'El logo descargado de Multitel en el sistema es exclusivo para los administradores principales. Si deseas utilizarlo, debes subirlo desde tu dispositivo.';
                    if ($isAjax) {
                        header('Content-Type: application/json', true, 403);
                        echo json_encode(['success' => false, 'error' => $error]);
                        exit;
                    }
                    $_SESSION['flash_error'] = $error;
                    $this->redirect('index.php?r=perfil');
                }
            }

            // Validar que sea un preset permitido (ej: preset_1, preset_2, etc.)
            $validPresets = ['preset_1', 'preset_2', 'preset_3', 'preset_4', 'preset_5', 'preset_6'];
            if (in_array($preset, $validPresets, true)) {
                $fotoPath = 'assets/images/avatars/' . $preset . '.svg';
                $this->usuarioModel->actualizarFoto($idUsuario, $fotoPath);
                $_SESSION['user_avatar'] = $fotoPath;

                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'foto_perfil' => $fotoPath, 'message' => '¡Avatar seleccionado con éxito!']);
                    exit;
                }
                $_SESSION['flash_success'] = '¡Avatar actualizado!';
                $this->redirect('index.php?r=perfil');
            }
        }

        // Opción 2: Subida de archivo
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['foto'];
            $maxSize = 4 * 1024 * 1024; // 4MB

            if ($file['size'] > $maxSize) {
                $error = 'El archivo no debe exceder 4MB.';
                if ($isAjax) {
                    header('Content-Type: application/json', true, 422);
                    echo json_encode(['success' => false, 'error' => $error]);
                    exit;
                }
                $_SESSION['flash_error'] = $error;
                $this->redirect('index.php?r=perfil');
            }

            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, $allowedMimes, true)) {
                $error = 'El formato debe ser JPG, PNG, WEBP o GIF.';
                if ($isAjax) {
                    header('Content-Type: application/json', true, 422);
                    echo json_encode(['success' => false, 'error' => $error]);
                    exit;
                }
                $_SESSION['flash_error'] = $error;
                $this->redirect('index.php?r=perfil');
            }

            $ext = match($mime) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'image/gif' => 'gif',
                default => 'jpg'
            };

            $uploadDir = __DIR__ . '/../../assets/uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = 'avatar_' . $idUsuario . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            $destPath = $uploadDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $destPath)) {
                $relPath = 'assets/uploads/avatars/' . $fileName;

                // Eliminar foto previa si era personalizada en uploads
                if (!empty($user['foto_perfil']) && strpos($user['foto_perfil'], 'assets/uploads/avatars/') === 0) {
                    $oldPath = __DIR__ . '/../../' . $user['foto_perfil'];
                    if (file_exists($oldPath) && is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                $this->usuarioModel->actualizarFoto($idUsuario, $relPath);
                $_SESSION['user_avatar'] = $relPath;

                $msg = '¡Foto de perfil actualizada con éxito!';
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'foto_perfil' => $relPath, 'message' => $msg]);
                    exit;
                }
                $_SESSION['flash_success'] = $msg;
                $this->redirect('index.php?r=perfil');
            } else {
                $error = 'No se pudo guardar la imagen en el servidor.';
                if ($isAjax) {
                    header('Content-Type: application/json', true, 500);
                    echo json_encode(['success' => false, 'error' => $error]);
                    exit;
                }
                $_SESSION['flash_error'] = $error;
                $this->redirect('index.php?r=perfil');
            }
        }

        $error = 'No se seleccionó ningún archivo de imagen.';
        if ($isAjax) {
            header('Content-Type: application/json', true, 422);
            echo json_encode(['success' => false, 'error' => $error]);
            exit;
        }
        $_SESSION['flash_error'] = $error;
        $this->redirect('index.php?r=perfil');
    }

    public function eliminarFoto(): void {
        $this->requireLogin();
        $this->validateCsrf();
        $user = $this->getLoggedInUser();
        $idUsuario = (int)$user['id_usuario'];

        if (!empty($user['foto_perfil']) && strpos($user['foto_perfil'], 'assets/uploads/avatars/') === 0) {
            $oldPath = __DIR__ . '/../../' . $user['foto_perfil'];
            if (file_exists($oldPath) && is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        $this->usuarioModel->actualizarFoto($idUsuario, null);
        $_SESSION['user_avatar'] = '';

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
               || (isset($_GET['format']) && $_GET['format'] === 'json');

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Foto de perfil eliminada.']);
            exit;
        }
        $_SESSION['flash_success'] = 'Foto eliminada. Se utilizará la inicial de tu nombre.';
        $this->redirect('index.php?r=perfil');
    }

    public function guardarPreferencias(): void {
        $this->requireLogin();
        $this->validateCsrf();
        $user = $this->getLoggedInUser();
        $idUsuario = (int)$user['id_usuario'];

        $headerColor = trim($_POST['header_color'] ?? '#0d251e');
        $primaryColor = trim($_POST['primary_color'] ?? '#0f766e');
        $navLayout = trim($_POST['nav_layout'] ?? 'header');

        // Validar formato hexadecimal
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $headerColor)) {
            $headerColor = '#0d251e';
        }
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $primaryColor)) {
            $primaryColor = '#0f766e';
        }
        if (!in_array($navLayout, ['header', 'sidebar'], true)) {
            $navLayout = 'header';
        }

        $prefs = [
            'header_color' => $headerColor,
            'primary_color' => $primaryColor,
            'nav_layout' => $navLayout,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $json = json_encode($prefs);
        $this->usuarioModel->actualizarPreferencias($idUsuario, $json);
        $_SESSION['user_preferencias'] = $json;

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => '¡Preferencias de diseño guardadas exitosamente!',
            'preferencias' => $prefs
        ]);
        exit;
    }

    public function revertirPreferencias(): void {
        $this->requireLogin();
        $this->validateCsrf();
        $user = $this->getLoggedInUser();
        $idUsuario = (int)$user['id_usuario'];

        // Revertir a colores predeterminados de fábrica
        $defaultPrefs = [
            'header_color' => '#0d251e',
            'primary_color' => '#0f766e',
            'nav_layout' => 'header',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $json = json_encode($defaultPrefs);
        $this->usuarioModel->actualizarPreferencias($idUsuario, $json);
        $_SESSION['user_preferencias'] = $json;

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => '¡Colores y botones revertidos al diseño original!',
            'preferencias' => $defaultPrefs
        ]);
        exit;
    }
}
