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

<div class="card" style="padding:0; overflow:hidden;">
  <table>
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
        <tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding:30px;">Aún no hay periodos creados.</td></tr>
      <?php endif; ?>
      <?php foreach ($periodos as $p): ?>
        <tr>
          <td><strong style="color:var(--text); font-size:13.5px;"><?= htmlspecialchars($p['descripcion'] ?? 'Sin descripción') ?></strong></td>
          <td><span class="badge" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;"><?= htmlspecialchars($p['tipo_periodo']) ?></span></td>
          <td><span style="font-size:13px; color:#475569;"><?= htmlspecialchars($p['fecha_inicio']) ?> &rarr; <?= htmlspecialchars($p['fecha_fin']) ?></span></td>
          <td><?= htmlspecialchars($p['anio']) ?> / <?= htmlspecialchars($p['mes']) ?></td>
          <td><?= htmlspecialchars($p['numero_quincena'] ?? '—') ?></td>
          <td style="text-align:right;">
            <a class="btn small" href="index.php?r=nomina/generar&id_periodo=<?= $p['id_periodo'] ?>"><i class="fa-solid fa-folder-open"></i> Generar / Ver Nómina</a>
            <a class="btn small secondary" style="margin-left:4px;" href="index.php?r=periodos/enviarCorreos&id_periodo=<?= $p['id_periodo'] ?>" onclick="return confirm('¿Desea enviar el comprobante de pago individual por correo a todos los empleados de este periodo?');"><i class="fa-solid fa-paper-plane"></i> Enviar Correo</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
