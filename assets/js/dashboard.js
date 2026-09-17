/**
 * Dashboard 2.0 — Charts + Interactions (100% Offline)
 */

document.addEventListener('DOMContentLoaded', () => {
    initClock();
    initDashboardCharts();
    initCommandPalette();
    initRangeToggles();

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
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#0284c7',
                            pointBorderWidth: 2.5,
                            pointRadius: 4,
                            pointHoverRadius: 8,
                            pointHoverBackgroundColor: '#0284c7',
                            pointHoverBorderColor: '#fff',
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
                            borderWidth: 2,
                            pointBackgroundColor: '#0f766e',
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            order: 2
                        },
                        {
                            label: 'Total Deducciones',
                            data: deducciones,
                            borderColor: '#e11d48',
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.4,
                            borderWidth: 2,
                            pointBackgroundColor: '#e11d48',
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            order: 3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false }, // usamos leyenda custom
                        tooltip: {
                            callbacks: {
                                label: (c) => `${c.dataset.label}: ${formatUSD(c.parsed.y)}`
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11, weight: '600' } }
                        },
                        y: {
                            border: { display: false },
                            grid: { color: 'rgba(226, 232, 240, 0.6)' },
                            ticks: {
                                font: { size: 11 },
                                callback: (v) => '$' + v.toLocaleString('es-VE')
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

            // Plugin para texto central dentro de la dona (estilo Rounded Donut)
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
                    const centerY = (top + bottom) / 2 - 2;

                    ctx.save();
                    // Subtítulo central
                    ctx.font = '600 11px "Plus Jakarta Sans", sans-serif';
                    ctx.fillStyle = isDark ? '#94a3b8' : '#64748b';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText('Presupuesto', centerX, centerY - 10);

                    // Valor principal destacado en el centro
                    ctx.font = '800 16px "Plus Jakarta Sans", sans-serif';
                    ctx.fillStyle = isDark ? '#f8fafc' : '#0f172a';
                    ctx.fillText(formatUSD(total), centerX, centerY + 10);
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
