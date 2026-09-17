<?php if (!empty($error)): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="view-header-bar" style="margin-bottom:20px;">
  <div>
    <h2 style="margin:0;"><i class="fa-solid fa-file-signature" style="color:var(--primary); margin-right:8px;"></i> Solicitar o Registrar Permiso</h2>
    <p style="margin:4px 0 0; color:var(--text-muted); font-size:13px;">Ingrese el empleado y las fechas de ausencia</p>
  </div>
  <a class="btn secondary" href="index.php?r=permisos"><i class="fa-solid fa-arrow-left"></i> Volver a permisos</a>
</div>

<div class="card">
  <form method="post" action="index.php?r=permisos/solicitar">
    <div class="form-grid">
      <div class="full">
        <label>Empleado *</label>
        <select name="id_empleado" required>
          <option value="">-- Selecciona --</option>
          <?php foreach ($empleados as $e): ?>
            <option value="<?= $e['id_empleado'] ?>"><?= htmlspecialchars(trim(preg_replace('/\s+/', ' ', implode(' ', array_filter([$e['primer_nombre'], $e['segundo_nombre'] ?? '', $e['primer_apellido'], $e['segundo_apellido'] ?? '']))))) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div><label>Fecha de inicio *</label><input type="date" name="fecha_inicio" required></div>
      <div><label>Fecha de fin *</label><input type="date" name="fecha_fin" required></div>
      <div class="full"><label>Motivo *</label><textarea name="motivo" rows="3" required></textarea></div>
    </div>
    <br>
    <button class="btn" type="submit">Registrar permiso</button>
    <a class="btn secondary" href="index.php?r=permisos">Cancelar</a>
  </form>
</div>
