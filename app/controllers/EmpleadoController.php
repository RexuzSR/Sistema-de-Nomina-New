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

    private function validateEmpleadoInput(array $data, EmpleadoModel $model, int $excludeId = 0): ?string {
        $cedula = trim($data['cedula'] ?? '');
        $p_nombre = trim($data['primer_nombre'] ?? '');
        $s_nombre = trim($data['segundo_nombre'] ?? '');
        $p_apellido = trim($data['primer_apellido'] ?? '');
        $s_apellido = trim($data['segundo_apellido'] ?? '');
        $id_tipo = (int)($data['id_tipo_empleado'] ?? 0);
        $id_jornada = (int)($data['id_jornada'] ?? 0);
        $id_cargo = (int)($data['id_cargo'] ?? 0);
        $sueldo = $data['sueldo_divisa'] ?? '';
        $fecha_ingreso = trim($data['fecha_ingreso'] ?? '');
        $email = trim($data['email'] ?? '');
        $id_banco = (int)($data['id_banco'] ?? 0);
        $numero_cuenta = trim($data['numero_cuenta'] ?? '');

        // 1. Campos obligatorios principales
        if ($cedula === '' || $p_nombre === '' || $p_apellido === '' || !$id_tipo || !$id_jornada || !$id_cargo
            || $sueldo === '' || !is_numeric($sueldo) || $fecha_ingreso === '') {
            return 'Completa todos los campos obligatorios (*) con valores válidos.';
        }

        // 2. Validación de Cédula (únicamente números, entre 6 y 11 dígitos)
        if (!preg_match('/^[0-9]{6,11}$/', $cedula)) {
            return 'La cédula de identidad solo debe contener números (entre 6 y 11 dígitos, sin letras ni símbolos).';
        }
        if ($model->isCedulaTaken($cedula, $excludeId)) {
            return "La cédula '{$cedula}' ya se encuentra registrada para otro empleado.";
        }

        // 3. Validación de Fecha de Ingreso (no futura)
        $today = date('Y-m-d');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_ingreso) || strtotime($fecha_ingreso) === false) {
            return 'La fecha de ingreso especificada no es una fecha válida.';
        }
        if ($fecha_ingreso > $today) {
            return 'La fecha de ingreso no puede ser una fecha futura (máximo hoy ' . date('d/m/Y') . ').';
        }
        if ($fecha_ingreso < '1950-01-01') {
            return 'La fecha de ingreso no puede ser anterior al año 1950.';
        }

        // 4. Nombres y Apellidos (sin números, max 50 caracteres)
        $nameRegex = "/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s'-]+$/u";
        if (mb_strlen($p_nombre) > 50 || mb_strlen($p_apellido) > 50 || mb_strlen($s_nombre) > 50 || mb_strlen($s_apellido) > 50) {
            return 'Los nombres y apellidos no pueden exceder los 50 caracteres.';
        }
        if (mb_strlen($p_nombre) < 2 || mb_strlen($p_apellido) < 2) {
            return 'El primer nombre y primer apellido deben tener al menos 2 caracteres.';
        }
        if (!preg_match($nameRegex, $p_nombre)) {
            return 'El primer nombre solo debe contener letras, sin números ni caracteres especiales.';
        }
        if (!preg_match($nameRegex, $p_apellido)) {
            return 'El primer apellido solo debe contener letras, sin números ni caracteres especiales.';
        }
        if ($s_nombre !== '' && !preg_match($nameRegex, $s_nombre)) {
            return 'El segundo nombre solo debe contener letras, sin números ni caracteres especiales.';
        }
        if ($s_apellido !== '' && !preg_match($nameRegex, $s_apellido)) {
            return 'El segundo apellido solo debe contener letras, sin números ni caracteres especiales.';
        }

        // 5. Sueldo en Divisas
        $sueldoVal = (float)$sueldo;
        if ($sueldoVal <= 0) {
            return 'El sueldo en divisas debe ser un monto mayor a cero.';
        }
        if ($sueldoVal > 1000000) {
            return 'El sueldo en divisas no puede exceder $1,000,000.00.';
        }

        // 6. Correo Electrónico
        if ($email !== '') {
            if (mb_strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return 'El correo electrónico ingresado no tiene un formato válido o excede los 100 caracteres.';
            }
        }

        // 7. Cuenta Bancaria (exactamente 20 dígitos numéricos)
        if (!empty($id_banco) && $numero_cuenta === '') {
            return 'Si seleccionas una entidad bancaria, debes ingresar el número de cuenta de 20 dígitos.';
        }
        if (empty($id_banco) && $numero_cuenta !== '') {
            return 'Debes seleccionar la entidad bancaria a la que pertenece el número de cuenta.';
        }
        if ($numero_cuenta !== '') {
            if (!preg_match('/^[0-9]{20}$/', $numero_cuenta)) {
                return 'El número de cuenta bancaria debe contener exactamente 20 dígitos numéricos (actualmente tiene ' . strlen($numero_cuenta) . ').';
            }
        }

        return null;
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
            $fecha_ingreso = trim($_POST['fecha_ingreso'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $id_banco = (int)($_POST['id_banco'] ?? 0);
            $numero_cuenta = trim($_POST['numero_cuenta'] ?? '');
            $tipo_cuenta = trim($_POST['tipo_cuenta'] ?? 'Corriente');

            $validationError = $this->validateEmpleadoInput($_POST, $empleadoModel, 0);
            if ($validationError !== null) {
                $error = $validationError;
            } else {
                try {
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
                } catch (\PDOException $e) {
                    if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
                        $error = "La cédula '{$cedula}' ya se encuentra registrada en el sistema.";
                    } else {
                        $error = 'Error en base de datos al registrar: ' . $e->getMessage();
                    }
                }
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
            $fecha_ingreso = trim($_POST['fecha_ingreso'] ?? '');
            $id_banco = (int)($_POST['id_banco'] ?? 0);
            $numero_cuenta = trim($_POST['numero_cuenta'] ?? '');
            $tipo_cuenta = trim($_POST['tipo_cuenta'] ?? 'Corriente');

            $validationError = $this->validateEmpleadoInput($_POST, $empleadoModel, $id);
            if ($validationError !== null) {
                $error = $validationError;
            } else {
                try {
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
                } catch (\PDOException $e) {
                    if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
                        $error = "La cédula '{$cedula}' ya se encuentra registrada en el sistema.";
                    } else {
                        $error = 'Error en base de datos al actualizar: ' . $e->getMessage();
                    }
                }
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

    public function verificarCedula(): void {
        $this->requireLogin();
        header('Content-Type: application/json; charset=utf-8');

        $cedula = preg_replace('/\D/', '', trim($_GET['cedula'] ?? $_POST['cedula'] ?? ''));
        $excludeId = (int)($_GET['exclude_id'] ?? $_POST['exclude_id'] ?? 0);

        if ($cedula === '') {
            echo json_encode(['taken' => false]);
            exit;
        }

        $empleadoModel = new EmpleadoModel();
        $taken = $empleadoModel->isCedulaTaken($cedula, $excludeId);
        $emp = null;
        if ($taken) {
            $emp = $empleadoModel->getByCedula($cedula);
        }

        echo json_encode([
            'taken' => $taken,
            'nombre' => $emp ? trim(($emp['primer_nombre'] ?? '') . ' ' . ($emp['primer_apellido'] ?? '')) : '',
            'estado' => $emp ? ($emp['estado'] ? 'Activo' : 'Inactivo') : ''
        ]);
        exit;
    }
}
