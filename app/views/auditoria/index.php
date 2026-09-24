<div class="view-header-bar" style="margin-bottom: 20px;">
  <div>
    <h2 style="margin: 0; display: flex; align-items: center; gap: 10px;">
      <i class="fa-solid fa-clock-rotate-left" style="color: var(--primary);"></i>
      Historial de Actividades
    </h2>
    <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 13.5px;">
      Bitácora cronológica de acciones y movimientos de usuarios
    </p>
  </div>
  <div class="actions-row" style="margin-bottom: 0;">
    <?php
      $exportQuery = http_build_query(array_merge($filtros, ['r' => 'auditoria/exportar']));
    ?>
    <a class="btn secondary" href="index.php?<?= $exportQuery ?>" title="Descargar bitácora en formato CSV para Excel">
      <i class="fa-solid fa-file-arrow-down"></i> Exportar CSV
    </a>
  </div>
</div>

<!-- ═══════════════ KPI STATS CARDS ═══════════════ -->
<div class="audit-kpi-grid">
  <div class="audit-kpi-card">
    <div class="audit-kpi-icon icon-today"><i class="fa-solid fa-bolt"></i></div>
    <div class="audit-kpi-info">
      <span class="audit-kpi-label">Acciones Hoy</span>
      <strong class="audit-kpi-val"><?= number_format($kpis['total_hoy']) ?></strong>
    </div>
  </div>

  <div class="audit-kpi-card">
    <div class="audit-kpi-icon icon-week"><i class="fa-solid fa-chart-line"></i></div>
    <div class="audit-kpi-info">
      <span class="audit-kpi-label">Últimos 7 Días</span>
      <strong class="audit-kpi-val"><?= number_format($kpis['total_semana']) ?></strong>
    </div>
  </div>

  <div class="audit-kpi-card">
    <div class="audit-kpi-icon icon-users"><i class="fa-solid fa-user-check"></i></div>
    <div class="audit-kpi-info">
      <span class="audit-kpi-label"><?= $isAdmin ? 'Usuarios Activos Hoy' : 'Tu Actividad Hoy' ?></span>
      <strong class="audit-kpi-val"><?= number_format($kpis['usuarios_hoy']) ?></strong>
    </div>
  </div>

  <div class="audit-kpi-card">
    <div class="audit-kpi-icon icon-total"><i class="fa-solid fa-list-check"></i></div>
    <div class="audit-kpi-info">
      <span class="audit-kpi-label">Total de Movimientos</span>
      <strong class="audit-kpi-val"><?= number_format($kpis['total_movimientos']) ?></strong>
    </div>
  </div>
</div>

<!-- ═══════════════ FILTROS Y BÚSQUEDA ═══════════════ -->
<div class="card audit-filter-card">
  <form method="GET" action="index.php" class="audit-filter-form">
    <input type="hidden" name="r" value="auditoria">
    <input type="hidden" name="limit" value="<?= (int)$limit ?>">
    
    <div class="audit-filter-row">
      <div class="audit-filter-field search-field">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input type="text" name="q" value="<?= htmlspecialchars($filtros['q']) ?>" placeholder="Buscar por acción, descripción, tabla o IP..." autocomplete="off">
        <?php if (!empty($filtros['q'])): ?>
          <a href="index.php?r=auditoria" class="clear-search-btn" title="Limpiar búsqueda">&times;</a>
        <?php endif; ?>
      </div>

      <div class="audit-filter-field select-field">
        <select name="modulo" onchange="this.form.submit()">
          <option value="">Todos los módulos</option>
          <?php foreach ($modulos as $mod): ?>
            <option value="<?= htmlspecialchars($mod) ?>" <?= ($filtros['modulo'] === $mod) ? 'selected' : '' ?>>
              Módulo: <?= htmlspecialchars($mod) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="audit-filter-field select-field">
        <select name="accion" onchange="this.form.submit()">
          <option value="">Todas las acciones</option>
          <?php 
            $accionesCatalogo = ['CREAR', 'MODIFICAR', 'ELIMINAR', 'LOGIN', 'LOGOUT', 'APROBAR', 'RECHAZAR', 'CALCULAR', 'EXPORTAR', 'IMPRIMIR_RECIBO', 'CAMBIAR_ESTADO'];
            foreach ($accionesCatalogo as $acc): 
          ?>
            <option value="<?= $acc ?>" <?= ($filtros['accion'] === $acc) ? 'selected' : '' ?>>
              Acción: <?= $acc ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <?php if ($isAdmin && !empty($usuariosFiltro)): ?>
      <div class="audit-filter-field select-field">
        <select name="id_usuario" onchange="this.form.submit()">
          <option value="">Todos los usuarios</option>
          <?php foreach ($usuariosFiltro as $uf): ?>
            <option value="<?= $uf['id_usuario'] ?>" <?= ((int)$filtros['id_usuario'] === (int)$uf['id_usuario']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($uf['nombre_usuario']) ?> (<?= htmlspecialchars($uf['nombre_rol'] ?? 'Usuario') ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php endif; ?>
    </div>

    <div class="audit-filter-dates-row">
      <div class="date-input-wrap">
        <label><i class="fa-regular fa-calendar"></i> Desde:</label>
        <input type="date" name="fecha_desde" value="<?= htmlspecialchars($filtros['fecha_desde']) ?>">
      </div>
      <div class="date-input-wrap">
        <label><i class="fa-regular fa-calendar"></i> Hasta:</label>
        <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($filtros['fecha_hasta']) ?>">
      </div>
      <button type="submit" class="btn small"><i class="fa-solid fa-filter"></i> Aplicar Filtros</button>
      <?php if (!empty(array_filter($filtros))): ?>
        <a href="index.php?r=auditoria" class="btn small secondary"><i class="fa-solid fa-rotate-left"></i> Restablecer</a>
      <?php endif; ?>
    </div>
  </form>
</div>

<!-- ═══════════════ TABLA DE REGISTROS ═══════════════ -->
<div class="card table-responsive" style="padding: 0; overflow-x: auto;">
  <table class="audit-table">
    <thead>
      <tr>
        <th style="width: 170px;">Fecha y Hora</th>
        <th style="width: 220px;">Usuario</th>
        <th style="width: 140px;">Módulo</th>
        <th style="width: 130px;">Acción</th>
        <th>Descripción de la Operación</th>
        <th style="width: 120px;">IP Origen</th>
        <th style="width: 100px; text-align: right;">Detalle</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($registros)): ?>
        <tr>
          <td colspan="7" class="audit-empty-state">
            <i class="fa-solid fa-clipboard-check"></i>
            <p>No se encontraron registros de auditoría con los filtros especificados.</p>
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($registros as $reg): 
          $fechaTs = strtotime($reg['fecha_hora']);
          $fechaFormato = date('d/m/Y h:i A', $fechaTs);
          
          // Cálculo de tiempo relativo amigable
          $difSegundos = time() - $fechaTs;
          if ($difSegundos < 60) {
              $relativo = 'Hace momentos';
          } elseif ($difSegundos < 3600) {
              $relativo = 'Hace ' . floor($difSegundos / 60) . ' min';
          } elseif ($difSegundos < 86400) {
              $relativo = 'Hace ' . floor($difSegundos / 3600) . ' h';
          } else {
              $relativo = 'Hace ' . floor($difSegundos / 86400) . ' d';
          }

          // Badges de colores por tipo de acción
          $accionClase = 'badge-other';
          $accUpper = strtoupper($reg['accion']);
          if (in_array($accUpper, ['CREAR', 'REGISTRO_MANUAL', 'GUARDAR'])) $accionClase = 'badge-success';
          elseif (in_array($accUpper, ['MODIFICAR', 'EDITAR', 'ACTUALIZAR', 'PASSWORD_CHANGE', 'THEME_CHANGE'])) $accionClase = 'badge-info';
          elseif (in_array($accUpper, ['ELIMINAR', 'BLOQUEAR', 'INACTIVAR'])) $accionClase = 'badge-danger';
          elseif (in_array($accUpper, ['LOGIN', 'LOGOUT', 'TIMEOUT'])) $accionClase = 'badge-purple';
          elseif (in_array($accUpper, ['APROBAR', 'CALCULAR', 'GENERAR_NOMINA'])) $accionClase = 'badge-emerald';
          elseif (in_array($accUpper, ['EXPORTAR', 'SINCRONIZAR_BCV'])) $accionClase = 'badge-teal';
          elseif (in_array($accUpper, ['IMPRIMIR_RECIBO', 'IMPRIMIR'])) $accionClase = 'badge-cyan';
        ?>
          <tr onclick="abrirDetalleAuditoria(<?= (int)$reg['id_auditoria'] ?>)" style="cursor: pointer;" title="Clic para ver detalle">
            <td>
              <div class="audit-time-cell">
                <span class="audit-date-main"><?= htmlspecialchars($fechaFormato) ?></span>
                <span class="audit-time-relative"><i class="fa-regular fa-clock"></i> <?= $relativo ?></span>
              </div>
            </td>
            <td>
              <div class="audit-user-cell">
                <?php if (!empty($reg['usuario_avatar'])): ?>
                  <span class="audit-user-avatar has-img">
                    <img src="<?= htmlspecialchars($reg['usuario_avatar']) ?>" alt="<?= htmlspecialchars($reg['nombre_usuario'] ?? '') ?>">
                  </span>
                <?php else: ?>
                  <span class="audit-user-avatar">
                    <?= strtoupper(substr($reg['nombre_usuario'] ?? 'U', 0, 1)) ?>
                  </span>
                <?php endif; ?>
                <div class="audit-user-text">
                  <strong class="audit-user-name"><?= htmlspecialchars($reg['nombre_usuario'] ?? 'Sistema') ?></strong>
                  <span class="audit-user-role"><?= htmlspecialchars($reg['nombre_rol'] ?? 'Operador') ?></span>
                </div>
              </div>
            </td>
            <td>
              <span class="audit-module-pill">
                <i class="fa-solid fa-folder"></i> <?= htmlspecialchars($reg['modulo'] ?? 'GENERAL') ?>
              </span>
            </td>
            <td>
              <span class="audit-action-badge <?= $accionClase ?>">
                <?php if ($accUpper === 'IMPRIMIR_RECIBO'): ?>
                  <i class="fa-solid fa-print" style="margin-right:4px;"></i>
                <?php endif; ?>
                <?= htmlspecialchars($reg['accion']) ?>
              </span>
            </td>
            <td>
              <div class="audit-desc-cell">
                <span class="audit-desc-text"><?= htmlspecialchars($reg['detalle']) ?></span>
                <?php if (!empty($reg['tabla_afectada'])): ?>
                  <span class="audit-table-meta"><i class="fa-solid fa-database"></i> <?= htmlspecialchars($reg['tabla_afectada']) ?><?= $reg['id_registro_afectado'] ? " #{$reg['id_registro_afectado']}" : '' ?></span>
                <?php endif; ?>
              </div>
            </td>
            <td>
              <code class="audit-ip-pill"><?= htmlspecialchars($reg['ip'] ?? '127.0.0.1') ?></code>
            </td>
            <td style="text-align: right;" onclick="event.stopPropagation()">
              <button type="button" class="btn small secondary audit-btn-detail" onclick="abrirDetalleAuditoria(<?= (int)$reg['id_auditoria'] ?>)" title="Ver detalle">
                <i class="fa-solid fa-eye"></i>
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- ═══════════════ PAGINACIÓN Y CONTROL DE REGISTROS ═══════════════ -->
  <div class="custom-paginator-wrap">
    <div class="paginator-size-box">
      <label for="auditLimitSelect">Mostrar</label>
      <select id="auditLimitSelect" class="paginator-select" onchange="cambiarLimiteAuditoria(this.value)" aria-label="Cantidad de registros por página">
        <?php foreach ($allowedLimits as $l): ?>
          <option value="<?= $l ?>" <?= $l === $limit ? 'selected' : '' ?>><?= $l ?></option>
        <?php endforeach; ?>
      </select>
      <span>registros por página</span>
    </div>

    <div class="paginator-info">
      <?php 
        $desde = $totalRegistros > 0 ? (($page - 1) * $limit + 1) : 0;
        $hasta = min($page * $limit, $totalRegistros);
      ?>
      Mostrando <strong><?= $desde ?></strong> &ndash; <strong><?= $hasta ?></strong> de <strong><?= number_format($totalRegistros) ?></strong> registros
    </div>

    <div class="paginator-nav">
      <?php 
        $baseParams = array_merge($filtros, ['r' => 'auditoria', 'limit' => $limit]);
      ?>
      <?php if ($totalPaginas > 1): ?>
        <!-- Primera página -->
        <a href="index.php?<?= http_build_query(array_merge($baseParams, ['page' => 1])) ?>" 
           class="paginator-btn <?= ($page === 1) ? 'disabled' : '' ?>" 
           title="Primera página" aria-label="Primera página">
          <i class="fa-solid fa-angles-left"></i>
        </a>

        <!-- Anterior -->
        <a href="index.php?<?= http_build_query(array_merge($baseParams, ['page' => max(1, $page - 1)])) ?>" 
           class="paginator-btn <?= ($page === 1) ? 'disabled' : '' ?>" 
           title="Página anterior" aria-label="Página anterior">
          <i class="fa-solid fa-chevron-left"></i>
        </a>

        <!-- Números dinámicos -->
        <?php
          $maxBtns = 5;
          $startP = max(1, $page - 2);
          $endP = min($totalPaginas, $startP + $maxBtns - 1);
          if ($endP - $startP < $maxBtns - 1) {
            $startP = max(1, $endP - $maxBtns + 1);
          }
          if ($startP > 1):
        ?>
          <a href="index.php?<?= http_build_query(array_merge($baseParams, ['page' => 1])) ?>" class="paginator-btn">1</a>
          <?php if ($startP > 2): ?><span class="paginator-ellipsis">…</span><?php endif; ?>
        <?php endif; ?>

        <?php for ($p = $startP; $p <= $endP; $p++): ?>
          <a href="index.php?<?= http_build_query(array_merge($baseParams, ['page' => $p])) ?>" 
             class="paginator-btn <?= ($p === $page) ? 'active' : '' ?>"
             <?= ($p === $page) ? 'aria-current="page"' : '' ?>><?= $p ?></a>
        <?php endfor; ?>

        <?php if ($endP < $totalPaginas): ?>
          <?php if ($endP < $totalPaginas - 1): ?><span class="paginator-ellipsis">…</span><?php endif; ?>
          <a href="index.php?<?= http_build_query(array_merge($baseParams, ['page' => $totalPaginas])) ?>" class="paginator-btn"><?= $totalPaginas ?></a>
        <?php endif; ?>

        <!-- Siguiente -->
        <a href="index.php?<?= http_build_query(array_merge($baseParams, ['page' => min($totalPaginas, $page + 1)])) ?>" 
           class="paginator-btn <?= ($page === $totalPaginas) ? 'disabled' : '' ?>" 
           title="Página siguiente" aria-label="Página siguiente">
          <i class="fa-solid fa-chevron-right"></i>
        </a>

        <!-- Última página -->
        <a href="index.php?<?= http_build_query(array_merge($baseParams, ['page' => $totalPaginas])) ?>" 
           class="paginator-btn <?= ($page === $totalPaginas) ? 'disabled' : '' ?>" 
           title="Última página" aria-label="Última página">
          <i class="fa-solid fa-angles-right"></i>
        </a>
      <?php else: ?>
        <button type="button" class="paginator-btn disabled" disabled aria-label="Página anterior"><i class="fa-solid fa-chevron-left"></i></button>
        <button type="button" class="paginator-btn active" aria-current="page">1</button>
        <button type="button" class="paginator-btn disabled" disabled aria-label="Página siguiente"><i class="fa-solid fa-chevron-right"></i></button>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ═══════════════ MODAL DE DETALLE DE ACCIÓN ═══════════════ -->
<div class="detail-modal-overlay" id="auditModalOverlay">
  <div class="detail-modal-card audit-modal-card">
    <div class="detail-modal-fixed-top">
      <div class="detail-modal-header">
        <h4 class="detail-modal-title">
          <i class="fa-solid fa-file-lines" style="color: var(--primary);"></i> Detalle de la Acción
        </h4>
        <button type="button" class="detail-modal-close" onclick="cerrarDetalleAuditoria()" aria-label="Cerrar modal">&times;</button>
      </div>
      <div class="detail-header-profile" id="modalAuditHeader">
        <!-- Rellenado dinámicamente por JS -->
      </div>
    </div>
    
    <div class="detail-modal-scroll-body" id="modalAuditBody">
      <!-- Rellenado dinámicamente por JS -->
    </div>
  </div>
</div>

<script nonce="<?= Security::getCspNonce() ?>">
function abrirDetalleAuditoria(id) {
  const overlay = document.getElementById('auditModalOverlay');
  const header = document.getElementById('modalAuditHeader');
  const body = document.getElementById('modalAuditBody');
  
  if (!overlay || !header || !body) return;

  header.innerHTML = '<div style="padding: 10px; color: var(--text-muted);"><i class="fa-solid fa-spinner fa-spin"></i> Cargando información...</div>';
  body.innerHTML = '';
  overlay.classList.add('active');

  fetch('index.php?r=auditoria/detalle&id=' + id)
    .then(res => res.json())
    .then(res => {
      if (!res.success) {
        body.innerHTML = '<div class="alert error">' + (res.error || 'Error al cargar el detalle.') + '</div>';
        return;
      }
      const d = res.data;

      header.innerHTML = `
        <div class="detail-profile-avatar" style="background: linear-gradient(135deg, #0f766e, #14b8a6);">
          ${d.avatar ? `<img src="${d.avatar}" class="detail-profile-img" style="border:none;">` : (d.usuario ? d.usuario.charAt(0).toUpperCase() : 'A')}
        </div>
        <div class="detail-profile-info">
          <h3>${escapeHtml(d.usuario)} <span class="badge" style="font-size:11px; margin-left:6px;">${escapeHtml(d.rol)}</span></h3>
          <span class="sub-label"><i class="fa-solid fa-network-wired"></i> IP: <strong>${escapeHtml(d.ip)}</strong> &bull; <i class="fa-regular fa-clock"></i> ${escapeHtml(d.fecha_hora)}</span>
        </div>
      `;

      let jsonHtml = '';
      if (d.detalles_json) {
        if (typeof d.detalles_json === 'object') {
          jsonHtml = `
            <div style="margin-top: 14px;">
              <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">
                <i class="fa-solid fa-code"></i> Parámetros Estructurados (JSON / Cambios)
              </div>
              <pre class="audit-json-box">${escapeHtml(JSON.stringify(d.detalles_json, null, 2))}</pre>
            </div>
          `;
        } else {
          jsonHtml = `
            <div style="margin-top: 14px;">
              <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">
                <i class="fa-solid fa-code"></i> Datos Adicionales
              </div>
              <pre class="audit-json-box">${escapeHtml(d.detalles_json)}</pre>
            </div>
          `;
        }
      }

      body.innerHTML = `
        <div class="detail-grid">
          <div class="param-pill">
            <span class="lbl">ID de Auditoría</span>
            <span class="val">#${d.id_auditoria}</span>
          </div>
          <div class="param-pill">
            <span class="lbl">Módulo Institucional</span>
            <span class="val">${escapeHtml(d.modulo)}</span>
          </div>
          <div class="param-pill">
            <span class="lbl">Tipo de Acción</span>
            <span class="val" style="color:var(--primary); font-weight:800;">${escapeHtml(d.accion)}</span>
          </div>
          <div class="param-pill">
            <span class="lbl">Tabla y Registro</span>
            <span class="val">${escapeHtml(d.tabla)} ${d.id_registro ? '#' + d.id_registro : '(General)'}</span>
          </div>
        </div>

        <div style="margin-top: 14px; background: rgba(15, 23, 42, 0.03); padding: 14px 16px; border-radius: 12px; border: 1px solid var(--border);">
          <div style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">
            <i class="fa-solid fa-align-left"></i> Resumen del Evento
          </div>
          <div style="font-size: 14px; font-weight: 600; color: var(--text); line-height: 1.5;">
            ${escapeHtml(d.detalle)}
          </div>
        </div>

        ${jsonHtml}
      `;
    })
    .catch(err => {
      body.innerHTML = '<div class="alert error">Error de conexión al cargar la bitácora.</div>';
    });
}

function cerrarDetalleAuditoria() {
  const overlay = document.getElementById('auditModalOverlay');
  if (overlay) overlay.classList.remove('active');
}

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') cerrarDetalleAuditoria();
});

function escapeHtml(text) {
  if (!text) return '';
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

function cambiarLimiteAuditoria(newLimit) {
  var url = new URL(window.location.href);
  url.searchParams.set('limit', newLimit);
  url.searchParams.set('page', 1);
  window.location.href = url.toString();
}
</script>
