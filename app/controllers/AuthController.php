<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Security.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

class AuthController extends Controller {
    public function login(): void {
        Security::initSecureSession();

        if (!empty($_SESSION['usuario'])) {
            $this->redirect('index.php?r=dashboard');
        }

        $error = '';
        $ip = Security::getClientIp();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                   || (isset($_GET['format']) && $_GET['format'] === 'json');

            // 1. Verificación de bloqueo preventivo por fuerza bruta
            $lockout = Security::checkLoginLockout($username, $ip);
            if ($lockout !== null) {
                $error = "Demasiados intentos fallidos. Tu acceso está temporalmente suspendido por seguridad. Intenta nuevamente en {$lockout['minutes_remaining']} minuto(s).";
                if ($isAjax) {
                    header('Content-Type: application/json', true, 429);
                    echo json_encode(['success' => false, 'error' => $error, 'locked' => true, 'minutes_remaining' => $lockout['minutes_remaining']]);
                    exit;
                }
            } elseif (empty($username) || empty($password)) {
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
                    // Login exitoso: reiniciar contadores de fallo y cortar de raíz la fijación de sesión
                    Security::resetLoginAttempts($username, $ip);
                    Security::rotateSessionId(true);

                    $_SESSION['usuario'] = $user['nombre_usuario'];
                    $_SESSION['user_id'] = $user['id_usuario'];
                    $_SESSION['user_nombre'] = $user['nombre_usuario'];
                    $_SESSION['user_rol'] = $user['nombre_rol'] ?? 'Administrador';
                    $_SESSION['user_correo'] = $user['correo'] ?? '';
                    $_SESSION['user_avatar'] = $user['foto_perfil'] ?? '';
                    $_SESSION['user_preferencias'] = $user['preferencias'] ?? '';
                    $_SESSION['welcome_banner'] = true;
                    $_SESSION['last_activity'] = time();
                    $_SESSION['last_regeneration'] = time();
                    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                    Security::logSecurityEvent(
                        'LOGIN_SUCCESS',
                        "Inicio de sesión exitoso desde IP {$ip}",
                        (int)$user['id_usuario'],
                        $user['nombre_usuario']
                    );

                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'redirect' => 'index.php?r=dashboard']);
                        exit;
                    }
                    $this->redirect('index.php?r=dashboard');
                } else {
                    // Fallo de autenticación: registrar intento y auditar
                    Security::recordFailedLogin($username, $ip);
                    Security::logSecurityEvent(
                        'LOGIN_FAILED',
                        "Credenciales incorrectas para usuario '{$username}' desde IP {$ip}",
                        null,
                        $username
                    );

                    // Re-evaluar si con este fallo alcanzó el umbral de bloqueo
                    $postLockout = Security::checkLoginLockout($username, $ip);
                    if ($postLockout !== null) {
                        $error = "Has excedido el número máximo de intentos permitidos. Tu acceso ha sido bloqueado por {$postLockout['minutes_remaining']} minuto(s).";
                    } else {
                        $error = 'Usuario o contraseña incorrectos.';
                    }

                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'error' => $error]);
                        exit;
                    }
                }
            }
        }

        extract(['error' => $error]);

        // Fetch exact dashboard stats for the door preview immersion
        require_once __DIR__ . '/../models/EmpleadoModel.php';
        require_once __DIR__ . '/../models/PermisoModel.php';
        require_once __DIR__ . '/../models/PeriodoModel.php';
        require_once __DIR__ . '/../models/NominaModel.php';
        require_once __DIR__ . '/../models/TasaModel.php';

        $empleadoModel = new EmpleadoModel();
        $permisoModel = new PermisoModel();
        $periodoModel = new PeriodoModel();
        $nominaModel = new NominaModel();
        $tasaModel = new TasaModel();

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

        // Tasa BCV y cálculos bimonetarios idénticos a inicio.php
        $ultimaTasa = $tasaModel->getUltima();
        $valorTasaBcv = $ultimaTasa ? (float)$ultimaTasa['valor_tasa'] : 0.0;
        $fechaTasaBcv = $ultimaTasa ? $ultimaTasa['fecha'] : null;
        $montoUltimaNominaBs = $valorTasaBcv > 0 ? ($montoUltimaNomina * $valorTasaBcv) : 0.0;
        $montoTotalHistoricoBs = $valorTasaBcv > 0 ? ($montoTotalHistorico * $valorTasaBcv) : 0.0;
        $resumenFinanciero = $nominaModel->getResumenFinancieroUltimaNomina();
        $netoEfectivo = $montoUltimaNomina;
        $netoEfectivoBs = $montoUltimaNominaBs;
        $variacionPorcentaje = null;
        $excesoDeducciones = 0;

        require __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Cierre de sesión manual seguro vía POST con validación CSRF.
     */
    public function logout(): void {
        Security::initSecureSession();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
        }

        $userId = $_SESSION['user_id'] ?? null;
        $username = $_SESSION['usuario'] ?? null;

        Security::logSecurityEvent('LOGOUT_MANUAL', 'Cierre de sesión voluntario', $userId, $username);
        Security::destroySession();

        $this->redirect('index.php?r=auth/login&reason=logout');
    }

    /**
     * Endpoint de renovación de sesión (Ping) con rate limiting (< 5 s throttling).
     */
    public function ping(): void {
        Security::initSecureSession();

        header('Content-Type: application/json');

        if (empty($_SESSION['usuario'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'session_expired' => true, 'redirect' => 'index.php?r=auth/login&reason=timeout']);
            exit;
        }

        $now = microtime(true);
        if (isset($_SESSION['last_ping_time']) && ($now - $_SESSION['last_ping_time'] < 5.0)) {
            // Throttling: responder afirmativo sin sobrecargar I/O
            echo json_encode(['success' => true, 'throttled' => true, 'expires_in' => 900]);
            exit;
        }

        $_SESSION['last_ping_time'] = $now;
        $_SESSION['last_activity'] = time();

        echo json_encode(['success' => true, 'throttled' => false, 'expires_in' => 900]);
        exit;
    }

    /**
     * Cierre de sesión silencioso y seguro mediante baliza (navigator.sendBeacon)
     * al cerrar la última ventana activa.
     */
    public function beaconLogout(): void {
        Security::initSecureSession();

        if (!Security::validateBeaconRequest()) {
            Security::logSecurityEvent(
                'SUSPICIOUS_BEACON_ATTEMPT',
                'Petición beaconLogout rechazada por Origin o token CSRF no coincidente'
            );
            http_response_code(403);
            exit;
        }

        // Marcar logout pendiente con marca de tiempo.
        // Si el usuario simplemente recargó la página (F5), la siguiente petición inmediata cancelará el logout.
        // Si efectivamente cerró la pestaña, la sesión caducará inmediatamente tras la ventana de gracia.
        $_SESSION['pending_beacon_logout'] = time();

        http_response_code(204);
        exit;
    }
}
