</main>

<!-- ═══════════════ COMMAND PALETTE GLOBAL ═══════════════ -->
<div class="cmdk-overlay" id="cmdkOverlay" onclick="if(event.target===this)closeCommandPalette()">
  <div class="cmdk-modal">
    <div class="cmdk-input-wrap">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" id="cmdkInput" placeholder="Buscar empleados, nóminas, periodos o acciones…" autocomplete="off">
      <button class="cmdk-close-btn" onclick="closeCommandPalette()" type="button" title="Cerrar búsqueda (Esc)" aria-label="Cerrar modal">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <div class="cmdk-results" id="cmdkResults">
      <!-- Grupo dinámico: Empleados -->
      <div class="cmdk-group" id="cmdkGroupEmpleados" style="display:none;">
        <div class="cmdk-group-label">Empleados</div>
        <div id="cmdkListEmpleados"></div>
      </div>

      <!-- Grupo dinámico: Periodos y Nóminas -->
      <div class="cmdk-group" id="cmdkGroupPeriodos" style="display:none;">
        <div class="cmdk-group-label">Periodos y Nóminas</div>
        <div id="cmdkListPeriodos"></div>
      </div>

      <!-- Acciones Rápidas -->
      <div class="cmdk-group" id="cmdkGroupAcciones">
        <div class="cmdk-group-label">Acciones rápidas</div>
        <a href="index.php?r=empleados/crear" class="cmdk-item">
          <i class="fa-solid fa-user-plus"></i>
          <span>Crear nuevo empleado</span>
          <kbd>↵</kbd>
        </a>
        <a href="index.php?r=periodos/crear" class="cmdk-item">
          <i class="fa-solid fa-calendar-plus"></i>
          <span>Crear nuevo periodo</span>
        </a>
        <a href="index.php?r=nomina/generar" class="cmdk-item">
          <i class="fa-solid fa-bolt"></i>
          <span>Generar nómina</span>
        </a>
      </div>

      <!-- Navegación -->
      <div class="cmdk-group" id="cmdkGroupNav">
        <div class="cmdk-group-label">Navegación</div>
        <a href="index.php?r=dashboard" class="cmdk-item">
          <i class="fa-solid fa-house"></i>
          <span>Ir al Dashboard</span>
        </a>
        <a href="index.php?r=empleados" class="cmdk-item">
          <i class="fa-solid fa-users"></i>
          <span>Ver empleados</span>
        </a>
        <a href="index.php?r=periodos" class="cmdk-item">
          <i class="fa-solid fa-calendar-days"></i>
          <span>Ver periodos</span>
        </a>
        <a href="index.php?r=permisos" class="cmdk-item">
          <i class="fa-solid fa-clock-rotate-left"></i>
          <span>Ver permisos</span>
        </a>
        <a href="index.php?r=tasas" class="cmdk-item">
          <i class="fa-solid fa-coins"></i>
          <span>Gestión de tasas BCV</span>
        </a>
      </div>

      <!-- Estado vacío cuando nada coincide -->
      <div class="cmdk-empty" id="cmdkEmpty" style="display:none;">
        <i class="fa-solid fa-magnifying-glass"></i>
        <span>No se encontraron empleados, nóminas o acciones para esa búsqueda</span>
      </div>
    </div>
  </div>
</div>

<script>
(function() {
  var searchDebounceTimer = null;
  var currentSearchAbort = null;

  function clearServerResults() {
    var groupEmp = document.getElementById('cmdkGroupEmpleados');
    var listEmp = document.getElementById('cmdkListEmpleados');
    var groupPer = document.getElementById('cmdkGroupPeriodos');
    var listPer = document.getElementById('cmdkListPeriodos');
    var emptyEl = document.getElementById('cmdkEmpty');

    if (groupEmp) groupEmp.style.display = 'none';
    if (listEmp) listEmp.innerHTML = '';
    if (groupPer) groupPer.style.display = 'none';
    if (listPer) listPer.innerHTML = '';
    if (emptyEl) emptyEl.style.display = 'none';
  }

  function openCommandPalette() {
    var overlay = document.getElementById('cmdkOverlay');
    if (!overlay) return;
    if (overlay.parentElement !== document.body) {
      document.body.appendChild(overlay);
    }
    overlay.classList.add('open');
    var inp = document.getElementById('cmdkInput');
    if (inp) {
      inp.value = '';
      clearServerResults();
      filterStaticItems('');
      setTimeout(function() { inp.focus(); }, 60);
    }
  }

  function closeCommandPalette() {
    var overlay = document.getElementById('cmdkOverlay');
    if (overlay) overlay.classList.remove('open');
    var inp = document.getElementById('cmdkInput');
    if (inp) inp.value = '';
    clearServerResults();
    filterStaticItems('');
  }

  function filterStaticItems(q) {
    var visibleCount = 0;
    var groupAcciones = document.getElementById('cmdkGroupAcciones');
    var groupNav = document.getElementById('cmdkGroupNav');

    var staticItems = document.querySelectorAll('#cmdkGroupAcciones .cmdk-item, #cmdkGroupNav .cmdk-item');
    staticItems.forEach(function(item) {
      var text = item.textContent.toLowerCase();
      var show = !q || text.indexOf(q) !== -1;
      item.style.display = show ? 'flex' : 'none';
      if (show) visibleCount++;
    });

    if (groupAcciones) {
      var hasAcciones = Array.from(groupAcciones.querySelectorAll('.cmdk-item')).some(function(i) { return i.style.display !== 'none'; });
      groupAcciones.style.display = hasAcciones ? 'block' : 'none';
    }
    if (groupNav) {
      var hasNav = Array.from(groupNav.querySelectorAll('.cmdk-item')).some(function(i) { return i.style.display !== 'none'; });
      groupNav.style.display = hasNav ? 'block' : 'none';
    }

    return visibleCount;
  }

  function escapeHtml(str) {
    return String(str || '').replace(/[&<>"']/g, function(m) {
      return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[m];
    });
  }

  function executeServerSearch(query) {
    if (!query) {
      clearServerResults();
      return;
    }

    if (currentSearchAbort) {
      currentSearchAbort.abort();
    }
    currentSearchAbort = new AbortController();

    var url = 'index.php?r=api/search&q=' + encodeURIComponent(query);
    fetch(url, { signal: currentSearchAbort.signal })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        renderServerResults(data, query);
      })
      .catch(function(err) {
        if (err.name !== 'AbortError') {
          console.warn('Error en búsqueda cmdk:', err);
          renderServerResults({ empleados: [], periodos: [] }, query);
        }
      });
  }

  function renderServerResults(data, query) {
    var inp = document.getElementById('cmdkInput');
    var currentQ = (inp ? inp.value : '').toLowerCase().trim();

    // Si el usuario borró la consulta, restaurar vista limpia de acciones y navegación
    if (!currentQ) {
      clearServerResults();
      filterStaticItems('');
      return;
    }

    var groupEmp = document.getElementById('cmdkGroupEmpleados');
    var listEmp = document.getElementById('cmdkListEmpleados');
    var groupPer = document.getElementById('cmdkGroupPeriodos');
    var listPer = document.getElementById('cmdkListPeriodos');
    var emptyEl = document.getElementById('cmdkEmpty');

    var empCount = 0;
    var perCount = 0;

    // Render Empleados
    if (groupEmp && listEmp) {
      listEmp.innerHTML = '';
      if (data.empleados && data.empleados.length > 0) {
        empCount = data.empleados.length;
        groupEmp.style.display = 'block';
        data.empleados.forEach(function(emp) {
          var pNombre = emp.primer_nombre || '';
          var pApellido = emp.primer_apellido || '';
          var iniciales = ((pNombre.charAt(0) || '') + (pApellido.charAt(0) || '')).toUpperCase() || 'EM';
          var nombreCompleto = (pNombre + ' ' + pApellido).trim() || ('Empleado #' + emp.id_empleado);
          var cargo = emp.nombre_cargo || 'Sin cargo';
          var cedula = emp.cedula ? ('CI: ' + emp.cedula) : 'Sin CI';
          var estadoText = emp.estado == 1 ? 'Activo' : 'Inactivo';
          var badgeClass = emp.estado == 1 ? 'ok' : 'muted';

          var a = document.createElement('a');
          a.href = 'index.php?r=empleados/editar&id=' + emp.id_empleado;
          a.className = 'cmdk-item';
          a.innerHTML = 
            '<div class="cmdk-item-avatar">' + escapeHtml(iniciales) + '</div>' +
            '<div class="cmdk-item-info">' +
              '<span class="cmdk-item-title">' + escapeHtml(nombreCompleto) + '</span>' +
              '<span class="cmdk-item-sub">' + escapeHtml(cedula) + ' · ' + escapeHtml(cargo) + '</span>' +
            '</div>' +
            '<span class="cmdk-badge ' + badgeClass + '">' + escapeHtml(estadoText) + '</span>';
          listEmp.appendChild(a);
        });
      } else {
        groupEmp.style.display = 'none';
      }
    }

    // Render Periodos y Nóminas
    if (groupPer && listPer) {
      listPer.innerHTML = '';
      if (data.periodos && data.periodos.length > 0) {
        perCount = data.periodos.length;
        groupPer.style.display = 'block';
        data.periodos.forEach(function(per) {
          var desc = per.descripcion || ('Periodo #' + per.id_periodo);
          var tipo = per.tipo_periodo || 'Quincenal';
          var fechas = (per.fecha_inicio || '') + ' → ' + (per.fecha_fin || '');
          var tieneNomina = !!per.id_nomina;
          var estadoNomina = tieneNomina ? 'Nómina Generada' : 'Periodo Abierto';
          var badgeClass = tieneNomina ? 'ok' : 'info';
          var iconClass = tieneNomina ? 'fa-file-invoice-dollar' : 'fa-calendar-days';

          var a = document.createElement('a');
          a.href = 'index.php?r=nomina/generar&id_periodo=' + per.id_periodo;
          a.className = 'cmdk-item';
          a.innerHTML = 
            '<i class="fa-solid ' + iconClass + '" style="font-size:16px; color:' + (tieneNomina ? '#10b981' : '#0284c7') + ';"></i>' +
            '<div class="cmdk-item-info">' +
              '<span class="cmdk-item-title">' + escapeHtml(desc) + '</span>' +
              '<span class="cmdk-item-sub">' + escapeHtml(tipo) + ' · ' + escapeHtml(fechas) + '</span>' +
            '</div>' +
            '<span class="cmdk-badge ' + badgeClass + '">' + escapeHtml(estadoNomina) + '</span>';
          listPer.appendChild(a);
        });
      } else {
        groupPer.style.display = 'none';
      }
    }

    // Comprobar estado vacío general
    var staticCount = filterStaticItems(currentQ);
    if (emptyEl) {
      var totalVisible = staticCount + empCount + perCount;
      if (totalVisible === 0) {
        emptyEl.style.display = 'flex';
        emptyEl.innerHTML = 
          '<i class="fa-solid fa-folder-open"></i>' +
          '<span>No se encontraron resultados para "<strong>' + escapeHtml(currentQ) + '</strong>"</span>' +
          '<span style="font-size:12px; color:var(--text-muted); opacity:0.8;">Verifica que el nombre, cédula o periodo estén bien escritos</span>';
      } else {
        emptyEl.style.display = 'none';
      }
    }
  }

  function onCmdkInput() {
    var inp = document.getElementById('cmdkInput');
    var q = (inp ? inp.value : '').toLowerCase().trim();

    if (!q) {
      if (currentSearchAbort) currentSearchAbort.abort();
      clearTimeout(searchDebounceTimer);
      clearServerResults();
      filterStaticItems('');
      return;
    }

    filterStaticItems(q);

    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(function() {
      executeServerSearch(q);
    }, 120);
  }

  // Atajos globales
  document.addEventListener('keydown', function(e) {
    if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault();
      openCommandPalette();
    }
    if (e.key === 'Escape') {
      closeCommandPalette();
    }
  });

  // Enter para ir al primer resultado visible si se presiona en el input
  document.addEventListener('DOMContentLoaded', function() {
    var inp = document.getElementById('cmdkInput');
    if (inp) {
      inp.addEventListener('input', onCmdkInput);
      inp.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          var firstItem = document.querySelector('.cmdk-results .cmdk-item:not([style*="display: none"])');
          if (firstItem && firstItem.href) {
            closeCommandPalette();
            window.location.href = firstItem.href;
          }
        }
      });
    }
  });

  window.openCommandPalette = openCommandPalette;
  window.closeCommandPalette = closeCommandPalette;
})();

function updateThemeButtonState(animate) {
  var btn = document.getElementById('headerThemeBtn');
  if (!btn) return;
  var moon = btn.querySelector('.moon-icon');
  var sun = btn.querySelector('.sun-icon');
  if (!moon || !sun) return;

  var isDark = document.documentElement.classList.contains('dark-theme');
  btn.title = isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro';

  if (!animate) {
    if (isDark) {
      moon.style.transform = 'translate(calc(-50% - 28px), -50%)';
      moon.style.opacity = '0';
      sun.style.transform = 'translate(-50%, -50%)';
      sun.style.opacity = '1';
    } else {
      sun.style.transform = 'translate(calc(-50% + 28px), -50%)';
      sun.style.opacity = '0';
      moon.style.transform = 'translate(-50%, -50%)';
      moon.style.opacity = '1';
    }
    return;
  }

  // Animación: el icono actual se desplaza hacia la IZQUIERDA y sale,
  // y el nuevo icono entra desde la derecha hacia el centro.
  if (isDark) {
    // Luna sale desplazándose hacia la izquierda
    moon.animate([
      { transform: 'translate(-50%, -50%) rotate(0deg) scale(1)', opacity: 1 },
      { transform: 'translate(calc(-50% - 28px), -50%) rotate(-45deg) scale(0.5)', opacity: 0 }
    ], { duration: 300, easing: 'cubic-bezier(0.4, 0, 0.2, 1)', fill: 'forwards' });

    // Sol entra desde la derecha al centro
    sun.animate([
      { transform: 'translate(calc(-50% + 28px), -50%) rotate(45deg) scale(0.5)', opacity: 0 },
      { transform: 'translate(-50%, -50%) rotate(0deg) scale(1)', opacity: 1 }
    ], { duration: 300, delay: 60, easing: 'cubic-bezier(0.16, 1, 0.3, 1)', fill: 'forwards' });
  } else {
    // Sol sale desplazándose hacia la izquierda
    sun.animate([
      { transform: 'translate(-50%, -50%) rotate(0deg) scale(1)', opacity: 1 },
      { transform: 'translate(calc(-50% - 28px), -50%) rotate(-45deg) scale(0.5)', opacity: 0 }
    ], { duration: 300, easing: 'cubic-bezier(0.4, 0, 0.2, 1)', fill: 'forwards' });

    // Luna entra desde la derecha al centro
    moon.animate([
      { transform: 'translate(calc(-50% + 28px), -50%) rotate(45deg) scale(0.5)', opacity: 0 },
      { transform: 'translate(-50%, -50%) rotate(0deg) scale(1)', opacity: 1 }
    ], { duration: 300, delay: 60, easing: 'cubic-bezier(0.16, 1, 0.3, 1)', fill: 'forwards' });
  }
}

function toggleTheme() {
  document.documentElement.classList.toggle('dark-theme');
  var isDark = document.documentElement.classList.contains('dark-theme');
  localStorage.setItem('dash-theme', isDark ? 'dark' : 'light');
  updateThemeButtonState(true);
  if (typeof showToast === 'function') {
    showToast(isDark ? 'Modo oscuro activado' : 'Modo claro activado');
  }
  if (window._chartCargos) {
    window._chartCargos.update();
  }
}

document.addEventListener('DOMContentLoaded', function() {
  updateThemeButtonState(false);
});

window.toggleTheme = toggleTheme;
window.updateThemeButtonState = updateThemeButtonState;
</script>
</body>
</html>
