<?php if (!empty($showWelcome)): ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const mainContainer = document.querySelector('main.container');
    if (mainContainer) {
      mainContainer.classList.add('door-dashboard-arrival');
    }
  });
</script>
<?php endif; ?>

<div class="view-header-bar">
  <div>
    <h1 style="margin-bottom:4px;">Tablero Principal</h1>
    <p style="margin:0; color:var(--text-muted); font-size:14px;">Resumen ejecutivo y estado de nóminas</p>
  </div>
  <div class="actions-row" style="margin-bottom:0;">
    <a class="btn" href="index.php?r=empleados/crear"><i class="fa-solid fa-user-plus"></i> Nuevo empleado</a>
    <a class="btn secondary" href="index.php?r=periodos/crear"><i class="fa-solid fa-calendar-plus"></i> Crear Periodo</a>
  </div>
</div>

<div class="dashboard-grid">
  <!-- Card 1: Empleados Activos -->
  <div class="stat-card">
    <div class="stat-card-header">
      <div class="stat-label">Empleados Activos</div>
      <div class="stat-icon-wrapper" style="background:#e0f2fe; color:#0284c7;">
        <i class="fa-solid fa-users"></i>
      </div>
    </div>
    <div class="stat-value"><?= $totalEmpleados ?></div>
    <div class="stat-desc">
      <span class="stat-badge-trend"><i class="fa-solid fa-user-slash"></i> <?= $totalInactivos ?> inactivos</span>
    </div>
  </div>

  <!-- Card 2: Nuevos Ingresos -->
  <div class="stat-card">
    <div class="stat-card-header">
      <div class="stat-label">Ingresos del Mes</div>
      <div class="stat-icon-wrapper" style="background:#dcfce7; color:#16a34a;">
        <i class="fa-solid fa-user-check"></i>
      </div>
    </div>
    <div class="stat-value"><?= $empleadosAgregadosEsteMes ?></div>
    <div class="stat-desc">
      <i class="fa-solid fa-calendar-check" style="color:#10b981;"></i> Registrados en el mes actual
    </div>
  </div>

  <!-- Card 3: Nóminas Generadas -->
  <div class="stat-card">
    <div class="stat-card-header">
      <div class="stat-label">Nóminas Procesadas</div>
      <div class="stat-icon-wrapper" style="background:#fef3c7; color:#d97706;">
        <i class="fa-solid fa-file-invoice-dollar"></i>
      </div>
    </div>
    <div class="stat-value"><?= $totalNominasCount ?></div>
    <div class="stat-desc">
      <i class="fa-solid fa-circle-check" style="color:#d97706;"></i> Periodos cerrados
    </div>
  </div>

  <!-- Card 4: Costo Última Nómina -->
  <div class="stat-card">
    <div class="stat-card-header">
      <div class="stat-label">Última Nómina</div>
      <div class="stat-icon-wrapper" style="background:#ccfbf1; color:#0d9488;">
        <i class="fa-solid fa-money-bill-wave"></i>
      </div>
    </div>
    <div class="stat-value">$<?= number_format($montoUltimaNomina, 2, ',', '.') ?></div>
    <div class="stat-desc">
      <i class="fa-solid fa-dollar-sign" style="color:#0d9488;"></i> Monto neto total USD
    </div>
  </div>

  <!-- Card 5: Costo Histórico -->
  <div class="stat-card">
    <div class="stat-card-header">
      <div class="stat-label">Total Acumulado</div>
      <div class="stat-icon-wrapper" style="background:#f3e8ff; color:#9333ea;">
        <i class="fa-solid fa-chart-line"></i>
      </div>
    </div>
    <div class="stat-value">$<?= number_format($montoTotalHistorico, 2, ',', '.') ?></div>
    <div class="stat-desc">
      <i class="fa-solid fa-vault" style="color:#9333ea;"></i> Pagos históricos
    </div>
  </div>
</div>

<div class="card" style="margin-bottom: 28px; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
  <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
    <div style="width:36px; height:36px; border-radius:10px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; font-size:16px;">
      <i class="fa-solid fa-circle-info"></i>
    </div>
    <h2 style="margin:0;">Resumen Operativo</h2>
  </div>
  <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:16px;">
    <div style="background:#ffffff; padding:14px 18px; border-radius:10px; border:1px solid #e2e8f0; display:flex; align-items:center; gap:14px;">
      <i class="fa-solid fa-clipboard-list" style="font-size:22px; color:var(--primary);"></i>
      <div>
        <div style="font-size:12px; color:var(--text-muted); font-weight:600;">SOLICITUDES DE PERMISO</div>
        <strong style="font-size:15px; color:var(--text);"><?= $permisosPendientes ?> pendientes por aprobar</strong>
      </div>
    </div>
    <?php if (!empty($ultimoPeriodo)): ?>
      <div style="background:#ffffff; padding:14px 18px; border-radius:10px; border:1px solid #e2e8f0; display:flex; align-items:center; gap:14px;">
        <i class="fa-solid fa-calendar-day" style="font-size:22px; color:#0284c7;"></i>
        <div>
          <div style="font-size:12px; color:var(--text-muted); font-weight:600;">ÚLTIMO PERIODO REGISTRADO</div>
          <strong style="font-size:14px; color:var(--text);"><?= htmlspecialchars($ultimoPeriodo['fecha_inicio']) ?> al <?= htmlspecialchars($ultimoPeriodo['fecha_fin']) ?></strong>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
    <h2 style="margin:0;"><i class="fa-solid fa-clock-rotate-left" style="margin-right:8px; color:var(--primary);"></i>Nóminas y Periodos Recientes</h2>
    <a href="index.php?r=periodos" class="btn secondary small"><i class="fa-solid fa-arrow-right"></i> Ver todos los periodos</a>
  </div>
  <div style="overflow-x: auto;">
    <table>
      <thead>
        <tr>
          <th>Tipo Periodo</th>
          <th>Rango de Fechas</th>
          <th>Detalle de Periodo</th>
          <th>Estado</th>
          <th style="text-align:right;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($periodosRecientes as $p): 
            $nomina = $nominaModel->getByPeriodo((int)$p['id_periodo']);
            $estadoStr = $nomina ? 'Generada' : 'Pendiente';
            $badgeClass = $nomina ? 'activo' : 'inactivo';
        ?>
          <tr>
            <td><strong><i class="fa-solid fa-calendar-check" style="margin-right:6px; color:var(--primary);"></i><?= htmlspecialchars($p['tipo_periodo']) ?></strong></td>
            <td><?= htmlspecialchars($p['fecha_inicio']) ?> &rarr; <?= htmlspecialchars($p['fecha_fin']) ?></td>
            <td>Año: <?= $p['anio'] ?> | Mes: <?= $p['mes'] ?> (Quincena <?= $p['numero_quincena'] ?>)</td>
            <td>
              <span class="badge <?= $badgeClass ?>"><?= $estadoStr ?></span>
            </td>
            <td style="text-align:right;">
              <?php if ($nomina): ?>
                <a class="btn small" href="index.php?r=nomina/detalle&id_nomina=<?= $nomina['id_nomina'] ?>"><i class="fa-solid fa-pen-to-square"></i> Ver / Editar</a>
              <?php else: ?>
                <a class="btn small secondary" href="index.php?r=nomina/generar&id_periodo=<?= $p['id_periodo'] ?>"><i class="fa-solid fa-bolt"></i> Generar Nómina</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($periodosRecientes)): ?>
          <tr>
            <td colspan="5" style="text-align: center; color: var(--text-muted); padding:24px;">No hay periodos de nómina registrados.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="actions-row" style="margin-top: 25px;">
  <a class="btn" href="index.php?r=empleados/crear"><i class="fa-solid fa-user-plus"></i> + Nuevo empleado</a>
  <a class="btn secondary" href="index.php?r=permisos"><i class="fa-solid fa-file-signature"></i> Ver permisos</a>
  <a class="btn secondary" href="index.php?r=periodos"><i class="fa-solid fa-calendar-days"></i> Ver todos los Periodos / Nóminas</a>
</div>
