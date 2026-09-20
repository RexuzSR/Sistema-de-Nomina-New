<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/NominaModel.php';
require_once __DIR__ . '/../models/PeriodoModel.php';
require_once __DIR__ . '/../models/EmpleadoModel.php';
require_once __DIR__ . '/../models/TasaModel.php';
require_once __DIR__ . '/../models/ConfiguracionModel.php';
require_once __DIR__ . '/../services/ReciboPdfService.php';

class NominaController extends Controller {
    public function generar(): void {
        $this->requireRole(['Administrador', 'Operador']);

        $id_periodo = (int)($_GET['id_periodo'] ?? $_POST['id_periodo'] ?? 0);
        $periodoModel = new PeriodoModel();
        $periodo = $periodoModel->getById($id_periodo);

        if (!$periodo) {
            $this->redirect('index.php?r=periodos&msg=' . urlencode('Periodo no encontrado.'));
        }

        $nominaModel = new NominaModel();
        $tasaModel = new TasaModel();
        $configModel = new ConfiguracionModel();
        $empConfig = $configModel->getConfiguracion();

        $nomina = $nominaModel->getByPeriodo($id_periodo);
        $ultimaTasa = $tasaModel->getUltima();
        $error = '';

        $semanas_anual = (float)($empConfig['semanas_factor_anual'] ?? 52);
        $factor_mes = $semanas_anual > 0 ? number_format($semanas_anual / 12, 6, '.', '') : '4.333333';

        $sugerencias = [
            'salario_minimo_vigente' => (string)($empConfig['salario_minimo_legal'] ?? '130.00'),
            'semanas_descontar_ivss' => '2',
            'factor_semanas_mes' => $factor_mes,
            'tasa_sso' => number_format((float)($empConfig['tasa_sso_trabajador'] ?? 0.04) * 100, 2, '.', ''),
            'tasa_rpe' => number_format((float)($empConfig['tasa_rpe_trabajador'] ?? 0.005) * 100, 2, '.', ''),
            'tasa_lph' => number_format((float)($empConfig['tasa_faov_trabajador'] ?? 0.01) * 100, 2, '.', '')
        ];

        if (!$nomina && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $salario_minimo = $_POST['salario_minimo_vigente'] ?? '';
            $semanas = $_POST['semanas_descontar_ivss'] ?? '';
            $factor_semanas = $_POST['factor_semanas_mes'] ?? '4.333333';
            $tasa_sso_input = $_POST['tasa_sso'] ?? '4.0';
            $tasa_rpe_input = $_POST['tasa_rpe'] ?? '0.5';
            $tasa_lph_input = $_POST['tasa_lph'] ?? '1.0';

            // Convert commas to dots for decimal format in Spanish input
            $factor_semanas = str_replace(',', '.', $factor_semanas);
            $tasa_sso_input = str_replace(',', '.', $tasa_sso_input);
            $tasa_rpe_input = str_replace(',', '.', $tasa_rpe_input);
            $tasa_lph_input = str_replace(',', '.', $tasa_lph_input);

            if ($salario_minimo === '' || !is_numeric($salario_minimo) || 
                $semanas === '' || !is_numeric($semanas) || 
                $factor_semanas === '' || !is_numeric($factor_semanas) ||
                $tasa_sso_input === '' || !is_numeric($tasa_sso_input) ||
                $tasa_rpe_input === '' || !is_numeric($tasa_rpe_input) ||
                $tasa_lph_input === '' || !is_numeric($tasa_lph_input)) {
                $error = 'Completa el salario minimo vigente, las semanas a descontar IVSS, el factor de semanas por mes y todas las tasas de deducción.';
            } elseif (!$ultimaTasa) {
                $error = 'Debes registrar al menos una tasa de cambio antes de generar la nomina.';
            } else {
                // Convert percentage values to decimals (e.g. 4.0% -> 0.04)
                $tasa_sso = (float)$tasa_sso_input / 100;
                $tasa_rpe = (float)$tasa_rpe_input / 100;
                $tasa_lph = (float)$tasa_lph_input / 100;

                $id_nomina = $nominaModel->createNomina(
                    $id_periodo, 
                    (int)$_SESSION['user_id'], 
                    (float)$salario_minimo, 
                    (float)$semanas, 
                    (float)$factor_semanas,
                    $tasa_sso,
                    $tasa_rpe,
                    $tasa_lph
                );

                Audit::log('NOMINA', 'GENERAR_NOMINA', "Generó la nómina #{$id_nomina} para el periodo ID {$id_periodo}", 'tb_nomina', (int)$id_nomina, [
                    'periodo_id' => $id_periodo,
                    'salario_minimo' => (float)$salario_minimo,
                    'semanas_ivss' => (float)$semanas
                ]);

                $this->redirect('index.php?r=nomina/detalle&id_nomina=' . $id_nomina . '&msg=' . urlencode('Nomina generada. Ahora ajusta los dias trabajados de cada empleado.'));
            }
        }

        if ($nomina) {
            $this->redirect('index.php?r=nomina/detalle&id_nomina=' . $nomina['id_nomina']);
        }

        $this->render('nomina/generar', [
            'periodo' => $periodo,
            'ultimaTasa' => $ultimaTasa,
            'error' => $error,
            'id_periodo' => $id_periodo,
            'sugerencias' => $sugerencias
        ], 'Generar Nomina');
    }

    public function detalle(): void {
        $this->requireLogin();

        $id_nomina = (int)($_GET['id_nomina'] ?? $_POST['id_nomina'] ?? 0);
        $nominaModel = new NominaModel();
        $periodoModel = new PeriodoModel();
        $empleadoModel = new EmpleadoModel();

        $nomina = $nominaModel->getById($id_nomina);
        if (!$nomina) {
            $this->redirect('index.php?r=periodos&msg=' . urlencode('Nomina no encontrada.'));
        }
        $periodo = $periodoModel->getById($nomina['id_periodo']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $action = $_POST['action'] ?? '';
            
            if ($action === 'agregar_trabajadores') {
                $seleccionados = $_POST['id_empleados'] ?? [];
                if (!empty($seleccionados)) {
                    $nominaModel->agregarTrabajadoresANomina($id_nomina, $seleccionados);
                    $msg = 'Trabajadores agregados a la nómina exitosamente.';
                } else {
                    $msg = 'No se seleccionó ningún trabajador.';
                }
                $this->redirect('index.php?r=nomina/detalle&id_nomina=' . $id_nomina . '&msg=' . urlencode($msg));
            } elseif ($action === 'eliminar_trabajador') {
                $id_empleado_del = (int)($_POST['id_empleado'] ?? 0);
                if ($id_empleado_del > 0) {
                    $nominaModel->eliminarEmpleadoDeNomina($id_nomina, $id_empleado_del);
                }
                $this->redirect('index.php?r=nomina/detalle&id_nomina=' . $id_nomina . '&msg=' . urlencode('Trabajador removido de la nómina.'));
            } elseif ($action === 'actualizar_tasas_ley') {
                $raw_sso = str_replace(',', '.', $_POST['tasa_sso'] ?? '4.0');
                $raw_rpe = str_replace(',', '.', $_POST['tasa_rpe'] ?? '0.5');
                $raw_lph = str_replace(',', '.', $_POST['tasa_lph'] ?? '1.0');

                $tasa_sso = max(0, min(100, (float)$raw_sso)) / 100;
                $tasa_rpe = max(0, min(100, (float)$raw_rpe)) / 100;
                $tasa_lph = max(0, min(100, (float)$raw_lph)) / 100;

                $nominaModel->actualizarTasasLey($id_nomina, $tasa_sso, $tasa_rpe, $tasa_lph);
                $this->redirect('index.php?r=nomina/detalle&id_nomina=' . $id_nomina . '&msg=' . urlencode('Tasas de Ley actualizadas y deducciones recalculadas exitosamente.'));
            } else {
                // Save worker detail calculations
                $id_empleado = (int)($_POST['id_empleado'] ?? 0);
                if ($id_empleado > 0) {
                    $nominaModel->calcularYGuardarDetalle($id_nomina, $id_empleado, $_POST);
                }
                $this->redirect('index.php?r=nomina/detalle&id_nomina=' . $id_nomina . '&msg=' . urlencode('Detalle guardado y nómina recalculada.'));
            }
        }

        $allEmpleados = $empleadoModel->getAll();
        $empleadosActivos = array_values(array_filter($allEmpleados, fn($e) => $e['estado'] == 1));

        $detallesList = $nominaModel->getDetallesByNomina($id_nomina);
        $detalles = [];
        foreach ($detallesList as $d) {
            $detalles[$d['id_empleado']] = $d;
        }

        $msg = $_GET['msg'] ?? '';
        $configModel = new ConfiguracionModel();
        $empConfig = $configModel->getConfiguracion();

        $this->render('nomina/detalle', [
            'nomina' => $nomina,
            'periodo' => $periodo,
            'empleadosActivos' => $empleadosActivos,
            'detallesList' => $detallesList,
            'detalles' => $detalles,
            'nominaModel' => $nominaModel,
            'empConfig' => $empConfig,
            'msg' => $msg
        ], 'Cargar Detalle de Nomina');
    }

    public function ver(): void {
        $this->requireLogin();

        $id_nomina = (int)($_GET['id_nomina'] ?? 0);
        $nominaModel = new NominaModel();
        $periodoModel = new PeriodoModel();

        $nomina = $nominaModel->getById($id_nomina);
        if (!$nomina) {
            $this->redirect('index.php?r=periodos&msg=' . urlencode('Nomina no encontrada.'));
        }
        $periodo = $periodoModel->getById($nomina['id_periodo']);

        $filas = $nominaModel->getDetallesByNomina($id_nomina);

        $totales = ['total_asignaciones' => 0, 'total_deducciones' => 0, 'neto_pagar' => 0];
        foreach ($filas as $f) {
            $totales['total_asignaciones'] += $f['total_asignaciones'];
            $totales['total_deducciones'] += $f['total_deducciones'];
            $totales['neto_pagar'] += $f['neto_pagar'];
        }

        $this->render('nomina/ver', [
            'id_nomina' => $id_nomina,
            'nomina' => $nomina,
            'periodo' => $periodo,
            'filas' => $filas,
            'totales' => $totales
        ], 'Nomina calculada');
    }

    public function recibo(): void {
        $this->requireLogin();

        $id_nomina = (int)($_GET['id_nomina'] ?? 0);
        $id_empleado = (int)($_GET['id_empleado'] ?? 0);
        $format = strtolower(trim($_GET['format'] ?? ''));

        $nominaModel = new NominaModel();
        $periodoModel = new PeriodoModel();
        $empleadoModel = new EmpleadoModel();
        $configModel = new ConfiguracionModel();
        $tasaModel = new TasaModel();

        $nomina = $nominaModel->getById($id_nomina);
        $periodo = $nomina ? $periodoModel->getById($nomina['id_periodo']) : null;
        $empConfig = $configModel->getConfiguracion();
        $ultimaTasa = $tasaModel->getUltima();

        if (!$nomina) {
            die('Nómina no encontrada.');
        }

        if ($id_empleado > 0) {
            $empleado = $empleadoModel->getById($id_empleado);
            $cargo = $empleadoModel->getCargoActual($id_empleado);
            $detalle = $nominaModel->getDetalleEmpleado($id_nomina, $id_empleado);

            if (!$empleado || !$detalle) {
                die('Datos no encontrados. Verifica que ya se haya guardado el detalle de este empleado en la nómina.');
            }

            $asignaciones = $nominaModel->getAsignacionesDetalle($detalle['id_detalle']);
            $deducciones = $nominaModel->getDeduccionesDetalle($detalle['id_detalle']);

            if ($format === 'pdf') {
                $pdf = ReciboPdfService::generarReciboIndividual(
                    $nomina, $periodo, $empleado, $cargo, $detalle, 
                    $asignaciones, $deducciones, $empConfig, $ultimaTasa
                );
                $cedula = preg_replace('/[^0-9A-Za-z]/', '', $empleado['cedula'] ?? 'trabajador');
                $filename = 'Recibo_Pago_' . $cedula . '_' . ($periodo['fecha_inicio'] ?? '') . '.pdf';
                $pdf->Output('I', $filename);
                exit;
            }

            extract([
                'id_nomina' => $id_nomina,
                'nomina' => $nomina,
                'periodo' => $periodo,
                'empleado' => $empleado,
                'cargo' => $cargo,
                'detalle' => $detalle,
                'asignaciones' => $asignaciones,
                'deducciones' => $deducciones,
                'empConfig' => $empConfig,
                'ultimaTasa' => $ultimaTasa
            ]);
            require __DIR__ . '/../views/nomina/recibo.php';
            exit;
        } else {
            $detallesList = $nominaModel->getDetallesByNomina($id_nomina);
            $recibos = [];

            foreach ($detallesList as $d) {
                $empId = $d['id_empleado'];
                $emp = $empleadoModel->getById($empId);
                $crg = $empleadoModel->getCargoActual($empId);
                $asig = $nominaModel->getAsignacionesDetalle($d['id_detalle']);
                $ded = $nominaModel->getDeduccionesDetalle($d['id_detalle']);

                $recibos[] = [
                    'empleado' => $emp,
                    'cargo' => $crg,
                    'detalle' => $d,
                    'asignaciones' => $asig,
                    'deducciones' => $ded
                ];
            }

            if ($format === 'pdf') {
                $modo = $_GET['modo'] ?? 'carta6';
                if ($modo === 'compacto') $modo = 'carta6';
                $pdf = ReciboPdfService::generarRecibosMasivos(
                    $nomina, $periodo, $recibos, $empConfig, $ultimaTasa, $modo
                );
                $filename = 'Recibos_Nomina_Periodo_' . ($periodo['fecha_inicio'] ?? '') . '_al_' . ($periodo['fecha_fin'] ?? '') . '.pdf';
                $pdf->Output('I', $filename);
                exit;
            }

            extract([
                'id_nomina' => $id_nomina,
                'nomina' => $nomina,
                'periodo' => $periodo,
                'recibos' => $recibos,
                'empConfig' => $empConfig,
                'ultimaTasa' => $ultimaTasa
            ]);
            require __DIR__ . '/../views/nomina/recibo_masivo.php';
            exit;
        }
    }

    public function reportePdf(): void {
        $this->requireLogin();

        $id_nomina = (int)($_GET['id_nomina'] ?? 0);
        $nominaModel = new NominaModel();
        $periodoModel = new PeriodoModel();
        $configModel = new ConfiguracionModel();
        $tasaModel = new TasaModel();

        $nomina = $nominaModel->getById($id_nomina);
        if (!$nomina) { die('Nómina no encontrada.'); }

        $periodo = $periodoModel->getById($nomina['id_periodo']);
        $detallesList = $nominaModel->getDetallesByNomina($id_nomina);
        $empConfig = $configModel->getConfiguracion();
        $ultimaTasa = $tasaModel->getUltima();

        $pdf = ReciboPdfService::generarResumenNominaPdf(
            $nomina, $periodo, $detallesList, $empConfig, $ultimaTasa
        );
        $filename = 'Reporte_Resumen_Nomina_' . ($periodo['fecha_inicio'] ?? '') . '_al_' . ($periodo['fecha_fin'] ?? '') . '.pdf';
        $pdf->Output('I', $filename);
        exit;
    }

    public function exportExcel(): void {
        $this->requireLogin();

        $id_nomina = (int)($_GET['id_nomina'] ?? 0);
        $nominaModel = new NominaModel();
        $periodoModel = new PeriodoModel();

        $nomina = $nominaModel->getById($id_nomina);
        if (!$nomina) { die('Nomina no encontrada.'); }
        $periodo = $periodoModel->getById($nomina['id_periodo']);

        $filas = $nominaModel->getDetallesByNomina($id_nomina);
        $nombreArchivo = 'Nomina_' . $periodo['fecha_inicio'] . '_al_' . $periodo['fecha_fin'] . '.xls';

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');

        require __DIR__ . '/../views/export/excel.php';
    }
}
