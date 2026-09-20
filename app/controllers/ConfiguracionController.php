<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/ConfiguracionModel.php';

class ConfiguracionController extends Controller {

    public function index(): void {
        $this->requireRole(['Administrador']);

        $configModel = new ConfiguracionModel();
        $config = $configModel->getConfiguracion();
        $regimenes = ConfiguracionModel::getRegimenesPredefinidos();
        $msg = $_GET['msg'] ?? '';
        $error = $_GET['error'] ?? '';

        $this->render('configuracion/index', [
            'config' => $config,
            'regimenes' => $regimenes,
            'msg' => $msg,
            'error' => $error
        ], 'Configuraciones Generales de Nómina');
    }

    public function guardar(): void {
        $this->requireRole(['Administrador']);
        $this->validateCsrf();

        $configModel = new ConfiguracionModel();

        $nombre_empresa = trim($_POST['nombre_empresa'] ?? '');
        $rif = trim($_POST['rif'] ?? '');
        $regimen = $_POST['regimen_ivss'] ?? 'Clase_II';
        $sso_trab = (float)($_POST['tasa_sso_trabajador'] ?? 4.0) / 100;
        $sso_pat = (float)($_POST['tasa_sso_patronal'] ?? 10.0) / 100;
        $rpe_trab = (float)($_POST['tasa_rpe_trabajador'] ?? 0.5) / 100;
        $rpe_pat = (float)($_POST['tasa_rpe_patronal'] ?? 2.1) / 100;
        $faov_trab = (float)($_POST['tasa_faov_trabajador'] ?? 1.0) / 100;
        $faov_pat = (float)($_POST['tasa_faov_patronal'] ?? 2.0) / 100;
        $salario_min = (float)($_POST['salario_minimo_legal'] ?? 130.00);
        $tope_sso = (int)($_POST['tope_sso_salarios'] ?? 5);
        $tope_rpe = (int)($_POST['tope_rpe_salarios'] ?? 5);
        $semanas_anual = (int)($_POST['semanas_factor_anual'] ?? 52);

        if ($salario_min <= 0 || $tope_sso <= 0 || $tope_rpe <= 0) {
            $this->redirect('index.php?r=configuracion&error=' . urlencode('Los valores de salario mínimo y topes deben ser números positivos válidos.'));
        }

        $exito = $configModel->updateConfiguracion([
            'nombre_empresa' => $nombre_empresa ?: 'EMPRESA DEMO C.A.',
            'rif' => $rif ?: 'J-12345678-9',
            'regimen_ivss' => $regimen,
            'tasa_sso_trabajador' => $sso_trab,
            'tasa_sso_patronal' => $sso_pat,
            'tasa_rpe_trabajador' => $rpe_trab,
            'tasa_rpe_patronal' => $rpe_pat,
            'tasa_faov_trabajador' => $faov_trab,
            'tasa_faov_patronal' => $faov_pat,
            'salario_minimo_legal' => $salario_min,
            'tope_sso_salarios' => $tope_sso,
            'tope_rpe_salarios' => $tope_rpe,
            'semanas_factor_anual' => $semanas_anual
        ]);

        if ($exito) {
            Audit::log(
                'CONFIGURACION',
                'MODIFICAR',
                "Actualizó los parámetros globales de empresa: Régimen {$regimen}, SSO Trab: " . ($sso_trab * 100) . "%, RPE Trab: " . ($rpe_trab * 100) . "%, FAOV Trab: " . ($faov_trab * 100) . "%, Salario Mínimo: Bs. {$salario_min}, Tope: {$tope_sso} SM",
                'tb_configuracion_empresa',
                1,
                $_POST
            );

            $this->redirect('index.php?r=configuracion&msg=' . urlencode('Configuraciones generales de la empresa actualizadas exitosamente. Afectarán a todas las nóminas del sistema.'));
        } else {
            $this->redirect('index.php?r=configuracion&error=' . urlencode('No se pudieron guardar las configuraciones generales.'));
        }
    }
}
