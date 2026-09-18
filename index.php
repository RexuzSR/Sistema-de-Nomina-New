<?php
/**
 * Front Controller principal para la arquitectura MVC
 */

require_once __DIR__ . '/core/Security.php';

// Inicializar sesión segura y aplicar cabeceras HTTP de protección (CSP con nonce, etc.)
Security::initSecureSession();
Security::setSecurityHeaders();

require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/Router.php';

$router = new Router();

// Definición de Rutas de Autenticación y Seguridad
$router->add('auth/login', 'AuthController', 'login');
$router->add('auth/logout', 'AuthController', 'logout');
$router->add('auth/ping', 'AuthController', 'ping');
$router->add('auth/beaconLogout', 'AuthController', 'beaconLogout');

// Módulos del Sistema
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

// Perfil de Usuario y Preferencias de Diseño
$router->add('perfil', 'PerfilController', 'index');
$router->add('perfil/actualizar', 'PerfilController', 'actualizar');
$router->add('perfil/actualizarPassword', 'PerfilController', 'actualizarPassword');
$router->add('perfil/actualizarFoto', 'PerfilController', 'actualizarFoto');
$router->add('perfil/eliminarFoto', 'PerfilController', 'eliminarFoto');
$router->add('perfil/guardarPreferencias', 'PerfilController', 'guardarPreferencias');
$router->add('perfil/revertirPreferencias', 'PerfilController', 'revertirPreferencias');

// Obtener ruta desde parámetro query 'r' (ej: index.php?r=empleados) o 'dashboard' por defecto
$route = isset($_GET['r']) ? trim($_GET['r'], '/') : 'dashboard';

// Despachar petición
$router->dispatch($route);
