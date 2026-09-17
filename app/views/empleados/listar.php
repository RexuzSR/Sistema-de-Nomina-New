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
        $fotoSrcList = '';
        if (!empty($e['foto'])) {
          if (strpos($e['foto'], 'data:image/') === 0) {
            $fotoSrcList = $e['foto'];
          } elseif (file_exists(__DIR__ . '/../../assets/images/empleados/' . $e['foto'])) {
            $fotoSrcList = 'assets/images/empleados/' . $e['foto'];
          }
        }
        $eJsonData = [
          'id_empleado' => $e['id_empleado'],
          'cedula' => $e['cedula'],
          'primer_nombre' => $e['primer_nombre'],
          'segundo_nombre' => $e['segundo_nombre'] ?? '',
          'primer_apellido' => $e['primer_apellido'],
          'segundo_apellido' => $e['segundo_apellido'] ?? '',
          'nombre_completo' => $nombreComp,
          'email' => $e['email'] ?? '',
          'nombre_tipo' => $e['nombre_tipo'] ?? '—',
          'nombre_jornada' => $e['nombre_jornada'] ?? '—',
          'nombre_cargo' => $e['nombre_cargo'] ?? '—',
          'sueldo_divisa' => (float)$e['sueldo_divisa'],
          'fecha_ingreso' => $e['fecha_ingreso'] ?? '',
          'estado' => (int)$e['estado'],
          'foto_src' => $fotoSrcList,
          'nombre_banco' => $e['nombre_banco'] ?? '',
          'codigo_banco' => $e['codigo_banco'] ?? '',
          'numero_cuenta' => $e['numero_cuenta'] ?? '',
          'tipo_cuenta' => $e['tipo_cuenta'] ?? '',
          'nombre_usuario' => $e['nombre_usuario'] ?? '',
          'nombre_rol' => $e['nombre_rol'] ?? '',
          'total_nominas' => (int)($e['total_nominas'] ?? 0),
          'total_permisos' => (int)($e['total_permisos'] ?? 0),
          'permisos_aprobados' => (int)($e['permisos_aprobados'] ?? 0),
          'permisos_pendientes' => (int)($e['permisos_pendientes'] ?? 0),
          'tasa_bcv' => (float)($tasaBcv ?? 0.0)
        ];
      ?>
        <tr onclick="abrirVerEmpleadoModal(this)" data-empleado="<?= htmlspecialchars(json_encode($eJsonData), ENT_QUOTES, 'UTF-8') ?>" title="Clic para ver expediente completo del empleado">

          <td><strong style="color:var(--text-muted); font-size:12.5px;"><?= htmlspecialchars($e['cedula']) ?></strong></td>
          <td>
            <div class="emp-avatar-wrapper">
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
          <td><span style="font-weight:600; color:var(--text); opacity:0.85;"><?= htmlspecialchars($e['nombre_tipo'] ?? '') ?></span></td>
          <td><span style="font-size:12.5px; color:var(--text-muted);"><?= htmlspecialchars($e['nombre_jornada'] ?? '') ?></span></td>
          <td><span class="cargo-tag"><?= htmlspecialchars($e['nombre_cargo'] ?? '—') ?></span></td>
          <td><strong style="color:var(--text); font-size:14px;">$<?= number_format((float)$e['sueldo_divisa'], 2, ',', '.') ?></strong></td>
          <td><span class="badge <?= $e['estado'] ? 'activo' : 'inactivo' ?>"><?= $e['estado'] ? 'Activo' : 'Inactivo' ?></span></td>
          <td style="text-align:right;" onclick="event.stopPropagation()">
            <a class="btn small" href="index.php?r=empleados/editar&id=<?= $e['id_empleado'] ?>"><i class="fa-solid fa-pen-to-square"></i> Editar</a>
            <a class="btn small secondary" href="index.php?r=empleados/cambiarEstado&id=<?= $e['id_empleado'] ?>"><i class="fa-solid fa-power-off"></i> <?= $e['estado'] ? 'Inactivar' : 'Activar' ?></a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/VerModal.php'; ?>

