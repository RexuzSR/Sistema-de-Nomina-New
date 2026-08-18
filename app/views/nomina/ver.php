<p><a href="index.php?r=periodos">&larr; Volver a periodos</a></p>
<h2><?= htmlspecialchars($periodo['tipo_periodo']) ?>: <?= htmlspecialchars($periodo['fecha_inicio']) ?> al <?= htmlspecialchars($periodo['fecha_fin']) ?></h2>

<div class="actions-row">
  <a class="btn" href="index.php?r=nomina/detalle&id_nomina=<?= $id_nomina ?>">Editar detalle</a>
  <a class="btn secondary" href="index.php?r=nomina/exportExcel&id_nomina=<?= $id_nomina ?>">Exportar a Excel</a>
</div>

<table>
  <tr>
    <th>Empleado</th>
    <th>Cedula</th>
    <th>Dias trab.</th>
    <th>Total Asignaciones</th>
    <th>Total Deducciones</th>
    <th>Neto a pagar</th>
    <th>Recibo</th>
  </tr>
  <?php if (!$filas): ?>
    <tr><td colspan="7">Aun no hay detalle calculado para esta nomina.</td></tr>
  <?php endif; ?>
  <?php foreach ($filas as $f):
    $nombreEmpleado = trim(preg_replace('/\s+/', ' ', implode(' ', array_filter([$f['primer_nombre'], $f['segundo_nombre'] ?? '', $f['primer_apellido'], $f['segundo_apellido'] ?? '']))));
  ?>
    <tr>
      <td><?= htmlspecialchars($nombreEmpleado) ?></td>
      <td><?= htmlspecialchars($f['cedula']) ?></td>
      <td><?= htmlspecialchars($f['dias_trabajados']) ?></td>
      <td><?= number_format((float)$f['total_asignaciones'], 2, ',', '.') ?></td>
      <td><?= number_format((float)$f['total_deducciones'], 2, ',', '.') ?></td>
      <td><strong><?= number_format((float)$f['neto_pagar'], 2, ',', '.') ?></strong></td>
      <td><a class="btn small" href="index.php?r=nomina/recibo&id_nomina=<?= $id_nomina ?>&id_empleado=<?= $f['id_empleado'] ?>" target="_blank">Ver / PDF</a></td>
    </tr>
  <?php endforeach; ?>
  <?php if ($filas): ?>
  <tr class="total-row">
    <td colspan="3">TOTAL GENERAL</td>
    <td><?= number_format((float)$totales['total_asignaciones'], 2, ',', '.') ?></td>
    <td><?= number_format((float)$totales['total_deducciones'], 2, ',', '.') ?></td>
    <td><?= number_format((float)$totales['neto_pagar'], 2, ',', '.') ?></td>
    <td></td>
  </tr>
  <?php endif; ?>
</table>
