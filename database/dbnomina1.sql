-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-07-2026 a las 18:29:19
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
  `accion` varchar(50) NOT NULL,
  `tabla_afectada` varchar(50) NOT NULL,
  `id_registro_afectado` int(11) NOT NULL,
  `fecha_hora` datetime DEFAULT current_timestamp(),
  `detalle` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'S.S.O', 'Porcentaje', 0.00, 'Seguro Social Obligatorio'),
(2, 'R.P.E', 'Porcentaje', 0.00, 'Régimen Prestacional de Empleo'),
(3, 'L.P.H', 'Porcentaje', 0.00, 'Ley de Política Habitacional'),
(4, 'Préstamo', 'Monto Fijo', 0.00, 'Descuento por préstamo concedido'),
(5, 'Inasistencia', 'Monto Fijo', 0.00, 'Descuento por días no trabajados sin justificar'),
(6, 'ISLR', 'Monto Fijo', 0.00, 'Impuesto sobre la Renta retenido');

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
  `email` varchar(150) DEFAULT NULL,
  `id_tipo_empleado` int(11) NOT NULL,
  `id_jornada` int(11) NOT NULL,
  `sueldo_divisa` decimal(12,2) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Estructura de tabla para la tabla `tb_nomina`
--

CREATE TABLE `tb_nomina` (
  `id_nomina` int(11) NOT NULL,
  `id_periodo` int(11) NOT NULL,
  `id_usuario_genera` int(11) NOT NULL,
  `fecha_generacion` datetime DEFAULT current_timestamp(),
  `salario_minimo_vigente` decimal(12,2) NOT NULL,
  `semanas_descontar_ivss` int(11) DEFAULT 0,
  `factor_semanas_mes` decimal(10,6) NOT NULL DEFAULT 4.333333,
  `tasa_sso` decimal(5,4) NOT NULL DEFAULT 0.0400,
  `tasa_rpe` decimal(5,4) NOT NULL DEFAULT 0.0050,
  `tasa_lph` decimal(5,4) NOT NULL DEFAULT 0.0100,
  `estado` varchar(20) DEFAULT 'Borrador'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `dias_inasistencia` int(11) DEFAULT 0,
  `sueldo_bss` decimal(12,2) NOT NULL,
  `total_asignaciones` decimal(12,2) DEFAULT 0.00,
  `total_deducciones` decimal(12,2) DEFAULT 0.00,
  `neto_pagar` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_periodo_nomina`
--

CREATE TABLE `tb_periodo_nomina` (
  `id_periodo` int(11) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `tipo_periodo` varchar(20) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `anio` int(11) NOT NULL,
  `mes` int(11) NOT NULL,
  `numero_quincena` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(2, 'Personal de Apoyo', 'Consultas y reportes');

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
  `password_hash` varchar(255) NOT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `id_auditoria` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT de la tabla `tb_cuenta_bancaria`
--
ALTER TABLE `tb_cuenta_bancaria`
  MODIFY `id_cuenta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_empleado`
--
ALTER TABLE `tb_empleado`
  MODIFY `id_empleado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_empleado_cargo`
--
ALTER TABLE `tb_empleado_cargo`
  MODIFY `id_empleado_cargo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_jornada`
--
ALTER TABLE `tb_jornada`
  MODIFY `id_jornada` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tb_nomina`
--
ALTER TABLE `tb_nomina`
  MODIFY `id_nomina` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_nomina_asignacion`
--
ALTER TABLE `tb_nomina_asignacion`
  MODIFY `id_asignacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_nomina_deduccion`
--
ALTER TABLE `tb_nomina_deduccion`
  MODIFY `id_deduccion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_nomina_detalle`
--
ALTER TABLE `tb_nomina_detalle`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_periodo_nomina`
--
ALTER TABLE `tb_periodo_nomina`
  MODIFY `id_periodo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_permiso_empleado`
--
ALTER TABLE `tb_permiso_empleado`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_rol`
--
ALTER TABLE `tb_rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tb_tasa_cambio`
--
ALTER TABLE `tb_tasa_cambio`
  MODIFY `id_tasa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_tipo_empleado`
--
ALTER TABLE `tb_tipo_empleado`
  MODIFY `id_tipo_empleado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tb_usuario`
--
ALTER TABLE `tb_usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;

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
