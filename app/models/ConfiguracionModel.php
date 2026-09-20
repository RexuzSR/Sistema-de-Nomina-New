<?php

require_once __DIR__ . '/../../core/Model.php';

class ConfiguracionModel extends Model {

    public static function getRegimenesPredefinidos(): array {
        return [
            'Clase_I' => [
                'nombre' => 'Riesgo Mínimo (Clase I)',
                'sso_patronal' => 0.0900,
                'sso_trabajador' => 0.0400,
                'rpe_patronal' => 0.0200,
                'rpe_trabajador' => 0.0050,
                'faov_patronal' => 0.0200,
                'faov_trabajador' => 0.0100,
                'descripcion' => 'Comercio, oficinas, servicios de baja exposición a siniestros.'
            ],
            'Clase_II' => [
                'nombre' => 'Riesgo Medio (Clase II)',
                'sso_patronal' => 0.1000,
                'sso_trabajador' => 0.0400,
                'rpe_patronal' => 0.0210,
                'rpe_trabajador' => 0.0050,
                'faov_patronal' => 0.0200,
                'faov_trabajador' => 0.0100,
                'descripcion' => 'Manufactura ligera, transporte ordinario, talleres y distribución.'
            ],
            'Clase_III' => [
                'nombre' => 'Riesgo Máximo (Clase III)',
                'sso_patronal' => 0.1100,
                'sso_trabajador' => 0.0400,
                'rpe_patronal' => 0.0220,
                'rpe_trabajador' => 0.0050,
                'faov_patronal' => 0.0200,
                'faov_trabajador' => 0.0100,
                'descripcion' => 'Construcción, minería, petroquímica, metalúrgica y alto riesgo laboral.'
            ]
        ];
    }

    public function getConfiguracion(): array {
        $stmt = $this->db->query("SELECT * FROM tb_configuracion_empresa ORDER BY id_config ASC LIMIT 1");
        $config = $stmt->fetch();

        if (!$config) {
            // Valores predeterminados en caso de tabla vacía
            return [
                'id_config' => 1,
                'nombre_empresa' => 'EMPRESA DEMO C.A.',
                'rif' => 'J-12345678-9',
                'regimen_ivss' => 'Clase_II',
                'tasa_sso_trabajador' => 0.0400,
                'tasa_sso_patronal' => 0.1000,
                'tasa_rpe_trabajador' => 0.0050,
                'tasa_rpe_patronal' => 0.0210,
                'tasa_faov_trabajador' => 0.0100,
                'tasa_faov_patronal' => 0.0200,
                'salario_minimo_legal' => 130.00,
                'tope_sso_salarios' => 5,
                'tope_rpe_salarios' => 5,
                'semanas_factor_anual' => 52
            ];
        }

        return $config;
    }

    public function updateConfiguracion(array $data): bool {
        $current = $this->getConfiguracion();
        $id = $current['id_config'] ?? 1;

        $stmt = $this->db->prepare("
            UPDATE tb_configuracion_empresa SET
                nombre_empresa = ?,
                rif = ?,
                regimen_ivss = ?,
                tasa_sso_trabajador = ?,
                tasa_sso_patronal = ?,
                tasa_rpe_trabajador = ?,
                tasa_rpe_patronal = ?,
                tasa_faov_trabajador = ?,
                tasa_faov_patronal = ?,
                salario_minimo_legal = ?,
                tope_sso_salarios = ?,
                tope_rpe_salarios = ?,
                semanas_factor_anual = ?
            WHERE id_config = ?
        ");

        return $stmt->execute([
            trim($data['nombre_empresa'] ?? 'EMPRESA DEMO C.A.'),
            trim($data['rif'] ?? 'J-12345678-9'),
            $data['regimen_ivss'] ?? 'Clase_II',
            (float)($data['tasa_sso_trabajador'] ?? 0.0400),
            (float)($data['tasa_sso_patronal'] ?? 0.1000),
            (float)($data['tasa_rpe_trabajador'] ?? 0.0050),
            (float)($data['tasa_rpe_patronal'] ?? 0.0210),
            (float)($data['tasa_faov_trabajador'] ?? 0.0100),
            (float)($data['tasa_faov_patronal'] ?? 0.0200),
            (float)($data['salario_minimo_legal'] ?? 130.00),
            (int)($data['tope_sso_salarios'] ?? 5),
            (int)($data['tope_rpe_salarios'] ?? 5),
            (int)($data['semanas_factor_anual'] ?? 52),
            $id
        ]);
    }
}
