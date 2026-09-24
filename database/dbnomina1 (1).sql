-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-09-2026 a las 08:15:20
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dbnomina1`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_auditoria`
--

CREATE TABLE `tb_auditoria` (
  `id_auditoria` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `modulo` varchar(50) DEFAULT NULL,
  `accion` varchar(50) NOT NULL,
  `tabla_afectada` varchar(50) NOT NULL,
  `id_registro_afectado` int(11) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `fecha_hora` datetime DEFAULT current_timestamp(),
  `detalle` text DEFAULT NULL,
  `detalles_json` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_auditoria`
--

INSERT INTO `tb_auditoria` (`id_auditoria`, `id_usuario`, `modulo`, `accion`, `tabla_afectada`, `id_registro_afectado`, `ip`, `fecha_hora`, `detalle`, `detalles_json`) VALUES
(2, 1, 'SISTEMA', 'CONFIGURACIÓN', 'tb_auditoria', NULL, '127.0.0.1', '2026-09-19 23:12:28', 'Configuración e inicialización del Historial de Actividades del Sistema', '{\n    \"modulo\": \"Historial\",\n    \"estado\": \"activo\"\n}'),
(3, 1, 'TASAS', 'SINCRONIZAR_BCV', 'tb_tasa_bcv', NULL, '127.0.0.1', '2026-09-19 23:42:29', 'Sincronizó tasa oficial del BCV: 848.5458 Bs/USD', '{\n    \"exito\": true,\n    \"origen\": \"online\",\n    \"accion\": \"actualizada\",\n    \"tasa\": 848.5458,\n    \"fecha\": \"2026-09-18\",\n    \"fuente\": \"BCV Oficial (DolarApi)\",\n    \"mensaje\": \"Tasa BCV sincronizada con éxito: 1 USD = 848,5458 Bs. (Vigencia: 2026-09-18)\"\n}'),
(4, 1, 'TASAS', 'SINCRONIZAR_BCV', 'tb_tasa_bcv', NULL, '::1', '2026-09-19 23:49:40', 'Sincronizó tasa oficial del BCV: 848.5458 Bs/USD', '{\n    \"exito\": true,\n    \"origen\": \"online\",\n    \"accion\": \"actualizada\",\n    \"tasa\": 848.5458,\n    \"fecha\": \"2026-09-18\",\n    \"fuente\": \"BCV Oficial (DolarApi)\",\n    \"mensaje\": \"Tasa BCV sincronizada con éxito: 1 USD = 848,5458 Bs. (Vigencia: 2026-09-18)\"\n}'),
(5, 1, 'CONFIGURACION', 'MODIFICAR', 'tb_configuracion_empresa', 1, '::1', '2026-09-20 00:00:17', 'Actualizó los parámetros globales de empresa: Régimen Clase_II, SSO Trab: 4%, RPE Trab: 0.5%, FAOV Trab: 5%, Salario Mínimo: Bs. 130, Tope: 5 SM', '{\n    \"csrf_token\": \"1eed4359b6d63051abca722fa6bff894d9c67057a021a6ca54c060efba23a908\",\n    \"nombre_empresa\": \"EMPRESA DEMO C.A.\",\n    \"rif\": \"J-12345678-9\",\n    \"regimen_ivss\": \"Clase_II\",\n    \"tasa_sso_trabajador\": \"4.00\",\n    \"tasa_sso_patronal\": \"10.00\",\n    \"tasa_rpe_trabajador\": \"0.50\",\n    \"tasa_rpe_patronal\": \"2.10\",\n    \"tasa_faov_trabajador\": \"5.00\",\n    \"tasa_faov_patronal\": \"9.00\",\n    \"salario_minimo_legal\": \"130.00\",\n    \"tope_sso_salarios\": \"5\",\n    \"tope_rpe_salarios\": \"5\",\n    \"semanas_factor_anual\": \"52\"\n}'),
(6, 1, 'CONFIGURACION', 'MODIFICAR', 'tb_configuracion_empresa', 1, '::1', '2026-09-20 00:00:35', 'Actualizó los parámetros globales de empresa: Régimen Clase_II, SSO Trab: 4%, RPE Trab: 0.5%, FAOV Trab: 1%, Salario Mínimo: Bs. 130, Tope: 5 SM', '{\n    \"csrf_token\": \"1eed4359b6d63051abca722fa6bff894d9c67057a021a6ca54c060efba23a908\",\n    \"nombre_empresa\": \"EMPRESA DEMO C.A.\",\n    \"rif\": \"J-12345678-9\",\n    \"regimen_ivss\": \"Clase_II\",\n    \"tasa_sso_trabajador\": \"4.00\",\n    \"tasa_sso_patronal\": \"10.00\",\n    \"tasa_rpe_trabajador\": \"0.50\",\n    \"tasa_rpe_patronal\": \"2.10\",\n    \"tasa_faov_trabajador\": \"1.00\",\n    \"tasa_faov_patronal\": \"2.00\",\n    \"salario_minimo_legal\": \"130.00\",\n    \"tope_sso_salarios\": \"5\",\n    \"tope_rpe_salarios\": \"5\",\n    \"semanas_factor_anual\": \"52\"\n}'),
(7, 1, 'TASAS', 'SINCRONIZAR_BCV', 'tb_tasa_bcv', NULL, '::1', '2026-09-20 00:10:21', 'Sincronizó tasa oficial del BCV: 848.5458 Bs/USD', '{\n    \"exito\": true,\n    \"origen\": \"online\",\n    \"accion\": \"actualizada\",\n    \"tasa\": 848.5458,\n    \"fecha\": \"2026-09-18\",\n    \"fuente\": \"BCV Oficial (DolarApi)\",\n    \"mensaje\": \"Tasa BCV sincronizada con éxito: 1 USD = 848,5458 Bs. (Vigencia: 2026-09-18)\"\n}'),
(8, 1, 'TASAS', 'SINCRONIZAR_BCV', 'tb_tasa_bcv', NULL, '::1', '2026-09-20 00:14:46', 'Sincronizó tasa oficial del BCV: 848.5458 Bs/USD', '{\n    \"exito\": true,\n    \"origen\": \"online\",\n    \"accion\": \"actualizada\",\n    \"tasa\": 848.5458,\n    \"fecha\": \"2026-09-18\",\n    \"fuente\": \"BCV Oficial (DolarApi)\",\n    \"mensaje\": \"Tasa BCV sincronizada con éxito: 1 USD = 848,5458 Bs. (Vigencia: 2026-09-18)\"\n}'),
(9, 1, 'ACCESO', 'LOGOUT', 'tb_usuario', 1, '::1', '2026-09-20 00:17:25', 'Cierre de sesión del usuario \'samu\'', NULL),
(10, 1, 'ACCESO', 'LOGIN', 'tb_usuario', 1, '::1', '2026-09-20 00:47:26', 'Inicio de sesión exitoso para el usuario \'samu\'', '{\n    \"rol\": \"Administrador\",\n    \"ip\": \"::1\"\n}'),
(11, 1, 'TASAS', 'SINCRONIZAR_BCV', 'tb_tasa_bcv', NULL, '::1', '2026-09-20 00:47:37', 'Sincronizó tasa oficial del BCV: 848.5458 Bs/USD', '{\n    \"exito\": true,\n    \"origen\": \"online\",\n    \"accion\": \"actualizada\",\n    \"tasa\": 848.5458,\n    \"fecha\": \"2026-09-18\",\n    \"fuente\": \"BCV Oficial (DolarApi)\",\n    \"mensaje\": \"Tasa BCV sincronizada con éxito: 1 USD = 848,5458 Bs. (Vigencia: 2026-09-18)\"\n}'),
(12, 1, 'USUARIOS', 'MODIFICAR', 'tb_usuario', 2, '::1', '2026-09-20 01:20:05', 'Modificó los datos del usuario ID 2 (\'mobile\')', '{\n    \"username\": \"mobile\",\n    \"correo\": \"samueljosealexanderr@gmail.com\",\n    \"rol_id\": 1,\n    \"empleado_id\": null,\n    \"foto_perfil\": \"assets\\/images\\/logo_multitel.png\",\n    \"cambio_password\": false\n}'),
(13, 1, 'USUARIOS', 'MODIFICAR', 'tb_usuario', 1, '::1', '2026-09-20 01:20:11', 'Modificó los datos del usuario ID 1 (\'samu\')', '{\n    \"username\": \"samu\",\n    \"correo\": \"samueljosealexander@gmail.com\",\n    \"rol_id\": 1,\n    \"empleado_id\": 1,\n    \"foto_perfil\": \"assets\\/images\\/logo_multitel.png\",\n    \"cambio_password\": false\n}'),
(14, 1, 'ACCESO', 'LOGIN', 'tb_usuario', 1, '::1', '2026-09-20 01:57:04', 'Inicio de sesión exitoso para el usuario \'samu\'', '{\n    \"rol\": \"Administrador\",\n    \"ip\": \"::1\"\n}');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_auditoria_seguridad`
--

CREATE TABLE `tb_auditoria_seguridad` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `evento` varchar(50) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `detalles` text DEFAULT NULL,
  `fecha_hora` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_auditoria_seguridad`
--

INSERT INTO `tb_auditoria_seguridad` (`id`, `id_usuario`, `username`, `evento`, `ip`, `user_agent`, `detalles`, `fecha_hora`) VALUES
(1, 999, 'test_admin', 'SESSION_TIMEOUT', '127.0.0.1', NULL, 'Sesión vencida por inactividad (950 s)', '2026-09-17 16:15:55'),
(2, NULL, 'payroll_admin_1789676155', 'LOCKOUT_IP_USER', '127.0.0.1', NULL, 'Bloqueo de 10 min activado para usuario \'payroll_admin_1789676155\' desde IP 198.51.100.10 tras 5 intentos', '2026-09-17 16:15:55'),
(3, NULL, 'test_multi_tab', 'SESSION_ROTATED', '127.0.0.1', NULL, 'Rotación periódica de session ID multi-pestaña ejecutada', '2026-09-17 16:15:55'),
(4, 999, 'test_admin', 'SESSION_TIMEOUT', '127.0.0.1', NULL, 'Sesión vencida por inactividad (950 s)', '2026-09-17 16:16:10'),
(5, NULL, 'payroll_admin_1789676170', 'LOCKOUT_IP_USER', '127.0.0.1', NULL, 'Bloqueo de 10 min activado para usuario \'payroll_admin_1789676170\' desde IP 198.51.100.10 tras 5 intentos', '2026-09-17 16:16:10'),
(6, NULL, 'test_multi_tab', 'SESSION_ROTATED', '127.0.0.1', NULL, 'Rotación periódica de session ID multi-pestaña ejecutada', '2026-09-17 16:16:11'),
(7, 999, 'test_admin', 'SESSION_TIMEOUT', '127.0.0.1', NULL, 'Sesión vencida por inactividad (950 s)', '2026-09-17 16:16:33'),
(8, NULL, 'payroll_admin_1789676193', 'LOCKOUT_IP_USER', '127.0.0.1', NULL, 'Bloqueo de 10 min activado para usuario \'payroll_admin_1789676193\' desde IP 198.51.100.10 tras 5 intentos', '2026-09-17 16:16:33'),
(9, NULL, 'test_multi_tab', 'SESSION_ROTATED', '127.0.0.1', NULL, 'Rotación periódica de session ID multi-pestaña ejecutada', '2026-09-17 16:16:34'),
(10, NULL, NULL, 'SUSPICIOUS_BEACON_ATTEMPT', '127.0.0.1', 'curl/8.13.0', 'Petición beaconLogout rechazada por Origin o token CSRF no coincidente', '2026-09-17 16:18:25'),
(11, 1, 'samu', 'LOGOUT_BEACON', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión automático por baliza al abandonar ventana', '2026-09-17 16:19:33'),
(12, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 16:19:40'),
(13, 1, 'samu', 'LOGOUT_BEACON', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión automático por baliza al abandonar ventana', '2026-09-17 16:19:49'),
(14, NULL, NULL, 'SUSPICIOUS_BEACON_ATTEMPT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Petición beaconLogout rechazada por Origin o token CSRF no coincidente', '2026-09-17 16:19:50'),
(15, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 16:20:07'),
(16, 1, 'samu', 'LOGOUT_BEACON', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión automático por baliza al abandonar ventana', '2026-09-17 16:21:52'),
(17, NULL, NULL, 'SUSPICIOUS_BEACON_ATTEMPT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Petición beaconLogout rechazada por Origin o token CSRF no coincidente', '2026-09-17 16:21:53'),
(18, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 16:23:36'),
(19, 1, 'samu', 'LOGOUT_BEACON', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión automático por baliza al abandonar ventana', '2026-09-17 16:23:53'),
(20, 999, 'test_admin', 'SESSION_TIMEOUT', '127.0.0.1', NULL, 'Sesión vencida por inactividad (950 s)', '2026-09-17 16:23:56'),
(21, NULL, 'payroll_admin_1789676636', 'LOCKOUT_IP_USER', '127.0.0.1', NULL, 'Bloqueo de 10 min activado para usuario \'payroll_admin_1789676636\' desde IP 198.51.100.10 tras 5 intentos', '2026-09-17 16:23:56'),
(22, NULL, 'test_multi_tab', 'SESSION_ROTATED', '127.0.0.1', NULL, 'Rotación periódica de session ID multi-pestaña ejecutada', '2026-09-17 16:23:57'),
(23, NULL, NULL, 'SUSPICIOUS_BEACON_ATTEMPT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Petición beaconLogout rechazada por Origin o token CSRF no coincidente', '2026-09-17 16:24:01'),
(24, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 16:24:20'),
(25, 1, 'samu', 'LOGOUT_BEACON', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión automático por baliza al abandonar ventana', '2026-09-17 16:24:25'),
(26, NULL, NULL, 'SUSPICIOUS_BEACON_ATTEMPT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Petición beaconLogout rechazada por Origin o token CSRF no coincidente', '2026-09-17 16:26:01'),
(27, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 16:26:07'),
(28, 1, 'samu', 'LOGIN_SUCCESS', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'Inicio de sesión exitoso desde IP 127.0.0.1', '2026-09-17 16:30:21'),
(29, 1, 'samu', 'LOGOUT_BEACON', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'Cierre de sesión automático por baliza al abandonar ventana', '2026-09-17 16:35:42'),
(30, 1, 'samu', 'LOGOUT_BEACON', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión automático por baliza al abandonar ventana', '2026-09-17 16:36:45'),
(31, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 16:37:32'),
(32, 1, 'samu', 'LOGOUT_MANUAL', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión voluntario', '2026-09-17 16:38:11'),
(33, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 16:38:34'),
(34, 1, 'samu', 'LOGOUT_BEACON', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión automático por baliza al abandonar ventana', '2026-09-17 16:39:28'),
(35, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 16:39:39'),
(36, 1, 'samu', 'LOGOUT_BEACON', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión automático por baliza al abandonar ventana', '2026-09-17 16:41:04'),
(37, NULL, NULL, 'SUSPICIOUS_BEACON_ATTEMPT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Petición beaconLogout rechazada por Origin o token CSRF no coincidente', '2026-09-17 16:41:29'),
(38, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 16:41:34'),
(39, 1, 'samu', 'LOGOUT_BEACON', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión automático por baliza al abandonar ventana', '2026-09-17 16:42:27'),
(40, NULL, NULL, 'SUSPICIOUS_BEACON_ATTEMPT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Petición beaconLogout rechazada por Origin o token CSRF no coincidente', '2026-09-17 16:42:31'),
(41, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 16:42:55'),
(42, 1, 'samu', 'LOGOUT_MANUAL', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión voluntario', '2026-09-17 16:43:01'),
(43, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 16:43:07'),
(44, 1, 'samu', 'LOGOUT_MANUAL', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión voluntario', '2026-09-17 16:43:44'),
(45, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 16:43:54'),
(46, 1, 'samu', 'LOGOUT_MANUAL', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión voluntario', '2026-09-17 16:45:07'),
(47, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 16:45:13'),
(48, NULL, 'samu', 'LOGIN_FAILED', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'Credenciales incorrectas para usuario \'samu\' desde IP 127.0.0.1', '2026-09-17 16:46:08'),
(49, NULL, 'samu', 'LOGIN_FAILED', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'Credenciales incorrectas para usuario \'samu\' desde IP 127.0.0.1', '2026-09-17 16:48:47'),
(50, 1, 'samu', 'LOGIN_SUCCESS', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'Inicio de sesión exitoso desde IP 127.0.0.1', '2026-09-17 16:48:51'),
(51, 1, 'samu', 'LOGOUT_BEACON', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'Cierre de sesión automático por baliza al abandonar ventana', '2026-09-17 16:49:15'),
(52, 1, 'samu', 'LOGOUT_BEACON', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión automático por baliza al abandonar ventana', '2026-09-17 16:50:23'),
(53, NULL, NULL, 'SUSPICIOUS_BEACON_ATTEMPT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Petición beaconLogout rechazada por Origin o token CSRF no coincidente', '2026-09-17 16:50:24'),
(54, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 16:50:38'),
(55, 1, 'samu', 'LOGOUT_BEACON', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión automático por baliza al abandonar ventana', '2026-09-17 16:50:42'),
(56, NULL, NULL, 'SUSPICIOUS_BEACON_ATTEMPT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Petición beaconLogout rechazada por Origin o token CSRF no coincidente', '2026-09-17 16:51:44'),
(57, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 16:51:51'),
(58, 1, 'samu', 'LOGIN_SUCCESS', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'Inicio de sesión exitoso desde IP 127.0.0.1', '2026-09-17 16:52:45'),
(59, 1, 'samu', 'LOGOUT_BEACON', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'Cierre de sesión automático por baliza al abandonar ventana', '2026-09-17 16:53:28'),
(60, 1, 'samu', 'LOGOUT_BEACON', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión automático por baliza al abandonar ventana', '2026-09-17 16:55:18'),
(61, NULL, NULL, 'SUSPICIOUS_BEACON_ATTEMPT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Petición beaconLogout rechazada por Origin o token CSRF no coincidente', '2026-09-17 16:57:22'),
(62, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 16:57:27'),
(63, NULL, NULL, 'SUSPICIOUS_BEACON_ATTEMPT', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'Petición beaconLogout rechazada por Origin o token CSRF no coincidente', '2026-09-17 16:57:44'),
(64, NULL, 'samu', 'LOGIN_FAILED', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'Credenciales incorrectas para usuario \'samu\' desde IP 127.0.0.1', '2026-09-17 16:59:42'),
(65, 1, 'samu', 'LOGOUT_BEACON', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión automático por baliza al abandonar ventana', '2026-09-17 17:00:36'),
(66, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 17:00:42'),
(67, 1, 'samu', 'LOGOUT_MANUAL', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión voluntario', '2026-09-17 17:00:47'),
(68, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 17:01:01'),
(69, 999, 'test_admin', 'SESSION_TIMEOUT', '127.0.0.1', NULL, 'Sesión vencida por inactividad (950 s)', '2026-09-17 17:03:19'),
(70, NULL, 'payroll_admin_1789678999', 'LOCKOUT_IP_USER', '127.0.0.1', NULL, 'Bloqueo de 10 min activado para usuario \'payroll_admin_1789678999\' desde IP 198.51.100.10 tras 5 intentos', '2026-09-17 17:03:19'),
(71, NULL, 'test_multi_tab', 'SESSION_ROTATED', '127.0.0.1', NULL, 'Rotación periódica de session ID multi-pestaña ejecutada', '2026-09-17 17:03:19'),
(72, 1, 'samu', 'LOGOUT_BEACON', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Sesión cerrada automáticamente por baliza al abandonar la pestaña', '2026-09-17 17:03:46'),
(73, NULL, NULL, 'SUSPICIOUS_BEACON_ATTEMPT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Petición beaconLogout rechazada por Origin o token CSRF no coincidente', '2026-09-17 17:03:46'),
(74, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 17:03:52'),
(75, 1, 'samu', 'LOGOUT_MANUAL', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión voluntario', '2026-09-17 17:04:05'),
(76, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 17:07:46'),
(77, 1, 'samu', 'LOGOUT_MANUAL', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión voluntario', '2026-09-17 17:07:50'),
(78, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 17:07:55'),
(79, 999, 'test_admin', 'SESSION_TIMEOUT', '127.0.0.1', NULL, 'Sesión vencida por inactividad (950 s)', '2026-09-17 17:14:45'),
(80, NULL, 'payroll_admin_1789679685', 'LOCKOUT_IP_USER', '127.0.0.1', NULL, 'Bloqueo de 10 min activado para usuario \'payroll_admin_1789679685\' desde IP 198.51.100.10 tras 5 intentos', '2026-09-17 17:14:45'),
(81, NULL, 'test_multi_tab', 'SESSION_ROTATED', '127.0.0.1', NULL, 'Rotación periódica de session ID multi-pestaña ejecutada', '2026-09-17 17:14:45'),
(82, 999, 'test_admin', 'SESSION_TIMEOUT', '127.0.0.1', NULL, 'Sesión vencida por inactividad (950 s)', '2026-09-17 17:18:23'),
(83, NULL, 'payroll_admin_1789679903', 'LOCKOUT_IP_USER', '127.0.0.1', NULL, 'Bloqueo de 10 min activado para usuario \'payroll_admin_1789679903\' desde IP 198.51.100.10 tras 5 intentos', '2026-09-17 17:18:23'),
(84, NULL, 'test_multi_tab', 'SESSION_ROTATED', '127.0.0.1', NULL, 'Rotación periódica de session ID multi-pestaña ejecutada', '2026-09-17 17:18:23'),
(85, 999, 'test_admin', 'SESSION_TIMEOUT', '127.0.0.1', NULL, 'Sesión vencida por inactividad (950 s)', '2026-09-17 17:21:27'),
(86, NULL, 'payroll_admin_1789680087', 'LOCKOUT_IP_USER', '127.0.0.1', NULL, 'Bloqueo de 10 min activado para usuario \'payroll_admin_1789680087\' desde IP 198.51.100.10 tras 5 intentos', '2026-09-17 17:21:27'),
(87, NULL, 'test_multi_tab', 'SESSION_ROTATED', '127.0.0.1', NULL, 'Rotación periódica de session ID multi-pestaña ejecutada', '2026-09-17 17:21:28'),
(88, 999, 'test_admin', 'SESSION_TIMEOUT', '127.0.0.1', NULL, 'Sesión vencida por inactividad (950 s)', '2026-09-17 17:23:20'),
(89, NULL, 'payroll_admin_1789680200', 'LOCKOUT_IP_USER', '127.0.0.1', NULL, 'Bloqueo de 10 min activado para usuario \'payroll_admin_1789680200\' desde IP 198.51.100.10 tras 5 intentos', '2026-09-17 17:23:20'),
(90, NULL, 'test_multi_tab', 'SESSION_ROTATED', '127.0.0.1', NULL, 'Rotación periódica de session ID multi-pestaña ejecutada', '2026-09-17 17:23:20'),
(91, 999, 'test_admin', 'SESSION_TIMEOUT', '127.0.0.1', NULL, 'Sesión vencida por inactividad (950 s)', '2026-09-17 17:27:52'),
(92, NULL, 'payroll_admin_1789680472', 'LOCKOUT_IP_USER', '127.0.0.1', NULL, 'Bloqueo de 10 min activado para usuario \'payroll_admin_1789680472\' desde IP 198.51.100.10 tras 5 intentos', '2026-09-17 17:27:52'),
(93, NULL, 'test_multi_tab', 'SESSION_ROTATED', '127.0.0.1', NULL, 'Rotación periódica de session ID multi-pestaña ejecutada', '2026-09-17 17:27:53'),
(94, 1, 'samu', 'LOGOUT_BEACON', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Sesión cerrada automáticamente por baliza al abandonar la pestaña', '2026-09-17 17:28:47'),
(95, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 17:28:54'),
(96, 999, 'test_admin', 'SESSION_TIMEOUT', '127.0.0.1', NULL, 'Sesión vencida por inactividad (950 s)', '2026-09-17 17:31:43'),
(97, NULL, 'payroll_admin_1789680703', 'LOCKOUT_IP_USER', '127.0.0.1', NULL, 'Bloqueo de 10 min activado para usuario \'payroll_admin_1789680703\' desde IP 198.51.100.10 tras 5 intentos', '2026-09-17 17:31:43'),
(98, NULL, 'test_multi_tab', 'SESSION_ROTATED', '127.0.0.1', NULL, 'Rotación periódica de session ID multi-pestaña ejecutada', '2026-09-17 17:31:44'),
(99, 999, 'test_admin', 'SESSION_TIMEOUT', '127.0.0.1', NULL, 'Sesión vencida por inactividad (950 s)', '2026-09-17 17:34:57'),
(100, NULL, 'payroll_admin_1789680897', 'LOCKOUT_IP_USER', '127.0.0.1', NULL, 'Bloqueo de 10 min activado para usuario \'payroll_admin_1789680897\' desde IP 198.51.100.10 tras 5 intentos', '2026-09-17 17:34:57'),
(101, NULL, 'test_multi_tab', 'SESSION_ROTATED', '127.0.0.1', NULL, 'Rotación periódica de session ID multi-pestaña ejecutada', '2026-09-17 17:34:58'),
(102, 1, 'samu', 'LOGOUT_MANUAL', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión voluntario', '2026-09-17 17:39:36'),
(103, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 17:39:42'),
(104, 1, 'samu', 'SESSION_TIMEOUT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Sesión vencida por inactividad (970 s)', '2026-09-17 18:12:33'),
(105, NULL, NULL, 'SUSPICIOUS_BEACON_ATTEMPT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Petición beaconLogout rechazada por Origin o token CSRF no coincidente', '2026-09-17 18:12:33'),
(106, NULL, 'samu', 'LOGIN_FAILED', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Credenciales incorrectas para usuario \'samu\' desde IP ::1', '2026-09-17 19:58:32'),
(107, NULL, 'samu', 'LOGIN_FAILED', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Credenciales incorrectas para usuario \'samu\' desde IP ::1', '2026-09-17 19:58:34'),
(108, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 19:58:38'),
(109, 999, 'test_admin', 'SESSION_TIMEOUT', '127.0.0.1', NULL, 'Sesión vencida por inactividad (950 s)', '2026-09-17 20:02:13'),
(110, NULL, 'payroll_admin_1789689733', 'LOCKOUT_IP_USER', '127.0.0.1', NULL, 'Bloqueo de 10 min activado para usuario \'payroll_admin_1789689733\' desde IP 198.51.100.10 tras 5 intentos', '2026-09-17 20:02:13'),
(111, NULL, 'test_multi_tab', 'SESSION_ROTATED', '127.0.0.1', NULL, 'Rotación periódica de session ID multi-pestaña ejecutada', '2026-09-17 20:02:14'),
(112, 1, 'samu', 'SESSION_TIMEOUT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Sesión vencida por inactividad (907 s)', '2026-09-17 20:16:15'),
(113, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 20:17:22'),
(114, 1, 'samu', 'LOGOUT_MANUAL', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión voluntario', '2026-09-17 20:46:01'),
(115, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-17 20:46:19'),
(116, 1, 'samu', 'SESSION_TIMEOUT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Sesión vencida por inactividad (164541 s)', '2026-09-19 18:37:42'),
(117, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-19 18:37:49'),
(118, 1, 'samu', 'SESSION_ROTATED', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Rotación periódica de session ID multi-pestaña ejecutada', '2026-09-19 19:08:07'),
(119, 1, 'samu', 'LOGOUT_BEACON', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Sesión cerrada automáticamente por baliza al abandonar la pestaña', '2026-09-19 19:10:00'),
(120, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-19 19:10:06'),
(121, 1, 'samu', 'SESSION_TIMEOUT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Sesión vencida por inactividad (1100 s)', '2026-09-19 19:29:10'),
(122, NULL, NULL, 'SUSPICIOUS_BEACON_ATTEMPT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Petición beaconLogout rechazada por Origin o token CSRF no coincidente', '2026-09-19 19:29:10'),
(123, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-19 21:04:53'),
(124, 1, 'samu', 'SESSION_TIMEOUT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Sesión vencida por inactividad (954 s)', '2026-09-19 21:30:10'),
(125, NULL, NULL, 'SUSPICIOUS_BEACON_ATTEMPT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Petición beaconLogout rechazada por Origin o token CSRF no coincidente', '2026-09-19 21:30:10'),
(126, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-19 21:45:02'),
(127, 1, 'samu', 'LOGOUT_MANUAL', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión voluntario', '2026-09-19 21:46:17'),
(128, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-19 21:47:24'),
(129, 1, 'samu', 'LOGOUT_MANUAL', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión voluntario', '2026-09-19 21:49:15'),
(130, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-19 21:59:50'),
(131, 1, 'samu', 'LOGIN_SUCCESS', '192.168.100.25', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Mobile Safari/537.36', 'Inicio de sesión exitoso desde IP 192.168.100.25', '2026-09-19 22:15:33'),
(132, 1, 'samu', 'LOGIN_SUCCESS', '192.168.100.97', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Mobile Safari/537.36', 'Inicio de sesión exitoso desde IP 192.168.100.97', '2026-09-19 22:36:15'),
(133, 1, 'samu', 'LOGOUT_MANUAL', '192.168.100.97', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Mobile Safari/537.36', 'Cierre de sesión voluntario', '2026-09-19 22:37:11'),
(134, 1, 'samu', 'LOGOUT_MANUAL', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión voluntario', '2026-09-19 22:53:43'),
(135, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-19 22:53:50'),
(136, 1, 'samu', 'SESSION_ROTATED', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Rotación periódica de session ID multi-pestaña ejecutada', '2026-09-19 23:24:29'),
(137, 1, 'samu', 'SESSION_ROTATED', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Rotación periódica de session ID multi-pestaña ejecutada', '2026-09-19 23:54:46'),
(138, 1, 'samu', 'LOGOUT_MANUAL', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Cierre de sesión voluntario', '2026-09-20 00:17:25'),
(139, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Inicio de sesión exitoso desde IP ::1', '2026-09-20 00:47:26'),
(140, 1, 'samu', 'SESSION_ROTATED', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Rotación periódica de session ID multi-pestaña ejecutada', '2026-09-20 01:18:14'),
(141, 1, 'samu', 'SESSION_ROTATED', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', 'Rotación periódica de session ID multi-pestaña ejecutada', '2026-09-20 01:48:15'),
(142, 1, 'samu', 'LOGIN_SUCCESS', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'Inicio de sesión exitoso desde IP ::1', '2026-09-20 01:57:04'),
(143, NULL, NULL, 'SUSPICIOUS_BEACON_ATTEMPT', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'Petición beaconLogout rechazada por Origin o token CSRF no coincidente', '2026-09-20 01:57:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_banco`
--

CREATE TABLE `tb_banco` (
  `id_banco` int(11) NOT NULL,
  `nombre_banco` varchar(100) NOT NULL,
  `codigo_banco` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_banco`
--

INSERT INTO `tb_banco` (`id_banco`, `nombre_banco`, `codigo_banco`) VALUES
(1, 'Banco de Venezuela', '0102'),
(2, 'Banesco', '0134'),
(3, 'Mercantil', '0105'),
(4, 'Provincial', '0108');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_cargo`
--

CREATE TABLE `tb_cargo` (
  `id_cargo` int(11) NOT NULL,
  `nombre_cargo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_cargo`
--

INSERT INTO `tb_cargo` (`id_cargo`, `nombre_cargo`) VALUES
(1, 'ACT'),
(2, 'Supervisor'),
(3, 'Monitoreo'),
(4, 'Técnico'),
(5, 'Administración'),
(6, 'Depósito'),
(7, 'Logística'),
(8, 'Directivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_concepto_asignacion`
--

CREATE TABLE `tb_concepto_asignacion` (
  `id_concepto` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_concepto_asignacion`
--

INSERT INTO `tb_concepto_asignacion` (`id_concepto`, `nombre`, `descripcion`) VALUES
(1, 'Salario', 'Sueldo base quincenal/semanal'),
(2, 'Bono Nocturno', 'Recargo por horas nocturnas'),
(3, 'Horas Extra', 'Horas adicionales trabajadas'),
(4, 'Días Feriados', 'Pago de días feriados trabajados');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_concepto_deduccion`
--

CREATE TABLE `tb_concepto_deduccion` (
  `id_concepto` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `tipo_calculo` varchar(20) NOT NULL,
  `valor_default` decimal(12,2) DEFAULT NULL,
  `descripcion` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_concepto_deduccion`
--

INSERT INTO `tb_concepto_deduccion` (`id_concepto`, `nombre`, `tipo_calculo`, `valor_default`, `descripcion`) VALUES
(1, 'S.S.O', 'Porcentaje', 0.04, 'Seguro Social Obligatorio'),
(2, 'R.P.E', 'Porcentaje', 0.01, 'Régimen Prestacional de Empleo'),
(3, 'L.P.H', 'Porcentaje', 0.01, 'Ley de Política Habitacional'),
(4, 'Préstamo', 'Monto Fijo', 0.00, 'Descuento por préstamo concedido'),
(5, 'Inasistencia', 'Monto Fijo', 0.00, 'Descuento por días no trabajados sin justificar'),
(6, 'ISLR', 'Monto Fijo', 0.00, 'Impuesto sobre la Renta retenido');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_configuracion_empresa`
--

CREATE TABLE `tb_configuracion_empresa` (
  `id_config` int(11) NOT NULL,
  `nombre_empresa` varchar(150) NOT NULL DEFAULT 'EMPRESA DEMO C.A.',
  `rif` varchar(20) NOT NULL DEFAULT 'J-12345678-9',
  `regimen_ivss` enum('Clase_I','Clase_II','Clase_III') NOT NULL DEFAULT 'Clase_II',
  `tasa_sso_trabajador` decimal(5,4) NOT NULL DEFAULT 0.0400,
  `tasa_sso_patronal` decimal(5,4) NOT NULL DEFAULT 0.1000,
  `tasa_rpe_trabajador` decimal(5,4) NOT NULL DEFAULT 0.0050,
  `tasa_rpe_patronal` decimal(5,4) NOT NULL DEFAULT 0.0210,
  `tasa_faov_trabajador` decimal(5,4) NOT NULL DEFAULT 0.0100,
  `tasa_faov_patronal` decimal(5,4) NOT NULL DEFAULT 0.0200,
  `salario_minimo_legal` decimal(12,2) NOT NULL DEFAULT 130.00,
  `tope_sso_salarios` int(11) NOT NULL DEFAULT 5,
  `tope_rpe_salarios` int(11) NOT NULL DEFAULT 5,
  `semanas_factor_anual` int(11) NOT NULL DEFAULT 52,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_configuracion_empresa`
--

INSERT INTO `tb_configuracion_empresa` (`id_config`, `nombre_empresa`, `rif`, `regimen_ivss`, `tasa_sso_trabajador`, `tasa_sso_patronal`, `tasa_rpe_trabajador`, `tasa_rpe_patronal`, `tasa_faov_trabajador`, `tasa_faov_patronal`, `salario_minimo_legal`, `tope_sso_salarios`, `tope_rpe_salarios`, `semanas_factor_anual`, `updated_at`) VALUES
(1, 'MULTITEL C.A.', 'J-40982716-0', 'Clase_II', 0.0400, 0.1000, 0.0050, 0.0210, 0.0100, 0.0200, 130.00, 5, 5, 52, '2026-09-20 01:06:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_cuenta_bancaria`
--

CREATE TABLE `tb_cuenta_bancaria` (
  `id_cuenta` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `id_banco` int(11) NOT NULL,
  `numero_cuenta` varchar(30) NOT NULL,
  `tipo_cuenta` varchar(20) DEFAULT 'Ahorro',
  `es_principal` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_empleado`
--

CREATE TABLE `tb_empleado` (
  `id_empleado` int(11) NOT NULL,
  `cedula` varchar(15) NOT NULL,
  `primer_nombre` varchar(50) NOT NULL,
  `segundo_nombre` varchar(50) DEFAULT NULL,
  `primer_apellido` varchar(50) NOT NULL,
  `segundo_apellido` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `id_tipo_empleado` int(11) NOT NULL,
  `id_jornada` int(11) NOT NULL,
  `sueldo_divisa` decimal(12,2) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `foto` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_empleado`
--

INSERT INTO `tb_empleado` (`id_empleado`, `cedula`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `email`, `id_tipo_empleado`, `id_jornada`, `sueldo_divisa`, `fecha_ingreso`, `estado`, `foto`) VALUES
(1, '30635942', 'samu', 'jose', 'Rivas', 'rengel', NULL, 1, 1, 500.00, '2026-08-11', 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_empleado_cargo`
--

CREATE TABLE `tb_empleado_cargo` (
  `id_empleado_cargo` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `id_cargo` int(11) NOT NULL,
  `fecha_asignacion` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `es_actual` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_empleado_cargo`
--

INSERT INTO `tb_empleado_cargo` (`id_empleado_cargo`, `id_empleado`, `id_cargo`, `fecha_asignacion`, `fecha_fin`, `es_actual`) VALUES
(1, 1, 5, '2026-08-12', NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_jornada`
--

CREATE TABLE `tb_jornada` (
  `id_jornada` int(11) NOT NULL,
  `nombre_jornada` varchar(30) NOT NULL,
  `horas_por_dia` decimal(4,2) NOT NULL,
  `dias_por_semana` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_jornada`
--

INSERT INTO `tb_jornada` (`id_jornada`, `nombre_jornada`, `horas_por_dia`, `dias_por_semana`) VALUES
(1, 'Tiempo Completo', 8.00, 5),
(2, 'Medio Tiempo', 4.00, 5),
(3, 'Tiempo Completo - 6 días', 8.00, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_login_intentos`
--

CREATE TABLE `tb_login_intentos` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `intentos` int(11) NOT NULL DEFAULT 1,
  `primer_intento` datetime NOT NULL,
  `ultimo_intento` datetime NOT NULL,
  `bloqueado_hasta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_login_intentos`
--

INSERT INTO `tb_login_intentos` (`id`, `ip_address`, `username`, `intentos`, `primer_intento`, `ultimo_intento`, `bloqueado_hasta`) VALUES
(6, '127.0.0.1', 'samu', 1, '2026-09-17 16:59:42', '2026-09-17 16:59:42', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_nomina`
--

CREATE TABLE `tb_nomina` (
  `id_nomina` int(11) NOT NULL,
  `id_periodo` int(11) NOT NULL,
  `id_usuario_genera` int(11) NOT NULL,
  `fecha_generacion` datetime DEFAULT current_timestamp(),
  `salario_minimo_vigente` decimal(12,2) NOT NULL,
  `semanas_descontar_ivss` int(11) DEFAULT 0,
  `estado` varchar(20) DEFAULT 'Borrador',
  `factor_semanas_mes` decimal(6,4) DEFAULT 4.3333,
  `tasa_sso` decimal(5,4) DEFAULT 0.0400,
  `tasa_rpe` decimal(5,4) DEFAULT 0.0050,
  `tasa_lph` decimal(5,4) DEFAULT 0.0100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_nomina`
--

INSERT INTO `tb_nomina` (`id_nomina`, `id_periodo`, `id_usuario_genera`, `fecha_generacion`, `salario_minimo_vigente`, `semanas_descontar_ivss`, `estado`, `factor_semanas_mes`, `tasa_sso`, `tasa_rpe`, `tasa_lph`) VALUES
(1, 1, 1, '2026-08-12 20:03:41', 1000.00, 2, 'Generada', 4.3333, 0.0400, 0.0050, 0.0100);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_nomina_asignacion`
--

CREATE TABLE `tb_nomina_asignacion` (
  `id_asignacion` int(11) NOT NULL,
  `id_detalle` int(11) NOT NULL,
  `id_concepto` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `monto` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_nomina_asignacion`
--

INSERT INTO `tb_nomina_asignacion` (`id_asignacion`, `id_detalle`, `id_concepto`, `cantidad`, `monto`) VALUES
(9, 1, 1, 15.00, 250.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_nomina_deduccion`
--

CREATE TABLE `tb_nomina_deduccion` (
  `id_deduccion` int(11) NOT NULL,
  `id_detalle` int(11) NOT NULL,
  `id_concepto` int(11) NOT NULL,
  `porcentaje_aplicado` decimal(5,2) DEFAULT NULL,
  `monto` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_nomina_deduccion`
--

INSERT INTO `tb_nomina_deduccion` (`id_deduccion`, `id_detalle`, `id_concepto`, `porcentaje_aplicado`, `monto`) VALUES
(30, 1, 1, 0.04, 9.23),
(31, 1, 2, 0.01, 1.15),
(32, 1, 3, 0.01, 2.50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_nomina_detalle`
--

CREATE TABLE `tb_nomina_detalle` (
  `id_detalle` int(11) NOT NULL,
  `id_nomina` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `id_cargo` int(11) NOT NULL,
  `id_tasa` int(11) NOT NULL,
  `sueldo_diario` decimal(10,2) NOT NULL,
  `dias_trabajados` int(11) NOT NULL,
  `sueldo_bss` decimal(12,2) NOT NULL,
  `total_asignaciones` decimal(12,2) DEFAULT 0.00,
  `total_deducciones` decimal(12,2) DEFAULT 0.00,
  `neto_pagar` decimal(12,2) DEFAULT 0.00,
  `dias_inasistencia` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_nomina_detalle`
--

INSERT INTO `tb_nomina_detalle` (`id_detalle`, `id_nomina`, `id_empleado`, `id_cargo`, `id_tasa`, `sueldo_diario`, `dias_trabajados`, `sueldo_bss`, `total_asignaciones`, `total_deducciones`, `neto_pagar`, `dias_inasistencia`) VALUES
(1, 1, 1, 5, 3, 16.67, 15, 250.00, 250.00, 12.88, 237.12, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_periodo_nomina`
--

CREATE TABLE `tb_periodo_nomina` (
  `id_periodo` int(11) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `tipo_periodo` varchar(20) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `anio` int(11) NOT NULL,
  `mes` int(11) NOT NULL,
  `numero_quincena` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_periodo_nomina`
--

INSERT INTO `tb_periodo_nomina` (`id_periodo`, `descripcion`, `tipo_periodo`, `fecha_inicio`, `fecha_fin`, `anio`, `mes`, `numero_quincena`) VALUES
(1, '1ra Quincena Agosto 2026', 'Quincenal', '2026-08-12', '2026-08-13', 2026, 8, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_permiso_empleado`
--

CREATE TABLE `tb_permiso_empleado` (
  `id_permiso` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `dias_solicitados` decimal(5,1) NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `id_usuario_aprueba` int(11) DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'Pendiente',
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_rol`
--

CREATE TABLE `tb_rol` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(30) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_rol`
--

INSERT INTO `tb_rol` (`id_rol`, `nombre_rol`, `descripcion`) VALUES
(1, 'Administrador', 'Acceso total al sistema'),
(2, 'Personal de Apoyo', 'Consultas y reportes'),
(3, 'Operador', 'Gestión operativa: empleados, nóminas, permisos. Sin acceso a gestión de usuarios.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_tasa_cambio`
--

CREATE TABLE `tb_tasa_cambio` (
  `id_tasa` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `valor_tasa` decimal(12,4) NOT NULL,
  `fuente` varchar(50) DEFAULT 'BCV'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_tasa_cambio`
--

INSERT INTO `tb_tasa_cambio` (`id_tasa`, `fecha`, `valor_tasa`, `fuente`) VALUES
(1, '2026-08-13', 500.0000, 'BCV'),
(2, '2026-09-16', 846.5131, 'BCV Oficial (DolarApi)'),
(3, '2026-09-18', 848.5458, 'BCV Oficial (DolarApi)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_tipo_empleado`
--

CREATE TABLE `tb_tipo_empleado` (
  `id_tipo_empleado` int(11) NOT NULL,
  `nombre_tipo` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_tipo_empleado`
--

INSERT INTO `tb_tipo_empleado` (`id_tipo_empleado`, `nombre_tipo`) VALUES
(1, 'Fijo'),
(2, 'Contratado'),
(3, 'Pasante');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_usuario`
--

CREATE TABLE `tb_usuario` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `preferencias` text DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_usuario`
--

INSERT INTO `tb_usuario` (`id_usuario`, `id_rol`, `id_empleado`, `nombre_usuario`, `correo`, `foto_perfil`, `password_hash`, `preferencias`, `estado`, `fecha_creacion`) VALUES
(1, 1, 1, 'samu', 'samueljosealexander@gmail.com', 'assets/images/logo_multitel.png', '$2y$12$amtgduIUNABRSkknwOyHbuAOcfT6XNXCVUnlO908I1Srqu1u5b6k6', '{\"header_color\":\"#0b1d3a\",\"primary_color\":\"#0284c7\",\"nav_layout\":\"sidebar\",\"updated_at\":\"2026-09-20 07:59:07\"}', 1, '2026-08-10 11:43:56'),
(2, 1, NULL, 'mobile', 'samueljosealexanderr@gmail.com', 'assets/images/logo_multitel.png', '$2y$12$Awv8cJ6/8Y7x5R3qTkRv.uD4/7GgE9.49CJLWpdLzoXZWHk8DiO0u', NULL, 1, '2026-09-19 22:54:49');

--
-- Disparadores `tb_usuario`
--
DELIMITER $$
CREATE TRIGGER `trg_proteger_usuarios_sistema_delete` BEFORE DELETE ON `tb_usuario` FOR EACH ROW BEGIN
        IF OLD.id_usuario IN (1, 2) OR LOWER(OLD.nombre_usuario) IN ('samu', 'mobile') THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ACCESO DENEGADO: Los usuarios principales (samu y mobile) estan blindados contra eliminacion a nivel de base de datos.';
        END IF;
    END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_proteger_usuarios_sistema_update` BEFORE UPDATE ON `tb_usuario` FOR EACH ROW BEGIN
        IF OLD.id_usuario IN (1, 2) OR LOWER(OLD.nombre_usuario) IN ('samu', 'mobile') THEN
            IF NEW.estado = 0 THEN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'ACCESO DENEGADO: Los usuarios principales (samu y mobile) no pueden ser desactivados en la base de datos.';
            END IF;
        END IF;
    END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vw_saldo_permisos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vw_saldo_permisos` (
`id_empleado` int(11)
,`nombre_completo` varchar(101)
,`dias_acumulados_politica` bigint(21)
,`dias_usados` decimal(27,1)
,`saldo_disponible` decimal(28,1)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vw_saldo_permisos`
--
DROP TABLE IF EXISTS `vw_saldo_permisos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_saldo_permisos`  AS SELECT `e`.`id_empleado` AS `id_empleado`, concat(`e`.`primer_nombre`,' ',`e`.`primer_apellido`) AS `nombre_completo`, timestampdiff(MONTH,`e`.`fecha_ingreso`,curdate()) AS `dias_acumulados_politica`, ifnull(sum(case when `p`.`estado` = 'Aprobado' then `p`.`dias_solicitados` else 0 end),0) AS `dias_usados`, timestampdiff(MONTH,`e`.`fecha_ingreso`,curdate()) - ifnull(sum(case when `p`.`estado` = 'Aprobado' then `p`.`dias_solicitados` else 0 end),0) AS `saldo_disponible` FROM (`tb_empleado` `e` left join `tb_permiso_empleado` `p` on(`e`.`id_empleado` = `p`.`id_empleado`)) GROUP BY `e`.`id_empleado` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tb_auditoria`
--
ALTER TABLE `tb_auditoria`
  ADD PRIMARY KEY (`id_auditoria`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `tb_auditoria_seguridad`
--
ALTER TABLE `tb_auditoria_seguridad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_evento` (`evento`),
  ADD KEY `idx_usuario` (`id_usuario`),
  ADD KEY `idx_fecha` (`fecha_hora`);

--
-- Indices de la tabla `tb_banco`
--
ALTER TABLE `tb_banco`
  ADD PRIMARY KEY (`id_banco`);

--
-- Indices de la tabla `tb_cargo`
--
ALTER TABLE `tb_cargo`
  ADD PRIMARY KEY (`id_cargo`);

--
-- Indices de la tabla `tb_concepto_asignacion`
--
ALTER TABLE `tb_concepto_asignacion`
  ADD PRIMARY KEY (`id_concepto`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `tb_concepto_deduccion`
--
ALTER TABLE `tb_concepto_deduccion`
  ADD PRIMARY KEY (`id_concepto`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `tb_configuracion_empresa`
--
ALTER TABLE `tb_configuracion_empresa`
  ADD PRIMARY KEY (`id_config`);

--
-- Indices de la tabla `tb_cuenta_bancaria`
--
ALTER TABLE `tb_cuenta_bancaria`
  ADD PRIMARY KEY (`id_cuenta`),
  ADD KEY `id_empleado` (`id_empleado`),
  ADD KEY `id_banco` (`id_banco`);

--
-- Indices de la tabla `tb_empleado`
--
ALTER TABLE `tb_empleado`
  ADD PRIMARY KEY (`id_empleado`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD KEY `id_tipo_empleado` (`id_tipo_empleado`),
  ADD KEY `id_jornada` (`id_jornada`);

--
-- Indices de la tabla `tb_empleado_cargo`
--
ALTER TABLE `tb_empleado_cargo`
  ADD PRIMARY KEY (`id_empleado_cargo`),
  ADD UNIQUE KEY `uk_empleado_cargo_actual` (`id_empleado`,`es_actual`),
  ADD KEY `id_cargo` (`id_cargo`);

--
-- Indices de la tabla `tb_jornada`
--
ALTER TABLE `tb_jornada`
  ADD PRIMARY KEY (`id_jornada`);

--
-- Indices de la tabla `tb_login_intentos`
--
ALTER TABLE `tb_login_intentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ip_user` (`ip_address`,`username`),
  ADD KEY `idx_ip` (`ip_address`),
  ADD KEY `idx_bloqueo` (`bloqueado_hasta`);

--
-- Indices de la tabla `tb_nomina`
--
ALTER TABLE `tb_nomina`
  ADD PRIMARY KEY (`id_nomina`),
  ADD KEY `id_periodo` (`id_periodo`),
  ADD KEY `id_usuario_genera` (`id_usuario_genera`);

--
-- Indices de la tabla `tb_nomina_asignacion`
--
ALTER TABLE `tb_nomina_asignacion`
  ADD PRIMARY KEY (`id_asignacion`),
  ADD KEY `id_detalle` (`id_detalle`),
  ADD KEY `id_concepto` (`id_concepto`);

--
-- Indices de la tabla `tb_nomina_deduccion`
--
ALTER TABLE `tb_nomina_deduccion`
  ADD PRIMARY KEY (`id_deduccion`),
  ADD KEY `id_detalle` (`id_detalle`),
  ADD KEY `id_concepto` (`id_concepto`);

--
-- Indices de la tabla `tb_nomina_detalle`
--
ALTER TABLE `tb_nomina_detalle`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_nomina` (`id_nomina`),
  ADD KEY `id_empleado` (`id_empleado`),
  ADD KEY `id_cargo` (`id_cargo`),
  ADD KEY `id_tasa` (`id_tasa`);

--
-- Indices de la tabla `tb_periodo_nomina`
--
ALTER TABLE `tb_periodo_nomina`
  ADD PRIMARY KEY (`id_periodo`),
  ADD UNIQUE KEY `uk_periodo` (`anio`,`mes`,`numero_quincena`);

--
-- Indices de la tabla `tb_permiso_empleado`
--
ALTER TABLE `tb_permiso_empleado`
  ADD PRIMARY KEY (`id_permiso`),
  ADD KEY `id_empleado` (`id_empleado`),
  ADD KEY `id_usuario_aprueba` (`id_usuario_aprueba`);

--
-- Indices de la tabla `tb_rol`
--
ALTER TABLE `tb_rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `tb_tasa_cambio`
--
ALTER TABLE `tb_tasa_cambio`
  ADD PRIMARY KEY (`id_tasa`);

--
-- Indices de la tabla `tb_tipo_empleado`
--
ALTER TABLE `tb_tipo_empleado`
  ADD PRIMARY KEY (`id_tipo_empleado`);

--
-- Indices de la tabla `tb_usuario`
--
ALTER TABLE `tb_usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `id_rol` (`id_rol`),
  ADD KEY `id_empleado` (`id_empleado`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tb_auditoria`
--
ALTER TABLE `tb_auditoria`
  MODIFY `id_auditoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `tb_auditoria_seguridad`
--
ALTER TABLE `tb_auditoria_seguridad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT de la tabla `tb_banco`
--
ALTER TABLE `tb_banco`
  MODIFY `id_banco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tb_cargo`
--
ALTER TABLE `tb_cargo`
  MODIFY `id_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tb_concepto_asignacion`
--
ALTER TABLE `tb_concepto_asignacion`
  MODIFY `id_concepto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tb_concepto_deduccion`
--
ALTER TABLE `tb_concepto_deduccion`
  MODIFY `id_concepto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tb_configuracion_empresa`
--
ALTER TABLE `tb_configuracion_empresa`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tb_cuenta_bancaria`
--
ALTER TABLE `tb_cuenta_bancaria`
  MODIFY `id_cuenta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_empleado`
--
ALTER TABLE `tb_empleado`
  MODIFY `id_empleado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tb_empleado_cargo`
--
ALTER TABLE `tb_empleado_cargo`
  MODIFY `id_empleado_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tb_jornada`
--
ALTER TABLE `tb_jornada`
  MODIFY `id_jornada` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tb_login_intentos`
--
ALTER TABLE `tb_login_intentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `tb_nomina`
--
ALTER TABLE `tb_nomina`
  MODIFY `id_nomina` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tb_nomina_asignacion`
--
ALTER TABLE `tb_nomina_asignacion`
  MODIFY `id_asignacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `tb_nomina_deduccion`
--
ALTER TABLE `tb_nomina_deduccion`
  MODIFY `id_deduccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `tb_nomina_detalle`
--
ALTER TABLE `tb_nomina_detalle`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tb_periodo_nomina`
--
ALTER TABLE `tb_periodo_nomina`
  MODIFY `id_periodo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tb_permiso_empleado`
--
ALTER TABLE `tb_permiso_empleado`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_rol`
--
ALTER TABLE `tb_rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tb_tasa_cambio`
--
ALTER TABLE `tb_tasa_cambio`
  MODIFY `id_tasa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tb_tipo_empleado`
--
ALTER TABLE `tb_tipo_empleado`
  MODIFY `id_tipo_empleado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tb_usuario`
--
ALTER TABLE `tb_usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tb_auditoria`
--
ALTER TABLE `tb_auditoria`
  ADD CONSTRAINT `tb_auditoria_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuario` (`id_usuario`);

--
-- Filtros para la tabla `tb_cuenta_bancaria`
--
ALTER TABLE `tb_cuenta_bancaria`
  ADD CONSTRAINT `tb_cuenta_bancaria_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `tb_empleado` (`id_empleado`),
  ADD CONSTRAINT `tb_cuenta_bancaria_ibfk_2` FOREIGN KEY (`id_banco`) REFERENCES `tb_banco` (`id_banco`);

--
-- Filtros para la tabla `tb_empleado`
--
ALTER TABLE `tb_empleado`
  ADD CONSTRAINT `tb_empleado_ibfk_1` FOREIGN KEY (`id_tipo_empleado`) REFERENCES `tb_tipo_empleado` (`id_tipo_empleado`),
  ADD CONSTRAINT `tb_empleado_ibfk_2` FOREIGN KEY (`id_jornada`) REFERENCES `tb_jornada` (`id_jornada`);

--
-- Filtros para la tabla `tb_empleado_cargo`
--
ALTER TABLE `tb_empleado_cargo`
  ADD CONSTRAINT `tb_empleado_cargo_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `tb_empleado` (`id_empleado`),
  ADD CONSTRAINT `tb_empleado_cargo_ibfk_2` FOREIGN KEY (`id_cargo`) REFERENCES `tb_cargo` (`id_cargo`);

--
-- Filtros para la tabla `tb_nomina`
--
ALTER TABLE `tb_nomina`
  ADD CONSTRAINT `tb_nomina_ibfk_1` FOREIGN KEY (`id_periodo`) REFERENCES `tb_periodo_nomina` (`id_periodo`),
  ADD CONSTRAINT `tb_nomina_ibfk_2` FOREIGN KEY (`id_usuario_genera`) REFERENCES `tb_usuario` (`id_usuario`);

--
-- Filtros para la tabla `tb_nomina_asignacion`
--
ALTER TABLE `tb_nomina_asignacion`
  ADD CONSTRAINT `tb_nomina_asignacion_ibfk_1` FOREIGN KEY (`id_detalle`) REFERENCES `tb_nomina_detalle` (`id_detalle`),
  ADD CONSTRAINT `tb_nomina_asignacion_ibfk_2` FOREIGN KEY (`id_concepto`) REFERENCES `tb_concepto_asignacion` (`id_concepto`);

--
-- Filtros para la tabla `tb_nomina_deduccion`
--
ALTER TABLE `tb_nomina_deduccion`
  ADD CONSTRAINT `tb_nomina_deduccion_ibfk_1` FOREIGN KEY (`id_detalle`) REFERENCES `tb_nomina_detalle` (`id_detalle`),
  ADD CONSTRAINT `tb_nomina_deduccion_ibfk_2` FOREIGN KEY (`id_concepto`) REFERENCES `tb_concepto_deduccion` (`id_concepto`);

--
-- Filtros para la tabla `tb_nomina_detalle`
--
ALTER TABLE `tb_nomina_detalle`
  ADD CONSTRAINT `tb_nomina_detalle_ibfk_1` FOREIGN KEY (`id_nomina`) REFERENCES `tb_nomina` (`id_nomina`),
  ADD CONSTRAINT `tb_nomina_detalle_ibfk_2` FOREIGN KEY (`id_empleado`) REFERENCES `tb_empleado` (`id_empleado`),
  ADD CONSTRAINT `tb_nomina_detalle_ibfk_3` FOREIGN KEY (`id_cargo`) REFERENCES `tb_cargo` (`id_cargo`),
  ADD CONSTRAINT `tb_nomina_detalle_ibfk_4` FOREIGN KEY (`id_tasa`) REFERENCES `tb_tasa_cambio` (`id_tasa`);

--
-- Filtros para la tabla `tb_permiso_empleado`
--
ALTER TABLE `tb_permiso_empleado`
  ADD CONSTRAINT `tb_permiso_empleado_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `tb_empleado` (`id_empleado`),
  ADD CONSTRAINT `tb_permiso_empleado_ibfk_2` FOREIGN KEY (`id_usuario_aprueba`) REFERENCES `tb_usuario` (`id_usuario`);

--
-- Filtros para la tabla `tb_usuario`
--
ALTER TABLE `tb_usuario`
  ADD CONSTRAINT `tb_usuario_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `tb_rol` (`id_rol`),
  ADD CONSTRAINT `tb_usuario_ibfk_2` FOREIGN KEY (`id_empleado`) REFERENCES `tb_empleado` (`id_empleado`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
