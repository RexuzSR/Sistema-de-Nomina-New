<?php if (!empty($error)): ?>
  <div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="view-header-bar" style="margin-bottom:20px;">
  <div>
    <h2 style="margin:0;"><i class="fa-solid fa-user-plus" style="color:var(--primary); margin-right:8px;"></i> Registrar Nuevo Empleado</h2>
    <p style="margin:4px 0 0; color:var(--text-muted); font-size:13px;">Ingrese los datos del trabajador para darlo de alta en el sistema</p>
  </div>
  <a class="btn secondary" href="index.php?r=empleados"><i class="fa-solid fa-arrow-left"></i> Volver a la lista</a>
</div>

<div class="card">
  <form method="post" action="index.php?r=empleados/crear" enctype="multipart/form-data">
    <?= Security::csrfField() ?>
    <!-- WIDGET DE FOTO DE PERFIL -->
    <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
      <h3 style="margin:0; font-size:15px; color:var(--primary); display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-camera"></i> Foto de Perfil del Empleado
      </h3>
    </div>

    <div class="avatar-upload-card">
      <div class="avatar-preview-wrapper">
        <div class="avatar-preview-box" id="avatarPreviewBox">
          <i class="fa-solid fa-user" id="avatarFallbackIcon"></i>
        </div>
        <label for="fotoInput" class="avatar-camera-btn" title="Subir / Cambiar Foto">
          <i class="fa-solid fa-camera"></i>
        </label>
      </div>
      <div class="avatar-upload-info">
        <h4>Fotografía del Trabajador</h4>
        <p>Formato JPG, PNG o WEBP. Tamaño máximo recomendado: 5 MB.</p>
        <label for="fotoInput" class="btn-file-select">
          <i class="fa-solid fa-upload"></i> Seleccionar imagen...
        </label>
        <input type="file" name="foto" id="fotoInput" accept="image/png, image/jpeg, image/webp" style="display:none;" onchange="previewEmployeePhoto(this)">
      </div>
    </div>

    <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
      <h3 style="margin:0; font-size:15px; color:var(--primary); display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-address-card"></i> Datos Personales
      </h3>
    </div>
    <div class="form-grid" style="margin-bottom: 28px;">
      <div>
        <label for="inputCedula">Cédula de Identidad *</label>
        <div style="position:relative;">
          <input type="text" id="inputCedula" name="cedula" maxlength="11" inputmode="numeric" pattern="[0-9]*" value="<?= htmlspecialchars($_POST['cedula'] ?? '') ?>" placeholder="Ej: 30635942" required autocomplete="off" oninput="this.value = this.value.replace(/\D/g, '')" onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" style="padding-right:36px; transition:border-color 0.2s, box-shadow 0.2s;">
          <span id="cedulaStatusIcon" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); pointer-events:none; font-size:14px;"></span>
        </div>
        <div id="cedulaFeedback" style="display:none; margin-top:6px; font-size:12.5px; border-radius:8px; padding:7px 11px; align-items:flex-start; gap:8px; line-height:1.4; animation:fadeInCedula 0.2s ease;"></div>
      </div>
      <div>
        <label for="inputFechaIngreso">Fecha de Ingreso *</label>
        <input type="date" id="inputFechaIngreso" name="fecha_ingreso" max="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['fecha_ingreso'] ?? '') ?>" required style="transition:border-color 0.2s, box-shadow 0.2s;">
        <div id="fechaFeedback" style="display:none; margin-top:5px; font-size:12px; border-radius:6px; padding:5px 8px; align-items:center; gap:6px;"></div>
      </div>
      <div>
        <label for="inputPrimerNombre">Primer Nombre *</label>
        <input type="text" id="inputPrimerNombre" name="primer_nombre" class="input-solo-letras" maxlength="50" value="<?= htmlspecialchars($_POST['primer_nombre'] ?? '') ?>" placeholder="Ej: Juan" required autocomplete="off">
        <small class="letras-hint" style="display:none; color:var(--danger, #ef4444); font-size:11px; margin-top:3px;"><i class="fa-solid fa-triangle-exclamation"></i> Solo se permiten letras (sin números)</small>
      </div>
      <div>
        <label for="inputSegundoNombre">Segundo Nombre</label>
        <input type="text" id="inputSegundoNombre" name="segundo_nombre" class="input-solo-letras" maxlength="50" value="<?= htmlspecialchars($_POST['segundo_nombre'] ?? '') ?>" placeholder="Ej: Carlos" autocomplete="off">
        <small class="letras-hint" style="display:none; color:var(--danger, #ef4444); font-size:11px; margin-top:3px;"><i class="fa-solid fa-triangle-exclamation"></i> Solo se permiten letras (sin números)</small>
      </div>
      <div>
        <label for="inputPrimerApellido">Primer Apellido *</label>
        <input type="text" id="inputPrimerApellido" name="primer_apellido" class="input-solo-letras" maxlength="50" value="<?= htmlspecialchars($_POST['primer_apellido'] ?? '') ?>" placeholder="Ej: Pérez" required autocomplete="off">
        <small class="letras-hint" style="display:none; color:var(--danger, #ef4444); font-size:11px; margin-top:3px;"><i class="fa-solid fa-triangle-exclamation"></i> Solo se permiten letras (sin números)</small>
      </div>
      <div>
        <label for="inputSegundoApellido">Segundo Apellido</label>
        <input type="text" id="inputSegundoApellido" name="segundo_apellido" class="input-solo-letras" maxlength="50" value="<?= htmlspecialchars($_POST['segundo_apellido'] ?? '') ?>" placeholder="Ej: Gómez" autocomplete="off">
        <small class="letras-hint" style="display:none; color:var(--danger, #ef4444); font-size:11px; margin-top:3px;"><i class="fa-solid fa-triangle-exclamation"></i> Solo se permiten letras (sin números)</small>
      </div>
      <div class="full">
        <label for="inputEmail">Correo Electrónico</label>
        <input type="email" id="inputEmail" name="email" maxlength="100" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="ejemplo@empresa.com">
        <div id="emailFeedback" style="display:none; margin-top:5px; font-size:12px; border-radius:6px; padding:5px 8px; align-items:center; gap:6px;"></div>
      </div>
    </div>

    <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
      <h3 style="margin:0; font-size:15px; color:var(--primary); display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-briefcase"></i> Información Laboral y Salario
      </h3>
    </div>
    <div class="form-grid" style="margin-bottom: 28px;">
      <div>
        <label>Tipo de Empleado *</label>
        <select name="id_tipo_empleado" required>
          <option value="">-- Seleccionar Tipo --</option>
          <?php foreach ($tipos as $t): ?>
            <option value="<?= $t['id_tipo_empleado'] ?>" <?= (isset($_POST['id_tipo_empleado']) && $_POST['id_tipo_empleado'] == $t['id_tipo_empleado']) ? 'selected' : '' ?>><?= htmlspecialchars($t['nombre_tipo']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label>Jornada Laboral *</label>
        <select name="id_jornada" required>
          <option value="">-- Seleccionar Jornada --</option>
          <?php foreach ($jornadas as $j): ?>
            <option value="<?= $j['id_jornada'] ?>" <?= (isset($_POST['id_jornada']) && $_POST['id_jornada'] == $j['id_jornada']) ? 'selected' : '' ?>><?= htmlspecialchars($j['nombre_jornada']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label>Cargo *</label>
        <select name="id_cargo" required>
          <option value="">-- Seleccionar Cargo --</option>
          <?php foreach ($cargos as $c): ?>
            <option value="<?= $c['id_cargo'] ?>" <?= (isset($_POST['id_cargo']) && $_POST['id_cargo'] == $c['id_cargo']) ? 'selected' : '' ?>><?= htmlspecialchars($c['nombre_cargo']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="inputSueldo">Sueldo Mensual ($ USD) *</label>
        <input type="number" step="0.01" min="0.01" max="1000000" id="inputSueldo" name="sueldo_divisa" value="<?= htmlspecialchars($_POST['sueldo_divisa'] ?? '') ?>" placeholder="0.00" required>
        <div id="sueldoFeedback" style="display:none; margin-top:5px; font-size:12px; border-radius:6px; padding:5px 8px; align-items:center; gap:6px;"></div>
      </div>
    </div>

    <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
      <h3 style="margin:0; font-size:15px; color:var(--primary); display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-building-columns"></i> Información Bancaria
      </h3>
    </div>
    <div class="form-grid" style="margin-bottom: 28px;">
      <div>
        <label for="selectBanco">Entidad Bancaria</label>
        <select name="id_banco" id="selectBanco">
          <option value="" data-codigo="">-- Sin banco asignado --</option>
          <?php foreach ($bancos as $b): ?>
            <option value="<?= $b['id_banco'] ?>" data-codigo="<?= htmlspecialchars($b['codigo_banco'] ?? '') ?>" <?= (isset($_POST['id_banco']) && $_POST['id_banco'] == $b['id_banco']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($b['nombre_banco']) . (!empty($b['codigo_banco']) ? ' (' . htmlspecialchars($b['codigo_banco']) . ')' : '') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label>Tipo de Cuenta</label>
        <?php $tipoActual = $_POST['tipo_cuenta'] ?? 'Corriente'; ?>
        <select name="tipo_cuenta">
          <option value="Corriente" <?= $tipoActual === 'Corriente' ? 'selected' : '' ?>>Cuenta Corriente</option>
          <option value="Ahorro" <?= $tipoActual === 'Ahorro' ? 'selected' : '' ?>>Cuenta de Ahorro</option>
        </select>
      </div>
      <div class="full">
        <label for="inputNumeroCuenta">Número de Cuenta (Exactamente 20 Dígitos)</label>
        <input type="text" id="inputNumeroCuenta" name="numero_cuenta" maxlength="20" inputmode="numeric" placeholder="Ej: 01020123456789012345" 
               value="<?= htmlspecialchars($_POST['numero_cuenta'] ?? '') ?>" autocomplete="off" style="font-family:monospace; letter-spacing:1px; transition:border-color 0.2s, box-shadow 0.2s;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:5px; flex-wrap:wrap; gap:6px;">
          <small id="cuentaCounter" style="color:var(--text-muted); font-size:12px; font-weight:600;">0 / 20 dígitos</small>
          <small id="cuentaBancoMatch" style="font-size:12px; font-weight:600;"></small>
        </div>
        <div id="cuentaFeedback" style="display:none; margin-top:5px; font-size:12px; border-radius:6px; padding:5px 8px; align-items:center; gap:6px;"></div>
      </div>
    </div>

    <div style="display:flex; gap:12px; border-top:1px solid var(--border); padding-top:20px;">
      <button class="btn" type="submit" id="btnSubmitEmpleado"><i class="fa-solid fa-floppy-disk"></i> Guardar Empleado</button>
      <a class="btn secondary" href="index.php?r=empleados"><i class="fa-solid fa-xmark"></i> Cancelar</a>
    </div>
  </form>
</div>

<style>
@keyframes shakeInput {
  0%, 100% { transform: translateX(0); }
  20%, 60% { transform: translateX(-6px); }
  40%, 80% { transform: translateX(6px); }
}
@keyframes fadeInCedula {
  from { opacity: 0; transform: translateY(-4px); }
  to { opacity: 1; transform: translateY(0); }
}
.shake-error {
  animation: shakeInput 0.4s ease-in-out !important;
}
</style>

<script>
function previewEmployeePhoto(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      var box = document.getElementById('avatarPreviewBox');
      box.innerHTML = '<img src="' + e.target.result + '" alt="Vista Previa">';
    }
    reader.readAsDataURL(input.files[0]);
  }
}

// Validación visual e inmediata de cédula y formulario completo
(function() {
  const inputCedula = document.getElementById('inputCedula');
  const statusIcon = document.getElementById('cedulaStatusIcon');
  const feedback = document.getElementById('cedulaFeedback');
  const btnSubmit = document.getElementById('btnSubmitEmpleado');
  const form = inputCedula ? inputCedula.closest('form') : null;

  const inputFechaIngreso = document.getElementById('inputFechaIngreso');
  const fechaFeedback = document.getElementById('fechaFeedback');

  const inputsSoloLetras = document.querySelectorAll('.input-solo-letras');

  const selectBanco = document.getElementById('selectBanco');
  const inputNumeroCuenta = document.getElementById('inputNumeroCuenta');
  const cuentaCounter = document.getElementById('cuentaCounter');
  const cuentaBancoMatch = document.getElementById('cuentaBancoMatch');
  const cuentaFeedback = document.getElementById('cuentaFeedback');

  const inputSueldo = document.getElementById('inputSueldo');
  const sueldoFeedback = document.getElementById('sueldoFeedback');

  const inputEmail = document.getElementById('inputEmail');
  const emailFeedback = document.getElementById('emailFeedback');

  let debounceTimer = null;
  let isDuplicate = false;
  let lastCheckedValue = '';

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
  }

  function shakeElement(el) {
    if (!el) return;
    el.classList.remove('shake-error');
    void el.offsetWidth;
    el.classList.add('shake-error');
  }

  // --- 1. RESTRICCIÓN DE NOMBRES Y APELLIDOS (SOLO LETRAS, SIN NÚMEROS) ---
  inputsSoloLetras.forEach(function(inp) {
    const hint = inp.parentElement.querySelector('.letras-hint');
    let hintTimeout = null;

    inp.addEventListener('input', function() {
      const orig = this.value;
      const clean = orig.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s'-]/g, '');
      if (orig !== clean) {
        this.value = clean;
        if (hint) {
          hint.style.display = 'block';
          clearTimeout(hintTimeout);
          hintTimeout = setTimeout(() => { hint.style.display = 'none'; }, 2500);
        }
      }
    });
  });

  // --- 2. VALIDACIÓN DE FECHA DE INGRESO (NO FUTURA) ---
  function validarFechaIngreso() {
    if (!inputFechaIngreso) return true;
    const val = inputFechaIngreso.value.trim();
    if (!val) {
      if (fechaFeedback) fechaFeedback.style.display = 'none';
      inputFechaIngreso.style.borderColor = '';
      return true;
    }

    const today = new Date().toISOString().split('T')[0];
    if (val > today) {
      inputFechaIngreso.style.borderColor = 'var(--danger, #ef4444)';
      inputFechaIngreso.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.2)';
      if (fechaFeedback) {
        fechaFeedback.style.display = 'flex';
        fechaFeedback.style.background = '#fef2f2';
        fechaFeedback.style.border = '1px solid #fecaca';
        fechaFeedback.style.color = '#991b1b';
        fechaFeedback.innerHTML = '<i class="fa-solid fa-calendar-xmark" style="color:#ef4444;"></i> La fecha de ingreso no puede ser una fecha futura (máximo hoy).';
      }
      return false;
    } else {
      inputFechaIngreso.style.borderColor = '';
      inputFechaIngreso.style.boxShadow = '';
      if (fechaFeedback) fechaFeedback.style.display = 'none';
      return true;
    }
  }

  if (inputFechaIngreso) {
    inputFechaIngreso.addEventListener('change', validarFechaIngreso);
    inputFechaIngreso.addEventListener('blur', validarFechaIngreso);
  }

  // --- 3. VALIDACIÓN DE NÚMERO DE CUENTA (EXACTAMENTE 20 DÍGITOS) Y BANCO ---
  function actualizarCuentaBancaria() {
    if (!inputNumeroCuenta) return;
    inputNumeroCuenta.value = inputNumeroCuenta.value.replace(/\D/g, '').slice(0, 20);
    const num = inputNumeroCuenta.value;
    const len = num.length;

    if (cuentaCounter) {
      if (len === 20) {
        cuentaCounter.style.color = '#10b981';
        cuentaCounter.innerHTML = '<i class="fa-solid fa-circle-check"></i> 20 / 20 dígitos completos';
        inputNumeroCuenta.style.borderColor = 'var(--ok, #10b981)';
        inputNumeroCuenta.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.15)';
      } else if (len > 0) {
        cuentaCounter.style.color = '#d97706';
        cuentaCounter.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> ${len} / 20 dígitos (faltan ${20 - len})`;
        inputNumeroCuenta.style.borderColor = '#f59e0b';
        inputNumeroCuenta.style.boxShadow = '0 0 0 3px rgba(245, 158, 11, 0.15)';
      } else {
        cuentaCounter.style.color = 'var(--text-muted, #64748b)';
        cuentaCounter.textContent = '0 / 20 dígitos';
        inputNumeroCuenta.style.borderColor = '';
        inputNumeroCuenta.style.boxShadow = '';
      }
    }

    if (selectBanco && cuentaBancoMatch) {
      const selectedOpt = selectBanco.selectedOptions[0];
      const codBanco = selectedOpt ? (selectedOpt.getAttribute('data-codigo') || '') : '';
      if (codBanco && len >= 4) {
        const prefijo = num.substring(0, 4);
        if (prefijo === codBanco) {
          cuentaBancoMatch.style.color = '#10b981';
          cuentaBancoMatch.innerHTML = `<i class="fa-solid fa-check"></i> Código (${codBanco}) coincide`;
        } else {
          cuentaBancoMatch.style.color = '#d97706';
          cuentaBancoMatch.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> Prefijo ${prefijo} no coincide con el banco (${codBanco})`;
        }
      } else {
        cuentaBancoMatch.textContent = '';
      }
    }

    if (cuentaFeedback) {
      if (selectBanco && selectBanco.value && len === 0) {
        cuentaFeedback.style.display = 'flex';
        cuentaFeedback.style.background = '#fffbeb';
        cuentaFeedback.style.border = '1px solid #fde68a';
        cuentaFeedback.style.color = '#92400e';
        cuentaFeedback.innerHTML = '<i class="fa-solid fa-info-circle"></i> Has seleccionado un banco. Recuerda ingresar los 20 dígitos de la cuenta.';
      } else if (selectBanco && !selectBanco.value && len > 0) {
        cuentaFeedback.style.display = 'flex';
        cuentaFeedback.style.background = '#fef2f2';
        cuentaFeedback.style.border = '1px solid #fecaca';
        cuentaFeedback.style.color = '#991b1b';
        cuentaFeedback.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Selecciona la entidad bancaria a la que pertenece esta cuenta.';
      } else {
        cuentaFeedback.style.display = 'none';
      }
    }
  }

  if (inputNumeroCuenta) {
    inputNumeroCuenta.addEventListener('input', actualizarCuentaBancaria);
  }
  if (selectBanco) {
    selectBanco.addEventListener('change', actualizarCuentaBancaria);
  }

  // --- 4. VALIDACIÓN DE SUELDO ---
  if (inputSueldo) {
    inputSueldo.addEventListener('input', function() {
      const val = parseFloat(this.value);
      if (isNaN(val) || val <= 0) {
        this.style.borderColor = 'var(--danger, #ef4444)';
        if (sueldoFeedback) {
          sueldoFeedback.style.display = 'flex';
          sueldoFeedback.style.background = '#fef2f2';
          sueldoFeedback.style.border = '1px solid #fecaca';
          sueldoFeedback.style.color = '#991b1b';
          sueldoFeedback.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> El sueldo debe ser mayor a cero.';
        }
      } else {
        this.style.borderColor = '';
        if (sueldoFeedback) sueldoFeedback.style.display = 'none';
      }
    });
  }

  // --- 5. VALIDACIÓN DE CÉDULA DUPLICADA ---
  function resetCedulaFeedback() {
    if (!inputCedula) return;
    inputCedula.style.borderColor = '';
    inputCedula.style.boxShadow = '';
    inputCedula.classList.remove('shake-error');
    if (statusIcon) statusIcon.innerHTML = '';
    if (feedback) {
      feedback.style.display = 'none';
      feedback.innerHTML = '';
    }
    isDuplicate = false;
    if (btnSubmit) {
      btnSubmit.removeAttribute('disabled');
      btnSubmit.style.opacity = '';
      btnSubmit.style.cursor = '';
    }
  }

  function setCheckingCedula() {
    if (statusIcon) {
      statusIcon.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="color:var(--text-muted, #94a3b8);"></i>';
    }
  }

  function checkCedula(cedula) {
    const clean = cedula.trim();
    if (clean === '') {
      resetCedulaFeedback();
      lastCheckedValue = '';
      return;
    }

    if (clean === lastCheckedValue) return;
    lastCheckedValue = clean;

    setCheckingCedula();

    fetch('index.php?r=empleados/verificarCedula&cedula=' + encodeURIComponent(clean), {
      headers: { 'Accept': 'application/json' }
    })
      .then(response => response.json())
      .then(data => {
        if (data.taken) {
          isDuplicate = true;
          inputCedula.style.borderColor = 'var(--danger, #ef4444)';
          inputCedula.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.2)';
          shakeElement(inputCedula);

          if (statusIcon) {
            statusIcon.innerHTML = '<i class="fa-solid fa-circle-xmark" style="color:var(--danger, #ef4444);"></i>';
          }

          if (feedback) {
            feedback.style.display = 'flex';
            feedback.style.background = '#fef2f2';
            feedback.style.border = '1px solid #fecaca';
            feedback.style.color = '#991b1b';
            feedback.innerHTML = `
              <i class="fa-solid fa-triangle-exclamation" style="margin-top:2px; color:#ef4444; font-size:14px; flex-shrink:0;"></i>
              <div>
                <strong style="display:block; margin-bottom:2px;">¡Esta cédula ya ha sido registrada!</strong>
                Pertenece a <strong>${escapeHtml(data.nombre || 'otro empleado')}</strong>${data.estado ? ' (' + escapeHtml(data.estado) + ')' : ''}.
                <div style="margin-top:3px; font-size:11.5px; color:#dc2626;">No es posible registrar dos empleados con la misma cédula de identidad.</div>
              </div>
            `;
          }

          if (btnSubmit) {
            btnSubmit.setAttribute('disabled', 'disabled');
            btnSubmit.style.opacity = '0.55';
            btnSubmit.style.cursor = 'not-allowed';
          }
        } else {
          isDuplicate = false;
          inputCedula.classList.remove('shake-error');
          inputCedula.style.borderColor = 'var(--ok, #10b981)';
          inputCedula.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.15)';

          if (statusIcon) {
            statusIcon.innerHTML = '<i class="fa-solid fa-circle-check" style="color:var(--ok, #10b981);"></i>';
          }

          if (feedback) {
            feedback.style.display = 'flex';
            feedback.style.background = '#ecfdf5';
            feedback.style.border = '1px solid #a7f3d0';
            feedback.style.color = '#065f46';
            feedback.innerHTML = `
              <i class="fa-solid fa-check" style="margin-top:2px; color:#10b981; font-size:14px; flex-shrink:0;"></i>
              <div><strong>Cédula disponible:</strong> No se encuentra registrada en el sistema.</div>
            `;
          }

          if (btnSubmit) {
            btnSubmit.removeAttribute('disabled');
            btnSubmit.style.opacity = '';
            btnSubmit.style.cursor = '';
          }
        }
      })
      .catch(err => {
        console.error('Error al verificar cédula:', err);
        if (statusIcon) statusIcon.innerHTML = '';
      });
  }

  if (inputCedula) {
    // Bloquear cualquier tecla no numérica en tiempo real
    inputCedula.addEventListener('keypress', function(e) {
      if (!/[0-9]/.test(e.key) && e.key !== 'Enter') {
        e.preventDefault();
      }
    });

    // Limpiar caracteres al pegar
    inputCedula.addEventListener('paste', function(e) {
      e.preventDefault();
      const text = (e.clipboardData || window.clipboardData).getData('text') || '';
      const clean = text.replace(/\D/g, '').slice(0, 11);
      document.execCommand('insertText', false, clean);
    });

    inputCedula.addEventListener('input', function() {
      // Filtrar letras o símbolos residuales inmediatamente
      this.value = this.value.replace(/\D/g, '');
      clearTimeout(debounceTimer);
      const val = this.value.trim();
      if (val === '') {
        resetCedulaFeedback();
        return;
      }
      setCheckingCedula();
      debounceTimer = setTimeout(() => {
        checkCedula(val);
      }, 280);
    });

    inputCedula.addEventListener('blur', function() {
      this.value = this.value.replace(/\D/g, '');
      clearTimeout(debounceTimer);
      if (this.value.trim() !== '') {
        checkCedula(this.value.trim());
      }
    });
  }

  // --- 6. INTERCEPTAR ENVÍO DEL FORMULARIO Y VALIDACIÓN FINAL ---
  if (form) {
    form.addEventListener('submit', function(e) {
      if (isDuplicate) {
        e.preventDefault();
        shakeElement(inputCedula);
        inputCedula.focus();
        return false;
      }

      if (!validarFechaIngreso()) {
        e.preventDefault();
        shakeElement(inputFechaIngreso);
        inputFechaIngreso.focus();
        return false;
      }

      // Validar banco y cuenta
      const numCuenta = inputNumeroCuenta ? inputNumeroCuenta.value.trim() : '';
      const tieneBanco = selectBanco ? !!selectBanco.value : false;

      if (tieneBanco && numCuenta.length !== 20) {
        e.preventDefault();
        shakeElement(inputNumeroCuenta);
        inputNumeroCuenta.focus();
        if (cuentaFeedback) {
          cuentaFeedback.style.display = 'flex';
          cuentaFeedback.style.background = '#fef2f2';
          cuentaFeedback.style.border = '1px solid #fecaca';
          cuentaFeedback.style.color = '#991b1b';
          cuentaFeedback.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> El número de cuenta debe tener exactamente 20 dígitos numéricos.';
        }
        return false;
      }

      if (!tieneBanco && numCuenta.length > 0) {
        e.preventDefault();
        shakeElement(selectBanco);
        selectBanco.focus();
        if (cuentaFeedback) {
          cuentaFeedback.style.display = 'flex';
          cuentaFeedback.style.background = '#fef2f2';
          cuentaFeedback.style.border = '1px solid #fecaca';
          cuentaFeedback.style.color = '#991b1b';
          cuentaFeedback.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Debes seleccionar la entidad bancaria a la que pertenece la cuenta.';
        }
        return false;
      }
    });
  }

  // Inicialización
  if (inputCedula && inputCedula.value.trim() !== '') {
    checkCedula(inputCedula.value.trim());
  }
  validarFechaIngreso();
  actualizarCuentaBancaria();
})();
</script>
