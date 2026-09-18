<?php if (!empty($msg)): ?>
  <div class="alert success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<!-- Encabezado Amigable con Búsqueda y Botón Agregar (Basado en el diseño de referencia) -->
<div class="emp-header-section">
  <div class="emp-header-title-group">
    <div class="emp-header-icon-box">
      <i class="fa-solid fa-users"></i>
    </div>
    <div>
      <h2 class="emp-header-title">Gestión de Empleados</h2>
      <p class="emp-header-subtitle">Listado general de la fuerza laboral activa e inactiva</p>
    </div>
  </div>

  <div class="emp-header-controls">
    <!-- Barra de búsqueda amigable en tiempo real -->
    <div class="emp-search-wrapper">
      <i class="fa-solid fa-magnifying-glass emp-search-icon"></i>
      <input 
        type="text" 
        id="empSearchInput" 
        class="emp-search-field" 
        placeholder="Buscar empleados..." 
        autocomplete="off" 
        oninput="filterEmployees()"
        aria-label="Buscar empleados"
      >
      <button 
        type="button" 
        id="empSearchClear" 
        class="emp-search-clear" 
        onclick="clearEmployeeSearch()" 
        title="Limpiar búsqueda"
      >
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <!-- Botón Agregar Empleado (Estilo píldora azul vibrante) -->
    <a class="emp-btn-add" href="index.php?r=empleados/crear">
      <i class="fa-solid fa-user-plus"></i> Agregar Empleado
    </a>
  </div>
</div>

<!-- Tarjeta contenedora de la tabla con bordes redondeados y sombra suave -->
<div class="emp-card-container">
  <div class="emp-table-wrapper">
    <table class="emp-friendly-table" id="empTable">
      <thead>
        <tr>
          <th>CÉDULA / RIF</th>
          <th>NOMBRE COMPLETO</th>
          <th>CARGO</th>
          <th>SALARIO DIARIO</th>
          <th>ESTADO</th>
          <th style="text-align:right;">ACCIONES</th>
        </tr>
      </thead>
      <tbody id="empTableBody">
        <?php if (empty($empleados)): ?>
          <tr id="empNoDataRow">
            <td colspan="6" style="text-align:center; color:var(--text-muted); padding:40px 20px;">
              <i class="fa-solid fa-users-slash" style="font-size:32px; opacity:0.4; margin-bottom:12px; display:block;"></i>
              No hay empleados registrados en el sistema.
            </td>
          </tr>
        <?php endif; ?>

        <?php foreach ($empleados as $e): 
          $apellidos = trim(($e['primer_apellido'] ?? '') . ' ' . ($e['segundo_apellido'] ?? ''));
          $nombres = trim(($e['primer_nombre'] ?? '') . ' ' . ($e['segundo_nombre'] ?? ''));
          $nombreDisplay = $apellidos ? ($apellidos . ', ' . $nombres) : ($nombres ?: '—');
          $nombreComp = trim(preg_replace('/\s+/', ' ', implode(' ', array_filter([$e['primer_nombre'], $e['segundo_nombre'] ?? '', $e['primer_apellido'], $e['segundo_apellido'] ?? '']))));
          
          $inicial = strtoupper(substr($e['primer_nombre'] ?? 'E', 0, 1));
          $fotoSrcList = '';
          if (!empty($e['foto'])) {
            if (strpos($e['foto'], 'data:image/') === 0) {
              $fotoSrcList = $e['foto'];
            } elseif (file_exists(__DIR__ . '/../../assets/images/empleados/' . $e['foto'])) {
              $fotoSrcList = 'assets/images/empleados/' . $e['foto'];
            }
          }

          // Cédula formateada
          $cedulaVal = trim($e['cedula'] ?? '');
          $cedulaDisplay = (preg_match('/^[VEJPG]-/i', $cedulaVal)) ? strtoupper($cedulaVal) : ('V-' . $cedulaVal);

          // Salario diario en Bs y USD (sueldo_divisa es mensual / 30)
          $sueldoDivisa = (float)($e['sueldo_divisa'] ?? 0);
          $salarioDiarioUsd = $sueldoDivisa > 0 ? ($sueldoDivisa / 30) : 0;
          $tasaVal = ($tasaBcv > 0) ? (float)$tasaBcv : 36.5;
          $salarioDiarioBs = $salarioDiarioUsd * $tasaVal;

          $eJsonData = [
            'id_empleado' => $e['id_empleado'],
            'cedula' => $e['cedula'],
            'primer_nombre' => $e['primer_nombre'],
            'segundo_nombre' => $e['segundo_nombre'] ?? '',
            'primer_apellido' => $e['primer_apellido'],
            'segundo_apellido' => $e['segundo_apellido'] ?? '',
            'nombre_completo' => $nombreComp,
            'email' => $e['email'] ?? '',
            'nombre_tipo' => $e['nombre_tipo'] ?? '—',
            'nombre_jornada' => $e['nombre_jornada'] ?? '—',
            'nombre_cargo' => $e['nombre_cargo'] ?? '—',
            'sueldo_divisa' => (float)$e['sueldo_divisa'],
            'fecha_ingreso' => $e['fecha_ingreso'] ?? '',
            'estado' => (int)$e['estado'],
            'foto_src' => $fotoSrcList,
            'nombre_banco' => $e['nombre_banco'] ?? '',
            'codigo_banco' => $e['codigo_banco'] ?? '',
            'numero_cuenta' => $e['numero_cuenta'] ?? '',
            'tipo_cuenta' => $e['tipo_cuenta'] ?? '',
            'nombre_usuario' => $e['nombre_usuario'] ?? '',
            'nombre_rol' => $e['nombre_rol'] ?? '',
            'total_nominas' => (int)($e['total_nominas'] ?? 0),
            'total_permisos' => (int)($e['total_permisos'] ?? 0),
            'permisos_aprobados' => (int)($e['permisos_aprobados'] ?? 0),
            'permisos_pendientes' => (int)($e['permisos_pendientes'] ?? 0),
            'tasa_bcv' => (float)($tasaBcv ?? 0.0)
          ];

          $searchKeywords = strtolower($cedulaVal . ' ' . $cedulaDisplay . ' ' . $nombreDisplay . ' ' . $nombreComp . ' ' . ($e['nombre_cargo'] ?? '') . ' ' . ($e['estado'] ? 'activo' : 'inactivo'));
        ?>
          <tr 
            class="emp-row" 
            onclick="abrirVerEmpleadoModal(this)" 
            data-empleado="<?= htmlspecialchars(json_encode($eJsonData), ENT_QUOTES, 'UTF-8') ?>" 
            data-search="<?= htmlspecialchars($searchKeywords, ENT_QUOTES, 'UTF-8') ?>"
            title="Clic para ver expediente completo del empleado"
          >
            <!-- CÉDULA / RIF -->
            <td class="emp-col-cedula">
              <?= htmlspecialchars($cedulaDisplay) ?>
            </td>

            <!-- NOMBRE COMPLETO -->
            <td class="emp-col-nombre">
              <div class="emp-name-with-avatar">
                <?php if ($fotoSrcList): ?>
                  <img src="<?= htmlspecialchars($fotoSrcList) ?>" class="emp-mini-avatar" alt="Foto">
                <?php else: ?>
                  <span class="emp-mini-initial"><?= $inicial ?></span>
                <?php endif; ?>
                <span><?= htmlspecialchars($nombreDisplay) ?></span>
              </div>
            </td>

            <!-- CARGO (Cápsula oscura redondeada como en la referencia) -->
            <td>
              <span class="emp-badge-cargo"><?= htmlspecialchars($e['nombre_cargo'] ?? '—') ?></span>
            </td>

            <!-- SALARIO DIARIO -->
            <td>
              <div class="emp-salario-wrap">
                <span class="emp-salario-bs">Bs. <?= number_format($salarioDiarioBs, 2, ',', '.') ?></span>
                <?php if ($salarioDiarioUsd > 0): ?>
                  <span class="emp-salario-usd">$<?= number_format($salarioDiarioUsd, 2, ',', '.') ?> / día</span>
                <?php endif; ?>
              </div>
            </td>

            <!-- ESTADO (Badge redondeado verde/rojo) -->
            <td>
              <?php if ($e['estado']): ?>
                <span class="emp-status-badge activo">Activo</span>
              <?php else: ?>
                <span class="emp-status-badge inactivo">Inactivo</span>
              <?php endif; ?>
            </td>

            <!-- ACCIONES (Botones cuadrados con bordes redondeados como en la referencia) -->
            <td style="text-align:right;" onclick="event.stopPropagation()">
              <div class="emp-actions-group">
                <!-- Botón Editar -->
                <a class="emp-action-square emp-square-edit" href="index.php?r=empleados/editar&id=<?= $e['id_empleado'] ?>" title="Editar empleado">
                  <i class="fa-regular fa-pen-to-square"></i>
                </a>

                <!-- Botón Cambiar Estado (Inactivar / Activar) -->
                <?php if ($e['estado']): ?>
                  <a class="emp-action-square emp-square-inactivate" href="index.php?r=empleados/cambiarEstado&id=<?= $e['id_empleado'] ?>" title="Inactivar empleado" onclick="return confirm('¿Está seguro de inactivar a <?= htmlspecialchars(addslashes($nombreDisplay)) ?>?');">
                    <i class="fa-solid fa-user-minus"></i>
                  </a>
                <?php else: ?>
                  <a class="emp-action-square emp-square-activate" href="index.php?r=empleados/cambiarEstado&id=<?= $e['id_empleado'] ?>" title="Activar empleado">
                    <i class="fa-solid fa-user-plus"></i>
                  </a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>

        <!-- Fila para resultados no encontrados en la búsqueda -->
        <tr id="empNoResultsRow" style="display:none;">
          <td colspan="6" style="text-align:center; padding:45px 20px; color:var(--text-muted);">
            <i class="fa-solid fa-magnifying-glass" style="font-size:28px; opacity:0.35; margin-bottom:10px; display:block;"></i>
            <span style="font-size:14px; font-weight:600;">No se encontraron empleados que coincidan con la búsqueda</span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal de Expediente 360° -->
<?php require __DIR__ . '/VerModal.php'; ?>

<!-- Script para Filtrado en Tiempo Real (Instant Live Search) -->
<script nonce="<?= Security::getCspNonce() ?>">
function filterEmployees() {
  var input = document.getElementById('empSearchInput');
  var query = input ? input.value.toLowerCase().trim() : '';
  var clearBtn = document.getElementById('empSearchClear');
  
  if (clearBtn) {
    clearBtn.style.display = query.length > 0 ? 'inline-flex' : 'none';
  }

  var rows = document.querySelectorAll('#empTableBody tr.emp-row');
  var visibleCount = 0;

  rows.forEach(function(row) {
    var searchTarget = row.getAttribute('data-search') || row.textContent.toLowerCase();
    if (!query || searchTarget.indexOf(query) !== -1) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });

  var noResultsRow = document.getElementById('empNoResultsRow');
  if (noResultsRow) {
    noResultsRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
  }
}

function clearEmployeeSearch() {
  var input = document.getElementById('empSearchInput');
  if (input) {
    input.value = '';
    input.focus();
    filterEmployees();
  }
}
</script>
