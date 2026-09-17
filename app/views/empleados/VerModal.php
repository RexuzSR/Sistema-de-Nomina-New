<!-- Modal de Visualización Detallada Integral del Empleado (Expediente 360°) -->
<div class="detail-modal-overlay" id="modalVerEmpleado" onclick="cerrarVerEmpleadoModal(event)">
  <div class="detail-modal-card detail-modal-lg" onclick="event.stopPropagation()">
    
    <!-- CABECERA FIJA / STICKY IDENTIFICADORA -->
    <div class="detail-modal-fixed-top">
      <!-- Barra Superior con Título y Cerrar -->
      <div class="detail-modal-header">
        <div style="display:flex; align-items:center; gap:10px;">
          <div style="width:34px; height:34px; border-radius:10px; background:rgba(15, 118, 110, 0.12); color:var(--primary); display:flex; align-items:center; justify-content:center; font-size:15px;">
            <i class="fa-solid fa-id-badge"></i>
          </div>
          <div>
            <h3 class="detail-modal-title" style="margin:0; font-size:15px; font-weight:700;">
              Expediente Integral del Empleado
            </h3>
            <span style="font-size:11.5px; color:var(--text-muted); font-weight:600;" id="vepHeaderId">Expediente Nº #—</span>
          </div>
        </div>
        <button type="button" class="detail-modal-close" onclick="cerrarVerEmpleadoModal()" title="Cerrar expediente (Esc)">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <!-- Hero Identificador Fijo del Perfil -->
      <div class="detail-header-profile">
        <div class="avatar-wrapper-rel" id="vepFotoContainer">
          <div class="detail-profile-avatar" id="vepAvatar">E</div>
          <span class="avatar-status-badge" id="vepAvatarStatusBadge" title="Estado"></span>
        </div>
        
        <div class="detail-profile-info" style="min-width:0;">
          <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            <h3 id="vepNombreCompleto" style="margin:0; font-size:18px; font-weight:800; color:var(--text); text-transform:capitalize; letter-spacing:-0.2px;">
              Nombre del Empleado
            </h3>
            <span class="status-pill active" id="vepBadgeEstado">
              <span class="status-dot"></span> <span id="vepBadgeEstadoTexto">Activo</span>
            </span>
          </div>
          
          <div class="profile-chips-row">
            <span class="profile-chip">
              <i class="fa-solid fa-address-card" style="color:var(--primary);"></i>
              <span>Cédula: <strong id="vepCedula">—</strong></span>
              <button type="button" class="mini-copy-btn" id="vepBtnCopyCedula" onclick="copiarDato(this, 'vepCedula')" title="Copiar Cédula">
                <i class="fa-regular fa-copy"></i>
              </button>
            </span>

            <span class="profile-chip">
              <i class="fa-solid fa-briefcase" style="color:var(--primary);"></i>
              <strong id="vepCargoBadge">—</strong>
            </span>

            <span class="profile-chip">
              <i class="fa-solid fa-file-contract" style="color:var(--primary);"></i>
              <strong id="vepTipoBadge">—</strong>
            </span>

            <span class="profile-chip highlight">
              <i class="fa-solid fa-dollar-sign"></i>
              <strong id="vepSueldoPill">$0,00 USD</strong>
            </span>
          </div>
        </div>
      </div>

      <!-- Barra Segmentada de Pestañas (Tabs de Navegación) -->
      <div class="detail-nav-tabs">
        <button type="button" class="detail-tab active" data-tab="all" onclick="switchVerTab('all', this)">
          <i class="fa-solid fa-layer-group"></i> Vista Completa
        </button>
        <button type="button" class="detail-tab" data-tab="personal" onclick="switchVerTab('personal', this)">
          <i class="fa-solid fa-user-shield"></i> Personal & Contacto
        </button>
        <button type="button" class="detail-tab" data-tab="laboral" onclick="switchVerTab('laboral', this)">
          <i class="fa-solid fa-briefcase"></i> Laboral & Contrato
        </button>
        <button type="button" class="detail-tab" data-tab="salario" onclick="switchVerTab('salario', this)">
          <i class="fa-solid fa-money-bill-wave"></i> Salario & BCV
        </button>
        <button type="button" class="detail-tab" data-tab="banco" onclick="switchVerTab('banco', this)">
          <i class="fa-solid fa-building-columns"></i> Banco & Actividad
        </button>
      </div>
    </div>

    <!-- CUERPO SCROLLABLE CON SCROLLBAR ELEGANTE -->
    <div class="detail-modal-scroll-body" id="vepScrollBody">
      
      <!-- SECCIÓN 1: DATOS PERSONALES Y CONTACTO -->
      <div class="tab-pane-section" data-section="personal">
        <div class="detail-section-title">
          <i class="fa-solid fa-user-shield"></i> Información Personal y Contacto
        </div>
        <div class="detail-grid">
          <div class="detail-item-card">
            <div class="detail-item-icon"><i class="fa-regular fa-id-card"></i></div>
            <div class="detail-item-content">
              <span class="detail-label">Cédula de Identidad</span>
              <div style="display:flex; align-items:center; justify-content:space-between; width:100%;">
                <span class="detail-value" id="vepCedulaDetalle">—</span>
                <button type="button" class="mini-copy-btn" onclick="copiarDato(this, 'vepCedulaDetalle')" title="Copiar Cédula">
                  <i class="fa-regular fa-copy"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="detail-item-card">
            <div class="detail-item-icon"><i class="fa-regular fa-envelope"></i></div>
            <div class="detail-item-content">
              <span class="detail-label">Correo Electrónico</span>
              <div style="display:flex; align-items:center; justify-content:space-between; width:100%;" id="vepEmailWrapper">
                <span class="detail-value" id="vepEmail">—</span>
              </div>
            </div>
          </div>

          <div class="detail-item-card">
            <div class="detail-item-icon"><i class="fa-solid fa-user"></i></div>
            <div class="detail-item-content">
              <span class="detail-label">Nombres Desglosados</span>
              <span class="detail-value" id="vepNombresDesglose" style="text-transform:capitalize;">—</span>
            </div>
          </div>

          <div class="detail-item-card">
            <div class="detail-item-icon"><i class="fa-solid fa-user-tag"></i></div>
            <div class="detail-item-content">
              <span class="detail-label">Apellidos Desglosados</span>
              <span class="detail-value" id="vepApellidosDesglose" style="text-transform:capitalize;">—</span>
            </div>
          </div>

          <div class="detail-item-card full-width">
            <div class="detail-item-icon"><i class="fa-solid fa-key"></i></div>
            <div class="detail-item-content">
              <span class="detail-label">Cuenta de Usuario en el Sistema</span>
              <span class="detail-value" id="vepUsuarioInfo">Sin usuario vinculado</span>
            </div>
          </div>
        </div>
      </div>

      <!-- SECCIÓN 2: INFORMACIÓN LABORAL Y CONTRATACIÓN -->
      <div class="tab-pane-section" data-section="laboral">
        <div class="detail-section-title">
          <i class="fa-solid fa-briefcase"></i> Ficha Laboral y Contratación
        </div>
        <div class="detail-grid">
          <div class="detail-item-card">
            <div class="detail-item-icon"><i class="fa-solid fa-building-user"></i></div>
            <div class="detail-item-content">
              <span class="detail-label">Cargo Oficial</span>
              <span class="detail-value" id="vepCargo">—</span>
            </div>
          </div>

          <div class="detail-item-card">
            <div class="detail-item-icon"><i class="fa-solid fa-file-contract"></i></div>
            <div class="detail-item-content">
              <span class="detail-label">Tipo de Personal / Contrato</span>
              <span class="detail-value" id="vepTipo">—</span>
            </div>
          </div>

          <div class="detail-item-card">
            <div class="detail-item-icon"><i class="fa-regular fa-clock"></i></div>
            <div class="detail-item-content">
              <span class="detail-label">Jornada Laboral</span>
              <span class="detail-value" id="vepJornada">—</span>
            </div>
          </div>

          <div class="detail-item-card">
            <div class="detail-item-icon"><i class="fa-regular fa-calendar-check"></i></div>
            <div class="detail-item-content">
              <span class="detail-label">Fecha de Ingreso</span>
              <span class="detail-value" id="vepFechaIngreso">—</span>
            </div>
          </div>

          <div class="detail-item-card">
            <div class="detail-item-icon" style="background:rgba(16, 185, 129, 0.12); color:#10b981;"><i class="fa-solid fa-hourglass-start"></i></div>
            <div class="detail-item-content">
              <span class="detail-label">Antigüedad en la Empresa</span>
              <span class="detail-value" id="vepAntiguedad" style="color:var(--primary); font-weight:700;">Calculando...</span>
            </div>
          </div>

          <div class="detail-item-card">
            <div class="detail-item-icon"><i class="fa-solid fa-circle-check"></i></div>
            <div class="detail-item-content">
              <span class="detail-label">Condición en Nómina</span>
              <span class="detail-value" id="vepCondicionNomina">Activo para emisión de recibos</span>
            </div>
          </div>
        </div>
      </div>

      <!-- SECCIÓN 3: COMPENSACIÓN Y SALARIO MULTIMONEDA -->
      <div class="tab-pane-section" data-section="salario">
        <div class="detail-section-title">
          <i class="fa-solid fa-money-bill-transfer"></i> Salario y Compensación Financiera
        </div>
        
        <div class="detail-salary-banner">
          <div class="detail-salary-stat">
            <span class="stat-label"><i class="fa-solid fa-dollar-sign"></i> Sueldo Mensual Base</span>
            <span class="stat-val" id="vepSueldoUsd">$0,00 USD</span>
          </div>
          <div class="detail-salary-stat" style="text-align:right;">
            <span class="stat-label"><i class="fa-solid fa-coins"></i> Equivalente Oficial BCV</span>
            <span class="stat-val" id="vepSueldoBs" style="color:#0284c7;">Bs. 0,00</span>
          </div>

          <!-- Desglose Mini de Quincena, Diario y Valor Hora -->
          <div class="detail-salary-mini-grid">
            <div class="detail-salary-mini-card">
              <span class="detail-label"><i class="fa-solid fa-calendar-days"></i> Quincenal (15d)</span>
              <strong style="font-size:13.5px; color:var(--text);" id="vepSueldoQuincenal">$0,00 / Bs. 0,00</strong>
            </div>
            <div class="detail-salary-mini-card">
              <span class="detail-label"><i class="fa-solid fa-sun"></i> Diario (30d)</span>
              <strong style="font-size:13.5px; color:var(--text);" id="vepSueldoDiario">$0,00 / Bs. 0,00</strong>
            </div>
            <div class="detail-salary-mini-card">
              <span class="detail-label"><i class="fa-solid fa-stopwatch"></i> Valor Hora (8h)</span>
              <strong style="font-size:13.5px; color:var(--text);" id="vepSueldoHora">$0,00 / Bs. 0,00</strong>
            </div>
          </div>
          <div style="font-size:11.5px; color:var(--text-muted); width:100%; margin-top:4px;" id="vepTasaNota">
            Tasa de cambio de referencia: <strong>—</strong>
          </div>
        </div>
      </div>

      <!-- SECCIÓN 4: INFORMACIÓN BANCARIA Y ACTIVIDAD -->
      <div class="tab-pane-section" data-section="banco">
        <div class="detail-section-title">
          <i class="fa-solid fa-building-columns"></i> Información Bancaria y Actividad
        </div>
        <div class="detail-grid">
          <div class="detail-item-card">
            <div class="detail-item-icon"><i class="fa-solid fa-landmark"></i></div>
            <div class="detail-item-content">
              <span class="detail-label">Entidad Bancaria</span>
              <span class="detail-value" id="vepBanco">—</span>
            </div>
          </div>

          <div class="detail-item-card">
            <div class="detail-item-icon"><i class="fa-solid fa-wallet"></i></div>
            <div class="detail-item-content">
              <span class="detail-label">Tipo de Cuenta</span>
              <span class="detail-value" id="vepTipoCuenta">—</span>
            </div>
          </div>

          <div class="detail-item-card full-width">
            <div class="detail-item-icon"><i class="fa-solid fa-hashtag"></i></div>
            <div class="detail-item-content">
              <span class="detail-label">Número de Cuenta (20 Dígitos)</span>
              <div style="display:flex; align-items:center; justify-content:space-between; width:100%; flex-wrap:wrap; gap:8px;">
                <span class="detail-value" id="vepNumeroCuenta" style="font-family:monospace; letter-spacing:1px; font-size:14.5px;">—</span>
                <button type="button" class="mini-copy-btn" onclick="copiarDato(this, 'vepNumeroCuenta')" title="Copiar número de cuenta bancaria" style="gap:4px; padding:3px 8px; font-size:11.5px; border:1px solid var(--border); border-radius:6px;">
                  <i class="fa-regular fa-copy"></i> Copiar Cuenta
                </button>
              </div>
            </div>
          </div>

          <div class="detail-item-card">
            <div class="detail-item-icon"><i class="fa-solid fa-receipt"></i></div>
            <div class="detail-item-content">
              <span class="detail-label">Nóminas Procesadas</span>
              <span class="detail-value" id="vepTotalNominas">0 nóminas en histórico</span>
            </div>
          </div>

          <div class="detail-item-card">
            <div class="detail-item-icon"><i class="fa-solid fa-file-signature"></i></div>
            <div class="detail-item-content">
              <span class="detail-label">Historial de Permisos</span>
              <span class="detail-value" id="vepPermisosResumen">0 permisos registrados</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- PIE DEL MODAL CON ACCIONES CONTEXTUALES -->
    <div class="detail-modal-footer">
      <a href="#" class="btn small secondary" id="vepBtnToggleEstado">
        <i class="fa-solid fa-power-off"></i> Inactivar Empleado
      </a>
      <div style="display:flex; align-items:center; gap:8px;">
        <button type="button" class="btn small secondary" onclick="cerrarVerEmpleadoModal()">
          Cerrar
        </button>
        <a href="#" class="btn small" id="vepBtnEditar">
          <i class="fa-solid fa-pen-to-square"></i> Editar Empleado
        </a>
      </div>
    </div>
  </div>
</div>

<script>
/* ─── PESTAÑAS DEL MODAL ────────────────────────────────── */
function switchVerTab(tabName, btn) {
  const tabs = document.querySelectorAll('.detail-tab');
  tabs.forEach(t => t.classList.remove('active'));
  if (btn) btn.classList.add('active');

  const sections = document.querySelectorAll('#vepScrollBody .tab-pane-section');
  if (tabName === 'all') {
    sections.forEach(sec => sec.style.display = 'block');
  } else {
    sections.forEach(sec => {
      const match = sec.getAttribute('data-section') === tabName;
      sec.style.display = match ? 'block' : 'none';
    });
  }
  const body = document.getElementById('vepScrollBody');
  if (body) body.scrollTop = 0;
}

/* ─── COPIAR AL PORTAPAPELES ────────────────────────────── */
function copiarDato(btn, elementId) {
  const el = document.getElementById(elementId);
  if (!el) return;
  const texto = el.textContent.trim().replace(/^V-\s*/, '').trim();
  if (!texto || texto === '—' || texto.indexOf('No ') === 0) return;

  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(texto).then(() => {
      mostrarFeedbackCopia(btn);
    }).catch(() => fallbackCopiar(texto, btn));
  } else {
    fallbackCopiar(texto, btn);
  }
}

function fallbackCopiar(texto, btn) {
  const ta = document.createElement('textarea');
  ta.value = texto;
  ta.style.position = 'fixed';
  ta.style.opacity = '0';
  document.body.appendChild(ta);
  ta.select();
  try {
    document.execCommand('copy');
    mostrarFeedbackCopia(btn);
  } catch(e) {}
  document.body.removeChild(ta);
}

function mostrarFeedbackCopia(btn) {
  if (!btn) return;
  const origHtml = btn.innerHTML;
  btn.classList.add('copied');
  btn.innerHTML = '<i class="fa-solid fa-check" style="color:#10b981;"></i>';
  if (typeof showToast === 'function') {
    showToast('Copiado al portapapeles ✓');
  }
  setTimeout(() => {
    btn.classList.remove('copied');
    btn.innerHTML = origHtml;
  }, 1800);
}

/* ─── CÁLCULOS Y FORMATEO ───────────────────────────────── */
function calcularAntiguedad(fechaStr) {
  if (!fechaStr) return 'No registrada';
  const fechaIngreso = new Date(fechaStr + 'T00:00:00');
  if (isNaN(fechaIngreso.getTime())) return 'Fecha inválida';

  const hoy = new Date();
  let anios = hoy.getFullYear() - fechaIngreso.getFullYear();
  let meses = hoy.getMonth() - fechaIngreso.getMonth();
  let dias = hoy.getDate() - fechaIngreso.getDate();

  if (dias < 0) {
    meses--;
    const ultimoMes = new Date(hoy.getFullYear(), hoy.getMonth(), 0);
    dias += ultimoMes.getDate();
  }

  if (meses < 0) {
    anios--;
    meses += 12;
  }

  if (anios < 0) return 'Fecha futura';

  const partes = [];
  if (anios > 0) partes.push(anios + (anios === 1 ? ' año' : ' años'));
  if (meses > 0) partes.push(meses + (meses === 1 ? ' mes' : ' meses'));
  if (dias > 0 || partes.length === 0) partes.push(dias + (dias === 1 ? ' día' : ' días'));

  return partes.join(', ');
}

function formatearFechaLegible(fechaStr) {
  if (!fechaStr) return 'No especificada';
  const parts = fechaStr.split('-');
  if (parts.length === 3) {
    return parts[2] + '/' + parts[1] + '/' + parts[0];
  }
  return fechaStr;
}

/* ─── ABRIR Y POBLAR MODAL ──────────────────────────────── */
function abrirVerEmpleadoModal(trElement) {
  try {
    const raw = trElement.getAttribute('data-empleado');
    if (!raw) return;
    const emp = JSON.parse(raw);

    // ID en cabecera
    document.getElementById('vepHeaderId').textContent = 'Expediente Nº #' + String(emp.id_empleado).padStart(4, '0');

    // Estado Activo/Inactivo
    const esActivo = Number(emp.estado) === 1;
    const badge = document.getElementById('vepBadgeEstado');
    const badgeTexto = document.getElementById('vepBadgeEstadoTexto');
    const avatarBadge = document.getElementById('vepAvatarStatusBadge');

    if (badge && badgeTexto) {
      badge.className = 'status-pill ' + (esActivo ? 'active' : 'inactive');
      badgeTexto.textContent = esActivo ? 'Activo' : 'Inactivo';
    }
    if (avatarBadge) {
      avatarBadge.className = 'avatar-status-badge ' + (esActivo ? '' : 'inactive');
      avatarBadge.title = esActivo ? 'Empleado Activo' : 'Empleado Inactivo';
    }

    // Foto o Inicial
    const fotoCont = document.getElementById('vepFotoContainer');
    const inicial = (emp.primer_nombre || 'E').charAt(0).toUpperCase();
    if (emp.foto_src && emp.foto_src.trim() !== '') {
      fotoCont.innerHTML = `
        <img src="${emp.foto_src}" class="detail-profile-img" alt="Foto">
        <span class="avatar-status-badge ${esActivo ? '' : 'inactive'}" id="vepAvatarStatusBadge"></span>`;
    } else {
      fotoCont.innerHTML = `
        <div class="detail-profile-avatar">${inicial}</div>
        <span class="avatar-status-badge ${esActivo ? '' : 'inactive'}" id="vepAvatarStatusBadge"></span>`;
    }

    // Nombres y Chips de Cabecera
    const nombreCompleto = emp.nombre_completo || [emp.primer_nombre, emp.segundo_nombre, emp.primer_apellido, emp.segundo_apellido].filter(Boolean).join(' ');
    document.getElementById('vepNombreCompleto').textContent = nombreCompleto || 'Empleado';
    document.getElementById('vepCedula').textContent = emp.cedula || '—';
    document.getElementById('vepCargoBadge').textContent = emp.nombre_cargo || 'Sin cargo';
    document.getElementById('vepTipoBadge').textContent = emp.nombre_tipo || 'General';

    const sueldoNum = parseFloat(emp.sueldo_divisa || 0);
    document.getElementById('vepSueldoPill').textContent = '$' + sueldoNum.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' USD';

    // Sección 1: Datos Personales
    document.getElementById('vepCedulaDetalle').textContent = emp.cedula ? ('V- ' + emp.cedula) : '—';
    
    // Correo Electrónico
    const emailWrapper = document.getElementById('vepEmailWrapper');
    if (emp.email && emp.email.trim() !== '') {
      emailWrapper.innerHTML = `
        <a href="mailto:${emp.email}" style="color:var(--primary); font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
          <i class="fa-regular fa-paper-plane"></i> <span id="vepEmail">${emp.email}</span>
        </a>
        <button type="button" class="mini-copy-btn" onclick="copiarDato(this, 'vepEmail')" title="Copiar Correo">
          <i class="fa-regular fa-copy"></i>
        </button>`;
    } else {
      emailWrapper.innerHTML = `<span class="detail-value" id="vepEmail">No registrado</span>`;
    }

    // Desglose de Nombres y Apellidos
    const nombresDesg = [emp.primer_nombre, emp.segundo_nombre].filter(Boolean).join(' ');
    const apellidosDesg = [emp.primer_apellido, emp.segundo_apellido].filter(Boolean).join(' ');
    document.getElementById('vepNombresDesglose').textContent = nombresDesg || '—';
    document.getElementById('vepApellidosDesglose').textContent = apellidosDesg || '—';

    // Cuenta de Usuario
    const usuarioCont = document.getElementById('vepUsuarioInfo');
    if (emp.nombre_usuario && emp.nombre_usuario.trim() !== '') {
      usuarioCont.innerHTML = `<span style="color:var(--primary); font-weight:700;"><i class="fa-solid fa-user-lock"></i> ${emp.nombre_usuario}</span> (${emp.nombre_rol || 'Usuario del Sistema'})`;
    } else {
      usuarioCont.textContent = 'Sin cuenta de usuario de acceso asignada';
    }

    // Sección 2: Ficha Laboral
    document.getElementById('vepCargo').textContent = emp.nombre_cargo || '—';
    document.getElementById('vepTipo').textContent = emp.nombre_tipo || '—';
    document.getElementById('vepJornada').textContent = emp.nombre_jornada || '—';
    document.getElementById('vepFechaIngreso').textContent = formatearFechaLegible(emp.fecha_ingreso);
    document.getElementById('vepAntiguedad').textContent = calcularAntiguedad(emp.fecha_ingreso);
    document.getElementById('vepCondicionNomina').textContent = esActivo 
      ? 'Habilitado para cálculo y cobro de nómina' 
      : 'Inactivo / Desincorporado de nómina';

    // Sección 3: Compensación Financiera
    const tasaBcv = parseFloat(emp.tasa_bcv || 0);
    const sueldoBs = tasaBcv > 0 ? (sueldoNum * tasaBcv) : 0;

    document.getElementById('vepSueldoUsd').textContent = '$' + sueldoNum.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' USD';
    document.getElementById('vepSueldoBs').textContent = tasaBcv > 0 
      ? ('Bs. ' + sueldoBs.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }))
      : 'Tasa BCV pendiente';

    const quincenaUsd = (sueldoNum / 2).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const quincenaBs = (sueldoBs / 2).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('vepSueldoQuincenal').textContent = `$${quincenaUsd} / Bs. ${quincenaBs}`;

    const diarioUsd = (sueldoNum / 30).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const diarioBs = (sueldoBs / 30).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('vepSueldoDiario').textContent = `$${diarioUsd} / Bs. ${diarioBs}`;

    const horaUsd = (sueldoNum / 240).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const horaBs = (sueldoBs / 240).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('vepSueldoHora').textContent = `$${horaUsd} / Bs. ${horaBs}`;

    document.getElementById('vepTasaNota').innerHTML = tasaBcv > 0
      ? `Conversión oficial calculada con tasa BCV: <strong>1 USD = Bs. ${tasaBcv.toLocaleString('es-VE', { minimumFractionDigits: 4, maximumFractionDigits: 4 })}</strong>`
      : `Tasa BCV oficial no disponible en caché de conexión.`;

    // Sección 4: Información Bancaria
    document.getElementById('vepBanco').textContent = emp.nombre_banco ? (emp.nombre_banco + (emp.codigo_banco ? ` (${emp.codigo_banco})` : '')) : 'No registrado';
    document.getElementById('vepTipoCuenta').textContent = emp.tipo_cuenta || 'No especificada';
    document.getElementById('vepNumeroCuenta').textContent = emp.numero_cuenta || 'Sin cuenta bancaria asociada';

    // Sección 5: Historial y Actividad
    document.getElementById('vepTotalNominas').textContent = (emp.total_nominas || 0) + ' recibos/nóminas procesadas';
    const totPerm = emp.total_permisos || 0;
    const apPerm = emp.permisos_aprobados || 0;
    const penPerm = emp.permisos_pendientes || 0;
    document.getElementById('vepPermisosResumen').textContent = totPerm > 0
      ? `${totPerm} solicitados (${apPerm} aprobados, ${penPerm} pendientes)`
      : '0 permisos o ausencias registradas';

    // Acciones del Footer
    document.getElementById('vepBtnEditar').href = 'index.php?r=empleados/editar&id=' + encodeURIComponent(emp.id_empleado);
    const btnToggle = document.getElementById('vepBtnToggleEstado');
    btnToggle.href = 'index.php?r=empleados/cambiarEstado&id=' + encodeURIComponent(emp.id_empleado);
    btnToggle.innerHTML = esActivo 
      ? '<i class="fa-solid fa-power-off"></i> Inactivar Empleado' 
      : '<i class="fa-solid fa-power-off"></i> Activar Empleado';

    // Resetear a pestaña "Vista Completa"
    const firstTab = document.querySelector('.detail-tab[data-tab="all"]');
    switchVerTab('all', firstTab);

    // Mostrar modal
    const modal = document.getElementById('modalVerEmpleado');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  } catch (err) {
    console.error('Error al abrir modal de empleado:', err);
  }
}

function cerrarVerEmpleadoModal(e) {
  if (e && e.target && e.target !== document.getElementById('modalVerEmpleado')) {
    return;
  }
  const modal = document.getElementById('modalVerEmpleado');
  if (modal) {
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }
}

// Cerrar con Escape
document.addEventListener('keydown', function(evt) {
  if (evt.key === 'Escape') {
    const modal = document.getElementById('modalVerEmpleado');
    if (modal && modal.classList.contains('active')) {
      cerrarVerEmpleadoModal();
    }
  }
});
</script>
