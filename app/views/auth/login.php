<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iniciar Sesión — Sistema de Nómina</title>
<link rel="icon" type="image/png" href="assets/images/logo_multitel.png?v=1">
<link rel="shortcut icon" type="image/png" href="assets/images/logo_multitel.png?v=1">
<link rel="apple-touch-icon" href="assets/images/logo_multitel.png?v=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css?v=2.5">
<link rel="stylesheet" href="assets/css/dashboard.css?v=2.5">
<link rel="stylesheet" href="assets/css/auth.css?v=2.7">
<script>
(function() {
  if (localStorage.getItem('dash-theme') === 'dark') {
    document.documentElement.classList.add('dark-theme');
  }
})();
</script>
</head>
<body class="mac-login-body">
  <!-- macOS Top Bar -->
  <div class="mac-topbar">
    <div class="mac-topbar-left">
      <span class="mac-app-name">Sistema de Nómina</span>
    </div>
    <div class="mac-topbar-right">
      <span id="mac-wifi-icon" class="mac-status-icon online" title="Conectado a internet">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
          <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
          <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
          <line x1="12" y1="20" x2="12.01" y2="20"></line>
        </svg>
      </span>
      <span id="mac-clock">Lun 10 Ago 11:47:00 AM</span>
    </div>
  </div>

  <div class="mac-login-viewport" id="login-viewport">
    <div class="dashboard-door-preview" id="dashboard-door-preview">
      <header class="topbar">
        <div class="topbar-left-group">
          <div class="brand">
            <img src="assets/images/logo_multitel.png" alt="Multitel" class="brand-logo-img">
            <span class="brand-text">MULTI<span class="brand-text-accent">TEL</span></span>
          </div>
          <span class="topbar-divider" aria-hidden="true"></span>
          <div class="header-cmdk-trigger" style="pointer-events: none;">
            <i class="fa-solid fa-magnifying-glass"></i>
            <span class="header-cmdk-text">Buscar empleados, nóminas, periodos…</span>
            <kbd class="header-cmdk-kbd">Ctrl K</kbd>
          </div>
        </div>
        <nav class="topbar-nav">
          <a href="#" class="nav-btn active"><i class="fa-solid fa-house"></i> Inicio</a>
          <span class="nav-divider" aria-hidden="true"></span>
          <a href="#" class="nav-btn"><i class="fa-solid fa-users"></i> Empleados</a>
          <span class="nav-divider" aria-hidden="true"></span>
          <a href="#" class="nav-btn"><i class="fa-solid fa-file-signature"></i> Permisos</a>
          <span class="nav-divider" aria-hidden="true"></span>
          <a href="#" class="nav-btn"><i class="fa-solid fa-calendar-days"></i> Periodos</a>
          <span class="nav-divider" aria-hidden="true"></span>
          <a href="#" class="nav-btn"><i class="fa-solid fa-money-bill-transfer"></i> Tasa de cambio</a>
          <span class="nav-divider nav-divider-lg" aria-hidden="true"></span>
          <button class="header-theme-toggle" type="button" aria-label="Modo oscuro" style="pointer-events: none;">
            <span class="theme-icon-viewport">
              <i class="fa-solid fa-moon theme-icon moon-icon"></i>
            </span>
          </button>
          <div class="user-dropdown-container">
            <button class="userbox-toggle" type="button">
              <span class="user-avatar-pill" id="preview-user-initial">S</span>
              <span class="user-name" id="preview-user-name">samu</span>
              <i class="fa-solid fa-chevron-down arrow-icon"></i>
            </button>
          </div>
        </nav>
      </header>
      <main class="container">
        <!-- ═══════════════ FONDO AURORA ANIMADO ═══════════════ -->
        <div class="aurora-bg" aria-hidden="true">
          <div class="aurora-blob aurora-1"></div>
          <div class="aurora-blob aurora-2"></div>
          <div class="aurora-blob aurora-3"></div>
          <div class="aurora-grid"></div>
        </div>

        <!-- ═══════════════ HERO EJECUTIVO ═══════════════ -->
        <section class="hero">
          <div class="hero-content">
            <div class="hero-left">
              <div class="hero-eyebrow">
                <span class="live-dot"></span>
                <span>Sistema en vivo</span>
                <span class="hero-sep">·</span>
                <span id="heroClock"><?= date('d M Y · H:i') ?></span>
              </div>
              <h1 class="hero-title">
                Bienvenido,<br>
                <span class="hero-gradient">aquí está tu operación</span>
              </h1>
              <p class="hero-subtitle">
                Tienes <strong><?= $permisosPendientes ?></strong> permisos por aprobar 
                y <strong><?= $totalNominasCount ?></strong> nóminas procesadas en el histórico.
              </p>
            </div>
            <div class="hero-right">
              <!-- Selector USD / Bs. -->
              <div class="currency-toggle hero-currency-toggle">
                <button class="cur-btn active" type="button">USD</button>
                <button class="cur-btn" type="button">Bs.</button>
              </div>

              <!-- Tasa BCV como tarjeta destacada -->
              <div class="bcv-card">
                <div class="bcv-card-top">
                  <span class="bcv-card-label">
                    <i class="fa-solid fa-coins"></i> TASA BCV
                  </span>
                  <span class="bcv-card-sync">
                    <i class="fa-solid fa-arrows-rotate"></i>
                  </span>
                </div>
                <div class="bcv-card-value">
                  <?= $valorTasaBcv > 0 ? number_format($valorTasaBcv, 4, ',', '.') : '—' ?>
                  <span class="bcv-card-unit">Bs./$</span>
                </div>
                <div class="bcv-card-foot">
                  <i class="fa-regular fa-calendar"></i>
                  <?= $fechaTasaBcv ? date('d/m/Y', strtotime($fechaTasaBcv)) : 'Pendiente' ?>
                </div>
              </div>
            </div>
          </div>

          <!-- Acciones rápidas flotantes -->
          <div class="hero-quick-actions">
            <div class="quick-action">
              <span class="qa-icon" style="background:linear-gradient(135deg,#10b981,#059669);">
                <i class="fa-solid fa-user-plus"></i>
              </span>
              <span class="qa-text">
                <strong>Nuevo empleado</strong>
                <small>Registrar en plantilla</small>
              </span>
              <i class="fa-solid fa-arrow-right qa-arrow"></i>
            </div>
            <div class="quick-action">
              <span class="qa-icon" style="background:linear-gradient(135deg,#0284c7,#0369a1);">
                <i class="fa-solid fa-calendar-plus"></i>
              </span>
              <span class="qa-text">
                <strong>Crear periodo</strong>
                <small>Nuevo ciclo de nómina</small>
              </span>
              <i class="fa-solid fa-arrow-right qa-arrow"></i>
            </div>
            <div class="quick-action">
              <span class="qa-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);">
                <i class="fa-solid fa-bolt"></i>
              </span>
              <span class="qa-text">
                <strong>Generar nómina</strong>
                <small>Procesar liquidación</small>
              </span>
              <i class="fa-solid fa-arrow-right qa-arrow"></i>
            </div>
          </div>
        </section>

        <!-- ═══════════════ KPI GRID CON SPARKLINES ═══════════════ -->
        <section class="kpi-grid">
          <!-- KPI 1: Última Nómina -->
          <div class="kpi-card kpi-emerald">
            <div class="kpi-glow"></div>
            <div class="kpi-head">
              <div class="kpi-icon" style="background:linear-gradient(135deg,#10b981,#059669);">
                <i class="fa-solid fa-money-bill-wave"></i>
              </div>
              <span class="kpi-tag kpi-tag-neutral">
                <i class="fa-solid fa-check"></i> Ciclo vigente
              </span>
            </div>
            <div class="kpi-label">Última nómina procesada</div>
            <div class="kpi-value usd-value">
              <span class="kpi-cur">$</span><?= number_format($netoEfectivo, 2, ',', '.') ?>
            </div>
            <div class="kpi-secondary usd-value">
              ≈ Bs. <?= number_format($netoEfectivoBs, 2, ',', '.') ?>
            </div>
            <svg class="kpi-spark" viewBox="0 0 100 30" preserveAspectRatio="none">
              <defs>
                <linearGradient id="sparkGreen" x1="0" x2="0" y1="0" y2="1">
                  <stop offset="0%" stop-color="#10b981" stop-opacity="0.35"/>
                  <stop offset="100%" stop-color="#10b981" stop-opacity="0"/>
                </linearGradient>
              </defs>
              <path d="M0,22 L15,18 L30,20 L45,12 L60,15 L75,8 L100,4 L100,30 L0,30 Z" fill="url(#sparkGreen)"/>
              <path d="M0,22 L15,18 L30,20 L45,12 L60,15 L75,8 L100,4" fill="none" stroke="#10b981" stroke-width="2"/>
            </svg>
            <div class="kpi-foot">
              <span><i class="fa-solid fa-users"></i> <?= $resumenFinanciero['trabajadores_procesados'] ?? $totalEmpleados ?> trabajadores</span>
              <i class="fa-solid fa-arrow-right kpi-arrow"></i>
            </div>
          </div>

          <!-- KPI 2: Plantilla -->
          <div class="kpi-card kpi-sky">
            <div class="kpi-glow"></div>
            <div class="kpi-head">
              <div class="kpi-icon" style="background:linear-gradient(135deg,#38bdf8,#0284c7);">
                <i class="fa-solid fa-users"></i>
              </div>
              <span class="kpi-tag kpi-tag-info">
                <?= $totalInactivos ?> inactivos
              </span>
            </div>
            <div class="kpi-label">Plantilla activa</div>
            <div class="kpi-value">
              <?= $totalEmpleados ?><span class="kpi-unit">empleados</span>
            </div>
            <div class="kpi-secondary">
              <span style="color:#0284c7;font-weight:700;">+<?= $empleadosAgregadosEsteMes ?></span> ingresos este mes
            </div>
            <svg class="kpi-spark" viewBox="0 0 100 30" preserveAspectRatio="none">
              <defs>
                <linearGradient id="sparkBlue" x1="0" x2="0" y1="0" y2="1">
                  <stop offset="0%" stop-color="#0284c7" stop-opacity="0.35"/>
                  <stop offset="100%" stop-color="#0284c7" stop-opacity="0"/>
                </linearGradient>
              </defs>
              <path d="M0,24 L20,22 L40,16 L60,18 L80,10 L100,6 L100,30 L0,30 Z" fill="url(#sparkBlue)"/>
              <path d="M0,24 L20,22 L40,16 L60,18 L80,10 L100,6" fill="none" stroke="#0284c7" stroke-width="2"/>
            </svg>
            <div class="kpi-foot">
              <span><i class="fa-solid fa-user-check"></i> Ver plantilla completa</span>
              <i class="fa-solid fa-arrow-right kpi-arrow"></i>
            </div>
          </div>

          <!-- KPI 3: Acumulado -->
          <div class="kpi-card kpi-violet">
            <div class="kpi-glow"></div>
            <div class="kpi-head">
              <div class="kpi-icon" style="background:linear-gradient(135deg,#a78bfa,#7c3aed);">
                <i class="fa-solid fa-vault"></i>
              </div>
              <span class="kpi-tag kpi-tag-purple">
                <i class="fa-solid fa-infinity"></i> Histórico
              </span>
            </div>
            <div class="kpi-label">Acumulado total</div>
            <div class="kpi-value usd-value">
              <span class="kpi-cur">$</span><?= number_format(max(0.0, $montoTotalHistorico), 2, ',', '.') ?>
            </div>
            <div class="kpi-secondary usd-value" style="color:#7c3aed;">
              ≈ Bs. <?= number_format(max(0.0, $montoTotalHistoricoBs), 2, ',', '.') ?>
            </div>
            <svg class="kpi-spark" viewBox="0 0 100 30" preserveAspectRatio="none">
              <defs>
                <linearGradient id="sparkPurple" x1="0" x2="0" y1="0" y2="1">
                  <stop offset="0%" stop-color="#8b5cf6" stop-opacity="0.35"/>
                  <stop offset="100%" stop-color="#8b5cf6" stop-opacity="0"/>
                </linearGradient>
              </defs>
              <path d="M0,20 L25,18 L50,14 L75,8 L100,4 L100,30 L0,30 Z" fill="url(#sparkPurple)"/>
              <path d="M0,20 L25,18 L50,14 L75,8 L100,4" fill="none" stroke="#8b5cf6" stroke-width="2"/>
            </svg>
            <div class="kpi-foot">
              <span>Pagos acumulados</span>
              <i class="fa-solid fa-arrow-right kpi-arrow"></i>
            </div>
          </div>
        </section>
      </main>
    </div>

    <div class="portal-bloom" id="portal-bloom"></div>
    <div class="mac-window<?= !empty($error) ? ' no-entry-anim' : '' ?>" id="login-form-card">
      <div class="mac-window-body">
        <div class="mac-avatar-container">
          <div class="mac-avatar">
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
          </div>
          <span class="mac-avatar-badge" title="En línea"></span>
        </div>

        <h1 class="mac-title">Sistema de Nómina</h1>
        <p class="mac-subtitle">Ingresa tus credenciales para continuar</p>

        <?php
        $reason = $_GET['reason'] ?? '';
        $reasonMsg = '';
        $reasonType = 'info';
        if ($reason === 'timeout') {
            $reasonMsg = 'Tu sesión ha expirado por inactividad. Por favor, ingresa tus credenciales nuevamente.';
            $reasonType = 'warning';
        } elseif ($reason === 'logout') {
            $reasonMsg = 'Has cerrado sesión correctamente.';
            $reasonType = 'info';
        } elseif ($reason === 'security') {
            $reasonMsg = 'Tu sesión se cerró por motivos de seguridad.';
            $reasonType = 'warning';
        } elseif ($reason === 'lockout') {
            $reasonMsg = 'Acceso bloqueado temporalmente por demasiados intentos fallidos.';
            $reasonType = 'error';
        }
        ?>

        <form method="post" action="index.php?r=auth/login" class="mac-form" id="mac-login-form">
          <?= Security::csrfField() ?>
          <div class="mac-input-group">
            <label for="username">Usuario</label>
            <div class="mac-input-wrapper">
              <svg class="mac-field-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              <input type="text" id="username" name="username" placeholder="Ej. admin" required autofocus autocomplete="username">
            </div>
          </div>

          <div class="mac-input-group">
            <label for="password">Contraseña</label>
            <div class="mac-input-wrapper">
              <svg class="mac-field-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
              <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
              <button type="button" class="mac-toggle-pw" aria-label="Mostrar contraseña" onclick="togglePasswordVisibility()">
                <svg id="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </button>
            </div>
          </div>

          <button class="mac-btn-primary" type="submit" id="btn-submit-login">
            <span>Entrar al Sistema</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </button>
        </form>
      </div>
    </div>
  </div>

  <script nonce="<?= Security::getCspNonce() ?>">
    function togglePasswordVisibility() {
      const pwInput = document.getElementById('password');
      const eyeIcon = document.getElementById('eye-icon');
      if (pwInput.type === 'password') {
        pwInput.type = 'text';
        eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
      } else {
        pwInput.type = 'password';
        eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
      }
    }

    function updateMacClock() {
      const now = new Date();
      const options = { weekday: 'short', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
      const str = now.toLocaleDateString('es-ES', options).replace('.', '');
      document.getElementById('mac-clock').textContent = str;
    }
    updateMacClock();
    setInterval(updateMacClock, 1000);

    // Sistema de Notificaciones Toast flotantes estilo macOS
    function showMacToast(msg, type, duration) {
      if (!msg) return;
      type = type || 'info';
      duration = typeof duration === 'number' ? duration : 4500;

      let container = document.getElementById('mac-toast-container');
      if (!container) {
        container = document.createElement('div');
        container.id = 'mac-toast-container';
        container.className = 'mac-toast-container';
        document.body.appendChild(container);
      }

      const toast = document.createElement('div');
      toast.className = 'mac-toast mac-toast-' + type;

      let iconSvg = '';
      if (type === 'error') {
        iconSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>';
      } else if (type === 'warning') {
        iconSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
      } else {
        iconSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
      }

      toast.innerHTML = 
        '<div class="mac-toast-icon">' + iconSvg + '</div>' +
        '<div class="mac-toast-text">' + escapeHtml(msg) + '</div>' +
        '<button type="button" class="mac-toast-close" aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>';

      container.appendChild(toast);
      requestAnimationFrame(function() {
        toast.classList.add('show');
      });

      function dismiss() {
        toast.classList.remove('show');
        toast.classList.add('hide');
        setTimeout(function() {
          if (toast.parentNode) toast.remove();
        }, 400);
      }

      var closeBtn = toast.querySelector('.mac-toast-close');
      if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          dismiss();
        });
      }
      toast.addEventListener('click', dismiss);

      if (duration > 0) {
        setTimeout(dismiss, duration);
      }
    }

    // Disparar mensaje inicial (si existe en sesión o redirección)
    document.addEventListener('DOMContentLoaded', function() {
      <?php if (!empty($error)): ?>
        showMacToast(<?= json_encode($error) ?>, 'error');
      <?php elseif (!empty($reasonMsg)): ?>
        showMacToast(<?= json_encode($reasonMsg) ?>, <?= json_encode($reasonType) ?>);
      <?php endif; ?>
    });

    const loginForm = document.getElementById('mac-login-form');
    const submitBtn = document.getElementById('btn-submit-login');
    const submitBtnOriginalHtml = submitBtn.innerHTML;

    loginForm.addEventListener('submit', function(e) {
      if (loginForm.dataset.submitting === 'true') return;
      e.preventDefault();
      loginForm.dataset.submitting = 'true';

      const usernameVal = document.getElementById('username').value.trim();
      const passwordVal = document.getElementById('password').value;
      const card = document.getElementById('login-form-card');
      const userInput = document.getElementById('username');
      const passInput = document.getElementById('password');

      // Feedback inmediato: Estado de carga con spinner y bloqueo suave de campos
      submitBtn.classList.add('is-loading');
      submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch btn-spinner"></i> <span>Verificando credenciales…</span>';
      userInput.readOnly = true;
      passInput.readOnly = true;

      const formData = new FormData(loginForm);
      formData.set('username', usernameVal);
      formData.set('password', passwordVal);

      fetch(loginForm.action, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Estado de éxito en el botón
          submitBtn.classList.remove('is-loading');
          submitBtn.classList.add('is-success');
          submitBtn.innerHTML = '<i class="fa-solid fa-check"></i> <span>¡Acceso Concedido!</span>';

          const preview = document.getElementById('dashboard-door-preview');
          const bloom = document.getElementById('portal-bloom');

          // Actualizar datos de usuario en la vista previa del header
          const uInitialEl = document.getElementById('preview-user-initial');
          const uNameEl = document.getElementById('preview-user-name');
          if (uInitialEl && usernameVal) uInitialEl.textContent = usernameVal.charAt(0).toUpperCase();
          if (uNameEl && usernameVal) uNameEl.textContent = usernameVal;

          // Ocultar suavemente la barra superior de macOS
          const macTopbar = document.querySelector('.mac-topbar');
          if (macTopbar) macTopbar.classList.add('fade-out-topbar');

          // Quitar no-entry-anim si estuviera presente para que la animación de salida funcione
          card.classList.remove('no-entry-anim');

          const viewport = document.getElementById('login-viewport');
          viewport.classList.add('door-perspective-active');
          card.classList.add('door-opening-swing');
          preview.classList.add('preview-reveal');

          setTimeout(function() {
            viewport.classList.add('camera-door-flythrough');
            preview.classList.add('preview-zoom-immersion');
          }, 300);

          setTimeout(function() {
            window.location.href = data.redirect;
          }, 1050);
        } else {
          loginForm.dataset.submitting = 'false';
          userInput.readOnly = false;
          passInput.readOnly = false;

          // Restaurar botón
          submitBtn.classList.remove('is-loading');
          submitBtn.innerHTML = submitBtnOriginalHtml;

          // Animación de sacudida táctil elástica macOS en la tarjeta
          card.classList.remove('card-shake');
          void card.offsetWidth; // Forzar reflow para reiniciar la animación
          card.classList.add('card-shake');

          // Resaltar error en el campo de contraseña y seleccionarlo para corrección rápida
          const pwWrapper = passInput.closest('.mac-input-wrapper');
          if (pwWrapper) {
            pwWrapper.classList.add('input-error');
            setTimeout(() => pwWrapper.classList.remove('input-error'), 2200);
          }
          passInput.select();

          // Notificación Toast flotante automática
          showMacToast(data.error || 'Credenciales incorrectas.', 'error');
        }
      })
      .catch(err => {
        console.error(err);
        loginForm.dataset.submitting = 'false';
        userInput.readOnly = false;
        passInput.readOnly = false;
        submitBtn.classList.remove('is-loading');
        submitBtn.innerHTML = submitBtnOriginalHtml;
        showMacToast('Ocurrió un error al procesar el inicio de sesión.', 'error');
      });
    });

    function escapeHtml(str) {
      return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
    }
  </script>
</body>
</html>
