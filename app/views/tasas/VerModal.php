<!-- Modal de Visualización Detallada de Tasa -->
<div class="detail-modal-overlay" id="modalVerTasa" onclick="cerrarVerTasaModal(event)">
  <div class="detail-modal-card" onclick="event.stopPropagation()">
    <!-- Cabecera Fija / Sticky Identificadora -->
    <div class="detail-modal-fixed-top">
      <div class="detail-modal-header">
        <h3 class="detail-modal-title">
          <i class="fa-solid fa-coins" style="color:var(--primary);"></i>
          Detalle de Tasa de Cambio (BCV)
        </h3>
        <button type="button" class="detail-modal-close" onclick="cerrarVerTasaModal()" title="Cerrar modal (Esc)">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <!-- Encabezado con Icono y Valor de la Tasa -->
      <div class="detail-header-profile">
        <div class="detail-profile-avatar" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);">
          <i class="fa-solid fa-building-columns" style="font-size:24px;"></i>
        </div>
        <div class="detail-profile-info">
          <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <h3 id="vtValorGrande">Bs. 0,0000</h3>
            <span class="badge" id="vtBadgeFuente" style="background:rgba(2, 132, 199, 0.12); color:#0284c7; border:1px solid rgba(2, 132, 199, 0.25);">BCV</span>
          </div>
          <span class="sub-label">Por cada 1 Dólar Estadounidense (USD)</span>
        </div>
      </div>
    </div>

    <!-- Cuerpo Scrollable con Scrollbar Elegante -->
    <div class="detail-modal-scroll-body">
      <!-- Cuadrícula de Datos -->
      <div class="detail-grid">
        <div class="detail-item">
          <span class="detail-label"><i class="fa-regular fa-calendar-check"></i> Fecha de Vigencia</span>
          <span class="detail-value" id="vtFecha">—</span>
        </div>

        <div class="detail-item">
          <span class="detail-label"><i class="fa-solid fa-satellite-dish"></i> Fuente / Método</span>
          <span class="detail-value" id="vtFuente">—</span>
        </div>

        <div class="detail-item">
          <span class="detail-label"><i class="fa-solid fa-calculator"></i> Conversión de Referencia</span>
          <span class="detail-value" id="vtCalcRef">$100 USD = Bs. 0,00</span>
        </div>

        <div class="detail-item">
          <span class="detail-label"><i class="fa-solid fa-database"></i> Registro ID</span>
          <span class="detail-value" id="vtId">—</span>
        </div>
      </div>
    </div>

    <div class="detail-modal-footer">
      <button type="button" class="btn small secondary" onclick="cerrarVerTasaModal()">
        Cerrar
      </button>
    </div>
  </div>
</div>

<script>
function abrirVerTasaModal(trElement) {
  try {
    const raw = trElement.getAttribute('data-tasa');
    if (!raw) return;
    const t = JSON.parse(raw);

    const valor = parseFloat(t.valor_tasa || 0);
    const valorFormatted = 'Bs. ' + valor.toLocaleString('es-VE', { minimumFractionDigits: 4, maximumFractionDigits: 4 });
    document.getElementById('vtValorGrande').textContent = valorFormatted;
    document.getElementById('vtId').textContent = '#' + (t.id_tasa || '—');

    const fuente = t.fuente || 'BCV';
    document.getElementById('vtFuente').textContent = fuente;
    document.getElementById('vtBadgeFuente').textContent = fuente;

    function formatD(d) {
      if (!d) return '—';
      const parts = d.split('-');
      return parts.length === 3 ? parts[2] + '/' + parts[1] + '/' + parts[0] : d;
    }
    document.getElementById('vtFecha').textContent = formatD(t.fecha);

    const ref100 = (valor * 100).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('vtCalcRef').textContent = '$100 USD = Bs. ' + ref100;

    // Mostrar modal
    const modal = document.getElementById('modalVerTasa');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  } catch (err) {
    console.error('Error al abrir modal de tasa:', err);
  }
}

function cerrarVerTasaModal(e) {
  if (e && e.target && e.target !== document.getElementById('modalVerTasa')) {
    return;
  }
  const modal = document.getElementById('modalVerTasa');
  if (modal) {
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }
}

document.addEventListener('keydown', function(evt) {
  if (evt.key === 'Escape') {
    const modal = document.getElementById('modalVerTasa');
    if (modal && modal.classList.contains('active')) {
      cerrarVerTasaModal();
    }
  }
});
</script>
