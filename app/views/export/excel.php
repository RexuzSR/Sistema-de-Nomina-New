<table border="1">
  <tr><td colspan="7"><strong>Nomina: <?= htmlspecialchars($periodo['fecha_inicio']) ?> al <?= htmlspecialchars($periodo['fecha_fin']) ?></strong></td></tr>
  <tr><td colspan="7"></td></tr>
  <tr>
    <th>Nombres y Apellidos</th>
    <th>Cedula</th>
    <th>Dias trabajados</th>
    <th>Total Asignaciones</th>
    <th>Total Deducciones</th>
    <th>Neto a Pagar</th>
  </tr>
  <?php
    $t = ['total_asignaciones'=>0,'total_deducciones'=>0,'neto_pagar'=>0];
    foreach ($filas as $f):
      $t['total_asignaciones']+=$f['total_asignaciones'];
      $t['total_deducciones']+=$f['total_deducciones'];
      $t['neto_pagar']+=$f['neto_pagar'];
      $nombreEmpleado = trim(preg_replace('/\s+/', ' ', implode(' ', array_filter([$f['primer_nombre'], $f['segundo_nombre'] ?? '', $f['primer_apellido'], $f['segundo_apellido'] ?? '']))));
  ?>
  <tr>
    <td><?= htmlspecialchars($nombreEmpleado) ?></td>
    <td><?= htmlspecialchars($f['cedula']) ?></td>
    <td><?= $f['dias_trabajados'] ?></td>
    <td><?= $f['total_asignaciones'] ?></td>
    <td><?= $f['total_deducciones'] ?></td>
    <td><?= $f['neto_pagar'] ?></td>
  </tr>
  <?php endforeach; ?>
  <tr>
    <td colspan="3"><strong>TOTAL GENERAL</strong></td>
    <td><strong><?= $t['total_asignaciones'] ?></strong></td>
    <td><strong><?= $t['total_deducciones'] ?></strong></td>
    <td><strong><?= $t['neto_pagar'] ?></strong></td>
  </tr>
</table>
