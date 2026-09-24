<?php if (!empty($error)): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="view-header-bar" style="margin-bottom:20px;">
  <div>
    <h2 style="margin:0;"><i class="fa-solid fa-file-signature" style="color:var(--primary); margin-right:8px;"></i> Solicitar o Registrar Permiso</h2>
    <p style="margin:4px 0 0; color:var(--text-muted); font-size:13px;">Ingrese el empleado y las fechas de ausencia</p>
  </div>
  <a class="btn secondary" href="index.php?r=permisos"><i class="fa-solid fa-arrow-left"></i> Volver a permisos</a>
</div>

<div class="card">
  <form method="post" action="index.php?r=permisos/solicitar" id="formSolicitarPermiso">
    <?= Security::csrfField() ?>
    <div class="form-grid">
      <div class="full">
        <label for="selectEmpleadoPermiso">Empleado *</label>
        <select id="selectEmpleadoPermiso" name="id_empleado" required>
          <option value="">-- Selecciona el empleado --</option>
          <?php foreach ($empleados as $e): ?>
            <option value="<?= $e['id_empleado'] ?>"><?= htmlspecialchars(trim(preg_replace('/\s+/', ' ', implode(' ', array_filter([$e['primer_nombre'], $e['segundo_nombre'] ?? '', $e['primer_apellido'], $e['segundo_apellido'] ?? '']))))) ?> (C.I. <?= htmlspecialchars($e['cedula']) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="inputPermisoInicio">Fecha de inicio *</label>
        <input type="date" id="inputPermisoInicio" name="fecha_inicio" value="<?= htmlspecialchars($_POST['fecha_inicio'] ?? date('Y-m-d')) ?>" required>
      </div>
      <div>
        <label for="inputPermisoFin">Fecha de fin *</label>
        <input type="date" id="inputPermisoFin" name="fecha_fin" value="<?= htmlspecialchars($_POST['fecha_fin'] ?? date('Y-m-d')) ?>" required>
        <div id="permisoFechasFeedback" style="display:none; margin-top:5px; font-size:12px; color:var(--danger, #ef4444);"></div>
      </div>
      <div class="full">
        <div id="badgeDiasSolicitados" style="display:none; background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:8px 12px; border-radius:8px; font-size:13px; font-weight:600; margin-bottom:12px; align-items:center; gap:8px;">
          <i class="fa-solid fa-calendar-days" style="color:#10b981;"></i>
          <span id="textoDiasCalculados">0 días solicitados</span>
        </div>
      </div>
      <div class="full">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
          <label for="textareaMotivo" style="margin:0;">Motivo de la ausencia *</label>
          <small id="motivoCounter" style="color:var(--text-muted); font-size:11.5px; font-weight:600;">0 / 500 caracteres</small>
        </div>
        <textarea id="textareaMotivo" name="motivo" rows="3" maxlength="500" placeholder="Especifique el motivo justificado del permiso o reposo médico..." required><?= htmlspecialchars($_POST['motivo'] ?? '') ?></textarea>
      </div>
    </div>
    <br>
    <div style="display:flex; gap:12px;">
      <button class="btn" type="submit" id="btnRegistrarPermiso"><i class="fa-solid fa-check"></i> Registrar permiso</button>
      <a class="btn secondary" href="index.php?r=permisos"><i class="fa-solid fa-xmark"></i> Cancelar</a>
    </div>
  </form>
</div>

<script>
(function() {
  const form = document.getElementById('formSolicitarPermiso');
  const inicioInput = document.getElementById('inputPermisoInicio');
  const finInput = document.getElementById('inputPermisoFin');
  const feedback = document.getElementById('permisoFechasFeedback');
  const badgeDias = document.getElementById('badgeDiasSolicitados');
  const textoDias = document.getElementById('textoDiasCalculados');
  const motivoArea = document.getElementById('textareaMotivo');
  const motivoCounter = document.getElementById('motivoCounter');

  function calcularDias() {
    if (!inicioInput || !finInput) return true;
    const iniVal = inicioInput.value;
    const finVal = finInput.value;

    if (!iniVal || !finVal) {
      if (badgeDias) badgeDias.style.display = 'none';
      if (feedback) feedback.style.display = 'none';
      finInput.style.borderColor = '';
      return true;
    }

    const dIni = new Date(iniVal);
    const dFin = new Date(finVal);

    if (dFin < dIni) {
      finInput.style.borderColor = 'var(--danger, #ef4444)';
      if (feedback) {
        feedback.style.display = 'block';
        feedback.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> La fecha de fin no puede ser anterior a la fecha de inicio.';
      }
      if (badgeDias) badgeDias.style.display = 'none';
      return false;
    } else {
      finInput.style.borderColor = '';
      if (feedback) feedback.style.display = 'none';

      const diffTime = Math.abs(dFin - dIni);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

      if (badgeDias && textoDias) {
        badgeDias.style.display = 'flex';
        textoDias.textContent = diffDays + (diffDays === 1 ? ' día calendario solicitado' : ' días calendario solicitados');
      }
      return true;
    }
  }

  function actualizarContadorMotivo() {
    if (!motivoArea || !motivoCounter) return;
    const len = motivoArea.value.length;
    motivoCounter.textContent = `${len} / 500 caracteres`;
    if (len > 450) {
      motivoCounter.style.color = '#ef4444';
    } else {
      motivoCounter.style.color = 'var(--text-muted, #64748b)';
    }
  }

  if (inicioInput) inicioInput.addEventListener('change', calcularDias);
  if (finInput) finInput.addEventListener('change', calcularDias);
  if (motivoArea) {
    motivoArea.addEventListener('input', actualizarContadorMotivo);
  }

  if (form) {
    form.addEventListener('submit', function(e) {
      if (!calcularDias()) {
        e.preventDefault();
        finInput.focus();
        return;
      }
      if (motivoArea && motivoArea.value.trim().length < 3) {
        e.preventDefault();
        motivoArea.focus();
      }
    });
  }

  calcularDias();
  actualizarContadorMotivo();
})();
</script>
