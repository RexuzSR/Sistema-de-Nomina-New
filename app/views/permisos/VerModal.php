<!-- Modal de Visualización Detallada de Permiso -->
<div class="detail-modal-overlay" id="modalVerPermiso" onclick="cerrarVerPermisoModal(event)">
  <div class="detail-modal-card" onclick="event.stopPropagation()">
    <!-- Cabecera Fija / Sticky Identificadora -->
    <div class="detail-modal-fixed-top">
      <div class="detail-modal-header">
        <h3 class="detail-modal-title">
          <i class="fa-solid fa-file-signature" style="color:var(--primary);"></i>
          Ficha del Permiso / Licencia
        </h3>
        <button type="button" class="detail-modal-close" onclick="cerrarVerPermisoModal()" title="Cerrar modal (Esc)">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <!-- Encabezado Fijo con Inicial y Nombre del Empleado -->
      <div class="detail-header-profile" style="padding:14px 24px;">
        <div class="detail-profile-avatar" id="vpmAvatar" style="background:linear-gradient(135deg, #10b981 0%, #059669 100%);">
          E
        </div>
        <div class="detail-profile-info">
          <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <h3 id="vpmEmpleado" style="margin:0; font-size:17px; color:var(--text);">Nombre del Empleado</h3>
            <span class="badge" id="vpmBadgeEstado">Pendiente</span>
          </div>
          <span class="sub-label">Solicitud ID: <strong id="vpmId" style="color:var(--text);">—</strong></span>
        </div>
      </div>
    </div>

    <!-- Cuerpo Scrollable con Scrollbar Fina -->
    <div class="detail-modal-scroll-body">
      <!-- Cuadrícula de Datos -->
      <div class="detail-grid">
        <div class="detail-item">
          <span class="detail-label"><i class="fa-regular fa-calendar-minus"></i> Fecha de Inicio</span>
          <span class="detail-value" id="vpmFechaInicio">—</span>
        </div>

        <div class="detail-item">
          <span class="detail-label"><i class="fa-regular fa-calendar-plus"></i> Fecha de Fin</span>
          <span class="detail-value" id="vpmFechaFin">—</span>
        </div>

        <div class="detail-item">
          <span class="detail-label"><i class="fa-solid fa-hourglass-half"></i> Total Días</span>
          <span class="detail-value" id="vpmDias" style="color:var(--primary); font-size:16px;">—</span>
        </div>

        <div class="detail-item">
          <span class="detail-label"><i class="fa-solid fa-traffic-light"></i> Estado</span>
          <span class="detail-value" id="vpmEstadoTexto">—</span>
        </div>

        <div class="detail-item full-width">
          <span class="detail-label"><i class="fa-solid fa-comment-dots"></i> Motivo / Justificación</span>
          <p class="detail-value" id="vpmMotivo" style="margin:4px 0 0; font-weight:500; font-size:13.5px; line-height:1.5; color:var(--text);">—</p>
        </div>
      </div>
    </div>

    <div class="detail-modal-footer">
      <div id="vpmAccionesWrap" style="display:inline-flex; gap:8px;"></div>
      <button type="button" class="btn small secondary" onclick="cerrarVerPermisoModal()">
        Cerrar
      </button>
    </div>
  </div>
</div>

<script>
function abrirVerPermisoModal(trElement) {
  try {
    const raw = trElement.getAttribute('data-permiso');
    if (!raw) return;
    const p = JSON.parse(raw);

    document.getElementById('vpmId').textContent = '#' + p.id_permiso;
    document.getElementById('vpmEmpleado').textContent = p.empleado_nombre || 'Empleado';
    const inicial = (p.primer_nombre || p.empleado_nombre || 'E').charAt(0).toUpperCase();
    document.getElementById('vpmAvatar').textContent = inicial;

    // Estado Badge
    const badge = document.getElementById('vpmBadgeEstado');
    const estado = p.estado || 'Pendiente';
    badge.textContent = estado;
    if (estado === 'Aprobado') {
      badge.className = 'badge activo';
      badge.style = 'background:rgba(34, 197, 94, 0.15); color:#16a34a; border:1px solid rgba(34, 197, 94, 0.3);';
    } else if (estado === 'Rechazado') {
      badge.className = 'badge inactivo';
      badge.style = 'background:rgba(239, 68, 68, 0.15); color:#dc2626; border:1px solid rgba(239, 68, 68, 0.3);';
    } else {
      badge.className = 'badge';
      badge.style = 'background:rgba(245, 158, 11, 0.15); color:#d97706; border:1px solid rgba(245, 158, 11, 0.3);';
    }
    document.getElementById('vpmEstadoTexto').textContent = estado;

    function formatD(d) {
      if (!d) return '—';
      const parts = d.split('-');
      return parts.length === 3 ? parts[2] + '/' + parts[1] + '/' + parts[0] : d;
    }
    document.getElementById('vpmFechaInicio').textContent = formatD(p.fecha_inicio);
    document.getElementById('vpmFechaFin').textContent = formatD(p.fecha_fin);
    document.getElementById('vpmDias').textContent = (p.dias_solicitados || 1) + ' día(s)';
    document.getElementById('vpmMotivo').textContent = p.motivo || 'Sin motivo detallado registrado.';

    // Botones de acción contextual
    const accionesWrap = document.getElementById('vpmAccionesWrap');
    if (estado === 'Pendiente') {
      accionesWrap.innerHTML = `
        <a class="btn small" style="background:#16a34a;" href="index.php?r=permisos/cambiarEstado&id=${p.id_permiso}&estado=Aprobado"><i class="fa-solid fa-check"></i> Aprobar</a>
        <a class="btn small danger" href="index.php?r=permisos/cambiarEstado&id=${p.id_permiso}&estado=Rechazado"><i class="fa-solid fa-xmark"></i> Rechazar</a>
      `;
    } else {
      accionesWrap.innerHTML = '';
    }

    // Mostrar modal
    const modal = document.getElementById('modalVerPermiso');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  } catch (err) {
    console.error('Error al abrir modal de permiso:', err);
  }
}

function cerrarVerPermisoModal(e) {
  if (e && e.target && e.target !== document.getElementById('modalVerPermiso')) {
    return;
  }
  const modal = document.getElementById('modalVerPermiso');
  if (modal) {
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }
}

document.addEventListener('keydown', function(evt) {
  if (evt.key === 'Escape') {
    const modal = document.getElementById('modalVerPermiso');
    if (modal && modal.classList.contains('active')) {
      cerrarVerPermisoModal();
    }
  }
});
</script>
