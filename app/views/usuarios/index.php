<?php
/**
 * Vista de Gestión de Usuarios del Sistema
 * Solo accesible para rol Administrador
 */
$userRol = $_SESSION['user_rol'] ?? '';
$csrfToken = Security::csrfToken();
?>

<div class="usuarios-page">
  <!-- ═══════════════ HERO BANNER ═══════════════ -->
  <div class="usuarios-hero">
    <div class="usuarios-hero-left">
      <div class="usuarios-hero-icon">
        <i class="fa-solid fa-users-gear"></i>
      </div>
      <div>
        <h1>Gestión de Usuarios</h1>
        <p class="usuarios-hero-sub">Administra las cuentas del sistema, asigna roles y controla el acceso</p>
      </div>
    </div>
    <button type="button" class="btn-crear-usuario" onclick="abrirModalUsuario()" id="btnNuevoUsuario">
      <i class="fa-solid fa-user-plus"></i>
      <span>Nuevo Usuario</span>
    </button>
  </div>

  <!-- ═══════════════ STATS RÁPIDOS ═══════════════ -->
  <div class="usuarios-stats">
    <?php
      $totalActivos = count(array_filter($usuarios, fn($u) => $u['estado'] == 1));
      $totalInactivos = count(array_filter($usuarios, fn($u) => $u['estado'] == 0));
      $totalAdmin = count(array_filter($usuarios, fn($u) => $u['nombre_rol'] === 'Administrador'));
      $totalOperador = count(array_filter($usuarios, fn($u) => $u['nombre_rol'] === 'Operador'));
      $totalApoyo = count(array_filter($usuarios, fn($u) => str_contains($u['nombre_rol'] ?? '', 'Apoyo')));
    ?>
    <div class="stat-card stat-total">
      <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
      <div class="stat-info">
        <span class="stat-number"><?= count($usuarios) ?></span>
        <span class="stat-label">Total Usuarios</span>
      </div>
    </div>
    <div class="stat-card stat-activos">
      <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
      <div class="stat-info">
        <span class="stat-number"><?= $totalActivos ?></span>
        <span class="stat-label">Activos</span>
      </div>
    </div>
    <div class="stat-card stat-admin">
      <div class="stat-icon"><i class="fa-solid fa-shield-halved"></i></div>
      <div class="stat-info">
        <span class="stat-number"><?= $totalAdmin ?></span>
        <span class="stat-label">Administradores</span>
      </div>
    </div>
    <div class="stat-card stat-operador">
      <div class="stat-icon"><i class="fa-solid fa-user-tie"></i></div>
      <div class="stat-info">
        <span class="stat-number"><?= $totalOperador ?></span>
        <span class="stat-label">Operadores</span>
      </div>
    </div>
    <div class="stat-card stat-apoyo">
      <div class="stat-icon"><i class="fa-solid fa-eye"></i></div>
      <div class="stat-info">
        <span class="stat-number"><?= $totalApoyo ?></span>
        <span class="stat-label">Apoyo</span>
      </div>
    </div>
  </div>

  <!-- ═══════════════ FILTROS Y BÚSQUEDA ═══════════════ -->
  <div class="usuarios-toolbar">
    <div class="usuarios-search-box">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" id="searchUsuarios" placeholder="Buscar por usuario, correo o empleado..." autocomplete="off">
    </div>
    <div class="usuarios-filters">
      <select id="filterRol" class="filter-select">
        <option value="">Todos los roles</option>
        <?php foreach ($roles as $rol): ?>
          <option value="<?= htmlspecialchars($rol['nombre_rol']) ?>"><?= htmlspecialchars($rol['nombre_rol']) ?></option>
        <?php endforeach; ?>
      </select>
      <select id="filterEstado" class="filter-select">
        <option value="">Todos los estados</option>
        <option value="1">Activos</option>
        <option value="0">Inactivos</option>
      </select>
    </div>
  </div>

  <!-- ═══════════════ TABLA DE USUARIOS ═══════════════ -->
  <div class="usuarios-table-wrapper">
    <table class="usuarios-table" id="tablaUsuarios">
      <thead>
        <tr>
          <th>Usuario</th>
          <th>Correo</th>
          <th>Rol</th>
          <th>Empleado Vinculado</th>
          <th>Estado</th>
          <th>Fecha Creación</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($usuarios)): ?>
          <tr>
            <td colspan="7" class="tabla-empty">
              <div class="empty-state">
                <i class="fa-solid fa-user-slash"></i>
                <p>No hay usuarios registrados</p>
              </div>
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($usuarios as $u): ?>
            <?php
              $esActivo = $u['estado'] == 1;
              $esMiCuenta = ($u['id_usuario'] == ($_SESSION['user_id'] ?? 0));
              $esProtegido = in_array((int)$u['id_usuario'], [1, 2], true) || in_array(strtolower($u['nombre_usuario']), ['samu', 'mobile'], true);
              $rolClass = match($u['nombre_rol']) {
                'Administrador' => 'role-admin',
                'Operador' => 'role-operador',
                default => 'role-apoyo'
              };
              $inicial = strtoupper(substr($u['nombre_usuario'], 0, 1));
            ?>
            <tr class="usuario-row <?= $esActivo ? '' : 'row-inactivo' ?>"
                data-rol="<?= htmlspecialchars($u['nombre_rol']) ?>"
                data-estado="<?= $u['estado'] ?>"
                data-search="<?= htmlspecialchars(strtolower($u['nombre_usuario'] . ' ' . $u['correo'] . ' ' . ($u['nombre_empleado'] ?? ''))) ?>">
              <td>
                <div class="usuario-cell-info">
                  <?php if (!empty($u['foto_perfil'])): ?>
                    <img src="<?= htmlspecialchars($u['foto_perfil']) ?>" alt="" class="usuario-avatar-mini">
                  <?php else: ?>
                    <div class="usuario-avatar-mini usuario-avatar-letter"><?= $inicial ?></div>
                  <?php endif; ?>
                  <div>
                    <div style="display:flex; align-items:center; gap:5px; flex-wrap:wrap;">
                      <strong><?= htmlspecialchars($u['nombre_usuario']) ?></strong>
                      <?php if ($esProtegido): ?>
                        <span class="badge-protegido-chip" title="Usuario principal del sistema (Protegido contra eliminación)"><i class="fa-solid fa-shield-halved"></i> Sistema</span>
                      <?php endif; ?>
                      <?php if ($esMiCuenta): ?>
                        <span class="badge-tu-cuenta">Tú</span>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </td>
              <td class="td-correo"><?= htmlspecialchars($u['correo']) ?></td>
              <td>
                <span class="role-badge <?= $rolClass ?>">
                  <?php
                    $iconRol = match($u['nombre_rol']) {
                      'Administrador' => 'fa-shield-halved',
                      'Operador' => 'fa-user-tie',
                      default => 'fa-eye'
                    };
                  ?>
                  <i class="fa-solid <?= $iconRol ?>"></i>
                  <?= htmlspecialchars($u['nombre_rol']) ?>
                </span>
              </td>
              <td>
                <?php if (!empty($u['nombre_empleado'])): ?>
                  <span class="empleado-link">
                    <i class="fa-solid fa-id-card-clip"></i>
                    <?= htmlspecialchars($u['nombre_empleado']) ?>
                    <small>(<?= htmlspecialchars($u['cedula_empleado']) ?>)</small>
                  </span>
                <?php else: ?>
                  <span class="sin-vinculacion">— Sin vincular</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="estado-badge <?= $esActivo ? 'estado-activo' : 'estado-inactivo' ?>">
                  <i class="fa-solid fa-circle" style="font-size: 7px;"></i>
                  <?= $esActivo ? 'Activo' : 'Inactivo' ?>
                </span>
              </td>
              <td class="td-fecha"><?= !empty($u['fecha_creacion']) ? date('d/m/Y', strtotime($u['fecha_creacion'])) : '—' ?></td>
              <td>
                <div class="acciones-cell">
                  <button type="button" class="btn-accion btn-editar" title="Editar usuario"
                    onclick="abrirModalEditar(<?= htmlspecialchars(json_encode($u)) ?>)">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>

                  <?php if ($esProtegido): ?>
                    <!-- Usuario principal protegido del sistema: intocable -->
                    <span class="btn-accion btn-accion-disabled" title="Usuario principal protegido del sistema (No se puede desactivar ni eliminar)" style="cursor:not-allowed; opacity:0.45; color:#64748b;">
                      <i class="fa-solid fa-lock"></i>
                    </span>
                  <?php else: ?>
                    <?php if (!$esMiCuenta): ?>
                      <button type="button" class="btn-accion <?= $esActivo ? 'btn-desactivar' : 'btn-activar' ?>"
                        title="<?= $esActivo ? 'Desactivar usuario' : 'Activar usuario' ?>"
                        onclick="cambiarEstadoUsuario(<?= $u['id_usuario'] ?>, <?= $esActivo ? 0 : 1 ?>)">
                        <i class="fa-solid <?= $esActivo ? 'fa-user-slash' : 'fa-user-check' ?>"></i>
                      </button>
                      <button type="button" class="btn-accion btn-eliminar"
                        title="Eliminar usuario definitivamente"
                        onclick="eliminarUsuario(<?= $u['id_usuario'] ?>, '<?= htmlspecialchars(addslashes($u['nombre_usuario'])) ?>')">
                        <i class="fa-solid fa-trash-can"></i>
                      </button>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        <tr id="usuarioNoResultsRow" style="display:none;">
          <td colspan="7" class="tabla-empty">
            <div class="empty-state">
              <i class="fa-solid fa-magnifying-glass"></i>
              <p>No se encontraron usuarios con los filtros seleccionados</p>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- ═══════════════ MODAL CREAR / EDITAR USUARIO ═══════════════ -->
<div class="usuario-modal-backdrop" id="modalUsuarioBackdrop" onclick="cerrarModalUsuario()">
  <div class="usuario-modal" onclick="event.stopPropagation()">
    <div class="usuario-modal-header">
      <div class="modal-header-left">
        <div class="modal-header-icon">
          <i class="fa-solid fa-user-plus" id="modalIcon"></i>
        </div>
        <div>
          <h2 id="modalTitulo">Nuevo Usuario</h2>
          <p id="modalSubtitulo">Completa los datos para crear una cuenta de acceso al sistema</p>
        </div>
      </div>
      <button type="button" class="modal-close-btn" onclick="cerrarModalUsuario()" title="Cerrar">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form id="formUsuario" onsubmit="return guardarUsuario(event)">
      <input type="hidden" id="inputIdUsuario" name="id_usuario" value="">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

      <div class="modal-body">
        <!-- ══════════ SELECCIONADOR DE FOTO DE PERFIL (PRE-SELECCIONADA) ══════════ -->
        <div class="modal-avatar-selection">
          <div class="avatar-hero-display">
            <div class="avatar-display-ring" id="modalAvatarPreviewWrap">
              <img src="assets/images/avatars/preset_1.svg" alt="Avatar Seleccionado" id="modalSelectedAvatarImg" class="avatar-display-img">
            </div>
            <div class="avatar-display-info">
              <div class="avatar-display-label">
                <span class="avatar-tag-badge"><i class="fa-solid fa-sparkles"></i> Foto de Perfil</span>
                <span class="avatar-active-name" id="avatarPreviewHint">Avatar Ejecutivo #1 (Pre-seleccionado)</span>
              </div>
              <p class="avatar-display-desc">Avatar corporativo pre-seleccionado para la cuenta de este usuario</p>
            </div>
          </div>

          <div class="avatar-options-strip">
            <span class="avatar-strip-title">Haz clic para cambiar el avatar:</span>
            <div class="avatar-presets-track" id="avatarPresetsTrack">
              <?php for ($i = 1; $i <= 6; $i++): ?>
                <button type="button" 
                        class="avatar-picker-btn <?= $i === 1 ? 'selected' : '' ?>" 
                        data-avatar="assets/images/avatars/preset_<?= $i ?>.svg" 
                        data-label="Avatar Ejecutivo #<?= $i ?>"
                        onclick="seleccionarAvatarModal(this)"
                        title="Avatar Ejecutivo <?= $i ?>">
                  <img src="assets/images/avatars/preset_<?= $i ?>.svg" alt="Avatar <?= $i ?>">
                  <span class="picker-check"><i class="fa-solid fa-check"></i></span>
                </button>
              <?php endfor; ?>

              <!-- Opción Corporativa Multitel (Exclusiva para Administradores Principales Samu y Mobile) -->
              <button type="button" 
                      class="avatar-picker-btn" 
                      id="btnPresetMultitelLogo"
                      style="display:none;"
                      data-avatar="assets/images/logo_multitel.png" 
                      data-label="Emblema Multitel C.A."
                      onclick="seleccionarAvatarModal(this)"
                      title="Emblema Multitel C.A. (Exclusivo Administradores del Sistema)">
                <img src="assets/images/logo_multitel.png" alt="Multitel" style="border-radius:50%; object-fit:contain; background:#ffffff;">
                <span class="picker-check"><i class="fa-solid fa-check"></i></span>
              </button>

              <!-- Opción Inicial del Nombre -->
              <button type="button" 
                      class="avatar-picker-btn avatar-picker-initial" 
                      data-avatar="" 
                      data-label="Inicial del Nombre (Sin foto)"
                      onclick="seleccionarAvatarModal(this)"
                      title="Solo Letra Inicial">
                <div class="avatar-picker-letter-icon"><i class="fa-solid fa-font"></i></div>
                <span class="picker-check"><i class="fa-solid fa-check"></i></span>
              </button>

              <!-- Opción Subir desde dispositivo (si otro usuario quiere ponerse el logo o su foto, debe subirlo desde su equipo) -->
              <button type="button" 
                      class="avatar-picker-btn avatar-picker-device" 
                      id="btnSubirDesdeDispositivo"
                      onclick="document.getElementById('inputFotoArchivo').click()"
                      title="Subir foto o logo desde tu dispositivo">
                <div class="avatar-picker-device-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                <span class="picker-check" id="checkUploadDevice" style="display:none;"><i class="fa-solid fa-check"></i></span>
              </button>
            </div>

            <!-- Input oculto de archivo para subir desde el dispositivo -->
            <input type="file" id="inputFotoArchivo" name="foto_archivo" accept="image/png,image/jpeg,image/webp,image/gif" style="display:none;" onchange="handleFotoArchivoSeleccionada(this)">
          </div>
        </div>

        <input type="hidden" name="foto_perfil" id="inputFotoPerfil" value="assets/images/avatars/preset_1.svg">

        <div class="modal-form-grid">
          <!-- Nombre de usuario -->
          <div class="form-group">
            <label for="inputNombreUsuario">
              <i class="fa-solid fa-user"></i> Nombre de Usuario <span class="req">*</span>
            </label>
            <input type="text" id="inputNombreUsuario" name="nombre_usuario" required minlength="3"
              placeholder="Ej: carlos.lopez" autocomplete="off">
          </div>

          <!-- Correo -->
          <div class="form-group">
            <label for="inputCorreo">
              <i class="fa-solid fa-envelope"></i> Correo Electrónico <span class="req">*</span>
            </label>
            <input type="email" id="inputCorreo" name="correo" required
              placeholder="Ej: carlos@empresa.com" autocomplete="off">
          </div>

          <!-- Contraseña -->
          <div class="form-group">
            <label for="inputPassword">
              <i class="fa-solid fa-lock"></i> Contraseña <span class="req" id="passRequired">*</span>
            </label>
            <div class="password-input-wrap">
              <input type="password" id="inputPassword" name="password" minlength="6"
                placeholder="Mínimo 6 caracteres" autocomplete="new-password">
              <button type="button" class="toggle-pass-btn" onclick="togglePasswordVis()" tabindex="-1">
                <i class="fa-solid fa-eye" id="passToggleIcon"></i>
              </button>
            </div>
            <small class="form-hint" id="passHint" style="display:none;">Déjalo vacío para mantener la contraseña actual</small>
          </div>

          <!-- Rol -->
          <div class="form-group">
            <label for="selectRol">
              <i class="fa-solid fa-shield-halved"></i> Rol <span class="req">*</span>
            </label>
            <select id="selectRol" name="id_rol" required>
              <option value="">Seleccionar rol...</option>
              <?php foreach ($roles as $rol): ?>
                <option value="<?= $rol['id_rol'] ?>"><?= htmlspecialchars($rol['nombre_rol']) ?> — <?= htmlspecialchars($rol['descripcion'] ?? '') ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Empleado vinculado -->
          <div class="form-group form-group-full">
            <label for="selectEmpleado">
              <i class="fa-solid fa-id-card-clip"></i> Vincular a Empleado <span class="opt">(opcional)</span>
            </label>
            <select id="selectEmpleado" name="id_empleado">
              <option value="">Sin vinculación</option>
              <?php foreach ($empleadosSinUsuario as $emp): ?>
                <option value="<?= $emp['id_empleado'] ?>"><?= htmlspecialchars($emp['nombre_completo']) ?> (<?= htmlspecialchars($emp['cedula']) ?>)</option>
              <?php endforeach; ?>
            </select>
            <small class="form-hint">Solo empleados activos que no tienen cuenta de usuario aparecen aquí</small>
          </div>
        </div>

        <!-- Previsualización de rol -->
        <div class="rol-preview" id="rolPreview" style="display:none;">
          <div class="rol-preview-header">
            <i class="fa-solid fa-info-circle"></i>
            <strong>Permisos del rol seleccionado:</strong>
          </div>
          <div class="rol-preview-body" id="rolPreviewBody"></div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-modal-cancel" onclick="cerrarModalUsuario()">
          <i class="fa-solid fa-xmark"></i> Cancelar
        </button>
        <button type="submit" class="btn-modal-save" id="btnGuardarUsuario">
          <i class="fa-solid fa-floppy-disk"></i>
          <span id="btnGuardarTexto">Crear Usuario</span>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ═══════════════ TOAST DE FEEDBACK ═══════════════ -->
<div class="usuario-toast" id="usuarioToast">
  <i class="fa-solid fa-circle-check" id="toastIcon"></i>
  <span id="toastMsg"></span>
</div>

<script nonce="<?= Security::getCspNonce() ?>">
// ─── DATA ───
const ROLES_PERMISOS = {
  '1': {
    nombre: 'Administrador',
    icon: 'fa-shield-halved',
    color: '#dc2626',
    permisos: ['Acceso total al sistema', 'Gestionar usuarios y roles', 'CRUD empleados, nóminas, permisos', 'Configuración del sistema']
  },
  '3': {
    nombre: 'Operador',
    icon: 'fa-user-tie',
    color: '#ca8a04',
    permisos: ['CRUD empleados', 'Generar y ver nóminas', 'Gestionar permisos y periodos', 'Registrar tasas de cambio', 'Sin acceso a gestión de usuarios']
  },
  '2': {
    nombre: 'Personal de Apoyo',
    icon: 'fa-eye',
    color: '#16a34a',
    permisos: ['Solo lectura en todos los módulos', 'Ver empleados, nóminas, permisos', 'Exportar reportes', 'Sin permisos de creación o edición']
  }
};

let modoEdicion = false;

// ─── SELECCIÓN DE AVATAR PRE-SELECCIONADO ───
function seleccionarAvatarModal(btn) {
  document.querySelectorAll('.avatar-picker-btn').forEach(b => b.classList.remove('selected'));
  btn.classList.add('selected');

  // Limpiar input de archivo del dispositivo si se elige un preset o inicial
  const inputArchivo = document.getElementById('inputFotoArchivo');
  if (inputArchivo) inputArchivo.value = '';
  const checkDev = document.getElementById('checkUploadDevice');
  if (checkDev) checkDev.style.display = 'none';

  const avatarUrl = btn.dataset.avatar || '';
  const label = btn.dataset.label || 'Avatar Seleccionado';

  document.getElementById('inputFotoPerfil').value = avatarUrl;
  document.getElementById('avatarPreviewHint').textContent = label;

  const wrap = document.getElementById('modalAvatarPreviewWrap');
  if (avatarUrl) {
    wrap.innerHTML = '<img src="' + avatarUrl + '" alt="Avatar" id="modalSelectedAvatarImg" class="avatar-display-img">';
  } else {
    const username = (document.getElementById('inputNombreUsuario').value || 'U').trim();
    const inicial = username.charAt(0).toUpperCase() || 'U';
    wrap.innerHTML = '<div class="avatar-preview-letter" id="modalSelectedAvatarLetter">' + inicial + '</div>';
  }
}

// Subida de imagen desde dispositivo local (para que cualquier usuario que tenga el logo/foto en su equipo pueda subirla)
function handleFotoArchivoSeleccionada(input) {
  if (!input.files || !input.files[0]) return;
  const file = input.files[0];

  // Deseleccionar presets
  document.querySelectorAll('.avatar-picker-btn').forEach(b => b.classList.remove('selected'));

  // Activar botón de dispositivo y mostrar check
  const btnDev = document.getElementById('btnSubirDesdeDispositivo');
  if (btnDev) btnDev.classList.add('selected');
  const checkDev = document.getElementById('checkUploadDevice');
  if (checkDev) checkDev.style.display = 'flex';

  // Dejar inputFotoPerfil vacío para que se procese el archivo
  document.getElementById('inputFotoPerfil').value = '';
  document.getElementById('avatarPreviewHint').textContent = 'Archivo del dispositivo: ' + file.name;

  // Previsualizar localmente
  const reader = new FileReader();
  reader.onload = function(e) {
    const wrap = document.getElementById('modalAvatarPreviewWrap');
    wrap.innerHTML = '<img src="' + e.target.result + '" alt="Avatar" id="modalSelectedAvatarImg" class="avatar-display-img">';
  };
  reader.readAsDataURL(file);
}

// ─── MODAL ───
function abrirModalUsuario() {
  modoEdicion = false;
  document.getElementById('modalTitulo').textContent = 'Nuevo Usuario';
  document.getElementById('modalSubtitulo').textContent = 'Completa los datos para crear una cuenta de acceso al sistema';
  document.getElementById('modalIcon').className = 'fa-solid fa-user-plus';
  document.getElementById('btnGuardarTexto').textContent = 'Crear Usuario';
  document.getElementById('passRequired').style.display = '';
  document.getElementById('passHint').style.display = 'none';
  document.getElementById('inputPassword').required = true;
  document.getElementById('formUsuario').reset();
  document.getElementById('inputIdUsuario').value = '';
  document.getElementById('rolPreview').style.display = 'none';

  // Ocultar preset de Multitel en creación (reservado exclusivamente a Samu y Mobile)
  const btnMultitel = document.getElementById('btnPresetMultitelLogo');
  if (btnMultitel) btnMultitel.style.display = 'none';

  // Resetear input de archivo y check
  const inputArchivo = document.getElementById('inputFotoArchivo');
  if (inputArchivo) inputArchivo.value = '';
  const checkDev = document.getElementById('checkUploadDevice');
  if (checkDev) checkDev.style.display = 'none';

  // Pre-seleccionar foto de perfil por defecto (Preset #1)
  const primerPreset = document.querySelector('.avatar-picker-btn[data-avatar="assets/images/avatars/preset_1.svg"]');
  if (primerPreset) {
    seleccionarAvatarModal(primerPreset);
    document.getElementById('avatarPreviewHint').textContent = 'Avatar Ejecutivo #1 (Pre-seleccionado)';
  }

  document.getElementById('modalUsuarioBackdrop').classList.add('show');
  setTimeout(() => document.getElementById('inputNombreUsuario').focus(), 200);
}

function abrirModalEditar(usuario) {
  modoEdicion = true;
  document.getElementById('modalTitulo').textContent = 'Editar Usuario';
  document.getElementById('modalSubtitulo').textContent = 'Modifica los datos de la cuenta de ' + usuario.nombre_usuario;
  document.getElementById('modalIcon').className = 'fa-solid fa-user-pen';
  document.getElementById('btnGuardarTexto').textContent = 'Guardar Cambios';
  document.getElementById('passRequired').style.display = 'none';
  document.getElementById('passHint').style.display = '';
  document.getElementById('inputPassword').required = false;
  document.getElementById('inputPassword').value = '';

  document.getElementById('inputIdUsuario').value = usuario.id_usuario;
  document.getElementById('inputNombreUsuario').value = usuario.nombre_usuario;
  document.getElementById('inputCorreo').value = usuario.correo;
  document.getElementById('selectRol').value = usuario.id_rol;

  // Resetear archivo del dispositivo
  const inputArchivo = document.getElementById('inputFotoArchivo');
  if (inputArchivo) inputArchivo.value = '';
  const checkDev = document.getElementById('checkUploadDevice');
  if (checkDev) checkDev.style.display = 'none';

  // Evaluar si es usuario protegido del sistema (id 1, 2 o nombres samu, mobile)
  const idUsr = parseInt(usuario.id_usuario, 10);
  const nombreUsr = (usuario.nombre_usuario || '').toLowerCase();
  const esProtegido = (idUsr === 1 || idUsr === 2 || ['samu', 'mobile'].includes(nombreUsr));

  // El botón preset de Multitel descargado en el sistema SOLO se muestra a los usuarios principales
  const btnMultitel = document.getElementById('btnPresetMultitelLogo');
  if (btnMultitel) {
    btnMultitel.style.display = esProtegido ? 'inline-flex' : 'none';
  }

  // Configurar foto de perfil actual del usuario
  const userFoto = usuario.foto_perfil || '';
  let matchingBtn = null;
  document.querySelectorAll('.avatar-picker-btn').forEach(b => {
    if (b.dataset.avatar === userFoto) {
      if (b.id === 'btnPresetMultitelLogo' && !esProtegido) {
        // No permitir seleccionar el preset a usuarios no protegidos
      } else {
        matchingBtn = b;
      }
    }
  });

  if (matchingBtn) {
    seleccionarAvatarModal(matchingBtn);
    if (!userFoto) {
      const inicial = (usuario.nombre_usuario || 'U').charAt(0).toUpperCase();
      document.getElementById('modalAvatarPreviewWrap').innerHTML = '<div class="avatar-preview-letter" id="modalSelectedAvatarLetter">' + inicial + '</div>';
    }
  } else if (userFoto) {
    // Foto personalizada o subida desde su dispositivo
    document.querySelectorAll('.avatar-picker-btn').forEach(b => b.classList.remove('selected'));
    document.getElementById('inputFotoPerfil').value = userFoto;
    document.getElementById('avatarPreviewHint').textContent = 'Fotografía actual del usuario';
    document.getElementById('modalAvatarPreviewWrap').innerHTML = '<img src="' + userFoto + '" alt="Avatar" id="modalSelectedAvatarImg" class="avatar-display-img">';
  } else {
    // Si no tiene foto, pre-seleccionar Preset #1
    const defaultBtn = document.querySelector('.avatar-picker-btn[data-avatar="assets/images/avatars/preset_1.svg"]');
    if (defaultBtn) seleccionarAvatarModal(defaultBtn);
  }
  
  // Para empleado vinculado: si el usuario ya tiene uno, agregarlo como opción temporal
  const selEmp = document.getElementById('selectEmpleado');
  if (usuario.id_empleado && usuario.nombre_empleado) {
    let existe = false;
    for (let opt of selEmp.options) {
      if (opt.value == usuario.id_empleado) { existe = true; break; }
    }
    if (!existe) {
      const opt = document.createElement('option');
      opt.value = usuario.id_empleado;
      opt.textContent = usuario.nombre_empleado + ' (' + (usuario.cedula_empleado || '') + ')';
      opt.dataset.temporal = 'true';
      selEmp.appendChild(opt);
    }
    selEmp.value = usuario.id_empleado;
  } else {
    selEmp.value = '';
  }

  actualizarRolPreview();
  document.getElementById('modalUsuarioBackdrop').classList.add('show');
  setTimeout(() => document.getElementById('inputNombreUsuario').focus(), 200);
}

function cerrarModalUsuario() {
  document.getElementById('modalUsuarioBackdrop').classList.remove('show');
  // Limpiar opciones temporales de empleado
  const selEmp = document.getElementById('selectEmpleado');
  selEmp.querySelectorAll('option[data-temporal]').forEach(o => o.remove());
}

// ─── GUARDAR ───
async function guardarUsuario(e) {
  e.preventDefault();
  const btn = document.getElementById('btnGuardarUsuario');
  const btnTexto = document.getElementById('btnGuardarTexto');
  const textoOriginal = btnTexto.textContent;
  btn.disabled = true;
  btnTexto.textContent = 'Guardando...';

  const form = document.getElementById('formUsuario');
  const formData = new FormData(form);
  const url = modoEdicion ? 'index.php?r=usuarios/editar' : 'index.php?r=usuarios/crear';

  try {
    const resp = await fetch(url, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: formData
    });
    const data = await resp.json();

    if (data.success) {
      mostrarToast(data.message || 'Operación exitosa', 'success');
      cerrarModalUsuario();
      setTimeout(() => location.reload(), 800);
    } else {
      mostrarToast(data.error || 'Error desconocido', 'error');
    }
  } catch (err) {
    mostrarToast('Error de conexión. Intenta nuevamente.', 'error');
  } finally {
    btn.disabled = false;
    btnTexto.textContent = textoOriginal;
  }
  return false;
}

// ─── CAMBIAR ESTADO ───
async function cambiarEstadoUsuario(idUsuario, nuevoEstado) {
  const accion = nuevoEstado === 1 ? 'activar' : 'desactivar';
  if (!confirm('¿Estás seguro de ' + accion + ' este usuario?')) return;

  const formData = new FormData();
  formData.append('id_usuario', idUsuario);
  formData.append('estado', nuevoEstado);
  formData.append('csrf_token', '<?= $csrfToken ?>');

  try {
    const resp = await fetch('index.php?r=usuarios/cambiarEstado', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: formData
    });
    const data = await resp.json();

    if (data.success) {
      mostrarToast(data.message, 'success');
      setTimeout(() => location.reload(), 800);
    } else {
      mostrarToast(data.error || 'Error', 'error');
    }
  } catch (err) {
    mostrarToast('Error de conexión.', 'error');
  }
}

// ─── ELIMINAR USUARIO DEFINITIVAMENTE ───
async function eliminarUsuario(idUsuario, nombre) {
  if (!confirm('¿Estás seguro de que deseas eliminar permanentemente al usuario "' + nombre + '"? Esta acción no se puede deshacer.')) {
    return;
  }

  const formData = new FormData();
  formData.append('id_usuario', idUsuario);
  formData.append('csrf_token', '<?= $csrfToken ?>');

  try {
    const resp = await fetch('index.php?r=usuarios/eliminar', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: formData
    });
    const data = await resp.json();

    if (data.success) {
      mostrarToast(data.message, 'success');
      setTimeout(() => location.reload(), 800);
    } else {
      mostrarToast(data.error || 'Error al eliminar usuario', 'error');
    }
  } catch (err) {
    mostrarToast('Error de conexión al eliminar el usuario.', 'error');
  }
}

// ─── BÚSQUEDA Y FILTROS ───
function filtrarTabla() {
  const search = document.getElementById('searchUsuarios').value.toLowerCase();
  const rolFilter = document.getElementById('filterRol').value;
  const estadoFilter = document.getElementById('filterEstado').value;

  document.querySelectorAll('.usuario-row').forEach(row => {
    const matchSearch = !search || row.dataset.search.includes(search);
    const matchRol = !rolFilter || row.dataset.rol === rolFilter;
    const matchEstado = estadoFilter === '' || row.dataset.estado === estadoFilter;
    row.style.display = (matchSearch && matchRol && matchEstado) ? '' : 'none';
  });
}

document.getElementById('searchUsuarios').addEventListener('input', filtrarTabla);
document.getElementById('filterRol').addEventListener('change', filtrarTabla);
document.getElementById('filterEstado').addEventListener('change', filtrarTabla);

// ─── ROL PREVIEW ───
document.getElementById('selectRol').addEventListener('change', actualizarRolPreview);

function actualizarRolPreview() {
  const val = document.getElementById('selectRol').value;
  const preview = document.getElementById('rolPreview');
  const body = document.getElementById('rolPreviewBody');

  if (val && ROLES_PERMISOS[val]) {
    const rol = ROLES_PERMISOS[val];
    body.innerHTML = rol.permisos.map(p =>
      '<div class="rol-permiso-item"><i class="fa-solid fa-check"></i> ' + p + '</div>'
    ).join('');
    preview.style.display = '';
    preview.style.borderColor = rol.color;
  } else {
    preview.style.display = 'none';
  }
}

// ─── ACTUALIZAR INICIAL EN TIEMPO REAL SI SE ELIGE MODO INICIAL ───
document.getElementById('inputNombreUsuario').addEventListener('input', function() {
  const currentAvatar = document.getElementById('inputFotoPerfil').value;
  if (!currentAvatar) {
    const letterEl = document.getElementById('modalSelectedAvatarLetter');
    if (letterEl) {
      letterEl.textContent = (this.value.trim().charAt(0).toUpperCase()) || 'U';
    }
  }
});

// ─── PASSWORD TOGGLE ───
function togglePasswordVis() {
  const input = document.getElementById('inputPassword');
  const icon = document.getElementById('passToggleIcon');
  if (input.type === 'password') {
    input.type = 'text';
    icon.className = 'fa-solid fa-eye-slash';
  } else {
    input.type = 'password';
    icon.className = 'fa-solid fa-eye';
  }
}

// ─── TOAST ───
function mostrarToast(msg, type) {
  const toast = document.getElementById('usuarioToast');
  const icon = document.getElementById('toastIcon');
  const msgEl = document.getElementById('toastMsg');

  msgEl.textContent = msg;
  toast.className = 'usuario-toast show ' + type;
  icon.className = type === 'success' ? 'fa-solid fa-circle-check' : 'fa-solid fa-circle-exclamation';

  setTimeout(() => { toast.classList.remove('show'); }, 4000);
}

// ─── KEYBOARD ───
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') cerrarModalUsuario();
});

// ─── FILTROS, BÚSQUEDA Y PAGINACIÓN ───
function filterUsuarios() {
  var q = (document.getElementById('searchUsuarios')?.value || '').toLowerCase().trim();
  var rol = document.getElementById('filterRol')?.value || '';
  var estado = document.getElementById('filterEstado')?.value || '';

  var rows = document.querySelectorAll('#tablaUsuarios tbody tr.usuario-row');
  var visible = 0;

  rows.forEach(function(row) {
    var searchTarget = (row.dataset.search || '').toLowerCase();
    var rowRol = row.dataset.rol || '';
    var rowEstado = row.dataset.estado || '';

    var matchQ = !q || searchTarget.indexOf(q) !== -1;
    var matchRol = !rol || rowRol === rol;
    var matchEstado = !estado || rowEstado === estado;

    if (matchQ && matchRol && matchEstado) {
      row.dataset.filteredOut = 'false';
      visible++;
    } else {
      row.dataset.filteredOut = 'true';
    }
  });

  if (window.usuariosPaginator) {
    window.usuariosPaginator.currentPage = 1;
    window.usuariosPaginator.update();
  } else {
    var noResultsRow = document.getElementById('usuarioNoResultsRow');
    if (noResultsRow) {
      noResultsRow.style.display = (visible === 0 && rows.length > 0) ? '' : 'none';
    }
  }
}

document.addEventListener('DOMContentLoaded', function() {
  var searchInput = document.getElementById('searchUsuarios');
  var filterRol = document.getElementById('filterRol');
  var filterEstado = document.getElementById('filterEstado');

  if (searchInput) searchInput.addEventListener('input', filterUsuarios);
  if (filterRol) filterRol.addEventListener('change', filterUsuarios);
  if (filterEstado) filterEstado.addEventListener('change', filterUsuarios);

  if (typeof initTablePagination === 'function') {
    window.usuariosPaginator = initTablePagination({
      table: '#tablaUsuarios',
      rowSelector: 'tbody tr.usuario-row',
      noResultsSelector: '#usuarioNoResultsRow',
      storageKey: 'nomina_paginator_usuarios',
      defaultPageSize: 10,
      appendTo: '.usuarios-table-wrapper'
    });
  }
});
</script>
