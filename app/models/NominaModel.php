<?php

require_once __DIR__ . '/../../core/Model.php';
require_once __DIR__ . '/EmpleadoModel.php';
require_once __DIR__ . '/PeriodoModel.php';
require_once __DIR__ . '/TasaModel.php';

class NominaModel extends Model {
    public function __construct() {
        parent::__construct();
        $this->ensureDetalleColumns();
    }

    private function ensureDetalleColumns(): void {
        try {
            $cols = $this->db->query("SHOW COLUMNS FROM tb_nomina_detalle LIKE 'dias_inasistencia'")->fetchAll();
            if (empty($cols)) {
                $this->db->exec("ALTER TABLE tb_nomina_detalle ADD COLUMN dias_inasistencia INT NOT NULL DEFAULT 0");
            }
        } catch (\Throwable $e) {
            // Silently ignore if already exists or table issue
        }
    }

    public function getById(int $idNomina): ?array {
        $stmt = $this->db->prepare("SELECT * FROM tb_nomina WHERE id_nomina = ?");
        $stmt->execute([$idNomina]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getByPeriodo(int $idPeriodo): ?array {
        $stmt = $this->db->prepare("SELECT * FROM tb_nomina WHERE id_periodo = ?");
        $stmt->execute([$idPeriodo]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getUltimaNominaGenerada(): ?array {
        $stmt = $this->db->query("SELECT * FROM tb_nomina ORDER BY id_nomina DESC LIMIT 1");
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function createNomina(int $idPeriodo, int $idUsuario, float $salarioMinimo, float $semanasIvss, float $factorSemanasMes, float $tasaSso, float $tasaRpe, float $tasaLph): int {
        $ins = $this->db->prepare("
            INSERT INTO tb_nomina (id_periodo, id_usuario_genera, salario_minimo_vigente, semanas_descontar_ivss, factor_semanas_mes, tasa_sso, tasa_rpe, tasa_lph, estado, fecha_generacion)
            VALUES (?,?,?,?,?,?,?,?,'Generada', NOW())
        ");
        $ins->execute([$idPeriodo, $idUsuario, $salarioMinimo, $semanasIvss, $factorSemanasMes, $tasaSso, $tasaRpe, $tasaLph]);
        $idNomina = (int)$this->db->lastInsertId();

        // Autocompletar trabajadores activos en la nómina
        $empleadoModel = new EmpleadoModel();
        $allEmpleados = $empleadoModel->getAll();
        $empleadosActivos = array_values(array_filter($allEmpleados, fn($e) => $e['estado'] == 1));

        $periodoModel = new PeriodoModel();
        $periodo = $periodoModel->getById($idPeriodo);
        $diasPeriodo = 0;

        if ($periodo) {
            $inicio = strtotime($periodo['fecha_inicio']);
            $fin = strtotime($periodo['fecha_fin']);
            $diasPeriodo = round(($fin - $inicio) / 86400) + 1;
        }

        $idEmpleados = array_column($empleadosActivos, 'id_empleado');
        $this->agregarTrabajadoresANomina($idNomina, $idEmpleados, $diasPeriodo);

        return $idNomina;
    }

    public function agregarTrabajadoresANomina(int $idNomina, array $idEmpleados, int $diasTrabajadosPorDefecto = 0): void {
        foreach ($idEmpleados as $idEmp) {
            $idEmp = (int)$idEmp;
            if ($idEmp > 0) {
                $stmt = $this->db->prepare("SELECT id_detalle FROM tb_nomina_detalle WHERE id_nomina = ? AND id_empleado = ?");
                $stmt->execute([$idNomina, $idEmp]);
                if (!$stmt->fetch()) {
                    $this->calcularYGuardarDetalle($idNomina, $idEmp, ['dias_trabajados' => $diasTrabajadosPorDefecto]);
                }
            }
        }
    }

    public function eliminarEmpleadoDeNomina(int $idNomina, int $idEmpleado): void {
        $stmt = $this->db->prepare("SELECT id_detalle FROM tb_nomina_detalle WHERE id_nomina = ? AND id_empleado = ?");
        $stmt->execute([$idNomina, $idEmpleado]);
        $row = $stmt->fetch();
        if ($row) {
            $id_detalle = $row['id_detalle'];
            $this->db->prepare("DELETE FROM tb_nomina_asignacion WHERE id_detalle = ?")->execute([$id_detalle]);
            $this->db->prepare("DELETE FROM tb_nomina_deduccion WHERE id_detalle = ?")->execute([$id_detalle]);
            $this->db->prepare("DELETE FROM tb_nomina_detalle WHERE id_detalle = ?")->execute([$id_detalle]);
        }
    }

    public function getTasasDeduccion(): array {
        $rows = $this->db->query("SELECT * FROM tb_concepto_deduccion")->fetchAll();
        $out = [];
        foreach ($rows as $r) { $out[$r['nombre']] = $r; }
        return $out;
    }

    public function getDetallesByNomina(int $idNomina): array {
        $stmt = $this->db->prepare("
            SELECT d.*, e.cedula, e.primer_nombre, e.segundo_nombre, e.primer_apellido, e.segundo_apellido, e.sueldo_divisa, e.foto,
                   c.nombre_cargo
            FROM tb_nomina_detalle d
            JOIN tb_empleado e ON e.id_empleado = d.id_empleado
            LEFT JOIN tb_cargo c ON c.id_cargo = d.id_cargo
            WHERE d.id_nomina = ?
            ORDER BY e.primer_nombre
        ");
        $stmt->execute([$idNomina]);
        return $stmt->fetchAll();
    }

    public function getAsignacionMonto(int $idDetalle, int $idConcepto): float {
        if (!$idDetalle) return 0.0;
        $s = $this->db->prepare("SELECT monto FROM tb_nomina_asignacion WHERE id_detalle=? AND id_concepto=?");
        $s->execute([$idDetalle, $idConcepto]);
        $r = $s->fetch();
        return (float)($r['monto'] ?? 0);
    }

    public function getAsignacionCantidad(int $idDetalle, int $idConcepto): float {
        if (!$idDetalle) return 0.0;
        $s = $this->db->prepare("SELECT cantidad FROM tb_nomina_asignacion WHERE id_detalle=? AND id_concepto=?");
        $s->execute([$idDetalle, $idConcepto]);
        $r = $s->fetch();
        return (float)($r['cantidad'] ?? 0);
    }

    public function getDeduccionMonto(int $idDetalle, int $idConcepto): float {
        if (!$idDetalle) return 0.0;
        $s = $this->db->prepare("SELECT monto FROM tb_nomina_deduccion WHERE id_detalle=? AND id_concepto=?");
        $s->execute([$idDetalle, $idConcepto]);
        $r = $s->fetch();
        return (float)($r['monto'] ?? 0);
    }

    public function getDetalleEmpleado(int $idNomina, int $idEmpleado): ?array {
        $stmt = $this->db->prepare("
            SELECT d.*, e.*, c.nombre_cargo, t.valor_tasa
            FROM tb_nomina_detalle d
            JOIN tb_empleado e ON e.id_empleado = d.id_empleado
            LEFT JOIN tb_cargo c ON c.id_cargo = d.id_cargo
            LEFT JOIN tb_tasa_cambio t ON t.id_tasa = d.id_tasa
            WHERE d.id_nomina = ? AND d.id_empleado = ?
        ");
        $stmt->execute([$idNomina, $idEmpleado]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getAsignacionesDetalle(int $idDetalle): array {
        $stmt = $this->db->prepare("
            SELECT a.*, c.nombre AS nombre_concepto
            FROM tb_nomina_asignacion a
            JOIN tb_concepto_asignacion c ON c.id_concepto = a.id_concepto
            WHERE a.id_detalle = ?
        ");
        $stmt->execute([$idDetalle]);
        return $stmt->fetchAll();
    }

    public function getDeduccionesDetalle(int $idDetalle): array {
        $stmt = $this->db->prepare("
            SELECT d.*, c.nombre
            FROM tb_nomina_deduccion d
            JOIN tb_concepto_deduccion c ON c.id_concepto = d.id_concepto
            WHERE d.id_detalle = ?
        ");
        $stmt->execute([$idDetalle]);
        return $stmt->fetchAll();
    }

    public function calcularYGuardarDetalle(int $idNomina, int $idEmpleado, array $datos): array {
        $nomina = $this->getById($idNomina);
        $empleadoModel = new EmpleadoModel();
        $tasaModel = new TasaModel();

        $empleado = $empleadoModel->getById($idEmpleado);
        $cargo = $empleadoModel->getCargoActual($idEmpleado);
        $tasas = $this->getTasasDeduccion();
        $tasaCambio = $tasaModel->getUltima();

        $dias_trabajados = (float)($datos['dias_trabajados'] ?? 0);
        $dias_inasistencia = (float)($datos['dias_inasistencia'] ?? 0);
        $horas_extra_qty = (float)($datos['horas_extra_qty'] ?? 0);
        $bono_nocturno_qty = (float)($datos['bono_nocturno_qty'] ?? 0);
        $feriados_qty = (float)($datos['feriados_qty'] ?? 0);

        $otras_asignaciones = (float)($datos['otras_asignaciones'] ?? 0);
        $prestamo = (float)($datos['prestamo'] ?? 0);
        $islr = (float)($datos['islr'] ?? 0);

        $sueldo_diario = round(($empleado['sueldo_divisa'] ?? 0) / 30, 4);
        $monto_dias_trabajados = round($sueldo_diario * $dias_trabajados, 2);

        // formulas based on Excel:
        // Bono Nocturno: (Sueldo Diario / 7.5) * 0.3 * Horas
        $bono_nocturno_monto = round(($sueldo_diario / 7.5) * 0.3 * $bono_nocturno_qty, 2);

        // Horas Extra: ((Sueldo Diario * 1.5) / 7.5) * Horas
        $horas_extra_monto = round((($sueldo_diario * 1.5) / 7.5) * $horas_extra_qty, 2);

        // Dias Feriados: (Sueldo Diario * 1.5) * Dias
        $feriados_monto = round(($sueldo_diario * 1.5) * $feriados_qty, 2);

        $total_asignaciones = round(
            $monto_dias_trabajados + $horas_extra_monto + $bono_nocturno_monto + $feriados_monto + $otras_asignaciones,
            2
        );

        require_once __DIR__ . '/ConfiguracionModel.php';
        $configModel = new ConfiguracionModel();
        $empConfig = $configModel->getConfiguracion();

        $semanas_anual = (float)($empConfig['semanas_factor_anual'] ?? 52);
        $factor_semanas_mes = $semanas_anual > 0 ? ($semanas_anual / 12) : 4.333333;
        $semanas_periodo = (float)($nomina['semanas_descontar_ivss'] ?? 2.0);
        if ($semanas_periodo <= 0) $semanas_periodo = 2.0;
        $prorrateo = $semanas_periodo / $factor_semanas_mes;

        // Tasas globales de ley configuradas a nivel de empresa
        $tasa_sso = (float)($empConfig['tasa_sso_trabajador'] ?? 0.0400);
        $tasa_rpe = (float)($empConfig['tasa_rpe_trabajador'] ?? 0.0050);
        $tasa_lph = (float)($empConfig['tasa_faov_trabajador'] ?? 0.0100);

        // Salario mínimo legal y topes de cotización (Art. 107 RGLSS: 1 a 5 Salarios Mínimos)
        $salarioMinimo = (float)($empConfig['salario_minimo_legal'] ?? 130.00);
        $topeSsoSalarios = (int)($empConfig['tope_sso_salarios'] ?? 5);
        $topeRpeSalarios = (int)($empConfig['tope_rpe_salarios'] ?? 5);

        // Sueldo mensual base del trabajador
        $sueldoMensual = (float)($empleado['sueldo_divisa'] ?? 0);

        // Base de cotización mensual acotada estrictamente entre mínimo (1 SM) y máximo (ej. 5 SM)
        $baseCotizableSso = max($salarioMinimo, min($sueldoMensual, $topeSsoSalarios * $salarioMinimo));
        $baseCotizableRpe = max($salarioMinimo, min($sueldoMensual, $topeRpeSalarios * $salarioMinimo));

        $sso = 0.0;
        $rpe = 0.0;
        if ($dias_trabajados > 0) {
            $sso = round($tasa_sso * $baseCotizableSso * $prorrateo, 2);
            $rpe = round($tasa_rpe * $baseCotizableRpe * $prorrateo, 2);
        }
        $lph = round($tasa_lph * max($salarioMinimo, $total_asignaciones), 2);
        $inasistencia_monto = round($sueldo_diario * $dias_inasistencia, 2);

        $total_deducciones = round($sso + $rpe + $lph + $prestamo + $inasistencia_monto + $islr, 2);
        $neto_pagar = round($total_asignaciones - $total_deducciones, 2);

        // upsert tb_nomina_detalle
        $stmt = $this->db->prepare("SELECT id_detalle FROM tb_nomina_detalle WHERE id_nomina = ? AND id_empleado = ?");
        $stmt->execute([$idNomina, $idEmpleado]);
        $existe = $stmt->fetch();

        if ($existe) {
            $id_detalle = $existe['id_detalle'];
            $upd = $this->db->prepare("
                UPDATE tb_nomina_detalle SET
                    id_cargo = ?, id_tasa = ?, sueldo_diario = ?, dias_trabajados = ?, dias_inasistencia = ?,
                    sueldo_bss = ?, total_asignaciones = ?, total_deducciones = ?, neto_pagar = ?
                WHERE id_detalle = ?
            ");
            $upd->execute([
                $cargo['id_cargo'] ?? null, $tasaCambio['id_tasa'] ?? null, $sueldo_diario, $dias_trabajados, $dias_inasistencia,
                $monto_dias_trabajados, $total_asignaciones, $total_deducciones, $neto_pagar, $id_detalle
            ]);
            $this->db->prepare("DELETE FROM tb_nomina_asignacion WHERE id_detalle = ?")->execute([$id_detalle]);
            $this->db->prepare("DELETE FROM tb_nomina_deduccion WHERE id_detalle = ?")->execute([$id_detalle]);
        } else {
            $ins = $this->db->prepare("
                INSERT INTO tb_nomina_detalle
                    (id_nomina, id_empleado, id_cargo, id_tasa, sueldo_diario, dias_trabajados, dias_inasistencia,
                     sueldo_bss, total_asignaciones, total_deducciones, neto_pagar)
                VALUES (?,?,?,?,?,?,?,?,?,?,?)
            ");
            $ins->execute([
                $idNomina, $idEmpleado, $cargo['id_cargo'] ?? null, $tasaCambio['id_tasa'] ?? null,
                $sueldo_diario, $dias_trabajados, $dias_inasistencia, $monto_dias_trabajados, $total_asignaciones, $total_deducciones, $neto_pagar
            ]);
            $id_detalle = (int)$this->db->lastInsertId();
        }

        // Asignaciones
        $asig = $this->db->prepare("INSERT INTO tb_nomina_asignacion (id_detalle, id_concepto, cantidad, monto) VALUES (?,?,?,?)");
        $asig->execute([$id_detalle, 1, $dias_trabajados, $monto_dias_trabajados]);
        if ($bono_nocturno_qty > 0 || $bono_nocturno_monto > 0) $asig->execute([$id_detalle, 2, $bono_nocturno_qty, $bono_nocturno_monto]);
        if ($horas_extra_qty > 0 || $horas_extra_monto > 0)   $asig->execute([$id_detalle, 3, $horas_extra_qty, $horas_extra_monto]);
        if ($feriados_qty > 0 || $feriados_monto > 0)      $asig->execute([$id_detalle, 4, $feriados_qty, $feriados_monto]);

        // Deducciones
        $ded = $this->db->prepare("INSERT INTO tb_nomina_deduccion (id_detalle, id_concepto, porcentaje_aplicado, monto) VALUES (?,?,?,?)");
        $ded->execute([$id_detalle, 1, $tasa_sso, $sso]);
        $ded->execute([$id_detalle, 2, $tasa_rpe, $rpe]);
        $ded->execute([$id_detalle, 3, $tasa_lph, $lph]);
        if ($prestamo > 0) $ded->execute([$id_detalle, 4, null, $prestamo]);
        if ($inasistencia_monto > 0) $ded->execute([$id_detalle, 5, null, $inasistencia_monto]);
        if ($islr > 0) $ded->execute([$id_detalle, 6, null, $islr]);

        return [
            'sueldo_diario' => $sueldo_diario, 'monto_dias_trabajados' => $monto_dias_trabajados,
            'total_asignaciones' => $total_asignaciones, 'sso' => $sso, 'rpe' => $rpe, 'lph' => $lph,
            'inasistencia_monto' => $inasistencia_monto, 'total_deducciones' => $total_deducciones,
            'neto_pagar' => $neto_pagar,
        ];
    }

    public function actualizarTasasLey(int $idNomina, float $tasa_sso, float $tasa_rpe, float $tasa_lph): bool {
        $stmt = $this->db->prepare("UPDATE tb_nomina SET tasa_sso = ?, tasa_rpe = ?, tasa_lph = ? WHERE id_nomina = ?");
        $stmt->execute([$tasa_sso, $tasa_rpe, $tasa_lph, $idNomina]);

        $detalles = $this->getDetallesByNomina($idNomina);
        foreach ($detalles as $d) {
            $idDetalle = (int)$d['id_detalle'];
            $horas_nocturnas = $this->getAsignacionCantidad($idDetalle, 2);
            $horas_extra = $this->getAsignacionCantidad($idDetalle, 3);
            $feriados = $this->getAsignacionCantidad($idDetalle, 4);
            $otras_asig = $this->getAsignacionMonto($idDetalle, 5);
            $prestamo = $this->getDeduccionMonto($idDetalle, 4);
            $islr = $this->getDeduccionMonto($idDetalle, 6);

            $this->calcularYGuardarDetalle($idNomina, (int)$d['id_empleado'], [
                'dias_trabajados' => (float)$d['dias_trabajados'],
                'dias_inasistencia' => (float)$d['dias_inasistencia'],
                'bono_nocturno_qty' => $horas_nocturnas,
                'horas_extra_qty' => $horas_extra,
                'feriados_qty' => $feriados,
                'otras_asignaciones' => $otras_asig,
                'prestamo' => $prestamo,
                'islr' => $islr
            ]);
        }
        return true;
    }

    public function getTotalNominasCount(): int {
        $s = $this->db->query("SELECT COUNT(*) FROM tb_nomina");
        return (int)$s->fetchColumn();
    }

    public function getMontoTotalHistorico(): float {
        $s = $this->db->query("SELECT SUM(neto_pagar) FROM tb_nomina_detalle");
        return (float)($s->fetchColumn() ?: 0.0);
    }

    public function getMontoUltimaNomina(): float {
        $s = $this->db->query("
            SELECT SUM(d.neto_pagar) 
            FROM tb_nomina_detalle d
            WHERE d.id_nomina = (SELECT id_nomina FROM tb_nomina ORDER BY id_nomina DESC LIMIT 1)
        ");
        return (float)($s->fetchColumn() ?: 0.0);
    }

    public function getHistoricoNominasParaGrafico(int $limite = 6): array {
        $stmt = $this->db->prepare("
            SELECT 
                n.id_nomina,
                n.fecha_generacion,
                p.id_periodo,
                p.tipo_periodo,
                p.fecha_inicio,
                p.fecha_fin,
                p.anio,
                p.mes,
                p.numero_quincena,
                COALESCE(SUM(d.total_asignaciones), 0) AS total_asignaciones,
                COALESCE(SUM(d.total_deducciones), 0) AS total_deducciones,
                COALESCE(SUM(d.neto_pagar), 0) AS total_neto,
                COUNT(d.id_detalle) AS total_empleados
            FROM tb_nomina n
            JOIN tb_periodo_nomina p ON p.id_periodo = n.id_periodo
            LEFT JOIN tb_nomina_detalle d ON d.id_nomina = n.id_nomina
            GROUP BY n.id_nomina, p.id_periodo
            ORDER BY n.id_nomina DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        return array_reverse($rows);
    }

    public function getDistribucionPorCargo(): array {
        $sql = "
            SELECT 
                c.id_cargo,
                c.nombre_cargo,
                COUNT(DISTINCT e.id_empleado) AS total_empleados,
                COALESCE(SUM(e.sueldo_divisa), 0) AS total_presupuesto_usd
            FROM tb_cargo c
            JOIN tb_empleado_cargo ec ON ec.id_cargo = c.id_cargo AND ec.es_actual = 1
            JOIN tb_empleado e ON e.id_empleado = ec.id_empleado AND e.estado = 1
            GROUP BY c.id_cargo, c.nombre_cargo
            ORDER BY total_presupuesto_usd DESC
        ";
        return $this->db->query($sql)->fetchAll();
    }

    public function getResumenFinancieroUltimaNomina(): array {
        $stmt = $this->db->query("
            SELECT 
                COALESCE(SUM(d.total_asignaciones), 0) AS asignaciones,
                COALESCE(SUM(d.total_deducciones), 0) AS deducciones,
                COALESCE(SUM(d.neto_pagar), 0) AS neto,
                COALESCE(SUM(d.sueldo_bss), 0) AS neto_bs,
                COUNT(d.id_detalle) AS trabajadores_procesados
            FROM tb_nomina_detalle d
            WHERE d.id_nomina = (SELECT id_nomina FROM tb_nomina ORDER BY id_nomina DESC LIMIT 1)
        ");
        $res = $stmt->fetch();
        if (!$res) {
            return [
                'asignaciones' => 0.0,
                'deducciones' => 0.0,
                'neto' => 0.0,
                'neto_bs' => 0.0,
                'trabajadores_procesados' => 0
            ];
        }
        return [
            'asignaciones' => (float)$res['asignaciones'],
            'deducciones' => (float)$res['deducciones'],
            'neto' => (float)$res['neto'],
            'neto_bs' => (float)$res['neto_bs'],
            'trabajadores_procesados' => (int)$res['trabajadores_procesados']
        ];
    }

    public function recalcularNominaCompleta(int $idNomina): void {
        $detalles = $this->getDetallesByNomina($idNomina);
        foreach ($detalles as $d) {
            $idDetalle = (int)$d['id_detalle'];
            $idEmpleado = (int)$d['id_empleado'];
            $inputs = [
                'dias_trabajados' => $d['dias_trabajados'],
                'dias_inasistencia' => $d['dias_inasistencia'],
                'horas_extra_qty' => $this->getAsignacionCantidad($idDetalle, 3),
                'bono_nocturno_qty' => $this->getAsignacionCantidad($idDetalle, 2),
                'feriados_qty' => $this->getAsignacionCantidad($idDetalle, 4),
                'otras_asignaciones' => $this->getAsignacionMonto($idDetalle, 5),
                'prestamo' => $this->getDeduccionMonto($idDetalle, 4),
                'islr' => $this->getDeduccionMonto($idDetalle, 6)
            ];
            $this->calcularYGuardarDetalle($idNomina, $idEmpleado, $inputs);
        }
    }
}
