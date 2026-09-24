<?php
$empConfig = $empConfig ?? [];
$nombreEmpresa = $empConfig['nombre_empresa'] ?? 'EMPRESA DEMO C.A.';
$rifEmpresa = $empConfig['rif'] ?? 'J-00000000-0';
$regimenIvss = $empConfig['regimen_ivss'] ?? 'Clase_II';

$nombreEmpleado = trim(preg_replace('/\s+/', ' ', implode(' ', array_filter([
    $empleado['primer_nombre'] ?? '', 
    $empleado['segundo_nombre'] ?? '', 
    $empleado['primer_apellido'] ?? '', 
    $empleado['segundo_apellido'] ?? ''
]))));

$descPeriodo = !empty($periodo['descripcion']) ? $periodo['descripcion'] : ($periodo['tipo_periodo'] . " (" . $periodo['fecha_inicio'] . " al " . $periodo['fecha_fin'] . ")");
$tasaBcvMonto = !empty($ultimaTasa['monto_bcv']) ? (float)$ultimaTasa['monto_bcv'] : 0;
$netoPagar = (float)$detalle['neto_pagar'];
$refUsd = ($tasaBcvMonto > 0) ? round($netoPagar / $tasaBcvMonto, 2) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Comprobante de Pago - <?= htmlspecialchars($nombreEmpleado) ?> (C.I. <?= htmlspecialchars($empleado['cedula']) ?>)</title>
  <link rel="icon" type="image/png" href="assets/images/logo_multitel.png?v=1">
  <link rel="shortcut icon" type="image/png" href="assets/images/logo_multitel.png?v=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      background: #f1f5f9;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
      color: #1e293b;
      padding: 24px 16px 40px;
    }

    /* BARRA FLOTANTE DE ACCIÓN (SOLO PANTALLA) */
    .screen-actions-bar {
      max-width: 820px;
      margin: 0 auto 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #ffffff;
      padding: 12px 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.06);
      border: 1px solid #e2e8f0;
    }
    .btn-print-cta {
      background: #134cb5;
      color: #ffffff;
      font-size: 13.5px;
      font-weight: 700;
      padding: 10px 22px;
      border-radius: 7px;
      border: none;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.15s ease;
      box-shadow: 0 2px 6px rgba(19,76,181,0.3);
    }
    .btn-print-cta:hover {
      background: #0d3a8f;
      transform: translateY(-1px);
    }
    .btn-back-link {
      color: #64748b;
      text-decoration: none;
      font-size: 13px;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 14px;
      border-radius: 6px;
      border: 1px solid #cbd5e1;
      background: #f8fafc;
      transition: all 0.15s ease;
    }
    .btn-back-link:hover {
      background: #e2e8f0;
      color: #0f172a;
    }

    /* CONTENEDOR PRINCIPAL DEL DOCUMENTO */
    .recibo-sheet-container {
      max-width: 820px;
      margin: 0 auto;
      background: #ffffff;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
      padding: 24px;
    }

    /* MEMBRETE CORPORATIVO OFICIAL MULTITEL C.A. */
    .company-brand-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-bottom: 14px;
      margin-bottom: 4px;
    }
    .brand-left {
      display: flex;
      align-items: center;
      gap: 16px;
    }
    .brand-logo-box {
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .multitel-real-logo {
      width: 58px;
      height: 58px;
      border-radius: 50%;
      object-fit: contain;
      display: block;
      box-shadow: 0 3px 10px rgba(19,76,181,0.25);
    }
    .brand-titles {
      display: flex;
      flex-direction: column;
    }
    .brand-badge-isp {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 10px;
      font-weight: 800;
      color: #134cb5;
      text-transform: uppercase;
      letter-spacing: 1.2px;
      margin-bottom: 2px;
    }
    .isp-dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: #10b981;
      display: inline-block;
      box-shadow: 0 0 6px #10b981;
    }
    .brand-company-name {
      font-size: 22px;
      font-weight: 900;
      color: #0f172a;
      letter-spacing: -0.5px;
      line-height: 1.1;
      font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .brand-subname {
      color: #134cb5;
      font-weight: 900;
    }
    .brand-company-rif {
      font-size: 12px;
      color: #475569;
      font-weight: 600;
      margin-top: 3px;
    }
    .brand-company-meta {
      font-size: 11px;
      color: #64748b;
      margin-top: 2px;
    }
    .brand-right {
      text-align: right;
    }
    .voucher-title {
      font-size: 15px;
      font-weight: 800;
      color: #134cb5;
      letter-spacing: 0.5px;
    }
    .voucher-subtitle {
      font-size: 10px;
      font-weight: 700;
      color: #64748b;
      margin-top: 2px;
      letter-spacing: 0.5px;
    }
    .voucher-meta-tag {
      display: inline-block;
      margin-top: 5px;
      background: #f1f5f9;
      color: #0f172a;
      font-size: 10px;
      font-weight: 700;
      padding: 3px 9px;
      border-radius: 4px;
      border: 1px solid #cbd5e1;
    }
    .voucher-timestamp {
      font-size: 9.5px;
      color: #94a3b8;
      margin-top: 4px;
      font-weight: 500;
    }

    /* TIRA DE FIBRA ÓPTICA */
    .fiber-optic-line {
      height: 3.5px;
      background: linear-gradient(90deg, #134cb5 0%, #2563eb 30%, #38bdf8 65%, #10b981 100%);
      border-radius: 2px;
      margin-bottom: 16px;
    }

    /* TALÓN DE RECIBO */
    .recibo-voucher {
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      overflow: hidden;
      margin-bottom: 24px;
    }
    .recibo-voucher:last-child {
      margin-bottom: 0;
    }
    .recibo-voucher-bar {
      background: linear-gradient(90deg, #0f172a 0%, #1e293b 100%);
      color: #ffffff;
      padding: 8px 14px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 11.5px;
    }
    .recibo-voucher-bar strong {
      letter-spacing: 0.5px;
    }

    /* DATOS DEL EMPLEADO */
    .recibo-employee-box {
      background: #f8fafc;
      border-bottom: 1px solid #e2e8f0;
      padding: 10px 14px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 8px 16px;
      font-size: 11.5px;
    }
    .recibo-info-item {
      display: flex;
      flex-direction: column;
    }
    .recibo-info-label {
      font-size: 9.5px;
      text-transform: uppercase;
      color: #64748b;
      font-weight: 700;
    }
    .recibo-info-val {
      font-size: 12px;
      font-weight: 600;
      color: #0f172a;
    }

    /* TABLA ASIGNACIONES VS DEDUCCIONES */
    .recibo-tables-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      border-bottom: 1px solid #e2e8f0;
    }
    .recibo-col-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 11.5px;
    }
    .recibo-col-table:first-child {
      border-right: 1px solid #e2e8f0;
    }
    .recibo-col-table th {
      background: #f1f5f9;
      padding: 6px 12px;
      font-size: 10.5px;
      text-transform: uppercase;
      font-weight: 700;
      border-bottom: 1px solid #cbd5e1;
    }
    .recibo-col-table th.asig-head {
      color: #166534;
      text-align: left;
    }
    .recibo-col-table th.ded-head {
      color: #991b1b;
      text-align: left;
    }
    .recibo-col-table td {
      padding: 5px 12px;
      border-bottom: 1px solid #f1f5f9;
    }
    .recibo-col-table tr:nth-child(even) td {
      background: #fafafa;
    }
    .recibo-col-table .col-monto {
      text-align: right;
      font-variant-numeric: tabular-nums;
      font-weight: 500;
    }

    /* SUB-TOTALES */
    .recibo-totals-strip {
      display: grid;
      grid-template-columns: 1fr 1fr;
      background: #f8fafc;
      border-bottom: 1px solid #cbd5e1;
      font-size: 11.5px;
      font-weight: 700;
    }
    .recibo-total-box {
      padding: 7px 14px;
      display: flex;
      justify-content: space-between;
    }
    .recibo-total-box:first-child {
      border-right: 1px solid #e2e8f0;
      color: #166534;
    }
    .recibo-total-box:last-child {
      color: #991b1b;
    }

    /* NETO A PAGAR */
    .recibo-neto-card {
      background: #dcfce7;
      border-bottom: 1px solid #86efac;
      padding: 9px 14px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: #166534;
    }
    .recibo-neto-label {
      font-size: 12px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .recibo-neto-val {
      font-size: 15px;
      font-weight: 800;
      font-variant-numeric: tabular-nums;
    }

    /* FIRMAS */
    .recibo-signatures-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px;
      padding: 24px 20px 14px;
      background: #ffffff;
    }
    .signature-field {
      text-align: center;
      font-size: 10.5px;
      color: #475569;
    }
    .signature-line {
      border-top: 1px solid #475569;
      width: 75%;
      margin: 0 auto 5px;
    }

    /* LÍNEA DE CORTE */
    .cut-separator {
      border-top: 1px dashed #94a3b8;
      text-align: center;
      margin: 18px 0;
      position: relative;
    }
    .cut-badge {
      position: relative;
      top: -9px;
      background: #ffffff;
      padding: 0 12px;
      font-size: 10px;
      color: #94a3b8;
      text-transform: uppercase;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    /* REGLAS ESTRICTAS DE IMPRESIÓN */
    @media print {
      @page {
        size: letter portrait;
        margin: 8mm 10mm;
      }
      body {
        background: #ffffff !important;
        padding: 0 !important;
        margin: 0 !important;
        color: #000000 !important;
      }
      .no-print, .screen-actions-bar {
        display: none !important;
      }
      .recibo-sheet-container {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        max-width: 100% !important;
      }
    }
  </style>
</head>
<body>

  <!-- BARRA DE CONTROL EN PANTALLA (DESAPARECE AL IMPRIMIR) -->
  <div class="screen-actions-bar no-print">
    <div style="display:flex; align-items:center; gap:8px;">
      <i class="fa-solid fa-file-invoice-dollar" style="color:#0284c7; font-size:18px;"></i>
      <span style="font-size:13px; font-weight:700; color:#0f172a;">Vista Previa de Comprobante Oficial</span>
    </div>
    <div style="display:flex; gap:10px; align-items:center;">
      <button class="btn-print-cta" onclick="imprimirReciboConAuditoria(<?= (int)$id_nomina ?>, <?= (int)$empleado['id_empleado'] ?>)">
        <i class="fa-solid fa-print"></i> Imprimir / Guardar como PDF
      </button>
      <a class="btn-back-link" href="index.php?r=nomina/detalle&id_nomina=<?= $id_nomina ?>">
        <i class="fa-solid fa-arrow-left"></i> Volver a la Nómina
      </a>
    </div>
  </div>

  <div class="recibo-sheet-container">
    
    <!-- MEMBRETE CORPORATIVO OFICIAL MULTITEL C.A. -->
    <div class="company-brand-header">
      <div class="brand-left">
        <div class="brand-logo-box">
          <img src="assets/images/logo_multitel.png" alt="MULTITEL C.A." class="multitel-real-logo">
        </div>
        <div class="brand-titles">
          <div class="brand-badge-isp">
            <span class="isp-dot"></span> PROVEEDOR DE INTERNET &bull; FIBRA ÓPTICA
          </div>
          <h1 class="brand-company-name">MULTITEL <span class="brand-subname">C.A.</span></h1>
          <div class="brand-company-rif"><strong>R.I.F.:</strong> <?= htmlspecialchars($rifEmpresa) ?> &bull; <strong>CONATEL:</strong> Operador de Telecomunicaciones Habilitado</div>
          <div class="brand-company-meta">
            <i class="fa-solid fa-location-dot" style="color:#0284c7; margin-right:3px;"></i> Cumaná, Estado Sucre, Venezuela &bull; 
            <strong>Régimen IVSS:</strong> <?= htmlspecialchars(str_replace('_', ' ', $regimenIvss)) ?>
          </div>
        </div>
      </div>
      <div class="brand-right">
        <div class="voucher-title">COMPROBANTE DE PAGO</div>
        <div class="voucher-subtitle">DEPARTAMENTO DE TALENTO HUMANO</div>
        <div class="voucher-meta-tag">NÓMINA #<?= str_pad((string)$id_nomina, 5, '0', STR_PAD_LEFT) ?> &bull; <?= htmlspecialchars($descPeriodo) ?></div>
        <div class="voucher-timestamp"><i class="fa-solid fa-tower-broadcast" style="color:#0284c7; margin-right:3px;"></i> Red de Fibra Cumaná</div>
      </div>
    </div>

    <!-- TIRA DE FIBRA ÓPTICA -->
    <div class="fiber-optic-line"></div>

    <!-- TALÓN 1: ORIGINAL (TRABAJADOR) -->
    <div class="recibo-voucher">
      <div class="recibo-voucher-bar">
        <span><strong>ORIGINAL &bull; TRABAJADOR</strong></span>
        <span>Periodo: <?= htmlspecialchars($periodo['fecha_inicio']) ?> al <?= htmlspecialchars($periodo['fecha_fin']) ?></span>
      </div>

      <div class="recibo-employee-box">
        <div class="recibo-info-item">
          <span class="recibo-info-label">Trabajador</span>
          <span class="recibo-info-val"><?= htmlspecialchars(strtoupper($nombreEmpleado)) ?></span>
        </div>
        <div class="recibo-info-item">
          <span class="recibo-info-label">Cédula de Identidad</span>
          <span class="recibo-info-val"><?= htmlspecialchars($empleado['cedula'] ?? '—') ?></span>
        </div>
        <div class="recibo-info-item">
          <span class="recibo-info-label">Cargo Desempeñado</span>
          <span class="recibo-info-val"><?= htmlspecialchars($cargo['nombre_cargo'] ?? 'General') ?></span>
        </div>
        <div class="recibo-info-item">
          <span class="recibo-info-label">Fecha de Ingreso</span>
          <span class="recibo-info-val"><?= htmlspecialchars($empleado['fecha_ingreso'] ?? '—') ?></span>
        </div>
        <div class="recibo-info-item">
          <span class="recibo-info-label">Sueldo Diario</span>
          <span class="recibo-info-val">Bs. <?= number_format((float)$detalle['sueldo_diario'], 2, ',', '.') ?></span>
        </div>
        <div class="recibo-info-item">
          <span class="recibo-info-label">Días Laborados</span>
          <span class="recibo-info-val"><?= htmlspecialchars($detalle['dias_trabajados']) ?> días</span>
        </div>
        <div class="recibo-info-item">
          <span class="recibo-info-label">Inasistencias</span>
          <span class="recibo-info-val"><?= htmlspecialchars($detalle['dias_inasistencia'] ?? 0) ?> días</span>
        </div>
        <div class="recibo-info-item">
          <span class="recibo-info-label">Semanas IVSS</span>
          <span class="recibo-info-val"><?= htmlspecialchars($nomina['semanas_descontar_ivss'] ?? 2) ?> semanas</span>
        </div>
      </div>

      <div class="recibo-tables-grid">
        <!-- Tabla Asignaciones -->
        <table class="recibo-col-table">
          <thead>
            <tr>
              <th class="asig-head">Asignaciones / Percepciones</th>
              <th class="asig-head" style="text-align:right;">Monto (Bs.)</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $maxRows = max(count($asignaciones), count($deducciones), 3);
            for ($i = 0; $i < $maxRows; $i++): 
              $a = $asignaciones[$i] ?? null;
            ?>
            <tr>
              <td>
                <?php if ($a): ?>
                  <?= htmlspecialchars($a['nombre_concepto'] ?? $a['nombre'] ?? '') ?>
                  <?= (!empty($a['cantidad']) && (float)$a['cantidad'] > 0) ? ' <small style="color:#64748b;">(' . (float)$a['cantidad'] . ')</small>' : '' ?>
                <?php else: ?>
                  &nbsp;
                <?php endif; ?>
              </td>
              <td class="col-monto">
                <?= $a ? number_format((float)$a['monto'], 2, ',', '.') : '' ?>
              </td>
            </tr>
            <?php endfor; ?>
          </tbody>
        </table>

        <!-- Tabla Deducciones -->
        <table class="recibo-col-table">
          <thead>
            <tr>
              <th class="ded-head">Deducciones / Retenciones</th>
              <th class="ded-head" style="text-align:right;">Monto (Bs.)</th>
            </tr>
          </thead>
          <tbody>
            <?php for ($i = 0; $i < $maxRows; $i++): 
              $d = $deducciones[$i] ?? null;
            ?>
            <tr>
              <td>
                <?= $d ? htmlspecialchars($d['nombre_concepto'] ?? $d['nombre'] ?? '') : '&nbsp;' ?>
              </td>
              <td class="col-monto" style="color:#991b1b;">
                <?= $d ? number_format((float)$d['monto'], 2, ',', '.') : '' ?>
              </td>
            </tr>
            <?php endfor; ?>
          </tbody>
        </table>
      </div>

      <!-- Sub-totales -->
      <div class="recibo-totals-strip">
        <div class="recibo-total-box">
          <span>TOTAL ASIGNACIONES:</span>
          <span>Bs. <?= number_format((float)$detalle['total_asignaciones'], 2, ',', '.') ?></span>
        </div>
        <div class="recibo-total-box">
          <span>TOTAL DEDUCCIONES:</span>
          <span>Bs. <?= number_format((float)$detalle['total_deducciones'], 2, ',', '.') ?></span>
        </div>
      </div>

      <!-- Neto a Pagar -->
      <div class="recibo-neto-card">
        <div class="recibo-neto-label">
          <i class="fa-solid fa-money-check-dollar" style="margin-right:6px;"></i> Neto a Cobrar Efectivo:
        </div>
        <div class="recibo-neto-val">
          Bs. <?= number_format($netoPagar, 2, ',', '.') ?>
          <?php if ($refUsd > 0): ?>
            <span style="font-size:11.5px; font-weight:600; color:#15803d; margin-left:6px;">
              (Ref. $<?= number_format($refUsd, 2) ?> USD @ <?= number_format($tasaBcvMonto, 2, ',', '.') ?>)
            </span>
          <?php endif; ?>
        </div>
      </div>

      <!-- Firmas Legales -->
      <div class="recibo-signatures-row">
        <div class="signature-field">
          <div class="signature-line"></div>
          <strong>Firma del Trabajador (Recibí Conforme)</strong><br>
          <span>C.I. <?= htmlspecialchars($empleado['cedula'] ?? '') ?></span>
        </div>
        <div class="signature-field">
          <div class="signature-line"></div>
          <strong>Por <?= htmlspecialchars($nombreEmpresa) ?></strong><br>
          <span>Sello y Firma Autorizada (RRHH / Pagaduría)</span>
        </div>
      </div>
    </div>

    <!-- LÍNEA DE CORTE -->
    <div class="cut-separator">
      <span class="cut-badge"><i class="fa-solid fa-scissors"></i> Corte aquí &bull; Original Trabajador / Copia Expediente</span>
    </div>

    <!-- TALÓN 2: COPIA (EXPEDIENTE EMPRESA) -->
    <div class="recibo-voucher">
      <div class="recibo-voucher-bar" style="background:#1e293b;">
        <span><strong>COPIA &bull; EXPEDIENTE DE LA EMPRESA</strong></span>
        <span>Periodo: <?= htmlspecialchars($periodo['fecha_inicio']) ?> al <?= htmlspecialchars($periodo['fecha_fin']) ?></span>
      </div>

      <div class="recibo-employee-box">
        <div class="recibo-info-item">
          <span class="recibo-info-label">Trabajador</span>
          <span class="recibo-info-val"><?= htmlspecialchars(strtoupper($nombreEmpleado)) ?></span>
        </div>
        <div class="recibo-info-item">
          <span class="recibo-info-label">Cédula de Identidad</span>
          <span class="recibo-info-val"><?= htmlspecialchars($empleado['cedula'] ?? '—') ?></span>
        </div>
        <div class="recibo-info-item">
          <span class="recibo-info-label">Cargo Desempeñado</span>
          <span class="recibo-info-val"><?= htmlspecialchars($cargo['nombre_cargo'] ?? 'General') ?></span>
        </div>
        <div class="recibo-info-item">
          <span class="recibo-info-label">Total Neto Percibido</span>
          <span class="recibo-info-val" style="color:#166534;">Bs. <?= number_format($netoPagar, 2, ',', '.') ?></span>
        </div>
      </div>

      <div class="recibo-totals-strip" style="border-top:1px solid #cbd5e1;">
        <div class="recibo-total-box">
          <span>TOTAL ASIGNACIONES:</span>
          <span>Bs. <?= number_format((float)$detalle['total_asignaciones'], 2, ',', '.') ?></span>
        </div>
        <div class="recibo-total-box">
          <span>TOTAL DEDUCCIONES:</span>
          <span>Bs. <?= number_format((float)$detalle['total_deducciones'], 2, ',', '.') ?></span>
        </div>
      </div>

      <div class="recibo-signatures-row" style="padding-top:20px;">
        <div class="signature-field">
          <div class="signature-line"></div>
          <strong>Firma del Trabajador</strong><br>
          <span>C.I. <?= htmlspecialchars($empleado['cedula'] ?? '') ?></span>
        </div>
        <div class="signature-field">
          <div class="signature-line"></div>
          <strong>Por la Empresa (Recibido RRHH)</strong><br>
          <span>Fecha de Entrega: ____ / ____ / ________</span>
        </div>
      </div>
    </div>

  </div>

  <script nonce="<?= class_exists('Security') ? Security::getCspNonce() : '' ?>">
  function imprimirReciboConAuditoria(idNomina, idEmpleado) {
    try {
      fetch('index.php?r=nomina/logImpresionRecibo', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id_nomina=' + encodeURIComponent(idNomina) + '&id_empleado=' + encodeURIComponent(idEmpleado) + '&tipo=individual'
      }).catch(function(){});
    } catch(e){}
    window.print();
  }
  </script>
</body>
</html>
