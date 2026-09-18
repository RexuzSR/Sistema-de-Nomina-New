<?php

require_once __DIR__ . '/Database.php';

class Security {
    private static ?string $cspNonce = null;

    /**
     * Devuelve el Nonce CSP único para la petición HTTP en curso.
     * Se almacena como propiedad estática en memoria para evitar condiciones de carrera entre pestañas.
     */
    public static function getCspNonce(): string {
        if (self::$cspNonce === null) {
            self::$cspNonce = base64_encode(random_bytes(16));
        }
        return self::$cspNonce;
    }

    /**
     * Determina si la conexión actual es segura (HTTPS).
     */
    public static function isHttps(): bool {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');
    }

    /**
     * Inicializa la sesión con parámetros y flags estrictos de seguridad.
     */
    public static function initSecureSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            if (!headers_sent()) {
                ini_set('session.use_only_cookies', '1');
                ini_set('session.use_strict_mode', '1');

                $isHttps = self::isHttps();

                session_set_cookie_params([
                    'lifetime' => 0,          // Expira al cerrar el navegador
                    'path'     => '/',
                    'domain'   => '',
                    'secure'   => $isHttps,   // Secure=1 en HTTPS, compatible con HTTP local
                    'httponly' => true,       // Previene acceso desde JS a la cookie de sesión
                    'samesite' => 'Lax'       // Protección contra peticiones cross-site CSRF
                ]);
            }

            session_start();
        }

        // Generar token CSRF base si no existe
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    /**
     * Invalida y destruye la sesión tanto en servidor como en el cliente (cookie con fecha pasada).
     */
    public static function destroySession(): void {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies") && !headers_sent()) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /**
     * Rotación de identificador de sesión.
     * @param bool $deleteOldSession true en login (corta fijación); false en rotación periódica (multi-pestaña)
     */
    public static function rotateSessionId(bool $deleteOldSession = false): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id($deleteOldSession);
            $_SESSION['last_regeneration'] = time();
        }
    }

    /**
     * Envía las cabeceras HTTP de seguridad y Content-Security-Policy con Nonce.
     */
    public static function setSecurityHeaders(): void {
        if (headers_sent()) {
            return;
        }

        $nonce = self::getCspNonce();

        header("X-Frame-Options: SAMEORIGIN");
        header("X-Content-Type-Options: nosniff");
        header("Referrer-Policy: strict-origin-when-cross-origin");
        header("Permissions-Policy: geolocation=(), camera=(), microphone=()");

        // CSP: compatible con manejadores inline (onclick) y scripts dinámicos de Chart.js
        $csp = "default-src 'self'; "
             . "script-src 'self' 'unsafe-inline'; "
             . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
             . "font-src 'self' https://fonts.gstatic.com data:; "
             . "img-src 'self' data:; "
             . "connect-src 'self'; "
             . "frame-ancestors 'none'; "
             . "form-action 'self';";

        header("Content-Security-Policy: {$csp}");

        // HSTS condicional solo si estamos en HTTPS real
        if (self::isHttps()) {
            header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
        }
    }

    /**
     * Evalúa el ciclo de vida de la sesión (inactividad y rotación periódica).
     * @param int $inactivityTimeout Segundos máximos de inactividad (por defecto 15 min = 900 s)
     * @param int $rotationInterval Segundos para rotación periódica (por defecto 30 min = 1800 s)
     */
    public static function checkSessionLifecycle(int $inactivityTimeout = 900, int $rotationInterval = 1800): array {
        if (empty($_SESSION['usuario'])) {
            return ['valid' => true, 'authenticated' => false];
        }

        $now = time();

        // 0. Validar si había un beacon de cierre pendiente
        if (!empty($_SESSION['pending_beacon_logout'])) {
            $beaconTime = (int)$_SESSION['pending_beacon_logout'];
            // Si la petición llega dentro de los 15 segundos, fue una recarga de página (F5/refresco) o navegación rápida:
            // cancelar el logout pendiente y mantener la sesión viva
            if (($now - $beaconTime) < 15) {
                unset($_SESSION['pending_beacon_logout']);
            } else {
                // Pasaron más de 15 segundos: el usuario efectivamente cerró la ventana
                self::logSecurityEvent(
                    'LOGOUT_BEACON',
                    'Sesión cerrada automáticamente por baliza al abandonar la pestaña',
                    $_SESSION['user_id'] ?? null,
                    $_SESSION['usuario'] ?? null
                );
                self::destroySession();
                return ['valid' => false, 'reason' => 'logout'];
            }
        }

        // 1. Validar inactividad
        $lastActivity = $_SESSION['last_activity'] ?? $now;
        if (($now - $lastActivity) > $inactivityTimeout) {
            self::logSecurityEvent(
                'SESSION_TIMEOUT',
                "Sesión vencida por inactividad (" . ($now - $lastActivity) . " s)",
                $_SESSION['user_id'] ?? null,
                $_SESSION['usuario'] ?? null
            );
            self::destroySession();
            return ['valid' => false, 'reason' => 'timeout'];
        }

        // Actualizar marca de última actividad
        $_SESSION['last_activity'] = $now;

        // 2. Rotación periódica con soporte multi-pestaña (false no borra inmediatamente el archivo anterior)
        $lastRotation = $_SESSION['last_regeneration'] ?? $now;
        if (($now - $lastRotation) > $rotationInterval) {
            self::rotateSessionId(false);
            self::logSecurityEvent(
                'SESSION_ROTATED',
                'Rotación periódica de session ID multi-pestaña ejecutada',
                $_SESSION['user_id'] ?? null,
                $_SESSION['usuario'] ?? null
            );
        }

        // 3. Verificación de anomalías (User-Agent drástico)
        if (!empty($_SESSION['user_agent']) && !empty($_SERVER['HTTP_USER_AGENT'])) {
            if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
                self::logSecurityEvent(
                    'SUSPICIOUS_CLIENT_CHANGE',
                    "Cambio de User-Agent detectado en sesión activa. Inicial: " . substr($_SESSION['user_agent'], 0, 100) . " | Actual: " . substr($_SERVER['HTTP_USER_AGENT'], 0, 100),
                    $_SESSION['user_id'] ?? null,
                    $_SESSION['usuario'] ?? null
                );
            }
        }

        return ['valid' => true, 'authenticated' => true];
    }

    /**
     * Obtiene la IP del cliente con validación anti-spoofing.
     * Solo confía en X-Forwarded-For si REMOTE_ADDR proviene de proxies locales de confianza.
     */
    public static function getClientIp(): string {
        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $trustedProxies = ['127.0.0.1', '::1', '10.0.0.1'];

        if (in_array($remoteAddr, $trustedProxies, true) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $clientIp = trim($parts[0]);
            if (filter_var($clientIp, FILTER_VALIDATE_IP)) {
                return $clientIp;
            }
        }

        return $remoteAddr;
    }

    /**
     * Helper de Token CSRF
     */
    public static function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Genera un input HTML oculto con el token CSRF para formularios POST.
     */
    public static function csrfField(): string {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Valida el token CSRF recibido por POST o encabezado HTTP usando hash_equals().
     */
    public static function validateCsrfRequest(): bool {
        $submittedToken = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $sessionToken = self::csrfToken();

        if (empty($submittedToken) || empty($sessionToken)) {
            return false;
        }

        return hash_equals($sessionToken, $submittedToken);
    }

    /**
     * Valida una petición baliza (sendBeacon) comprobando origen y token CSRF.
     */
    public static function validateBeaconRequest(): bool {
        // Validar mismo origen
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $host = $_SERVER['HTTP_HOST'] ?? '';

        $originValid = false;
        if (!empty($origin) && strpos($origin, $host) !== false) {
            $originValid = true;
        } elseif (!empty($referer) && strpos($referer, $host) !== false) {
            $originValid = true;
        }

        if (!$originValid) {
            return false;
        }

        return self::validateCsrfRequest();
    }

    /**
     * Control de fuerza bruta dual anti-DoS:
     * 1. Bloqueo por IP + Usuario (5 intentos = 10 min)
     * 2. Rate limit global por IP (20 intentos/hora = 30 min)
     */
    public static function checkLoginLockout(string $username, string $ip): ?array {
        try {
            $pdo = Database::getInstance();

            // 1. Revisar bloqueo específico IP + Usuario
            $stmt = $pdo->prepare("
                SELECT intentos, bloqueado_hasta
                FROM tb_login_intentos
                WHERE ip_address = ? AND username = ? AND bloqueado_hasta > NOW()
                LIMIT 1
            ");
            $stmt->execute([$ip, $username]);
            $row = $stmt->fetch();
            if ($row) {
                $seconds = strtotime($row['bloqueado_hasta']) - time();
                return [
                    'locked' => true,
                    'type' => 'user_ip',
                    'seconds_remaining' => max(1, $seconds),
                    'minutes_remaining' => ceil(max(1, $seconds) / 60)
                ];
            }

            // 2. Revisar rate limit global de la IP
            $stmt = $pdo->prepare("
                SELECT SUM(intentos) as total_intentos, MAX(bloqueado_hasta) as max_bloqueo
                FROM tb_login_intentos
                WHERE ip_address = ? AND ultimo_intento > (NOW() - INTERVAL 1 HOUR)
            ");
            $stmt->execute([$ip]);
            $global = $stmt->fetch();
            if ($global && !empty($global['max_bloqueo']) && strtotime($global['max_bloqueo']) > time()) {
                $seconds = strtotime($global['max_bloqueo']) - time();
                return [
                    'locked' => true,
                    'type' => 'ip_global',
                    'seconds_remaining' => max(1, $seconds),
                    'minutes_remaining' => ceil(max(1, $seconds) / 60)
                ];
            }

            if ($global && (int)($global['total_intentos'] ?? 0) >= 20) {
                // Bloquear globalmente la IP por 30 minutos
                $pdo->prepare("
                    UPDATE tb_login_intentos
                    SET bloqueado_hasta = (NOW() + INTERVAL 30 MINUTE)
                    WHERE ip_address = ?
                ")->execute([$ip]);

                return [
                    'locked' => true,
                    'type' => 'ip_global',
                    'seconds_remaining' => 1800,
                    'minutes_remaining' => 30
                ];
            }

            return null;
        } catch (Exception $e) {
            error_log("Error checkLoginLockout: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Registra un intento fallido de inicio de sesión.
     */
    public static function recordFailedLogin(string $username, string $ip): void {
        try {
            $pdo = Database::getInstance();

            $stmt = $pdo->prepare("
                SELECT id, intentos
                FROM tb_login_intentos
                WHERE ip_address = ? AND username = ?
                LIMIT 1
            ");
            $stmt->execute([$ip, $username]);
            $record = $stmt->fetch();

            if ($record) {
                $newIntentos = (int)$record['intentos'] + 1;
                $bloqueoSql = ($newIntentos >= 5) ? "(NOW() + INTERVAL 10 MINUTE)" : "NULL";

                $update = $pdo->prepare("
                    UPDATE tb_login_intentos
                    SET intentos = ?, ultimo_intento = NOW(), bloqueado_hasta = {$bloqueoSql}
                    WHERE id = ?
                ");
                $update->execute([$newIntentos, $record['id']]);

                if ($newIntentos >= 5) {
                    self::logSecurityEvent(
                        'LOCKOUT_IP_USER',
                        "Bloqueo de 10 min activado para usuario '{$username}' desde IP {$ip} tras {$newIntentos} intentos",
                        null,
                        $username
                    );
                }
            } else {
                $insert = $pdo->prepare("
                    INSERT INTO tb_login_intentos (ip_address, username, intentos, primer_intento, ultimo_intento, bloqueado_hasta)
                    VALUES (?, ?, 1, NOW(), NOW(), NULL)
                ");
                $insert->execute([$ip, $username]);
            }

            // Poda probabilística (2% de los requests fallidos) para limpiar registros viejos > 7 días
            if (random_int(1, 50) === 1) {
                $pdo->exec("DELETE FROM tb_login_intentos WHERE ultimo_intento < (NOW() - INTERVAL 7 DAY)");
            }
        } catch (Exception $e) {
            error_log("Error recordFailedLogin: " . $e->getMessage());
        }
    }

    /**
     * Reinicia los intentos fallidos al tener un login exitoso.
     */
    public static function resetLoginAttempts(string $username, string $ip): void {
        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("DELETE FROM tb_login_intentos WHERE ip_address = ? AND username = ?");
            $stmt->execute([$ip, $username]);
        } catch (Exception $e) {
            error_log("Error resetLoginAttempts: " . $e->getMessage());
        }
    }

    /**
     * Hashea contraseñas usando Bcrypt con costo 12.
     */
    public static function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Registra un evento en la pista de auditoría forense tb_auditoria_seguridad.
     */
    public static function logSecurityEvent(string $evento, ?string $detalles = null, ?int $idUsuario = null, ?string $username = null): void {
        try {
            $pdo = Database::getInstance();
            $ip = self::getClientIp();
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;

            $stmt = $pdo->prepare("
                INSERT INTO tb_auditoria_seguridad (id_usuario, username, evento, ip, user_agent, detalles, fecha_hora)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$idUsuario, $username, $evento, $ip, $ua, $detalles]);
        } catch (Exception $e) {
            error_log("Error logSecurityEvent: " . $e->getMessage());
        }
    }
}
