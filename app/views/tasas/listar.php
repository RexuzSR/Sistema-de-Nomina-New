<?php if (!empty($msg)): ?>
  <div class="alert success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="view-header-bar" style="margin-bottom:20px;">
  <div>
    <h2 style="margin:0;"><i class="fa-solid fa-money-bill-transfer" style="color:var(--primary); margin-right:8px;"></i> Historial de Tasa de Cambio (BCV / Oficial)</h2>
    <p style="margin:4px 0 0; color:var(--text-muted); font-size:13px;">Tasas registradas para la conversión de nómina en Bolívares (Bs)</p>
  </div>
  <div class="actions-row" style="margin-bottom:0;">
    <a class="btn" href="index.php?r=tasas/crear"><i class="fa-solid fa-plus"></i> Registrar Tasa del Día</a>
  </div>
</div>

<div class="card" style="padding:0; overflow:hidden; max-width:900px;">
  <table>
    <thead>
      <tr>
        <th>Fecha de Vigencia</th>
        <th>Tasa de Cambio (Bs / USD)</th>
        <th>Fuente</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$tasas): ?>
        <tr><td colspan="3" style="text-align:center; color:var(--text-muted); padding:30px;">Aún no hay tasas registradas. Registra al menos una antes de generar nómina.</td></tr>
      <?php endif; ?>
      <?php foreach ($tasas as $t): ?>
        <tr>
          <td><strong style="color:var(--text); font-size:13.5px;"><i class="fa-solid fa-calendar-day" style="margin-right:6px; color:var(--primary);"></i><?= htmlspecialchars($t['fecha']) ?></strong></td>
          <td><strong style="color:#0f172a; font-size:15px;">Bs. <?= number_format((float)$t['valor_tasa'], 2, ',', '.') ?></strong></td>
          <td>
            <?php if (strtolower(trim($t['fuente'])) === 'manual'): ?>
              <span class="badge" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1;"><i class="fa-solid fa-user"></i> Introducida por el usuario</span>
            <?php else: ?>
              <span class="badge" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;"><i class="fa-solid fa-building-columns"></i> <?= htmlspecialchars($t['fuente']) ?></span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
