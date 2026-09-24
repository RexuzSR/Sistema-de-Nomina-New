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
        $rif = strtoupper(trim($_POST['rif'] ?? ''));
        $regimen = $_POST['regimen_ivss'] ?? 'Clase_II';
        $sso_trab_pct = (float)($_POST['tasa_sso_trabajador'] ?? 4.0);
        $sso_pat_pct = (float)($_POST['tasa_sso_patronal'] ?? 10.0);
        $rpe_trab_pct = (float)($_POST['tasa_rpe_trabajador'] ?? 0.5);
        $rpe_pat_pct = (float)($_POST['tasa_rpe_patronal'] ?? 2.1);
        $faov_trab_pct = (float)($_POST['tasa_faov_trabajador'] ?? 1.0);
        $faov_pat_pct = (float)($_POST['tasa_faov_patronal'] ?? 2.0);
        $salario_min = (float)($_POST['salario_minimo_legal'] ?? 130.00);
        $tope_sso = (int)($_POST['tope_sso_salarios'] ?? 5);
        $tope_rpe = (int)($_POST['tope_rpe_salarios'] ?? 5);
        $semanas_anual = (int)($_POST['semanas_factor_anual'] ?? 52);

        $errors = [];
        if (empty($nombre_empresa) || mb_strlen($nombre_empresa) > 100) {
            $errors[] = 'El nombre de la empresa es obligatorio y no debe superar los 100 caracteres.';
        }
        if (empty($rif) || !preg_match('/^[JVEG]-\d{7,9}-\d$/i', $rif)) {
            if (preg_match('/^([JVEG])(\d{7,9})(\d)$/i', $rif, $m)) {
                $rif = strtoupper($m[1]) . '-' . $m[2] . '-' . $m[3];
            } else {
                $errors[] = 'El R.I.F. debe tener un formato válido (ej. J-12345678-9 o V-12345678-0).';
            }
        }
        if (!in_array($regimen, ['Clase_I', 'Clase_II', 'Clase_III'])) {
            $errors[] = 'El régimen de IVSS seleccionado no es válido.';
        }
        if ($sso_trab_pct < 0 || $sso_trab_pct > 50 || $sso_pat_pct < 0 || $sso_pat_pct > 50) {
            $errors[] = 'Las tasas de SSO deben encontrarse entre 0% y 50%.';
        }
        if ($rpe_trab_pct < 0 || $rpe_trab_pct > 50 || $rpe_pat_pct < 0 || $rpe_pat_pct > 50) {
            $errors[] = 'Las tasas de RPE deben encontrarse entre 0% y 50%.';
        }
        if ($faov_trab_pct < 0 || $faov_trab_pct > 50 || $faov_pat_pct < 0 || $faov_pat_pct > 50) {
            $errors[] = 'Las tasas de FAOV deben encontrarse entre 0% y 50%.';
        }
        if ($salario_min <= 0 || $salario_min > 1000000) {
            $errors[] = 'El salario mínimo legal debe ser mayor a 0 y menor a 1.000.000.';
        }
        if ($tope_sso < 1 || $tope_sso > 20 || $tope_rpe < 1 || $tope_rpe > 20) {
            $errors[] = 'Los topes de salarios mínimos para SSO y RPE deben ubicarse entre 1 y 20.';
        }
        if ($semanas_anual < 50 || $semanas_anual > 54) {
            $errors[] = 'El factor de semanas anual debe estar entre 50 y 54 semanas.';
        }

        if (!empty($errors)) {
            $this->redirect('index.php?r=configuracion&error=' . urlencode(implode(' ', $errors)));
        }

        $sso_trab = $sso_trab_pct / 100;
        $sso_pat = $sso_pat_pct / 100;
        $rpe_trab = $rpe_trab_pct / 100;
        $rpe_pat = $rpe_pat_pct / 100;
        $faov_trab = $faov_trab_pct / 100;
        $faov_pat = $faov_pat_pct / 100;

        $exito = $configModel->updateConfiguracion([
            'nombre_empresa' => $nombre_empresa,
            'rif' => $rif,
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
