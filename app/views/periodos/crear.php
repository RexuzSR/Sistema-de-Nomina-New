<?php if (!empty($error)): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="view-header-bar" style="margin-bottom:20px;">
  <div>
    <h2 style="margin:0;"><i class="fa-solid fa-calendar-plus" style="color:var(--primary); margin-right:8px;"></i> Crear Periodo de Nómina</h2>
    <p style="margin:4px 0 0; color:var(--text-muted); font-size:13px;">Defina el rango de fechas y tipo de ciclo de pago</p>
  </div>
  <a class="btn secondary" href="index.php?r=periodos"><i class="fa-solid fa-arrow-left"></i> Volver a periodos</a>
</div>

<div class="card">
  <form method="post" action="index.php?r=periodos/crear" id="formCrearPeriodo">
    <?= Security::csrfField() ?>
    <div class="form-grid">
      <div class="full">
        <label for="inputDescPeriodo">Nombre descriptivo / Descripción</label>
        <input type="text" id="inputDescPeriodo" name="descripcion" maxlength="100" placeholder="Ej: 1ra Quincena Mayo 2026" value="<?= htmlspecialchars($sugerencias['descripcion']) ?>">
      </div>
      <div>
        <label for="selectTipoPeriodo">Tipo de periodo *</label>
        <select id="selectTipoPeriodo" name="tipo_periodo" required>
          <option value="Quincenal" <?= $sugerencias['tipo_periodo'] === 'Quincenal' ? 'selected' : '' ?>>Quincenal</option>
          <option value="Semanal" <?= $sugerencias['tipo_periodo'] === 'Semanal' ? 'selected' : '' ?>>Semanal</option>
          <option value="Mensual" <?= $sugerencias['tipo_periodo'] === 'Mensual' ? 'selected' : '' ?>>Mensual</option>
        </select>
      </div>
      <div id="wrapNumeroQuincena">
        <label for="inputNumeroQuincena">Número de quincena (1 o 2)</label>
        <input type="number" id="inputNumeroQuincena" min="1" max="2" name="numero_quincena" value="<?= htmlspecialchars($sugerencias['numero_quincena']) ?>">
      </div>
      <div>
        <label for="inputFechaInicio">Fecha de inicio *</label>
        <input type="date" id="inputFechaInicio" name="fecha_inicio" value="<?= htmlspecialchars($sugerencias['fecha_inicio']) ?>" required>
      </div>
      <div>
        <label for="inputFechaFin">Fecha de fin *</label>
        <input type="date" id="inputFechaFin" name="fecha_fin" value="<?= htmlspecialchars($sugerencias['fecha_fin']) ?>" required>
        <div id="periodoFechasFeedback" style="display:none; margin-top:5px; font-size:12px; color:var(--danger, #ef4444);"></div>
      </div>
      <div>
        <label for="inputAnio">Año *</label>
        <input type="number" id="inputAnio" min="2000" max="2100" name="anio" value="<?= htmlspecialchars($sugerencias['anio']) ?>" required>
      </div>
      <div>
        <label for="inputMes">Mes (1-12) *</label>
        <input type="number" id="inputMes" min="1" max="12" name="mes" value="<?= htmlspecialchars($sugerencias['mes']) ?>" required>
      </div>
    </div>

    <!-- Indicador visual interactivo de días del periodo -->
    <div id="diasPeriodoBadge" style="margin-top:14px; padding:10px 14px; border-radius:8px; background:var(--bg-card, #f0fdf4); border:1px solid #bbf7d0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
      <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:#166534;" id="diasPeriodoTexto">
        <i class="fa-solid fa-clock-rotate-left"></i>
        <span>Duración del periodo: <strong>15 días</strong></span>
      </div>
      <button type="button" class="btn small secondary" id="btnAutoAjustar15" style="display:none; font-size:12px; padding:4px 10px;" onclick="autoCalcularFechas15(true)">
        <i class="fa-solid fa-wand-magic-sparkles"></i> Ajustar a 15 días exactos
      </button>
    </div>

    <br>
    <div style="display:flex; gap:12px;">
      <button class="btn" type="submit" id="btnCrearPeriodo"><i class="fa-solid fa-calendar-check"></i> Crear periodo</button>
      <a class="btn secondary" href="index.php?r=periodos"><i class="fa-solid fa-xmark"></i> Cancelar</a>
    </div>
  </form>
</div>

<script>
(function() {
  const form = document.getElementById('formCrearPeriodo');
  const tipoSelect = document.getElementById('selectTipoPeriodo');
  const quincenaWrap = document.getElementById('wrapNumeroQuincena');
  const quincenaInput = document.getElementById('inputNumeroQuincena');
  const inicioInput = document.getElementById('inputFechaInicio');
  const finInput = document.getElementById('inputFechaFin');
  const anioInput = document.getElementById('inputAnio');
  const mesInput = document.getElementById('inputMes');
  const descInput = document.getElementById('inputDescPeriodo');
  const feedback = document.getElementById('periodoFechasFeedback');
  const badgeWrap = document.getElementById('diasPeriodoBadge');
  const badgeTexto = document.getElementById('diasPeriodoTexto');
  const btnAjustar = document.getElementById('btnAutoAjustar15');

  const mesesNom = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

  let descManual = false;
  if (descInput) {
    descInput.addEventListener('input', () => { descManual = true; });
  }

  window.autoCalcularFechas15 = function(forzar = false) {
    const tipo = tipoSelect ? tipoSelect.value : 'Quincenal';
    const anio = parseInt(anioInput.value, 10) || (new Date()).getFullYear();
    const mes = parseInt(mesInput.value, 10) || ((new Date()).getMonth() + 1);
    const quincena = parseInt(quincenaInput.value, 10) || 1;

    if (tipo === 'Quincenal') {
      const mesStr = String(mes).padStart(2, '0');
      if (quincena === 1) {
        inicioInput.value = `${anio}-${mesStr}-01`;
        finInput.value = `${anio}-${mesStr}-15`;
        if (!descManual || forzar) {
          descInput.value = `1ra Quincena ${mesesNom[mes] || ''} ${anio}`;
        }
      } else {
        inicioInput.value = `${anio}-${mesStr}-16`;
        // Último día del mes
        const ultimoDia = new Date(anio, mes, 0).getDate();
        finInput.value = `${anio}-${mesStr}-${ultimoDia}`;
        if (!descManual || forzar) {
          descInput.value = `2da Quincena ${mesesNom[mes] || ''} ${anio}`;
        }
      }
    } else if (tipo === 'Mensual') {
      const mesStr = String(mes).padStart(2, '0');
      inicioInput.value = `${anio}-${mesStr}-01`;
      const ultimoDia = new Date(anio, mes, 0).getDate();
      finInput.value = `${anio}-${mesStr}-${ultimoDia}`;
      if (!descManual || forzar) {
        descInput.value = `Mes completo ${mesesNom[mes] || ''} ${anio}`;
      }
    }

    validarRangoFechas();
  };

  function ajustarTipo() {
    if (!tipoSelect || !quincenaWrap || !quincenaInput) return;
    if (tipoSelect.value === 'Quincenal') {
      quincenaWrap.style.opacity = '1';
      quincenaInput.removeAttribute('disabled');
      if (!quincenaInput.value) quincenaInput.value = '1';
    } else {
      quincenaWrap.style.opacity = '0.4';
      quincenaInput.setAttribute('disabled', 'disabled');
      quincenaInput.value = '';
    }
    autoCalcularFechas15();
  }

  function validarRangoFechas() {
    if (!inicioInput || !finInput) return true;
    const ini = inicioInput.value;
    const fin = finInput.value;

    if (!ini || !fin) return true;

    if (fin < ini) {
      finInput.style.borderColor = 'var(--danger, #ef4444)';
      if (feedback) {
        feedback.style.display = 'block';
        feedback.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> La fecha de fin no puede ser anterior a la fecha de inicio.';
      }
      if (badgeWrap) badgeWrap.style.display = 'none';
      return false;
    }

    const tIni = new Date(ini + 'T00:00:00');
    const tFin = new Date(fin + 'T00:00:00');
    const diffDias = Math.round((tFin - tIni) / (1000 * 60 * 60 * 24)) + 1;

    const tipo = tipoSelect ? tipoSelect.value : 'Quincenal';

    if (badgeWrap && badgeTexto) {
      badgeWrap.style.display = 'flex';
      if (tipo === 'Quincenal') {
        if (diffDias >= 13 && diffDias <= 16) {
          badgeWrap.style.background = '#f0fdf4';
          badgeWrap.style.borderColor = '#bbf7d0';
          badgeTexto.style.color = '#166534';
          badgeTexto.innerHTML = `<i class="fa-solid fa-circle-check"></i> Duración del ciclo: <strong>${diffDias} días</strong> (abarca la quincena completa).`;
          btnAjustar.style.display = 'none';
          finInput.style.borderColor = '';
          if (feedback) feedback.style.display = 'none';
        } else {
          badgeWrap.style.background = '#fef2f2';
          badgeWrap.style.borderColor = '#fecaca';
          badgeTexto.style.color = '#991b1b';
          badgeTexto.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> Duración actual: <strong>${diffDias} días</strong>. Una quincena debe abarcar 15 días (ej: del 1 al 15, o del 16 al fin de mes).`;
          btnAjustar.style.display = 'inline-flex';
          finInput.style.borderColor = 'var(--danger, #ef4444)';
          if (feedback) {
            feedback.style.display = 'block';
            feedback.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> El periodo abarca solo ${diffDias} días en lugar de los 15 días de la quincena.`;
          }
        }
      } else {
        badgeWrap.style.background = '#f0fdf4';
        badgeWrap.style.borderColor = '#bbf7d0';
        badgeTexto.style.color = '#166534';
        badgeTexto.innerHTML = `<i class="fa-solid fa-circle-check"></i> Duración del ciclo: <strong>${diffDias} días</strong>.`;
        btnAjustar.style.display = 'none';
        finInput.style.borderColor = '';
        if (feedback) feedback.style.display = 'none';
      }
    }

    return true;
  }

  if (tipoSelect) tipoSelect.addEventListener('change', ajustarTipo);
  if (quincenaInput) quincenaInput.addEventListener('change', () => autoCalcularFechas15());
  if (anioInput) anioInput.addEventListener('change', () => autoCalcularFechas15());
  if (mesInput) mesInput.addEventListener('change', () => autoCalcularFechas15());
  if (inicioInput) inicioInput.addEventListener('change', validarRangoFechas);
  if (finInput) finInput.addEventListener('change', validarRangoFechas);

  if (form) {
    form.addEventListener('submit', function(e) {
      if (!validarRangoFechas()) {
        e.preventDefault();
        finInput.focus();
        return false;
      }
      const tipo = tipoSelect ? tipoSelect.value : 'Quincenal';
      const tIni = new Date(inicioInput.value + 'T00:00:00');
      const tFin = new Date(finInput.value + 'T00:00:00');
      const diffDias = Math.round((tFin - tIni) / (1000 * 60 * 60 * 24)) + 1;
      if (tipo === 'Quincenal' && (diffDias < 13 || diffDias > 16)) {
        if (!confirm(`Atención: La quincena seleccionada abarca solo ${diffDias} días en lugar de los 15 días reglamentarios. ¿Deseas crearla de todos modos?`)) {
          e.preventDefault();
          autoCalcularFechas15(true);
          return false;
        }
      }
    });
  }

  ajustarTipo();
  validarRangoFechas();
})();
</script>
