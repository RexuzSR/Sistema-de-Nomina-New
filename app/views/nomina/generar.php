<p><a href="index.php?r=periodos">&larr; Volver a periodos</a></p>
<h2><?= htmlspecialchars($periodo['tipo_periodo']) ?>: <?= htmlspecialchars($periodo['fecha_inicio']) ?> al <?= htmlspecialchars($periodo['fecha_fin']) ?></h2>

<?php if (!empty($error)): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<?php if (!$ultimaTasa): ?>
  <div class="alert error">No hay ninguna tasa de cambio registrada. <a href="index.php?r=tasas/crear">Registra una aqui</a> antes de generar la nomina.</div>
<?php endif; ?>

<div class="card">
  <form method="post" action="index.php?r=nomina/generar">
    <input type="hidden" name="id_periodo" value="<?= $id_periodo ?>">
    <div class="form-grid">
      <div>
        <label>Salario minimo vigente *</label>
        <input type="number" step="0.01" min="0" name="salario_minimo_vigente" required>
      </div>
      <div>
        <label>Semanas a descontar IVSS (# de lunes en el periodo) *</label>
        <input type="number" step="1" min="0" name="semanas_descontar_ivss" value="2" required>
      </div>
      <div>
        <label>Factor de semanas por mes *</label>
        <input type="number" step="0.000001" min="0.1" name="factor_semanas_mes" value="4.333333" required>
      </div>
      <div>
        <label>Tasa S.S.O (%) *</label>
        <input type="number" step="0.01" min="0" name="tasa_sso" value="4.0" required>
      </div>
      <div>
        <label>Tasa R.P.E (%) *</label>
        <input type="number" step="0.01" min="0" name="tasa_rpe" value="0.5" required>
      </div>
      <div>
        <label>Tasa L.P.H (%) *</label>
        <input type="number" step="0.01" min="0" name="tasa_lph" value="1.0" required>
      </div>
    </div>
    <br>
    <button class="btn" type="submit" <?= !$ultimaTasa ? 'disabled' : '' ?>>Generar nomina para este periodo</button>
  </form>
</div>
