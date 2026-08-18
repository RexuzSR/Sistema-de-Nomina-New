<?php if (!empty($msg)): ?>
  <div class="alert success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="view-header-bar" style="margin-bottom:20px;">
  <div>
    <h2 style="margin:0;"><i class="fa-solid fa-users" style="color:var(--primary); margin-right:8px;"></i> Catálogo de Empleados</h2>
    <p style="margin:4px 0 0; color:var(--text-muted); font-size:13px;">Gestión de personal registrado y estado laboral</p>
  </div>
  <div class="actions-row" style="margin-bottom:0;">
    <a class="btn" href="index.php?r=empleados/crear"><i class="fa-solid fa-user-plus"></i> Nuevo empleado</a>
  </div>
</div>

<div class="card" style="padding:0; overflow:hidden;">
  <table>
    <thead>
      <tr>
        <th>Cédula</th>
        <th>Empleado</th>
        <th>Tipo</th>
        <th>Jornada</th>
        <th>Cargo</th>
        <th>Sueldo ($)</th>
        <th>Estado</th>
        <th style="text-align:right;">Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$empleados): ?>
        <tr><td colspan="8" style="text-align:center; color:var(--text-muted); padding:30px;">No hay empleados registrados.</td></tr>
      <?php endif; ?>
      <?php foreach ($empleados as $e): 
        $nombreComp = trim(preg_replace('/\s+/', ' ', implode(' ', array_filter([$e['primer_nombre'], $e['segundo_nombre'] ?? '', $e['primer_apellido'], $e['segundo_apellido'] ?? '']))));
        $inicial = strtoupper(substr($e['primer_nombre'] ?? 'E', 0, 1));
      ?>
        <tr>
          <td><strong style="color:var(--text-muted); font-size:12.5px;"><?= htmlspecialchars($e['cedula']) ?></strong></td>
          <td>
            <div class="emp-avatar-wrapper">
              <?php 
                $fotoSrcList = '';
                if (!empty($e['foto'])) {
                  if (strpos($e['foto'], 'data:image/') === 0) {
                    $fotoSrcList = $e['foto'];
                  } elseif (file_exists(__DIR__ . '/../../assets/images/empleados/' . $e['foto'])) {
                    $fotoSrcList = 'assets/images/empleados/' . $e['foto'];
                  }
                }
              ?>
              <?php if ($fotoSrcList): ?>
                <img src="<?= htmlspecialchars($fotoSrcList) ?>" class="emp-avatar-img" alt="Foto">
              <?php else: ?>
                <div class="emp-avatar"><?= $inicial ?></div>
              <?php endif; ?>
              <div>
                <strong style="font-size:14px; color:var(--text);"><?= htmlspecialchars($nombreComp) ?></strong>
              </div>
            </div>
          </td>
          <td><span style="font-weight:600; color:#475569;"><?= htmlspecialchars($e['nombre_tipo'] ?? '') ?></span></td>
          <td><span style="font-size:12.5px; color:#64748b;"><?= htmlspecialchars($e['nombre_jornada'] ?? '') ?></span></td>
          <td><span class="cargo-tag"><?= htmlspecialchars($e['nombre_cargo'] ?? '—') ?></span></td>
          <td><strong style="color:#0f172a; font-size:14px;">$<?= number_format((float)$e['sueldo_divisa'], 2, ',', '.') ?></strong></td>
          <td><span class="badge <?= $e['estado'] ? 'activo' : 'inactivo' ?>"><?= $e['estado'] ? 'Activo' : 'Inactivo' ?></span></td>
          <td style="text-align:right;">
            <a class="btn small" href="index.php?r=empleados/editar&id=<?= $e['id_empleado'] ?>"><i class="fa-solid fa-pen-to-square"></i> Editar</a>
            <a class="btn small secondary" href="index.php?r=empleados/cambiarEstado&id=<?= $e['id_empleado'] ?>"><i class="fa-solid fa-power-off"></i> <?= $e['estado'] ? 'Inactivar' : 'Activar' ?></a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
