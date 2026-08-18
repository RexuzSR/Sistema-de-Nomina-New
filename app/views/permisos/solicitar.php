<?php if (!empty($error)): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

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
