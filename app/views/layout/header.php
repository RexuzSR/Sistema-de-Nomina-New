<?php
if (!isset($pageTitle)) { $pageTitle = 'Sistema de Nómina'; }
$currentRoute = $GLOBALS['activeRoute'] ?? $_GET['r'] ?? 'dashboard';

// Obtener preferencias y avatar del usuario en sesión
$uNombre = $_SESSION['user_nombre'] ?? $_SESSION['usuario'] ?? 'Usuario';
$uInicial = strtoupper(substr($uNombre, 0, 1));
$uAvatar = $_SESSION['user_avatar'] ?? '';

$dbPrefs = [];
if (!empty($_SESSION['user_preferencias'])) {
    $decoded = json_decode($_SESSION['user_preferencias'], true);
    if (is_array($decoded)) {
        $dbPrefs = $decoded;
    }
}
$dbHeaderColor = $dbPrefs['header_color'] ?? '';
$dbPrimaryColor = $dbPrefs['primary_color'] ?? '';
$dbNavLayout = $dbPrefs['nav_layout'] ?? 'header';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="<?= Security::csrfToken() ?>">
<meta name="csp-nonce" content="<?= Security::getCspNonce() ?>">
<title><?= htmlspecialchars($pageTitle) ?> - Nómina</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css?v=3.7">
<link rel="stylesheet" href="assets/css/paginator.css?v=1.0">
<?php if (empty($currentRoute) || strpos($currentRoute, 'dashboard') === 0): ?>
<link rel="stylesheet" href="assets/css/dashboard.css?v=2.8">
<?php elseif (strpos($currentRoute, 'nomina') === 0 || strpos($currentRoute, 'periodos') === 0): ?>
<link rel="stylesheet" href="assets/css/nomina.css">
<?php elseif (strpos($currentRoute, 'empleados') === 0): ?>
<link rel="stylesheet" href="assets/css/empleados.css?v=2.3">
<?php elseif (strpos($currentRoute, 'perfil') === 0): ?>
<link rel="stylesheet" href="assets/css/perfil.css?v=1.2">
<?php elseif (strpos($currentRoute, 'usuarios') === 0): ?>
<link rel="stylesheet" href="assets/css/usuarios.css?v=2.4">
<?php elseif (strpos($currentRoute, 'auditoria') === 0 || strpos($currentRoute, 'historial') === 0): ?>
<link rel="stylesheet" href="assets/css/auditoria.css?v=1.0">
<?php elseif (strpos($currentRoute, 'configuracion') === 0): ?>
<link rel="stylesheet" href="assets/css/configuracion.css?v=1.0">
<?php endif; ?>
<script nonce="<?= Security::getCspNonce() ?>">
(function() {
  if (localStorage.getItem('dash-theme') === 'dark') {
    document.documentElement.classList.add('dark-theme');
  }
  
  var dbHeader = <?= json_encode($dbHeaderColor) ?>;
  var dbPrimary = <?= json_encode($dbPrimaryColor) ?>;
  var dbLayout = <?= json_encode($dbNavLayout) ?>;

  var savedHeaderBg = localStorage.getItem('nomina-header-color') || dbHeader;
  var savedPrimaryColor = localStorage.getItem('nomina-primary-color') || dbPrimary;
  var savedLayout = localStorage.getItem('nomina-nav-layout') || dbLayout || 'header';

  if (savedHeaderBg) {
    document.documentElement.style.setProperty('--topbar-custom-bg', savedHeaderBg);
  }
  if (savedPrimaryColor) {
    document.documentElement.style.setProperty('--primary-custom-color', savedPrimaryColor);
  }
  if (savedLayout === 'sidebar') {
    document.documentElement.classList.add('layout-sidebar-mode');
    if (localStorage.getItem('nomina-sidebar-collapsed') === 'true') {
      document.documentElement.classList.add('sidebar-collapsed');
    }
  }
})();
</script>
</head>
<body>
<header class="topbar" id="appTopbar">
  <div class="topbar-left-group">
    <a href="index.php?r=dashboard" class="brand">
      <i class="fa-solid fa-calculator brand-icon"></i>
      <span class="brand-text">NÓMINA</span>
    </a>
    <span class="topbar-divider" aria-hidden="true"></span>
  </div>

  <!-- Grupo Central: Barra de búsqueda ejecutiva centrada con más contenido -->
  <div class="topbar-center-group" id="topbarCenterGroup">
    <button class="header-cmdk-trigger" onclick="openCommandPalette()" type="button" title="Buscar en el sistema (Ctrl + K)">
      <i class="fa-solid fa-magnifying-glass"></i>
      <span class="header-cmdk-text">Buscar empleados, nóminas, periodos…</span>
      <div class="header-cmdk-tags">
        <span class="cmdk-filter-pill pill-emp"><i class="fa-solid fa-users"></i> Empleados</span>
        <span class="cmdk-filter-pill pill-nom"><i class="fa-solid fa-receipt"></i> Nóminas</span>
        <kbd class="header-cmdk-kbd">Ctrl K</kbd>
      </div>
    </button>
  </div>
  
  <nav class="topbar-nav" id="topbarNav">
    <!-- Enlaces superiores (se ocultan automáticamente si el usuario activa el modo Sidebar) -->
    <div class="topbar-nav-links" id="topbarNavLinks">
      <a href="index.php?r=dashboard" class="nav-btn <?= strpos($currentRoute, 'dashboard') === 0 ? 'active' : '' ?>"><i class="fa-solid fa-house"></i> Inicio</a>
      <a href="index.php?r=empleados" class="nav-btn <?= strpos($currentRoute, 'empleados') === 0 ? 'active' : '' ?>"><i class="fa-solid fa-users"></i> Empleados</a>
      <a href="index.php?r=permisos" class="nav-btn <?= strpos($currentRoute, 'permisos') === 0 ? 'active' : '' ?>"><i class="fa-solid fa-file-signature"></i> Permisos</a>
      <a href="index.php?r=periodos" class="nav-btn <?= (strpos($currentRoute, 'periodos') === 0 || strpos($currentRoute, 'nomina') === 0) ? 'active' : '' ?>"><i class="fa-solid fa-calendar-days"></i> Periodos</a>
      <a href="index.php?r=tasas" class="nav-btn <?= strpos($currentRoute, 'tasas') === 0 ? 'active' : '' ?>"><i class="fa-solid fa-money-bill-transfer"></i> Tasa de cambio</a>
      <span class="nav-divider nav-divider-lg" aria-hidden="true"></span>
    </div>

    <!-- Botón Gema Celestial de Modo Día / Noche (Luminous Celestial Orb) -->
    <button class="header-theme-orb" id="headerThemeBtn" title="Alternar modo día / noche" onclick="toggleTheme()" type="button" aria-label="Alternar modo día / noche">
      <span class="theme-orb-icon-box">
        <i class="fa-solid fa-sun orb-icon sun-icon"></i>
        <i class="fa-solid fa-moon orb-icon moon-icon"></i>
      </span>
    </button>

    <?php if (!empty($_SESSION['usuario'])): ?>
      <div class="user-dropdown-container" id="userDropdown">
        <button class="userbox-toggle" id="userDropdownBtn" type="button" aria-expanded="false" aria-haspopup="true">
          <?php if (!empty($uAvatar)): ?>
            <span class="user-avatar-pill has-img"><img src="<?= htmlspecialchars($uAvatar) ?>" alt="<?= htmlspecialchars($uNombre) ?>"></span>
          <?php else: ?>
            <span class="user-avatar-pill"><?= $uInicial ?></span>
          <?php endif; ?>
          <span class="user-name"><?= htmlspecialchars($uNombre) ?></span>
          <i class="fa-solid fa-chevron-down arrow-icon"></i>
        </button>
        <div class="user-dropdown-menu" id="userDropdownMenu">
          <div class="dropdown-header-card">
            <?php if (!empty($uAvatar)): ?>
              <div class="u-card-avatar has-img"><img src="<?= htmlspecialchars($uAvatar) ?>" alt="<?= htmlspecialchars($uNombre) ?>"></div>
            <?php else: ?>
              <div class="u-card-avatar"><?= $uInicial ?></div>
            <?php endif; ?>
            <div class="u-card-details">
              <strong class="u-card-name"><?= htmlspecialchars($uNombre) ?></strong>
              <span class="u-card-status"><span class="status-dot"></span> Sesión Activa</span>
            </div>
          </div>
          <div class="dropdown-menu-list">
            <!-- Opción 'Editar Perfil' solicitada -->
            <a href="index.php?r=perfil" class="dropdown-item <?= strpos($currentRoute, 'perfil') === 0 ? 'active' : '' ?>">
              <span class="icon-box"><i class="fa-solid fa-user-gear"></i></span>
              <span>Editar Perfil</span>
            </a>
            <?php if (($_SESSION['user_rol'] ?? '') === 'Administrador'): ?>
            <a href="index.php?r=usuarios" class="dropdown-item <?= strpos($currentRoute, 'usuarios') === 0 ? 'active' : '' ?>">
              <span class="icon-box"><i class="fa-solid fa-users-gear"></i></span>
              <span>Gestión de Usuarios</span>
            </a>
            <a href="index.php?r=configuracion" class="dropdown-item <?= strpos($currentRoute, 'configuracion') === 0 ? 'active' : '' ?>">
              <span class="icon-box"><i class="fa-solid fa-sliders"></i></span>
              <span>Configuración General</span>
            </a>
            <?php endif; ?>
            <a href="index.php?r=auditoria" class="dropdown-item <?= (strpos($currentRoute, 'auditoria') === 0 || strpos($currentRoute, 'historial') === 0) ? 'active' : '' ?>">
              <span class="icon-box"><i class="fa-solid fa-clock-rotate-left"></i></span>
              <span>Historial</span>
            </a>
            <hr class="dropdown-divider">
            <form id="form-manual-logout" method="POST" action="index.php?r=auth/logout" style="margin: 0; padding: 0;">
              <?= Security::csrfField() ?>
              <button type="submit" class="dropdown-item logout-link" style="width: 100%; border: none; background: transparent; text-align: left; cursor: pointer; font-family: inherit; font-size: inherit;">
                <span class="icon-box logout-icon-box"><i class="fa-solid fa-power-off"></i></span>
                <span>Cerrar Sesión</span>
              </button>
            </form>
          </div>
        </div>
      </div>
      <script nonce="<?= Security::getCspNonce() ?>">
      (function() {
        var btn = document.getElementById('userDropdownBtn');
        var dropdown = document.getElementById('userDropdown');
        var menu = document.getElementById('userDropdownMenu');

        if (btn && dropdown && menu) {
          btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var isOpen = dropdown.classList.contains('open');
            if (isOpen) {
              dropdown.classList.remove('open');
              menu.classList.remove('show');
              btn.setAttribute('aria-expanded', 'false');
            } else {
              dropdown.classList.add('open');
              menu.classList.add('show');
              btn.setAttribute('aria-expanded', 'true');
            }
          });

          document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target)) {
              dropdown.classList.remove('open');
              menu.classList.remove('show');
              btn.setAttribute('aria-expanded', 'false');
            }
          });
        }
      })();
      </script>
    <?php endif; ?>
  </nav>

  <!-- Acciones visibles en barra para dispositivos móviles (tema + menú hamburguesa) -->
  <div class="topbar-mobile-actions">
    <button class="header-theme-orb" id="headerMobileThemeBtn" title="Alternar modo día / noche" onclick="toggleTheme()" type="button" aria-label="Alternar modo día / noche">
      <span class="theme-orb-icon-box">
        <i class="fa-solid fa-sun orb-icon sun-icon"></i>
        <i class="fa-solid fa-moon orb-icon moon-icon"></i>
      </span>
    </button>
    <button class="topbar-mobile-toggle" id="topbarMobileToggle" type="button" aria-label="Alternar menú de navegación" aria-expanded="false">
      <span class="hamburger-bar"></span>
      <span class="hamburger-bar"></span>
      <span class="hamburger-bar"></span>
    </button>
  </div>

  <script nonce="<?= Security::getCspNonce() ?>">
  (function() {
    var mobileToggle = document.getElementById('topbarMobileToggle');
    var topbarNav = document.getElementById('topbarNav');

    if (mobileToggle && topbarNav) {
      mobileToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        if (document.documentElement.classList.contains('layout-sidebar-mode')) {
          var isSideOpen = document.documentElement.classList.toggle('sidebar-mobile-open');
          mobileToggle.classList.toggle('is-active', isSideOpen);
          mobileToggle.setAttribute('aria-expanded', isSideOpen ? 'true' : 'false');
        } else {
          var isOpen = topbarNav.classList.toggle('mobile-open');
          mobileToggle.classList.toggle('is-active', isOpen);
          mobileToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        }
      });

      document.addEventListener('click', function(e) {
        if (document.documentElement.classList.contains('layout-sidebar-mode')) {
          var sidebar = document.getElementById('appSidebar');
          if (document.documentElement.classList.contains('sidebar-mobile-open') && sidebar && !sidebar.contains(e.target) && !mobileToggle.contains(e.target)) {
            document.documentElement.classList.remove('sidebar-mobile-open');
            mobileToggle.classList.remove('is-active');
            mobileToggle.setAttribute('aria-expanded', 'false');
          }
        } else {
          if (topbarNav.classList.contains('mobile-open') && !topbarNav.contains(e.target) && !mobileToggle.contains(e.target)) {
            topbarNav.classList.remove('mobile-open');
            mobileToggle.classList.remove('is-active');
            mobileToggle.setAttribute('aria-expanded', 'false');
          }
        }
      });
    }
  })();
  </script>
</header>

<!-- ═══════════════ SIDEBAR LATERAL EJECUTIVO (MODO SIDEBAR) ═══════════════ -->
<aside class="app-sidebar" id="appSidebar" aria-label="Navegación lateral del sistema">
  <div class="sidebar-header">
    <a href="index.php?r=dashboard" class="sidebar-brand" title="Ir al Inicio">
      <i class="fa-solid fa-calculator sidebar-brand-icon"></i>
      <span class="sidebar-brand-text">NÓMINA</span>
    </a>
    <button class="sidebar-desktop-toggle" id="sidebarDesktopToggle" type="button" title="Colapsar menú lateral" aria-label="Colapsar menú lateral">
      <i class="fa-solid fa-angles-left"></i>
    </button>
  </div>
  
  <div class="sidebar-menu-wrapper">
    <div class="sidebar-section-title">MENÚ PRINCIPAL</div>
    <ul class="sidebar-nav-list">
      <li>
        <a href="index.php?r=dashboard" class="sidebar-link <?= strpos($currentRoute, 'dashboard') === 0 ? 'active' : '' ?>" title="Inicio" data-title="Inicio">
          <i class="fa-solid fa-house"></i>
          <span>Inicio</span>
        </a>
      </li>
      <li>
        <a href="index.php?r=empleados" class="sidebar-link <?= strpos($currentRoute, 'empleados') === 0 ? 'active' : '' ?>" title="Empleados" data-title="Empleados">
          <i class="fa-solid fa-users"></i>
          <span>Empleados</span>
        </a>
      </li>
      <li>
        <a href="index.php?r=permisos" class="sidebar-link <?= strpos($currentRoute, 'permisos') === 0 ? 'active' : '' ?>" title="Permisos" data-title="Permisos">
          <i class="fa-solid fa-file-signature"></i>
          <span>Permisos</span>
        </a>
      </li>
      <li>
        <a href="index.php?r=periodos" class="sidebar-link <?= (strpos($currentRoute, 'periodos') === 0 || strpos($currentRoute, 'nomina') === 0) ? 'active' : '' ?>" title="Periodos" data-title="Periodos">
          <i class="fa-solid fa-calendar-days"></i>
          <span>Periodos</span>
        </a>
      </li>
      <li>
        <a href="index.php?r=tasas" class="sidebar-link <?= strpos($currentRoute, 'tasas') === 0 ? 'active' : '' ?>" title="Tasa de cambio" data-title="Tasa de cambio">
          <i class="fa-solid fa-money-bill-transfer"></i>
          <span>Tasa de cambio</span>
        </a>
      </li>
    </ul>

    <div class="sidebar-section-title" style="margin-top: 24px;">CUENTA Y AJUSTES</div>
    <ul class="sidebar-nav-list">
      <li>
        <a href="index.php?r=perfil" class="sidebar-link <?= strpos($currentRoute, 'perfil') === 0 ? 'active' : '' ?>" title="Editar Perfil" data-title="Editar Perfil">
          <i class="fa-solid fa-user-gear"></i>
          <span>Editar Perfil</span>
        </a>
      </li>
      <?php if (($_SESSION['user_rol'] ?? '') === 'Administrador'): ?>
      <li>
        <a href="index.php?r=usuarios" class="sidebar-link <?= strpos($currentRoute, 'usuarios') === 0 ? 'active' : '' ?>" title="Gestión de Usuarios" data-title="Gestión de Usuarios">
          <i class="fa-solid fa-users-gear"></i>
          <span>Gestión de Usuarios</span>
        </a>
      </li>
      <li>
        <a href="index.php?r=configuracion" class="sidebar-link <?= strpos($currentRoute, 'configuracion') === 0 ? 'active' : '' ?>" title="Configuraciones Generales" data-title="Configuración">
          <i class="fa-solid fa-sliders"></i>
          <span>Configuración</span>
        </a>
      </li>
      <?php endif; ?>
      <li>
        <a href="index.php?r=auditoria" class="sidebar-link <?= (strpos($currentRoute, 'auditoria') === 0 || strpos($currentRoute, 'historial') === 0) ? 'active' : '' ?>" title="Historial del Sistema" data-title="Historial">
          <i class="fa-solid fa-clock-rotate-left"></i>
          <span>Historial</span>
        </a>
      </li>
    </ul>
  </div>

</aside>

<script nonce="<?= Security::getCspNonce() ?>">
(function() {
  var collapseBtn = document.getElementById('sidebarCollapseBtn');
  var desktopToggle = document.getElementById('sidebarDesktopToggle');
  var sidebar = document.getElementById('appSidebar');

  function toggleSidebarCollapse() {
    if (window.innerWidth < 992) {
      document.documentElement.classList.toggle('sidebar-mobile-open');
    } else {
      var isCollapsed = document.documentElement.classList.toggle('sidebar-collapsed');
      localStorage.setItem('nomina-sidebar-collapsed', isCollapsed ? 'true' : 'false');
      if (desktopToggle) {
        desktopToggle.setAttribute('title', isCollapsed ? 'Expandir menú lateral' : 'Colapsar menú lateral');
        desktopToggle.setAttribute('aria-expanded', !isCollapsed);
      }
    }
  }

  if (collapseBtn) collapseBtn.addEventListener('click', toggleSidebarCollapse);
  if (desktopToggle) desktopToggle.addEventListener('click', toggleSidebarCollapse);

  document.addEventListener('click', function(e) {
    if (window.innerWidth < 992 && document.documentElement.classList.contains('sidebar-mobile-open')) {
      var mobileToggle = document.getElementById('topbarMobileToggle');
      if (sidebar && !sidebar.contains(e.target) && desktopToggle && !desktopToggle.contains(e.target) && (!mobileToggle || !mobileToggle.contains(e.target))) {
        document.documentElement.classList.remove('sidebar-mobile-open');
        if (mobileToggle) {
          mobileToggle.classList.remove('is-active');
          mobileToggle.setAttribute('aria-expanded', 'false');
        }
      }
    }
  });
})();
</script>

<main class="container">
