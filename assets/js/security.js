/**
 * Sistema de Nómina — Gestor de Seguridad del Cliente
 * Control de inactividad, renovación proactiva (Ping), advertencia modal interactiva
 * y detección de abandono seguro con sendBeacon.
 */
(function() {
  'use strict';

  // Configuración de tiempos (en segundos)
  // En producción: 15 min inactividad (900 s), advertencia a los 14 min (840 s)
  var INACTIVITY_LIMIT = 900;
  var WARNING_BEFORE = 60;
  var WARNING_TIME = INACTIVITY_LIMIT - WARNING_BEFORE; // 840 s

  var lastActivity = Date.now();
  var warningModalVisible = false;
  var countdownInterval = null;
  var secondsLeft = WARNING_BEFORE;
  var isInternalNavigation = false;

  // Claves para coordinación multi-pestaña en localStorage
  var TAB_ID = 'tab_' + Math.random().toString(36).substring(2, 9) + '_' + Date.now();
  sessionStorage.setItem('nomina_active_tab_id', TAB_ID);

  function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
  }

  function getCspNonce() {
    var meta = document.querySelector('meta[name="csp-nonce"]');
    return meta ? meta.getAttribute('content') : '';
  }

  // Exponer utilidades globales
  window.Security = {
    getCsrfToken: getCsrfToken,
    getCspNonce: getCspNonce,
    resetInactivity: onUserActivity,
    pingSession: pingSession
  };

  /**
   * Registro y descarte de pestañas activas
   */
  function registerTab() {
    try {
      var tabs = JSON.parse(localStorage.getItem('nomina_active_tabs') || '{}');
      tabs[TAB_ID] = Date.now();
      localStorage.setItem('nomina_active_tabs', JSON.stringify(tabs));
    } catch (e) {}
  }

  function unregisterTab() {
    try {
      var tabs = JSON.parse(localStorage.getItem('nomina_active_tabs') || '{}');
      delete tabs[TAB_ID];
      // Limpiar pestañas zombis con más de 1 hora
      var now = Date.now();
      var count = 0;
      for (var id in tabs) {
        if (now - tabs[id] < 3600000) {
          count++;
        } else {
          delete tabs[id];
        }
      }
      localStorage.setItem('nomina_active_tabs', JSON.stringify(tabs));
      return count;
    } catch (e) {
      return 0;
    }
  }

  registerTab();
  // Latido periódico de pestaña activa
  setInterval(registerTab, 15000);

  /**
   * Detección de actividad del usuario
   */
  var activityDebounce = 0;
  function onUserActivity() {
    var now = Date.now();
    if (now - activityDebounce < 5000) return; // Evitar saturación cada 5s
    activityDebounce = now;
    lastActivity = now;

    // Sincronizar timestamp con otras pestañas
    try {
      localStorage.setItem('nomina_last_user_activity', String(now));
    } catch (e) {}

    // Si el modal de advertencia estaba visible y el usuario interactuó, renovar
    if (warningModalVisible) {
      pingSession();
    }
  }

  // Eventos de usuario para monitoreo de actividad
  ['mousemove', 'keydown', 'mousedown', 'touchstart', 'scroll'].forEach(function(evt) {
    window.addEventListener(evt, onUserActivity, { passive: true });
  });

  // Escuchar actividad desde otras pestañas
  window.addEventListener('storage', function(e) {
    if (e.key === 'nomina_last_user_activity' && e.newValue) {
      var foreignActivity = parseInt(e.newValue, 10);
      if (!isNaN(foreignActivity) && foreignActivity > lastActivity) {
        lastActivity = foreignActivity;
        if (warningModalVisible) {
          hideWarningModal();
        }
      }
    }
  });

  /**
   * Bucle de verificación de inactividad
   */
  setInterval(function() {
    var elapsedSeconds = Math.floor((Date.now() - lastActivity) / 1000);

    if (elapsedSeconds >= INACTIVITY_LIMIT) {
      // Tiempo vencido
      handleSessionTimeout();
    } else if (elapsedSeconds >= WARNING_TIME && !warningModalVisible) {
      // A falta de 60 segundos: mostrar advertencia
      showWarningModal();
    }
  }, 1000);

  function showWarningModal() {
    warningModalVisible = true;
    var modal = document.getElementById('security-timeout-modal');
    if (!modal) return;

    modal.classList.add('active');
    secondsLeft = Math.max(1, INACTIVITY_LIMIT - Math.floor((Date.now() - lastActivity) / 1000));
    updateCountdownDisplay(secondsLeft);

    if (countdownInterval) clearInterval(countdownInterval);
    countdownInterval = setInterval(function() {
      secondsLeft--;
      if (secondsLeft <= 0) {
        clearInterval(countdownInterval);
        handleSessionTimeout();
      } else {
        updateCountdownDisplay(secondsLeft);
      }
    }, 1000);
  }

  function hideWarningModal() {
    warningModalVisible = false;
    if (countdownInterval) clearInterval(countdownInterval);
    var modal = document.getElementById('security-timeout-modal');
    if (modal) modal.classList.remove('active');
  }

  function updateCountdownDisplay(sec) {
    var timerEl = document.getElementById('timeout-countdown-seconds');
    if (timerEl) {
      timerEl.textContent = sec < 10 ? '0' + sec : sec;
    }
    var progressEl = document.getElementById('timeout-progress-bar');
    if (progressEl) {
      var pct = Math.max(0, Math.min(100, (sec / WARNING_BEFORE) * 100));
      progressEl.style.width = pct + '%';
    }
  }

  function pingSession() {
    fetch('index.php?r=auth/ping', {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(function(res) {
      if (res.status === 401) {
        handleSessionTimeout();
      } else {
        lastActivity = Date.now();
        try {
          localStorage.setItem('nomina_last_user_activity', String(lastActivity));
        } catch (e) {}
        hideWarningModal();
      }
    })
    .catch(function() {
      // Error de red temporal
    });
  }

  function handleSessionTimeout() {
    hideWarningModal();
    window.location.href = 'index.php?r=auth/login&reason=timeout';
  }

  /**
   * Detección de navegación interna vs abandono de pestaña
   */
  document.addEventListener('click', function(e) {
    var link = e.target.closest('a');
    if (link && link.href) {
      // Si el enlace apunta al mismo host y no abre en target="_blank"
      var target = link.getAttribute('target');
      if (!target || target === '_self') {
        if (link.origin === window.location.origin) {
          isInternalNavigation = true;
        }
      }
    }
  }, true);

  document.addEventListener('submit', function() {
    isInternalNavigation = true;
  }, true);

  // Detectar atajos de recarga de página (F5, Ctrl+R, Cmd+R) para tratarlos como navegación interna
  window.addEventListener('keydown', function(e) {
    if (e.key === 'F5' || ((e.ctrlKey || e.metaKey) && (e.key === 'r' || e.key === 'R'))) {
      isInternalNavigation = true;
    }
  }, true);

  // Al cerrar la ventana o cambiar de página
  window.addEventListener('pagehide', function() {
    var remainingTabs = unregisterTab();

    // Si NO fue navegación interna y ya no quedan pestañas abiertas del sistema de nómina
    if (!isInternalNavigation && remainingTabs <= 0) {
      var token = getCsrfToken();
      if (token && navigator.sendBeacon) {
        var formData = new FormData();
        formData.append('csrf_token', token);
        navigator.sendBeacon('index.php?r=auth/beaconLogout', formData);
      }
    }
  });

  /**
   * Conectar botones del modal una vez que el DOM esté listo
   */
  document.addEventListener('DOMContentLoaded', function() {
    var btnKeepAlive = document.getElementById('btn-security-extend');
    var btnLogout = document.getElementById('btn-security-logout');

    if (btnKeepAlive) {
      btnKeepAlive.addEventListener('click', function(e) {
        e.preventDefault();
        pingSession();
      });
    }

    if (btnLogout) {
      btnLogout.addEventListener('click', function(e) {
        e.preventDefault();
        var logoutForm = document.getElementById('form-manual-logout');
        if (logoutForm) {
          logoutForm.submit();
        } else {
          window.location.href = 'index.php?r=auth/logout';
        }
      });
    }
  });
})();
