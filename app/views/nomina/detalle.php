<?php
$totalTrabajadores = count($detallesList);
$totalBrutoAcumulado = 0;
$totalNetoAcumulado = 0;
foreach ($detallesList as $d) {
    $totalBrutoAcumulado += (float)$d['total_asignaciones'];
    $totalNetoAcumulado += (float)$d['neto_pagar'];
}
$descPeriodo = !empty($periodo['descripcion']) ? $periodo['descripcion'] : $periodo['tipo_periodo'];

// Porcentajes de retención legal configurados a nivel de empresa
$empConfig = $empConfig ?? [];
$regimenClave = $empConfig['regimen_ivss'] ?? 'Clase_II';
$regimenesMap = [
    'Clase_I' => 'Riesgo Mínimo (Clase I)',
    'Clase_II' => 'Riesgo Medio (Clase II)',
    'Clase_III' => 'Riesgo Máximo (Clase III)'
];
$regimenNombre = $regimenesMap[$regimenClave] ?? $regimenClave;
$salarioMinimoConfig = (float)($empConfig['salario_minimo_legal'] ?? ($nomina['salario_minimo_vigente'] ?? 130.00));
$ssoRaw = (float)($empConfig['tasa_sso_trabajador'] ?? ($nomina['tasa_sso'] ?? 0.04));
$rpeRaw = (float)($empConfig['tasa_rpe_trabajador'] ?? ($nomina['tasa_rpe'] ?? 0.005));
$lphRaw = (float)($empConfig['tasa_faov_trabajador'] ?? ($nomina['tasa_lph'] ?? 0.01));

$ssoPatronoRaw = (float)($empConfig['tasa_sso_patronal'] ?? 0.10);
$rpePatronoRaw = (float)($empConfig['tasa_rpe_patronal'] ?? 0.021);
$lphPatronoRaw = (float)($empConfig['tasa_faov_patronal'] ?? 0.02);

$ssoVal = round($ssoRaw * 100, 2);
$rpeVal = round($rpeRaw * 100, 2);
$lphVal = round($lphRaw * 100, 2);

$ssoText = (fmod($ssoVal, 1) == 0) ? number_format($ssoVal, 0) : number_format($ssoVal, 1, ',', '.');
$rpeText = (fmod($rpeVal, 1) == 0) ? number_format($rpeVal, 0) : number_format($rpeVal, 1, ',', '.');
$lphText = (fmod($lphVal, 1) == 0) ? number_format($lphVal, 0) : number_format($lphVal, 1, ',', '.');
?>

<?php
$evalDel = isset($nominaModel) ? $nominaModel->verificarPuedeEliminar($nomina) : ['puede' => false, 'motivo' => '', 'horas_restantes' => 0];
$puedeEliminarDetalle = $evalDel['puede'];
$motivoEliminarDetalle = $evalDel['motivo'];
$horasRestantesDetalle = $evalDel['horas_restantes'] ?? 0;
?>

<?php if (!empty($msg)): ?>
  <div class="alert success" style="margin-bottom:16px;"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($msg) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
  <div class="alert danger" style="margin-bottom:16px;"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- BREADCRUMB & TOOLBAR -->
<div class="view-header-bar">
  <div class="breadcrumb-trail">
    <a href="index.php?r=periodos" class="btn-link-back"><i class="fa-solid fa-arrow-left"></i> Volver a Periodos</a>
    <span class="sep">/</span>
    <span class="current">Carga de Nómina</span>
  </div>
  <div class="header-actions" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
    <a class="btn secondary small" href="index.php?r=nomina/ver&id_nomina=<?= $nomina['id_nomina'] ?>"><i class="fa-solid fa-chart-pie"></i> Ver Totales / Exportar</a>
    <a class="btn secondary small" href="index.php?r=nomina/recibo&id_nomina=<?= $nomina['id_nomina'] ?>" target="_blank"><i class="fa-solid fa-receipt"></i> Recibos de Nómina</a>
    <?php if ($puedeEliminarDetalle): ?>
      <a class="btn danger small" style="background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;" href="index.php?r=nomina/eliminar&id_nomina=<?= $nomina['id_nomina'] ?>" onclick="return confirm('¿Confirmas que deseas eliminar esta nómina por completo? La nómina no tiene cambios guardados y está dentro de las 24 horas iniciales (quedan <?= $horasRestantesDetalle ?> h disponibles). El periodo volverá a quedar disponible.');" title="Eliminar nómina (Quedan <?= $horasRestantesDetalle ?> h disponibles)">
        <i class="fa-solid fa-trash-can"></i> Eliminar Nómina
      </a>
    <?php else: ?>
      <button type="button" class="btn small" style="background:#f8fafc; color:#94a3b8; border:1px solid #e2e8f0; opacity:0.75; cursor:help;" onclick="alert('<?= addslashes(htmlspecialchars($motivoEliminarDetalle)) ?>')" title="<?= htmlspecialchars($motivoEliminarDetalle) ?>">
        <i class="fa-solid fa-lock"></i> No eliminable
      </button>
    <?php endif; ?>
  </div>
</div>

<!-- EXECUTIVE HERO CARD -->
<div class="hero-payroll-card">
  <div class="hero-main-info">
    <div class="period-badge-tag"><?= htmlspecialchars($periodo['tipo_periodo']) ?></div>
    <h2 class="hero-title"><?= htmlspecialchars($descPeriodo) ?></h2>
    <div class="hero-dates"><i class="fa-solid fa-calendar-days" style="margin-right:4px;"></i> <?= htmlspecialchars($periodo['fecha_inicio']) ?> &rarr; <?= htmlspecialchars($periodo['fecha_fin']) ?></div>
  </div>

  <div class="hero-params-grid">
    <div class="param-pill" title="Régimen de riesgo de la empresa">
      <span class="lbl">Régimen Empresa</span>
      <span class="val"><i class="fa-solid fa-shield-halved" style="color:var(--primary); margin-right:3px;"></i> <?= htmlspecialchars($regimenNombre) ?></span>
    </div>
    <div class="param-pill" title="Salario Mínimo Legal Vigente">
      <span class="lbl">Salario Mínimo</span>
      <span class="val">Bs. <?= number_format($salarioMinimoConfig, 2, ',', '.') ?></span>
    </div>
    <a href="index.php?r=configuracion" class="param-pill param-pill-interactive" title="Configuración General de la Empresa (Afecta a todos los trabajadores)" style="text-decoration:none; color:inherit;">
      <div class="param-pill-header">
        <span class="lbl"><i class="fa-solid fa-scale-balanced" style="margin-right:3px;"></i> Deducciones Ley (Empresa)</span>
        <span class="pill-badge-edit"><i class="fa-solid fa-gear"></i> Ajustar</span>
      </div>
      <span class="val">SSO <?= $ssoText ?>% | RPE <?= $rpeText ?>% | FAOV <?= $lphText ?>%</span>
    </a>
  </div>

  <div class="hero-action-btn">
    <button class="btn btn-hero-cta" onclick="abrirModalTrabajadores()">
      <i class="fa-solid fa-user-plus" style="margin-right:6px;"></i> Seleccionar Trabajadores para Pago
    </button>
  </div>
</div>

<!-- TOP SUMMARY METRICS -->
<div class="metrics-summary-row">
  <div class="metric-card">
    <div class="m-icon" style="background:#eef7f3; color:var(--primary);"><i class="fa-solid fa-users"></i></div>
    <div class="m-info">
      <span class="m-label">Trabajadores en Nómina</span>
      <strong class="m-val"><?= $totalTrabajadores ?></strong>
    </div>
  </div>
  <div class="metric-card">
    <div class="m-icon" style="background:#eff6ff; color:#1d4ed8;"><i class="fa-solid fa-file-invoice-dollar"></i></div>
    <div class="m-info">
      <span class="m-label">Total Asignaciones Brutas</span>
      <strong class="m-val" id="metricTotalBruto">Bs. <?= number_format($totalBrutoAcumulado, 2, ',', '.') ?></strong>
    </div>
  </div>
  <div class="metric-card metric-card-highlight">
    <div class="m-icon" style="background:rgba(255,255,255,0.2); color:#fff;"><i class="fa-solid fa-money-bill-wave"></i></div>
    <div class="m-info">
      <span class="m-label" style="color:rgba(255,255,255,0.9);">Monto Total Neto a Pagar</span>
      <strong class="m-val" id="metricTotalNeto" style="color:#ffffff;">Bs. <?= number_format($totalNetoAcumulado, 2, ',', '.') ?></strong>
    </div>
  </div>
</div>

<?php if (!empty($msg)): ?><div class="alert success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<?php if (empty($detallesList)): ?>
  <div class="card empty-payroll-state">
    <div class="empty-icon"><i class="fa-solid fa-clipboard-list"></i></div>
    <h3>No se han seleccionado trabajadores para este pago de nómina.</h3>
    <p>Haz clic en el botón a continuación para abrir el catálogo y seleccionar los empleados que cobrarán en este periodo.</p>
    <br>
    <button class="btn btn-hero-cta" onclick="abrirModalTrabajadores()">
      <i class="fa-solid fa-user-plus"></i> Seleccionar Trabajadores
    </button>
  </div>
<?php else: ?>

<!-- DATA MATRIX TABLE CARD -->
<div class="card-table-wrap">
  <div class="table-scroll-container">
    <table class="matrix-table">
      <thead>
        <tr>
          <th rowspan="2" class="sticky-col" title="Identificación del trabajador: Nombre completo, Cédula de Identidad y Cargo asignado">EMPLEADO</th>
          <th rowspan="2" title="Salario Diario: Calculado como (Sueldo en Divisas × Tasa de Cambio ÷ 30 días). Base fundamental para todos los cálculos.">S. DIARIO</th>
          <th colspan="5" class="header-group asig-group" title="Carga manual de conceptos: Días laborados, horas nocturnas, horas extra, feriados y otras asignaciones.">CARGA DE ASIGNACIONES</th>
          <th colspan="5" class="header-group calc-asig-group" title="Asignaciones devengadas: Resultados de Sueldo Base + Bono Nocturno + Horas Extra + Feriados + Total Bruto.">ASIGNACIONES CALCULADAS</th>
          <th colspan="3" class="header-group ded-group" title="Carga manual de deducciones: Inasistencias, préstamos y retención directa de ISLR.">CARGA DE DEDUCCIONES</th>
          <th colspan="5" class="header-group calc-ded-group" title="Deducciones de ley y retenciones: Resultados de SSO + RPE + FAOV + Inasistencias + Total Deducciones.">DEDUCCIONES CALCULADAS</th>
          <th rowspan="2" class="header-neto" title="Neto a Pagar: Salario líquido final a cobrar por el trabajador (Total Asignaciones Brutas - Total Deducciones).">NETO</th>
          <th rowspan="2" title="Recibo PDF: Generación e impresión del comprobante oficial de pago individual en PDF.">PDF</th>
          <th rowspan="2" title="Acciones de control: Guardar y recalcular la fila, o remover al trabajador del pago del periodo.">ACCIONES</th>
        </tr>
        <tr>
          <!-- Carga Asignaciones (5) -->
          <th title="Días Trabajados: Días laborados en el periodo.">Días</th>
          <th title="Horas Nocturnas: Laboradas en horario nocturno [(Salario Diario / 7.5) × 30% × Horas].">H.Noct</th>
          <th title="Horas Extra: Horas extraordinarias laboradas [((Salario Diario × 1.5) / 7.5) × Horas].">H.Extra</th>
          <th title="Días Feriados: Días festivos laborados [Salario Diario × 1.5 × Días].">Feriad.</th>
          <th title="Otras Asignaciones: Bonos extraordinarios agregados directamente al Total Bruto.">O.Asig</th>

          <!-- Asignaciones Calculadas (5) -->
          <th title="Sueldo Base: Monto ganado por días laborados (Salario Diario × Días).">S.Base</th>
          <th title="Bono Nocturno: Monto calculado por horas nocturnas.">B.Noct</th>
          <th title="Horas Extra: Monto acumulado por horas extraordinarias.">H.Extra</th>
          <th title="Feriados: Monto acumulado por días feriados laborados.">Feriad.</th>
          <th title="Total Asignaciones Brutas: Suma de todas las asignaciones ganadas antes de deducciones.">Tot.Bruto</th>

          <!-- Carga Deducciones Adicionales (3) -->
          <th title="Días de Inasistencia: Faltas injustificadas descontadas de las deducciones.">Inas.</th>
          <th title="Préstamos: Amortización de préstamos o anticipos de sueldo.">Prést.</th>
          <th title="ISLR: Retención directa del Impuesto sobre la Renta del trabajador.">ISLR</th>

          <!-- Deducciones Calculadas (5) -->
          <th title="SSO (<?= $ssoText ?>%): Seguro Social Obligatorio (Base con topes legales de 1 a 5 Salarios Mínimos)." class="th-tax-col" data-tax="sso" onclick="abrirModalTasasLey('sso')" style="cursor:pointer;">SSO (<?= $ssoText ?>%)</th>
          <th title="RPE (<?= $rpeText ?>%): Régimen Prestacional de Empleo / Paro Forzoso." class="th-tax-col" data-tax="rpe" onclick="abrirModalTasasLey('rpe')" style="cursor:pointer;">RPE (<?= $rpeText ?>%)</th>
          <th title="FAOV (<?= $lphText ?>%): Fondo de Ahorro Obligatorio para la Vivienda (1% de ley)." class="th-tax-col" data-tax="lph" onclick="abrirModalTasasLey('lph')" style="cursor:pointer;">FAOV (<?= $lphText ?>%)</th>
          <th title="Descuento por Inasistencia: Salario Diario × Días Inasistencia.">Inasist.</th>
          <th title="Total Deducciones: Suma total de retenciones de ley, préstamos, inasistencias e ISLR.">Tot.Deduc.</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($detallesList as $d):
          $empId = $d['id_empleado'];
          $id_detalle = $d['id_detalle'];
          $pNombre = $d['primer_nombre'] ?? '';
          $pApellido = $d['primer_apellido'] ?? '';
          $iniciales = strtoupper(substr($pNombre, 0, 1) . substr($pApellido, 0, 1));
          $nombreEmpleado = trim(preg_replace('/\s+/', ' ', implode(' ', array_filter([$pNombre, $d['segundo_nombre'] ?? '', $pApellido, $d['segundo_apellido'] ?? '']))));

          $bono_noct_qty = $nominaModel->getAsignacionCantidad($id_detalle, 2);
          $horas_extra_qty = $nominaModel->getAsignacionCantidad($id_detalle, 3);
          $feriados_qty = $nominaModel->getAsignacionCantidad($id_detalle, 4);

          $bono_noct_monto = $nominaModel->getAsignacionMonto($id_detalle, 2);
          $horas_extra_monto = $nominaModel->getAsignacionMonto($id_detalle, 3);
          $feriados_monto = $nominaModel->getAsignacionMonto($id_detalle, 4);

          $prestamo_monto = $nominaModel->getDeduccionMonto($id_detalle, 4);
          $sso_monto = $nominaModel->getDeduccionMonto($id_detalle, 1);
          $rpe_monto = $nominaModel->getDeduccionMonto($id_detalle, 2);
          $lph_monto = $nominaModel->getDeduccionMonto($id_detalle, 3);
          $inasist_monto = $nominaModel->getDeduccionMonto($id_detalle, 5);
          $islr_monto = $nominaModel->getDeduccionMonto($id_detalle, 6);
        ?>
        <tr class="nomina-row"
            id="row_<?= $empId ?>"
            data-emp-id="<?= $empId ?>"
            data-sueldo-diario="<?= (float)$d['sueldo_diario'] ?>"
            data-sueldo-mensual="<?= (float)($d['sueldo_divisa'] ?? 0) ?>">
            <td class="emp-cell sticky-col">
              <form id="form_row_<?= $empId ?>" method="post" action="index.php?r=nomina/detalle" style="display:none;">
                <?= Security::csrfField() ?>
                <input type="hidden" name="id_nomina" value="<?= $nomina['id_nomina'] ?>">
                <input type="hidden" name="id_empleado" value="<?= $empId ?>">
                <input type="hidden" name="action" value="guardar_detalle">
              </form>
              <div class="emp-avatar-wrapper">
                <?php 
                  $fotoSrcMatrix = '';
                  if (!empty($d['foto'])) {
                    if (strpos($d['foto'], 'data:image/') === 0) {
                      $fotoSrcMatrix = $d['foto'];
                    } elseif (file_exists(__DIR__ . '/../../assets/images/empleados/' . $d['foto'])) {
                      $fotoSrcMatrix = 'assets/images/empleados/' . $d['foto'];
                    }
                  }
                ?>
                <?php if ($fotoSrcMatrix): ?>
                  <img src="<?= htmlspecialchars($fotoSrcMatrix) ?>" class="emp-avatar-img" alt="Foto">
                <?php else: ?>
                  <span class="emp-avatar"><?= $iniciales ?></span>
                <?php endif; ?>
                <div>
                  <strong class="emp-name"><?= htmlspecialchars($nombreEmpleado) ?></strong>
                  <div class="sub-text">C.I. <?= htmlspecialchars($d['cedula']) ?></div>
                  <div class="sub-text cargo-tag"><?= htmlspecialchars($d['nombre_cargo'] ?? 'General') ?></div>
                </div>
              </div>
            </td>

            <td class="num-cell bold-text">Bs. <?= number_format((float)$d['sueldo_diario'], 2, ',', '.') ?></td>

            <!-- Carga Asignaciones (5) -->
            <td class="input-cell"><input type="number" step="1" min="0" max="31" name="dias_trabajados" class="calc-input" value="<?= (float)($d['dias_trabajados'] ?? 0) ?>" form="form_row_<?= $empId ?>" onfocus="if(this.value==='0') this.value=''; this.select();" onblur="if(this.value==='') this.value='0';"></td>
            <td class="input-cell"><input type="number" step="0.1" min="0" max="200" name="bono_nocturno_qty" class="calc-input" value="<?= (float)$bono_noct_qty ?>" form="form_row_<?= $empId ?>" onfocus="if(this.value==='0') this.value=''; this.select();" onblur="if(this.value==='') this.value='0';"></td>
            <td class="input-cell"><input type="number" step="0.1" min="0" max="200" name="horas_extra_qty" class="calc-input" value="<?= (float)$horas_extra_qty ?>" form="form_row_<?= $empId ?>" onfocus="if(this.value==='0') this.value=''; this.select();" onblur="if(this.value==='') this.value='0';"></td>
            <td class="input-cell"><input type="number" step="0.5" min="0" max="15" name="feriados_qty" class="calc-input" value="<?= (float)$feriados_qty ?>" form="form_row_<?= $empId ?>" onfocus="if(this.value==='0') this.value=''; this.select();" onblur="if(this.value==='') this.value='0';"></td>
            <td class="input-cell"><input type="number" step="0.01" min="0" name="otras_asignaciones" class="calc-input" value="0" form="form_row_<?= $empId ?>" onfocus="if(this.value==='0') this.value=''; this.select();" onblur="if(this.value==='') this.value='0';"></td>

            <!-- Asignaciones Calculadas (5) -->
            <td class="num-cell cell-asig cell-sueldo-base">Bs. <?= number_format((float)$d['sueldo_bss'], 2, ',', '.') ?></td>
            <td class="num-cell cell-asig cell-bono-noct">Bs. <?= number_format((float)$bono_noct_monto, 2, ',', '.') ?></td>
            <td class="num-cell cell-asig cell-horas-extra">Bs. <?= number_format((float)$horas_extra_monto, 2, ',', '.') ?></td>
            <td class="num-cell cell-asig cell-feriados">Bs. <?= number_format((float)$feriados_monto, 2, ',', '.') ?></td>
            <td class="num-cell cell-asig total-asig cell-tot-bruto" data-raw="<?= (float)$d['total_asignaciones'] ?>">Bs. <?= number_format((float)$d['total_asignaciones'], 2, ',', '.') ?></td>

            <!-- Carga Deducciones Adicionales (3) -->
            <td class="input-cell"><input type="number" step="1" min="0" max="31" name="dias_inasistencia" class="calc-input" value="<?= (float)($d['dias_inasistencia'] ?? 0) ?>" form="form_row_<?= $empId ?>" onfocus="if(this.value==='0') this.value=''; this.select();" onblur="if(this.value==='') this.value='0';"></td>
            <td class="input-cell"><input type="number" step="0.01" min="0" name="prestamo" class="calc-input" value="<?= (float)$prestamo_monto ?>" form="form_row_<?= $empId ?>" onfocus="if(this.value==='0') this.value=''; this.select();" onblur="if(this.value==='') this.value='0';"></td>
            <td class="input-cell"><input type="number" step="0.01" min="0" name="islr" class="calc-input" value="<?= (float)$islr_monto ?>" form="form_row_<?= $empId ?>" onfocus="if(this.value==='0') this.value=''; this.select();" onblur="if(this.value==='') this.value='0';"></td>

            <!-- Deducciones Calculadas (5) -->
            <td class="num-cell cell-ded cell-sso">Bs. <?= number_format((float)$sso_monto, 2, ',', '.') ?></td>
            <td class="num-cell cell-ded cell-rpe">Bs. <?= number_format((float)$rpe_monto, 2, ',', '.') ?></td>
            <td class="num-cell cell-ded cell-lph">Bs. <?= number_format((float)$lph_monto, 2, ',', '.') ?></td>
            <td class="num-cell cell-ded cell-inasist">Bs. <?= number_format((float)$inasist_monto, 2, ',', '.') ?></td>
            <td class="num-cell cell-ded total-ded cell-tot-deduc">Bs. <?= number_format((float)$d['total_deducciones'], 2, ',', '.') ?></td>

            <!-- Neto a pagar cell -->
            <td class="neto-cell cell-neto" data-raw="<?= (float)$d['neto_pagar'] ?>">Bs. <?= number_format((float)$d['neto_pagar'], 2, ',', '.') ?></td>

            <!-- Recibo PDF Individual -->
            <td style="text-align: center;">
              <a class="btn-pdf" href="index.php?r=nomina/recibo&id_nomina=<?= $nomina['id_nomina'] ?>&id_empleado=<?= $empId ?>" target="_blank" title="Ver e Imprimir Comprobante de Pago">
                <i class="fa-solid fa-file-invoice-dollar" style="margin-right:4px;"></i> Recibo
              </a>
            </td>

            <!-- Action buttons -->
            <td style="text-align: center; white-space: nowrap;">
              <button type="submit" class="btn-row-action btn-save" id="btn_save_<?= $empId ?>" title="Guardar y recalcular asignaciones, deducciones y neto del trabajador" form="form_row_<?= $empId ?>"><i class="fa-solid fa-floppy-disk" style="color:var(--primary);"></i></button>
              <form method="post" action="index.php?r=nomina/detalle" style="display:inline;" onsubmit="return confirm('¿Quitar a este trabajador de esta nómina?');">
                <?= Security::csrfField() ?>
                <input type="hidden" name="id_nomina" value="<?= $nomina['id_nomina'] ?>">
                <input type="hidden" name="id_empleado" value="<?= $empId ?>">
                <input type="hidden" name="action" value="eliminar_trabajador">
                <button type="submit" class="btn-row-action btn-del" title="Excluir temporalmente a este trabajador de esta nómina"><i class="fa-solid fa-trash-can" style="color:var(--danger);"></i></button>
              </form>
            </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<!-- MODAL PARA SELECCIONAR TRABAJADORES -->
<div id="modalTrabajadores" class="modal-backdrop" style="display:none;">
  <div class="modal-content">
    <div class="modal-header">
      <h3><i class="fa-solid fa-users-gear" style="margin-right:8px; color:var(--primary);"></i> Seleccionar Trabajadores para Pago de Nómina</h3>
      <span class="modal-close" onclick="cerrarModalTrabajadores()">&times;</span>
    </div>
    <form method="post" action="index.php?r=nomina/detalle">
      <?= Security::csrfField() ?>
      <input type="hidden" name="id_nomina" value="<?= $nomina['id_nomina'] ?>">
      <input type="hidden" name="action" value="agregar_trabajadores">

      <div style="margin-bottom:14px; display:flex; gap:10px;">
        <button type="button" class="btn small secondary" onclick="marcarTodos(true)"><i class="fa-solid fa-square-check"></i> Seleccionar Todos</button>
        <button type="button" class="btn small secondary" onclick="marcarTodos(false)"><i class="fa-regular fa-square"></i> Desmarcar Todos</button>
      </div>

      <div class="modal-body-scroll">
        <table class="modal-table">
          <thead>
            <tr>
              <th style="width:50px; text-align:center;">Incluir</th>
              <th>Cédula</th>
              <th>Trabajador</th>
              <th>Cargo</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($empleadosActivos as $emp):
              $empNombre = trim(preg_replace('/\s+/', ' ', implode(' ', array_filter([$emp['primer_nombre'], $emp['segundo_nombre'] ?? '', $emp['primer_apellido'], $emp['segundo_apellido'] ?? '']))));
              $estaIncluido = isset($detalles[$emp['id_empleado']]);
            ?>
            <tr>
              <td style="text-align:center;">
                <input type="checkbox" name="id_empleados[]" value="<?= $emp['id_empleado'] ?>" class="chk-empleado" <?= $estaIncluido ? 'checked' : '' ?> style="width:18px; height:18px; cursor:pointer;">
              </td>
              <td><?= htmlspecialchars($emp['cedula']) ?></td>
              <td><strong><?= htmlspecialchars($empNombre) ?></strong></td>
              <td><span class="badge active" style="background:#eef7f3; color:var(--primary-dark);"><?= htmlspecialchars($emp['nombre_cargo'] ?? 'General') ?></span></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn secondary" onclick="cerrarModalTrabajadores()">Cancelar</button>
        <button type="submit" class="btn btn-hero-cta" style="padding:10px 20px;"><i class="fa-solid fa-check"></i> Guardar Selección de Trabajadores</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL INFORMATIVO DE TASAS DE LEY A NIVEL DE EMPRESA -->
<div id="modalTasasLey" class="modal-backdrop" style="display:none;">
  <div class="modal-content modal-tasas-content" style="max-width:620px;">
    <div class="modal-header">
      <div style="display:flex; align-items:center; gap:12px;">
        <div class="modal-icon-badge"><i class="fa-solid fa-building-shield"></i></div>
        <div>
          <h3 style="margin:0; font-size:17px;">Tasas Legales de la Empresa</h3>
          <span style="font-size:12px; color:#64748b;">Régimen legal unificado aplicable a todos los trabajadores</span>
        </div>
      </div>
      <span class="modal-close" onclick="cerrarModalTasasLey()">&times;</span>
    </div>

    <div style="padding: 20px 24px;">
      <div class="tasas-notice" style="margin-bottom:18px;">
        <i class="fa-solid fa-circle-info" style="font-size:18px; margin-top:2px;"></i>
        <div>
          <strong>Configuración Centralizada de la Empresa:</strong> Las deducciones de SSO, RPE y FAOV no se configuran por trabajador ni de forma aislada. Se establecen a nivel general de la empresa según su <strong>régimen de riesgo</strong> y afectan a todos los trabajadores automáticamente considerando los límites legales de cotización (1 a 5 Salarios Mínimos).
        </div>
      </div>

      <div class="tasas-cards-grid" style="grid-template-columns: 1fr; gap:12px;">
        <div class="tasa-edit-card" style="padding:14px 16px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div style="display:flex; align-items:center; gap:8px;">
              <i class="fa-solid fa-shield-heart" style="color:#0284c7; font-size:18px;"></i>
              <strong>Seguro Social Obligatorio (SSO)</strong>
            </div>
            <span class="badge" style="background:#e0f2fe; color:#0369a1; font-weight:700; font-size:12px; padding:4px 10px; border-radius:12px;">
              Trabajador: <?= $ssoText ?>% | Patrono: <?= number_format($ssoPatronoRaw * 100, 2, ',', '.') ?>%
            </span>
          </div>
          <div style="font-size:12px; color:#64748b;">
            Base de cálculo: Acotada entre 1 SM (Bs. <?= number_format($salarioMinimoConfig, 2, ',', '.') ?>) y 5 SM (Bs. <?= number_format($salarioMinimoConfig * 5, 2, ',', '.') ?>). Ponderado por semanas cotizables.
          </div>
        </div>

        <div class="tasa-edit-card" style="padding:14px 16px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div style="display:flex; align-items:center; gap:8px;">
              <i class="fa-solid fa-handshake-angle" style="color:#8b5cf6; font-size:18px;"></i>
              <strong>Régimen Prestacional de Empleo (RPE / Paro Forzoso)</strong>
            </div>
            <span class="badge" style="background:#ede9fe; color:#6d28d9; font-weight:700; font-size:12px; padding:4px 10px; border-radius:12px;">
              Trabajador: <?= $rpeText ?>% | Patrono: <?= number_format($rpePatronoRaw * 100, 2, ',', '.') ?>%
            </span>
          </div>
          <div style="font-size:12px; color:#64748b;">
            Base de cálculo: Acotada entre 1 SM y 5 SM según el nivel de riesgo de la empresa.
          </div>
        </div>

        <div class="tasa-edit-card" style="padding:14px 16px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div style="display:flex; align-items:center; gap:8px;">
              <i class="fa-solid fa-house-chimney-user" style="color:#0d9488; font-size:18px;"></i>
              <strong>Fondo de Ahorro Obligatorio para la Vivienda (FAOV / BANAVIH)</strong>
            </div>
            <span class="badge" style="background:#ccfbf1; color:#0f766e; font-weight:700; font-size:12px; padding:4px 10px; border-radius:12px;">
              Trabajador: <?= $lphText ?>% | Patrono: <?= number_format($lphPatronoRaw * 100, 2, ',', '.') ?>%
            </span>
          </div>
          <div style="font-size:12px; color:#64748b;">
            Base de cálculo: 1% sobre asignaciones brutas devengadas (mínimo 1 Salario Mínimo Legal).
          </div>
        </div>
      </div>

      <div style="margin-top:18px; padding:12px 14px; background:var(--bg-card, #f8fafc); border-radius:8px; border:1px solid var(--border-color, #e2e8f0); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
        <span style="font-size:13px; color:#475569;">
          <i class="fa-solid fa-circle-check" style="color:#10b981; margin-right:4px;"></i>
          Régimen activo: <strong><?= htmlspecialchars($regimenNombre) ?></strong>
        </span>
        <a href="index.php?r=configuracion" class="btn btn-hero-cta" style="padding:8px 18px; font-size:13px; text-decoration:none;">
          <i class="fa-solid fa-sliders" style="margin-right:6px;"></i> Ir a Configuración General
        </a>
      </div>
    </div>

    <div class="modal-footer" style="padding:14px 24px;">
      <button type="button" class="btn secondary" onclick="cerrarModalTasasLey()">Cerrar</button>
    </div>
  </div>
</div>

<!-- FLOATING TOOLTIP ELEMENT -->
<div id="taxFloatingTooltip" class="tax-floating-tooltip" style="display:none;" role="tooltip"></div>

<script>
function abrirModalTrabajadores() {
  document.getElementById('modalTrabajadores').style.display = 'flex';
}
function cerrarModalTrabajadores() {
  document.getElementById('modalTrabajadores').style.display = 'none';
}
function marcarTodos(estado) {
  var checkboxes = document.querySelectorAll('.chk-empleado');
  checkboxes.forEach(function(chk) { chk.checked = estado; });
}

// Tasas de Ley: Tooltips interactivos y Modal Informativo
const taxData = {
  sso: {
    title: 'Seguro Social Obligatorio (SSO)',
    badgeClass: 'sso',
    badgeText: 'SSO <?= $ssoText ?>%',
    formula: '(Base Cotizable [1 a 5 SM] × Tasa %) × (Semanas IVSS ÷ 52 × 12 / 30)',
    funcion: 'Configurado a nivel de empresa según régimen de riesgo. Se calcula sobre el sueldo acotado entre 1 SM (mínimo) y 5 SM (máximo legal Art. 107 RGLSS).',
    efecto: 'Deduce obligatoriamente el <strong><?= $ssoText ?>%</strong> del salario base del trabajador para el IVSS. Garantiza asistencia médica, cobertura de reposos y derecho a la pensión de vejez.'
  },
  rpe: {
    title: 'Régimen Prestacional de Empleo (RPE / Paro Forzoso)',
    badgeClass: 'rpe',
    badgeText: 'RPE <?= $rpeText ?>%',
    formula: '(Base Cotizable [1 a 5 SM] × Tasa %) × (Semanas IVSS ÷ 52 × 12 / 30)',
    funcion: 'Configurado a nivel de empresa según régimen de riesgo (Clase I: 0.5%, Clase II: 0.5%, Clase III: 0.5% trabajador). Tope máximo: 5 salarios mínimos.',
    efecto: 'Retiene el <strong><?= $rpeText ?>%</strong> del salario para el fondo solidario de cesantía/paro forzoso de todos los empleados de la empresa.'
  },
  lph: {
    title: 'Fondo de Ahorro Obligatorio para la Vivienda (FAOV)',
    badgeClass: 'lph',
    badgeText: 'FAOV <?= $lphText ?>%',
    formula: 'Total Asignaciones Devengadas × Tasa 1%',
    funcion: 'Aporte obligatorio fijado en 1% para el trabajador y 2% para el patrono sobre la totalidad de ingresos brutos devengados.',
    efecto: 'Descuenta el <strong><?= $lphText ?>%</strong> de las asignaciones brutas devengadas para la cuenta de ahorro habitacional del trabajador en BANAVIH.'
  }
};

const tooltipEl = document.getElementById('taxFloatingTooltip');
let tooltipHideTimeout = null;

document.querySelectorAll('.th-tax-col[data-tax]').forEach(function(el) {
  el.addEventListener('mouseenter', function() {
    const taxKey = el.getAttribute('data-tax');
    const data = taxData[taxKey];
    if (!data) return;

    clearTimeout(tooltipHideTimeout);

    tooltipEl.innerHTML = `
      <div class="tax-tt-head">
        <div class="tax-tt-title">
          <i class="fa-solid fa-scale-balanced" style="color:var(--primary);"></i>
          <span>${data.title}</span>
        </div>
        <span class="tax-tt-badge ${data.badgeClass}">${data.badgeText}</span>
      </div>
      <div class="tax-tt-block">
        <div class="tax-tt-label"><i class="fa-solid fa-calculator"></i> ¿Cómo funciona?</div>
        <div class="tax-tt-effect">${data.funcion}</div>
        <div class="tax-tt-formula">${data.formula}</div>
      </div>
      <div class="tax-tt-block" style="margin-bottom:0;">
        <div class="tax-tt-label"><i class="fa-solid fa-arrow-trend-down"></i> ¿Cuál es su efecto?</div>
        <div class="tax-tt-effect">${data.efecto}</div>
      </div>
      <div class="tax-tt-footer">
        <i class="fa-solid fa-building-gear"></i>
        <span>Parámetro corporativo &bull; Clic para ver régimen</span>
      </div>
    `;

    const rect = el.getBoundingClientRect();
    const tooltipWidth = 320;
    let left = rect.left + (rect.width / 2) - (tooltipWidth / 2);
    
    if (left < 10) left = 10;
    if (left + tooltipWidth > window.innerWidth - 10) {
      left = window.innerWidth - tooltipWidth - 10;
    }

    tooltipEl.style.left = left + 'px';
    tooltipEl.style.top = (rect.bottom + 8) + 'px';
    tooltipEl.style.display = 'block';

    requestAnimationFrame(function() {
      tooltipEl.classList.add('active');
    });
  });

  el.addEventListener('mouseleave', function() {
    tooltipEl.classList.remove('active');
    tooltipHideTimeout = setTimeout(function() {
      if (!tooltipEl.classList.contains('active')) {
        tooltipEl.style.display = 'none';
      }
    }, 180);
  });
});

function abrirModalTasasLey(foco = '') {
  if (tooltipEl) {
    tooltipEl.classList.remove('active');
    tooltipEl.style.display = 'none';
  }
  const modal = document.getElementById('modalTasasLey');
  if (modal) {
    modal.style.display = 'flex';
  }
}

function cerrarModalTasasLey() {
  const modal = document.getElementById('modalTasasLey');
  if (modal) {
    modal.style.display = 'none';
  }
}

window.addEventListener('click', function(e) {
  const modalTasas = document.getElementById('modalTasasLey');
  if (e.target === modalTasas) {
    cerrarModalTasasLey();
  }
  const modalTrab = document.getElementById('modalTrabajadores');
  if (e.target === modalTrab) {
    cerrarModalTrabajadores();
  }
});

// ═══════════════ CÁLCULO REACTIVO AUTOMÁTICO EN VIVO (NETO Y TOTALES) ═══════════════
const NOMINA_CONFIG = {
  salarioMinimo: <?= (float)($empConfig['salario_minimo_legal'] ?? 130.00) ?>,
  topeSso: <?= (int)($empConfig['tope_sso_salarios'] ?? 5) ?>,
  topeRpe: <?= (int)($empConfig['tope_rpe_salarios'] ?? 5) ?>,
  tasaSso: <?= (float)($empConfig['tasa_sso_trabajador'] ?? 0.04) ?>,
  tasaRpe: <?= (float)($empConfig['tasa_rpe_trabajador'] ?? 0.005) ?>,
  tasaLph: <?= (float)($empConfig['tasa_faov_trabajador'] ?? 0.01) ?>,
  semanasPeriodo: <?= (float)($nomina['semanas_descontar_ivss'] ?? 2.0) ?>,
  factorSemanasMes: <?= (float)($nomina['factor_semanas_mes'] ?? 4.333333) ?>,
  prorrateo: <?= ((float)($nomina['semanas_descontar_ivss'] ?? 2.0) > 0 ? (float)($nomina['semanas_descontar_ivss'] ?? 2.0) : 2.0) / ((float)($nomina['factor_semanas_mes'] ?? 4.333333) > 0 ? (float)($nomina['factor_semanas_mes'] ?? 4.333333) : 4.333333) ?>
};

function formatBs(val) {
  const parts = Number(val).toFixed(2).split('.');
  parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  return parts.join(',');
}

function recalcularFila(row) {
  const sueldoDiario = parseFloat(row.dataset.sueldoDiario) || 0;
  const sueldoMensual = parseFloat(row.dataset.sueldoMensual) || 0;

  const diasTrab = parseFloat(row.querySelector('input[name="dias_trabajados"]')?.value) || 0;
  const bonoNoctQty = parseFloat(row.querySelector('input[name="bono_nocturno_qty"]')?.value) || 0;
  const horasExtraQty = parseFloat(row.querySelector('input[name="horas_extra_qty"]')?.value) || 0;
  const feriadosQty = parseFloat(row.querySelector('input[name="feriados_qty"]')?.value) || 0;
  const otrasAsig = parseFloat(row.querySelector('input[name="otras_asignaciones"]')?.value) || 0;

  const diasInasist = parseFloat(row.querySelector('input[name="dias_inasistencia"]')?.value) || 0;
  const prestamo = parseFloat(row.querySelector('input[name="prestamo"]')?.value) || 0;
  const islr = parseFloat(row.querySelector('input[name="islr"]')?.value) || 0;

  // 1. Asignaciones
  const sueldoBss = Math.round((sueldoDiario * diasTrab) * 100) / 100;
  const bonoNoctMonto = Math.round(((sueldoDiario / 7.5) * 0.3 * bonoNoctQty) * 100) / 100;
  const horasExtraMonto = Math.round((((sueldoDiario * 1.5) / 7.5) * horasExtraQty) * 100) / 100;
  const feriadosMonto = Math.round(((sueldoDiario * 1.5) * feriadosQty) * 100) / 100;
  const totalAsignaciones = Math.round((sueldoBss + bonoNoctMonto + horasExtraMonto + feriadosMonto + otrasAsig) * 100) / 100;

  // 2. Deducciones
  let sso = 0;
  let rpe = 0;
  if (diasTrab > 0) {
    const baseCotizableSso = Math.max(NOMINA_CONFIG.salarioMinimo, Math.min(sueldoMensual, NOMINA_CONFIG.topeSso * NOMINA_CONFIG.salarioMinimo));
    const baseCotizableRpe = Math.max(NOMINA_CONFIG.salarioMinimo, Math.min(sueldoMensual, NOMINA_CONFIG.topeRpe * NOMINA_CONFIG.salarioMinimo));
    sso = Math.round((NOMINA_CONFIG.tasaSso * baseCotizableSso * NOMINA_CONFIG.prorrateo) * 100) / 100;
    rpe = Math.round((NOMINA_CONFIG.tasaRpe * baseCotizableRpe * NOMINA_CONFIG.prorrateo) * 100) / 100;
  }
  const lph = Math.round((NOMINA_CONFIG.tasaLph * Math.max(NOMINA_CONFIG.salarioMinimo, totalAsignaciones)) * 100) / 100;
  const inasistMonto = Math.round((sueldoDiario * diasInasist) * 100) / 100;
  const totalDeducciones = Math.round((sso + rpe + lph + prestamo + inasistMonto + islr) * 100) / 100;

  // 3. Neto
  const netoPagar = Math.round((totalAsignaciones - totalDeducciones) * 100) / 100;

  // Update DOM cells
  const elSueldoBase = row.querySelector('.cell-sueldo-base');
  const elBonoNoct = row.querySelector('.cell-bono-noct');
  const elHorasExtra = row.querySelector('.cell-horas-extra');
  const elFeriados = row.querySelector('.cell-feriados');
  const elTotBruto = row.querySelector('.cell-tot-bruto');

  const elSso = row.querySelector('.cell-sso');
  const elRpe = row.querySelector('.cell-rpe');
  const elLph = row.querySelector('.cell-lph');
  const elInasist = row.querySelector('.cell-inasist');
  const elTotDeduc = row.querySelector('.cell-tot-deduc');
  const elNeto = row.querySelector('.cell-neto');

  if (elSueldoBase) elSueldoBase.textContent = 'Bs. ' + formatBs(sueldoBss);
  if (elBonoNoct) elBonoNoct.textContent = 'Bs. ' + formatBs(bonoNoctMonto);
  if (elHorasExtra) elHorasExtra.textContent = 'Bs. ' + formatBs(horasExtraMonto);
  if (elFeriados) elFeriados.textContent = 'Bs. ' + formatBs(feriadosMonto);
  if (elTotBruto) {
    elTotBruto.textContent = 'Bs. ' + formatBs(totalAsignaciones);
    elTotBruto.dataset.raw = totalAsignaciones;
  }

  if (elSso) elSso.textContent = 'Bs. ' + formatBs(sso);
  if (elRpe) elRpe.textContent = 'Bs. ' + formatBs(rpe);
  if (elLph) elLph.textContent = 'Bs. ' + formatBs(lph);
  if (elInasist) elInasist.textContent = 'Bs. ' + formatBs(inasistMonto);
  if (elTotDeduc) elTotDeduc.textContent = 'Bs. ' + formatBs(totalDeducciones);

  if (elNeto) {
    elNeto.textContent = 'Bs. ' + formatBs(netoPagar);
    elNeto.dataset.raw = netoPagar;
    elNeto.classList.remove('neto-pulse');
    void elNeto.offsetWidth;
    elNeto.classList.add('neto-pulse');
  }

  recalcularTotalGeneral();
}

function recalcularTotalGeneral() {
  let sumBruto = 0;
  let sumNeto = 0;

  document.querySelectorAll('.nomina-row').forEach(row => {
    const b = parseFloat(row.querySelector('.cell-tot-bruto')?.dataset.raw);
    const n = parseFloat(row.querySelector('.cell-neto')?.dataset.raw);

    if (!isNaN(b)) sumBruto += b;
    if (!isNaN(n)) sumNeto += n;
  });

  const mBruto = document.getElementById('metricTotalBruto');
  const mNeto = document.getElementById('metricTotalNeto');

  if (mBruto) mBruto.textContent = 'Bs. ' + formatBs(sumBruto);
  if (mNeto) mNeto.textContent = 'Bs. ' + formatBs(sumNeto);
}

const saveTimers = {};

function autoGuardarFila(row) {
  const empId = row.dataset.empId;
  const form = document.getElementById('form_row_' + empId);
  const btnSave = document.getElementById('btn_save_' + empId);
  if (!form) return;

  const formData = new FormData(form);

  row.querySelectorAll('.calc-input').forEach(inp => {
    formData.append(inp.name, inp.value);
  });
  formData.append('ajax', '1');

  if (btnSave) {
    btnSave.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="color:var(--primary);"></i>';
  }

  fetch('index.php?r=nomina/detalle', {
    method: 'POST',
    body: formData,
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(res => res.json())
  .then(data => {
    if (btnSave) {
      btnSave.innerHTML = '<i class="fa-solid fa-check" style="color:var(--ok, #10b981);"></i>';
      setTimeout(() => {
        btnSave.innerHTML = '<i class="fa-solid fa-floppy-disk" style="color:var(--primary);"></i>';
      }, 1200);
    }
  })
  .catch(err => {
    console.error('Error al guardar fila:', err);
    if (btnSave) {
      btnSave.innerHTML = '<i class="fa-solid fa-floppy-disk" style="color:var(--primary);"></i>';
    }
  });
}

document.querySelectorAll('.nomina-row').forEach(row => {
  const empId = row.dataset.empId;
  row.querySelectorAll('.calc-input').forEach(inp => {
    inp.addEventListener('input', function() {
      recalcularFila(row);

      clearTimeout(saveTimers[empId]);
      saveTimers[empId] = setTimeout(() => {
        autoGuardarFila(row);
      }, 700);
    });

    inp.addEventListener('blur', function() {
      clearTimeout(saveTimers[empId]);
      autoGuardarFila(row);
    });
  });
});

document.querySelectorAll('.btn-save').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    const row = this.closest('.nomina-row');
    if (row) {
      recalcularFila(row);
      autoGuardarFila(row);
    }
  });
});
</script>

<style>
@keyframes pulseNetoGreen {
  0% { transform: scale(1); background: rgba(16, 185, 129, 0.22); }
  50% { transform: scale(1.06); background: rgba(16, 185, 129, 0.38); }
  100% { transform: scale(1); }
}
.neto-pulse {
  animation: pulseNetoGreen 0.35s ease-out;
  border-radius: 6px;
}
</style>
