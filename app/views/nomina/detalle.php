<?php
$totalTrabajadores = count($detallesList);
$totalBrutoAcumulado = 0;
$totalNetoAcumulado = 0;
foreach ($detallesList as $d) {
    $totalBrutoAcumulado += (float)$d['total_asignaciones'];
    $totalNetoAcumulado += (float)$d['neto_pagar'];
}
$descPeriodo = !empty($periodo['descripcion']) ? $periodo['descripcion'] : $periodo['tipo_periodo'];
?>

<!-- BREADCRUMB & TOOLBAR -->
<div class="view-header-bar">
  <div class="breadcrumb-trail">
    <a href="index.php?r=periodos" class="btn-link-back"><i class="fa-solid fa-arrow-left"></i> Volver a Periodos</a>
    <span class="sep">/</span>
    <span class="current">Carga de Nómina</span>
  </div>
  <div class="header-actions">
    <a class="btn secondary small" href="index.php?r=nomina/ver&id_nomina=<?= $nomina['id_nomina'] ?>"><i class="fa-solid fa-chart-pie"></i> Ver Totales / Exportar</a>
    <a class="btn secondary small" href="index.php?r=nomina/recibo&id_nomina=<?= $nomina['id_nomina'] ?>" target="_blank"><i class="fa-solid fa-print"></i> Imprimir Recibos</a>
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
    <div class="param-pill">
      <span class="lbl">Salario Mínimo</span>
      <span class="val">Bs. <?= number_format((float)$nomina['salario_minimo_vigente'], 2, ',', '.') ?></span>
    </div>
    <div class="param-pill">
      <span class="lbl">Semanas IVSS</span>
      <span class="val"><?= $nomina['semanas_descontar_ivss'] ?></span>
    </div>
    <div class="param-pill">
      <span class="lbl">Tasas Ley</span>
      <span class="val">SSO <?= number_format((float)(($nomina['tasa_sso'] ?? 0.04) * 100), 1, ',', '.') ?>% | RPE <?= number_format((float)(($nomina['tasa_rpe'] ?? 0.005) * 100), 1, ',', '.') ?>% | LPH <?= number_format((float)(($nomina['tasa_lph'] ?? 0.01) * 100), 1, ',', '.') ?>%</span>
    </div>
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
      <strong class="m-val">Bs. <?= number_format($totalBrutoAcumulado, 2, ',', '.') ?></strong>
    </div>
  </div>
  <div class="metric-card metric-card-highlight">
    <div class="m-icon" style="background:rgba(255,255,255,0.2); color:#fff;"><i class="fa-solid fa-money-bill-wave"></i></div>
    <div class="m-info">
      <span class="m-label" style="color:rgba(255,255,255,0.9);">Monto Total Neto a Pagar</span>
      <strong class="m-val" style="color:#ffffff;">Bs. <?= number_format($totalNetoAcumulado, 2, ',', '.') ?></strong>
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
          <th colspan="6" class="header-group asig-group" title="Carga manual de conceptos: Ingreso de días laborados, inasistencias, horas nocturnas, horas extra y días feriados.">CARGA DE ASIGNACIONES</th>
          <th rowspan="2" title="Préstamos: Descuento directo por amortización de préstamos, anticipos de sueldo o adeudamientos.">Prést.</th>
          <th rowspan="2" title="ISLR / Retenciones: Retención directa del Impuesto sobre la Renta u otros descuentos aplicables.">ISLR</th>
          <th colspan="5" class="header-group calc-asig-group" title="Asignaciones devengadas: Resultados de Sueldo Base + Bono Nocturno + Horas Extra + Feriados + Otras Asignaciones.">ASIGNACIONES CALCULADAS</th>
          <th colspan="5" class="header-group calc-ded-group" title="Deducciones de ley y retenciones: Resultados de SSO + RPE + LPH + Inasistencias + Préstamos + ISLR.">DEDUCCIONES CALCULADAS</th>
          <th rowspan="2" class="header-neto" title="Neto a Pagar: Salario líquido final a cobrar por el trabajador (Total Asignaciones Brutas - Total Deducciones).">NETO</th>
          <th rowspan="2" title="Recibo PDF: Generación e impresión del comprobante oficial de pago individual en PDF.">PDF</th>
          <th rowspan="2" title="Acciones de control: Guardar y recalcular la fila, o remover al trabajador del pago del periodo.">ACCIONES</th>
        </tr>
        <tr>
          <!-- Carga Asignaciones -->
          <th title="Días Trabajados: Días laborados en el periodo. Multiplica el Salario Diario para generar el Sueldo Base en asignaciones.">Días</th>
          <th title="Días de Inasistencia: Faltas injustificadas. Genera un descuento de (Salario Diario × Días) en las deducciones del trabajador.">Inas.</th>
          <th title="Horas Nocturnas: Laboradas en horario nocturno. Otorga un bono recargado al 30% sobre el salario/hora ordinario [(Salario Diario / 7.5) × 30% × Horas].">H.Noct</th>
          <th title="Horas Extra: Horas extraordinarias laboradas. Se pagan al 150% del valor de la hora ordinaria [((Salario Diario × 1.5) / 7.5) × Horas].">H.Extra</th>
          <th title="Días Feriados: Días festivos laborados que se pagan al 150% del salario diario habitual [Salario Diario × 1.5 × Días].">Feriad.</th>
          <th title="Otras Asignaciones: Bonos o ingresos adicionales extraordinarios agregados directamente al Total Bruto.">O.Asig</th>

          <!-- Asignaciones Calculadas -->
          <th title="Sueldo Base: Monto ganado por los días efectivamente trabajados (Salario Diario × Días Trabajados).">S.Base</th>
          <th title="Bono Nocturno: Monto calculado por horas nocturnas [(Salario Diario / 7.5) × 30% × Horas Nocturnas].">B.Noct</th>
          <th title="Horas Extra: Monto acumulado por horas extraordinarias [((Salario Diario × 1.5) / 7.5) × Horas Extra].">H.Extra</th>
          <th title="Feriados: Monto acumulado por días feriados laborados [Salario Diario × 1.5 × Días Feriados].">Feriad.</th>
          <th title="Total Asignaciones Brutas: Suma de todas las asignaciones ganadas antes de efectuar deducciones.">Tot.Bruto</th>

          <!-- Deducciones Calculadas -->
          <th title="Seguro Social Obligatorio (4%): Retención legal calculada sobre el Salario Mínimo Vigente según semanas del periodo.">SSO (4%)</th>
          <th title="Régimen Prestacional de Empleo (0.5%): Paro Forzoso calculado sobre el Salario Mínimo Vigente según semanas del periodo.">RPE (0.5%)</th>
          <th title="Ley de Política Habitacional / FAOV (1%): Retención legal del 1% sobre el Total de Asignaciones Brutas devengadas.">LPH (1%)</th>
          <th title="Descuento por Inasistencia: Descuento acumulado por días de falta injustificada (Salario Diario × Días Inasistencia).">Inasist.</th>
          <th title="Total Deducciones: Suma total de todos los descuentos, retenciones de ley y préstamos aplicados.">Tot.Deduc.</th>
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
        <tr>
            <td class="emp-cell sticky-col">
              <form id="form_row_<?= $empId ?>" method="post" action="index.php?r=nomina/detalle" style="display:none;">
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

            <!-- Input fields con limpieza inteligente de ceros y auto-selección -->
            <td class="input-cell"><input type="number" step="1" name="dias_trabajados" value="<?= (float)($d['dias_trabajados'] ?? 0) ?>" form="form_row_<?= $empId ?>" onfocus="if(this.value==='0') this.value=''; this.select();" onblur="if(this.value==='') this.value='0';"></td>
            <td class="input-cell"><input type="number" step="1" min="0" name="dias_inasistencia" value="<?= (float)($d['dias_inasistencia'] ?? 0) ?>" form="form_row_<?= $empId ?>" onfocus="if(this.value==='0') this.value=''; this.select();" onblur="if(this.value==='') this.value='0';"></td>
            <td class="input-cell"><input type="number" step="0.1" min="0" name="bono_nocturno_qty" value="<?= (float)$bono_noct_qty ?>" form="form_row_<?= $empId ?>" onfocus="if(this.value==='0') this.value=''; this.select();" onblur="if(this.value==='') this.value='0';"></td>
            <td class="input-cell"><input type="number" step="0.1" min="0" name="horas_extra_qty" value="<?= (float)$horas_extra_qty ?>" form="form_row_<?= $empId ?>" onfocus="if(this.value==='0') this.value=''; this.select();" onblur="if(this.value==='') this.value='0';"></td>
            <td class="input-cell"><input type="number" step="0.5" min="0" name="feriados_qty" value="<?= (float)$feriados_qty ?>" form="form_row_<?= $empId ?>" onfocus="if(this.value==='0') this.value=''; this.select();" onblur="if(this.value==='') this.value='0';"></td>
            <td class="input-cell"><input type="number" step="0.01" name="otras_asignaciones" value="0" form="form_row_<?= $empId ?>" onfocus="if(this.value==='0') this.value=''; this.select();" onblur="if(this.value==='') this.value='0';"></td>
            <td class="input-cell"><input type="number" step="0.01" name="prestamo" value="<?= (float)$prestamo_monto ?>" form="form_row_<?= $empId ?>" onfocus="if(this.value==='0') this.value=''; this.select();" onblur="if(this.value==='') this.value='0';"></td>
            <td class="input-cell"><input type="number" step="0.01" name="islr" value="<?= (float)$islr_monto ?>" form="form_row_<?= $empId ?>" onfocus="if(this.value==='0') this.value=''; this.select();" onblur="if(this.value==='') this.value='0';"></td>

            <!-- Asignaciones Calculadas -->
            <td class="num-cell cell-asig">Bs. <?= number_format((float)$d['sueldo_bss'], 2, ',', '.') ?></td>
            <td class="num-cell cell-asig">Bs. <?= number_format((float)$bono_noct_monto, 2, ',', '.') ?></td>
            <td class="num-cell cell-asig">Bs. <?= number_format((float)$horas_extra_monto, 2, ',', '.') ?></td>
            <td class="num-cell cell-asig">Bs. <?= number_format((float)$feriados_monto, 2, ',', '.') ?></td>
            <td class="num-cell cell-asig total-asig">Bs. <?= number_format((float)$d['total_asignaciones'], 2, ',', '.') ?></td>

            <!-- Deducciones Calculadas -->
            <td class="num-cell cell-ded">Bs. <?= number_format((float)$sso_monto, 2, ',', '.') ?></td>
            <td class="num-cell cell-ded">Bs. <?= number_format((float)$rpe_monto, 2, ',', '.') ?></td>
            <td class="num-cell cell-ded">Bs. <?= number_format((float)$lph_monto, 2, ',', '.') ?></td>
            <td class="num-cell cell-ded">Bs. <?= number_format((float)$inasist_monto, 2, ',', '.') ?></td>
            <td class="num-cell cell-ded total-ded">Bs. <?= number_format((float)$d['total_deducciones'], 2, ',', '.') ?></td>

            <!-- Neto a pagar cell -->
            <td class="neto-cell">Bs. <?= number_format((float)$d['neto_pagar'], 2, ',', '.') ?></td>

            <!-- Recibo PDF Individual -->
            <td style="text-align: center;">
              <a class="btn-pdf" href="index.php?r=nomina/recibo&id_nomina=<?= $nomina['id_nomina'] ?>&id_empleado=<?= $empId ?>" target="_blank" title="Generar PDF del comprobante de pago individual">
                <i class="fa-solid fa-file-pdf" style="margin-right:4px;"></i> Recibo PDF
              </a>
            </td>

            <!-- Action buttons -->
            <td style="text-align: center; white-space: nowrap;">
              <button type="submit" class="btn-row-action btn-save" title="Guardar y recalcular asignaciones, deducciones y neto del trabajador" form="form_row_<?= $empId ?>"><i class="fa-solid fa-floppy-disk" style="color:var(--primary);"></i></button>
              <form method="post" action="index.php?r=nomina/detalle" style="display:inline;" onsubmit="return confirm('¿Quitar a este trabajador de esta nómina?');">
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
</script>
