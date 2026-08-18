<?php

class Router {
    private array $routes = [];

    public function add(string $route, string $controller, string $action): void {
        $this->routes[$route] = ['controller' => $controller, 'action' => $action];
    }

    public function dispatch(string $route): void {
        if (!isset($this->routes[$route])) {
            // Default route if not matched
            $route = 'dashboard';
        }

        $target = $this->routes[$route];
        $controllerName = $target['controller'];
        $actionName = $target['action'];

        $controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';
        if (!file_exists($controllerFile)) {
            die("Controlador no encontrado: " . htmlspecialchars($controllerName));
        }

        require_once $controllerFile;
        $controller = new $controllerName();
        if (!method_exists($controller, $actionName)) {
            die("Método no encontrado: " . htmlspecialchars($actionName));
        }

        $controller->$actionName();
    }
}
