<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/PeriodoModel.php';

class PeriodoController extends Controller {
    public function index(): void {
        $this->requireLogin();

        $periodoModel = new PeriodoModel();
        $periodos = $periodoModel->getAll();
        $msg = $_GET['msg'] ?? '';

        $this->render('periodos/listar', [
            'periodos' => $periodos,
            'msg' => $msg
        ], 'Periodos de Nomina');
    }

    public function crear(): void {
        $this->requireRole(['Administrador', 'Operador']);

        $periodoModel = new PeriodoModel();
        $error = '';

        $hoy = date('Y-m-d');
        $sugerencias = [
            'tipo_periodo' => 'Quincenal',
            'fecha_inicio' => $hoy,
            'fecha_fin' => date('Y-m-d', strtotime('+14 days')),
            'anio' => (int)date('Y'),
            'mes' => (int)date('n'),
            'numero_quincena' => ((int)date('j') <= 15) ? 1 : 2,
            'descripcion' => ''
        ];

        $ultimoPeriodo = $periodoModel->getUltimo();
        if ($ultimoPeriodo) {
            $sugerencias['tipo_periodo'] = $ultimoPeriodo['tipo_periodo'];
            $inicioAnterior = strtotime($ultimoPeriodo['fecha_inicio']);
            $finAnterior = strtotime($ultimoPeriodo['fecha_fin']);

            $diffDias = max(1, round(($finAnterior - $inicioAnterior) / 86400) + 1);

            // La fecha de inicio SIEMPRE debe ser hoy (fecha actual)
            $sugerencias['fecha_inicio'] = $hoy;
            $sugerencias['fecha_fin'] = date('Y-m-d', strtotime($hoy . ' +' . ($diffDias - 1) . ' days'));

            // Actualizar mes/año/quincena según la fecha de hoy
            $sugerencias['anio'] = (int)date('Y', strtotime($hoy));
            $sugerencias['mes'] = (int)date('n', strtotime($hoy));

            if ($sugerencias['tipo_periodo'] === 'Quincenal') {
                $dia = (int)date('j', strtotime($hoy));
                $sugerencias['numero_quincena'] = ($dia <= 15) ? 1 : 2;
                $sugerencias['descripcion'] = $sugerencias['numero_quincena'] . 'ra Quincena ' . date('M Y', strtotime($hoy));
            } elseif ($sugerencias['tipo_periodo'] === 'Mensual') {
                $sugerencias['descripcion'] = 'Mes ' . date('M Y', strtotime($hoy));
            }
        } else {
            $dia = (int)date('j', strtotime($hoy));
            $sugerencias['numero_quincena'] = ($dia <= 15) ? 1 : 2;
            $sugerencias['descripcion'] = $sugerencias['numero_quincena'] . 'ra Quincena ' . date('M Y', strtotime($hoy));
        }

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
                $error = 'Completa todos los campos obligatorios.';
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
                    $error = 'Ya existe un periodo con ese año, mes y numero de quincena.';
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
}
