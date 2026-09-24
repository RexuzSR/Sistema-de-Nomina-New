<div class="view-header-bar" style="margin-bottom:20px;">
  <div>
    <h2 style="margin:0;"><i class="fa-solid fa-calculator" style="color:var(--primary); margin-right:8px;"></i> Generar Nómina: <?= htmlspecialchars($periodo['tipo_periodo']) ?></h2>
    <p style="margin:4px 0 0; color:var(--text-muted); font-size:13px;">Ciclo del <?= htmlspecialchars($periodo['fecha_inicio']) ?> al <?= htmlspecialchars($periodo['fecha_fin']) ?></p>
  </div>
  <a class="btn secondary" href="index.php?r=periodos"><i class="fa-solid fa-arrow-left"></i> Volver a periodos</a>
</div>

<?php if (!empty($error)): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<?php if (!$ultimaTasa): ?>
  <div class="alert error">No hay ninguna tasa de cambio registrada. <a href="index.php?r=tasas/crear">Registra una aqui</a> antes de generar la nomina.</div>
<?php endif; ?>

<div class="card">
  <form method="post" action="index.php?r=nomina/generar" id="formGenerarNomina">
    <?= Security::csrfField() ?>
    <input type="hidden" name="id_periodo" value="<?= $id_periodo ?>">
    <div class="form-grid">
      <div>
        <label>Salario mínimo vigente *</label>
        <input type="number" step="0.01" min="0.01" max="1000000" id="inputSalarioMin" name="salario_minimo_vigente" value="<?= htmlspecialchars($sugerencias['salario_minimo_vigente']) ?>" required>
      </div>
      <div>
        <label>Semanas a descontar IVSS (# de lunes en el periodo) *</label>
        <input type="number" step="1" min="0" max="5" id="inputSemanasIvss" name="semanas_descontar_ivss" value="<?= htmlspecialchars($sugerencias['semanas_descontar_ivss']) ?>" required>
      </div>
      <div>
        <label>Factor de semanas por mes *</label>
        <input type="number" step="0.000001" min="1.0" max="6.0" id="inputFactorSem" name="factor_semanas_mes" value="<?= htmlspecialchars($sugerencias['factor_semanas_mes']) ?>" required>
      </div>
      <div>
        <label>Tasa S.S.O (%) *</label>
        <input type="number" step="0.01" min="0" max="50" id="inputTasaSso" name="tasa_sso" value="<?= htmlspecialchars($sugerencias['tasa_sso']) ?>" required>
      </div>
      <div>
        <label>Tasa R.P.E (%) *</label>
        <input type="number" step="0.01" min="0" max="50" id="inputTasaRpe" name="tasa_rpe" value="<?= htmlspecialchars($sugerencias['tasa_rpe']) ?>" required>
      </div>
      <div>
        <label>Tasa L.P.H (%) *</label>
        <input type="number" step="0.01" min="0" max="50" id="inputTasaLph" name="tasa_lph" value="<?= htmlspecialchars($sugerencias['tasa_lph']) ?>" required>
      </div>
    </div>
    <br>
    <button class="btn" type="submit" <?= !$ultimaTasa ? 'disabled' : '' ?>>Generar nómina para este periodo</button>
  </form>
</div>

<script>
document.getElementById('formGenerarNomina')?.addEventListener('submit', function(e) {
  const sal = parseFloat(document.getElementById('inputSalarioMin')?.value) || 0;
  const sem = parseFloat(document.getElementById('inputSemanasIvss')?.value);
  const factor = parseFloat(document.getElementById('inputFactorSem')?.value) || 0;

  if (sal <= 0 || sal > 1000000) {
    e.preventDefault();
    alert('El salario mínimo vigente debe ser mayor a 0 y menor a 1.000.000.');
    document.getElementById('inputSalarioMin')?.focus();
    return false;
  }
  if (isNaN(sem) || sem < 0 || sem > 5) {
    e.preventDefault();
    alert('Las semanas a descontar IVSS deben ubicarse entre 0 y 5.');
    document.getElementById('inputSemanasIvss')?.focus();
    return false;
  }
  if (factor <= 0 || factor > 6) {
    e.preventDefault();
    alert('El factor de semanas por mes debe ubicarse entre 1 y 6.');
    document.getElementById('inputFactorSem')?.focus();
    return false;
  }
});
</script>
