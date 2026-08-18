<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/PermisoModel.php';
require_once __DIR__ . '/../models/EmpleadoModel.php';

class PermisoController extends Controller {
    public function index(): void {
        $this->requireLogin();

        $permisoModel = new PermisoModel();
        $permisos = $permisoModel->getAll();
        $msg = $_GET['msg'] ?? '';

        $this->render('permisos/listar', [
            'permisos' => $permisos,
            'msg' => $msg
        ], 'Permisos');
    }

    public function solicitar(): void {
        $this->requireLogin();

        $empleadoModel = new EmpleadoModel();
        $permisoModel = new PermisoModel();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_empleado = (int)($_POST['id_empleado'] ?? 0);
            $fecha_inicio = $_POST['fecha_inicio'] ?? '';
            $fecha_fin = $_POST['fecha_fin'] ?? '';
            $motivo = trim($_POST['motivo'] ?? '');

            if (!$id_empleado || !$fecha_inicio || !$fecha_fin || $motivo === '') {
                $error = 'Completa todos los campos.';
            } elseif ($fecha_fin < $fecha_inicio) {
                $error = 'La fecha de fin no puede ser anterior a la fecha de inicio.';
            } else {
                $dias = (strtotime($fecha_fin) - strtotime($fecha_inicio)) / 86400 + 1;
                $permisoModel->create($id_empleado, $fecha_inicio, $fecha_fin, $dias, $motivo);
                $this->redirect('index.php?r=permisos&msg=' . urlencode('Permiso registrado y en espera de aprobacion.'));
            }
        }

        $allEmpleados = $empleadoModel->getAll();
        $empleados = array_filter($allEmpleados, fn($e) => $e['estado'] == 1);

        $this->render('permisos/solicitar', [
            'empleados' => $empleados,
            'error' => $error
        ], 'Registrar permiso');
    }

    public function aprobar(): void {
        $this->requireLogin();

        $id = (int)($_GET['id'] ?? 0);
        $accion = $_GET['accion'] ?? '';

        if ($id && in_array($accion, ['Aprobado', 'Rechazado'], true)) {
            $permisoModel = new PermisoModel();
            $permisoModel->updateEstado($id, $accion, (int)$_SESSION['user_id']);
        }

        $this->redirect('index.php?r=permisos&msg=' . urlencode('Permiso actualizado.'));
    }
}
