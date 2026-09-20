<?php if (!empty($msg)): ?>
  <div class="alert success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="view-header-bar" style="margin-bottom:20px;">
  <div>
    <h2 style="margin:0;"><i class="fa-solid fa-calendar-days" style="color:var(--primary); margin-right:8px;"></i> Gestión de Periodos de Nómina</h2>
    <p style="margin:4px 0 0; color:var(--text-muted); font-size:13px;">Ciclos de pago y distribución de recibos</p>
  </div>
  <div class="actions-row" style="margin-bottom:0;">
    <a class="btn" href="index.php?r=periodos/crear"><i class="fa-solid fa-plus"></i> Nuevo Periodo</a>
  </div>
</div>

<div class="card table-responsive" id="cardTablaPeriodos" style="padding:0; overflow-x:auto; -webkit-overflow-scrolling:touch;">
  <table id="tablaPeriodos">
    <thead>
      <tr>
        <th>Nombre del Ciclo / Descripción</th>
        <th>Tipo</th>
        <th>Fechas</th>
        <th>Año / Mes</th>
        <th>Quincena #</th>
        <th style="text-align:right;">Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$periodos): ?>
        <tr class="no-data-row"><td colspan="6" style="text-align:center; color:var(--text-muted); padding:30px;">Aún no hay periodos creados.</td></tr>
      <?php endif; ?>
      <?php foreach ($periodos as $p): 
        $pJsonData = [
          'id_periodo' => $p['id_periodo'],
          'descripcion' => $p['descripcion'] ?? 'Sin descripción',
          'tipo_periodo' => $p['tipo_periodo'],
          'fecha_inicio' => $p['fecha_inicio'],
          'fecha_fin' => $p['fecha_fin'],
          'anio' => $p['anio'],
          'mes' => $p['mes'],
          'numero_quincena' => $p['numero_quincena'] ?? '—'
        ];
      ?>
        <tr class="periodo-row" onclick="abrirVerPeriodoModal(this)" data-periodo="<?= htmlspecialchars(json_encode($pJsonData), ENT_QUOTES, 'UTF-8') ?>" title="Clic para ver detalles completos del periodo">
          <td><strong style="color:var(--text); font-size:13.5px;"><?= htmlspecialchars($p['descripcion'] ?? 'Sin descripción') ?></strong></td>
          <td><span class="badge" style="background:rgba(2, 132, 199, 0.12); color:#0284c7; border:1px solid rgba(2, 132, 199, 0.25);"><?= htmlspecialchars($p['tipo_periodo']) ?></span></td>
          <td><span style="font-size:13px; color:var(--text-muted);"><?= htmlspecialchars($p['fecha_inicio']) ?> &rarr; <?= htmlspecialchars($p['fecha_fin']) ?></span></td>
          <td><span style="color:var(--text); font-weight:600;"><?= htmlspecialchars($p['anio']) ?> / <?= htmlspecialchars($p['mes']) ?></span></td>
          <td><span style="color:var(--text); font-weight:600;"><?= htmlspecialchars($p['numero_quincena'] ?? '—') ?></span></td>
          <td style="text-align:right;" onclick="event.stopPropagation()">
            <a class="btn small" href="index.php?r=nomina/generar&id_periodo=<?= $p['id_periodo'] ?>"><i class="fa-solid fa-folder-open"></i> Generar / Ver Nómina</a>
            <a class="btn small secondary" style="margin-left:4px;" href="index.php?r=periodos/enviarCorreos&id_periodo=<?= $p['id_periodo'] ?>" onclick="return confirm('¿Desea enviar el comprobante de pago individual por correo a todos los empleados de este periodo?');"><i class="fa-solid fa-paper-plane"></i> Enviar Correo</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/VerModal.php'; ?>

<script nonce="<?= Security::getCspNonce() ?>">
document.addEventListener('DOMContentLoaded', function() {
  if (typeof initTablePagination === 'function') {
    window.periodosPaginator = initTablePagination({
      table: '#tablaPeriodos',
      rowSelector: 'tbody tr.periodo-row',
      storageKey: 'nomina_paginator_periodos',
      defaultPageSize: 10,
      appendTo: '#cardTablaPeriodos'
    });
  }
});
</script>

