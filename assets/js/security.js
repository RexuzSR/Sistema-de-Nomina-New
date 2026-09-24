/**
 * Sistema de Nómina — Gestor de Seguridad del Cliente
 * Control de inactividad, renovación proactiva (Heartbeat Ping) y advertencia modal interactiva.
 */
(function() {
  'use strict';

  // Configuración de tiempos de sesión
  // Inactividad máxima: 30 minutos (1800 s), advertencia a los 28 minutos (faltando 120 s)
  var INACTIVITY_LIMIT = 1800;
  var WARNING_BEFORE = 120;
  var WARNING_TIME = INACTIVITY_LIMIT - WARNING_BEFORE; // 1680 s

  var lastActivity = Date.now();
  var lastServerPing = Date.now();
  var warningModalVisible = false;
  var countdownInterval = null;
  var secondsLeft = WARNING_BEFORE;

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
      var now = Date.now();
      for (var id in tabs) {
        if (now - tabs[id] >= 3600000) {
          delete tabs[id];
        }
      }
      localStorage.setItem('nomina_active_tabs', JSON.stringify(tabs));
    } catch (e) {}
  }

  registerTab();
  setInterval(registerTab, 15000);
  window.addEventListener('beforeunload', unregisterTab);

  /**
   * Detección de actividad del usuario con Heartbeat proactivo
   */
  var activityDebounce = 0;
  function onUserActivity() {
    var now = Date.now();
    lastActivity = now;

    // Heartbeat proactivo: si el usuario está interactuando y han pasado más de 3 minutos
    // desde el último ping, refrescar la sesión en el servidor silenciosamente
    if (now - lastServerPing > 180000) { // 3 minutos
      lastServerPing = now;
      pingSession();
    }

    if (now - activityDebounce < 5000) return; // Evitar saturar localStorage
    activityDebounce = now;

    try {
      localStorage.setItem('nomina_last_user_activity', String(now));
    } catch (e) {}

    // Si el modal de advertencia estaba visible y el usuario volvió a interactuar, renovar
    if (warningModalVisible) {
      pingSession();
    }
  }

  // Eventos de usuario para monitoreo de actividad
  ['mousemove', 'keydown', 'mousedown', 'touchstart', 'scroll', 'click'].forEach(function(evt) {
    window.addEventListener(evt, onUserActivity, { passive: true });
  });

  // Al regresar a la pestaña (foco o visibilidad activa), renovar de inmediato si la sesión está vigente
  function onTabWakeUp() {
    var elapsedSeconds = Math.floor((Date.now() - lastActivity) / 1000);
    if (elapsedSeconds < INACTIVITY_LIMIT) {
      lastActivity = Date.now();
      lastServerPing = Date.now();
      pingSession();
      if (warningModalVisible) {
        hideWarningModal();
      }
    }
  }
  window.addEventListener('focus', onTabWakeUp);
  document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
      onTabWakeUp();
    }
  });

  // Escuchar actividad desde otras pestañas del mismo navegador
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
      handleSessionTimeout();
    } else if (elapsedSeconds >= WARNING_TIME && !warningModalVisible) {
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
      var m = Math.floor(sec / 60);
      var s = sec % 60;
      timerEl.textContent = (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
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
        lastServerPing = Date.now();
        try {
          localStorage.setItem('nomina_last_user_activity', String(lastActivity));
        } catch (e) {}
        hideWarningModal();
      }
    })
    .catch(function() {
      // Tolerar errores transitorios de red sin interrumpir al usuario
    });
  }

  function handleSessionTimeout() {
    hideWarningModal();
    window.location.href = 'index.php?r=auth/login&reason=timeout';
  }

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
