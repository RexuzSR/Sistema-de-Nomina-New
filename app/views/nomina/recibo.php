<?php
$nombreEmpleado = trim(preg_replace('/\s+/', ' ', implode(' ', array_filter([$empleado['primer_nombre'], $empleado['segundo_nombre'] ?? '', $empleado['primer_apellido'], $empleado['segundo_apellido'] ?? '']))));
$descPeriodo = !empty($periodo['descripcion']) ? $periodo['descripcion'] : ($periodo['tipo_periodo'] . " (" . $periodo['fecha_inicio'] . " al " . $periodo['fecha_fin'] . ")");
?>
<div class="actions-row no-print" style="margin-bottom: 20px;">
  <button class="btn" style="background:#059669; font-size:14px; padding:10px 18px;" onclick="window.print()">📄 Imprimir / Guardar como PDF</button>
  <a class="btn secondary" href="index.php?r=nomina/detalle&id_nomina=<?= $id_nomina ?>">&larr; Volver al Detalle de Nómina</a>
</div>

<div class="recibo">
  <h2>Comprobante de Pago de Nómina</h2>
  <div class="sub"><strong><?= htmlspecialchars($descPeriodo) ?></strong> &middot; Periodo: <?= htmlspecialchars($periodo['fecha_inicio']) ?> al <?= htmlspecialchars($periodo['fecha_fin']) ?></div>

  <table>
    <tr><td><strong>Nombre y Apellido:</strong></td><td><?= htmlspecialchars($nombreEmpleado) ?></td></tr>
    <tr><td><strong>Cédula:</strong></td><td><?= htmlspecialchars($empleado['cedula']) ?></td></tr>
    <tr><td><strong>Cargo:</strong></td><td><?= htmlspecialchars($cargo['nombre_cargo'] ?? '—') ?></td></tr>
    <tr><td><strong>Sueldo Diario:</strong></td><td>Bs. <?= number_format((float)$detalle['sueldo_diario'], 2, ',', '.') ?></td></tr>
    <tr><td><strong>Días Trabajados:</strong></td><td><?= htmlspecialchars($detalle['dias_trabajados']) ?></td></tr>
  </table>

  <table>
    <tr><th>Asignaciones</th><th style="text-align:right;">Monto (Bs.)</th></tr>
    <?php foreach ($asignaciones as $a): ?>
      <tr><td><?= htmlspecialchars($a['nombre_concepto']) ?></td><td style="text-align:right;"><?= number_format((float)$a['monto'], 2, ',', '.') ?></td></tr>
    <?php endforeach; ?>
    <tr class="total-row"><td>TOTAL ASIGNACIONES</td><td style="text-align:right;">Bs. <?= number_format((float)$detalle['total_asignaciones'], 2, ',', '.') ?></td></tr>
  </table>

  <table>
    <tr><th>Deducciones</th><th style="text-align:right;">Monto (Bs.)</th></tr>
    <?php foreach ($deducciones as $d): ?>
      <tr><td><?= htmlspecialchars($d['nombre'] ?? $d['nombre_concepto'] ?? '') ?></td><td style="text-align:right;"><?= number_format((float)$d['monto'], 2, ',', '.') ?></td></tr>
    <?php endforeach; ?>
    <tr class="total-row"><td>TOTAL DEDUCCIONES</td><td style="text-align:right;">Bs. <?= number_format((float)$detalle['total_deducciones'], 2, ',', '.') ?></td></tr>
  </table>

  <table>
    <tr class="total-row" style="font-size:15px; background:#dcfce7 !important; color:#166534;">
      <td>TOTAL NETO A COBRAR</td>
      <td style="text-align:right;">Bs. <?= number_format((float)$detalle['neto_pagar'], 2, ',', '.') ?></td>
    </tr>
  </table>

  <div class="firma">
    <div class="line"></div>
    Firma del trabajador
  </div>
</div>
