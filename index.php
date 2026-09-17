<?php
/**
 * Front Controller principal para la arquitectura MVC
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/Router.php';

$router = new Router();

// Definición de Rutas MVC
$router->add('auth/login', 'AuthController', 'login');
$router->add('auth/logout', 'AuthController', 'logout');

$router->add('dashboard', 'DashboardController', 'index');
$router->add('api/search', 'DashboardController', 'search');

$router->add('empleados', 'EmpleadoController', 'index');
$router->add('empleados/crear', 'EmpleadoController', 'crear');
$router->add('empleados/editar', 'EmpleadoController', 'editar');
$router->add('empleados/cambiarEstado', 'EmpleadoController', 'cambiarEstado');

$router->add('permisos', 'PermisoController', 'index');
$router->add('permisos/solicitar', 'PermisoController', 'solicitar');
$router->add('permisos/aprobar', 'PermisoController', 'aprobar');

$router->add('tasas', 'TasaController', 'index');
$router->add('tasas/crear', 'TasaController', 'crear');

$router->add('periodos', 'PeriodoController', 'index');
$router->add('periodos/crear', 'PeriodoController', 'crear');

$router->add('nomina/generar', 'NominaController', 'generar');
$router->add('nomina/detalle', 'NominaController', 'detalle');
$router->add('nomina/ver', 'NominaController', 'ver');
$router->add('nomina/recibo', 'NominaController', 'recibo');
$router->add('nomina/exportExcel', 'NominaController', 'exportExcel');

// Obtener ruta desde parámetro query 'r' (ej: index.php?r=empleados) o 'dashboard' por defecto
$route = isset($_GET['r']) ? trim($_GET['r'], '/') : 'dashboard';

// Despachar petición
$router->dispatch($route);
