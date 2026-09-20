<?php if (!empty($msg)): ?>
  <div class="alert success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="view-header-bar" style="margin-bottom:20px;">
  <div>
    <h2 style="margin:0;"><i class="fa-solid fa-file-signature" style="color:var(--primary); margin-right:8px;"></i> Gestión de Permisos y Licencias</h2>
    <p style="margin:4px 0 0; color:var(--text-muted); font-size:13px;">Aprobaciones y control de ausencias de empleados</p>
  </div>
  <div class="actions-row" style="margin-bottom:0;">
    <a class="btn" href="index.php?r=permisos/solicitar"><i class="fa-solid fa-plus"></i> Registrar Permiso</a>
  </div>
</div>

<div class="card table-responsive" id="cardTablaPermisos" style="padding:0; overflow-x:auto; -webkit-overflow-scrolling:touch;">
  <table id="tablaPermisos">
    <thead>
      <tr>
        <th>Empleado</th>
        <th>Del</th>
        <th>Al</th>
        <th>Días</th>
        <th>Motivo</th>
        <th>Estado</th>
        <th style="text-align:right;">Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$permisos): ?>
        <tr class="no-data-row"><td colspan="7" style="text-align:center; color:var(--text-muted); padding:30px;">No hay permisos registrados.</td></tr>
      <?php endif; ?>
      <?php foreach ($permisos as $p): 
        $nombreComp = trim(preg_replace('/\s+/', ' ', implode(' ', array_filter([$p['primer_nombre'], $p['segundo_nombre'] ?? '', $p['primer_apellido'], $p['segundo_apellido'] ?? '']))));
        $inicial = strtoupper(substr($p['primer_nombre'] ?? 'E', 0, 1));
        $pJsonData = [
          'id_permiso' => $p['id_permiso'],
          'empleado_nombre' => $nombreComp,
          'primer_nombre' => $p['primer_nombre'] ?? '',
          'fecha_inicio' => $p['fecha_inicio'],
          'fecha_fin' => $p['fecha_fin'],
          'dias_solicitados' => $p['dias_solicitados'],
          'motivo' => $p['motivo'],
          'estado' => $p['estado']
        ];
        $badgeClass = 'badge';
        if ($p['estado'] === 'Aprobado') {
          $badgeClass .= ' activo';
        } elseif ($p['estado'] === 'Rechazado') {
          $badgeClass .= ' inactivo';
        }
      ?>
        <tr class="permiso-row" onclick="abrirVerPermisoModal(this)" data-permiso="<?= htmlspecialchars(json_encode($pJsonData), ENT_QUOTES, 'UTF-8') ?>" title="Clic para ver detalles completos del permiso">
          <td>
            <div class="emp-avatar-wrapper">
              <div class="emp-avatar"><?= $inicial ?></div>
              <strong style="font-size:14px; color:var(--text);"><?= htmlspecialchars($nombreComp) ?></strong>
            </div>
          </td>
          <td><span style="font-size:13px; color:var(--text-muted);"><?= htmlspecialchars($p['fecha_inicio']) ?></span></td>
          <td><span style="font-size:13px; color:var(--text-muted);"><?= htmlspecialchars($p['fecha_fin']) ?></span></td>
          <td><strong style="color:var(--text);"><?= htmlspecialchars($p['dias_solicitados']) ?> d</strong></td>
          <td><span style="font-size:13px; color:var(--text);"><?= htmlspecialchars($p['motivo']) ?></span></td>
          <td>
            <span class="<?= $badgeClass ?>">
              <?= htmlspecialchars($p['estado']) ?>
            </span>
          </td>
          <td style="text-align:right;" onclick="event.stopPropagation()">
            <?php if ($p['estado'] === 'Pendiente'): ?>
              <a class="btn small" href="index.php?r=permisos/aprobar&id=<?= $p['id_permiso'] ?>&accion=Aprobado"><i class="fa-solid fa-check"></i> Aprobar</a>
              <a class="btn small danger" href="index.php?r=permisos/aprobar&id=<?= $p['id_permiso'] ?>&accion=Rechazado"><i class="fa-solid fa-xmark"></i> Rechazar</a>
            <?php else: ?>
              <span style="color:var(--text-muted); font-size:12px;">— Procesado —</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/VerModal.php'; ?>

<script nonce="<?= Security::getCspNonce() ?>">
document.addEventListener('DOMContentLoaded', function() {
  if (typeof initTablePagination === 'function') {
    window.permisosPaginator = initTablePagination({
      table: '#tablaPermisos',
      rowSelector: 'tbody tr.permiso-row',
      storageKey: 'nomina_paginator_permisos',
      defaultPageSize: 10,
      appendTo: '#cardTablaPermisos'
    });
  }
});
</script>

