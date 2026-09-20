<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/EmpleadoModel.php';
require_once __DIR__ . '/../models/TasaModel.php';

class EmpleadoController extends Controller {
    public function index(): void {
        $this->requireLogin();

        $empleadoModel = new EmpleadoModel();
        $empleados = $empleadoModel->getAll();
        $msg = $_GET['msg'] ?? '';

        $tasaModel = new TasaModel();
        $ultimaTasa = $tasaModel->getUltima();
        $tasaBcv = $ultimaTasa ? (float)$ultimaTasa['valor_tasa'] : 0.0;

        $this->render('empleados/listar', [
            'empleados' => $empleados,
            'msg' => $msg,
            'tasaBcv' => $tasaBcv
        ], 'Empleados');
    }


    private function handlePhotoUpload(?string $fotoActual = null): ?string {
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['foto'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            
            if (in_array($ext, $allowed, true) && $file['size'] <= 5 * 1024 * 1024) {
                $mimeMap = [
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'webp' => 'image/webp',
                    'gif' => 'image/gif'
                ];
                $mime = $mimeMap[$ext] ?? 'image/jpeg';
                $binary = file_get_contents($file['tmp_name']);
                if ($binary !== false) {
                    return 'data:' . $mime . ';base64,' . base64_encode($binary);
                }
            }
        }
        return $fotoActual;
    }

    public function crear(): void {
        $this->requireRole(['Administrador', 'Operador']);

        $empleadoModel = new EmpleadoModel();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $cedula = trim($_POST['cedula'] ?? '');
            $p_nombre = trim($_POST['primer_nombre'] ?? '');
            $s_nombre = trim($_POST['segundo_nombre'] ?? '');
            $p_apellido = trim($_POST['primer_apellido'] ?? '');
            $s_apellido = trim($_POST['segundo_apellido'] ?? '');
            $id_tipo = (int)($_POST['id_tipo_empleado'] ?? 0);
            $id_jornada = (int)($_POST['id_jornada'] ?? 0);
            $id_cargo = (int)($_POST['id_cargo'] ?? 0);
            $sueldo = $_POST['sueldo_divisa'] ?? '';
            $fecha_ingreso = $_POST['fecha_ingreso'] ?? '';
            $email = trim($_POST['email'] ?? '');
            $id_banco = (int)($_POST['id_banco'] ?? 0);
            $numero_cuenta = trim($_POST['numero_cuenta'] ?? '');
            $tipo_cuenta = trim($_POST['tipo_cuenta'] ?? 'Corriente');

            if ($cedula === '' || $p_nombre === '' || $p_apellido === '' || !$id_tipo || !$id_jornada || !$id_cargo
                || $sueldo === '' || !is_numeric($sueldo) || $fecha_ingreso === '') {
                $error = 'Completa todos los campos obligatorios (*) con valores válidos.';
            } else {
                $fotoFilename = $this->handlePhotoUpload(null);

                $empleadoModel->create([
                    'cedula' => $cedula,
                    'primer_nombre' => $p_nombre,
                    'segundo_nombre' => $s_nombre,
                    'primer_apellido' => $p_apellido,
                    'segundo_apellido' => $s_apellido,
                    'email' => $email,
                    'foto' => $fotoFilename,
                    'id_tipo_empleado' => $id_tipo,
                    'id_jornada' => $id_jornada,
                    'id_cargo' => $id_cargo,
                    'sueldo_divisa' => $sueldo,
                    'fecha_ingreso' => $fecha_ingreso,
                    'id_banco' => $id_banco,
                    'numero_cuenta' => $numero_cuenta,
                    'tipo_cuenta' => $tipo_cuenta
                ]);

                Audit::log('EMPLEADOS', 'CREAR', "Registró al nuevo empleado {$p_nombre} {$p_apellido} (C.I. {$cedula})", 'tb_empleado', null, [
                    'cedula' => $cedula,
                    'nombre_completo' => "{$p_nombre} {$s_nombre} {$p_apellido} {$s_apellido}",
                    'cargo_id' => $id_cargo,
                    'sueldo_divisa' => $sueldo,
                    'email' => $email
                ]);

                $this->redirect('index.php?r=empleados&msg=' . urlencode('Empleado creado correctamente.'));
            }
        }

        $this->render('empleados/crear', [
            'tipos' => $empleadoModel->getTipos(),
            'jornadas' => $empleadoModel->getJornadas(),
            'cargos' => $empleadoModel->getCargos(),
            'bancos' => $empleadoModel->getBancos(),
            'error' => $error
        ], 'Nuevo empleado');
    }

    public function editar(): void {
        $this->requireRole(['Administrador', 'Operador']);

        $empleadoModel = new EmpleadoModel();
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        $empleado = $empleadoModel->getById($id);

        if (!$empleado) {
            $this->redirect('index.php?r=empleados&msg=' . urlencode('Empleado no encontrado.'));
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $cedula = trim($_POST['cedula'] ?? '');
            $p_nombre = trim($_POST['primer_nombre'] ?? '');
            $s_nombre = trim($_POST['segundo_nombre'] ?? '');
            $p_apellido = trim($_POST['primer_apellido'] ?? '');
            $s_apellido = trim($_POST['segundo_apellido'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $id_tipo = (int)($_POST['id_tipo_empleado'] ?? 0);
            $id_jornada = (int)($_POST['id_jornada'] ?? 0);
            $id_cargo = (int)($_POST['id_cargo'] ?? 0);
            $sueldo = $_POST['sueldo_divisa'] ?? '';
            $fecha_ingreso = $_POST['fecha_ingreso'] ?? '';
            $id_banco = (int)($_POST['id_banco'] ?? 0);
            $numero_cuenta = trim($_POST['numero_cuenta'] ?? '');
            $tipo_cuenta = trim($_POST['tipo_cuenta'] ?? 'Corriente');

            if ($cedula === '' || $p_nombre === '' || $p_apellido === '' || !$id_tipo || !$id_jornada || !$id_cargo
                || $sueldo === '' || !is_numeric($sueldo) || $fecha_ingreso === '') {
                $error = 'Completa todos los campos obligatorios (*) con valores válidos.';
            } else {
                $fotoFilename = $this->handlePhotoUpload($empleado['foto'] ?? null);

                $empleadoModel->update($id, [
                    'cedula' => $cedula,
                    'primer_nombre' => $p_nombre,
                    'segundo_nombre' => $s_nombre,
                    'primer_apellido' => $p_apellido,
                    'segundo_apellido' => $s_apellido,
                    'email' => $email,
                    'foto' => $fotoFilename,
                    'id_tipo_empleado' => $id_tipo,
                    'id_jornada' => $id_jornada,
                    'id_cargo' => $id_cargo,
                    'sueldo_divisa' => $sueldo,
                    'fecha_ingreso' => $fecha_ingreso,
                    'id_banco' => $id_banco,
                    'numero_cuenta' => $numero_cuenta,
                    'tipo_cuenta' => $tipo_cuenta
                ]);

                Audit::log('EMPLEADOS', 'MODIFICAR', "Actualizó los datos del empleado {$p_nombre} {$p_apellido} (C.I. {$cedula})", 'tb_empleado', $id, [
                    'cedula' => $cedula,
                    'nombre_completo' => "{$p_nombre} {$s_nombre} {$p_apellido} {$s_apellido}",
                    'cargo_id' => $id_cargo,
                    'sueldo_divisa' => $sueldo
                ]);

                $this->redirect('index.php?r=empleados&msg=' . urlencode('Empleado actualizado correctamente.'));
            }
            $empleado = array_merge($empleado, $_POST);
        }

        $this->render('empleados/editar', [
            'empleado' => $empleado,
            'tipos' => $empleadoModel->getTipos(),
            'jornadas' => $empleadoModel->getJornadas(),
            'cargos' => $empleadoModel->getCargos(),
            'cargoActual' => $empleadoModel->getCargoActual($id),
            'bancos' => $empleadoModel->getBancos(),
            'cuentaBancaria' => $empleadoModel->getCuentaBancaria($id),
            'error' => $error
        ], 'Editar empleado');
    }

    public function cambiarEstado(): void {
        $this->requireRole(['Administrador', 'Operador']);

        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            $empleadoModel = new EmpleadoModel();
            $emp = $empleadoModel->getById($id);
            $nuevoEstado = !empty($emp['estado']) ? 'Inactivado' : 'Activado';
            $empleadoModel->toggleEstado($id);
            Audit::log('EMPLEADOS', 'CAMBIAR_ESTADO', "Cambió el estado laboral del empleado ID {$id} (" . ($emp['primer_nombre'] ?? '') . " " . ($emp['primer_apellido'] ?? '') . ") a '{$nuevoEstado}'", 'tb_empleado', $id);
        }
        $this->redirect('index.php?r=empleados&msg=' . urlencode('Estado actualizado.'));
    }
}
