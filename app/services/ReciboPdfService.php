<?php

require_once __DIR__ . '/../../core/fpdf/fpdf.php';

class ReciboPdfService {

    /**
     * Convierte texto a codificación ISO-8859-1 para compatibilidad con FPDF.
     */
    private static function text(string $str): string {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $str) ?: utf8_decode($str);
    }

    /**
     * Formatea un número monetario a estándar venezolano (ej: 1.250,50)
     */
    private static function money(float $amount): string {
        return number_format($amount, 2, ',', '.');
    }

    /**
     * Dibuja un solo recibo en la posición Y indicada dentro de una página.
     * Si $startY es 10, es la mitad superior. Si es 145, es la mitad inferior.
     * Retorna la altura final ocupada.
     */
    public static function dibujarRecibo(
        FPDF $pdf, 
        float $startY, 
        array $nomina, 
        array $periodo, 
        array $empleado, 
        ?array $cargo, 
        array $detalle, 
        array $asignaciones, 
        array $deducciones, 
        array $empConfig,
        ?array $tasaCambio = null
    ): void {
        $pdf->SetY($startY);
        $startX = 10;
        $width = 196; // Ancho utilizable en hoja carta (216 - 20)

        // Contenedor exterior con borde redondeado / fino
        $pdf->SetDrawColor(203, 213, 225); // #cbd5e1
        $pdf->SetLineWidth(0.3);

        // 1. MEMBRETE Y CABECERA DE LA EMPRESA
        $nombreEmpresa = $empConfig['nombre_empresa'] ?? 'EMPRESA DEMO C.A.';
        $rifEmpresa = $empConfig['rif'] ?? 'J-00000000-0';
        $regimenIvss = $empConfig['regimen_ivss'] ?? 'Clase_II';
        $descPeriodo = !empty($periodo['descripcion']) ? $periodo['descripcion'] : $periodo['tipo_periodo'];

        // Barra superior de título institucional
        $pdf->SetFillColor(15, 23, 42); // #0f172a
        $pdf->Rect($startX, $startY, $width, 14, 'F');

        $logoPath = __DIR__ . '/../../assets/images/logo_multitel.png';
        $textX = $startX + 3;
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, $startX + 2, $startY + 2, 10, 10);
            $textX = $startX + 14;
        }

        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Helvetica', 'B', 10.5);
        $pdf->SetXY($textX, $startY + 2);
        $pdf->Cell(110, 5, self::text(strtoupper($nombreEmpresa) . ' | INTERNET & FIBRA OPTICA'), 0, 0, 'L');

        $pdf->SetFont('Helvetica', 'B', 8.5);
        $pdf->SetXY($startX + 125, $startY + 2);
        $pdf->Cell(68, 5, self::text('COMPROBANTE DE PAGO'), 0, 1, 'R');

        $pdf->SetFont('Helvetica', '', 7.5);
        $pdf->SetTextColor(203, 213, 225);
        $pdf->SetXY($textX, $startY + 7.5);
        $pdf->Cell(110, 4, self::text('R.I.F.: ' . $rifEmpresa . ' | Cumana, Edo. Sucre | Regimen IVSS: ' . str_replace('_', ' ', $regimenIvss)), 0, 0, 'L');

        $pdf->SetXY($startX + 125, $startY + 7.5);
        $pdf->Cell(68, 4, self::text('Nomina #' . $nomina['id_nomina'] . ' | ' . $descPeriodo), 0, 1, 'R');

        // 2. DATOS DEL TRABAJADOR Y PERIODO
        $curY = $startY + 15.5;
        $pdf->SetFillColor(248, 250, 252); // #f8fafc
        $pdf->Rect($startX, $curY, $width, 17, 'F');
        $pdf->Rect($startX, $curY, $width, 17, 'D');

        $nombreCompleto = trim(implode(' ', array_filter([
            $empleado['primer_nombre'] ?? '',
            $empleado['segundo_nombre'] ?? '',
            $empleado['primer_apellido'] ?? '',
            $empleado['segundo_apellido'] ?? ''
        ])));

        $pdf->SetTextColor(30, 41, 59); // #1e293b
        
        // Fila 1 datos
        $pdf->SetFont('Helvetica', 'B', 7.5);
        $pdf->SetXY($startX + 3, $curY + 1.5);
        $pdf->Cell(22, 4, self::text('TRABAJADOR:'), 0, 0, 'L');
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->Cell(75, 4, self::text(strtoupper($nombreCompleto)), 0, 0, 'L');

        $pdf->SetFont('Helvetica', 'B', 7.5);
        $pdf->Cell(18, 4, self::text('CÉDULA:'), 0, 0, 'L');
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->Cell(30, 4, self::text($empleado['cedula'] ?? '—'), 0, 0, 'L');

        $pdf->SetFont('Helvetica', 'B', 7.5);
        $pdf->Cell(22, 4, self::text('F. INGRESO:'), 0, 0, 'L');
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->Cell(25, 4, self::text($empleado['fecha_ingreso'] ?? '—'), 0, 1, 'L');

        // Fila 2 datos
        $pdf->SetFont('Helvetica', 'B', 7.5);
        $pdf->SetXY($startX + 3, $curY + 6.5);
        $pdf->Cell(22, 4, self::text('CARGO:'), 0, 0, 'L');
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->Cell(75, 4, self::text($cargo['nombre_cargo'] ?? 'General'), 0, 0, 'L');

        $pdf->SetFont('Helvetica', 'B', 7.5);
        $pdf->Cell(18, 4, self::text('PERIODO:'), 0, 0, 'L');
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->Cell(75, 4, self::text(($periodo['fecha_inicio'] ?? '') . ' al ' . ($periodo['fecha_fin'] ?? '')), 0, 1, 'L');

        // Fila 3 datos
        $pdf->SetFont('Helvetica', 'B', 7.5);
        $pdf->SetXY($startX + 3, $curY + 11.5);
        $pdf->Cell(22, 4, self::text('SUELDO DIARIO:'), 0, 0, 'L');
        $pdf->SetFont('Helvetica', '', 8);
        $sueldoDiarioBs = (float)($detalle['sueldo_diario'] ?? 0);
        $pdf->Cell(35, 4, self::text('Bs. ' . self::money($sueldoDiarioBs)), 0, 0, 'L');

        $pdf->SetFont('Helvetica', 'B', 7.5);
        $pdf->Cell(25, 4, self::text('DÍAS LABORADOS:'), 0, 0, 'L');
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->Cell(20, 4, self::text((string)($detalle['dias_trabajados'] ?? 0)), 0, 0, 'L');

        $pdf->SetFont('Helvetica', 'B', 7.5);
        $pdf->Cell(22, 4, self::text('INASISTENCIAS:'), 0, 0, 'L');
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->Cell(20, 4, self::text((string)($detalle['dias_inasistencia'] ?? 0)), 0, 0, 'L');

        $pdf->SetFont('Helvetica', 'B', 7.5);
        $pdf->Cell(25, 4, self::text('SEM. IVSS:'), 0, 0, 'L');
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->Cell(25, 4, self::text((string)($nomina['semanas_descontar_ivss'] ?? 2)), 0, 1, 'L');

        // 3. TABLA COMPARATIVA: ASIGNACIONES VS DEDUCCIONES (DOBLE COLUMNA)
        $curY += 19;
        $colW = $width / 2; // 98mm por cada columna

        // Cabeceras de columnas
        $pdf->SetFillColor(241, 245, 249); // #f1f5f9
        $pdf->SetDrawColor(203, 213, 225);
        $pdf->SetFont('Helvetica', 'B', 8);

        // Header Asignaciones (Izquierda)
        $pdf->SetXY($startX, $curY);
        $pdf->SetTextColor(16, 100, 50); // Verde oscuro
        $pdf->Cell(68, 6, self::text('   ASIGNACIONES / INGRESOS'), 1, 0, 'L', true);
        $pdf->Cell(30, 6, self::text('MONTO (Bs.)   '), 1, 0, 'R', true);

        // Header Deducciones (Derecha)
        $pdf->SetTextColor(153, 27, 27); // Rojo vino
        $pdf->Cell(68, 6, self::text('   DEDUCCIONES / RETENCIONES'), 1, 0, 'L', true);
        $pdf->Cell(30, 6, self::text('MONTO (Bs.)   '), 1, 1, 'R', true);

        // Renderizado paralelo de filas
        $maxRows = max(count($asignaciones), count($deducciones), 4);
        $rowH = 5.2;
        $tableY = $curY + 6;

        $pdf->SetTextColor(30, 41, 59);
        $pdf->SetFont('Helvetica', '', 7.5);

        for ($i = 0; $i < $maxRows; $i++) {
            $posY = $tableY + ($i * $rowH);
            $bg = ($i % 2 === 1) ? 250 : 255;
            $pdf->SetFillColor($bg, $bg, $bg);

            // Asignación
            $asigNombre = '';
            $asigMonto = '';
            if (isset($asignaciones[$i])) {
                $a = $asignaciones[$i];
                $cant = !empty($a['cantidad']) && (float)$a['cantidad'] > 0 ? ' (' . (float)$a['cantidad'] . ')' : '';
                $asigNombre = ' ' . ($a['nombre_concepto'] ?? $a['nombre'] ?? '') . $cant;
                $asigMonto = self::money((float)($a['monto'] ?? 0)) . ' ';
            }
            $pdf->SetXY($startX, $posY);
            $pdf->Cell(68, $rowH, self::text($asigNombre), 'LR', 0, 'L', true);
            $pdf->Cell(30, $rowH, self::text($asigMonto), 'LR', 0, 'R', true);

            // Deducción
            $dedNombre = '';
            $dedMonto = '';
            if (isset($deducciones[$i])) {
                $d = $deducciones[$i];
                $dedNombre = ' ' . ($d['nombre_concepto'] ?? $d['nombre'] ?? '');
                $dedMonto = self::money((float)($d['monto'] ?? 0)) . ' ';
            }
            $pdf->SetXY($startX + $colW, $posY);
            $pdf->Cell(68, $rowH, self::text($dedNombre), 'LR', 0, 'L', true);
            $pdf->Cell(30, $rowH, self::text($dedMonto), 'LR', 1, 'R', true);
        }

        // Borde inferior de la tabla
        $footY = $tableY + ($maxRows * $rowH);
        $pdf->Line($startX, $footY, $startX + $width, $footY);

        // 4. SUB-TOTALES POR COLUMNA
        $totalAsignaciones = (float)($detalle['total_asignaciones'] ?? 0);
        $totalDeducciones = (float)($detalle['total_deducciones'] ?? 0);
        $netoPagar = (float)($detalle['neto_pagar'] ?? 0);

        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->SetFillColor(241, 245, 249);
        $pdf->SetXY($startX, $footY);
        $pdf->Cell(68, 6, self::text(' TOTAL ASIGNACIONES:'), 1, 0, 'L', true);
        $pdf->Cell(30, 6, self::text('Bs. ' . self::money($totalAsignaciones) . ' '), 1, 0, 'R', true);

        $pdf->SetXY($startX + $colW, $footY);
        $pdf->Cell(68, 6, self::text(' TOTAL DEDUCCIONES:'), 1, 0, 'L', true);
        $pdf->Cell(30, 6, self::text('Bs. ' . self::money($totalDeducciones) . ' '), 1, 1, 'R', true);

        // 5. CAJA DESTACADA DE TOTAL NETO A PAGAR
        $netoY = $footY + 7.5;
        $pdf->SetFillColor(220, 252, 231); // #dcfce7 (Verde tenue formal)
        $pdf->SetDrawColor(34, 197, 94);   // #22c55e
        $pdf->Rect($startX, $netoY, $width, 8.5, 'DF');

        $pdf->SetTextColor(22, 101, 52); // #166534
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->SetXY($startX + 4, $netoY + 1.8);
        $pdf->Cell(90, 5, self::text('NETO EFECTIVO A DEPOSITAR / COBRAR:'), 0, 0, 'L');

        // Referencia USD si hay tasa
        $textoRefUsd = '';
        if (!empty($tasaCambio['monto_bcv']) && (float)$tasaCambio['monto_bcv'] > 0) {
            $equivUsd = round($netoPagar / (float)$tasaCambio['monto_bcv'], 2);
            $textoRefUsd = ' (Ref. $' . number_format($equivUsd, 2, '.', ',') . ' USD @ ' . number_format((float)$tasaCambio['monto_bcv'], 2, ',', '.') . ' Bs/$)';
        }

        $pdf->SetFont('Helvetica', 'B', 11);
        $pdf->Cell(98, 5, self::text('Bs. ' . self::money($netoPagar) . $textoRefUsd), 0, 1, 'R');

        // 6. FIRMAS DE CONFORMIDAD
        $firmasY = $netoY + 11;
        $pdf->SetTextColor(71, 85, 105);
        $pdf->SetFont('Helvetica', '', 7);

        // Firma Trabajador
        $lineW = 70;
        $xTrab = $startX + 18;
        $pdf->Line($xTrab, $firmasY + 11, $xTrab + $lineW, $firmasY + 11);
        $pdf->SetXY($xTrab, $firmasY + 12);
        $pdf->Cell($lineW, 3.5, self::text('Firma del Trabajador (Recibí Conforme)'), 0, 1, 'C');
        $pdf->SetXY($xTrab, $firmasY + 15.5);
        $pdf->Cell($lineW, 3.5, self::text('C.I.: ' . ($empleado['cedula'] ?? '_______________')), 0, 0, 'C');

        // Firma Empleador / RRHH
        $xPatr = $startX + $colW + 10;
        $pdf->Line($xPatr, $firmasY + 11, $xPatr + $lineW, $firmasY + 11);
        $pdf->SetXY($xPatr, $firmasY + 12);
        $pdf->Cell($lineW, 3.5, self::text('Por: ' . substr($nombreEmpresa, 0, 32)), 0, 1, 'C');
        $pdf->SetXY($xPatr, $firmasY + 15.5);
        $pdf->Cell($lineW, 3.5, self::text('Sello y Firma Autorizada (RRHH / Pagaduría)'), 0, 0, 'C');
    }

    /**
     * Dibuja una línea punteada horizontal fina.
     */
    public static function dibujarDashedLine(FPDF $pdf, float $x1, float $y, float $x2): void {
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.18);
        for ($x = $x1; $x < $x2; $x += 2.5) {
            $pdf->Line($x, $y, min($x + 1.2, $x2), $y);
        }
    }

    /**
     * Dibuja un ticket vertical tipo caja / centro comercial (exactamente como en la referencia POS).
     */
    public static function dibujarTicketComercial(
        FPDF $pdf, 
        float $startX, 
        float $startY, 
        float $ticketW, 
        float $ticketH, 
        array $nomina, 
        array $periodo, 
        array $empleado, 
        ?array $cargo, 
        array $detalle, 
        array $asignaciones, 
        array $deducciones, 
        array $empConfig,
        ?array $tasaCambio = null
    ): void {
        // Borde exterior suave del ticket
        $pdf->SetDrawColor(203, 213, 225); // #cbd5e1
        $pdf->SetLineWidth(0.2);
        $pdf->Rect($startX, $startY, $ticketW, $ticketH, 'D');

        $nombreEmpresa = $empConfig['nombre_empresa'] ?? 'MULTITEL C.A.';
        $rifEmpresa = $empConfig['rif'] ?? 'J-40982716-0';
        $descPeriodo = !empty($periodo['descripcion']) ? $periodo['descripcion'] : ($periodo['tipo_periodo'] ?? 'Quincenal');

        // 1. LOGO Y CABECERA CENTRADA
        $logoPath = __DIR__ . '/../../assets/images/logo_multitel.png';
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, $startX + ($ticketW - 7) / 2, $startY + 1.2, 7, 7);
        }

        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->SetTextColor(15, 23, 42);
        $pdf->SetXY($startX, $startY + 8.5);
        $pdf->Cell($ticketW, 3.2, self::text(strtoupper($nombreEmpresa)), 0, 1, 'C');

        $pdf->SetFont('Helvetica', 'B', 6);
        $pdf->SetTextColor(19, 76, 181);
        $pdf->Cell($ticketW, 2.6, self::text('INTERNET & FIBRA OPTICA'), 0, 1, 'C');

        $pdf->SetFont('Helvetica', '', 5.2);
        $pdf->SetTextColor(100, 116, 139);
        $pdf->Cell($ticketW, 2.4, self::text('R.I.F.: ' . $rifEmpresa . ' | Cumana, Sucre'), 0, 1, 'C');

        $pdf->SetFont('Helvetica', 'B', 6.2);
        $pdf->SetTextColor(15, 23, 42);
        $pdf->Cell($ticketW, 3, self::text('COMPROBANTE DE PAGO'), 0, 1, 'C');

        $pdf->SetFont('Helvetica', '', 5.2);
        $pdf->SetTextColor(71, 85, 105);
        $pdf->Cell($ticketW, 2.4, self::text('Nomina #' . str_pad((string)$nomina['id_nomina'], 5, '0', STR_PAD_LEFT) . ' | ' . substr($descPeriodo, 0, 26)), 0, 1, 'C');

        // Línea divisoria punteada
        $curY = $startY + 23;
        self::dibujarDashedLine($pdf, $startX + 2, $curY, $startX + $ticketW - 2);

        // 2. DATOS DEL TRABAJADOR
        $curY += 1.2;
        $pdf->SetFillColor(248, 250, 252);
        $pdf->Rect($startX + 1.5, $curY, $ticketW - 3, 14.5, 'F');

        $nombreCompleto = trim(implode(' ', array_filter([
            $empleado['primer_nombre'] ?? '',
            $empleado['segundo_nombre'] ?? '',
            $empleado['primer_apellido'] ?? '',
            $empleado['segundo_apellido'] ?? ''
        ])));

        $pdf->SetXY($startX + 2.5, $curY + 0.8);
        $pdf->SetFont('Helvetica', 'B', 5.2);
        $pdf->SetTextColor(100, 116, 139);
        $pdf->Cell(15, 2.8, self::text('TRABAJADOR:'), 0, 0, 'L');
        $pdf->SetFont('Helvetica', 'B', 6);
        $pdf->SetTextColor(15, 23, 42);
        $pdf->Cell($ticketW - 20, 2.8, self::text(substr(strtoupper($nombreCompleto), 0, 28)), 0, 1, 'L');

        $pdf->SetX($startX + 2.5);
        $pdf->SetFont('Helvetica', 'B', 5.2);
        $pdf->SetTextColor(100, 116, 139);
        $pdf->Cell(11, 2.7, self::text('CEDULA:'), 0, 0, 'L');
        $pdf->SetFont('Helvetica', '', 5.8);
        $pdf->SetTextColor(15, 23, 42);
        $pdf->Cell(20, 2.7, self::text($empleado['cedula'] ?? '—'), 0, 0, 'L');
        $pdf->SetFont('Helvetica', 'B', 5.2);
        $pdf->SetTextColor(100, 116, 139);
        $pdf->Cell(9, 2.7, self::text('DIAS:'), 0, 0, 'L');
        $pdf->SetFont('Helvetica', 'B', 5.8);
        $pdf->SetTextColor(15, 23, 42);
        $pdf->Cell(15, 2.7, self::text((string)($detalle['dias_trabajados'] ?? 0)), 0, 1, 'L');

        $pdf->SetX($startX + 2.5);
        $pdf->SetFont('Helvetica', 'B', 5.2);
        $pdf->SetTextColor(100, 116, 139);
        $pdf->Cell(11, 2.7, self::text('CARGO:'), 0, 0, 'L');
        $pdf->SetFont('Helvetica', '', 5.6);
        $pdf->SetTextColor(15, 23, 42);
        $pdf->Cell($ticketW - 16, 2.7, self::text(substr($cargo['nombre_cargo'] ?? 'General', 0, 28)), 0, 1, 'L');

        $pdf->SetX($startX + 2.5);
        $pdf->SetFont('Helvetica', 'B', 5.2);
        $pdf->SetTextColor(100, 116, 139);
        $pdf->Cell(11, 2.7, self::text('INGRESO:'), 0, 0, 'L');
        $pdf->SetFont('Helvetica', '', 5.5);
        $pdf->SetTextColor(15, 23, 42);
        $pdf->Cell(19, 2.7, self::text($empleado['fecha_ingreso'] ?? '—'), 0, 0, 'L');
        $pdf->SetFont('Helvetica', 'B', 5.2);
        $pdf->SetTextColor(100, 116, 139);
        $pdf->Cell(7, 2.7, self::text('SD:'), 0, 0, 'L');
        $pdf->SetFont('Helvetica', '', 5.5);
        $pdf->SetTextColor(15, 23, 42);
        $pdf->Cell(20, 2.7, self::text('Bs. ' . self::money((float)($detalle['sueldo_diario'] ?? 0))), 0, 1, 'L');

        $curY += 15.5;
        self::dibujarDashedLine($pdf, $startX + 2, $curY, $startX + $ticketW - 2);

        // 3. ASIGNACIONES
        $curY += 1.2;
        $pdf->SetXY($startX + 2, $curY);
        $pdf->SetFont('Helvetica', 'B', 5.5);
        $pdf->SetTextColor(16, 100, 50);
        $pdf->Cell($ticketW - 22, 2.8, self::text('(+) ASIGNACIONES'), 0, 0, 'L');
        $pdf->Cell(18, 2.8, self::text('MONTO Bs.'), 0, 1, 'R');

        $pdf->SetFont('Helvetica', '', 5.2);
        $pdf->SetTextColor(30, 41, 59);

        $asigList = !empty($asignaciones) ? array_slice($asignaciones, 0, 3) : [['nombre_concepto' => 'Sueldo Basico', 'monto' => 0]];
        foreach ($asigList as $a) {
            $cant = !empty($a['cantidad']) && (float)$a['cantidad'] > 0 ? ' (' . (float)$a['cantidad'] . ')' : '';
            $pdf->SetX($startX + 2);
            $pdf->Cell($ticketW - 22, 2.6, self::text(substr(($a['nombre_concepto'] ?? $a['nombre'] ?? 'Asignacion') . $cant, 0, 24)), 0, 0, 'L');
            $pdf->Cell(18, 2.6, self::money((float)($a['monto'] ?? 0)), 0, 1, 'R');
        }

        $pdf->SetX($startX + 2);
        $pdf->SetFont('Helvetica', 'B', 5.2);
        $pdf->SetTextColor(16, 100, 50);
        $pdf->Cell($ticketW - 22, 2.8, self::text('TOTAL ASIGNACIONES:'), 0, 0, 'L');
        $pdf->Cell(18, 2.8, 'Bs. ' . self::money((float)($detalle['total_asignaciones'] ?? 0)), 0, 1, 'R');

        $curY = $pdf->GetY() + 0.8;
        self::dibujarDashedLine($pdf, $startX + 2, $curY, $startX + $ticketW - 2);

        // 4. DEDUCCIONES
        $curY += 1.2;
        $pdf->SetXY($startX + 2, $curY);
        $pdf->SetFont('Helvetica', 'B', 5.5);
        $pdf->SetTextColor(153, 27, 27);
        $pdf->Cell($ticketW - 22, 2.8, self::text('(-) DEDUCCIONES'), 0, 0, 'L');
        $pdf->Cell(18, 2.8, self::text('MONTO Bs.'), 0, 1, 'R');

        $pdf->SetFont('Helvetica', '', 5.2);
        $pdf->SetTextColor(30, 41, 59);

        $dedList = !empty($deducciones) ? array_slice($deducciones, 0, 4) : [['nombre_concepto' => 'Sin deducciones', 'monto' => 0]];
        foreach ($dedList as $d) {
            $pdf->SetX($startX + 2);
            $pdf->Cell($ticketW - 22, 2.6, self::text(substr($d['nombre_concepto'] ?? $d['nombre'] ?? 'Deduccion', 0, 24)), 0, 0, 'L');
            $pdf->Cell(18, 2.6, self::money((float)($d['monto'] ?? 0)), 0, 1, 'R');
        }

        $pdf->SetX($startX + 2);
        $pdf->SetFont('Helvetica', 'B', 5.2);
        $pdf->SetTextColor(153, 27, 27);
        $pdf->Cell($ticketW - 22, 2.8, self::text('TOTAL DEDUCCIONES:'), 0, 0, 'L');
        $pdf->Cell(18, 2.8, 'Bs. ' . self::money((float)($detalle['total_deducciones'] ?? 0)), 0, 1, 'R');

        // 5. CAJA DE TOTAL NETO
        $curY = $pdf->GetY() + 1.2;
        $pdf->SetDrawColor(34, 197, 94);
        $pdf->SetFillColor(220, 252, 231);
        $pdf->Rect($startX + 2, $curY, $ticketW - 4, 9.5, 'DF');

        $netoPagar = (float)($detalle['neto_pagar'] ?? 0);
        $pdf->SetXY($startX + 2, $curY + 0.8);
        $pdf->SetFont('Helvetica', 'B', 5.5);
        $pdf->SetTextColor(22, 101, 52);
        $pdf->Cell($ticketW - 4, 2.5, self::text('NETO EFECTIVO A COBRAR:'), 0, 1, 'C');

        $pdf->SetFont('Helvetica', 'B', 8.5);
        $pdf->Cell($ticketW - 4, 3.8, 'Bs. ' . self::money($netoPagar), 0, 1, 'C');

        if (!empty($tasaCambio['monto_bcv']) && (float)$tasaCambio['monto_bcv'] > 0) {
            $equivUsd = round($netoPagar / (float)$tasaCambio['monto_bcv'], 2);
            $pdf->SetFont('Helvetica', 'B', 4.8);
            $pdf->SetTextColor(21, 128, 61);
            $pdf->Cell($ticketW - 4, 2.2, self::text('(Ref. $' . number_format($equivUsd, 2, '.', ',') . ' USD @ ' . number_format((float)$tasaCambio['monto_bcv'], 2, ',', '.') . ' Bs/$)'), 0, 1, 'C');
        }

        // 6. FIRMAS
        $curY += 11.5;
        $lineW = 40;
        $xCenter = $startX + ($ticketW - $lineW) / 2;

        $pdf->SetDrawColor(100, 116, 139);
        $pdf->Line($xCenter, $curY + 6, $xCenter + $lineW, $curY + 6);
        $pdf->SetXY($startX, $curY + 6.5);
        $pdf->SetFont('Helvetica', '', 5);
        $pdf->SetTextColor(71, 85, 105);
        $pdf->Cell($ticketW, 2.4, self::text('Firma Trabajador (Recibi Conforme)'), 0, 1, 'C');
        $pdf->Cell($ticketW, 2.4, self::text('C.I.: ' . ($empleado['cedula'] ?? '____________')), 0, 1, 'C');

        $pdf->Line($xCenter, $curY + 16.5, $xCenter + $lineW, $curY + 16.5);
        $pdf->SetXY($startX, $curY + 17);
        $pdf->SetFont('Helvetica', 'B', 5);
        $pdf->Cell($ticketW, 2.4, self::text('Por: ' . substr($nombreEmpresa, 0, 24)), 0, 1, 'C');
        $pdf->SetFont('Helvetica', '', 4.6);
        $pdf->Cell($ticketW, 2.2, self::text('Sello y Firma Autorizada (RRHH)'), 0, 1, 'C');
    }

    /**
     * Dibuja una línea punteada horizontal con texto para corte con tijera.
     */
    public static function dibujarLineaCorte(FPDF $pdf, float $y, string $texto = '--- CORTE AQUI  -  COMPROBANTE ORIGINAL TRABAJADOR ---'): void {
        $pdf->SetDrawColor(148, 163, 184); // #94a3b8
        $pdf->SetLineWidth(0.2);
        for ($x = 10; $x < 206; $x += 4) {
            $pdf->Line($x, $y, $x + 2, $y);
        }
        $pdf->SetFont('Helvetica', 'I', 6.5);
        $pdf->SetTextColor(100, 116, 139);
        $pdf->SetXY(10, $y - 1.8);
        $pdf->Cell(196, 3.5, self::text($texto), 0, 0, 'C');
    }

    /**
     * Genera el PDF del recibo individual de un trabajador.
     */
    public static function generarReciboIndividual(
        array $nomina, 
        array $periodo, 
        array $empleado, 
        ?array $cargo, 
        array $detalle, 
        array $asignaciones, 
        array $deducciones, 
        array $empConfig,
        ?array $tasaCambio = null
    ): FPDF {
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->SetTitle(self::text('Recibo de Pago - ' . ($empleado['cedula'] ?? '')));
        $pdf->SetAuthor(self::text($empConfig['nombre_empresa'] ?? 'Nómina'));
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        // En hoja carta individual, colocamos el recibo original arriba y una copia de acuse abajo
        self::dibujarRecibo($pdf, 10, $nomina, $periodo, $empleado, $cargo, $detalle, $asignaciones, $deducciones, $empConfig, $tasaCambio);

        // Línea divisoria punteada para corte
        self::dibujarLineaCorte($pdf, 137, '--- CORTE AQUI  (ORIGINAL TRABAJADOR / COPIA EXPEDIENTE) ---');

        // Copia inferior
        self::dibujarRecibo($pdf, 142, $nomina, $periodo, $empleado, $cargo, $detalle, $asignaciones, $deducciones, $empConfig, $tasaCambio);

        return $pdf;
    }

    /**
     * Genera el PDF con todos los recibos de la nómina.
     * Formatos soportados:
     * - 'carta6' (por defecto): 6 tickets de centro comercial por hoja Carta (3 columnas x 2 filas).
     * - 'pos80': Formato continuo de rollo térmico 80mm para impresora de tickets POS.
     * - 'extendido': 1 trabajador amplio por hoja.
     */
    public static function generarRecibosMasivos(
        array $nomina, 
        array $periodo, 
        array $recibos, 
        array $empConfig,
        ?array $tasaCambio = null,
        string $modo = 'carta6'
    ): FPDF {
        $total = count($recibos);

        // MODO TICKET TÉRMICO 80mm (Punto de Venta / Centro Comercial directo)
        if ($modo === 'pos80' || $modo === 'ticket') {
            $pdf = new FPDF('P', 'mm', [80, 145]);
            $pdf->SetTitle(self::text('Tickets Nómina POS 80mm - ' . ($periodo['fecha_inicio'] ?? '')));
            $pdf->SetAuthor(self::text($empConfig['nombre_empresa'] ?? 'Nómina'));
            $pdf->SetAutoPageBreak(false);

            for ($i = 0; $i < $total; $i++) {
                $pdf->AddPage();
                $r = $recibos[$i];
                // Ticket 72mm de ancho centrado en 80mm (X=4, Y=5, H=134)
                self::dibujarTicketComercial(
                    $pdf, 4, 5, 72, 134,
                    $nomina, $periodo, $r['empleado'], $r['cargo'],
                    $r['detalle'], $r['asignaciones'], $r['deducciones'],
                    $empConfig, $tasaCambio
                );
            }
            return $pdf;
        }

        // MODO EXTENDIDO (1 trabajador amplio por hoja)
        if ($modo === 'extendido') {
            $pdf = new FPDF('P', 'mm', 'Letter');
            $pdf->SetTitle(self::text('Recibos de Nómina - Periodo ' . ($periodo['fecha_inicio'] ?? '')));
            $pdf->SetAuthor(self::text($empConfig['nombre_empresa'] ?? 'Nómina'));
            $pdf->SetAutoPageBreak(false);

            for ($i = 0; $i < $total; $i++) {
                $pdf->AddPage();
                $r = $recibos[$i];
                self::dibujarRecibo(
                    $pdf, 15, $nomina, $periodo,
                    $r['empleado'], $r['cargo'], $r['detalle'],
                    $r['asignaciones'], $r['deducciones'],
                    $empConfig, $tasaCambio
                );
            }
            return $pdf;
        }

        // MODO PREDETERMINADO: CARTA 6 POR HOJA (3 COLUMNAS X 2 FILAS)
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->SetTitle(self::text('Tickets Nómina (6 por Hoja) - Periodo ' . ($periodo['fecha_inicio'] ?? '')));
        $pdf->SetAuthor(self::text($empConfig['nombre_empresa'] ?? 'Nómina'));
        $pdf->SetAutoPageBreak(false);

        $colWidth = 63;
        $rowHeight = 127;
        $colX = [8, 76, 144];
        $rowY = [8, 141];

        for ($i = 0; $i < $total; $i += 6) {
            $pdf->AddPage();

            // Líneas de corte con tijeras guía entre columnas y filas
            $pdf->SetDrawColor(160, 174, 192);
            $pdf->SetLineWidth(0.15);

            // Guías verticales de corte (a X=73.5 y X=141.5)
            for ($y = 6; $y < 272; $y += 3) {
                $pdf->Line(73.5, $y, 73.5, $y + 1.5);
                $pdf->Line(141.5, $y, 141.5, $y + 1.5);
            }
            // Guía horizontal de corte (a Y=137.5)
            for ($x = 6; $x < 210; $x += 3) {
                $pdf->Line($x, 137.5, $x + 1.5, 137.5);
            }

            // Renderizado de hasta 6 tickets en la hoja
            for ($slot = 0; $slot < 6; $slot++) {
                $idx = $i + $slot;
                if ($idx >= $total) break;

                $col = $slot % 3;
                $row = (int)($slot / 3);
                $x = $colX[$col];
                $y = $rowY[$row];

                $r = $recibos[$idx];
                self::dibujarTicketComercial(
                    $pdf, $x, $y, $colWidth, $rowHeight,
                    $nomina, $periodo, $r['empleado'], $r['cargo'],
                    $r['detalle'], $r['asignaciones'], $r['deducciones'],
                    $empConfig, $tasaCambio
                );
            }
        }

        return $pdf;
    }

    /**
     * Genera la sábana consolidada de nómina en orientación horizontal (Landscape).
     */
    public static function generarResumenNominaPdf(
        array $nomina, 
        array $periodo, 
        array $detallesList, 
        array $empConfig,
        ?array $tasaCambio = null
    ): FPDF {
        $pdf = new FPDF('L', 'mm', 'Letter'); // Horizontal
        $pdf->SetTitle(self::text('Resumen General de Nómina #' . $nomina['id_nomina']));
        $pdf->SetAuthor(self::text($empConfig['nombre_empresa'] ?? 'Nómina'));
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        $nombreEmpresa = $empConfig['nombre_empresa'] ?? 'EMPRESA DEMO C.A.';
        $rifEmpresa = $empConfig['rif'] ?? 'J-00000000-0';
        $descPeriodo = !empty($periodo['descripcion']) ? $periodo['descripcion'] : $periodo['tipo_periodo'];

        // Cabecera reporte
        $pdf->SetFont('Helvetica', 'B', 13.5);
        $pdf->SetTextColor(15, 23, 42);
        $pdf->Cell(259, 6, self::text(strtoupper($nombreEmpresa) . ' - PROVEEDOR DE INTERNET & FIBRA OPTICA'), 0, 1, 'C');

        $pdf->SetFont('Helvetica', 'B', 9.5);
        $pdf->SetTextColor(100, 116, 139);
        $pdf->Cell(259, 4.5, self::text('R.I.F.: ' . $rifEmpresa . ' | Cumana, Edo. Sucre | REPORTE RESUMEN CONSOLIDADO DE NOMINA'), 0, 1, 'C');

        $pdf->SetFont('Helvetica', '', 9);
        $pdf->Cell(259, 5, self::text('Periodo: ' . $descPeriodo . ' (' . $periodo['fecha_inicio'] . ' al ' . $periodo['fecha_fin'] . ') | Semanas IVSS: ' . $nomina['semanas_descontar_ivss']), 0, 1, 'C');
        $pdf->Ln(4);

        // Tabla de trabajadores
        // Columnas: N° (8), Cédula (22), Nombre (60), Cargo (40), Días (14), Asignaciones (28), Deducciones (28), Neto Bs. (32), Firma (27) = 259mm
        $pdf->SetFillColor(15, 23, 42);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Helvetica', 'B', 8);

        $pdf->Cell(8, 7, '#', 1, 0, 'C', true);
        $pdf->Cell(22, 7, self::text('CÉDULA'), 1, 0, 'C', true);
        $pdf->Cell(60, 7, self::text('APELLIDOS Y NOMBRES'), 1, 0, 'L', true);
        $pdf->Cell(40, 7, self::text('CARGO'), 1, 0, 'L', true);
        $pdf->Cell(14, 7, self::text('DÍAS'), 1, 0, 'C', true);
        $pdf->Cell(28, 7, self::text('ASIGNACIONES'), 1, 0, 'R', true);
        $pdf->Cell(28, 7, self::text('DEDUCCIONES'), 1, 0, 'R', true);
        $pdf->Cell(32, 7, self::text('NETO A PAGAR'), 1, 0, 'R', true);
        $pdf->Cell(27, 7, self::text('FIRMA'), 1, 1, 'C', true);

        $pdf->SetFont('Helvetica', '', 8);
        $pdf->SetTextColor(30, 41, 59);

        $totAsig = 0;
        $totDeduc = 0;
        $totNeto = 0;
        $i = 1;

        foreach ($detallesList as $d) {
            $bg = ($i % 2 === 0) ? 248 : 255;
            $pdf->SetFillColor($bg, $bg, $bg);

            $nombre = trim(($d['primer_apellido'] ?? '') . ' ' . ($d['segundo_apellido'] ?? '') . ', ' . ($d['primer_nombre'] ?? '') . ' ' . ($d['segundo_nombre'] ?? ''));
            $asig = (float)$d['total_asignaciones'];
            $ded = (float)$d['total_deducciones'];
            $neto = (float)$d['neto_pagar'];

            $totAsig += $asig;
            $totDeduc += $ded;
            $totNeto += $neto;

            $pdf->Cell(8, 6, $i, 1, 0, 'C', true);
            $pdf->Cell(22, 6, self::text($d['cedula'] ?? ''), 1, 0, 'C', true);
            $pdf->Cell(60, 6, self::text(substr($nombre, 0, 36)), 1, 0, 'L', true);
            $pdf->Cell(40, 6, self::text(substr($d['nombre_cargo'] ?? 'General', 0, 24)), 1, 0, 'L', true);
            $pdf->Cell(14, 6, $d['dias_trabajados'] ?? 0, 1, 0, 'C', true);
            $pdf->Cell(28, 6, self::money($asig), 1, 0, 'R', true);
            $pdf->Cell(28, 6, self::money($ded), 1, 0, 'R', true);
            $pdf->Cell(32, 6, self::money($neto), 1, 0, 'R', true);
            $pdf->Cell(27, 6, '', 1, 1, 'C', true);

            $i++;
        }

        // Fila de Totales
        $pdf->SetFont('Helvetica', 'B', 8.5);
        $pdf->SetFillColor(220, 252, 231);
        $pdf->SetTextColor(22, 101, 52);

        $pdf->Cell(144, 7, self::text('TOTALES GENERALES CONSOLIDADOS (Bs.):'), 1, 0, 'R', true);
        $pdf->Cell(28, 7, self::money($totAsig), 1, 0, 'R', true);
        $pdf->Cell(28, 7, self::money($totDeduc), 1, 0, 'R', true);
        $pdf->Cell(32, 7, self::money($totNeto), 1, 0, 'R', true);
        $pdf->Cell(27, 7, '', 1, 1, 'C', true);

        return $pdf;
    }
}
