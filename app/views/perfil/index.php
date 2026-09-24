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
      <div class="perfil-avatar-wrapper" onclick="abrirModalAvatar()" role="button" tabindex="0" title="Haz clic para cambiar tu foto o avatar" onkeydown="if(event.key==='Enter'||event.key===' ')abrirModalAvatar()">
        <?php if (!empty($foto)): ?>
          <img src="<?= htmlspecialchars($foto) ?>" alt="<?= htmlspecialchars($nombre) ?>" class="perfil-avatar-img" id="heroAvatarImg">
        <?php else: ?>
          <div class="perfil-avatar-img" id="heroAvatarFallback"><?= $inicial ?></div>
        <?php endif; ?>
        <div class="perfil-avatar-overlay">
          <i class="fa-solid fa-camera"></i>
          <span>Cambiar</span>
        </div>
        <button type="button" class="perfil-avatar-edit-badge" onclick="event.stopPropagation(); abrirModalAvatar();" title="Cambiar foto de perfil">
          <i class="fa-solid fa-camera"></i>
        </button>
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
                   value="<?= htmlspecialchars($nombre) ?>" required minlength="3" maxlength="30"
                   pattern="^[a-zA-Z0-9._-]+$" title="Solo letras, números, puntos, guiones y guiones bajos (sin espacios)"
                   oninput="this.value = this.value.replace(/[^a-zA-Z0-9._-]/g, '')"
                   autocomplete="username">
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
              <input type="password" id="password_actual" name="password_actual" class="form-control-perfil" required maxlength="64" autocomplete="current-password" placeholder="Tu contraseña actual">
              <button type="button" class="password-toggle-btn" onclick="togglePasswordVis('password_actual', this)" title="Ver / Ocultar">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="form-group-perfil">
            <label class="form-label-perfil" for="password_nueva">Nueva Contraseña</label>
            <div class="password-input-wrap">
              <input type="password" id="password_nueva" name="password_nueva" class="form-control-perfil" required minlength="8" maxlength="64" autocomplete="new-password" placeholder="Mínimo 8 caracteres" oninput="checkPasswordStrength(this.value)">
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
              <input type="password" id="password_confirmar" name="password_confirmar" class="form-control-perfil" required minlength="8" maxlength="64" autocomplete="new-password" placeholder="Repite la nueva contraseña">
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

    <!-- ── PANEL 3: PERSONALIZACIÓN DE COLOR DE HEADER Y BOTONES ── -->
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

            <div style="margin-left:auto; display:flex; gap:10px; align-items:center;">
              <button type="button" class="btn-perfil-secondary" id="btnDescartarPreviewColores" style="display:none;" onclick="descartarPreviewColores()" title="Descartar vista previa y volver a tus colores guardados">
                <i class="fa-solid fa-rotate-left"></i>
                <span>Descartar</span>
              </button>
              <button type="button" class="btn-perfil-primary" id="btnConfirmarColores" onclick="confirmarColores()">
                <i class="fa-solid fa-check"></i>
                <span>Confirmar Colores</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── PANEL 4: SELECTOR DE ESTRUCTURA CON SKELETONS (HEADER VS SIDEBAR) ── -->
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
          <button type="button" class="btn-perfil-primary" id="btnAplicarEstructura" onclick="confirmarEstructura()">
            <i class="fa-solid fa-wand-magic-sparkles"></i>
            <span>Aplicar Estructura Seleccionada</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ═══════════════ MODAL UNIFICADO: FOTO DE PERFIL Y AVATARES ═══════════════ -->
<div class="avatar-modal-backdrop" id="modalAvatarBackdrop" onclick="cerrarModalAvatarOnClickOut(event)">
  <div class="avatar-modal-card" role="dialog" aria-modal="true" aria-labelledby="modalAvatarTitle">
    
    <!-- Header del Modal -->
    <div class="avatar-modal-header">
      <div>
        <h3 class="avatar-modal-title" id="modalAvatarTitle">
          <i class="fa-solid fa-camera-retro"></i>
          <span>Foto de Perfil y Avatares</span>
        </h3>
        <p class="avatar-modal-subtitle">Personaliza tu imagen corporativa o elige un avatar ejecutivo</p>
      </div>
      <button type="button" class="avatar-modal-close" onclick="cerrarModalAvatar()" title="Cerrar ventana">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <!-- Contenido del Modal -->
    <div class="avatar-modal-body">
      
      <!-- Vista previa activa -->
      <div class="avatar-modal-preview-box">
        <div id="modalPreviewContainer">
          <?php if (!empty($foto)): ?>
            <img src="<?= htmlspecialchars($foto) ?>" alt="<?= htmlspecialchars($nombre) ?>" class="avatar-modal-preview-img" id="modalAvatarPreviewImg">
          <?php else: ?>
            <div class="avatar-modal-preview-img" id="modalAvatarPreviewFallback"><?= $inicial ?></div>
          <?php endif; ?>
        </div>
        <div class="avatar-modal-preview-info">
          <span class="avatar-modal-preview-name"><?= htmlspecialchars($nombre) ?></span>
          <span class="avatar-modal-preview-badge" id="modalAvatarStatusBadge">
            <i class="fa-solid <?= !empty($foto) ? (strpos($foto, 'preset_') !== false ? 'fa-user-tie' : 'fa-image') : 'fa-font' ?>"></i>
            <span id="modalAvatarStatusText">
              <?php 
                if (empty($foto)) {
                  echo 'Inicial de usuario';
                } elseif (strpos($foto, 'preset_') !== false) {
                  echo 'Avatar ejecutivo';
                } else {
                  echo 'Fotografía personalizada';
                }
              ?>
            </span>
          </span>
        </div>
      </div>

      <!-- Sección 1: Subir Fotografía con Dropzone -->
      <div>
        <div class="avatar-modal-section-title">
          <i class="fa-solid fa-cloud-arrow-up"></i>
          <span>Subir fotografía desde tu equipo</span>
        </div>
        
        <form id="formModalSubirFoto" method="POST" action="index.php?r=perfil/actualizarFoto" enctype="multipart/form-data">
          <?= Security::csrfField() ?>
          <input type="file" id="modalFotoInput" name="foto" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none;" onchange="subirFotoAjax(this)">
          
          <div class="avatar-dropzone" id="avatarDropzone" onclick="document.getElementById('modalFotoInput').click()">
            <div class="avatar-dropzone-icon">
              <i class="fa-solid fa-arrow-up-from-bracket"></i>
            </div>
            <div class="avatar-dropzone-text">
              Arrastra tu imagen aquí o <strong>haz clic para examinar</strong>
            </div>
            <div class="avatar-dropzone-hint">
              Formatos JPG, PNG, WebP o GIF (Tamaño máximo: 4MB)
            </div>
          </div>
        </form>
      </div>

      <!-- Sección 2: Selección rápida de Avatares Ejecutivos -->
      <div>
        <div class="avatar-modal-section-title">
          <i class="fa-solid fa-users-viewfinder"></i>
          <span>O selecciona un avatar ejecutivo prediseñado</span>
        </div>

        <div class="avatar-presets-modal-grid">
          <?php 
            $idUsuarioLogueado = (int)($user['id_usuario'] ?? 0);
            $nomUsuarioLogueado = strtolower(trim($user['nombre_usuario'] ?? ''));
            $esProtegidoPerfil = in_array($idUsuarioLogueado, [1, 2], true) || in_array($nomUsuarioLogueado, ['samu', 'mobile'], true);
          ?>
          <?php if ($esProtegidoPerfil): ?>
            <button type="button" class="avatar-preset-modal-btn <?= ($foto === 'assets/images/logo_multitel.png') ? 'active' : '' ?>" 
                    id="presetBtn_multitel"
                    data-preset="multitel"
                    onclick="seleccionarAvatarPresetAjax('multitel', this)" 
                    title="Emblema Institucional Multitel C.A. (Exclusivo Administradores del Sistema)">
              <img src="assets/images/logo_multitel.png" alt="Multitel C.A." style="background:#ffffff; border-radius:50%; object-fit:contain;">
            </button>
          <?php endif; ?>
          <?php for ($i = 1; $i <= 6; $i++): 
            $presetFile = "assets/images/avatars/preset_{$i}.svg";
            $isActivePreset = ($foto === $presetFile);
          ?>
            <button type="button" class="avatar-preset-modal-btn <?= $isActivePreset ? 'active' : '' ?>" 
                    id="presetBtn_<?= $i ?>"
                    data-preset="preset_<?= $i ?>"
                    onclick="seleccionarAvatarPresetAjax('preset_<?= $i ?>', this)" 
                    title="Avatar Ejecutivo <?= $i ?>">
              <img src="<?= $presetFile ?>" alt="Avatar Ejecutivo <?= $i ?>">
            </button>
          <?php endfor; ?>
        </div>
      </div>

    </div>

    <!-- Footer del Modal -->
    <div class="avatar-modal-footer">
      <div>
        <button type="button" class="btn-perfil-secondary" id="btnModalEliminarFoto" 
                style="color:var(--danger); border-color:rgba(239,68,68,0.3); <?= empty($foto) ? 'display:none;' : '' ?>" 
                onclick="eliminarFotoPerfilAjax()">
          <i class="fa-solid fa-trash-can"></i>
          <span>Eliminar foto</span>
        </button>
      </div>
      <div>
        <button type="button" class="btn-perfil-secondary" onclick="cerrarModalAvatar()">
          <span>Cerrar</span>
        </button>
      </div>
    </div>

  </div>
</div>

<!-- ═══════════════ SCRIPT INTERACTIVO DEL PERFIL ═══════════════ -->
<script nonce="<?= Security::getCspNonce() ?>">
(function() {
  // Estado guardado en base de datos / aplicado actualmente
  var savedHeaderColor = <?= json_encode($headerColor) ?>;
  var savedPrimaryColor = <?= json_encode($primaryColor) ?>;
  var savedNavLayout = <?= json_encode($navLayout) ?>;

  // Estado en selección (staged: pendiente de confirmación mediante botón)
  var stagedHeaderColor = savedHeaderColor;
  var stagedPrimaryColor = savedPrimaryColor;
  var stagedNavLayout = savedNavLayout;

  // Actualizar indicadores según valores actuales
  window.addEventListener('DOMContentLoaded', function() {
    highlightMatchingPreset(stagedHeaderColor, stagedPrimaryColor);
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

  // ═══════════════ MODAL UNIFICADO DE AVATAR Y FOTO ═══════════════
  var userInitialFallback = <?= json_encode($inicial) ?>;
  var userNameDisplay = <?= json_encode($nombre) ?>;

  window.abrirModalAvatar = function() {
    var modal = document.getElementById('modalAvatarBackdrop');
    if (modal) {
      modal.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
  };

  window.cerrarModalAvatar = function() {
    var modal = document.getElementById('modalAvatarBackdrop');
    if (modal) {
      modal.classList.remove('open');
      document.body.style.overflow = '';
    }
  };

  window.cerrarModalAvatarOnClickOut = function(e) {
    if (e.target === e.currentTarget) {
      cerrarModalAvatar();
    }
  };

  // Cerrar con tecla Escape
  window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      cerrarModalAvatar();
    }
  });

  // Notificaciones Toast fluidas
  function showPerfilToast(msg, isError) {
    var toast = document.getElementById('perfilToastAlert');
    if (!toast) {
      toast = document.createElement('div');
      toast.id = 'perfilToastAlert';
      toast.style.cssText = 'position:fixed; bottom:24px; right:24px; z-index:100000; padding:14px 20px; border-radius:12px; font-size:13.5px; font-weight:700; color:#fff; display:flex; align-items:center; gap:10px; box-shadow:0 10px 25px rgba(0,0,0,0.2); transform:translateY(30px); opacity:0; transition:all 0.25s cubic-bezier(0.16,1,0.3,1); pointer-events:none;';
      document.body.appendChild(toast);
    }
    toast.style.background = isError ? '#ef4444' : '#0f766e';
    toast.innerHTML = (isError ? '<i class="fa-solid fa-triangle-exclamation"></i> ' : '<i class="fa-solid fa-circle-check"></i> ') + msg;
    
    requestAnimationFrame(function() {
      toast.style.transform = 'translateY(0)';
      toast.style.opacity = '1';
    });

    clearTimeout(toast._timer);
    toast._timer = setTimeout(function() {
      toast.style.transform = 'translateY(20px)';
      toast.style.opacity = '0';
    }, 3500);
  }

  // Sincronizar avatares en todo el DOM (Hero, Modal, Navbar)
  function syncAvatarsInDom(fotoUrl, isPreset, presetNumber) {
    // 1. Hero Avatar
    var heroWrap = document.querySelector('.perfil-avatar-wrapper');
    if (heroWrap) {
      var heroImg = document.getElementById('heroAvatarImg');
      var heroFallback = document.getElementById('heroAvatarFallback');
      if (fotoUrl) {
        if (heroImg) {
          heroImg.src = fotoUrl + '?t=' + Date.now();
        } else if (heroFallback) {
          var newImg = document.createElement('img');
          newImg.src = fotoUrl + '?t=' + Date.now();
          newImg.alt = userNameDisplay;
          newImg.className = 'perfil-avatar-img';
          newImg.id = 'heroAvatarImg';
          heroFallback.replaceWith(newImg);
        }
      } else {
        // Volver a inicial
        if (heroImg) {
          var newFallback = document.createElement('div');
          newFallback.className = 'perfil-avatar-img';
          newFallback.id = 'heroAvatarFallback';
          newFallback.textContent = userInitialFallback;
          heroImg.replaceWith(newFallback);
        }
      }
    }

    // 2. Modal Avatar Preview
    var modalContainer = document.getElementById('modalPreviewContainer');
    if (modalContainer) {
      if (fotoUrl) {
        modalContainer.innerHTML = '<img src="' + fotoUrl + '?t=' + Date.now() + '" alt="' + userNameDisplay + '" class="avatar-modal-preview-img" id="modalAvatarPreviewImg">';
      } else {
        modalContainer.innerHTML = '<div class="avatar-modal-preview-img" id="modalAvatarPreviewFallback">' + userInitialFallback + '</div>';
      }
    }

    // 3. Status Badge en el Modal
    var statusBadge = document.getElementById('modalAvatarStatusBadge');
    var statusText = document.getElementById('modalAvatarStatusText');
    if (statusBadge && statusText) {
      if (!fotoUrl) {
        statusBadge.querySelector('i').className = 'fa-solid fa-font';
        statusText.textContent = 'Inicial de usuario';
      } else if (isPreset) {
        statusBadge.querySelector('i').className = 'fa-solid fa-user-tie';
        statusText.textContent = 'Avatar ejecutivo' + (presetNumber ? ' #' + presetNumber : '');
      } else {
        statusBadge.querySelector('i').className = 'fa-solid fa-image';
        statusText.textContent = 'Fotografía personalizada';
      }
    }

    // 4. Botón Eliminar Foto en el modal
    var btnEliminar = document.getElementById('btnModalEliminarFoto');
    if (btnEliminar) {
      btnEliminar.style.display = fotoUrl ? 'inline-flex' : 'none';
    }

    // 5. Barra de Navegación superior (header)
    var navAvatarPill = document.querySelector('.user-avatar-pill');
    if (navAvatarPill) {
      if (fotoUrl) {
        navAvatarPill.classList.add('has-img');
        navAvatarPill.innerHTML = '<img src="' + fotoUrl + '?t=' + Date.now() + '" alt="' + userNameDisplay + '">';
      } else {
        navAvatarPill.classList.remove('has-img');
        navAvatarPill.textContent = userInitialFallback;
      }
    }

    var uCardAvatar = document.querySelector('.u-card-avatar');
    if (uCardAvatar) {
      if (fotoUrl) {
        uCardAvatar.classList.add('has-img');
        uCardAvatar.innerHTML = '<img src="' + fotoUrl + '?t=' + Date.now() + '" alt="' + userNameDisplay + '">';
      } else {
        uCardAvatar.classList.remove('has-img');
        uCardAvatar.textContent = userInitialFallback;
      }
    }
  }

  // Subir foto mediante AJAX con Drag & Drop o Input
  window.subirFotoAjax = function(fileOrInput) {
    var file = null;
    if (fileOrInput instanceof File) {
      file = fileOrInput;
    } else if (fileOrInput && fileOrInput.files && fileOrInput.files[0]) {
      file = fileOrInput.files[0];
    }
    if (!file) return;

    var validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (validTypes.indexOf(file.type) === -1) {
      showPerfilToast('Formato no compatible. Usa JPG, PNG, WebP o GIF.', true);
      return;
    }

    if (file.size > 4 * 1024 * 1024) {
      showPerfilToast('El archivo supera el límite de 4MB.', true);
      return;
    }

    var dropzone = document.getElementById('avatarDropzone');
    var originalDropHtml = dropzone ? dropzone.innerHTML : '';
    if (dropzone) {
      dropzone.innerHTML = '<div style="display:flex; align-items:center; gap:10px; color:var(--primary);"><i class="fa-solid fa-spinner fa-spin" style="font-size:20px;"></i> <span>Optimizando y subiendo foto...</span></div>';
    }

    var formData = new FormData();
    formData.append('foto', file);
    var csrf = document.querySelector('meta[name="csrf-token"]');
    if (csrf) formData.append('csrf_token', csrf.content);

    fetch('index.php?r=perfil/actualizarFoto', {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
      if (dropzone) dropzone.innerHTML = originalDropHtml;
      if (data.success) {
        document.querySelectorAll('.avatar-preset-modal-btn').forEach(function(b) {
          b.classList.remove('active');
        });

        syncAvatarsInDom(data.foto_perfil, false, null);
        showPerfilToast(data.message || '¡Fotografía actualizada con éxito!');
      } else {
        showPerfilToast(data.error || 'Error al subir la imagen.', true);
      }
    })
    .catch(function(err) {
      if (dropzone) dropzone.innerHTML = originalDropHtml;
      console.error(err);
      showPerfilToast('No se pudo completar la subida.', true);
    });
  };

  // Seleccionar avatar preset por AJAX
  window.seleccionarAvatarPresetAjax = function(presetName, btn) {
    var formData = new FormData();
    formData.append('avatar_preset', presetName);
    var csrf = document.querySelector('meta[name="csrf-token"]');
    if (csrf) formData.append('csrf_token', csrf.content);

    document.querySelectorAll('.avatar-preset-modal-btn').forEach(function(b) {
      b.classList.remove('active');
    });
    if (btn) btn.classList.add('active');

    fetch('index.php?r=perfil/actualizarFoto', {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
      if (data.success) {
        var num = presetName.replace('preset_', '');
        syncAvatarsInDom(data.foto_perfil, true, num);
        showPerfilToast('¡Avatar corporativo seleccionado!');
      } else {
        showPerfilToast(data.error || 'No se pudo seleccionar el avatar.', true);
      }
    })
    .catch(function(err) {
      console.error(err);
      showPerfilToast('Error de conexión al seleccionar avatar.', true);
    });
  };

  // Eliminar foto de perfil
  window.eliminarFotoPerfilAjax = function() {
    if (!confirm('¿Deseas eliminar tu foto de perfil y volver a la inicial?')) return;
    
    var formData = new FormData();
    var csrf = document.querySelector('meta[name="csrf-token"]');
    if (csrf) formData.append('csrf_token', csrf.content);

    fetch('index.php?r=perfil/eliminarFoto', {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
      if (data.success) {
        document.querySelectorAll('.avatar-preset-modal-btn').forEach(function(b) {
          b.classList.remove('active');
        });
        syncAvatarsInDom(null, false, null);
        showPerfilToast('Foto eliminada. Se usará la inicial de tu nombre.');
      } else {
        showPerfilToast(data.error || 'Error al eliminar foto.', true);
      }
    })
    .catch(function(err) {
      console.error(err);
      showPerfilToast('Error de conexión al eliminar foto.', true);
    });
  };

  // Drag and drop para la Dropzone
  window.addEventListener('DOMContentLoaded', function() {
    var dropzone = document.getElementById('avatarDropzone');
    if (dropzone) {
      ['dragenter', 'dragover'].forEach(function(eventName) {
        dropzone.addEventListener(eventName, function(e) {
          e.preventDefault();
          e.stopPropagation();
          dropzone.classList.add('dragover');
        }, false);
      });

      ['dragleave', 'drop'].forEach(function(eventName) {
        dropzone.addEventListener(eventName, function(e) {
          e.preventDefault();
          e.stopPropagation();
          dropzone.classList.remove('dragover');
        }, false);
      });

      dropzone.addEventListener('drop', function(e) {
        var dt = e.dataTransfer;
        if (dt && dt.files && dt.files[0]) {
          subirFotoAjax(dt.files[0]);
        }
      }, false);
    }
  });

  // ═══════════════ VISTA PREVIA TEMPORAL EN VIVO DE COLORES ═══════════════
  // Aplica temporalmente al DOM para que el usuario aprecie el contraste, pero NO persiste en localStorage ni BD
  function actualizarLivePreviewColores(hColor, pColor) {
    document.documentElement.style.setProperty('--topbar-custom-bg', hColor);
    document.documentElement.style.setProperty('--primary-custom-color', pColor);

    // Detectar si hay cambios respecto a los colores guardados
    var isChanged = (hColor.toLowerCase() !== savedHeaderColor.toLowerCase() || pColor.toLowerCase() !== savedPrimaryColor.toLowerCase());
    var btnDescartar = document.getElementById('btnDescartarPreviewColores');
    var btnConfirmar = document.getElementById('btnConfirmarColores');
    if (btnDescartar) {
      btnDescartar.style.display = isChanged ? 'inline-flex' : 'none';
    }
    if (btnConfirmar) {
      if (isChanged) {
        btnConfirmar.classList.add('pulse-highlight');
      } else {
        btnConfirmar.classList.remove('pulse-highlight');
      }
    }
  }

  // ═══════════════ SELECCIÓN DE PALETA (PRESET) ═══════════════
  window.aplicarPaletaPreset = function(btn) {
    var hColor = btn.getAttribute('data-header');
    var pColor = btn.getAttribute('data-primary');
    
    stagedHeaderColor = hColor;
    stagedPrimaryColor = pColor;

    var inpHeader = document.getElementById('inputHeaderColor');
    var inpPrimary = document.getElementById('inputPrimaryColor');
    if (inpHeader) inpHeader.value = hColor;
    if (inpPrimary) inpPrimary.value = pColor;

    var lblH = document.getElementById('labelHeaderHex');
    var lblP = document.getElementById('labelPrimaryHex');
    if (lblH) lblH.textContent = hColor;
    if (lblP) lblP.textContent = pColor;

    document.querySelectorAll('.color-preset-card').forEach(function(c) {
      c.classList.remove('active');
    });
    btn.classList.add('active');

    // Mostrar vista previa en vivo temporal
    actualizarLivePreviewColores(hColor, pColor);
  };

  // ═══════════════ SELECTORES COLOR PICKER ═══════════════
  window.onCustomHeaderColor = function(val) {
    stagedHeaderColor = val;
    var lblH = document.getElementById('labelHeaderHex');
    if (lblH) lblH.textContent = val;
    uncheckPresetsIfNoMatch();
    actualizarLivePreviewColores(stagedHeaderColor, stagedPrimaryColor);
  };

  window.onCustomPrimaryColor = function(val) {
    stagedPrimaryColor = val;
    var lblP = document.getElementById('labelPrimaryHex');
    if (lblP) lblP.textContent = val;
    uncheckPresetsIfNoMatch();
    actualizarLivePreviewColores(stagedHeaderColor, stagedPrimaryColor);
  };

  function uncheckPresetsIfNoMatch() {
    var matched = false;
    document.querySelectorAll('.color-preset-card').forEach(function(c) {
      var ch = c.getAttribute('data-header');
      var cp = c.getAttribute('data-primary');
      if (ch && cp && ch.toLowerCase() === stagedHeaderColor.toLowerCase() && cp.toLowerCase() === stagedPrimaryColor.toLowerCase()) {
        c.classList.add('active');
        matched = true;
      } else {
        c.classList.remove('active');
      }
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

  // ═══════════════ BOTÓN: DESCARTAR VISTA PREVIA ═══════════════
  window.descartarPreviewColores = function() {
    stagedHeaderColor = savedHeaderColor;
    stagedPrimaryColor = savedPrimaryColor;

    var inpHeader = document.getElementById('inputHeaderColor');
    var inpPrimary = document.getElementById('inputPrimaryColor');
    if (inpHeader) inpHeader.value = savedHeaderColor;
    if (inpPrimary) inpPrimary.value = savedPrimaryColor;

    var lblH = document.getElementById('labelHeaderHex');
    var lblP = document.getElementById('labelPrimaryHex');
    if (lblH) lblH.textContent = savedHeaderColor;
    if (lblP) lblP.textContent = savedPrimaryColor;

    highlightMatchingPreset(savedHeaderColor, savedPrimaryColor);

    // Restaurar DOM a los colores guardados
    document.documentElement.style.setProperty('--topbar-custom-bg', savedHeaderColor);
    document.documentElement.style.setProperty('--primary-custom-color', savedPrimaryColor);

    var btnDescartar = document.getElementById('btnDescartarPreviewColores');
    var btnConfirmar = document.getElementById('btnConfirmarColores');
    if (btnDescartar) btnDescartar.style.display = 'none';
    if (btnConfirmar) btnConfirmar.classList.remove('pulse-highlight');

    showPerfilToast('Vista previa descartada. Se restauraron tus colores guardados.');
  };

  // ═══════════════ BOTÓN: CONFIRMAR COLORES ═══════════════
  // Confirma definitivamente los colores, persiste en localStorage y sincroniza con la base de datos
  window.confirmarColores = function() {
    savedHeaderColor = stagedHeaderColor;
    savedPrimaryColor = stagedPrimaryColor;

    // Aplicar al DOM
    document.documentElement.style.setProperty('--topbar-custom-bg', savedHeaderColor);
    document.documentElement.style.setProperty('--primary-custom-color', savedPrimaryColor);

    // Guardar definitivamente en localStorage
    localStorage.setItem('nomina-header-color', savedHeaderColor);
    localStorage.setItem('nomina-primary-color', savedPrimaryColor);

    var btnDescartar = document.getElementById('btnDescartarPreviewColores');
    var btnConfirmar = document.getElementById('btnConfirmarColores');
    if (btnDescartar) btnDescartar.style.display = 'none';
    if (btnConfirmar) {
      btnConfirmar.classList.remove('pulse-highlight');
      btnConfirmar.disabled = true;
    }

    // Guardar en base de datos
    var formData = new FormData();
    formData.append('header_color', savedHeaderColor);
    formData.append('primary_color', savedPrimaryColor);
    formData.append('nav_layout', savedNavLayout);

    var csrf = document.querySelector('meta[name="csrf-token"]');
    if (csrf) formData.append('csrf_token', csrf.content);

    fetch('index.php?r=perfil/guardarPreferencias', {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
      if (btnConfirmar) btnConfirmar.disabled = false;
      if (data.success) {
        showPerfilToast('✓ ¡Colores confirmados y guardados exitosamente!');
      } else {
        showPerfilToast(data.error || 'Error al guardar colores.', true);
      }
    })
    .catch(function(err) {
      if (btnConfirmar) btnConfirmar.disabled = false;
      console.error(err);
      showPerfilToast('✓ Colores guardados en tu navegador.');
    });
  };

  // Restaurar DOM si el usuario navega fuera sin confirmar
  window.addEventListener('beforeunload', function() {
    if (stagedHeaderColor.toLowerCase() !== savedHeaderColor.toLowerCase() || stagedPrimaryColor.toLowerCase() !== savedPrimaryColor.toLowerCase()) {
      document.documentElement.style.setProperty('--topbar-custom-bg', savedHeaderColor);
      document.documentElement.style.setProperty('--primary-custom-color', savedPrimaryColor);
    }
  });

  // ═══════════════ REVERTIR CAMBIOS DE COLOR (BOTÓN HERO / PANEL) ═══════════════
  window.revertirColoresOriginales = function() {
    var defaultHeader = '#0d251e';
    var defaultPrimary = '#0f766e';

    stagedHeaderColor = defaultHeader;
    stagedPrimaryColor = defaultPrimary;
    savedHeaderColor = defaultHeader;
    savedPrimaryColor = defaultPrimary;

    var inpHeader = document.getElementById('inputHeaderColor');
    var inpPrimary = document.getElementById('inputPrimaryColor');
    if (inpHeader) inpHeader.value = defaultHeader;
    if (inpPrimary) inpPrimary.value = defaultPrimary;

    var lblH = document.getElementById('labelHeaderHex');
    var lblP = document.getElementById('labelPrimaryHex');
    if (lblH) lblH.textContent = defaultHeader;
    if (lblP) lblP.textContent = defaultPrimary;

    highlightMatchingPreset(defaultHeader, defaultPrimary);

    document.documentElement.style.setProperty('--topbar-custom-bg', defaultHeader);
    document.documentElement.style.setProperty('--primary-custom-color', defaultPrimary);

    localStorage.setItem('nomina-header-color', defaultHeader);
    localStorage.setItem('nomina-primary-color', defaultPrimary);

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
      showPerfilToast('↺ Colores originales restaurados con éxito');
    })
    .catch(function(err) {
      console.warn('Revertido localmente', err);
      showPerfilToast('↺ Colores originales restaurados');
    });
  };

  // ═══════════════ SELECCIÓN DE MODO LAYOUT (SOLO SELECCIÓN VISUAL) ═══════════════
  // Solo resalta la opción elegida; NO cambia la estructura del sistema hasta presionar el botón
  window.seleccionarLayoutMode = function(mode) {
    stagedNavLayout = mode;

    var cardHeader = document.getElementById('cardLayoutHeader');
    var cardSidebar = document.getElementById('cardLayoutSidebar');

    if (mode === 'sidebar') {
      if (cardSidebar) cardSidebar.classList.add('active');
      if (cardHeader) cardHeader.classList.remove('active');
    } else {
      if (cardHeader) cardHeader.classList.add('active');
      if (cardSidebar) cardSidebar.classList.remove('active');
    }
  };

  // ═══════════════ BOTÓN: APLICAR ESTRUCTURA SELECCIONADA ═══════════════
  // Al hacer clic, aplica el layout al sistema, a localStorage y lo guarda en base de datos
  window.confirmarEstructura = function() {
    savedNavLayout = stagedNavLayout;

    if (savedNavLayout === 'sidebar') {
      document.documentElement.classList.add('layout-sidebar-mode');
    } else {
      document.documentElement.classList.remove('layout-sidebar-mode');
    }

    localStorage.setItem('nomina-nav-layout', savedNavLayout);

    var formData = new FormData();
    formData.append('header_color', savedHeaderColor);
    formData.append('primary_color', savedPrimaryColor);
    formData.append('nav_layout', savedNavLayout);

    var csrf = document.querySelector('meta[name="csrf-token"]');
    if (csrf) formData.append('csrf_token', csrf.content);

    var btn = document.getElementById('btnAplicarEstructura');
    if (btn) btn.disabled = true;

    fetch('index.php?r=perfil/guardarPreferencias', {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
      if (btn) btn.disabled = false;
      if (data.success) {
        showPerfilToast('✓ ¡Estructura de navegación aplicada con éxito!');
      } else {
        showPerfilToast(data.error || 'Error al guardar estructura.', true);
      }
    })
    .catch(function(err) {
      if (btn) btn.disabled = false;
      console.error(err);
      showPerfilToast('✓ Estructura aplicada en tu navegador.');
    });
  };

  // Compatibilidad hacia atrás si alguna función externa llama guardarPreferenciasServidor
  window.guardarPreferenciasServidor = function() {
    confirmarColores();
    confirmarEstructura();
  };

})();
</script>
