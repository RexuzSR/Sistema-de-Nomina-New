<?php
if (!isset($pageTitle)) { $pageTitle = 'Sistema de Nómina'; }
$currentRoute = $_GET['r'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?> - Nómina</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="topbar">
  <a href="index.php?r=dashboard" class="brand">
    <i class="fa-solid fa-calculator brand-icon"></i>
    <span class="brand-text">NÓMINA</span>
  </a>
  <nav class="topbar-nav">
    <a href="index.php?r=dashboard" class="nav-btn <?= strpos($currentRoute, 'dashboard') === 0 ? 'active' : '' ?>"><i class="fa-solid fa-house"></i> Inicio</a>
    <a href="index.php?r=empleados" class="nav-btn <?= strpos($currentRoute, 'empleados') === 0 ? 'active' : '' ?>"><i class="fa-solid fa-users"></i> Empleados</a>
    <a href="index.php?r=permisos" class="nav-btn <?= strpos($currentRoute, 'permisos') === 0 ? 'active' : '' ?>"><i class="fa-solid fa-file-signature"></i> Permisos</a>
    <a href="index.php?r=periodos" class="nav-btn <?= (strpos($currentRoute, 'periodos') === 0 || strpos($currentRoute, 'nomina') === 0) ? 'active' : '' ?>"><i class="fa-solid fa-calendar-days"></i> Periodos</a>
    <a href="index.php?r=tasas" class="nav-btn <?= strpos($currentRoute, 'tasas') === 0 ? 'active' : '' ?>"><i class="fa-solid fa-money-bill-transfer"></i> Tasa de cambio</a>
    <?php if (!empty($_SESSION['usuario'])):
      $uNombre = $_SESSION['user_nombre'] ?? $_SESSION['usuario'];
      $uInicial = strtoupper(substr($uNombre, 0, 1));
    ?>
      <div class="user-dropdown-container" id="userDropdown">
        <button class="userbox-toggle" onclick="toggleUserDropdown(event)" type="button">
          <span class="user-avatar-pill"><?= $uInicial ?></span>
          <span class="user-name"><?= htmlspecialchars($uNombre) ?></span>
          <i class="fa-solid fa-chevron-down arrow-icon"></i>
        </button>
        <div class="user-dropdown-menu" id="userDropdownMenu">
          <div class="dropdown-header-card">
            <div class="u-card-avatar"><?= $uInicial ?></div>
            <div class="u-card-details">
              <strong class="u-card-name"><?= htmlspecialchars($uNombre) ?></strong>
              <span class="u-card-status"><span class="status-dot"></span> Sesión Activa</span>
            </div>
          </div>
          <div class="dropdown-menu-list">
            <a href="index.php?r=dashboard" class="dropdown-item">
              <span class="icon-box"><i class="fa-solid fa-gauge-high"></i></span>
              <span>Tablero Principal</span>
            </a>
            <a href="index.php?r=periodos" class="dropdown-item">
              <span class="icon-box"><i class="fa-solid fa-calendar-check"></i></span>
              <span>Gestión de Periodos</span>
            </a>
            <a href="index.php?r=tasas" class="dropdown-item">
              <span class="icon-box"><i class="fa-solid fa-coins"></i></span>
              <span>Tasa de Cambio</span>
            </a>
            <hr class="dropdown-divider">
            <a href="index.php?r=auth/logout" class="dropdown-item logout-link">
              <span class="icon-box logout-icon-box"><i class="fa-solid fa-power-off"></i></span>
              <span>Cerrar Sesión</span>
            </a>
          </div>
        </div>
      </div>
      <script>
      function toggleUserDropdown(e) {
        e.stopPropagation();
        var dropdown = document.getElementById('userDropdown');
        var menu = document.getElementById('userDropdownMenu');
        dropdown.classList.toggle('open');
        menu.classList.toggle('show');
      }
      document.addEventListener('click', function(e) {
        var dropdown = document.getElementById('userDropdown');
        var menu = document.getElementById('userDropdownMenu');
        if (dropdown && !dropdown.contains(e.target) && menu) {
          dropdown.classList.remove('open');
          menu.classList.remove('show');
        }
      });
      </script>
    <?php endif; ?>
  </nav>
</header>
<main class="container">
<h1><?= htmlspecialchars($pageTitle) ?></h1>
