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
        $this->requireRole(['Administrador', 'Operador']);

        $empleadoModel = new EmpleadoModel();
        $permisoModel = new PermisoModel();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $id_empleado = (int)($_POST['id_empleado'] ?? 0);
            $fecha_inicio = $_POST['fecha_inicio'] ?? '';
            $fecha_fin = $_POST['fecha_fin'] ?? '';
            $motivo = trim($_POST['motivo'] ?? '');

            if (!$id_empleado || !$fecha_inicio || !$fecha_fin || $motivo === '') {
                $error = 'Completa todos los campos obligatorios (*).';
            } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin) || strtotime($fecha_inicio) === false || strtotime($fecha_fin) === false) {
                $error = 'Las fechas ingresadas no son válidas.';
            } elseif ($fecha_fin < $fecha_inicio) {
                $error = 'La fecha de fin no puede ser anterior a la fecha de inicio.';
            } elseif (mb_strlen($motivo) < 3) {
                $error = 'El motivo del permiso debe contener al menos 3 caracteres.';
            } elseif (mb_strlen($motivo) > 500) {
                $error = 'El motivo del permiso no puede exceder los 500 caracteres.';
            } else {
                $dias = (int)round((strtotime($fecha_fin) - strtotime($fecha_inicio)) / 86400) + 1;
                if ($dias > 365) {
                    $error = 'La duración del permiso no puede exceder los 365 días.';
                } else {
                    $permisoModel->create($id_empleado, $fecha_inicio, $fecha_fin, $dias, $motivo);
                    Audit::log('PERMISOS', 'SOLICITAR', "Registró solicitud de permiso ({$dias} días) para el empleado ID {$id_empleado}", 'tb_permiso', null, [
                        'empleado_id' => $id_empleado,
                        'fecha_inicio' => $fecha_inicio,
                        'fecha_fin' => $fecha_fin,
                        'dias' => $dias,
                        'motivo' => $motivo
                    ]);
                    $this->redirect('index.php?r=permisos&msg=' . urlencode('Permiso registrado y en espera de aprobacion.'));
                }
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
        $this->requireRole(['Administrador', 'Operador']);

        $id = (int)($_GET['id'] ?? 0);
        $accion = $_GET['accion'] ?? '';

        if ($id && in_array($accion, ['Aprobado', 'Rechazado'], true)) {
            $permisoModel = new PermisoModel();
            $permisoModel->updateEstado($id, $accion, (int)$_SESSION['user_id']);
            Audit::log('PERMISOS', strtoupper($accion), "Cambió el estado del permiso ID {$id} a '{$accion}'", 'tb_permiso', $id);
        }

        $this->redirect('index.php?r=permisos&msg=' . urlencode('Permiso actualizado.'));
    }
}
