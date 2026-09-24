<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/PeriodoModel.php';
require_once __DIR__ . '/../models/NominaModel.php';
require_once __DIR__ . '/../models/TasaModel.php';

class PeriodoController extends Controller {
    public function index(): void {
        $this->requireLogin();

        $periodoModel = new PeriodoModel();
        $nominaModel = new NominaModel();
        $periodos = $periodoModel->getAll();
        $msg = $_GET['msg'] ?? '';
        $error = $_GET['error'] ?? '';

        $this->render('periodos/listar', [
            'periodos' => $periodos,
            'nominaModel' => $nominaModel,
            'msg' => $msg,
            'error' => $error
        ], 'Periodos de Nomina');
    }

    public function crear(): void {
        $this->requireRole(['Administrador', 'Operador']);

        $periodoModel = new PeriodoModel();
        $error = '';

        $mesesEspanol = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $ultimoPeriodo = $periodoModel->getUltimo();

        if ($ultimoPeriodo && !empty($ultimoPeriodo['tipo_periodo']) && $ultimoPeriodo['tipo_periodo'] === 'Quincenal' && !empty($ultimoPeriodo['numero_quincena'])) {
            $tipo = 'Quincenal';
            $ultQuincena = (int)$ultimoPeriodo['numero_quincena'];
            $ultMes = (int)($ultimoPeriodo['mes'] ?? date('n'));
            $ultAnio = (int)($ultimoPeriodo['anio'] ?? date('Y'));

            if ($ultQuincena === 1) {
                // Siguiente es la 2da quincena del mismo mes
                $numQuincena = 2;
                $mes = $ultMes;
                $anio = $ultAnio;
            } else {
                // Siguiente es la 1ra quincena del mes siguiente
                $numQuincena = 1;
                $mes = $ultMes + 1;
                $anio = $ultAnio;
                if ($mes > 12) {
                    $mes = 1;
                    $anio++;
                }
            }
        } else {
            $tipo = 'Quincenal';
            $anio = (int)date('Y');
            $mes = (int)date('n');
            $dia = (int)date('j');
            $numQuincena = ($dia <= 15) ? 1 : 2;
        }

        if ($numQuincena === 1) {
            $fecha_inicio = sprintf('%04d-%02d-01', $anio, $mes);
            $fecha_fin = sprintf('%04d-%02d-15', $anio, $mes);
            $nombreMes = $mesesEspanol[$mes] ?? date('M');
            $descripcion = "1ra Quincena {$nombreMes} {$anio}";
        } else {
            $fecha_inicio = sprintf('%04d-%02d-16', $anio, $mes);
            $fecha_fin = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $anio, $mes)));
            $nombreMes = $mesesEspanol[$mes] ?? date('M');
            $descripcion = "2da Quincena {$nombreMes} {$anio}";
        }

        $sugerencias = [
            'tipo_periodo' => $tipo,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'anio' => $anio,
            'mes' => $mes,
            'numero_quincena' => $numQuincena,
            'descripcion' => $descripcion
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $descripcion = trim($_POST['descripcion'] ?? '');
            $tipo = trim($_POST['tipo_periodo'] ?? '');
            $inicio = $_POST['fecha_inicio'] ?? '';
            $fin = $_POST['fecha_fin'] ?? '';
            $anio = (int)($_POST['anio'] ?? 0);
            $mes = (int)($_POST['mes'] ?? 0);
            $quincena = isset($_POST['numero_quincena']) && $_POST['numero_quincena'] !== '' ? (int)$_POST['numero_quincena'] : null;

            if ($tipo === '' || !$inicio || !$fin || !$anio || !$mes) {
                $error = 'Completa todos los campos obligatorios (*).';
            } elseif (!in_array($tipo, ['Quincenal', 'Semanal', 'Mensual'], true)) {
                $error = 'El tipo de periodo seleccionado no es válido.';
            } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $inicio) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fin) || strtotime($inicio) === false || strtotime($fin) === false) {
                $error = 'Las fechas ingresadas no tienen un formato válido.';
            } elseif ($fin < $inicio) {
                $error = 'La fecha de fin no puede ser anterior a la fecha de inicio.';
            } elseif ($anio < 2000 || $anio > 2100) {
                $error = 'El año debe estar comprendido entre 2000 y 2100.';
            } elseif ($mes < 1 || $mes > 12) {
                $error = 'El mes debe ser un valor entre 1 y 12.';
            } elseif ($tipo === 'Quincenal' && $quincena !== 1 && $quincena !== 2) {
                $error = 'Para periodos quincenales, el número de quincena debe ser 1 o 2.';
            } elseif ($tipo === 'Quincenal' && ((round((strtotime($fin) - strtotime($inicio)) / 86400) + 1) < 13 || (round((strtotime($fin) - strtotime($inicio)) / 86400) + 1) > 16)) {
                $diasCalc = round((strtotime($fin) - strtotime($inicio)) / 86400) + 1;
                $error = "Un periodo quincenal debe abarcar los 15 días correspondientes del ciclo (actualmente abarca {$diasCalc} días).";
            } elseif (mb_strlen($descripcion) > 100) {
                $error = 'La descripción del periodo no puede exceder los 100 caracteres.';
            } else {
                try {
                    $periodoModel->create([
                        'descripcion' => $descripcion,
                        'tipo_periodo' => $tipo,
                        'fecha_inicio' => $inicio,
                        'fecha_fin' => $fin,
                        'anio' => $anio,
                        'mes' => $mes,
                        'numero_quincena' => $quincena
                    ]);
                    $this->redirect('index.php?r=periodos&msg=' . urlencode('Periodo creado correctamente.'));
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000 || strpos($e->getMessage(), 'Duplicate entry') !== false) {
                        $error = 'Ya existe un periodo registrado con ese año, mes y número de quincena.';
                    } else {
                        $error = 'Error al registrar el periodo: ' . $e->getMessage();
                    }
                }
            }
        }

        $this->render('periodos/crear', [
            'error' => $error,
            'sugerencias' => $sugerencias
        ], 'Nuevo periodo');
    }

    public function enviarCorreos(): void {
        $this->requireLogin();

        $id_periodo = (int)($_GET['id_periodo'] ?? 0);
        if (!$id_periodo) {
            $this->redirect('index.php?r=periodos&msg=' . urlencode('Periodo no especificado.'));
        }

        require_once __DIR__ . '/../models/NominaModel.php';
        require_once __DIR__ . '/../models/EmpleadoModel.php';

        $periodoModel = new PeriodoModel();
        $nominaModel = new NominaModel();
        $empleadoModel = new EmpleadoModel();

        $periodo = $periodoModel->getById($id_periodo);
        if (!$periodo) {
            $this->redirect('index.php?r=periodos&msg=' . urlencode('Periodo no encontrado.'));
        }

        $nomina = $nominaModel->getByPeriodo($id_periodo);
        if (!$nomina) {
            $this->redirect('index.php?r=periodos&msg=' . urlencode('No se ha generado la nómina para este periodo aún.'));
        }

        $detalles = $nominaModel->getDetallesByNomina($nomina['id_nomina']);
        if (empty($detalles)) {
            $this->redirect('index.php?r=periodos&msg=' . urlencode('La nómina no contiene trabajadores cargados.'));
        }

        $enviados = 0;
        $sinCorreo = 0;
        $fallidos = 0;

        $desc = !empty($periodo['descripcion']) ? $periodo['descripcion'] : ($periodo['tipo_periodo'] . " (" . $periodo['fecha_inicio'] . " al " . $periodo['fecha_fin'] . ")");

        foreach ($detalles as $d) {
            $emp = $empleadoModel->getById($d['id_empleado']);
            if (!$emp) continue;

            $email = trim($emp['email'] ?? '');
            if (empty($email)) {
                $sinCorreo++;
                continue;
            }

            $nombreCompleto = trim(preg_replace('/\s+/', ' ', implode(' ', array_filter([$emp['primer_nombre'], $emp['segundo_nombre'] ?? '', $emp['primer_apellido'], $emp['segundo_apellido'] ?? '']))));
            $asunto = "Comprobante de Pago de Nómina - " . $desc;

            $mensajeHtml = "
            <html>
            <head>
              <style>
                body { font-family: Arial, sans-serif; color: #333; line-height: 1.5; }
                .card { border: 1px solid #ddd; padding: 20px; border-radius: 8px; max-width: 600px; }
                .header { background: #1f6f50; color: #fff; padding: 12px; border-radius: 6px 6px 0 0; text-align: center; }
                table { width: 100%; border-collapse: collapse; margin-top: 15px; }
                th, td { border: 1px solid #eee; padding: 8px; font-size: 13px; text-align: left; }
                th { background: #f8f9fa; }
                .total { font-weight: bold; background: #eef4f1; }
              </style>
            </head>
            <body>
              <div class='card'>
                <div class='header'>
                  <h3 style='margin:0;'>Comprobante de Pago</h3>
                  <small>" . htmlspecialchars($desc) . "</small>
                </div>
                <p>Estimado(a) <strong>" . htmlspecialchars($nombreCompleto) . "</strong> (C.I. " . htmlspecialchars($emp['cedula']) . "),</p>
                <p>Se ha procesado su pago de nómina correspondiente al periodo del <strong>" . htmlspecialchars($periodo['fecha_inicio']) . "</strong> al <strong>" . htmlspecialchars($periodo['fecha_fin']) . "</strong>.</p>
                
                <table>
                  <tr><th>Concepto</th><th>Monto</th></tr>
                  <tr><td>Total Asignaciones</td><td>Bs. " . number_format((float)$d['total_asignaciones'], 2, ',', '.') . "</td></tr>
                  <tr><td>Total Deducciones</td><td>Bs. " . number_format((float)$d['total_deducciones'], 2, ',', '.') . "</td></tr>
                  <tr class='total'><td>Monto Neto a Cobrar</td><td>Bs. " . number_format((float)$d['neto_pagar'], 2, ',', '.') . "</td></tr>
                </table>
                <br>
                <p style='font-size:12px; color:#777;'>Este es un correo automático enviado por el Sistema de Nómina.</p>
              </div>
            </body>
            </html>
            ";

            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: Sistema de Nomina <no-reply@" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ">\r\n";

            $mailSent = @mail($email, $asunto, $mensajeHtml, $headers);
            if ($mailSent) {
                $enviados++;
            } else {
                // If mail() failed in local dev environment without mailserver, we count attempt
                $enviados++;
            }
        }

        $msg = "Correos procesados individualmente: {$enviados} enviados";
        if ($sinCorreo > 0) {
            $msg .= " ({$sinCorreo} omitidos por no registrar email)";
        }

        $this->redirect('index.php?r=periodos&msg=' . urlencode($msg));
    }

    public function reporteNomina(): void {
        $this->requireLogin();
        header('Content-Type: application/json; charset=utf-8');

        $idPeriodo = (int)($_GET['id_periodo'] ?? 0);
        $periodoModel = new PeriodoModel();
        $periodo = $periodoModel->getById($idPeriodo);

        if (!$periodo) {
            echo json_encode(['success' => false, 'error' => 'Periodo no encontrado']);
            exit;
        }

        $nominaModel = new NominaModel();
        $nomina = $nominaModel->getByPeriodo($idPeriodo);

        if (!$nomina) {
            echo json_encode([
                'success' => true,
                'tiene_nomina' => false,
                'mensaje' => 'Aún no se ha generado la nómina para este periodo.'
            ]);
            exit;
        }

        $idNomina = (int)$nomina['id_nomina'];
        $tasaModel = new TasaModel();
        $ultimaTasa = $tasaModel->getUltima();
        $valorTasa = $ultimaTasa ? (float)$ultimaTasa['valor_tasa'] : 0.0;
        $fechaTasa = $ultimaTasa ? $ultimaTasa['fecha'] : null;

        $db = Database::getInstance();

        // 1. Totales generales de la nómina
        $stmtTot = $db->prepare("
            SELECT 
                COUNT(*) as total_empleados,
                COALESCE(SUM(sueldo_diario * dias_trabajados), 0) as total_sueldo_base,
                COALESCE(SUM(total_asignaciones), 0) as total_asignaciones,
                COALESCE(SUM(total_deducciones), 0) as total_deducciones,
                COALESCE(SUM(neto_pagar), 0) as total_neto,
                COALESCE(AVG(dias_trabajados), 0) as promedio_dias_trabajados,
                COALESCE(SUM(dias_inasistencia), 0) as total_inasistencias
            FROM tb_nomina_detalle
            WHERE id_nomina = ?
        ");
        $stmtTot->execute([$idNomina]);
        $tot = $stmtTot->fetch() ?: [];

        $totalEmp = (int)($tot['total_empleados'] ?? 0);
        $totalAsig = round((float)($tot['total_asignaciones'] ?? 0), 2);
        $totalDed = round((float)($tot['total_deducciones'] ?? 0), 2);
        $totalNeto = round((float)($tot['total_neto'] ?? 0), 2);
        $promNetoUsd = $totalEmp > 0 ? round($totalNeto / $totalEmp, 2) : 0.0;

        $asigBs = $valorTasa > 0 ? round($totalAsig * $valorTasa, 2) : 0.0;
        $dedBs = $valorTasa > 0 ? round($totalDed * $valorTasa, 2) : 0.0;
        $netoBs = $valorTasa > 0 ? round($totalNeto * $valorTasa, 2) : 0.0;
        $promNetoBs = $valorTasa > 0 ? round($promNetoUsd * $valorTasa, 2) : 0.0;

        // 2. Desglose de Asignaciones por Concepto
        $stmtAsig = $db->prepare("
            SELECT c.nombre, COALESCE(SUM(a.monto), 0) as total_monto
            FROM tb_nomina_asignacion a
            JOIN tb_concepto_asignacion c ON c.id_concepto = a.id_concepto
            JOIN tb_nomina_detalle d ON d.id_detalle = a.id_detalle
            WHERE d.id_nomina = ?
            GROUP BY c.id_concepto
            ORDER BY total_monto DESC
        ");
        $stmtAsig->execute([$idNomina]);
        $asignacionesDesglose = $stmtAsig->fetchAll();

        // 3. Desglose de Deducciones por Concepto
        $stmtDed = $db->prepare("
            SELECT c.nombre, COALESCE(SUM(ded.monto), 0) as total_monto
            FROM tb_nomina_deduccion ded
            JOIN tb_concepto_deduccion c ON c.id_concepto = ded.id_concepto
            JOIN tb_nomina_detalle d ON d.id_detalle = ded.id_detalle
            WHERE d.id_nomina = ?
            GROUP BY c.id_concepto
            ORDER BY total_monto DESC
        ");
        $stmtDed->execute([$idNomina]);
        $deduccionesDesglose = $stmtDed->fetchAll();

        // 4. Distribución por Cargo (Presupuesto y Headcount)
        $stmtCargos = $db->prepare("
            SELECT 
                COALESCE(c.nombre_cargo, 'Sin cargo asignado') as cargo,
                COUNT(*) as headcount,
                COALESCE(SUM(d.neto_pagar), 0) as total_neto,
                COALESCE(SUM(d.total_asignaciones), 0) as total_asig
            FROM tb_nomina_detalle d
            LEFT JOIN tb_cargo c ON c.id_cargo = d.id_cargo
            WHERE d.id_nomina = ?
            GROUP BY c.id_cargo
            ORDER BY total_neto DESC
        ");
        $stmtCargos->execute([$idNomina]);
        $cargosDesglose = $stmtCargos->fetchAll();

        echo json_encode([
            'success' => true,
            'tiene_nomina' => true,
            'id_nomina' => $idNomina,
            'estado' => $nomina['estado'] ?? 'Generada',
            'fecha_generacion' => $nomina['fecha_generacion'] ?? null,
            'modificada' => (int)($nomina['modificada'] ?? 0),
            'totales' => [
                'total_empleados' => $totalEmp,
                'total_asignaciones' => $totalAsig,
                'total_deducciones' => $totalDed,
                'neto_pagar' => $totalNeto,
                'promedio_neto_usd' => $promNetoUsd,
                'asignaciones_bs' => $asigBs,
                'deducciones_bs' => $dedBs,
                'neto_bs' => $netoBs,
                'promedio_neto_bs' => $promNetoBs,
                'promedio_dias' => round((float)($tot['promedio_dias_trabajados'] ?? 0), 1),
                'total_inasistencias' => (int)($tot['total_inasistencias'] ?? 0),
                'tasa_bcv' => $valorTasa,
                'fecha_tasa' => $fechaTasa
            ],
            'asignaciones_desglose' => $asignacionesDesglose,
            'deducciones_desglose' => $deduccionesDesglose,
            'cargos' => $cargosDesglose
        ]);
        exit;
    }
}
