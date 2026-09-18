<?php if (!empty($showWelcome)): ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const mainContainer = document.querySelector('main.container');
    if (mainContainer) {
      mainContainer.classList.add('door-dashboard-arrival');
      mainContainer.addEventListener('animationend', function() {
        mainContainer.classList.remove('door-dashboard-arrival');
      }, { once: true });
    }
  });
</script>
<?php endif; ?>

<!-- ═══════════════ FONDO AURORA ANIMADO ═══════════════ -->
<div class="aurora-bg" aria-hidden="true">
  <div class="aurora-blob aurora-1"></div>
  <div class="aurora-blob aurora-2"></div>
  <div class="aurora-blob aurora-3"></div>
  <div class="aurora-grid"></div>
</div>

<!-- ═══════════════ HERO EJECUTIVO ═══════════════ -->
<section class="hero">
  <div class="hero-content">
    <div class="hero-left">
      <div class="hero-eyebrow">
        <span class="live-dot"></span>
        <span>Sistema en vivo</span>
        <span class="hero-sep">·</span>
        <span id="heroClock"><?= date('d M Y · H:i') ?></span>
      </div>
      <h1 class="hero-title">
        Bienvenido,<br>
        <span class="hero-gradient">aquí está tu operación</span>
      </h1>
      <p class="hero-subtitle">
        Tienes <strong><?= $permisosPendientes ?></strong> permisos por aprobar 
        y <strong><?= $totalNominasCount ?></strong> nóminas procesadas en el histórico.
      </p>
    </div>
    <div class="hero-right">
      <!-- Selector USD / Bs. integrado dentro de la tarjeta de bienvenida -->
      <div class="currency-toggle hero-currency-toggle">
        <button class="cur-btn active" onclick="switchCurrency('usd', this)" type="button">USD</button>
        <button class="cur-btn" onclick="switchCurrency('bs', this)" type="button">Bs.</button>
      </div>

      <!-- Tasa BCV como tarjeta destacada -->
      <div class="bcv-card" onclick="sincronizarBcvLive(event)">
        <div class="bcv-card-top">
          <span class="bcv-card-label">
            <i class="fa-solid fa-coins"></i> TASA BCV
          </span>
          <span class="bcv-card-sync" id="dashSyncIcon">
            <i class="fa-solid fa-arrows-rotate"></i>
          </span>
        </div>
        <div class="bcv-card-value" id="dashBcvVal">
          <?= $valorTasaBcv > 0 ? number_format($valorTasaBcv, 4, ',', '.') : '—' ?>
          <span class="bcv-card-unit">Bs./$</span>
        </div>
        <div class="bcv-card-foot">
          <i class="fa-regular fa-calendar"></i>
          <?= $fechaTasaBcv ? date('d/m/Y', strtotime($fechaTasaBcv)) : 'Pendiente' ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Acciones rápidas flotantes -->
  <div class="hero-quick-actions">
    <a href="index.php?r=empleados/crear" class="quick-action">
      <span class="qa-icon" style="background:linear-gradient(135deg,#10b981,#059669);">
        <i class="fa-solid fa-user-plus"></i>
      </span>
      <span class="qa-text">
        <strong>Nuevo empleado</strong>
        <small>Registrar en plantilla</small>
      </span>
      <i class="fa-solid fa-arrow-right qa-arrow"></i>
    </a>
    <a href="index.php?r=periodos/crear" class="quick-action">
      <span class="qa-icon" style="background:linear-gradient(135deg,#0284c7,#0369a1);">
        <i class="fa-solid fa-calendar-plus"></i>
      </span>
      <span class="qa-text">
        <strong>Crear periodo</strong>
        <small>Nuevo ciclo de nómina</small>
      </span>
      <i class="fa-solid fa-arrow-right qa-arrow"></i>
    </a>
    <a href="index.php?r=nomina/generar" class="quick-action">
      <span class="qa-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);">
        <i class="fa-solid fa-bolt"></i>
      </span>
      <span class="qa-text">
        <strong>Generar nómina</strong>
        <small>Procesar liquidación</small>
      </span>
      <i class="fa-solid fa-arrow-right qa-arrow"></i>
    </a>
  </div>
</section>

<!-- ═══════════════ KPI GRID CON SPARKLINES ═══════════════ -->
<section class="kpi-grid">
  <!-- KPI 1: Última Nómina -->
  <a href="index.php?r=periodos" class="kpi-card kpi-emerald">
    <div class="kpi-glow"></div>
    <div class="kpi-head">
      <div class="kpi-icon" style="background:linear-gradient(135deg,#10b981,#059669);">
        <i class="fa-solid fa-money-bill-wave"></i>
      </div>
      <?php if ($variacionPorcentaje !== null): ?>
        <span class="kpi-tag <?= $variacionPorcentaje >= 0 ? 'kpi-tag-success' : 'kpi-tag-warning' ?>">
          <i class="fa-solid <?= $variacionPorcentaje >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' ?>"></i>
          <?= ($variacionPorcentaje > 0 ? '+' : '') . $variacionPorcentaje ?>%
        </span>
      <?php elseif ($excesoDeducciones > 0): ?>
        <span class="kpi-tag kpi-tag-warning" title="Deducciones superan asignaciones en este ciclo">
          <i class="fa-solid fa-triangle-exclamation"></i> Saldo retención
        </span>
      <?php else: ?>
        <span class="kpi-tag kpi-tag-neutral">
          <i class="fa-solid fa-check"></i> Ciclo vigente
        </span>
      <?php endif; ?>
    </div>
    <div class="kpi-label">Última nómina procesada</div>
    <div class="kpi-value usd-value">
      <span class="kpi-cur">$</span><?= number_format($netoEfectivo, 2, ',', '.') ?>
    </div>
    <div class="kpi-value bs-value" style="display:none;">
      <span class="kpi-cur">Bs.</span><?= number_format($netoEfectivoBs, 2, ',', '.') ?>
    </div>
    <div class="kpi-secondary usd-value">
      <?php if ($excesoDeducciones > 0): ?>
        <span style="color:#ea580c;font-size:11.5px;"><i class="fa-solid fa-circle-exclamation"></i> Retenciones pendientes: $<?= number_format($excesoDeducciones, 2, ',', '.') ?></span>
      <?php else: ?>
        ≈ Bs. <?= number_format($netoEfectivoBs, 2, ',', '.') ?>
      <?php endif; ?>
    </div>
    <div class="kpi-secondary bs-value" style="display:none;">
      ≈ $ <?= number_format($netoEfectivo, 2, ',', '.') ?>
    </div>
    <!-- Sparkline SVG inline -->
    <svg class="kpi-spark" viewBox="0 0 100 30" preserveAspectRatio="none">
      <defs>
        <linearGradient id="sparkGreen" x1="0" x2="0" y1="0" y2="1">
          <stop offset="0%" stop-color="#10b981" stop-opacity="0.35"/>
          <stop offset="100%" stop-color="#10b981" stop-opacity="0"/>
        </linearGradient>
      </defs>
      <path d="M0,22 L15,18 L30,20 L45,12 L60,15 L75,8 L100,4 L100,30 L0,30 Z" fill="url(#sparkGreen)"/>
      <path d="M0,22 L15,18 L30,20 L45,12 L60,15 L75,8 L100,4" fill="none" stroke="#10b981" stroke-width="2"/>
    </svg>
    <div class="kpi-foot">
      <span><i class="fa-solid fa-users"></i> <?= $resumenFinanciero['trabajadores_procesados'] ?> trabajadores</span>
      <i class="fa-solid fa-arrow-right kpi-arrow"></i>
    </div>
  </a>

  <!-- KPI 2: Plantilla -->
  <a href="index.php?r=empleados" class="kpi-card kpi-sky">
    <div class="kpi-glow"></div>
    <div class="kpi-head">
      <div class="kpi-icon" style="background:linear-gradient(135deg,#38bdf8,#0284c7);">
        <i class="fa-solid fa-users"></i>
      </div>
      <span class="kpi-tag kpi-tag-info">
        <?= $totalInactivos ?> inactivos
      </span>
    </div>
    <div class="kpi-label">Plantilla activa</div>
    <div class="kpi-value">
      <?= $totalEmpleados ?><span class="kpi-unit">empleados</span>
    </div>
    <div class="kpi-secondary">
      <span style="color:#0284c7;font-weight:700;">+<?= $empleadosAgregadosEsteMes ?></span> ingresos este mes
    </div>
    <svg class="kpi-spark" viewBox="0 0 100 30" preserveAspectRatio="none">
      <defs>
        <linearGradient id="sparkBlue" x1="0" x2="0" y1="0" y2="1">
          <stop offset="0%" stop-color="#0284c7" stop-opacity="0.35"/>
          <stop offset="100%" stop-color="#0284c7" stop-opacity="0"/>
        </linearGradient>
      </defs>
      <path d="M0,24 L20,22 L40,16 L60,18 L80,10 L100,6 L100,30 L0,30 Z" fill="url(#sparkBlue)"/>
      <path d="M0,24 L20,22 L40,16 L60,18 L80,10 L100,6" fill="none" stroke="#0284c7" stroke-width="2"/>
    </svg>
    <div class="kpi-foot">
      <span><i class="fa-solid fa-user-check"></i> Ver plantilla completa</span>
      <i class="fa-solid fa-arrow-right kpi-arrow"></i>
    </div>
  </a>

  <!-- KPI 3: Acumulado -->
  <div class="kpi-card kpi-violet">
    <div class="kpi-glow"></div>
    <div class="kpi-head">
      <div class="kpi-icon" style="background:linear-gradient(135deg,#a78bfa,#7c3aed);">
        <i class="fa-solid fa-vault"></i>
      </div>
      <span class="kpi-tag kpi-tag-purple">
        <i class="fa-solid fa-infinity"></i> Histórico
      </span>
    </div>
    <div class="kpi-label">Acumulado total</div>
    <div class="kpi-value usd-value">
      <span class="kpi-cur">$</span><?= number_format(max(0.0, $montoTotalHistorico), 2, ',', '.') ?>
    </div>
    <div class="kpi-value bs-value" style="display:none;">
      <span class="kpi-cur">Bs.</span><?= number_format(max(0.0, $montoTotalHistoricoBs), 2, ',', '.') ?>
    </div>
    <div class="kpi-secondary usd-value" style="color:#7c3aed;">
      ≈ Bs. <?= number_format(max(0.0, $montoTotalHistoricoBs), 2, ',', '.') ?>
    </div>
    <div class="kpi-secondary bs-value" style="color:#7c3aed; display:none;">
      ≈ $ <?= number_format(max(0.0, $montoTotalHistorico), 2, ',', '.') ?>
    </div>
    <svg class="kpi-spark" viewBox="0 0 100 30" preserveAspectRatio="none">
      <defs>
        <linearGradient id="sparkViolet" x1="0" x2="0" y1="0" y2="1">
          <stop offset="0%" stop-color="#7c3aed" stop-opacity="0.35"/>
          <stop offset="100%" stop-color="#7c3aed" stop-opacity="0"/>
        </linearGradient>
      </defs>
      <path d="M0,26 L15,24 L30,20 L45,22 L60,14 L75,10 L100,5 L100,30 L0,30 Z" fill="url(#sparkViolet)"/>
      <path d="M0,26 L15,24 L30,20 L45,22 L60,14 L75,10 L100,5" fill="none" stroke="#7c3aed" stroke-width="2"/>
    </svg>
    <div class="kpi-foot">
      <span><i class="fa-solid fa-file-circle-check"></i> <?= $totalNominasCount ?> nóminas cerradas</span>
    </div>
  </div>

  <!-- KPI 4: Permisos con ANILLO SVG -->
  <a href="index.php?r=permisos" class="kpi-card <?= $permisosPendientes > 0 ? 'kpi-amber' : 'kpi-slate' ?>">
    <div class="kpi-glow"></div>
    <div class="kpi-head">
      <div class="kpi-icon" style="background:linear-gradient(135deg,<?= $permisosPendientes > 0 ? '#fb923c,#ea580c' : '#94a3b8,#64748b' ?>);">
        <i class="fa-solid fa-clock-rotate-left"></i>
      </div>
      <?php if ($permisosPendientes > 0): ?>
        <span class="kpi-tag kpi-tag-warning">
          <i class="fa-solid fa-triangle-exclamation"></i> Requieren acción
        </span>
      <?php else: ?>
        <span class="kpi-tag kpi-tag-neutral">
          <i class="fa-solid fa-circle-check"></i> Todo al día
        </span>
      <?php endif; ?>
    </div>
    <div class="kpi-permiso-body">
      <div class="kpi-permiso-num">
        <div class="kpi-label">Permisos pendientes</div>
        <div class="kpi-value" style="color:<?= $permisosPendientes > 0 ? '#ea580c' : '#0f172a' ?>;">
          <?= $permisosPendientes ?>
        </div>
      </div>
      <!-- Anillo SVG de progreso -->
      <svg class="kpi-ring" viewBox="0 0 60 60">
        <circle cx="30" cy="30" r="24" fill="none" stroke="#e2e8f0" stroke-width="6"/>
        <circle cx="30" cy="30" r="24" fill="none" 
                stroke="<?= $permisosPendientes > 0 ? '#ea580c' : '#10b981' ?>" 
                stroke-width="6" stroke-linecap="round"
                stroke-dasharray="150.8"
                stroke-dashoffset="<?= 150.8 - (min($permisosPendientes, 10) / 10 * 150.8) ?>"
                transform="rotate(-90 30 30)"/>
        <text x="30" y="34" text-anchor="middle" font-size="14" font-weight="800" 
              fill="<?= $permisosPendientes > 0 ? '#ea580c' : '#10b981' ?>">
          <?= $permisosPendientes ?>
        </text>
      </svg>
    </div>
    <div class="kpi-foot">
      <span><i class="fa-solid fa-arrow-right"></i> Ver solicitudes</span>
    </div>
  </a>
</section>

<!-- ═══════════════ SALUD OPERATIVA ═══════════════ -->
<section class="ops-section">
  <div class="section-header">
    <div>
      <h2 class="section-title">
        <i class="fa-solid fa-heart-pulse"></i> Salud operativa
      </h2>
      <p class="section-sub">Indicadores en tiempo real del periodo activo</p>
    </div>
  </div>

  <div class="ops-grid">
    <!-- Periodo activo con diseño expandido, rico y balanceado -->
    <?php
      $diasCiclo = 15;
      if (!empty($ultimoPeriodo['fecha_inicio']) && !empty($ultimoPeriodo['fecha_fin'])) {
        $iniTs = strtotime($ultimoPeriodo['fecha_inicio']);
        $finTs = strtotime($ultimoPeriodo['fecha_fin']);
        $diasCiclo = max(1, round(($finTs - $iniTs) / 86400) + 1);
      }
    ?>
    <div class="ops-card ops-card-hero">
      <div class="ops-hero-inner">
        <div class="ops-hero-text">
          <div class="ops-hero-badge-row">
            <span class="ops-hero-label-pill">
              <i class="fa-solid fa-calendar-check"></i> Periodo Actual
            </span>
            <?php if (!empty($ultimoPeriodo)): ?>
              <span class="ops-period-tag">
                Quincena <?= $ultimoPeriodo['numero_quincena'] ?? '1' ?> · <?= $ultimoPeriodo['anio'] ?>
              </span>
              <span class="ops-status-pill <?= $diasRestantes > 0 ? 'pill-active' : 'pill-done' ?>">
                <span class="status-dot"></span> <?= $diasRestantes > 0 ? 'En curso' : 'Concluido' ?>
              </span>
            <?php endif; ?>
          </div>

          <?php if (!empty($ultimoPeriodo)): ?>
            <h3 class="ops-period-title">
              <span><?= htmlspecialchars($ultimoPeriodo['fecha_inicio']) ?></span>
              <i class="fa-solid fa-arrow-right-long ops-period-arrow"></i>
              <span><?= htmlspecialchars($ultimoPeriodo['fecha_fin']) ?></span>
            </h3>
          <?php else: ?>
            <h3 class="ops-period-title">Sin periodo abierto</h3>
          <?php endif; ?>

          <!-- Fila de chips informativos que llenan el espacio con valor operativo -->
          <div class="ops-chips-row">
            <div class="ops-chip">
              <i class="fa-solid fa-hourglass-half"></i>
              <span><?= $diasRestantes > 0 ? "Faltan {$diasRestantes} días para el cierre" : "Cierre concluido" ?></span>
            </div>
            <div class="ops-chip">
              <i class="fa-regular fa-calendar-days"></i>
              <span><?= $diasCiclo ?> días de ciclo</span>
            </div>
            <div class="ops-chip">
              <i class="fa-solid fa-layer-group"></i>
              <span>Tipo: <?= htmlspecialchars($ultimoPeriodo['tipo_periodo'] ?? 'Quincenal') ?></span>
            </div>
          </div>
        </div>

        <!-- Anillo grande de progreso -->
        <div class="ops-hero-ring">
          <svg viewBox="0 0 120 120">
            <circle cx="60" cy="60" r="52" fill="none" stroke="#e2e8f0" stroke-width="8"/>
            <circle cx="60" cy="60" r="52" fill="none" stroke="url(#ringGrad)" stroke-width="8" 
                    stroke-linecap="round"
                    stroke-dasharray="326.7"
                    stroke-dashoffset="<?= 326.7 - ($periodoProgreso / 100 * 326.7) ?>"
                    transform="rotate(-90 60 60)"/>
            <defs>
              <linearGradient id="ringGrad" x1="0" x2="1" y1="0" y2="1">
                <stop offset="0%" stop-color="#14b8a6"/>
                <stop offset="100%" stop-color="#0284c7"/>
              </linearGradient>
            </defs>
            <text class="ops-ring-text-pct" x="60" y="58" text-anchor="middle" font-size="24" font-weight="800">
              <?= $periodoProgreso ?>%
            </text>
            <text class="ops-ring-text-sub" x="60" y="76" text-anchor="middle" font-size="9.5" font-weight="700" letter-spacing="0.5">
              TRANSCURRIDO
            </text>
          </svg>
        </div>
      </div>
    </div>

    <!-- Mini tarjetas con datos rápidos -->
    <div class="ops-mini-grid">
      <div class="ops-mini">
        <div class="ops-mini-icon" style="background:linear-gradient(135deg,#3b82f6,#2563eb);">
          <i class="fa-solid fa-users"></i>
        </div>
        <div class="ops-mini-label">Empleados activos</div>
        <div class="ops-mini-value"><?= $totalEmpleados ?></div>
      </div>
      <div class="ops-mini">
        <div class="ops-mini-icon" style="background:linear-gradient(135deg,#22c55e,#16a34a);">
          <i class="fa-solid fa-file-invoice-dollar"></i>
        </div>
        <div class="ops-mini-label">Nóminas cerradas</div>
        <div class="ops-mini-value"><?= $totalNominasCount ?></div>
      </div>
      <div class="ops-mini">
        <div class="ops-mini-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
          <i class="fa-solid fa-calendar-days"></i>
        </div>
        <div class="ops-mini-label">Tasa BCV</div>
        <div class="ops-mini-value" style="font-size:16px;">
          <?= $valorTasaBcv > 0 ? number_format($valorTasaBcv, 2, ',', '.') : '—' ?>
        </div>
      </div>
      <div class="ops-mini">
        <div class="ops-mini-icon" style="background:linear-gradient(135deg,#ec4899,#db2777);">
          <i class="fa-solid fa-clock"></i>
        </div>
        <div class="ops-mini-label">Hora actual</div>
        <div class="ops-mini-value" id="liveClock" style="font-size:16px;"><?= date('H:i') ?></div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ ANALÍTICA CON TOGGLE DE RANGO ═══════════════ -->
<section class="analytics-section">
  <div class="section-header">
    <div>
      <h2 class="section-title">
        <i class="fa-solid fa-chart-simple"></i> Analítica avanzada
      </h2>
      <p class="section-sub">Visualización de tendencias y distribución de nómina</p>
    </div>
    <div class="range-toggle" data-chart="evolucion">
      <button class="range-btn active" data-range="6">6Q</button>
      <button class="range-btn" data-range="12">12Q</button>
      <button class="range-btn" data-range="all">Todo</button>
    </div>
  </div>

  <div class="analytics-grid">
    <!-- Gráfico evolutivo -->
    <div class="chart-card chart-card-wide">
      <div class="chart-head">
        <div class="chart-head-left">
          <div class="chart-icon" style="background:linear-gradient(135deg,#0f766e,#0284c7);">
            <i class="fa-solid fa-chart-line"></i>
          </div>
          <div>
            <h4 class="chart-title">Evolución financiera</h4>
            <span class="chart-sub">Asignaciones vs Deducciones vs Neto</span>
          </div>
        </div>
        <div class="chart-legend-custom">
          <span class="lg-item"><span class="lg-dot" style="background:#0f766e;"></span>Asignaciones</span>
          <span class="lg-item"><span class="lg-dot" style="background:#e11d48;"></span>Deducciones</span>
          <span class="lg-item"><span class="lg-dot" style="background:#0284c7;"></span>Neto</span>
        </div>
      </div>
      <div class="chart-canvas-wrap">
        <canvas id="chartEvolucion"></canvas>
      </div>
    </div>

    <!-- Gráfico de cargos -->
    <div class="chart-card">
      <div class="chart-head">
        <div class="chart-head-left">
          <div class="chart-icon" style="background:linear-gradient(135deg,#0284c7,#6366f1);">
            <i class="fa-solid fa-chart-pie"></i>
          </div>
          <div>
            <h4 class="chart-title">Inversión por cargo</h4>
            <span class="chart-sub">Presupuesto en USD</span>
          </div>
        </div>
      </div>
      <div class="chart-canvas-wrap">
        <canvas id="chartCargos"></canvas>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ ESTRUCTURA FINANCIERA ═══════════════ -->
<section class="structure-section">
  <div class="section-header">
    <div>
      <h2 class="section-title">
        <i class="fa-solid fa-scale-balanced"></i> Estructura financiera
      </h2>
      <p class="section-sub">Consolidado de la nómina vigente · USD + Bs.</p>
    </div>
    <div class="currency-toggle">
      <button class="cur-btn active" onclick="switchCurrency('usd', this)">USD</button>
      <button class="cur-btn" onclick="switchCurrency('bs', this)">Bs.</button>
    </div>
  </div>

  <div class="structure-grid">
    <div class="struct-box struct-green">
      <div class="struct-box-glow"></div>
      <div class="struct-top">
        <div class="struct-icon" style="background:linear-gradient(135deg,#10b981,#059669);">
          <i class="fa-solid fa-circle-plus"></i>
        </div>
        <span class="struct-badge">+ Ingresos</span>
      </div>
      <div class="struct-label">Asignaciones brutas</div>
      <div class="struct-value usd-value" style="color:#059669;">
        $<?= number_format($resumenFinanciero['asignaciones'], 2, ',', '.') ?>
      </div>
      <div class="struct-value bs-value" style="color:#059669; display:none;">
        Bs. <?= number_format($asignacionesBs, 2, ',', '.') ?>
      </div>
      <div class="struct-meta">Salarios, bonos, horas extras</div>
      <div class="struct-bar">
        <div class="struct-bar-fill" style="background:linear-gradient(90deg,#10b981,#059669);width:100%;"></div>
      </div>
    </div>

    <div class="struct-box struct-rose">
      <div class="struct-box-glow"></div>
      <div class="struct-top">
        <div class="struct-icon" style="background:linear-gradient(135deg,#f43f5e,#e11d48);">
          <i class="fa-solid fa-circle-minus"></i>
        </div>
        <span class="struct-badge">− Egresos</span>
      </div>
      <div class="struct-label">Retenciones y deducciones</div>
      <div class="struct-value usd-value" style="color:#e11d48;">
        $<?= number_format($resumenFinanciero['deducciones'], 2, ',', '.') ?>
      </div>
      <div class="struct-value bs-value" style="color:#e11d48; display:none;">
        Bs. <?= number_format($deduccionesBs, 2, ',', '.') ?>
      </div>
      <div class="struct-meta">S.S.O · R.P.E · L.P.H · Préstamos</div>
      <div class="struct-bar">
        <div class="struct-bar-fill" style="background:linear-gradient(90deg,#f43f5e,#e11d48);width:<?= $resumenFinanciero['asignaciones'] > 0 ? round($resumenFinanciero['deducciones'] / $resumenFinanciero['asignaciones'] * 100) : 0 ?>%;"></div>
      </div>
    </div>

    <div class="struct-box struct-blue struct-highlight">
      <div class="struct-box-glow"></div>
      <div class="struct-top">
        <div class="struct-icon" style="background:linear-gradient(135deg,#0ea5e9,#0284c7);">
          <i class="fa-solid fa-wallet"></i>
        </div>
        <span class="struct-badge struct-badge-primary">= Líquido</span>
      </div>
      <div class="struct-label">Neto transferido</div>
      <div class="struct-value usd-value" style="color:#0284c7;">
        $<?= number_format($netoEfectivo, 2, ',', '.') ?>
      </div>
      <div class="struct-value bs-value" style="color:#0284c7; display:none;">
        Bs. <?= number_format($netoEfectivoBs, 2, ',', '.') ?>
      </div>
      <div class="struct-meta" style="color:#0284c7;font-weight:600;">
        <?= $excesoDeducciones > 0 ? "Deducciones superan asignaciones en este ciclo" : "Monto líquido a empleados" ?>
      </div>
      <div class="struct-bar">
        <div class="struct-bar-fill" style="background:linear-gradient(90deg,#0ea5e9,#0284c7);width:<?= $resumenFinanciero['asignaciones'] > 0 ? round($netoEfectivo / $resumenFinanciero['asignaciones'] * 100) : 0 ?>%;"></div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ TIMELINE DE PERIODOS ═══════════════ -->
<section class="timeline-section">
  <div class="section-header">
    <div>
      <h2 class="section-title">
        <i class="fa-solid fa-clock-rotate-left"></i> Historial de periodos
      </h2>
      <p class="section-sub">Últimos ciclos registrados y su estado</p>
    </div>
    <a href="index.php?r=periodos" class="btn-ghost">
      Ver todos <i class="fa-solid fa-arrow-right"></i>
    </a>
  </div>

  <div class="timeline">
    <?php foreach ($periodosRecientes as $idx => $p): 
        $nomina = $nominaModel->getByPeriodo((int)$p['id_periodo']);
        $generada = !empty($nomina);
        $detalles = $generada ? $nominaModel->getDetallesByNomina((int)$nomina['id_nomina']) : [];
        $totalUsd = 0.0;
        foreach ($detalles as $det) $totalUsd += (float)$det['neto_pagar'];
        $totalBs = $valorTasaBcv > 0 ? ($totalUsd * $valorTasaBcv) : 0.0;
        
        $meses = [1=>'ENE',2=>'FEB',3=>'MAR',4=>'ABR',5=>'MAY',6=>'JUN',7=>'JUL',8=>'AGO',9=>'SEP',10=>'OCT',11=>'NOV',12=>'DIC'];
        $mes = $meses[(int)$p['mes']] ?? 'MES';
        $quincenaTexto = !empty($p['numero_quincena']) ? "Quincena {$p['numero_quincena']}" : "Periodo {$p['id_periodo']}";
    ?>
      <div class="tl-item <?= $generada ? 'tl-done' : 'tl-pending' ?>">
        <!-- Conector -->
        <div class="tl-line">
          <div class="tl-node">
            <i class="fa-solid <?= $generada ? 'fa-check' : 'fa-clock' ?>"></i>
          </div>
        </div>

        <!-- Card del periodo -->
        <div class="tl-card">
          <div class="tl-card-left">
            <div class="tl-date">
              <span class="tl-month"><?= $mes ?></span>
              <span class="tl-day"><?= date('d', strtotime($p['fecha_inicio'])) ?></span>
            </div>
            <div class="tl-info">
              <div class="tl-title">
                <?= htmlspecialchars($p['tipo_periodo']) ?>
                <span class="tl-status <?= $generada ? 'tl-status-done' : 'tl-status-pending' ?>">
                  <?= $generada ? 'Generada' : 'Pendiente' ?>
                </span>
              </div>
              <div class="tl-dates">
                <?= htmlspecialchars($p['fecha_inicio']) ?> → <?= htmlspecialchars($p['fecha_fin']) ?>
                · <?= htmlspecialchars($quincenaTexto) ?> · <?= htmlspecialchars($p['anio']) ?>
                <?php if ($generada): ?>
                  · <i class="fa-solid fa-users"></i> <?= count($detalles) ?> emp.
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="tl-card-right">
            <?php if ($generada): ?>
              <div class="tl-amount">
                <div class="tl-usd usd-value">$<?= number_format(max(0.0, $totalUsd), 2, ',', '.') ?></div>
                <div class="tl-usd bs-value" style="display:none;">Bs. <?= number_format(max(0.0, $totalBs), 2, ',', '.') ?></div>
                <div class="tl-bs usd-value">≈ Bs. <?= number_format(max(0.0, $totalBs), 2, ',', '.') ?></div>
                <div class="tl-bs bs-value" style="display:none;">≈ $ <?= number_format(max(0.0, $totalUsd), 2, ',', '.') ?></div>
              </div>
              <a href="index.php?r=nomina/detalle&id_nomina=<?= $nomina['id_nomina'] ?>" class="tl-btn tl-btn-primary" title="Ver / Editar">
                <i class="fa-solid fa-pen-to-square"></i>
              </a>
              <a href="index.php?r=nomina/ver&id_nomina=<?= $nomina['id_nomina'] ?>" class="tl-btn tl-btn-ghost" title="Resumen">
                <i class="fa-solid fa-eye"></i>
              </a>
            <?php else: ?>
              <a href="index.php?r=nomina/generar&id_periodo=<?= $p['id_periodo'] ?>" class="tl-btn tl-btn-generate">
                <i class="fa-solid fa-bolt"></i> Generar
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

    <?php if (empty($periodosRecientes)): ?>
      <div class="empty-state-modern">
        <div class="empty-icon-wrap">
          <i class="fa-solid fa-calendar-xmark"></i>
        </div>
        <h3>Aún no hay periodos registrados</h3>
        <p>Crea tu primer ciclo quincenal o mensual para empezar a procesar nóminas.</p>
        <a href="index.php?r=periodos/crear" class="btn-hero-primary">
          <i class="fa-solid fa-plus"></i> Crear primer periodo
        </a>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- ═══════════════ DATOS PARA CHART.JS ═══════════════ -->
<script src="assets/js/vendor/chart.umd.min.js"></script>
<script>
  window.dashboardChartData = {
    labels: <?= $chartLabelsJson ?>,
    asignaciones: <?= $chartAsignacionesJson ?>,
    deducciones: <?= $chartDeduccionesJson ?>,
    neto: <?= $chartNetoJson ?>,
    empleados: <?= $chartEmpleadosJson ?>
  };
  window.dashboardCargosData = {
    labels: <?= $cargoLabelsJson ?>,
    presupuesto: <?= $cargoPresupuestoJson ?>,
    headcount: <?= $cargoHeadcountJson ?>
  };
</script>
<script src="assets/js/dashboard.js?v=2.3"></script>