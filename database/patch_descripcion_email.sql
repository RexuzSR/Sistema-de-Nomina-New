USE dbnomina1;

-- Agregar columna descripcion a tb_periodo_nomina si no existe
ALTER TABLE `tb_periodo_nomina` ADD COLUMN IF NOT EXISTS `descripcion` VARCHAR(255) DEFAULT NULL;

-- Agregar columna email a tb_empleado si no existe
ALTER TABLE `tb_empleado` ADD COLUMN IF NOT EXISTS `email` VARCHAR(150) DEFAULT NULL;
