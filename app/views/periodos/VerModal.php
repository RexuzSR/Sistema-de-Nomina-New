<!-- Modal de Visualización Detallada de Periodo -->
<div class="detail-modal-overlay" id="modalVerPeriodo" onclick="cerrarVerPeriodoModal(event)">
  <div class="detail-modal-card" onclick="event.stopPropagation()">
    <!-- Cabecera Fija / Sticky Identificadora -->
    <div class="detail-modal-fixed-top">
      <div class="detail-modal-header">
        <h3 class="detail-modal-title">
          <i class="fa-solid fa-calendar-check" style="color:var(--primary);"></i>
          Detalle del Periodo de Nómina
        </h3>
        <button type="button" class="detail-modal-close" onclick="cerrarVerPeriodoModal()" title="Cerrar modal (Esc)">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <!-- Encabezado Fijo con Ciclo y Tipo -->
      <div class="detail-header-profile" style="padding:14px 24px;">
        <div class="detail-profile-avatar" style="background:linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
          <i class="fa-solid fa-calendar-days" style="font-size:24px;"></i>
        </div>
        <div class="detail-profile-info">
          <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <h3 id="vpDescripcion" style="margin:0; font-size:17px; color:var(--text);">Nombre del Ciclo</h3>
            <span class="badge" id="vpBadgeTipo" style="background:rgba(2, 132, 199, 0.12); color:#0284c7; border:1px solid rgba(2, 132, 199, 0.25);">Quincenal</span>
          </div>
          <span class="sub-label">ID Periodo: <strong id="vpId" style="color:var(--text);">—</strong></span>
        </div>
      </div>
    </div>

    <!-- Cuerpo Scrollable con Scrollbar Fina -->
    <div class="detail-modal-scroll-body">
      <!-- Cuadrícula de Datos -->
      <div class="detail-grid">
        <div class="detail-item">
          <span class="detail-label"><i class="fa-solid fa-play"></i> Fecha de Inicio</span>
          <span class="detail-value" id="vpFechaInicio">—</span>
        </div>

        <div class="detail-item">
          <span class="detail-label"><i class="fa-solid fa-flag-checkered"></i> Fecha de Fin</span>
          <span class="detail-value" id="vpFechaFin">—</span>
        </div>

        <div class="detail-item">
          <span class="detail-label"><i class="fa-solid fa-calendar"></i> Año Contable</span>
          <span class="detail-value" id="vpAnio">—</span>
        </div>

        <div class="detail-item">
          <span class="detail-label"><i class="fa-regular fa-calendar"></i> Mes</span>
          <span class="detail-value" id="vpMes">—</span>
        </div>

        <div class="detail-item">
          <span class="detail-label"><i class="fa-solid fa-hashtag"></i> Quincena #</span>
          <span class="detail-value" id="vpQuincena">—</span>
        </div>

        <div class="detail-item">
          <span class="detail-label"><i class="fa-solid fa-clock-rotate-left"></i> Rango Completo</span>
          <span class="detail-value" id="vpRangoTexto">—</span>
        </div>
      </div>
    </div>

    <div class="detail-modal-footer">
      <a href="#" class="btn small" id="vpBtnGenerar">
        <i class="fa-solid fa-folder-open"></i> Generar / Ver Nómina
      </a>
      <button type="button" class="btn small secondary" onclick="cerrarVerPeriodoModal()">
        Cerrar
      </button>
    </div>
  </div>
</div>

<script>
function abrirVerPeriodoModal(trElement) {
  try {
    const raw = trElement.getAttribute('data-periodo');
    if (!raw) return;
    const p = JSON.parse(raw);

    document.getElementById('vpId').textContent = '#' + p.id_periodo;
    document.getElementById('vpDescripcion').textContent = p.descripcion || 'Ciclo de Nómina';
    document.getElementById('vpBadgeTipo').textContent = p.tipo_periodo || 'Quincenal';
    
    // Fechas
    function formatD(d) {
      if (!d) return '—';
      const parts = d.split('-');
      return parts.length === 3 ? parts[2] + '/' + parts[1] + '/' + parts[0] : d;
    }
    document.getElementById('vpFechaInicio').textContent = formatD(p.fecha_inicio);
    document.getElementById('vpFechaFin').textContent = formatD(p.fecha_fin);
    document.getElementById('vpRangoTexto').textContent = formatD(p.fecha_inicio) + ' al ' + formatD(p.fecha_fin);
    
    document.getElementById('vpAnio').textContent = p.anio || '—';
    const meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    const mesNom = meses[Number(p.mes)] || ('Mes ' + p.mes);
    document.getElementById('vpMes').textContent = mesNom;
    document.getElementById('vpQuincena').textContent = p.numero_quincena ? 'Quincena ' + p.numero_quincena : '—';

    // Link a Generar / Ver nómina
    document.getElementById('vpBtnGenerar').href = 'index.php?r=nomina/generar&id_periodo=' + encodeURIComponent(p.id_periodo);

    // Mostrar modal
    const modal = document.getElementById('modalVerPeriodo');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  } catch (err) {
    console.error('Error al abrir modal de periodo:', err);
  }
}

function cerrarVerPeriodoModal(e) {
  if (e && e.target && e.target !== document.getElementById('modalVerPeriodo')) {
    return;
  }
  const modal = document.getElementById('modalVerPeriodo');
  if (modal) {
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }
}

document.addEventListener('keydown', function(evt) {
  if (evt.key === 'Escape') {
    const modal = document.getElementById('modalVerPeriodo');
    if (modal && modal.classList.contains('active')) {
      cerrarVerPeriodoModal();
    }
  }
});
</script>
