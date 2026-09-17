<div class="view-header-bar" style="margin-bottom:20px;">
  <div>
    <h2 style="margin:0;"><i class="fa-solid fa-file-invoice-dollar" style="color:var(--primary); margin-right:8px;"></i> Resumen de Nómina: <?= htmlspecialchars($periodo['tipo_periodo']) ?></h2>
    <p style="margin:4px 0 0; color:var(--text-muted); font-size:13px;">Ciclo del <?= htmlspecialchars($periodo['fecha_inicio']) ?> al <?= htmlspecialchars($periodo['fecha_fin']) ?></p>
  </div>
  <div class="actions-row" style="margin-bottom:0;">
    <a class="btn" href="index.php?r=nomina/detalle&id_nomina=<?= $id_nomina ?>"><i class="fa-solid fa-pen-to-square"></i> Editar detalle</a>
    <a class="btn secondary" href="index.php?r=nomina/exportExcel&id_nomina=<?= $id_nomina ?>"><i class="fa-solid fa-file-excel"></i> Exportar a Excel</a>
    <a class="btn secondary" href="index.php?r=periodos"><i class="fa-solid fa-arrow-left"></i> Volver a periodos</a>
  </div>
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
