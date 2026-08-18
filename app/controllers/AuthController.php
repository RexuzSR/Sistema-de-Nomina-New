<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

class AuthController extends Controller {
    public function login(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['usuario'])) {
            $this->redirect('index.php?r=dashboard');
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || (isset($_GET['format']) && $_GET['format'] === 'json');

            if (empty($username) || empty($password)) {
                $error = 'Ingresa usuario y contraseña.';
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $error]);
                    exit;
                }
            } else {
                $usuarioModel = new UsuarioModel();
                $user = $usuarioModel->login($username, $password);

                if ($user) {
                    $_SESSION['usuario'] = $user['nombre_usuario'];
                    $_SESSION['user_id'] = $user['id_usuario'];
                    $_SESSION['user_nombre'] = $user['nombre_usuario'];
                    $_SESSION['user_rol'] = $user['nombre_rol'] ?? 'Administrador';
                    $_SESSION['welcome_banner'] = true;

                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'redirect' => 'index.php?r=dashboard']);
                        exit;
                    }
                    $this->redirect('index.php?r=dashboard');
                } else {
                    $error = 'Usuario o contraseña incorrectos.';
                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'error' => $error]);
                        exit;
                    }
                }
            }
        }

        extract(['error' => $error]);

        // Fetch exact dashboard stats for the 1-to-1 door preview immersion
        require_once __DIR__ . '/../models/EmpleadoModel.php';
        require_once __DIR__ . '/../models/PermisoModel.php';
        require_once __DIR__ . '/../models/PeriodoModel.php';
        require_once __DIR__ . '/../models/NominaModel.php';

        $empleadoModel = new EmpleadoModel();
        $permisoModel = new PermisoModel();
        $periodoModel = new PeriodoModel();
        $nominaModel = new NominaModel();

        $empleados = $empleadoModel->getAll();
        $totalEmpleados = count(array_filter($empleados, fn($e) => $e['estado'] == 1));
        $totalInactivos = count(array_filter($empleados, fn($e) => $e['estado'] == 0));
        $empleadosAgregadosEsteMes = $empleadoModel->getEmpleadosAgregadosMesActualCount();

        $permisos = $permisoModel->getAll();
        $permisosPendientes = count(array_filter($permisos, fn($p) => $p['estado'] === 'Pendiente'));

        $ultimoPeriodo = $periodoModel->getUltimo();
        $totalNominasCount = $nominaModel->getTotalNominasCount();
        $montoTotalHistorico = $nominaModel->getMontoTotalHistorico();
        $montoUltimaNomina = $nominaModel->getMontoUltimaNomina();

        require __DIR__ . '/../views/auth/login.php';
    }

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();
        $this->redirect('index.php?r=auth/login');
    }
}
