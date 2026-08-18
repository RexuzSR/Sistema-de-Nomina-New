<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/EmpleadoModel.php';
require_once __DIR__ . '/../models/PermisoModel.php';
require_once __DIR__ . '/../models/PeriodoModel.php';
require_once __DIR__ . '/../models/NominaModel.php';

class DashboardController extends Controller {
    public function index(): void {
        $this->requireLogin();

        $empleadoModel = new EmpleadoModel();
        $permisoModel = new PermisoModel();
        $periodoModel = new PeriodoModel();
        $nominaModel = new NominaModel();

        $empleados = $empleadoModel->getAll();
        $totalEmpleados = count(array_filter($empleados, fn($e) => $e['estado'] == 1));
        $totalInactivos = count(array_filter($empleados, fn($e) => $e['estado'] == 0));
        $empleadosAgregadosEsteMes = $empleadoModel->getEmpleadosAgregadosMesActualCount();

        $permisos = $permisoModel->getAll();
        $permisosPendientes = count(array_filter($permisos, fn($p) => $p['estado'] === 'Pendiente'));

        $ultimoPeriodo = $periodoModel->getUltimo();
        $totalNominasCount = $nominaModel->getTotalNominasCount();
        $montoTotalHistorico = $nominaModel->getMontoTotalHistorico();
        $montoUltimaNomina = $nominaModel->getMontoUltimaNomina();

        // Get recent periods with status
        $periodosRecientes = $periodoModel->getAll();
        // Sort descending by id_periodo
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
            'totalNominasCount' => $totalNominasCount,
            'montoTotalHistorico' => $montoTotalHistorico,
            'montoUltimaNomina' => $montoUltimaNomina,
            'periodosRecientes' => $periodosRecientes,
            'nominaModel' => $nominaModel,
            'showWelcome' => $showWelcome,
        ], 'Inicio');
    }
}
