<?php
$empConfig = $empConfig ?? [];
$nombreEmpresa = $empConfig['nombre_empresa'] ?? 'MULTITEL C.A.';
$rifEmpresa = $empConfig['rif'] ?? 'J-40982716-0';
$regimenIvss = $empConfig['regimen_ivss'] ?? 'Clase_II';
$descPeriodo = !empty($periodo['descripcion']) ? $periodo['descripcion'] : ($periodo['tipo_periodo'] . " (" . $periodo['fecha_inicio'] . " al " . $periodo['fecha_fin'] . ")");
$tasaBcvMonto = !empty($ultimaTasa['monto_bcv']) ? (float)$ultimaTasa['monto_bcv'] : 0;
$totalRecibos = count($recibos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tickets de Nómina (Formato Comercial POS) - <?= htmlspecialchars($nombreEmpresa) ?></title>
  <link rel="icon" type="image/png" href="assets/images/logo_multitel.png?v=1">
  <link rel="shortcut icon" type="image/png" href="assets/images/logo_multitel.png?v=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      background: #e2e8f0;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      color: #0f172a;
      padding: 16px 12px 40px;
    }

    /* BARRA DE ACCIONES EN PANTALLA */
    .screen-actions-bar {
      max-width: 1040px;
      margin: 0 auto 16px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #ffffff;
      padding: 12px 18px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.06);
      border: 1px solid #cbd5e1;
      flex-wrap: wrap;
      gap: 12px;
    }
    .actions-bar-left {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }
    .badge-workers-count {
      background: rgba(19, 76, 181, 0.1);
      color: #134cb5;
      font-size: 11.5px;
      font-weight: 800;
      padding: 5px 10px;
      border-radius: 6px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .mode-switch-group {
      display: inline-flex;
      background: #f1f5f9;
      padding: 3px;
      border-radius: 8px;
      border: 1px solid #cbd5e1;
      gap: 2px;
    }
    .btn-mode-toggle {
      border: none;
      background: transparent;
      padding: 6px 12px;
      font-size: 11.5px;
      font-weight: 700;
      color: #64748b;
      border-radius: 6px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      transition: all 0.15s ease;
    }
    .btn-mode-toggle.active {
      background: #ffffff;
      color: #134cb5;
      box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    }
    .actions-bar-right {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }
    .btn-print-cta {
      background: #134cb5;
      color: #ffffff;
      font-size: 13px;
      font-weight: 700;
      padding: 9px 18px;
      border-radius: 7px;
      border: none;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      transition: all 0.15s ease;
      box-shadow: 0 2px 6px rgba(19,76,181,0.3);
    }
    .btn-print-cta:hover {
      background: #0d3a8f;
      transform: translateY(-1px);
    }
    .btn-pdf-link {
      color: #ffffff;
      background: #0f172a;
      text-decoration: none;
      font-size: 12.5px;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 14px;
      border-radius: 7px;
      border: 1px solid #0f172a;
      transition: all 0.15s ease;
    }
    .btn-pdf-link:hover {
      background: #1e293b;
    }
    .btn-back-link {
      color: #475569;
      text-decoration: none;
      font-size: 12px;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 8px 12px;
      border-radius: 6px;
      border: 1px solid #cbd5e1;
      background: #f8fafc;
      transition: all 0.15s ease;
    }
    .btn-back-link:hover {
      background: #e2e8f0;
      color: #0f172a;
    }

    /* CONTENEDOR DE TICKETS */
    .tickets-container {
      max-width: 1040px;
      margin: 0 auto;
    }

    /* MODO PARRILLA (CARTA 6 POR HOJA: 3 COLUMNAS X 2 FILAS) */
    .view-grid .tickets-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 16px;
      justify-content: center;
    }

    /* MODO TIRA TÉRMICA CONTINUA (POS 80mm) */
    .view-pos .tickets-grid {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 20px;
    }
    .view-pos .ticket-receipt {
      width: 320px !important;
    }

    /* ═══════════════ DISEÑO DEL TICKET DE CENTRO COMERCIAL ═══════════════ */
    .ticket-receipt {
      background: #ffffff;
      border: 1px solid #cbd5e1;
      border-radius: 4px;
      padding: 14px 14px 16px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.06);
      position: relative;
      font-size: 11px;
      color: #1e293b;
      page-break-inside: avoid;
      break-inside: avoid;
    }

    /* Borde dentado de corte de ticket térmico */
    .ticket-receipt::before {
      content: "";
      position: absolute;
      top: -4px;
      left: 0;
      right: 0;
      height: 4px;
      background: radial-gradient(circle, transparent, transparent 50%, #ffffff 50%, #ffffff 100%);
      background-size: 8px 4px;
    }

    /* CABECERA DEL TICKET */
    .ticket-header {
      text-align: center;
      padding-bottom: 8px;
    }
    .ticket-logo {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      object-fit: contain;
      margin: 0 auto 4px;
      display: block;
      border: 1px solid rgba(19,76,181,0.25);
    }
    .ticket-brand {
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 13.5px;
      font-weight: 900;
      color: #0f172a;
      letter-spacing: 0.3px;
      line-height: 1.1;
    }
    .ticket-tagline {
      font-size: 9px;
      font-weight: 800;
      color: #134cb5;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      margin-top: 1px;
    }
    .ticket-company-meta {
      font-size: 9px;
      color: #64748b;
      margin-top: 2px;
      line-height: 1.25;
    }
    .ticket-doc-badge {
      display: inline-block;
      margin-top: 5px;
      background: #0f172a;
      color: #ffffff;
      font-size: 9.5px;
      font-weight: 800;
      padding: 2px 8px;
      border-radius: 3px;
      letter-spacing: 0.5px;
    }
    .ticket-period-sub {
      font-size: 9px;
      color: #475569;
      margin-top: 3px;
      font-weight: 600;
    }

    /* LÍNEAS DIVISORIAS PUNTEADAS ESTILO POS */
    .ticket-dashed-line {
      border-top: 1px dashed #94a3b8;
      margin: 6px 0;
      position: relative;
    }
    .ticket-double-line {
      border-top: 2px solid #0f172a;
      margin: 7px 0;
    }

    /* DATOS DEL EMPLEADO */
    .ticket-worker-box {
      font-size: 10px;
      line-height: 1.4;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 4px;
      padding: 5px 8px;
    }
    .ticket-row {
      display: flex;
      justify-content: space-between;
      gap: 6px;
    }
    .ticket-label {
      color: #64748b;
      font-weight: 700;
      font-size: 8.5px;
      text-transform: uppercase;
    }
    .ticket-val {
      font-weight: 700;
      color: #0f172a;
      text-align: right;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    /* TABLA DE CONCEPTOS TIPO CASH RECEIPT */
    .ticket-section-title {
      font-size: 9.5px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.4px;
      margin: 5px 0 3px;
      display: flex;
      justify-content: space-between;
    }
    .title-asig {
      color: #166534;
    }
    .title-ded {
      color: #991b1b;
    }

    .ticket-items-list {
      list-style: none;
      padding: 0;
      margin: 0;
      font-size: 10px;
    }
    .ticket-item {
      display: flex;
      justify-content: space-between;
      align-items: baseline;
      padding: 1.5px 0;
    }
    .ticket-item-name {
      color: #334155;
      max-width: 68%;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    .ticket-dots {
      flex: 1;
      border-bottom: 1px dotted #cbd5e1;
      margin: 0 4px;
    }
    .ticket-item-amount {
      font-family: 'Space Mono', monospace;
      font-weight: 700;
      font-size: 10px;
      color: #0f172a;
      white-space: nowrap;
    }

    .ticket-subtotal-row {
      display: flex;
      justify-content: space-between;
      font-weight: 800;
      font-size: 9.5px;
      margin-top: 3px;
      padding-top: 2px;
      border-top: 1px dotted #94a3b8;
    }

    /* CAJA DESTACADA DE TOTAL NETO A COBRAR */
    .ticket-neto-card {
      background: #dcfce7;
      border: 1.5px solid #22c55e;
      border-radius: 4px;
      padding: 6px 8px;
      text-align: center;
      margin: 6px 0;
    }
    .ticket-neto-lbl {
      font-size: 8.5px;
      font-weight: 800;
      text-transform: uppercase;
      color: #166534;
      letter-spacing: 0.6px;
    }
    .ticket-neto-val {
      font-size: 16px;
      font-weight: 900;
      color: #14532d;
      font-family: 'Space Mono', monospace;
      line-height: 1.2;
      margin: 2px 0;
    }
    .ticket-neto-ref {
      font-size: 9px;
      font-weight: 700;
      color: #15803d;
    }

    /* SECCIÓN DE FIRMAS */
    .ticket-signatures {
      margin-top: 8px;
      font-size: 8px;
      color: #64748b;
      text-align: center;
      line-height: 1.2;
    }
    .ticket-sig-box {
      margin-top: 12px;
    }
    .ticket-sig-line {
      border-top: 1px solid #475569;
      width: 75%;
      margin: 0 auto 3px;
    }

    /* CÓDIGO DE BARRAS & PIE DE TICKET */
    .ticket-barcode-wrap {
      text-align: center;
      margin-top: 10px;
      padding-top: 6px;
      border-top: 1px dashed #cbd5e1;
    }
    .ticket-barcode-lines {
      letter-spacing: 1px;
      font-family: 'Space Mono', monospace;
      font-size: 13px;
      font-weight: 900;
      color: #0f172a;
      line-height: 1;
      transform: scaleY(1.3);
      margin-bottom: 3px;
    }
    .ticket-barcode-code {
      font-family: 'Space Mono', monospace;
      font-size: 8px;
      color: #64748b;
      letter-spacing: 0.5px;
    }
    .ticket-thankyou {
      text-align: center;
      font-size: 8.5px;
      font-weight: 800;
      color: #475569;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      margin-top: 6px;
    }
    .ticket-footer-sub {
      text-align: center;
      font-size: 7.5px;
      color: #94a3b8;
      margin-top: 2px;
    }

    /* ═══════════════ REGLAS DE IMPRESIÓN ESTRICTAS ═══════════════ */
    @media print {
      @page {
        size: letter portrait;
        margin: 5mm 6mm;
      }
      body {
        background: #ffffff !important;
        padding: 0 !important;
        margin: 0 !important;
      }
      .no-print, .screen-actions-bar {
        display: none !important;
      }
      .tickets-container {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
      }

      /* MODO CARTA: 3 COLUMNAS X 2 FILAS (6 POR HOJA) */
      .view-grid .tickets-grid {
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        grid-template-rows: repeat(2, 1fr) !important;
        gap: 3mm 4mm !important;
        width: 100% !important;
      }
      .view-grid .ticket-receipt {
        border: 1px dashed #94a3b8 !important;
        box-shadow: none !important;
        padding: 6px 8px 8px !important;
        font-size: 9.5px !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
      }
      .view-grid .ticket-logo {
        width: 28px !important;
        height: 28px !important;
      }
      .view-grid .ticket-brand {
        font-size: 11.5px !important;
      }
      .view-grid .ticket-neto-val {
        font-size: 13px !important;
      }
      .view-grid .ticket-item {
        font-size: 9px !important;
      }

      /* Salto estricto cada 6 tickets en hoja Carta */
      .view-grid .ticket-receipt.break-page-6 {
        page-break-after: always !important;
        break-after: page !important;
      }

      /* MODO POS TÉRMICO 80mm */
      .view-pos .tickets-grid {
        display: block !important;
        width: 76mm !important;
        margin: 0 auto !important;
      }
      .view-pos .ticket-receipt {
        width: 76mm !important;
        border: none !important;
        border-bottom: 2px dashed #000000 !important;
        box-shadow: none !important;
        padding: 4mm 2mm 6mm !important;
        page-break-after: always !important;
        break-after: page !important;
      }
    }
  </style>
</head>
<body class="view-grid" id="bodyRoot">

  <!-- BARRA DE ACCIONES SUPERIOR -->
  <div class="screen-actions-bar no-print">
    <div class="actions-bar-left">
      <div class="badge-workers-count">
        <i class="fa-solid fa-receipt"></i>
        <span>Lote de Nómina (<?= $totalRecibos ?> Trabajadores)</span>
      </div>

      <!-- Selector de Modo de Impresión -->
      <div class="mode-switch-group">
        <button type="button" class="btn-mode-toggle active" id="btnModeGrid" onclick="setReceiptLayout('grid')" title="Parrilla compacta de 6 trabajadores por hoja Carta (3x2) para máximo ahorro">
          <i class="fa-solid fa-table-cells"></i> 6 por Hoja (Carta)
        </button>
        <button type="button" class="btn-mode-toggle" id="btnModePos" onclick="setReceiptLayout('pos')" title="Tira vertical térmica de 80mm (Punto de venta / Centro Comercial)">
          <i class="fa-solid fa-scroll"></i> Ticket 80mm (POS)
        </button>
      </div>
    </div>

    <div class="actions-bar-right">
      <a id="btnDownloadPdf" class="btn-pdf-link" href="index.php?r=nomina/recibo_masivo&id_nomina=<?= $id_nomina ?>&format=pdf&modo=carta6" target="_blank" title="Descargar documento PDF listo para imprimir o archivar">
        <i class="fa-solid fa-file-pdf" style="color:#ef4444;"></i> Descargar PDF
      </a>
      <button class="btn-print-cta" onclick="imprimirMasivoConAuditoria(<?= (int)$id_nomina ?>)" title="Enviar directamente a la impresora">
        <i class="fa-solid fa-print"></i> Imprimir Tickets
      </button>
      <a class="btn-back-link" href="index.php?r=nomina/detalle&id_nomina=<?= $id_nomina ?>">
        <i class="fa-solid fa-arrow-left"></i> Volver a la Nómina
      </a>
    </div>
  </div>

  <!-- CONTENEDOR PRINCIPAL DE TICKETS -->
  <div class="tickets-container">
    <div class="tickets-grid" id="ticketsGrid">
      <?php 
      foreach ($recibos as $index => $r):
          $empleado = $r['empleado'];
          $cargo = $r['cargo'];
          $detalle = $r['detalle'];
          $asignaciones = $r['asignaciones'];
          $deducciones = $r['deducciones'];
          
          $nombreEmpleado = trim(preg_replace('/\s+/', ' ', implode(' ', array_filter([
              $empleado['primer_nombre'] ?? '', 
              $empleado['segundo_nombre'] ?? '', 
              $empleado['primer_apellido'] ?? '', 
              $empleado['segundo_apellido'] ?? ''
          ]))));

          $netoPagar = (float)$detalle['neto_pagar'];
          $totalAsig = (float)$detalle['total_asignaciones'];
          $totalDeduc = (float)$detalle['total_deducciones'];
          $refUsd = ($tasaBcvMonto > 0) ? round($netoPagar / $tasaBcvMonto, 2) : 0;
          
          // Salto de página cada 6 recibos en modo Parrilla Carta
          $isSixth = (($index + 1) % 6 === 0) && ($index + 1 < $totalRecibos);
      ?>

        <div class="ticket-receipt <?= $isSixth ? 'break-page-6' : '' ?>">
          
          <!-- CABECERA DEL TICKET (MULTITEL C.A.) -->
          <div class="ticket-header">
            <img src="assets/images/logo_multitel.png" alt="Multitel" class="ticket-logo">
            <div class="ticket-brand"><?= htmlspecialchars($nombreEmpresa) ?></div>
            <div class="ticket-tagline">INTERNET &bull; FIBRA ÓPTICA</div>
            <div class="ticket-company-meta">
              <strong>R.I.F.:</strong> <?= htmlspecialchars($rifEmpresa) ?><br>
              Cumaná, Edo. Sucre &bull; CONATEL Habilitado
            </div>
            <div class="ticket-doc-badge">COMPROBANTE DE PAGO</div>
            <div class="ticket-period-sub">
              Nómina #<?= str_pad((string)$id_nomina, 5, '0', STR_PAD_LEFT) ?> &bull; <?= htmlspecialchars($descPeriodo) ?>
            </div>
          </div>

          <div class="ticket-dashed-line"></div>

          <!-- DATOS DEL TRABAJADOR -->
          <div class="ticket-worker-box">
            <div class="ticket-row">
              <span class="ticket-label">Trabajador:</span>
              <span class="ticket-val" title="<?= htmlspecialchars($nombreEmpleado) ?>"><?= htmlspecialchars(strtoupper($nombreEmpleado)) ?></span>
            </div>
            <div class="ticket-row">
              <span class="ticket-label">Cédula:</span>
              <span class="ticket-val"><?= htmlspecialchars($empleado['cedula'] ?? '—') ?></span>
            </div>
            <div class="ticket-row">
              <span class="ticket-label">Cargo:</span>
              <span class="ticket-val"><?= htmlspecialchars($cargo['nombre_cargo'] ?? 'General') ?></span>
            </div>
            <div class="ticket-row">
              <span class="ticket-label">F. Ingreso:</span>
              <span class="ticket-val"><?= htmlspecialchars($empleado['fecha_ingreso'] ?? '—') ?></span>
            </div>
            <div class="ticket-row">
              <span class="ticket-label">Días Lab:</span>
              <span class="ticket-val"><?= (int)($detalle['dias_trabajados'] ?? 0) ?> d. &bull; SD: Bs. <?= number_format((float)($detalle['sueldo_diario'] ?? 0), 2, ',', '.') ?></span>
            </div>
          </div>

          <div class="ticket-dashed-line"></div>

          <!-- ASIGNACIONES -->
          <div class="ticket-section-title title-asig">
            <span>(+) Asignaciones</span>
            <span>Monto (Bs.)</span>
          </div>
          <ul class="ticket-items-list">
            <?php if (!empty($asignaciones)): ?>
              <?php foreach ($asignaciones as $asig): 
                $cant = !empty($asig['cantidad']) && (float)$asig['cantidad'] > 0 ? ' (' . (float)$asig['cantidad'] . ')' : '';
              ?>
                <li class="ticket-item">
                  <span class="ticket-item-name"><?= htmlspecialchars(($asig['nombre_concepto'] ?? $asig['nombre'] ?? 'Asignación') . $cant) ?></span>
                  <span class="ticket-dots"></span>
                  <span class="ticket-item-amount"><?= number_format((float)($asig['monto'] ?? 0), 2, ',', '.') ?></span>
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="ticket-item">
                <span class="ticket-item-name">Sueldo Básico</span>
                <span class="ticket-dots"></span>
                <span class="ticket-item-amount">0,00</span>
              </li>
            <?php endif; ?>
          </ul>
          <div class="ticket-subtotal-row title-asig">
            <span>TOTAL ASIG:</span>
            <span>Bs. <?= number_format($totalAsig, 2, ',', '.') ?></span>
          </div>

          <div class="ticket-dashed-line"></div>

          <!-- DEDUCCIONES -->
          <div class="ticket-section-title title-ded">
            <span>(-) Deducciones</span>
            <span>Monto (Bs.)</span>
          </div>
          <ul class="ticket-items-list">
            <?php if (!empty($deducciones)): ?>
              <?php foreach ($deducciones as $ded): ?>
                <li class="ticket-item">
                  <span class="ticket-item-name"><?= htmlspecialchars($ded['nombre_concepto'] ?? $ded['nombre'] ?? 'Deducción') ?></span>
                  <span class="ticket-dots"></span>
                  <span class="ticket-item-amount"><?= number_format((float)($ded['monto'] ?? 0), 2, ',', '.') ?></span>
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="ticket-item">
                <span class="ticket-item-name">Sin deducciones</span>
                <span class="ticket-dots"></span>
                <span class="ticket-item-amount">0,00</span>
              </li>
            <?php endif; ?>
          </ul>
          <div class="ticket-subtotal-row title-ded">
            <span>TOTAL DED:</span>
            <span>Bs. <?= number_format($totalDeduc, 2, ',', '.') ?></span>
          </div>

          <div class="ticket-double-line"></div>

          <!-- TOTAL NETO A COBRAR -->
          <div class="ticket-neto-card">
            <div class="ticket-neto-lbl">Neto Efectivo a Cobrar</div>
            <div class="ticket-neto-val">Bs. <?= number_format($netoPagar, 2, ',', '.') ?></div>
            <?php if ($tasaBcvMonto > 0): ?>
              <div class="ticket-neto-ref">
                (Ref. $<?= number_format($refUsd, 2, '.', ',') ?> USD @ <?= number_format($tasaBcvMonto, 2, ',', '.') ?> Bs/$)
              </div>
            <?php endif; ?>
          </div>

          <div class="ticket-dashed-line"></div>

          <!-- FIRMAS DE CONFORMIDAD -->
          <div class="ticket-signatures">
            <div class="ticket-sig-box">
              <div class="ticket-sig-line"></div>
              <strong>Firma del Trabajador (Recibí Conforme)</strong><br>
              <span>C.I.: <?= htmlspecialchars($empleado['cedula'] ?? '______________') ?></span>
            </div>
            <div class="ticket-sig-box">
              <div class="ticket-sig-line"></div>
              <strong>Por <?= htmlspecialchars($nombreEmpresa) ?></strong><br>
              <span>Sello y Firma Autorizada (RRHH)</span>
            </div>
          </div>

        </div>

      <?php endforeach; ?>
    </div>
  </div>

  <script>
  function setReceiptLayout(mode) {
    const bodyRoot = document.getElementById('bodyRoot');
    const btnGrid = document.getElementById('btnModeGrid');
    const btnPos = document.getElementById('btnModePos');
    const btnPdf = document.getElementById('btnDownloadPdf');

    if (mode === 'grid') {
      bodyRoot.className = 'view-grid';
      btnGrid.classList.add('active');
      btnPos.classList.remove('active');
      if (btnPdf) {
        btnPdf.href = 'index.php?r=nomina/recibo_masivo&id_nomina=<?= $id_nomina ?>&format=pdf&modo=carta6';
      }
    } else {
      bodyRoot.className = 'view-pos';
      btnPos.classList.add('active');
      btnGrid.classList.remove('active');
      if (btnPdf) {
        btnPdf.href = 'index.php?r=nomina/recibo_masivo&id_nomina=<?= $id_nomina ?>&format=pdf&modo=pos80';
      }
    }
  }

  function imprimirMasivoConAuditoria(idNomina) {
    try {
      fetch('index.php?r=nomina/logImpresionRecibo', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id_nomina=' + encodeURIComponent(idNomina) + '&id_empleado=0&tipo=masivo'
      }).catch(function(){});
    } catch(e){}
    window.print();
  }
  </script>

</body>
</html>
