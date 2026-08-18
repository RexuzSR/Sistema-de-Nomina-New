-- ============================================================
-- Patch adicional para dbnomina1
-- Ejecutar DESPUES de importar database/dbnomina1.sql
-- No modifica la estructura, solo actualiza valores por defecto
-- que en el dump original quedaron en 0.00
-- ============================================================
USE dbnomina1;

UPDATE tb_concepto_deduccion SET valor_default = 0.0400 WHERE nombre = 'S.S.O';
UPDATE tb_concepto_deduccion SET valor_default = 0.0050 WHERE nombre = 'R.P.E';
UPDATE tb_concepto_deduccion SET valor_default = 0.0100 WHERE nombre = 'L.P.H';
