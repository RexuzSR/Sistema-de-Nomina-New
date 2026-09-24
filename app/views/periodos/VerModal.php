<!-- Modal de Visualización Detallada y Reportes Gerenciales de Periodo -->
<div class="detail-modal-overlay" id="modalVerPeriodo" onclick="cerrarVerPeriodoModal(event)">
  <div class="detail-modal-card detail-modal-lg" onclick="event.stopPropagation()">
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
      <div class="detail-header-profile" style="padding:14px 24px 10px 24px;">
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

      <!-- Barra de Viñetas / Pestañas de Navegación -->
      <div class="vp-tabs-bar">
        <button type="button" class="vp-tab-btn active" id="vpTabBtnPeriodo" onclick="cambiarPestanaModal('periodo')">
          <i class="fa-solid fa-calendar-days"></i> Datos del Periodo
        </button>
        <button type="button" class="vp-tab-btn" id="vpTabBtnNomina" onclick="cambiarPestanaModal('nomina')">
          <i class="fa-solid fa-chart-pie"></i> Detalles de la Nómina
          <span class="vp-tab-status-dot" id="vpTabStatusDot" title="Cargando estado..."></span>
        </button>
      </div>
    </div>

    <!-- Cuerpo Scrollable -->
    <div class="detail-modal-scroll-body" style="padding: 18px 24px;">

      <!-- ══════════ PANEL 1: DATOS DEL PERIODO ══════════ -->
      <div id="vpPanelPeriodo" class="vp-tab-panel active">
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

      <!-- ══════════ PANEL 2: DETALLES Y REPORTES DE NÓMINA ══════════ -->
      <div id="vpPanelNomina" class="vp-tab-panel" style="display:none;">

        <!-- Estado de Carga (Spinner) -->
        <div id="vpNominaLoading" style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:50px 0; color:var(--text-muted);">
          <i class="fa-solid fa-circle-notch fa-spin" style="font-size:32px; color:var(--primary); margin-bottom:12px;"></i>
          <span style="font-size:13.5px; font-weight:600;">Cargando métricas y reportes gerenciales...</span>
        </div>

        <!-- Estado Vacío: Nómina aún no generada -->
        <div id="vpNominaVacio" style="display:none; text-align:center; padding:40px 20px;">
          <div style="width:68px; height:68px; margin:0 auto 16px; border-radius:50%; background:rgba(2, 132, 199, 0.1); display:flex; align-items:center; justify-content:center; color:#0284c7; font-size:28px;">
            <i class="fa-solid fa-file-invoice-dollar"></i>
          </div>
          <h4 style="margin:0 0 6px; font-size:16px; color:var(--text);">Aún no se ha generado la nómina de este periodo</h4>
          <p style="margin:0 auto 20px; font-size:13px; color:var(--text-muted); max-width:440px; line-height:1.45;">
            Al generar la nómina quincenal, se activarán las gráficas ejecutivas de dona, indicadores de desembolso neto, retenciones de ley y distribución presupuestaria.
          </p>
          <a href="#" id="vpBtnGenerarEmpty" class="btn small" style="display:inline-flex; align-items:center; gap:6px;">
            <i class="fa-solid fa-plus"></i> Generar Nómina de este Ciclo
          </a>
        </div>

        <!-- Contenedor con Reportes Gerenciales Activos -->
        <div id="vpNominaContent" style="display:none;">

          <!-- Barra de Estado y Tasa BCV -->
          <div class="vp-report-status-bar">
            <div style="display:flex; align-items:center; gap:8px;">
              <span class="badge" id="vpRepBadgeEstado" style="background:#e0f2fe; color:#0369a1; font-weight:700;">Generada</span>
              <span class="vp-status-pill" id="vpRepModificadaPill">
                <i class="fa-solid fa-check-double"></i> Sin modificaciones
              </span>
            </div>
            <div class="vp-bcv-tag" id="vpRepTasaBcv" title="Tasa oficial de cambio aplicada">
              <i class="fa-solid fa-building-columns" style="color:var(--primary);"></i>
              <span>Tasa BCV: <strong>—</strong></span>
            </div>
          </div>

          <!-- Cuadrícula de 4 KPIs Gerenciales -->
          <div class="vp-kpi-grid">
            <!-- KPI 1: Neto a Transferir -->
            <div class="vp-kpi-card vp-kpi-primary">
              <div class="vp-kpi-head">
                <span class="vp-kpi-title"><i class="fa-solid fa-money-bill-wave"></i> Desembolso Neto</span>
                <span class="vp-kpi-badge">Líquido</span>
              </div>
              <div class="vp-kpi-val" id="vpKpiNeto">$ 0,00</div>
              <div class="vp-kpi-sub" id="vpKpiNetoBs">≈ Bs. 0,00</div>
              <div class="vp-kpi-footer">Transferencia total a empleados</div>
            </div>

            <!-- KPI 2: Asignaciones Brutas -->
            <div class="vp-kpi-card vp-kpi-success">
              <div class="vp-kpi-head">
                <span class="vp-kpi-title"><i class="fa-solid fa-arrow-trend-up"></i> Total Asignaciones</span>
                <span class="vp-kpi-badge">+ Bruto</span>
              </div>
              <div class="vp-kpi-val" id="vpKpiAsig">$ 0,00</div>
              <div class="vp-kpi-sub" id="vpKpiAsigBs">≈ Bs. 0,00</div>
              <div class="vp-kpi-footer">Sueldo base + complementos</div>
            </div>

            <!-- KPI 3: Deducciones de Ley -->
            <div class="vp-kpi-card vp-kpi-danger">
              <div class="vp-kpi-head">
                <span class="vp-kpi-title"><i class="fa-solid fa-shield-halved"></i> Total Retenciones</span>
                <span class="vp-kpi-badge">− Descuentos</span>
              </div>
              <div class="vp-kpi-val" id="vpKpiDed">$ 0,00</div>
              <div class="vp-kpi-sub" id="vpKpiDedBs">≈ Bs. 0,00</div>
              <div class="vp-kpi-footer">IVSS (SSO), RPE, FAOV</div>
            </div>

            <!-- KPI 4: Plantilla Remunerada -->
            <div class="vp-kpi-card vp-kpi-info">
              <div class="vp-kpi-head">
                <span class="vp-kpi-title"><i class="fa-solid fa-users"></i> Personal Activo</span>
                <span class="vp-kpi-badge">Nómina</span>
              </div>
              <div class="vp-kpi-val" id="vpKpiEmpleados">0 trab.</div>
              <div class="vp-kpi-sub" id="vpKpiPromedio">Prom: $ 0,00</div>
              <div class="vp-kpi-footer" id="vpKpiDiasProm">Promedio días: 15.0</div>
            </div>
          </div>

          <!-- Sección de Gráficas de Dona -->
          <div class="vp-charts-row">
            <!-- Gráfica 1: Estructura Financiera (Neto vs Retenciones) -->
            <div class="vp-chart-card">
              <div class="vp-chart-card-header">
                <div>
                  <h5 class="vp-chart-title"><i class="fa-solid fa-chart-pie" style="color:#0284c7;"></i> Estructura Financiera</h5>
                  <span class="vp-chart-sub">Desembolso Neto vs Deducciones de Ley</span>
                </div>
              </div>
              <div class="vp-donut-canvas-container">
                <canvas id="vpChartEstructura"></canvas>
                <div class="vp-donut-center" id="vpCenterEstructura">
                  <span class="vp-donut-center-pct" id="vpPctEstructura">--%</span>
                  <span class="vp-donut-center-sub" id="vpSubEstructura">Neto Líquido</span>
                  <span class="vp-donut-center-val" id="vpValEstructura">$ 0,00</span>
                </div>
              </div>
              <div class="vp-donut-legend" id="vpLegendEstructura"></div>
            </div>

            <!-- Gráfica 2: Distribución por Cargo / Departamento -->
            <div class="vp-chart-card">
              <div class="vp-chart-card-header">
                <div>
                  <h5 class="vp-chart-title"><i class="fa-solid fa-briefcase" style="color:#0f766e;"></i> Inversión por Cargo</h5>
                  <span class="vp-chart-sub">Participación del presupuesto en este ciclo</span>
                </div>
              </div>
              <div class="vp-donut-canvas-container">
                <canvas id="vpChartCargos"></canvas>
                <div class="vp-donut-center" id="vpCenterCargos">
                  <span class="vp-donut-center-pct" id="vpPctCargos">--%</span>
                  <span class="vp-donut-center-sub" id="vpSubCargos">Cargo</span>
                  <span class="vp-donut-center-val" id="vpValCargos">$ 0,00</span>
                </div>
              </div>
              <div class="vp-donut-legend" id="vpLegendCargos"></div>
            </div>
          </div>

          <!-- Desgloses Comparativos en Tarjetas -->
          <div class="vp-breakdown-row">
            <!-- Composición de Asignaciones -->
            <div class="vp-breakdown-card">
              <div class="vp-breakdown-title">
                <i class="fa-solid fa-circle-plus" style="color:#0f766e;"></i> Composición de Asignaciones
              </div>
              <div class="vp-breakdown-list" id="vpListaAsignaciones"></div>
            </div>

            <!-- Composición de Deducciones -->
            <div class="vp-breakdown-card">
              <div class="vp-breakdown-title">
                <i class="fa-solid fa-circle-minus" style="color:#e11d48;"></i> Retenciones y Deducciones de Ley
              </div>
              <div class="vp-breakdown-list" id="vpListaDeducciones"></div>
            </div>
          </div>

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

<style>
/* Estilos Scoped para el Modal Detalle con Reportes Gerenciales */
.vp-tabs-bar {
  display: flex;
  gap: 8px;
  padding: 0 24px 10px;
  border-bottom: 1px solid var(--border);
  background: transparent;
}

.vp-tab-btn {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  background: transparent;
  border: 1px solid transparent;
  color: var(--text-muted);
  font-size: 13px;
  font-weight: 600;
  padding: 7px 14px;
  border-radius: 999px;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  font-family: inherit;
}

.vp-tab-btn:hover {
  background: var(--card-hover, rgba(2, 132, 199, 0.08));
  color: var(--text);
}

.vp-tab-btn.active {
  background: rgba(2, 132, 199, 0.12);
  color: #0284c7;
  border-color: rgba(2, 132, 199, 0.28);
  font-weight: 700;
}

.vp-tab-status-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #cbd5e1;
  display: inline-block;
  transition: background 0.2s ease;
}

.vp-tab-status-dot.active-green {
  background: #10b981;
  box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
}

.vp-tab-status-dot.active-amber {
  background: #f59e0b;
}

.vp-report-status-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 10px;
  padding: 10px 14px;
  background: var(--card, rgba(248, 250, 252, 0.8));
  border: 1px solid var(--border);
  border-radius: 12px;
  margin-bottom: 16px;
}

.vp-status-pill {
  font-size: 11.5px;
  color: var(--text-muted);
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}

.vp-bcv-tag {
  font-size: 12px;
  color: var(--text-muted);
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.vp-bcv-tag strong {
  color: var(--text);
}

/* Grid de Tarjetas KPI */
.vp-kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  margin-bottom: 18px;
}

@media (max-width: 768px) {
  .vp-kpi-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .vp-kpi-grid {
    grid-template-columns: 1fr;
  }
}

.vp-kpi-card {
  padding: 14px 14px 12px;
  border-radius: 14px;
  background: var(--surface);
  border: 1px solid var(--border);
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
  display: flex;
  flex-direction: column;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.vp-kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 14px rgba(0, 0, 0, 0.05);
}

.vp-kpi-primary { border-top: 3px solid #0284c7; }
.vp-kpi-success { border-top: 3px solid #0f766e; }
.vp-kpi-danger  { border-top: 3px solid #e11d48; }
.vp-kpi-info    { border-top: 3px solid #6366f1; }

.vp-kpi-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 6px;
}

.vp-kpi-title {
  font-size: 11.5px;
  font-weight: 700;
  color: var(--text-muted);
  display: flex;
  align-items: center;
  gap: 5px;
}

.vp-kpi-badge {
  font-size: 9.5px;
  font-weight: 800;
  padding: 2px 6px;
  border-radius: 999px;
  background: rgba(0, 0, 0, 0.05);
  color: var(--text-muted);
  text-transform: uppercase;
}

.vp-kpi-val {
  font-size: 19px;
  font-weight: 800;
  color: var(--text);
  line-height: 1.2;
}

.vp-kpi-sub {
  font-size: 11.5px;
  color: var(--text-muted);
  font-weight: 600;
  margin-top: 2px;
}

.vp-kpi-footer {
  font-size: 10.5px;
  color: var(--text-muted);
  opacity: 0.85;
  margin-top: 8px;
  padding-top: 6px;
  border-top: 1px dashed var(--border);
}

/* Grid de Donas */
.vp-charts-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 18px;
}

@media (max-width: 680px) {
  .vp-charts-row {
    grid-template-columns: 1fr;
  }
}

.vp-chart-card {
  padding: 16px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 14px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
}

.vp-chart-card-header {
  margin-bottom: 12px;
}

.vp-chart-title {
  margin: 0;
  font-size: 13.5px;
  font-weight: 700;
  color: var(--text);
  display: flex;
  align-items: center;
  gap: 6px;
}

.vp-chart-sub {
  font-size: 11px;
  color: var(--text-muted);
  font-weight: 500;
}

.vp-donut-canvas-container {
  position: relative;
  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 4px 0 6px;
}

.vp-donut-canvas-container canvas {
  max-height: 200px !important;
  position: relative;
  z-index: 1;
}

/* Centro de la Dona con Porcentaje y Detalle */
.vp-donut-center {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  pointer-events: none;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  width: auto;
  min-width: 90px;
  background: transparent !important;
  user-select: none;
  z-index: 2;
  transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.vp-donut-center.is-hovered {
  transform: translate(-50%, -50%) scale(1.05);
}

.vp-donut-center-pct {
  font-size: 28px;
  font-weight: 800;
  line-height: 1;
  color: var(--text);
  letter-spacing: -0.5px;
  transition: color 0.2s ease;
  font-family: inherit;
  display: block;
}

.vp-donut-center-sub {
  font-size: 11px;
  font-weight: 600;
  color: var(--text-muted);
  margin-top: 3px;
  max-width: 135px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  transition: color 0.2s ease;
  display: block;
}

.vp-donut-center-val {
  font-size: 11.5px;
  font-weight: 700;
  color: var(--text);
  margin-top: 2px;
  max-width: 135px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  opacity: 0.9;
  display: block;
}

.vp-donut-legend {
  margin-top: 8px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  font-size: 11.5px;
}

.vp-donut-legend-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: var(--text);
  padding: 5px 8px;
  border-radius: 8px;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  cursor: pointer;
  background: transparent;
  user-select: none;
}

.vp-donut-legend-item:hover,
.vp-donut-legend-item.active-slice {
  background: var(--card-hover, rgba(2, 132, 199, 0.08));
  transform: translateX(2px);
}

.vp-donut-legend-label {
  display: flex;
  align-items: center;
  gap: 7px;
  color: var(--text-muted);
  font-weight: 500;
}

.vp-donut-legend-dot {
  width: 9px;
  height: 9px;
  border-radius: 50%;
  flex-shrink: 0;
  box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.04);
}

.vp-donut-legend-val {
  font-weight: 700;
  color: var(--text);
}

/* Desgloses Comparativos */
.vp-breakdown-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

@media (max-width: 680px) {
  .vp-breakdown-row {
    grid-template-columns: 1fr;
  }
}

.vp-breakdown-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 14px 16px;
}

.vp-breakdown-title {
  font-size: 12px;
  font-weight: 700;
  color: var(--text);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 10px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.vp-breakdown-list {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.vp-breakdown-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 5px 8px;
  border-radius: 6px;
  background: var(--card, rgba(0, 0, 0, 0.02));
  font-size: 12px;
}

.vp-breakdown-item-name {
  color: var(--text-muted);
}

.vp-breakdown-item-val {
  font-weight: 700;
  color: var(--text);
}

/* Dark Theme specific overrides */
.dark-theme .vp-kpi-badge {
  background: rgba(255, 255, 255, 0.08);
  color: #94a3b8;
}
.dark-theme .vp-report-status-bar {
  background: #111827;
  border-color: #1f2937;
}
.dark-theme .vp-breakdown-item {
  background: rgba(255, 255, 255, 0.04);
}
</style>

<script>
let currentPeriodoId = null;
let currentPeriodoData = null;
let nominaReportCache = {};
window._modalChartEstructura = null;
window._modalChartCargos = null;

function formatCurrencyUsd(v) {
  return '$ ' + Number(v || 0).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatCurrencyBs(v) {
  return 'Bs. ' + Number(v || 0).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function abrirVerPeriodoModal(trElement) {
  try {
    const raw = trElement.getAttribute('data-periodo');
    if (!raw) return;
    const p = JSON.parse(raw);
    currentPeriodoData = p;
    currentPeriodoId = p.id_periodo;

    document.getElementById('vpId').textContent = '#' + p.id_periodo;
    document.getElementById('vpDescripcion').textContent = p.descripcion || 'Ciclo de Nómina';
    document.getElementById('vpBadgeTipo').textContent = p.tipo_periodo || 'Quincenal';
    
    // Formato de Fechas
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

    // Links a Generar / Ver nómina
    const generarUrl = 'index.php?r=nomina/generar&id_periodo=' + encodeURIComponent(p.id_periodo);
    document.getElementById('vpBtnGenerar').href = generarUrl;
    const emptyBtn = document.getElementById('vpBtnGenerarEmpty');
    if (emptyBtn) emptyBtn.href = generarUrl;

    // Resetear a pestaña inicial (Periodo)
    cambiarPestanaModal('periodo', false);

    // Precargar estado de nómina en segundo plano para encender el status dot
    verificarEstadoNominaTab(p.id_periodo);

    // Mostrar modal
    const modal = document.getElementById('modalVerPeriodo');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  } catch (err) {
    console.error('Error al abrir modal de periodo:', err);
  }
}

function cambiarPestanaModal(tabName, forceFetch = true) {
  const btnPeriodo = document.getElementById('vpTabBtnPeriodo');
  const btnNomina = document.getElementById('vpTabBtnNomina');
  const panelPeriodo = document.getElementById('vpPanelPeriodo');
  const panelNomina = document.getElementById('vpPanelNomina');

  if (tabName === 'periodo') {
    btnPeriodo.classList.add('active');
    btnNomina.classList.remove('active');
    panelPeriodo.style.display = 'block';
    panelNomina.style.display = 'none';
  } else {
    btnPeriodo.classList.remove('active');
    btnNomina.classList.add('active');
    panelPeriodo.style.display = 'none';
    panelNomina.style.display = 'block';

    if (forceFetch && currentPeriodoId) {
      cargarReporteNominaPeriodo(currentPeriodoId);
    }
  }
}

async function verificarEstadoNominaTab(idPeriodo) {
  const dot = document.getElementById('vpTabStatusDot');
  if (!dot) return;
  dot.className = 'vp-tab-status-dot';

  try {
    const data = await fetchReporteNominaData(idPeriodo);
    if (data && data.tiene_nomina) {
      dot.className = 'vp-tab-status-dot active-green';
      dot.title = 'Nómina procesada · ' + data.totales.total_empleados + ' trabajadores';
    } else {
      dot.className = 'vp-tab-status-dot active-amber';
      dot.title = 'Nómina aún no generada';
    }
  } catch (e) {
    dot.className = 'vp-tab-status-dot';
  }
}

async function fetchReporteNominaData(idPeriodo) {
  if (nominaReportCache[idPeriodo]) {
    return nominaReportCache[idPeriodo];
  }
  const res = await fetch('index.php?r=periodos/reporteNomina&id_periodo=' + encodeURIComponent(idPeriodo));
  const data = await res.json();
  nominaReportCache[idPeriodo] = data;
  return data;
}

async function cargarReporteNominaPeriodo(idPeriodo) {
  const loading = document.getElementById('vpNominaLoading');
  const vacio = document.getElementById('vpNominaVacio');
  const content = document.getElementById('vpNominaContent');

  loading.style.display = 'flex';
  vacio.style.display = 'none';
  content.style.display = 'none';

  try {
    const data = await fetchReporteNominaData(idPeriodo);
    loading.style.display = 'none';

    if (!data.success || !data.tiene_nomina) {
      vacio.style.display = 'block';
      return;
    }

    content.style.display = 'block';
    renderizarDatosGerenciales(data);
  } catch (err) {
    console.error('Error al cargar reporte de nómina:', err);
    loading.style.display = 'none';
    vacio.style.display = 'block';
  }
}

function renderizarDatosGerenciales(data) {
  const t = data.totales || {};
  
  // Barra de estado y tasa
  document.getElementById('vpRepBadgeEstado').textContent = data.estado || 'Generada';
  const modPill = document.getElementById('vpRepModificadaPill');
  if (data.modificada === 1) {
    modPill.innerHTML = '<i class="fa-solid fa-pen-to-square" style="color:#0284c7;"></i> Con cambios guardados';
  } else {
    modPill.innerHTML = '<i class="fa-solid fa-lock" style="color:#10b981;"></i> Sin cambios (Intacta)';
  }

  const bcvEl = document.getElementById('vpRepTasaBcv');
  if (t.tasa_bcv > 0) {
    bcvEl.innerHTML = `<i class="fa-solid fa-building-columns" style="color:var(--primary);"></i> Tasa BCV: <strong>Bs. ${Number(t.tasa_bcv).toLocaleString('es-VE', {minimumFractionDigits:2, maximumFractionDigits:2})}</strong>`;
  } else {
    bcvEl.innerHTML = `<i class="fa-solid fa-building-columns" style="color:var(--primary);"></i> Tasa BCV: <strong>No registrada</strong>`;
  }

  // KPIs
  document.getElementById('vpKpiNeto').textContent = formatCurrencyUsd(t.neto_pagar);
  document.getElementById('vpKpiNetoBs').textContent = '≈ ' + formatCurrencyBs(t.neto_bs);

  document.getElementById('vpKpiAsig').textContent = formatCurrencyUsd(t.total_asignaciones);
  document.getElementById('vpKpiAsigBs').textContent = '≈ ' + formatCurrencyBs(t.asignaciones_bs);

  document.getElementById('vpKpiDed').textContent = formatCurrencyUsd(t.total_deducciones);
  document.getElementById('vpKpiDedBs').textContent = '≈ ' + formatCurrencyBs(t.deducciones_bs);

  document.getElementById('vpKpiEmpleados').textContent = (t.total_empleados || 0) + ' trab.';
  document.getElementById('vpKpiPromedio').textContent = 'Prom: ' + formatCurrencyUsd(t.promedio_neto_usd);
  document.getElementById('vpKpiDiasProm').textContent = 'Días prom: ' + (t.promedio_dias || 15) + ' d.';

  // Desgloses
  renderizarListaDesglose('vpListaAsignaciones', data.asignaciones_desglose, '#0f766e');
  renderizarListaDesglose('vpListaDeducciones', data.deducciones_desglose, '#e11d48');

  // Gráficas de Dona
  renderizarDonutEstructura(t);
  renderizarDonutCargos(data.cargos, data.deducciones_desglose);
}

function renderizarListaDesglose(containerId, items, dotColor) {
  const el = document.getElementById(containerId);
  if (!el) return;
  if (!items || items.length === 0) {
    el.innerHTML = '<span style="font-size:11.5px; color:var(--text-muted);">Sin registros en este ciclo.</span>';
    return;
  }
  let html = '';
  items.forEach(it => {
    html += `
      <div class="vp-breakdown-item">
        <span class="vp-breakdown-item-name">
          <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:${dotColor}; margin-right:5px;"></span>
          ${it.nombre || 'Concepto'}
        </span>
        <span class="vp-breakdown-item-val">${formatCurrencyUsd(it.total_monto)}</span>
      </div>
    `;
  });
  el.innerHTML = html;
}

function renderizarDonutEstructura(totales) {
  if (typeof Chart === 'undefined') return;

  const canvas = document.getElementById('vpChartEstructura');
  if (!canvas) return;

  if (window._modalChartEstructura) {
    window._modalChartEstructura.destroy();
  }

  const neto = Number(totales.neto_pagar || 0);
  const ded = Number(totales.total_deducciones || 0);
  const total = neto + ded;
  const pctNeto = total > 0 ? Math.round((neto / total) * 100) : 0;
  const pctDed = total > 0 ? (100 - pctNeto) : 0;

  const sliceData = [
    { label: 'Neto a Cobrar', pct: pctNeto, val: neto, color: '#0284c7' },
    { label: 'Retenciones Ley', pct: pctDed, val: ded, color: '#e11d48' }
  ];

  const centerEl = document.getElementById('vpCenterEstructura');
  const pctEl = document.getElementById('vpPctEstructura');
  const subEl = document.getElementById('vpSubEstructura');
  const valEl = document.getElementById('vpValEstructura');

  function setEstructuraCenter(idx) {
    const item = sliceData[idx];
    if (!item) return;
    if (pctEl) {
      pctEl.textContent = item.pct + '%';
      pctEl.style.color = item.color;
    }
    if (subEl) subEl.textContent = item.label;
    if (valEl) valEl.textContent = formatCurrencyUsd(item.val);
    if (centerEl) centerEl.classList.add('is-hovered');

    const items = document.querySelectorAll('#vpLegendEstructura .vp-donut-legend-item');
    items.forEach((it, i) => {
      if (i === idx) it.classList.add('active-slice');
      else it.classList.remove('active-slice');
    });
  }

  function resetEstructuraCenter() {
    setEstructuraCenter(0);
    if (centerEl) centerEl.classList.remove('is-hovered');
    const items = document.querySelectorAll('#vpLegendEstructura .vp-donut-legend-item');
    items.forEach(it => it.classList.remove('active-slice'));
  }

  // Inicializar estado central
  resetEstructuraCenter();

  const ctx = canvas.getContext('2d');
  window._modalChartEstructura = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Neto a Cobrar', 'Retenciones Ley'],
      datasets: [{
        data: [neto, ded],
        backgroundColor: ['#0284c7', '#e11d48'],
        hoverBackgroundColor: ['#0369a1', '#be123c'],
        borderRadius: 4,
        spacing: 1,
        borderWidth: 0,
        hoverOffset: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '72%',
      animation: {
        animateScale: true,
        animateRotate: true,
        duration: 700
      },
      onHover: function(evt, elements) {
        if (elements && elements.length > 0) {
          setEstructuraCenter(elements[0].index);
        } else {
          resetEstructuraCenter();
        }
      },
      plugins: {
        legend: { display: false },
        tooltip: { enabled: false }
      }
    }
  });

  canvas.onmouseleave = function() {
    resetEstructuraCenter();
  };

  // Leyenda interactiva sincronizada
  const leg = document.getElementById('vpLegendEstructura');
  if (leg) {
    leg.innerHTML = `
      <div class="vp-donut-legend-item" data-index="0">
        <span class="vp-donut-legend-label">
          <span class="vp-donut-legend-dot" style="background:#0284c7;"></span>
          Neto a Cobrar (${pctNeto}%)
        </span>
        <span class="vp-donut-legend-val">${formatCurrencyUsd(neto)}</span>
      </div>
      <div class="vp-donut-legend-item" data-index="1">
        <span class="vp-donut-legend-label">
          <span class="vp-donut-legend-dot" style="background:#e11d48;"></span>
          Retenciones Legales (${pctDed}%)
        </span>
        <span class="vp-donut-legend-val">${formatCurrencyUsd(ded)}</span>
      </div>
    `;

    leg.querySelectorAll('.vp-donut-legend-item').forEach(item => {
      const idx = parseInt(item.getAttribute('data-index'));
      item.addEventListener('mouseenter', () => {
        setEstructuraCenter(idx);
        if (window._modalChartEstructura) {
          window._modalChartEstructura.setActiveElements([{ datasetIndex: 0, index: idx }]);
          window._modalChartEstructura.update();
        }
      });
      item.addEventListener('mouseleave', () => {
        resetEstructuraCenter();
        if (window._modalChartEstructura) {
          window._modalChartEstructura.setActiveElements([]);
          window._modalChartEstructura.update();
        }
      });
    });
  }
}

function renderizarDonutCargos(cargos, deducciones) {
  if (typeof Chart === 'undefined') return;

  const canvas = document.getElementById('vpChartCargos');
  if (!canvas) return;

  if (window._modalChartCargos) {
    window._modalChartCargos.destroy();
  }

  const palette = ['#0d9488', '#0284c7', '#6366f1', '#8b5cf6', '#f59e0b', '#ec4899', '#10b981'];

  let labels = [];
  let dataVals = [];
  let colors = [];
  let totalSuma = 0;

  if (cargos && cargos.length > 0) {
    cargos.forEach((c, i) => {
      labels.push(c.cargo || 'Cargo ' + (i+1));
      const val = Number(c.total_neto || 0);
      dataVals.push(val);
      totalSuma += val;
      colors.push(palette[i % palette.length]);
    });
  } else if (deducciones && deducciones.length > 0) {
    deducciones.forEach((d, i) => {
      labels.push(d.nombre || 'Deducción');
      const val = Number(d.total_monto || 0);
      dataVals.push(val);
      totalSuma += val;
      colors.push(palette[i % palette.length]);
    });
  } else {
    labels = ['Sin datos'];
    dataVals = [1];
    colors = ['#cbd5e1'];
  }

  const centerEl = document.getElementById('vpCenterCargos');
  const pctEl = document.getElementById('vpPctCargos');
  const subEl = document.getElementById('vpSubCargos');
  const valEl = document.getElementById('vpValCargos');

  function setCargosCenter(idx) {
    if (idx < 0 || idx >= labels.length) return;
    const val = dataVals[idx];
    const pct = totalSuma > 0 ? Math.round((val / totalSuma) * 100) : 0;
    const col = colors[idx];
    if (pctEl) {
      pctEl.textContent = pct + '%';
      pctEl.style.color = col;
    }
    if (subEl) subEl.textContent = labels[idx];
    if (valEl) valEl.textContent = totalSuma > 0 ? formatCurrencyUsd(val) : '—';
    if (centerEl) centerEl.classList.add('is-hovered');

    const items = document.querySelectorAll('#vpLegendCargos .vp-donut-legend-item');
    items.forEach((it, i) => {
      if (i === idx) it.classList.add('active-slice');
      else it.classList.remove('active-slice');
    });
  }

  function resetCargosCenter() {
    setCargosCenter(0);
    if (centerEl) centerEl.classList.remove('is-hovered');
    const items = document.querySelectorAll('#vpLegendCargos .vp-donut-legend-item');
    items.forEach(it => it.classList.remove('active-slice'));
  }

  resetCargosCenter();

  const ctx = canvas.getContext('2d');
  window._modalChartCargos = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: labels,
      datasets: [{
        data: dataVals,
        backgroundColor: colors,
        borderRadius: 4,
        spacing: 1,
        borderWidth: 0,
        hoverOffset: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '72%',
      animation: {
        animateScale: true,
        animateRotate: true,
        duration: 700
      },
      onHover: function(evt, elements) {
        if (elements && elements.length > 0) {
          setCargosCenter(elements[0].index);
        } else {
          resetCargosCenter();
        }
      },
      plugins: {
        legend: { display: false },
        tooltip: { enabled: false }
      }
    }
  });

  canvas.onmouseleave = function() {
    resetCargosCenter();
  };

  // Leyenda interactiva de cargos sincronizada
  const leg = document.getElementById('vpLegendCargos');
  if (leg) {
    let legHtml = '';
    labels.forEach((lbl, i) => {
      const val = dataVals[i];
      const pct = totalSuma > 0 ? Math.round((val / totalSuma) * 100) : 0;
      legHtml += `
        <div class="vp-donut-legend-item" data-index="${i}">
          <span class="vp-donut-legend-label">
            <span class="vp-donut-legend-dot" style="background:${colors[i]};"></span>
            ${lbl} (${pct}%)
          </span>
          <span class="vp-donut-legend-val">${formatCurrencyUsd(val)}</span>
        </div>
      `;
    });
    leg.innerHTML = legHtml;

    leg.querySelectorAll('.vp-donut-legend-item').forEach(item => {
      const idx = parseInt(item.getAttribute('data-index'));
      item.addEventListener('mouseenter', () => {
        setCargosCenter(idx);
        if (window._modalChartCargos) {
          window._modalChartCargos.setActiveElements([{ datasetIndex: 0, index: idx }]);
          window._modalChartCargos.update();
        }
      });
      item.addEventListener('mouseleave', () => {
        resetCargosCenter();
        if (window._modalChartCargos) {
          window._modalChartCargos.setActiveElements([]);
          window._modalChartCargos.update();
        }
      });
    });
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
  if (window._modalChartEstructura) {
    window._modalChartEstructura.destroy();
    window._modalChartEstructura = null;
  }
  if (window._modalChartCargos) {
    window._modalChartCargos.destroy();
    window._modalChartCargos = null;
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
