<?php
$nombreArchivo = 'Recibos_Nomina_' . $periodo['fecha_inicio'] . '_al_' . $periodo['fecha_fin'];
?>
<div class="actions-row no-print">
  <button class="btn" onclick="window.print()">Imprimir Todos / Guardar como PDF</button>
  <a class="btn secondary" href="index.php?r=nomina/detalle&id_nomina=<?= $id_nomina ?>">&larr; Volver al Detalle</a>
</div>

<style>
.recibo-box {
  border-bottom: 2px dashed #94a3b8;
  padding-bottom: 40px;
  margin-bottom: 40px;
}
.recibo-box:last-child {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}
@media print {
  .recibo-box {
    page-break-after: always;
    border-bottom: none;
    padding-bottom: 0;
    margin-bottom: 0;
  }
}
</style>

<div class="recibos-container">
  <?php foreach ($recibos as $index => $r):
      $empleado = $r['empleado'];
      $cargo = $r['cargo'];
      $detalle = $r['detalle'];
      $asignaciones = $r['asignaciones'];
      $deducciones = $r['deducciones'];
      $nombreEmpleado = trim(preg_replace('/\s+/', ' ', implode(' ', array_filter([$empleado['primer_nombre'], $empleado['segundo_nombre'] ?? '', $empleado['primer_apellido'], $empleado['segundo_apellido'] ?? '']))));
  ?>
    <div class="recibo-box">
      <div class="recibo">
        <h2>Recibo de Pago</h2>
        <div class="sub">Periodo: <?= htmlspecialchars($periodo['fecha_inicio']) ?> al <?= htmlspecialchars($periodo['fecha_fin']) ?></div>

        <table>
          <tr><td><strong>Nombre y Apellido:</strong></td><td><?= htmlspecialchars($nombreEmpleado) ?></td></tr>
          <tr><td><strong>Cédula:</strong></td><td><?= htmlspecialchars($empleado['cedula']) ?></td></tr>
          <tr><td><strong>Cargo:</strong></td><td><?= htmlspecialchars($cargo['nombre_cargo'] ?? '—') ?></td></tr>
          <tr><td><strong>Sueldo diario:</strong></td><td><?= number_format((float)$detalle['sueldo_diario'], 2, ',', '.') ?></td></tr>
          <tr><td><strong>Días trabajados:</strong></td><td><?= htmlspecialchars($detalle['dias_trabajados']) ?></td></tr>
          <?php if (($detalle['dias_inasistencia'] ?? 0) > 0): ?>
            <tr><td><strong>Días de inasistencia:</strong></td><td><?= htmlspecialchars($detalle['dias_inasistencia']) ?></td></tr>
          <?php endif; ?>
        </table>

        <table>
          <tr><th>Asignaciones</th><th>Cantidad</th><th>Monto</th></tr>
          <?php foreach ($asignaciones as $a): ?>
            <tr>
              <td><?= htmlspecialchars($a['nombre_concepto']) ?></td>
              <td><?= $a['cantidad'] > 0 ? htmlspecialchars((float)$a['cantidad']) : '—' ?></td>
              <td><?= number_format((float)$a['monto'], 2, ',', '.') ?></td>
            </tr>
          <?php endforeach; ?>
          <tr class="total-row"><td colspan="2">TOTAL ASIGNACIONES</td><td><?= number_format((float)$detalle['total_asignaciones'], 2, ',', '.') ?></td></tr>
        </table>

        <table>
          <tr><th>Deducciones</th><th>Monto</th></tr>
          <?php foreach ($deducciones as $d): ?>
            <tr><td><?= htmlspecialchars($d['nombre'] ?? $d['nombre_concepto']) ?></td><td><?= number_format((float)$d['monto'], 2, ',', '.') ?></td></tr>
          <?php endforeach; ?>
          <tr class="total-row"><td>TOTAL DEDUCCIONES</td><td><?= number_format((float)$detalle['total_deducciones'], 2, ',', '.') ?></td></tr>
        </table>

        <table>
          <tr class="total-row"><td>TOTAL A PAGAR (NETO)</td><td><?= number_format((float)$detalle['neto_pagar'], 2, ',', '.') ?></td></tr>
        </table>

        <div class="firma">
          <div class="line"></div>
          Firma del trabajador
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
