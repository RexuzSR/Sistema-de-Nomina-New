<?php

require_once __DIR__ . '/Security.php';

abstract class Controller {
    /**
     * Valida que el usuario tenga una sesión activa y vigente.
     * Gestiona el tiempo de inactividad, la rotación periódica multi-pestaña y la concurrencia.
     */
    protected function requireLogin(string $base = '', bool $releaseSessionLock = false): void {
        Security::initSecureSession();

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
               || (isset($_GET['format']) && $_GET['format'] === 'json')
               || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);

        if (empty($_SESSION['usuario'])) {
            if ($isAjax) {
                header('Content-Type: application/json', true, 401);
                echo json_encode(['success' => false, 'session_expired' => true, 'redirect' => $base . 'index.php?r=auth/login']);
                exit;
            }
            header('Location: ' . $base . 'index.php?r=auth/login');
            exit;
        }

        $lifecycle = Security::checkSessionLifecycle();
        if (!$lifecycle['valid']) {
            $reason = $lifecycle['reason'] ?? 'timeout';
            if ($isAjax) {
                header('Content-Type: application/json', true, 401);
                echo json_encode(['success' => false, 'session_expired' => true, 'redirect' => $base . 'index.php?r=auth/login&reason=' . urlencode($reason)]);
                exit;
            }
            header('Location: ' . $base . 'index.php?r=auth/login&reason=' . urlencode($reason));
            exit;
        }

        // Si se solicita liberar el lock para permitir concurrencia en peticiones de lectura o cálculos largos
        if ($releaseSessionLock && session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
    }

    /**
     * Valida obligatoriamente el token CSRF para peticiones POST.
     * Aborta con HTTP 403 y registra auditoría si no coincide.
     */
    protected function validateCsrf(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCsrfRequest()) {
                Security::logSecurityEvent(
                    'CSRF_VIOLATION',
                    'Intento de petición POST bloqueado por token CSRF ausente o no coincidente en ' . ($_SERVER['REQUEST_URI'] ?? ''),
                    $_SESSION['user_id'] ?? null,
                    $_SESSION['usuario'] ?? null
                );

                $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                       || (isset($_GET['format']) && $_GET['format'] === 'json');

                if ($isAjax) {
                    header('Content-Type: application/json', true, 403);
                    echo json_encode(['success' => false, 'error' => 'Token CSRF inválido o expirado. Por favor recargue la página.']);
                    exit;
                }

                http_response_code(403);
                die('Error 403: Token de seguridad CSRF inválido o expirado. Por favor, vuelva a cargar la página e intente de nuevo.');
            }
        }
    }

    /**
     * Cierra la escritura de la sesión para evitar bloqueos por lock en scripts largos.
     */
    protected function releaseSessionLock(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
    }

    protected function render(string $viewPath, array $data = [], string $pageTitle = 'Sistema de Nómina', string $base = ''): void {
        extract($data);
        $pdo = Database::getInstance();
        
        require __DIR__ . '/../app/views/layout/header.php';
        require __DIR__ . '/../app/views/' . $viewPath . '.php';
        require __DIR__ . '/../app/views/layout/footer.php';
    }

    protected function redirect(string $url): void {
        header('Location: ' . $url);
        exit;
    }
}
