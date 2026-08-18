<?php if (!empty($error)): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card">
  <form method="post" action="index.php?r=periodos/crear">
    <div class="form-grid">
      <div class="full">
        <label>Nombre descriptivo / Descripción</label>
        <input type="text" name="descripcion" placeholder="Ej: 1ra Quincena Mayo 2026">
      </div>
      <div>
        <label>Tipo de periodo *</label>
        <select name="tipo_periodo" required>
          <option value="Quincenal">Quincenal</option>
          <option value="Semanal">Semanal</option>
          <option value="Mensual">Mensual</option>
        </select>
      </div>
      <div><label>Numero de quincena (1 o 2)</label><input type="number" min="1" max="2" name="numero_quincena"></div>
      <div><label>Fecha de inicio *</label><input type="date" name="fecha_inicio" required></div>
      <div><label>Fecha de fin *</label><input type="date" name="fecha_fin" required></div>
      <div><label>Año *</label><input type="number" name="anio" value="<?= date('Y') ?>" required></div>
      <div><label>Mes (1-12) *</label><input type="number" min="1" max="12" name="mes" value="<?= date('n') ?>" required></div>
    </div>
    <br>
    <button class="btn" type="submit">Crear periodo</button>
    <a class="btn secondary" href="index.php?r=periodos">Cancelar</a>
  </form>
</div>
