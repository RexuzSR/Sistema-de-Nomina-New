<?php

abstract class Controller {
    protected function requireLogin(string $base = ''): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['usuario'])) {
            header('Location: ' . $base . 'index.php?r=auth/login');
            exit;
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
