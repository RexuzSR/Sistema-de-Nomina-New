<?php
/**
 * Vista de Edición de Perfil de Usuario, Personalización de Header/Botones y Selector de Layout con Skeletons
 */
$nombre = $user['nombre_usuario'] ?? 'Usuario';
$correo = $user['correo'] ?? '';
$rol = $user['nombre_rol'] ?? 'Administrador';
$foto = $user['foto_perfil'] ?? '';
$inicial = strtoupper(substr($nombre, 0, 1));
$fechaCreacion = !empty($user['fecha_creacion']) ? date('d/m/Y', strtotime($user['fecha_creacion'])) : 'No registrada';
?>

<div class="perfil-page">
  <!-- Feedback Messages -->
  <?php if (!empty($msgSuccess)): ?>
    <div class="alert alert-success" style="background:#dcfce7; color:#166534; border:1px solid #bbf7d0; border-radius:12px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
      <i class="fa-solid fa-circle-check" style="font-size:18px;"></i>
      <span><?= htmlspecialchars($msgSuccess) ?></span>
    </div>
  <?php endif; ?>

  <?php if (!empty($msgError)): ?>
    <div class="alert alert-danger" style="background:#fee2e2; color:#991b1b; border:1px solid #fecaca; border-radius:12px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
      <i class="fa-solid fa-triangle-exclamation" style="font-size:18px;"></i>
      <span><?= htmlspecialchars($msgError) ?></span>
    </div>
  <?php endif; ?>

  <!-- ═══════════════ HERO BANNER DE PERFIL ═══════════════ -->
  <div class="perfil-hero">
    <div class="perfil-hero-left">
      <div class="perfil-avatar-wrapper">
        <?php if (!empty($foto)): ?>
          <img src="<?= htmlspecialchars($foto) ?>" alt="<?= htmlspecialchars($nombre) ?>" class="perfil-avatar-img" id="heroAvatarImg">
        <?php else: ?>
          <div class="perfil-avatar-img" id="heroAvatarFallback"><?= $inicial ?></div>
        <?php endif; ?>
        <label for="fotoInputFile" class="perfil-avatar-edit-badge" title="Cambiar foto de perfil">
          <i class="fa-solid fa-camera"></i>
        </label>
      </div>
      <div class="perfil-user-meta">
        <h1 id="heroUsernameDisplay"><?= htmlspecialchars($nombre) ?></h1>
        <div class="perfil-tags">
          <span class="perfil-tag role-badge">
            <i class="fa-solid fa-shield-halved"></i>
            <span><?= htmlspecialchars($rol) ?></span>
          </span>
          <span class="perfil-tag status-active">
            <i class="fa-solid fa-circle" style="font-size:8px;"></i>
            <span>Sesión Activa</span>
          </span>
          <span class="perfil-tag">
            <i class="fa-solid fa-envelope"></i>
            <span id="heroEmailDisplay"><?= htmlspecialchars($correo) ?></span>
          </span>
          <span class="perfil-tag">
            <i class="fa-solid fa-calendar-check"></i>
            <span>Miembro desde <?= $fechaCreacion ?></span>
          </span>
        </div>
      </div>
    </div>

    <!-- Botón directo de revertir colores en la cabecera -->
    <div class="perfil-hero-right">
      <button type="button" class="btn-revert-hero" onclick="revertirColoresOriginales()" title="Restaurar colores originales de inmediato">
        <i class="fa-solid fa-arrow-rotate-left"></i>
        <span>Revertir Cambios de Color</span>
      </button>
    </div>
  </div>

  <!-- ═══════════════ GRID DE GESTIÓN ═══════════════ -->
  <div class="perfil-grid">

    <!-- ── PANEL 1: DATOS PERSONALES Y CORREO ── -->
    <div class="perfil-col-6">
      <div class="perfil-card">
        <div class="perfil-card-header">
          <div>
            <h2 class="perfil-card-title">
              <i class="fa-solid fa-id-card"></i>
              <span>Datos de Cuenta</span>
            </h2>
            <p class="perfil-card-subtitle">Actualiza tu nombre de usuario y correo de notificaciones</p>
          </div>
        </div>

        <form id="formPerfilDatos" method="POST" action="index.php?r=perfil/actualizar">
          <?= Security::csrfField() ?>

          <div class="form-group-perfil">
            <label class="form-label-perfil" for="nombre_usuario">
              <i class="fa-solid fa-user" style="color:var(--primary); margin-right:4px;"></i>
              Nombre de Usuario
            </label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" class="form-control-perfil" 
                   value="<?= htmlspecialchars($nombre) ?>" required minlength="3" maxlength="50" autocomplete="username">
          </div>

          <div class="form-group-perfil">
            <label class="form-label-perfil" for="correo">
              <i class="fa-solid fa-envelope" style="color:var(--primary); margin-right:4px;"></i>
              Correo Electrónico
            </label>
            <input type="email" id="correo" name="correo" class="form-control-perfil" 
                   value="<?= htmlspecialchars($correo) ?>" required maxlength="100" autocomplete="email">
          </div>

          <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:24px;">
            <button type="submit" class="btn-perfil-primary" id="btnGuardarDatos">
              <i class="fa-solid fa-floppy-disk"></i>
              <span>Guardar Cambios</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ── PANEL 2: SEGURIDAD Y CONTRASEÑA ── -->
    <div class="perfil-col-6">
      <div class="perfil-card">
        <div class="perfil-card-header">
          <div>
            <h2 class="perfil-card-title">
              <i class="fa-solid fa-lock"></i>
              <span>Seguridad y Contraseña</span>
            </h2>
            <p class="perfil-card-subtitle">Protege tu acceso mediante una clave de alta seguridad</p>
          </div>
        </div>

        <form id="formPerfilPassword" method="POST" action="index.php?r=perfil/actualizarPassword">
          <?= Security::csrfField() ?>

          <div class="form-group-perfil">
            <label class="form-label-perfil" for="password_actual">Contraseña Actual</label>
            <div class="password-input-wrap">
              <input type="password" id="password_actual" name="password_actual" class="form-control-perfil" required autocomplete="current-password" placeholder="Tu contraseña actual">
              <button type="button" class="password-toggle-btn" onclick="togglePasswordVis('password_actual', this)" title="Ver / Ocultar">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="form-group-perfil">
            <label class="form-label-perfil" for="password_nueva">Nueva Contraseña</label>
            <div class="password-input-wrap">
              <input type="password" id="password_nueva" name="password_nueva" class="form-control-perfil" required minlength="8" autocomplete="new-password" placeholder="Mínimo 8 caracteres" oninput="checkPasswordStrength(this.value)">
              <button type="button" class="password-toggle-btn" onclick="togglePasswordVis('password_nueva', this)" title="Ver / Ocultar">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
            <div class="password-strength-wrap">
              <div class="password-strength-bar">
                <div class="password-strength-fill" id="passStrengthFill"></div>
              </div>
              <span class="password-strength-text" id="passStrengthText">Seguridad: introduce una contraseña</span>
            </div>
          </div>

          <div class="form-group-perfil">
            <label class="form-label-perfil" for="password_confirmar">Confirmar Nueva Contraseña</label>
            <div class="password-input-wrap">
              <input type="password" id="password_confirmar" name="password_confirmar" class="form-control-perfil" required minlength="8" autocomplete="new-password" placeholder="Repite la nueva contraseña">
              <button type="button" class="password-toggle-btn" onclick="togglePasswordVis('password_confirmar', this)" title="Ver / Ocultar">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>

          <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:24px;">
            <button type="submit" class="btn-perfil-primary" id="btnGuardarPassword">
              <i class="fa-solid fa-key"></i>
              <span>Actualizar Contraseña</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ── PANEL 3: FOTO DE PERFIL Y AVATARES ── -->
    <div class="perfil-col-12">
      <div class="perfil-card">
        <div class="perfil-card-header">
          <div>
            <h2 class="perfil-card-title">
              <i class="fa-solid fa-camera-retro"></i>
              <span>Foto de Perfil y Avatares</span>
            </h2>
            <p class="perfil-card-subtitle">Sube tu fotografía corporativa o escoge un avatar ejecutivo ilustrado</p>
          </div>
        </div>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:24px; align-items:start;">
          <!-- Subida personalizada -->
          <div>
            <span class="form-label-perfil">Subir foto desde tu dispositivo</span>
            <form id="formSubirFoto" method="POST" action="index.php?r=perfil/actualizarFoto" enctype="multipart/form-data" style="margin-top:8px;">
              <?= Security::csrfField() ?>
              <input type="file" id="fotoInputFile" name="foto" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none;" onchange="previewAndSubmitPhoto(this)">
              <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button type="button" class="btn-perfil-secondary" onclick="document.getElementById('fotoInputFile').click()">
                  <i class="fa-solid fa-arrow-up-from-bracket"></i>
                  <span>Seleccionar imagen (JPG, PNG, WebP)</span>
                </button>
                <?php if (!empty($foto)): ?>
                  <button type="button" class="btn-perfil-secondary" style="color:var(--danger); border-color:rgba(239,68,68,0.3);" onclick="eliminarFotoPerfil()">
                    <i class="fa-solid fa-trash-can"></i>
                    <span>Eliminar foto</span>
                  </button>
                <?php endif; ?>
              </div>
              <small style="display:block; margin-top:8px; color:var(--text-muted); font-size:12px;">Tamaño máximo: 4MB. La imagen se optimiza para el header y credenciales.</small>
            </form>
          </div>

          <!-- Selección rápida de Avatares Ejecutivos -->
          <div>
            <span class="form-label-perfil">O elige un avatar profesional predeterminado</span>
            <div class="avatar-presets-grid">
              <?php for ($i = 1; $i <= 6; $i++): 
                $presetFile = "assets/images/avatars/preset_{$i}.svg";
                $isActivePreset = ($foto === $presetFile);
              ?>
                <button type="button" class="avatar-preset-btn <?= $isActivePreset ? 'active' : '' ?>" 
                        onclick="seleccionarAvatarPreset('preset_<?= $i ?>')" title="Avatar Ejecutivo <?= $i ?>">
                  <img src="<?= $presetFile ?>" alt="Preset <?= $i ?>">
                </button>
              <?php endfor; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── PANEL 4: PERSONALIZACIÓN DE COLOR DE HEADER Y BOTONES ── -->
    <div class="perfil-col-12">
      <div class="perfil-card">
        <div class="perfil-card-header">
          <div>
            <h2 class="perfil-card-title">
              <i class="fa-solid fa-palette"></i>
              <span>Personalización del Header y Botones</span>
            </h2>
            <p class="perfil-card-subtitle">Cambia el color del header y de los botones inspirados en él, o restáuralos al instante</p>
          </div>
          <!-- Botón Revertir Cambios siempre visible -->
          <button type="button" class="btn-revert-danger" onclick="revertirColoresOriginales()" id="btnRevertirCambios" title="Volver al color que tienen actualmente">
            <i class="fa-solid fa-arrow-rotate-left"></i>
            <span>Revertir Cambios</span>
          </button>
        </div>

        <div class="theme-customizer-box">
          <span class="form-label-perfil" style="margin-bottom:12px;">Paletas Ejecutivas Inspiradas</span>
          
          <div class="color-presets-row">
            <!-- Preset 1: Esmeralda Nómina (Predeterminado actual) -->
            <button type="button" class="color-preset-card active" data-header="#0d251e" data-primary="#0f766e" onclick="aplicarPaletaPreset(this)">
              <div class="preset-preview-swatches">
                <div class="swatch-header" style="background:#0d251e;"></div>
                <div class="swatch-btn" style="background:#0f766e;"></div>
              </div>
              <span class="preset-name">Esmeralda (Original)</span>
            </button>

            <!-- Preset 2: Océano Profundo -->
            <button type="button" class="color-preset-card" data-header="#0b1d3a" data-primary="#0284c7" onclick="aplicarPaletaPreset(this)">
              <div class="preset-preview-swatches">
                <div class="swatch-header" style="background:#0b1d3a;"></div>
                <div class="swatch-btn" style="background:#0284c7;"></div>
              </div>
              <span class="preset-name">Azul Océano</span>
            </button>

            <!-- Preset 3: Índigo Tech -->
            <button type="button" class="color-preset-card" data-header="#131438" data-primary="#4f46e5" onclick="aplicarPaletaPreset(this)">
              <div class="preset-preview-swatches">
                <div class="swatch-header" style="background:#131438;"></div>
                <div class="swatch-btn" style="background:#4f46e5;"></div>
              </div>
              <span class="preset-name">Índigo Real</span>
            </button>

            <!-- Preset 4: Carbón Minimalista -->
            <button type="button" class="color-preset-card" data-header="#141416" data-primary="#334155" onclick="aplicarPaletaPreset(this)">
              <div class="preset-preview-swatches">
                <div class="swatch-header" style="background:#141416;"></div>
                <div class="swatch-btn" style="background:#334155;"></div>
              </div>
              <span class="preset-name">Dark Graphite</span>
            </button>

            <!-- Preset 5: Púrpura Amatista -->
            <button type="button" class="color-preset-card" data-header="#240d3a" data-primary="#7c3aed" onclick="aplicarPaletaPreset(this)">
              <div class="preset-preview-swatches">
                <div class="swatch-header" style="background:#240d3a;"></div>
                <div class="swatch-btn" style="background:#7c3aed;"></div>
              </div>
              <span class="preset-name">Amatista</span>
            </button>

            <!-- Preset 6: Verde Bosque -->
            <button type="button" class="color-preset-card" data-header="#06291a" data-primary="#16a34a" onclick="aplicarPaletaPreset(this)">
              <div class="preset-preview-swatches">
                <div class="swatch-header" style="background:#06291a;"></div>
                <div class="swatch-btn" style="background:#16a34a;"></div>
              </div>
              <span class="preset-name">Bosque Tropical</span>
            </button>

            <!-- Preset 7: Carmesí Borgoña -->
            <button type="button" class="color-preset-card" data-header="#2e0d15" data-primary="#dc2626" onclick="aplicarPaletaPreset(this)">
              <div class="preset-preview-swatches">
                <div class="swatch-header" style="background:#2e0d15;"></div>
                <div class="swatch-btn" style="background:#dc2626;"></div>
              </div>
              <span class="preset-name">Borgoña Real</span>
            </button>
          </div>

          <!-- Selector personalizado de Color Picker -->
          <div class="custom-pickers-row">
            <div class="color-picker-item">
              <input type="color" id="inputHeaderColor" class="color-input-styled" value="<?= htmlspecialchars($headerColor) ?>" oninput="onCustomHeaderColor(this.value)">
              <div>
                <div class="color-picker-label">Color del Header</div>
                <div class="color-picker-sub" id="labelHeaderHex"><?= htmlspecialchars($headerColor) ?></div>
              </div>
            </div>

            <div class="color-picker-item">
              <input type="color" id="inputPrimaryColor" class="color-input-styled" value="<?= htmlspecialchars($primaryColor) ?>" oninput="onCustomPrimaryColor(this.value)">
              <div>
                <div class="color-picker-label">Color de Botones y Acentos</div>
                <div class="color-picker-sub" id="labelPrimaryHex"><?= htmlspecialchars($primaryColor) ?></div>
              </div>
            </div>

            <div style="margin-left:auto; display:flex; gap:10px;">
              <button type="button" class="btn-perfil-primary" onclick="guardarPreferenciasServidor()">
                <i class="fa-solid fa-check"></i>
                <span>Confirmar Colores</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── PANEL 5: SELECTOR DE ESTRUCTURA CON SKELETONS (HEADER VS SIDEBAR) ── -->
    <div class="perfil-col-12">
      <div class="perfil-card">
        <div class="perfil-card-header">
          <div>
            <h2 class="perfil-card-title">
              <i class="fa-solid fa-table-columns"></i>
              <span>Estructura del Sistema: Header Superior vs Modo Sidebar</span>
            </h2>
            <p class="perfil-card-subtitle">
              Elige cómo deseas organizar la navegación: en la barra superior o trasladar esos apartados (Inicio, Empleados, Permisos, Periodos, Tasa de cambio) a un Sidebar para dejar el header más limpio y despejado
            </p>
          </div>
        </div>

        <div class="skeleton-selector-grid">
          <!-- SKELETON 1: DISEÑO ACTUAL CON HEADER COMPLETO -->
          <div class="skeleton-option-card <?= ($navLayout === 'header') ? 'active' : '' ?>" id="cardLayoutHeader" onclick="seleccionarLayoutMode('header')">
            <div class="skeleton-option-header">
              <div class="skeleton-option-title">
                <i class="fa-solid fa-window-maximize" style="color:var(--primary);"></i>
                <span>Barra Superior (Header Tradicional)</span>
              </div>
              <div class="skeleton-radio-indicator"></div>
            </div>

            <!-- MOCKUP SKELETON 1 -->
            <div class="skeleton-window">
              <div class="skeleton-window-bar">
                <div class="skeleton-dot red"></div>
                <div class="skeleton-dot yellow"></div>
                <div class="skeleton-dot green"></div>
              </div>
              <div class="skeleton-view-header-layout">
                <!-- Barra superior con los 5 apartados -->
                <div class="skeleton-topbar shimmer">
                  <div class="skeleton-brand-box">
                    <div class="skeleton-brand-logo"></div>
                    <div class="skeleton-brand-name"></div>
                  </div>
                  <div class="skeleton-nav-pills">
                    <div class="skeleton-nav-pill active" title="Inicio"></div>
                    <div class="skeleton-nav-pill" title="Empleados"></div>
                    <div class="skeleton-nav-pill" title="Permisos"></div>
                    <div class="skeleton-nav-pill" title="Periodos"></div>
                    <div class="skeleton-nav-pill" title="Tasa"></div>
                  </div>
                  <div class="skeleton-user-circle"></div>
                </div>

                <!-- Contenido dashboard -->
                <div class="skeleton-dashboard-content">
                  <div class="skeleton-metrics-row">
                    <div class="skeleton-metric-box shimmer">
                      <div class="skeleton-line-sm"></div>
                      <div class="skeleton-line-md"></div>
                    </div>
                    <div class="skeleton-metric-box shimmer">
                      <div class="skeleton-line-sm"></div>
                      <div class="skeleton-line-md"></div>
                    </div>
                    <div class="skeleton-metric-box shimmer">
                      <div class="skeleton-line-sm"></div>
                      <div class="skeleton-line-md"></div>
                    </div>
                  </div>
                  <div class="skeleton-table-box shimmer">
                    <div class="skeleton-table-row"></div>
                    <div class="skeleton-table-row" style="width:85%;"></div>
                    <div class="skeleton-table-row" style="width:65%;"></div>
                  </div>
                </div>
              </div>
            </div>

            <p class="skeleton-desc-text">
              <strong>Diseño Actual:</strong> Todos los accesos principales (Inicio, Empleados, Permisos, Periodos, Tasa de cambio) se ubican horizontalmente en el encabezado.
            </p>
            <div style="margin-top:12px;">
              <span class="skeleton-badge"><i class="fa-solid fa-check-circle"></i> Diseño Estándar</span>
            </div>
          </div>

          <!-- SKELETON 2: MODO SIDEBAR (HEADER LIMPIO) -->
          <div class="skeleton-option-card <?= ($navLayout === 'sidebar') ? 'active' : '' ?>" id="cardLayoutSidebar" onclick="seleccionarLayoutMode('sidebar')">
            <div class="skeleton-option-header">
              <div class="skeleton-option-title">
                <i class="fa-solid fa-table-cells-large" style="color:var(--primary);"></i>
                <span>Modo Sidebar (Header Limpio y Despejado)</span>
              </div>
              <div class="skeleton-radio-indicator"></div>
            </div>

            <!-- MOCKUP SKELETON 2 -->
            <div class="skeleton-window">
              <div class="skeleton-window-bar">
                <div class="skeleton-dot red"></div>
                <div class="skeleton-dot yellow"></div>
                <div class="skeleton-dot green"></div>
              </div>
              <div class="skeleton-view-sidebar-layout">
                <!-- Sidebar lateral izquierdo con los apartados -->
                <div class="skeleton-sidebar shimmer">
                  <div class="skeleton-sidebar-brand">
                    <div class="skeleton-brand-logo"></div>
                    <div class="skeleton-brand-name" style="width:25px;"></div>
                  </div>
                  <div class="skeleton-sidebar-list">
                    <div class="skeleton-sidebar-item active" title="Inicio"></div>
                    <div class="skeleton-sidebar-item" title="Empleados"></div>
                    <div class="skeleton-sidebar-item" title="Permisos"></div>
                    <div class="skeleton-sidebar-item" title="Periodos"></div>
                    <div class="skeleton-sidebar-item" title="Tasa de cambio"></div>
                  </div>
                </div>

                <!-- Encabezado limpio y contenido -->
                <div class="skeleton-content-area">
                  <div class="skeleton-clean-topbar shimmer">
                    <div class="skeleton-clean-search"></div>
                    <div class="skeleton-user-circle"></div>
                  </div>

                  <div class="skeleton-dashboard-content">
                    <div class="skeleton-metrics-row">
                      <div class="skeleton-metric-box shimmer">
                        <div class="skeleton-line-sm"></div>
                        <div class="skeleton-line-md"></div>
                      </div>
                      <div class="skeleton-metric-box shimmer">
                        <div class="skeleton-line-sm"></div>
                        <div class="skeleton-line-md"></div>
                      </div>
                    </div>
                    <div class="skeleton-table-box shimmer">
                      <div class="skeleton-table-row"></div>
                      <div class="skeleton-table-row" style="width:75%;"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <p class="skeleton-desc-text">
              <strong>Header Más Limpio:</strong> Los apartados pasan a una barra lateral izquierda elegante, liberando espacio en el encabezado para mayor amplitud y comodidad de trabajo.
            </p>
            <div style="margin-top:12px;">
              <span class="skeleton-badge"><i class="fa-solid fa-sparkles"></i> Navegación Lateral Ergonómica</span>
            </div>
          </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:24px; padding-top:16px; border-top:1px solid var(--border);">
          <button type="button" class="btn-perfil-primary" onclick="guardarPreferenciasServidor()">
            <i class="fa-solid fa-wand-magic-sparkles"></i>
            <span>Aplicar Estructura Seleccionada</span>
          </button>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- ═══════════════ SCRIPT INTERACTIVO DEL PERFIL ═══════════════ -->
<script nonce="<?= Security::getCspNonce() ?>">
(function() {
  var currentHeaderColor = <?= json_encode($headerColor) ?>;
  var currentPrimaryColor = <?= json_encode($primaryColor) ?>;
  var currentNavLayout = <?= json_encode($navLayout) ?>;

  // Actualizar indicadores según valores actuales
  window.addEventListener('DOMContentLoaded', function() {
    highlightMatchingPreset(currentHeaderColor, currentPrimaryColor);
  });

  // Mostrar u ocultar contraseña
  window.togglePasswordVis = function(inputId, btn) {
    var inp = document.getElementById(inputId);
    if (!inp) return;
    var icon = btn.querySelector('i');
    if (inp.type === 'password') {
      inp.type = 'text';
      if (icon) {
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      }
    } else {
      inp.type = 'password';
      if (icon) {
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }
  };

  // Medidor de fortaleza de contraseña
  window.checkPasswordStrength = function(val) {
    var fill = document.getElementById('passStrengthFill');
    var text = document.getElementById('passStrengthText');
    if (!fill || !text) return;

    if (!val) {
      fill.style.width = '0%';
      text.textContent = 'Seguridad: introduce una contraseña';
      text.style.color = 'var(--text-muted)';
      return;
    }

    var score = 0;
    if (val.length >= 8) score++;
    if (val.length >= 12) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    if (score <= 2) {
      fill.style.width = '33%';
      fill.style.background = '#ef4444';
      text.textContent = 'Seguridad: Débil (añade números o mayúsculas)';
      text.style.color = '#ef4444';
    } else if (score <= 4) {
      fill.style.width = '66%';
      fill.style.background = '#f59e0b';
      text.textContent = 'Seguridad: Media (buena protección)';
      text.style.color = '#f59e0b';
    } else {
      fill.style.width = '100%';
      fill.style.background = '#10b981';
      text.textContent = 'Seguridad: Excelente (clave robusta)';
      text.style.color = '#10b981';
    }
  };

  // Previsualización y subida de foto
  window.previewAndSubmitPhoto = function(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        var heroImg = document.getElementById('heroAvatarImg');
        var heroFallback = document.getElementById('heroAvatarFallback');
        if (heroImg) {
          heroImg.src = e.target.result;
        } else if (heroFallback) {
          var newImg = document.createElement('img');
          newImg.src = e.target.result;
          newImg.id = 'heroAvatarImg';
          newImg.className = 'perfil-avatar-img';
          heroFallback.replaceWith(newImg);
        }
      };
      reader.readAsDataURL(input.files[0]);
      document.getElementById('formSubirFoto').submit();
    }
  };

  // Seleccionar avatar preset
  window.seleccionarAvatarPreset = function(presetName) {
    var formData = new FormData();
    formData.append('avatar_preset', presetName);
    var csrf = document.querySelector('meta[name="csrf-token"]');
    if (csrf) formData.append('csrf_token', csrf.content);

    fetch('index.php?r=perfil/actualizarFoto', {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
      if (data.success) {
        window.location.reload();
      } else {
        alert(data.error || 'No se pudo seleccionar el avatar.');
      }
    })
    .catch(function(err) {
      console.error(err);
      window.location.reload();
    });
  };

  // Eliminar foto de perfil
  window.eliminarFotoPerfil = function() {
    if (!confirm('¿Deseas eliminar tu foto y volver a la inicial de tu nombre?')) return;
    var formData = new FormData();
    var csrf = document.querySelector('meta[name="csrf-token"]');
    if (csrf) formData.append('csrf_token', csrf.content);

    fetch('index.php?r=perfil/eliminarFoto', {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function() { window.location.reload(); })
    .catch(function() { window.location.reload(); });
  };

  // Aplicar paleta preset
  window.aplicarPaletaPreset = function(btn) {
    var hColor = btn.getAttribute('data-header');
    var pColor = btn.getAttribute('data-primary');
    aplicarColoresEnVivo(hColor, pColor);

    document.querySelectorAll('.color-preset-card').forEach(function(c) {
      c.classList.remove('active');
    });
    btn.classList.add('active');
  };

  // Color Pickers personalizados
  window.onCustomHeaderColor = function(val) {
    document.getElementById('labelHeaderHex').textContent = val;
    aplicarColoresEnVivo(val, currentPrimaryColor);
    uncheckPresets();
  };

  window.onCustomPrimaryColor = function(val) {
    document.getElementById('labelPrimaryHex').textContent = val;
    aplicarColoresEnVivo(currentHeaderColor, val);
    uncheckPresets();
  };

  function uncheckPresets() {
    document.querySelectorAll('.color-preset-card').forEach(function(c) {
      c.classList.remove('active');
    });
  }

  function highlightMatchingPreset(hColor, pColor) {
    var cards = document.querySelectorAll('.color-preset-card');
    cards.forEach(function(c) {
      var ch = c.getAttribute('data-header');
      var cp = c.getAttribute('data-primary');
      if (ch && cp && ch.toLowerCase() === hColor.toLowerCase() && cp.toLowerCase() === pColor.toLowerCase()) {
        c.classList.add('active');
      } else {
        c.classList.remove('active');
      }
    });
  }

  // Actualizar CSS en tiempo real en la página
  function aplicarColoresEnVivo(hColor, pColor) {
    currentHeaderColor = hColor;
    currentPrimaryColor = pColor;

    document.documentElement.style.setProperty('--topbar-custom-bg', hColor);
    document.documentElement.style.setProperty('--primary-custom-color', pColor);

    var inpHeader = document.getElementById('inputHeaderColor');
    var inpPrimary = document.getElementById('inputPrimaryColor');
    if (inpHeader) inpHeader.value = hColor;
    if (inpPrimary) inpPrimary.value = pColor;

    var lblH = document.getElementById('labelHeaderHex');
    var lblP = document.getElementById('labelPrimaryHex');
    if (lblH) lblH.textContent = hColor;
    if (lblP) lblP.textContent = pColor;

    localStorage.setItem('nomina-header-color', hColor);
    localStorage.setItem('nomina-primary-color', pColor);
  }

  // ═══════════════ REVERTIR CAMBIOS DE COLOR (SOLICITADO EXPLÍCITAMENTE) ═══════════════
  window.revertirColoresOriginales = function() {
    var defaultHeader = '#0d251e';
    var defaultPrimary = '#0f766e';

    aplicarColoresEnVivo(defaultHeader, defaultPrimary);
    highlightMatchingPreset(defaultHeader, defaultPrimary);

    // Guardar en servidor
    var formData = new FormData();
    var csrf = document.querySelector('meta[name="csrf-token"]');
    if (csrf) formData.append('csrf_token', csrf.content);

    fetch('index.php?r=perfil/revertirPreferencias', {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
      if (typeof showToast === 'function') {
        showToast('↺ Colores originales restaurados con éxito');
      } else {
        alert('¡Header y botones revertidos al color original!');
      }
    })
    .catch(function(err) {
      console.warn('Revertido localmente', err);
    });
  };

  // ═══════════════ SELECCIÓN DE MODO LAYOUT (SKELETON) ═══════════════
  window.seleccionarLayoutMode = function(mode) {
    currentNavLayout = mode;

    var cardHeader = document.getElementById('cardLayoutHeader');
    var cardSidebar = document.getElementById('cardLayoutSidebar');

    if (mode === 'sidebar') {
      if (cardSidebar) cardSidebar.classList.add('active');
      if (cardHeader) cardHeader.classList.remove('active');
      document.documentElement.classList.add('layout-sidebar-mode');
    } else {
      if (cardHeader) cardHeader.classList.add('active');
      if (cardSidebar) cardSidebar.classList.remove('active');
      document.documentElement.classList.remove('layout-sidebar-mode');
    }

    localStorage.setItem('nomina-nav-layout', mode);
  };

  // Guardar preferencias en base de datos
  window.guardarPreferenciasServidor = function() {
    var formData = new FormData();
    formData.append('header_color', currentHeaderColor);
    formData.append('primary_color', currentPrimaryColor);
    formData.append('nav_layout', currentNavLayout);

    var csrf = document.querySelector('meta[name="csrf-token"]');
    if (csrf) formData.append('csrf_token', csrf.content);

    fetch('index.php?r=perfil/guardarPreferencias', {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
      if (data.success) {
        if (typeof showToast === 'function') {
          showToast('✓ ' + data.message);
        } else {
          alert(data.message);
        }
      } else {
        alert(data.error || 'Error al guardar preferencias.');
      }
    })
    .catch(function(err) {
      console.error(err);
      alert('Se aplicaron los cambios en tu navegador.');
    });
  };

})();
</script>
