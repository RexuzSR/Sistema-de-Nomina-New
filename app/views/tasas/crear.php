<?php if (!empty($error)): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="view-header-bar" style="margin-bottom:20px;">
  <div>
    <h2 style="margin:0;"><i class="fa-solid fa-coins" style="color:var(--primary); margin-right:8px;"></i> Registrar Tasa de Cambio Manual</h2>
    <p style="margin:4px 0 0; color:var(--text-muted); font-size:13px;">Ingrese el valor de cotización oficial BCV</p>
  </div>
  <a class="btn secondary" href="index.php?r=tasas"><i class="fa-solid fa-arrow-left"></i> Volver a tasas</a>
</div>

<div class="card">
  <form method="post" action="index.php?r=tasas/crear" id="formCrearTasa">
    <?= Security::csrfField() ?>
    <div class="form-grid">
      <div>
        <label for="inputFechaTasa">Fecha de la Tasa *</label>
        <input type="date" id="inputFechaTasa" name="fecha" max="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['fecha'] ?? date('Y-m-d')) ?>" required>
        <div id="fechaTasaFeedback" style="display:none; margin-top:5px; font-size:12px; color:var(--danger, #ef4444);"></div>
      </div>
      <div>
        <label for="inputValorTasa">Valor de la Tasa (Bs por USD) *</label>
        <input type="number" id="inputValorTasa" step="0.0001" min="0.0001" max="1000000" name="valor_tasa" value="<?= htmlspecialchars($_POST['valor_tasa'] ?? '') ?>" placeholder="Ej: 36.5000" required>
        <div id="valorTasaFeedback" style="display:none; margin-top:5px; font-size:12px; color:var(--danger, #ef4444);"></div>
      </div>
      <div class="full">
        <label for="inputFuenteTasa">Fuente Oficial</label>
        <input type="text" id="inputFuenteTasa" name="fuente" maxlength="50" value="<?= htmlspecialchars($_POST['fuente'] ?? 'BCV') ?>" placeholder="Ej: BCV">
      </div>
    </div>
    <br>
    <div style="display:flex; gap:12px;">
      <button class="btn" type="submit" id="btnGuardarTasa"><i class="fa-solid fa-floppy-disk"></i> Guardar Tasa</button>
      <a class="btn secondary" href="index.php?r=tasas"><i class="fa-solid fa-xmark"></i> Cancelar</a>
    </div>
  </form>
</div>

<script>
(function() {
  const form = document.getElementById('formCrearTasa');
  const fechaInput = document.getElementById('inputFechaTasa');
  const fechaFeedback = document.getElementById('fechaTasaFeedback');
  const valorInput = document.getElementById('inputValorTasa');
  const valorFeedback = document.getElementById('valorTasaFeedback');
  const today = '<?= date('Y-m-d') ?>';

  function validarFecha() {
    if (!fechaInput) return true;
    if (fechaInput.value > today) {
      fechaInput.style.borderColor = 'var(--danger, #ef4444)';
      fechaFeedback.style.display = 'block';
      fechaFeedback.innerHTML = '<i class="fa-solid fa-calendar-xmark"></i> La tasa no puede tener una fecha futura (máximo hoy ' + '<?= date('d/m/Y') ?>' + ').';
      return false;
    } else {
      fechaInput.style.borderColor = '';
      fechaFeedback.style.display = 'none';
      return true;
    }
  }

  function validarValor() {
    if (!valorInput) return true;
    const val = parseFloat(valorInput.value);
    if (isNaN(val) || val <= 0) {
      valorInput.style.borderColor = 'var(--danger, #ef4444)';
      valorFeedback.style.display = 'block';
      valorFeedback.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> El valor de la tasa debe ser mayor a 0.';
      return false;
    } else {
      valorInput.style.borderColor = '';
      valorFeedback.style.display = 'none';
      return true;
    }
  }

  if (fechaInput) {
    fechaInput.addEventListener('change', validarFecha);
    fechaInput.addEventListener('blur', validarFecha);
  }
  if (valorInput) {
    valorInput.addEventListener('input', validarValor);
  }

  if (form) {
    form.addEventListener('submit', function(e) {
      const okFecha = validarFecha();
      const okValor = validarValor();
      if (!okFecha || !okValor) {
        e.preventDefault();
        if (!okFecha && fechaInput) fechaInput.focus();
        else if (!okValor && valorInput) valorInput.focus();
      }
    });
  }
})();
</script>
