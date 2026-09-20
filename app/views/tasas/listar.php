<?php if (!empty($msg)): ?>
  <div class="alert success" style="margin-bottom:20px;">
    <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($msg) ?>
  </div>
<?php endif; ?>

<div class="view-header-bar" style="margin-bottom:24px;">
  <div>
    <h2 style="margin:0;"><i class="fa-solid fa-money-bill-transfer" style="color:var(--primary); margin-right:8px;"></i> Historial y Control de Tasa de Cambio (BCV)</h2>
    <p style="margin:4px 0 0; color:var(--text-muted); font-size:13px;">Conversión oficial de nómina en Bolívares (Bs.) con respaldo 100% offline</p>
  </div>
  <div class="actions-row" style="margin-bottom:0;">
    <a class="btn" href="index.php?r=tasas/sincronizar" id="btnSyncBcv" onclick="animateSync(this)">
      <i class="fa-solid fa-arrows-rotate" id="syncIcon"></i> Sincronizar BCV Ahora
    </a>
    <a class="btn secondary" href="index.php?r=tasas/crear">
      <i class="fa-solid fa-pen-to-square"></i> Registrar Manual
    </a>
  </div>
</div>

<!-- Card de Estado de Tasa Vigente & Sincronización Automática -->
<div class="card bcv-hero-status-card" style="margin-bottom:24px;">
  <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
    <div style="display:flex; align-items:center; gap:16px;">
      <div class="bcv-hero-icon-box">
        <i class="fa-solid fa-building-columns"></i>
      </div>
      <div>
        <div style="font-size:11.5px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:0.5px;">
          Tasa Oficial BCV Vigente
        </div>
        <div class="bcv-hero-rate-val">
          <?= !empty($ultimaTasa) ? number_format((float)$ultimaTasa['valor_tasa'], 4, ',', '.') . ' Bs. <span class="bcv-hero-rate-unit">/ 1 USD</span>' : 'Sin tasa registrada' ?>
        </div>
        <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">
          <i class="fa-regular fa-calendar-check" style="color:var(--primary); margin-right:4px;"></i>
          Vigencia: <strong style="color:var(--text);"><?= !empty($ultimaTasa) ? date('d/m/Y', strtotime($ultimaTasa['fecha'])) : 'N/A' ?></strong>
          &bull; Fuente: <strong style="color:var(--text);"><?= htmlspecialchars($ultimaTasa['fuente'] ?? 'N/A') ?></strong>
        </div>
      </div>
    </div>

    <!-- Indicador de Conectividad & Sincronización Automática -->
    <div style="display:flex; flex-direction:column; align-items:flex-end; gap:8px;">
      <div id="connectionStatusBadge" class="bcv-connection-badge">
        <span style="width:8px; height:8px; border-radius:50%; background:#10b981; display:inline-block;" id="statusDot"></span>
        <span id="statusText">Verificando conexión...</span>
      </div>
      <div style="font-size:11px; color:var(--text-muted); text-align:right;">
        Sistema con detección de red y respaldo automático en BD
      </div>
    </div>
  </div>
</div>

<div class="card table-responsive" id="cardTablaTasas" style="padding:0; overflow-x:auto; -webkit-overflow-scrolling:touch; max-width:1000px;">
  <div style="padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between;">
    <h3 style="margin:0; font-size:15px; font-weight:700; color:var(--text);">
      <i class="fa-solid fa-clock-rotate-left" style="color:var(--primary); margin-right:6px;"></i> Registros Históricos de Tasas
    </h3>
    <span style="font-size:12px; font-weight:600; color:var(--text-muted);">
      Total: <?= count($tasas) ?> tasas archivadas
    </span>
  </div>
  <table id="tablaTasas">
    <thead>
      <tr>
        <th>Fecha de Vigencia</th>
        <th>Tasa de Cambio (Bs / USD)</th>
        <th>Fuente / Método de Captura</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$tasas): ?>
        <tr class="no-data-row"><td colspan="3" style="text-align:center; color:var(--text-muted); padding:36px;">Aún no hay tasas registradas. Puede sincronizar con el BCV con un clic o ingresar una manualmente.</td></tr>
      <?php endif; ?>
      <?php foreach ($tasas as $t): 
        $tJsonData = [
          'id_tasa' => $t['id_tasa'] ?? null,
          'fecha' => $t['fecha'],
          'valor_tasa' => $t['valor_tasa'],
          'fuente' => $t['fuente']
        ];
      ?>
        <tr class="tasa-row" onclick="abrirVerTasaModal(this)" data-tasa="<?= htmlspecialchars(json_encode($tJsonData), ENT_QUOTES, 'UTF-8') ?>" title="Clic para ver detalles completos de esta tasa">
          <td><strong style="color:var(--text); font-size:13.5px;"><i class="fa-solid fa-calendar-day" style="margin-right:6px; color:var(--primary);"></i><?= htmlspecialchars($t['fecha']) ?></strong></td>
          <td><strong style="color:var(--text); font-size:15px;">Bs. <?= number_format((float)$t['valor_tasa'], 4, ',', '.') ?></strong></td>
          <td>
            <?php if (strtolower(trim($t['fuente'])) === 'manual'): ?>
              <span class="badge" style="background:rgba(148, 163, 184, 0.15); color:var(--text-muted); border:1px solid rgba(148, 163, 184, 0.25);"><i class="fa-solid fa-user"></i> Ingreso Manual</span>
            <?php else: ?>
              <span class="badge" style="background:rgba(2, 132, 199, 0.12); color:#0284c7; border:1px solid rgba(2, 132, 199, 0.25);"><i class="fa-solid fa-cloud-arrow-down"></i> <?= htmlspecialchars($t['fuente']) ?></span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/VerModal.php'; ?>


<script nonce="<?= Security::getCspNonce() ?>">
function animateSync(btn) {
  const icon = document.getElementById('syncIcon');
  if (icon) {
    icon.classList.add('fa-spin');
  }
}

function updateConnectionUI() {
  const badge = document.getElementById('connectionStatusBadge');
  const dot = document.getElementById('statusDot');
  const text = document.getElementById('statusText');
  if (!badge || !dot || !text) return;

  if (navigator.onLine) {
    badge.style.background = '#ecfdf5';
    badge.style.color = '#059669';
    badge.style.borderColor = '#a7f3d0';
    dot.style.background = '#10b981';
    text.textContent = 'En Línea: Sincronización BCV disponible';
  } else {
    badge.style.background = '#fffbeb';
    badge.style.color = '#d97706';
    badge.style.borderColor = '#fde68a';
    dot.style.background = '#f59e0b';
    text.textContent = 'Sin WiFi: Operando con caché local en BD';
  }
}

window.addEventListener('online', updateConnectionUI);
window.addEventListener('offline', updateConnectionUI);
updateConnectionUI();

document.addEventListener('DOMContentLoaded', function() {
  if (typeof initTablePagination === 'function') {
    window.tasasPaginator = initTablePagination({
      table: '#tablaTasas',
      rowSelector: 'tbody tr.tasa-row',
      storageKey: 'nomina_paginator_tasas',
      defaultPageSize: 10,
      appendTo: '#cardTablaTasas'
    });
  }
});
</script>
