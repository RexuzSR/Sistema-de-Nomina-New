/**
 * Dashboard 2.0 — Charts + Interactions (100% Offline)
 */

document.addEventListener('DOMContentLoaded', () => {
    initClock();
    initDashboardCharts();
    initCommandPalette();
    initRangeToggles();
    initChartLegendTooltips();

    const savedCur = localStorage.getItem('dash-currency');
    if (savedCur) {
        window.switchCurrency(savedCur);
    }
});

/* ─── RELOJ EN VIVO ─────────────────────────────────────── */
function initClock() {
    const clock = document.getElementById('liveClock');
    const heroClock = document.getElementById('heroClock');
    if (!clock && !heroClock) return;
    
    const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    
    const tick = () => {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('es-VE', {hour:'2-digit', minute:'2-digit', hour12: true});
        if (clock) {
            clock.textContent = timeStr;
        }
        if (heroClock) {
            heroClock.textContent = `${String(now.getDate()).padStart(2,'0')} ${meses[now.getMonth()]} ${now.getFullYear()} · ${timeStr}`;
        }
    };
    tick();
    setInterval(tick, 10000);
}

/* ─── CHARTS ────────────────────────────────────────────── */
function initDashboardCharts() {
    if (typeof Chart === 'undefined') return;

    Chart.defaults.font.family = "'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif";
    Chart.defaults.color = '#64748b';
    Chart.defaults.plugins.tooltip.padding = 12;
    Chart.defaults.plugins.tooltip.cornerRadius = 10;
    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(10, 15, 28, 0.94)';
    Chart.defaults.plugins.tooltip.titleFont = { weight: '700', size: 13 };
    // Configuración global para todas las gráficas de dona (estilo "ROUNDED DONUT")
    if (Chart.defaults.elements && Chart.defaults.elements.arc) {
        Chart.defaults.elements.arc.borderRadius = 14;
        Chart.defaults.elements.arc.spacing = 6;
        Chart.defaults.elements.arc.borderWidth = 0;
    }
    if (Chart.defaults.datasets && Chart.defaults.datasets.doughnut) {
        Chart.defaults.datasets.doughnut.cutout = '72%';
    }

    const formatUSD = (v) => '$ ' + Number(v).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    /* Gráfico Evolución */
    const ctxEvo = document.getElementById('chartEvolucion');
    if (ctxEvo && window.dashboardChartData) {
        const d = window.dashboardChartData;
        const labels = d.labels || [];
        const asignaciones = d.asignaciones || [];
        const deducciones = d.deducciones || [];
        const neto = d.neto || [];

        if (labels.length === 0) {
            ctxEvo.parentElement.innerHTML = `
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:240px;color:#94a3b8;text-align:center;">
                    <i class="fa-solid fa-chart-line" style="font-size:36px;margin-bottom:12px;color:#cbd5e1;"></i>
                    <p style="margin:0;font-size:14px;font-weight:600;color:#475569;">Aún no hay historial de nóminas</p>
                    <p style="margin:4px 0 0;font-size:12px;">Al generar periodos, la tendencia aparecerá aquí.</p>
                </div>`;
        } else {
            const ctx2d = ctxEvo.getContext('2d');
            const gNeto = ctx2d.createLinearGradient(0,0,0,300);
            gNeto.addColorStop(0, 'rgba(2, 132, 199, 0.30)');
            gNeto.addColorStop(1, 'rgba(2, 132, 199, 0)');

            const gAsig = ctx2d.createLinearGradient(0,0,0,300);
            gAsig.addColorStop(0, 'rgba(15, 118, 110, 0.22)');
            gAsig.addColorStop(1, 'rgba(15, 118, 110, 0)');

            window._chartEvo = new Chart(ctx2d, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Neto a Pagar',
                            data: neto,
                            borderColor: '#0284c7',
                            backgroundColor: gNeto,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#0284c7',
                            pointBorderWidth: 2.5,
                            pointRadius: 5,
                            pointHoverRadius: 9,
                            pointHitRadius: 18,
                            pointHoverBackgroundColor: '#0284c7',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverBorderWidth: 3,
                            order: 1
                        },
                        {
                            label: 'Total Asignaciones',
                            data: asignaciones,
                            borderColor: '#0f766e',
                            backgroundColor: gAsig,
                            fill: false,
                            borderDash: [6, 6],
                            tension: 0.4,
                            borderWidth: 2.2,
                            pointBackgroundColor: '#0f766e',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            pointHitRadius: 18,
                            pointHoverBackgroundColor: '#0f766e',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverBorderWidth: 3,
                            order: 2
                        },
                        {
                            label: 'Total Deducciones',
                            data: deducciones,
                            borderColor: '#e11d48',
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.4,
                            borderWidth: 2.2,
                            pointBackgroundColor: '#e11d48',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            pointHitRadius: 18,
                            pointHoverBackgroundColor: '#e11d48',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverBorderWidth: 3,
                            order: 3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false }, // Usamos leyenda custom interactiva en el header
                        tooltip: {
                            enabled: true,
                            usePointStyle: true,
                            boxPadding: 6,
                            padding: 13,
                            cornerRadius: 12,
                            backgroundColor: 'rgba(15, 23, 42, 0.96)',
                            titleColor: '#f8fafc',
                            titleFont: { size: 12.5, weight: '700' },
                            bodyColor: '#e2e8f0',
                            bodyFont: { size: 11.5 },
                            bodySpacing: 6,
                            borderColor: 'rgba(255, 255, 255, 0.12)',
                            borderWidth: 1,
                            callbacks: {
                                title: (items) => {
                                    const label = items[0]?.label || '';
                                    return `📅 Periodo: ${label}`;
                                },
                                label: (c) => {
                                    const valUsd = c.parsed.y;
                                    const tasa = Number(d.tasaBcv || 0);
                                    const usdStr = formatUSD(valUsd);
                                    if (tasa > 0) {
                                        const bsVal = valUsd * tasa;
                                        const bsStr = 'Bs. ' + Number(bsVal).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                        return ` ${c.dataset.label}: ${usdStr} (${bsStr})`;
                                    }
                                    return ` ${c.dataset.label}: ${usdStr}`;
                                },
                                afterBody: (items) => {
                                    const dataIdx = items[0]?.dataIndex;
                                    const chartInst = items[0]?.chart;
                                    const offset = chartInst?._rangeStartIdx || 0;
                                    const actualIdx = (dataIdx !== undefined) ? (offset + dataIdx) : null;
                                    const lines = [];

                                    if (actualIdx !== null && d.empleados && d.empleados[actualIdx] !== undefined) {
                                        const emps = d.empleados[actualIdx];
                                        lines.push(`👥 Personal procesado: ${emps} trabajador${emps === 1 ? '' : 'es'}`);
                                    }
                                    return lines;
                                },
                                footer: (items) => {
                                    return '───────────────────────────\n💡 Neto = Asignaciones − Deducciones';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 11, weight: '600' },
                                maxRotation: 0,
                                autoSkip: true,
                                maxTicksLimit: 6
                            }
                        },
                        y: {
                            border: { display: false },
                            grid: { color: 'rgba(226, 232, 240, 0.6)' },
                            beginAtZero: true,
                            ticks: {
                                font: { size: 11 },
                                precision: 0,
                                callback: (v) => '$ ' + Number(v).toLocaleString('es-VE')
                            }
                        }
                    }
                }
            });
        }
    }

    /* Gráfico Cargos */
    const ctxCargos = document.getElementById('chartCargos');
    if (ctxCargos && window.dashboardCargosData) {
        const cd = window.dashboardCargosData;
        const lbl = cd.labels || [];
        const data = cd.presupuesto || [];
        const heads = cd.headcount || [];

        if (lbl.length === 0 || data.every(v => v === 0)) {
            ctxCargos.parentElement.innerHTML = `
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:240px;color:#94a3b8;text-align:center;">
                    <i class="fa-solid fa-id-badge" style="font-size:36px;margin-bottom:12px;color:#cbd5e1;"></i>
                    <p style="margin:0;font-size:14px;font-weight:600;color:#475569;">Sin cargos activos</p>
                    <p style="margin:4px 0 0;font-size:12px;">Asigna cargos para ver el balance.</p>
                </div>`;
        } else {
            // Configuración global de donas con estilo Rounded Donut
            if (typeof Chart !== 'undefined' && Chart.defaults && Chart.defaults.elements && Chart.defaults.elements.arc) {
                Chart.defaults.elements.arc.borderRadius = 14;
                Chart.defaults.elements.arc.spacing = 6;
                Chart.defaults.elements.arc.borderWidth = 0;
            }

            // Plugin para texto central dentro de la dona (estilo Rounded Donut con tamaño elástico al zoom)
            const roundedDonutCenterPlugin = {
                id: 'roundedDonutCenter',
                beforeDraw(chart) {
                    if (chart.config.type !== 'doughnut') return;
                    const { ctx, chartArea } = chart;
                    if (!chartArea) return;
                    const { top, bottom, left, right } = chartArea;
                    const dataset = chart.data.datasets[0];
                    if (!dataset || !dataset.data) return;

                    const total = dataset.data.reduce((a, b) => a + Number(b || 0), 0);
                    const isDark = document.documentElement.classList.contains('dark-theme');
                    const centerX = (left + right) / 2;
                    const centerY = (top + bottom) / 2;

                    const innerR = chart.innerRadius || (Math.min(right - left, bottom - top) * 0.35);
                    const valSize = Math.max(13, Math.min(22, Math.round(innerR * 0.36)));
                    const lblSize = Math.max(9, Math.min(12, Math.round(innerR * 0.2)));

                    ctx.save();
                    // Subtítulo central
                    ctx.font = `600 ${lblSize}px "Plus Jakarta Sans", sans-serif`;
                    ctx.fillStyle = isDark ? '#94a3b8' : '#64748b';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText('Presupuesto', centerX, centerY - (valSize * 0.7));

                    // Valor principal destacado en el centro
                    ctx.font = `800 ${valSize}px "Plus Jakarta Sans", sans-serif`;
                    ctx.fillStyle = isDark ? '#f8fafc' : '#0f172a';
                    ctx.fillText(formatUSD(total), centerX, centerY + (valSize * 0.7));
                    ctx.restore();
                }
            };

            const palette = ['#0284c7', '#0f766e', '#f59e0b', '#ec4899', '#8b5cf6', '#10b981', '#3b82f6', '#64748b'];
            window._chartCargos = new Chart(ctxCargos, {
                type: 'doughnut',
                data: {
                    labels: lbl,
                    datasets: [{
                        data,
                        backgroundColor: palette.slice(0, lbl.length),
                        borderWidth: 0,
                        borderRadius: 14,
                        spacing: 6,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    layout: {
                        padding: {
                            bottom: 10,
                            top: 4,
                            left: 6,
                            right: 6
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                padding: 14,
                                font: { size: 11.5, weight: '600' }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: (c) => ` ${formatUSD(c.parsed)} · ${heads[c.dataIndex] || 0} emp.`
                            }
                        }
                    }
                },
                plugins: [roundedDonutCenterPlugin]
            });
        }
    }

    setupChartResponsiveSync();
}

/* ─── RESPONSIVE SYNC (ZOOM, UNZOOM, MOBILE) ────────────── */
function setupChartResponsiveSync() {
    let resizeTimer = null;
    const triggerResize = () => {
        if (resizeTimer) cancelAnimationFrame(resizeTimer);
        resizeTimer = requestAnimationFrame(() => {
            if (window._chartEvo && typeof window._chartEvo.resize === 'function') {
                const wrapEvo = document.getElementById('chartEvolucion')?.parentElement;
                if (wrapEvo && wrapEvo.clientWidth > 0) {
                    window._chartEvo.resize();
                }
            }
            if (window._chartCargos && typeof window._chartCargos.resize === 'function') {
                const wrapCargos = document.getElementById('chartCargos')?.parentElement;
                if (wrapCargos && wrapCargos.clientWidth > 0) {
                    window._chartCargos.resize();
                }
            }
        });
    };

    // 1. ResizeObserver en cada wrap de canvas
    if (window.ResizeObserver) {
        const ro = new ResizeObserver(() => {
            triggerResize();
        });
        document.querySelectorAll('.chart-canvas-wrap').forEach(wrap => {
            ro.observe(wrap);
        });
        const grid = document.querySelector('.analytics-grid');
        if (grid) ro.observe(grid);
        const container = document.querySelector('main.container');
        if (container) ro.observe(container);
    }

    // 2. Escuchar resize de la ventana
    window.addEventListener('resize', triggerResize, { passive: true });

    // 3. Escuchar visualViewport para zoom (Ctrl + / Ctrl - / pinch to zoom)
    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', triggerResize, { passive: true });
    }

    // 4. Orientación móvil y cambios de visibilidad
    window.addEventListener('orientationchange', () => {
        setTimeout(triggerResize, 100);
        setTimeout(triggerResize, 300);
    });

    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            triggerResize();
        }
    });

    // 5. Ajuste diferido tras completar renderizado inicial
    setTimeout(triggerResize, 150);
}

/* ─── RANGE TOGGLE (6Q / 12Q / Todo) ────────────────────── */
function initRangeToggles() {
    document.querySelectorAll('.range-toggle').forEach(group => {
        group.querySelectorAll('.range-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                group.querySelectorAll('.range-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const range = btn.dataset.range;
                const chart = window._chartEvo;
                if (!chart || !window.dashboardChartData) return;

                const fullLabels = window.dashboardChartData.labels || [];
                const total = fullLabels.length;
                const n = range === 'all' ? total : Math.min(parseInt(range), total);
                const startIdx = Math.max(0, total - n);

                chart._rangeStartIdx = startIdx;
                chart.data.labels = fullLabels.slice(startIdx);
                chart.data.datasets[0].data = (window.dashboardChartData.neto || []).slice(startIdx);
                chart.data.datasets[1].data = (window.dashboardChartData.asignaciones || []).slice(startIdx);
                chart.data.datasets[2].data = (window.dashboardChartData.deducciones || []).slice(startIdx);
                chart.update();
            });
        });
    });
}

/* ─── COMMAND PALETTE ───────────────────────────────────── */
/* ─── COMMAND PALETTE (Controlado de forma global en footer.php) ─── */
function initCommandPalette() {
    // La paleta de comandos y búsqueda en tiempo real se gestiona globalmente en footer.php
}

/* ─── BOTÓN SINCRONIZAR BCV ─────────────────────────────── */
window.sincronizarBcvLive = async function(e) {
    if (e) e.stopPropagation();
    const icon = document.getElementById('dashSyncIcon');
    const valEl = document.getElementById('dashBcvVal');

    const spinTarget = icon ? (icon.querySelector('i') || icon) : null;
    if (spinTarget) spinTarget.classList.add('fa-spin');

    if (!navigator.onLine) {
        showToast('Modo Offline: usando tasa guardada en BD.');
        if (spinTarget) spinTarget.classList.remove('fa-spin');
        return;
    }

    try {
        const res = await fetch('index.php?r=tasas/sincronizar&json=1', { signal: AbortSignal.timeout(5000) });
        const data = await res.json();
        if (data?.tasa) {
            const formatted = Number(data.tasa).toLocaleString('es-VE', { minimumFractionDigits: 4, maximumFractionDigits: 4 });
            if (valEl) valEl.innerHTML = `${formatted} <span class="bcv-card-unit">Bs./$</span>`;
            showToast(data.mensaje || 'Tasa BCV sincronizada ✓');
        } else {
            showToast('No se pudo obtener la tasa en línea.');
        }
    } catch {
        showToast('Conexión inestable. Usando tasa de BD.');
    } finally {
        if (spinTarget) spinTarget.classList.remove('fa-spin');
    }
};

/* ─── TOAST ─────────────────────────────────────────────── */
function showToast(msg) {
    let toast = document.getElementById('dashLiveToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'dashLiveToast';
        toast.className = 'dash-toast';
        document.body.appendChild(toast);
    }
    toast.innerHTML = '<i class="fa-solid fa-circle-info"></i> ' + msg;
    requestAnimationFrame(() => toast.classList.add('show'));
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => toast.classList.remove('show'), 4000);
}

/* ─── CURRENCY TOGGLE (USD ↔ Bs.) ───────────────────────── */
window.switchCurrency = function(cur, btn) {
    const targetCur = (cur || 'usd').toLowerCase();
    document.querySelectorAll('.cur-btn').forEach(b => {
        if (b.textContent.trim().toLowerCase() === targetCur) {
            b.classList.add('active');
        } else {
            b.classList.remove('active');
        }
    });

    document.querySelectorAll('.usd-value').forEach(el => el.style.display = targetCur === 'usd' ? '' : 'none');
    document.querySelectorAll('.bs-value').forEach(el => el.style.display = targetCur === 'bs' ? '' : 'none');
    localStorage.setItem('dash-currency', targetCur);

    if (window._chartCargos) {
        window._chartCargos.update();
    }
};

/* ─── DARK MODE (Manejado globalmente en footer.php) ─── */
// toggleTheme se gestiona en footer.php con animación suave del icono

// Exportar globalmente para llamadas desde atributos onclick del DOM
window.openCommandPalette = openCommandPalette;
window.closeCommandPalette = closeCommandPalette;
window.showToast = showToast;

/* ─── TOOLTIPS EXPLICATIVOS PARA LEYENDA DEL HEADER ─────── */
function initChartLegendTooltips() {
    const legendContainer = document.getElementById('chartEvoLegend');
    if (!legendContainer) return;

    const legendExplanations = {
        asignaciones: {
            title: 'Asignaciones Totales',
            dotColor: '#0f766e',
            badge: '+ Ingresos',
            badgeBg: 'rgba(15, 118, 110, 0.25)',
            badgeColor: '#2dd4bf',
            pointInfo: '🟢 Puntos verdes: Marcan el total bruto devengado en la quincena.',
            desc: 'Suma de todas las percepciones económicas generadas por los trabajadores antes de cualquier deducción legal o laboral.',
            items: [
                '<strong>Sueldo base:</strong> Días efectivamente laborados en la quincena.',
                '<strong>Horas extras:</strong> Diurnas y nocturnas con recargo legal.',
                '<strong>Feriados y descansos:</strong> Compensados según la LOTTT.',
                '<strong>Bonos y primas:</strong> Bonos especiales y complementos salariales.'
            ],
            hint: 'Haz clic para alternar la línea verde · Puntos = total devengado'
        },
        deducciones: {
            title: 'Deducciones Totales',
            dotColor: '#e11d48',
            badge: '− Retenciones',
            badgeBg: 'rgba(225, 29, 72, 0.25)',
            badgeColor: '#fb7185',
            pointInfo: '🔴 Puntos rojos: Marcan los descuentos y retenciones del ciclo.',
            desc: 'Total de retenciones obligatorias de ley venezolana y descuentos autorizados aplicados a los salarios.',
            items: [
                '<strong>IVSS (SSO):</strong> 4% retención de seguro social.',
                '<strong>RPE (Paro Forzoso):</strong> 0.5% régimen prestacional de empleo.',
                '<strong>FAOV (Banavih):</strong> 1% ahorro habitacional obligatorio.',
                '<strong>Otras deducciones:</strong> Inasistencias, cuotas de préstamos o anticipos.'
            ],
            hint: 'Haz clic para alternar la línea roja · Puntos = total retenido'
        },
        neto: {
            title: 'Neto a Pagar',
            dotColor: '#0284c7',
            badge: '= Desembolso Real',
            badgeBg: 'rgba(2, 132, 199, 0.25)',
            badgeColor: '#38bdf8',
            pointInfo: '🔵 Puntos azules: Marcan el monto líquido definitivo pagado.',
            desc: 'Monto líquido y definitivo efectivamente desembolsado o transferido a las cuentas bancarias de los empleados.',
            formula: 'Neto = Total Asignaciones − Total Deducciones',
            items: [
                '<strong>Desembolso efectivo:</strong> Saldo líquido transferido al personal.',
                '<strong>Garantía salarial:</strong> Si las deducciones igualan o superan asignaciones, el neto es $0.'
            ],
            hint: 'Haz clic para alternar la línea azul · Puntos = monto a cobrar'
        }
    };

    function getPopover() {
        let pop = document.getElementById('legendFloatingPopover');
        if (!pop) {
            pop = document.createElement('div');
            pop.id = 'legendFloatingPopover';
            pop.className = 'legend-floating-popover';
            pop.setAttribute('role', 'tooltip');
            pop.setAttribute('aria-live', 'polite');
            document.body.appendChild(pop);
        }
        return pop;
    }

    function positionPopover(targetEl, popEl) {
        const rect = targetEl.getBoundingClientRect();
        const popRect = popEl.getBoundingClientRect();
        const vw = window.innerWidth;
        const vh = window.innerHeight;

        let left = rect.left + (rect.width / 2) - (popRect.width / 2);
        if (left + popRect.width > vw - 14) {
            left = vw - popRect.width - 14;
        }
        if (left < 14) {
            left = 14;
        }

        let top = rect.bottom + 8;
        if (top + popRect.height > vh - 14) {
            top = Math.max(14, rect.top - popRect.height - 8);
        }

        popEl.style.top = `${Math.round(top)}px`;
        popEl.style.left = `${Math.round(left)}px`;
    }

    function buildPopoverHtml(data) {
        let itemsHtml = '';
        if (data.items && data.items.length) {
            itemsHtml = `<ul class="popover-list">${data.items.map(i => `<li>${i}</li>`).join('')}</ul>`;
        }

        let formulaHtml = '';
        if (data.formula) {
            formulaHtml = `<div class="popover-formula"><i class="fa-solid fa-calculator" style="margin-right:5px;"></i>${data.formula}</div>`;
        }

        return `
            <div class="popover-header">
                <div class="popover-title-wrap">
                    <span class="popover-title-dot" style="background:${data.dotColor};"></span>
                    <span>${data.title}</span>
                </div>
                <span class="popover-badge" style="background:${data.badgeBg};color:${data.badgeColor};">${data.badge}</span>
            </div>
            <div class="popover-point-badge">
                <span>${data.pointInfo}</span>
            </div>
            <p class="popover-desc">${data.desc}</p>
            ${itemsHtml}
            ${formulaHtml}
            <div class="popover-footer-hint">
                <i class="fa-solid fa-circle-info"></i>
                <span>${data.hint}</span>
            </div>
        `;
    }

    let hideTimer = null;

    function showTooltip(el) {
        const key = el.dataset.legend;
        const data = legendExplanations[key];
        if (!data) return;

        clearTimeout(hideTimer);
        const pop = getPopover();
        pop.innerHTML = buildPopoverHtml(data);
        pop.classList.add('show');
        positionPopover(el, pop);
    }

    function scheduleHide() {
        clearTimeout(hideTimer);
        hideTimer = setTimeout(() => {
            const pop = document.getElementById('legendFloatingPopover');
            if (pop) {
                pop.classList.remove('show');
            }
        }, 150);
    }

    const items = legendContainer.querySelectorAll('.lg-item');
    items.forEach(item => {
        // Hover
        item.addEventListener('mouseenter', () => showTooltip(item));
        item.addEventListener('mouseleave', scheduleHide);

        // Accesibilidad teclado
        item.addEventListener('focus', () => showTooltip(item));
        item.addEventListener('blur', scheduleHide);

        // Click / Touch: alternar dataset en la gráfica
        item.addEventListener('click', (e) => {
            const dsIdx = parseInt(item.dataset.datasetIndex);
            if (window._chartEvo && !isNaN(dsIdx)) {
                const isVisible = window._chartEvo.isDatasetVisible(dsIdx);
                window._chartEvo.setDatasetVisibility(dsIdx, !isVisible);
                window._chartEvo.update();
                item.classList.toggle('is-hidden', isVisible);
            }
        });
    });

    const pop = getPopover();
    pop.addEventListener('mouseenter', () => clearTimeout(hideTimer));
    pop.addEventListener('mouseleave', scheduleHide);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const pop = document.getElementById('legendFloatingPopover');
            if (pop) pop.classList.remove('show');
        }
    });

    window.addEventListener('scroll', () => {
        const pop = document.getElementById('legendFloatingPopover');
        if (pop && pop.classList.contains('show')) {
            pop.classList.remove('show');
        }
    }, { passive: true });
}

