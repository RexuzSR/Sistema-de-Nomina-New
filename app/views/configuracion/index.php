<?php if (!empty($msg)): ?>
  <div class="alert success" style="margin-bottom:20px;">
    <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($msg) ?>
  </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
  <div class="alert error" style="margin-bottom:20px;">
    <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
  </div>
<?php endif; ?>

<div class="config-page">
  <!-- ENCABEZADO EJECUTIVO -->
  <div class="view-header-bar" style="margin-bottom:0;">
    <div>
      <h2 style="margin:0;"><i class="fa-solid fa-sliders" style="color:var(--primary); margin-right:8px;"></i> Configuraciones Generales de Nómina</h2>
      <p style="margin:4px 0 0; color:var(--text-muted); font-size:13px;">Parámetros legales de empresa (SSO, RPE, FAOV, Régimen de Riesgo y Topes de Cotización)</p>
    </div>
    <a class="btn secondary" href="index.php?r=dashboard"><i class="fa-solid fa-arrow-left"></i> Volver al Inicio</a>
  </div>

  <form method="POST" action="index.php?r=configuracion/guardar" id="formConfiguracion">
    <?= Security::csrfField() ?>

    <!-- TARJETA 1: DATOS GENERALES DE LA EMPRESA -->
    <div class="card" style="margin-bottom:24px;">
      <div style="margin-bottom: 18px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
        <h3 style="margin:0; font-size:15px; color:var(--primary); display:flex; align-items:center; gap:8px;">
          <i class="fa-solid fa-building"></i> Identificación de la Empresa
        </h3>
      </div>
      <div class="form-grid">
        <div class="full">
          <label>Razón Social o Nombre de la Empresa *</label>
          <input type="text" name="nombre_empresa" value="<?= htmlspecialchars($config['nombre_empresa'] ?? '') ?>" required placeholder="Ej: EMPRESA DEMO C.A.">
        </div>
        <div>
          <label>Número de R.I.F. *</label>
          <input type="text" name="rif" value="<?= htmlspecialchars($config['rif'] ?? '') ?>" required placeholder="Ej: J-12345678-9">
        </div>
        <div>
          <label>Jurisdicción / Localidad</label>
          <input type="text" value="Cumaná, Estado Sucre (Venezuela)" disabled style="opacity:0.75; cursor:not-allowed;">
        </div>
      </div>
    </div>

    <!-- TARJETA 2: CLASIFICACIÓN DE RIESGO DE LA EMPRESA (IVSS) -->
    <div class="card" style="margin-bottom:24px;">
      <div style="margin-bottom: 18px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
        <h3 style="margin:0; font-size:15px; color:var(--primary); display:flex; align-items:center; gap:8px;">
          <i class="fa-solid fa-shield-halved"></i> Clasificación de Riesgo de la Empresa (Régimen IVSS)
        </h3>
        <p style="margin:4px 0 0; font-size:12.5px; color:var(--text-muted);">
          Seleccione el régimen de riesgo asignado a su empresa para auto-completar los porcentajes oficiales de SSO y RPE vigentes en Venezuela y Cumaná.
        </p>
      </div>

      <input type="hidden" name="regimen_ivss" id="inputRegimenIvss" value="<?= htmlspecialchars($config['regimen_ivss'] ?? 'Clase_II') ?>">

      <div class="regimen-selector-grid">
        <!-- CLASE I -->
        <div class="regimen-card <?= ($config['regimen_ivss'] ?? '') === 'Clase_I' ? 'active' : '' ?>" onclick="seleccionarRegimen('Clase_I')" id="card_Clase_I">
          <div class="regimen-header">
            <span class="regimen-badge badge-minimo">Clase I</span>
            <i class="fa-solid fa-circle-check" style="color:var(--primary); display: <?= ($config['regimen_ivss'] ?? '') === 'Clase_I' ? 'block' : 'none' ?>;" id="check_Clase_I"></i>
          </div>
          <h4 class="regimen-title">Riesgo Mínimo</h4>
          <p class="regimen-desc">Comercio, oficinas, servicios de baja exposición a siniestros.</p>
          <div class="regimen-rates-mini">
            <span>SSO: <strong>9,0%</strong> / 4,0%</span>
            <span>RPE: <strong>2,0%</strong> / 0,5%</span>
          </div>
        </div>

        <!-- CLASE II -->
        <div class="regimen-card <?= ($config['regimen_ivss'] ?? 'Clase_II') === 'Clase_II' ? 'active' : '' ?>" onclick="seleccionarRegimen('Clase_II')" id="card_Clase_II">
          <div class="regimen-header">
            <span class="regimen-badge badge-medio">Clase II (Habitual)</span>
            <i class="fa-solid fa-circle-check" style="color:var(--primary); display: <?= ($config['regimen_ivss'] ?? 'Clase_II') === 'Clase_II' ? 'block' : 'none' ?>;" id="check_Clase_II"></i>
          </div>
          <h4 class="regimen-title">Riesgo Medio</h4>
          <p class="regimen-desc">Manufactura ligera, transporte regular, talleres y servicios generales.</p>
          <div class="regimen-rates-mini">
            <span>SSO: <strong>10,0%</strong> / 4,0%</span>
            <span>RPE: <strong>2,1%</strong> / 0,5%</span>
          </div>
        </div>

        <!-- CLASE III -->
        <div class="regimen-card <?= ($config['regimen_ivss'] ?? '') === 'Clase_III' ? 'active' : '' ?>" onclick="seleccionarRegimen('Clase_III')" id="card_Clase_III">
          <div class="regimen-header">
            <span class="regimen-badge badge-maximo">Clase III</span>
            <i class="fa-solid fa-circle-check" style="color:var(--primary); display: <?= ($config['regimen_ivss'] ?? '') === 'Clase_III' ? 'block' : 'none' ?>;" id="check_Clase_III"></i>
          </div>
          <h4 class="regimen-title">Riesgo Máximo</h4>
          <p class="regimen-desc">Construcción, minería, petroquímica, metalúrgica y alto peligro.</p>
          <div class="regimen-rates-mini">
            <span>SSO: <strong>11,0%</strong> / 4,0%</span>
            <span>RPE: <strong>2,2%</strong> / 0,5%</span>
          </div>
        </div>
      </div>
    </div>

    <!-- TARJETA 3: TABLA DE ALÍCUOTAS DE LEY (EMPLEADO Y PATRONO) -->
    <div class="card" style="margin-bottom:24px;">
      <div style="margin-bottom: 18px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
        <h3 style="margin:0; font-size:15px; color:var(--primary); display:flex; align-items:center; gap:8px;">
          <i class="fa-solid fa-scale-balanced"></i> Alícuotas y Tasas de Retención Legal
        </h3>
        <p style="margin:4px 0 0; font-size:12.5px; color:var(--text-muted);">
          Estas tasas son globales para la empresa y se aplican a todos los trabajadores en cada cálculo de nómina. Puede editarlas manualmente si posee un régimen especial.
        </p>
      </div>

      <div class="law-table-wrap">
        <table class="law-table">
          <thead>
            <tr>
              <th>Concepto de Ley</th>
              <th style="text-align:right;">Retención Trabajador (%)</th>
              <th style="text-align:right;">Aporte Patrono (%)</th>
              <th style="text-align:right;">Total Aporte Seguridad Social</th>
            </tr>
          </thead>
          <tbody>
            <!-- SSO -->
            <tr>
              <td>
                <div class="law-concept-title">
                  <i class="fa-solid fa-heart-pulse" style="color:#ef4444;"></i>
                  <span>S.S.O. (Seguro Social Obligatorio - IVSS)</span>
                </div>
                <div class="law-concept-sub">Asistencia médica, pensiones y subsidios por incapacidad temporal.</div>
              </td>
              <td style="text-align:right;">
                <div class="pct-input-wrap">
                  <input type="number" step="0.01" min="0" max="100" name="tasa_sso_trabajador" id="inputSsoTrab" 
                         value="<?= number_format((float)($config['tasa_sso_trabajador'] ?? 0.04) * 100, 2, '.', '') ?>" oninput="actualizarTotalesLey()">
                  <span class="pct-symbol">%</span>
                </div>
              </td>
              <td style="text-align:right;">
                <div class="pct-input-wrap">
                  <input type="number" step="0.01" min="0" max="100" name="tasa_sso_patronal" id="inputSsoPat" 
                         value="<?= number_format((float)($config['tasa_sso_patronal'] ?? 0.10) * 100, 2, '.', '') ?>" oninput="actualizarTotalesLey()">
                  <span class="pct-symbol">%</span>
                </div>
              </td>
              <td style="text-align:right; font-weight:700; color:var(--primary);" id="totalSso">
                <?= number_format(((float)($config['tasa_sso_trabajador'] ?? 0.04) + (float)($config['tasa_sso_patronal'] ?? 0.10)) * 100, 2, ',', '.') ?>%
              </td>
            </tr>

            <!-- RPE -->
            <tr>
              <td>
                <div class="law-concept-title">
                  <i class="fa-solid fa-user-shield" style="color:#3b82f6;"></i>
                  <span>R.P.E. (Régimen Prestacional de Empleo / Paro Forzoso)</span>
                </div>
                <div class="law-concept-sub">Protección económica al trabajador cesante o desempleado.</div>
              </td>
              <td style="text-align:right;">
                <div class="pct-input-wrap">
                  <input type="number" step="0.01" min="0" max="100" name="tasa_rpe_trabajador" id="inputRpeTrab" 
                         value="<?= number_format((float)($config['tasa_rpe_trabajador'] ?? 0.005) * 100, 2, '.', '') ?>" oninput="actualizarTotalesLey()">
                  <span class="pct-symbol">%</span>
                </div>
              </td>
              <td style="text-align:right;">
                <div class="pct-input-wrap">
                  <input type="number" step="0.01" min="0" max="100" name="tasa_rpe_patronal" id="inputRpePat" 
                         value="<?= number_format((float)($config['tasa_rpe_patronal'] ?? 0.021) * 100, 2, '.', '') ?>" oninput="actualizarTotalesLey()">
                  <span class="pct-symbol">%</span>
                </div>
              </td>
              <td style="text-align:right; font-weight:700; color:var(--primary);" id="totalRpe">
                <?= number_format(((float)($config['tasa_rpe_trabajador'] ?? 0.005) + (float)($config['tasa_rpe_patronal'] ?? 0.021)) * 100, 2, ',', '.') ?>%
              </td>
            </tr>

            <!-- FAOV -->
            <tr>
              <td>
                <div class="law-concept-title">
                  <i class="fa-solid fa-house-chimney" style="color:#10b981;"></i>
                  <span>F.A.O.V. / L.P.H. (Fondo de Ahorro Obligatorio de Vivienda)</span>
                </div>
                <div class="law-concept-sub">Aporte al Banavih para adquisición y mejoras de vivienda habitacional.</div>
              </td>
              <td style="text-align:right;">
                <div class="pct-input-wrap">
                  <input type="number" step="0.01" min="0" max="100" name="tasa_faov_trabajador" id="inputFaovTrab" 
                         value="<?= number_format((float)($config['tasa_faov_trabajador'] ?? 0.01) * 100, 2, '.', '') ?>" oninput="actualizarTotalesLey()">
                  <span class="pct-symbol">%</span>
                </div>
              </td>
              <td style="text-align:right;">
                <div class="pct-input-wrap">
                  <input type="number" step="0.01" min="0" max="100" name="tasa_faov_patronal" id="inputFaovPat" 
                         value="<?= number_format((float)($config['tasa_faov_patronal'] ?? 0.02) * 100, 2, '.', '') ?>" oninput="actualizarTotalesLey()">
                  <span class="pct-symbol">%</span>
                </div>
              </td>
              <td style="text-align:right; font-weight:700; color:var(--primary);" id="totalFaov">
                <?= number_format(((float)($config['tasa_faov_trabajador'] ?? 0.01) + (float)($config['tasa_faov_patronal'] ?? 0.02)) * 100, 2, ',', '.') ?>%
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TARJETA 4: TOPES LEGALES (MÍNIMO Y MÁXIMO) Y SALARIO MÍNIMO -->
    <div class="card" style="margin-bottom:24px;">
      <div style="margin-bottom: 18px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
        <h3 style="margin:0; font-size:15px; color:var(--primary); display:flex; align-items:center; gap:8px;">
          <i class="fa-solid fa-arrows-up-down"></i> Límites de Cotización Legal (Mínimo y Máximo)
        </h3>
        <p style="margin:4px 0 0; font-size:12.5px; color:var(--text-muted);">
          Bases legales para acotar el salario cotizable de cada trabajador (Art. 107 del Reglamento General de la Ley del Seguro Social).
        </p>
      </div>

      <div class="form-grid" style="margin-bottom: 20px;">
        <div>
          <label>Salario Mínimo Legal Vigente (Referencial) *</label>
          <input type="number" step="0.01" min="0.01" name="salario_minimo_legal" value="<?= htmlspecialchars($config['salario_minimo_legal'] ?? '130.00') ?>" required>
          <small style="color:var(--text-muted); font-size:11.5px; display:block; margin-top:4px;">
            Constituye el <strong>piso mínimo</strong> de cotización para SSO y RPE.
          </small>
        </div>

        <div>
          <label>Tope Máximo de Cotización SSO (Número de Salarios Mínimos) *</label>
          <input type="number" min="1" max="20" name="tope_sso_salarios" value="<?= htmlspecialchars($config['tope_sso_salarios'] ?? 5) ?>" required>
          <small style="color:var(--text-muted); font-size:11.5px; display:block; margin-top:4px;">
            Fijado legalmente en <strong>5 Salarios Mínimos</strong>.
          </small>
        </div>

        <div>
          <label>Tope Máximo de Cotización RPE (Número de Salarios Mínimos) *</label>
          <input type="number" min="1" max="20" name="tope_rpe_salarios" value="<?= htmlspecialchars($config['tope_rpe_salarios'] ?? 5) ?>" required>
          <small style="color:var(--text-muted); font-size:11.5px; display:block; margin-top:4px;">
            Fijado legalmente en <strong>5 Salarios Mínimos</strong>.
          </small>
        </div>

        <div>
          <label>Factor de Semanas por Año *</label>
          <input type="number" min="50" max="54" name="semanas_factor_anual" value="<?= htmlspecialchars($config['semanas_factor_anual'] ?? 52) ?>" required>
          <small style="color:var(--text-muted); font-size:11.5px; display:block; margin-top:4px;">
            Estándar legal de cálculo: <strong>52 semanas / año</strong>.
          </small>
        </div>
      </div>

      <div class="info-callout">
        <i class="fa-solid fa-circle-info"></i>
        <div>
          <strong>¿Cómo se aplican estos topes en el cálculo de nómina?</strong><br>
          Para cada empleado, su salario mensual se compara contra los límites:
          si gana menos de 1 Salario Mínimo, se cotiza sobre 1 Salario Mínimo;
          si gana más de 5 Salarios Mínimos, el excedente no cotiza y se topa a 5 Salarios Mínimos.
          Sobre ese salario acotado se aplica la fórmula legal <code>(Sueldo Acotado × 12 ÷ 52) × %Tasa × Semanas</code>.
        </div>
      </div>
    </div>

    <!-- BOTONES DE ACCIÓN -->
    <div style="display:flex; gap:14px; align-items:center;">
      <button class="btn" type="submit">
        <i class="fa-solid fa-floppy-disk"></i> Guardar Configuraciones Generales
      </button>
      <a class="btn secondary" href="index.php?r=dashboard">
        <i class="fa-solid fa-xmark"></i> Cancelar
      </a>
    </div>
  </form>
</div>

<script>
const PRESETS = <?= json_encode(ConfiguracionModel::getRegimenesPredefinidos()) ?>;

function seleccionarRegimen(clase) {
  document.getElementById('inputRegimenIvss').value = clase;

  // Actualizar tarjetas visuales
  ['Clase_I', 'Clase_II', 'Clase_III'].forEach(c => {
    const card = document.getElementById('card_' + c);
    const check = document.getElementById('check_' + c);
    if (card) {
      if (c === clase) {
        card.classList.add('active');
        if (check) check.style.display = 'block';
      } else {
        card.classList.remove('active');
        if (check) check.style.display = 'none';
      }
    }
  });

  // Si existe el preset, autocompletar valores
  if (PRESETS[clase]) {
    const p = PRESETS[clase];
    document.getElementById('inputSsoTrab').value = (p.sso_trabajador * 100).toFixed(2);
    document.getElementById('inputSsoPat').value = (p.sso_patronal * 100).toFixed(2);
    document.getElementById('inputRpeTrab').value = (p.rpe_trabajador * 100).toFixed(2);
    document.getElementById('inputRpePat').value = (p.rpe_patronal * 100).toFixed(2);
    document.getElementById('inputFaovTrab').value = (p.faov_trabajador * 100).toFixed(2);
    document.getElementById('inputFaovPat').value = (p.faov_patronal * 100).toFixed(2);
    actualizarTotalesLey();
  }
}

function actualizarTotalesLey() {
  const ssoTrab = parseFloat(document.getElementById('inputSsoTrab')?.value) || 0;
  const ssoPat = parseFloat(document.getElementById('inputSsoPat')?.value) || 0;
  const rpeTrab = parseFloat(document.getElementById('inputRpeTrab')?.value) || 0;
  const rpePat = parseFloat(document.getElementById('inputRpePat')?.value) || 0;
  const faovTrab = parseFloat(document.getElementById('inputFaovTrab')?.value) || 0;
  const faovPat = parseFloat(document.getElementById('inputFaovPat')?.value) || 0;

  const totalSsoEl = document.getElementById('totalSso');
  if (totalSsoEl) totalSsoEl.textContent = (ssoTrab + ssoPat).toFixed(2).replace('.', ',') + '%';

  const totalRpeEl = document.getElementById('totalRpe');
  if (totalRpeEl) totalRpeEl.textContent = (rpeTrab + rpePat).toFixed(2).replace('.', ',') + '%';

  const totalFaovEl = document.getElementById('totalFaov');
  if (totalFaovEl) totalFaovEl.textContent = (faovTrab + faovPat).toFixed(2).replace('.', ',') + '%';
}
</script>
