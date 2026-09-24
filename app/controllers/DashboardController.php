<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/EmpleadoModel.php';
require_once __DIR__ . '/../models/PermisoModel.php';
require_once __DIR__ . '/../models/PeriodoModel.php';
require_once __DIR__ . '/../models/NominaModel.php';
require_once __DIR__ . '/../models/TasaModel.php';

class DashboardController extends Controller {
    public function index(): void {
        $this->requireLogin();

        $empleadoModel = new EmpleadoModel();
        $permisoModel = new PermisoModel();
        $periodoModel = new PeriodoModel();
        $nominaModel = new NominaModel();
        $tasaModel = new TasaModel();

        // Empleados
        $empleados = $empleadoModel->getAll();
        $totalEmpleados = count(array_filter($empleados, fn($e) => $e['estado'] == 1));
        $totalInactivos = count(array_filter($empleados, fn($e) => $e['estado'] == 0));
        $empleadosAgregadosEsteMes = $empleadoModel->getEmpleadosAgregadosMesActualCount();

        // Permisos
        $permisos = $permisoModel->getAll();
        $permisosPendientes = count(array_filter($permisos, fn($p) => $p['estado'] === 'Pendiente'));

        // Periodos y Nóminas
        $ultimoPeriodo = $periodoModel->getUltimo();
        $totalNominasCount = $nominaModel->getTotalNominasCount();
        $montoTotalHistorico = $nominaModel->getMontoTotalHistorico();
        $montoUltimaNomina = $nominaModel->getMontoUltimaNomina();

        // Tasa BCV
        $ultimaTasa = $tasaModel->getUltima();
        $valorTasaBcv = $ultimaTasa ? (float)$ultimaTasa['valor_tasa'] : 0.0;
        $fechaTasaBcv = $ultimaTasa ? $ultimaTasa['fecha'] : null;

        // Cálculos bimonetarios (USD y Bs.)
        $montoUltimaNominaBs = $valorTasaBcv > 0 ? ($montoUltimaNomina * $valorTasaBcv) : 0.0;
        $montoTotalHistoricoBs = $valorTasaBcv > 0 ? ($montoTotalHistorico * $valorTasaBcv) : 0.0;

        // Resumen Financiero última nómina (Asignaciones vs Deducciones)
        $resumenFinanciero = $nominaModel->getResumenFinancieroUltimaNomina();
        $asignacionesBs = $valorTasaBcv > 0 ? ($resumenFinanciero['asignaciones'] * $valorTasaBcv) : 0.0;
        $deduccionesBs = $valorTasaBcv > 0 ? ($resumenFinanciero['deducciones'] * $valorTasaBcv) : 0.0;

        // Datos para gráfico histórico de nóminas y variación porcentual
        $historicoGrafico = $nominaModel->getHistoricoNominasParaGrafico(6);
        $mesesNombres = [1=>'Ene', 2=>'Feb', 3=>'Mar', 4=>'Abr', 5=>'May', 6=>'Jun', 7=>'Jul', 8=>'Ago', 9=>'Sep', 10=>'Oct', 11=>'Nov', 12=>'Dic'];
        $chartLabels = [];
        $chartAsignaciones = [];
        $chartDeducciones = [];
        $chartNeto = [];
        $chartEmpleados = [];

        foreach ($historicoGrafico as $h) {
            $mesNom = $mesesNombres[(int)$h['mes']] ?? 'M' . $h['mes'];
            $label = !empty($h['numero_quincena']) ? "Q{$h['numero_quincena']} {$mesNom} '{$h['anio']}" : "{$h['fecha_inicio']}";
            $chartLabels[] = $label;
            $chartAsignaciones[] = round((float)$h['total_asignaciones'], 2);
            $chartDeducciones[] = round((float)$h['total_deducciones'], 2);
            $chartNeto[] = round((float)$h['total_neto'], 2);
            $chartEmpleados[] = (int)$h['total_empleados'];
        }

        // Variación porcentual real respecto al ciclo inmediatamente anterior
        $variacionPorcentaje = null;
        $numHistorico = count($historicoGrafico);
        if ($numHistorico >= 2) {
            $netoAnterior = (float)$historicoGrafico[$numHistorico - 2]['total_neto'];
            $netoActual = (float)$historicoGrafico[$numHistorico - 1]['total_neto'];
            if ($netoAnterior > 0) {
                $variacionPorcentaje = round((($netoActual - $netoAnterior) / $netoAnterior) * 100, 1);
            }
        }

        // Manejo de desembolso efectivo vs deducciones excedentes
        $netoEfectivo = max(0.0, $montoUltimaNomina);
        $netoEfectivoBs = $valorTasaBcv > 0 ? ($netoEfectivo * $valorTasaBcv) : 0.0;
        $excesoDeducciones = $montoUltimaNomina < 0 ? abs($montoUltimaNomina) : 0.0;

        // Datos para gráfico de distribución por cargo
        $cargosData = $nominaModel->getDistribucionPorCargo();
        $cargoLabels = [];
        $cargoPresupuesto = [];
        $cargoHeadcount = [];
        foreach ($cargosData as $c) {
            $cargoLabels[] = $c['nombre_cargo'];
            $cargoPresupuesto[] = round((float)$c['total_presupuesto_usd'], 2);
            $cargoHeadcount[] = (int)$c['total_empleados'];
        }

        // Periodo actual: cálculo de progreso y días restantes
        $periodoProgreso = 0;
        $diasRestantes = 0;
        $diasTotalesPeriodo = 15;
        if (!empty($ultimoPeriodo['fecha_inicio']) && !empty($ultimoPeriodo['fecha_fin'])) {
            $ini = strtotime($ultimoPeriodo['fecha_inicio']);
            $fin = strtotime($ultimoPeriodo['fecha_fin']);
            $hoy = strtotime(date('Y-m-d'));
            $diasTotalesPeriodo = max(1, (int)round(($fin - $ini) / 86400) + 1);
            if ($hoy < $ini) {
                $periodoProgreso = 0;
                $diasRestantes = (int)round(($fin - $hoy) / 86400);
            } elseif ($hoy > $fin) {
                $periodoProgreso = 100;
                $diasRestantes = 0;
            } else {
                $transcurridos = (int)round(($hoy - $ini) / 86400) + 1;
                $periodoProgreso = min(100, max(0, (int)round(($transcurridos / $diasTotalesPeriodo) * 100)));
                $diasRestantes = max(0, (int)round(($fin - $hoy) / 86400));
            }
        }

        // Periodos recientes ordenados descendente
        $periodosRecientes = $periodoModel->getAll();
        usort($periodosRecientes, fn($a, $b) => $b['id_periodo'] <=> $a['id_periodo']);
        $periodosRecientes = array_slice($periodosRecientes, 0, 5);

        $showWelcome = !empty($_SESSION['welcome_banner']);
        if ($showWelcome) {
            unset($_SESSION['welcome_banner']);
        }

        $this->render('dashboard/inicio', [
            'totalEmpleados' => $totalEmpleados,
            'totalInactivos' => $totalInactivos,
            'empleadosAgregadosEsteMes' => $empleadosAgregadosEsteMes,
            'permisosPendientes' => $permisosPendientes,
            'ultimoPeriodo' => $ultimoPeriodo,
            'periodoProgreso' => $periodoProgreso,
            'diasRestantes' => $diasRestantes,
            'diasTotalesPeriodo' => $diasTotalesPeriodo,
            'totalNominasCount' => $totalNominasCount,
            'montoTotalHistorico' => $montoTotalHistorico,
            'montoTotalHistoricoBs' => $montoTotalHistoricoBs,
            'montoUltimaNomina' => $montoUltimaNomina,
            'montoUltimaNominaBs' => $montoUltimaNominaBs,
            'netoEfectivo' => $netoEfectivo,
            'netoEfectivoBs' => $netoEfectivoBs,
            'excesoDeducciones' => $excesoDeducciones,
            'variacionPorcentaje' => $variacionPorcentaje,
            'valorTasaBcv' => $valorTasaBcv,
            'fechaTasaBcv' => $fechaTasaBcv,
            'resumenFinanciero' => $resumenFinanciero,
            'asignacionesBs' => $asignacionesBs,
            'deduccionesBs' => $deduccionesBs,
            'periodosRecientes' => $periodosRecientes,
            'nominaModel' => $nominaModel,
            'showWelcome' => $showWelcome,
            // JSON para Chart.js
            'chartLabelsJson' => json_encode($chartLabels),
            'chartAsignacionesJson' => json_encode($chartAsignaciones),
            'chartDeduccionesJson' => json_encode($chartDeducciones),
            'chartNetoJson' => json_encode($chartNeto),
            'chartEmpleadosJson' => json_encode($chartEmpleados),
            'cargoLabelsJson' => json_encode($cargoLabels),
            'cargoPresupuestoJson' => json_encode($cargoPresupuesto),
            'cargoHeadcountJson' => json_encode($cargoHeadcount),
        ], 'Inicio');
    }

    public function search(): void {
        $this->requireLogin();
        header('Content-Type: application/json; charset=utf-8');

        $q = trim($_GET['q'] ?? '');
        $db = Database::getInstance();

        try {
            if ($q === '') {
                echo json_encode(['empleados' => [], 'periodos' => []]);
                exit;
            }

            $term = '%' . $q . '%';

            // Buscar empleados por nombre, apellido, cédula o cargo
            $stmtEmp = $db->prepare("
                SELECT e.id_empleado, e.primer_nombre, e.primer_apellido, e.cedula, e.estado,
                       c.nombre_cargo
                FROM tb_empleado e
                LEFT JOIN tb_empleado_cargo ec ON ec.id_empleado = e.id_empleado AND ec.es_actual = 1
                LEFT JOIN tb_cargo c ON c.id_cargo = ec.id_cargo
                WHERE e.primer_nombre LIKE ?
                   OR e.primer_apellido LIKE ?
                   OR CONCAT(e.primer_nombre, ' ', e.primer_apellido) LIKE ?
                   OR e.cedula LIKE ?
                   OR c.nombre_cargo LIKE ?
                ORDER BY e.estado DESC, e.primer_apellido ASC
                LIMIT 8
            ");
            $stmtEmp->execute([$term, $term, $term, $term, $term]);
            $empleados = $stmtEmp->fetchAll();

            // Buscar periodos y nóminas (soporte completo para 2da, 2ra, 1ra, meses en español y descripción)
            $qLower = mb_strtolower($q, 'UTF-8');
            $altQ = $q;
            if (strpos($qLower, '2ra') !== false) {
                $altQ = str_ireplace('2ra', '2da', $q);
            } elseif (strpos($qLower, '2da') !== false) {
                $altQ = str_ireplace('2da', '2ra', $q);
            }
            $altTerm = '%' . $altQ . '%';

            $mesesMap = [
                'ene' => 1, 'enero' => 1,
                'feb' => 2, 'febrero' => 2,
                'mar' => 3, 'marzo' => 3,
                'abr' => 4, 'abril' => 4,
                'may' => 5, 'mayo' => 5,
                'jun' => 6, 'junio' => 6,
                'jul' => 7, 'julio' => 7,
                'ago' => 8, 'agosto' => 8,
                'sep' => 9, 'sept' => 9, 'septiembre' => 9,
                'oct' => 10, 'octubre' => 10,
                'nov' => 11, 'noviembre' => 11,
                'dic' => 12, 'diciembre' => 12
            ];
            $matchedMes = null;
            foreach ($mesesMap as $k => $mNum) {
                if (strpos($qLower, $k) !== false) {
                    $matchedMes = $mNum;
                    break;
                }
            }

            $matchedQuincena = null;
            if (preg_match('/\b(1|1ra|primera)\b/i', $qLower)) {
                $matchedQuincena = 1;
            } elseif (preg_match('/\b(2|2da|2ra|segunda)\b/i', $qLower)) {
                $matchedQuincena = 2;
            }

            $sqlPer = "
                SELECT p.id_periodo, p.tipo_periodo, p.fecha_inicio, p.fecha_fin, p.anio, p.mes, p.numero_quincena,
                       n.id_nomina, n.estado AS estado_nomina,
                       COALESCE(NULLIF(TRIM(p.descripcion), ''), CONCAT(IF(p.numero_quincena = 2, '2da Quincena ', IF(p.numero_quincena = 1, '1ra Quincena ', '')), p.tipo_periodo, ' ', p.mes, '/', p.anio)) AS descripcion
                FROM tb_periodo_nomina p
                LEFT JOIN tb_nomina n ON n.id_periodo = p.id_periodo
                WHERE p.descripcion LIKE ?
                   OR p.descripcion LIKE ?
                   OR p.tipo_periodo LIKE ?
                   OR p.fecha_inicio LIKE ?
                   OR p.fecha_fin LIKE ?
                   OR p.anio LIKE ?
                   OR p.mes LIKE ?
            ";
            $perParams = [$term, $altTerm, $term, $term, $term, $term, $term];

            if ($matchedMes !== null) {
                $sqlPer .= " OR p.mes = ? ";
                $perParams[] = $matchedMes;
            }
            if ($matchedQuincena !== null) {
                $sqlPer .= " OR p.numero_quincena = ? ";
                $perParams[] = $matchedQuincena;
            }

            if (stripos($q, 'nom') !== false || stripos($q, 'nóm') !== false) {
                $sqlPer .= " OR n.id_nomina IS NOT NULL ";
            }

            $sqlPer .= " ORDER BY p.fecha_inicio DESC LIMIT 8";

            $stmtPer = $db->prepare($sqlPer);
            $stmtPer->execute($perParams);
            $periodos = $stmtPer->fetchAll();

            echo json_encode([
                'empleados' => $empleados,
                'periodos' => $periodos
            ]);
            exit;
        } catch (\Throwable $e) {
            echo json_encode([
                'empleados' => [],
                'periodos' => [],
                'error' => $e->getMessage()
            ]);
            exit;
        }
    }
}
