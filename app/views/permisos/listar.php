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

<div class="card" style="padding:0; overflow:hidden;">
  <table>
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
        <tr><td colspan="7" style="text-align:center; color:var(--text-muted); padding:30px;">No hay permisos registrados.</td></tr>
      <?php endif; ?>
      <?php foreach ($permisos as $p): 
        $nombreComp = trim(preg_replace('/\s+/', ' ', implode(' ', array_filter([$p['primer_nombre'], $p['segundo_nombre'] ?? '', $p['primer_apellido'], $p['segundo_apellido'] ?? '']))));
        $inicial = strtoupper(substr($p['primer_nombre'] ?? 'E', 0, 1));
        $badgeStyle = '';
        if ($p['estado'] === 'Aprobado') {
          $badgeStyle = 'background:#dcfce7; color:#15803d; border:1px solid #86efac;';
        } elseif ($p['estado'] === 'Rechazado') {
          $badgeStyle = 'background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5;';
        } else {
          $badgeStyle = 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;';
        }
      ?>
        <tr>
          <td>
            <div class="emp-avatar-wrapper">
              <div class="emp-avatar"><?= $inicial ?></div>
              <strong style="font-size:14px; color:var(--text);"><?= htmlspecialchars($nombreComp) ?></strong>
            </div>
          </td>
          <td><span style="font-size:13px; color:#475569;"><?= htmlspecialchars($p['fecha_inicio']) ?></span></td>
          <td><span style="font-size:13px; color:#475569;"><?= htmlspecialchars($p['fecha_fin']) ?></span></td>
          <td><strong style="color:var(--text);"><?= htmlspecialchars($p['dias_solicitados']) ?> d</strong></td>
          <td><span style="font-size:13px; color:#334155;"><?= htmlspecialchars($p['motivo']) ?></span></td>
          <td>
            <span class="badge" style="<?= $badgeStyle ?>">
              <?= htmlspecialchars($p['estado']) ?>
            </span>
          </td>
          <td style="text-align:right;">
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
