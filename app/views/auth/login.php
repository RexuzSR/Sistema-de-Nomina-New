<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iniciar Sesión — Sistema de Nómina</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
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
        <div class="brand">
          <i class="fa-solid fa-calculator brand-icon"></i>
          <span class="brand-text">NÓMINA</span>
        </div>
        <nav class="topbar-nav">
          <a href="#" class="nav-btn active"><i class="fa-solid fa-house"></i> Inicio</a>
          <a href="#" class="nav-btn"><i class="fa-solid fa-users"></i> Empleados</a>
          <a href="#" class="nav-btn"><i class="fa-solid fa-file-signature"></i> Permisos</a>
          <a href="#" class="nav-btn"><i class="fa-solid fa-calendar-days"></i> Periodos</a>
          <a href="#" class="nav-btn"><i class="fa-solid fa-money-bill-transfer"></i> Tasa de cambio</a>
          <div class="user-dropdown-container">
            <button class="userbox-toggle" type="button">
              <span class="user-avatar-pill" id="preview-user-initial">S</span>
              <span class="user-name" id="preview-user-name">samu</span>
              <i class="fa-solid fa-chevron-down arrow-icon"></i>
            </button>
          </div>
        </nav>
      </header>
      <main class="container" style="margin-top: 15px;">
        <h1 style="margin-bottom: 20px;">Tablero de Inicio</h1>
        
        <div class="dashboard-grid">
          <!-- Card 1: Empleados Activos -->
          <div class="stat-card">
            <div>
              <div class="stat-label">Empleados Activos</div>
              <div class="stat-value"><?= $totalEmpleados ?></div>
            </div>
            <div class="stat-desc"><?= $totalInactivos ?> empleados inactivos registrados</div>
          </div>

          <!-- Card 2: Nuevos Ingresos -->
          <div class="stat-card">
            <div>
              <div class="stat-label">Ingresos del Mes</div>
              <div class="stat-value"><?= $empleadosAgregadosEsteMes ?></div>
            </div>
            <div class="stat-desc">Empleados registrados en el mes actual</div>
          </div>

          <!-- Card 3: Nóminas Generadas -->
          <div class="stat-card">
            <div>
              <div class="stat-label">Nóminas Generadas</div>
              <div class="stat-value"><?= $totalNominasCount ?></div>
            </div>
            <div class="stat-desc">Periodos con nómina procesada</div>
          </div>

          <!-- Card 4: Costo Última Nómina -->
          <div class="stat-card">
            <div>
              <div class="stat-label">Última Nómina</div>
              <div class="stat-value">$<?= number_format($montoUltimaNomina, 2, ',', '.') ?></div>
            </div>
            <div class="stat-desc">Monto neto total pagado en USD</div>
          </div>

          <!-- Card 5: Costo Histórico -->
          <div class="stat-card">
            <div>
              <div class="stat-label">Total Histórico</div>
              <div class="stat-value">$<?= number_format($montoTotalHistorico, 2, ',', '.') ?></div>
            </div>
            <div class="stat-desc">Pagos acumulados registrados</div>
          </div>
        </div>

        <div class="card" style="margin-bottom: 30px;">
          <h2>Resumen Operativo</h2>
          <p>Tienes <strong><?= $permisosPendientes ?></strong> solicitudes de permiso pendientes por aprobar.</p>
          <?php if (!empty($ultimoPeriodo)): ?>
            <p>Último periodo registrado en el sistema: <strong><?= htmlspecialchars($ultimoPeriodo['fecha_inicio']) ?> al <?= htmlspecialchars($ultimoPeriodo['fecha_fin']) ?></strong></p>
          <?php endif; ?>
        </div>

        <div class="actions-row" style="margin-top: 25px;">
          <a class="btn" href="#">+ Nuevo empleado</a>
          <a class="btn secondary" href="#">Ver permisos</a>
          <a class="btn secondary" href="#">Ver todos los Periodos / Nóminas</a>
        </div>
      </main>
    </div>

    <div class="door-portal-glow" id="door-portal-glow"></div>
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

        <div id="mac-alert-container">
          <?php if (!empty($error)): ?>
            <div class="mac-alert mac-alert-error">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
              <span><?= htmlspecialchars($error) ?></span>
            </div>
          <?php endif; ?>
        </div>

        <form method="post" action="index.php?r=auth/login" class="mac-form" id="mac-login-form">
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

  <script>
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

    const loginForm = document.getElementById('mac-login-form');
    loginForm.addEventListener('submit', function(e) {
      if (loginForm.dataset.submitting === 'true') return;
      e.preventDefault();
      loginForm.dataset.submitting = 'true';

      const usernameVal = document.getElementById('username').value.trim();
      const passwordVal = document.getElementById('password').value;
      const card = document.getElementById('login-form-card');

      const formData = new FormData();
      formData.append('username', usernameVal);
      formData.append('password', passwordVal);

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
          const viewport = document.getElementById('login-viewport');
          const preview = document.getElementById('dashboard-door-preview');

          // Actualizar datos de usuario en la vista previa del header para lograr coincidencia 1-a-1 perfecta
          const uInitialEl = document.getElementById('preview-user-initial');
          const uNameEl = document.getElementById('preview-user-name');
          if (uInitialEl && usernameVal) uInitialEl.textContent = usernameVal.charAt(0).toUpperCase();
          if (uNameEl && usernameVal) uNameEl.textContent = usernameVal;

          // Ocultar suavemente la barra superior de macOS para evitar cualquier superposición o choque visual con la cabecera
          const macTopbar = document.querySelector('.mac-topbar');
          if (macTopbar) macTopbar.classList.add('fade-out-topbar');

          // Quitar no-entry-anim si estuviera presente para que la animación de salida funcione
          card.classList.remove('no-entry-anim');

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
          
          // Desactivar animaciones de entrada/salida para el modal
          card.classList.add('no-entry-anim');
          
          const alertContainer = document.getElementById('mac-alert-container');
          alertContainer.innerHTML = `
            <div class="mac-alert mac-alert-error">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
              <span>${escapeHtml(data.error)}</span>
            </div>
          `;
          
          // Reiniciar animación de vibración (shake) del cuadro de alerta
          const alertEl = alertContainer.querySelector('.mac-alert');
          alertEl.style.animation = 'none';
          alertEl.offsetHeight; // Forzar reflujo
          alertEl.style.animation = '';
        }
      })
      .catch(err => {
        console.error(err);
        loginForm.dataset.submitting = 'false';
        alert('Ocurrió un error al procesar el inicio de sesión.');
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
