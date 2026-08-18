<?php if (!empty($error)): ?>
  <div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="view-header-bar" style="margin-bottom:20px;">
  <div>
    <h2 style="margin:0;"><i class="fa-solid fa-user-plus" style="color:var(--primary); margin-right:8px;"></i> Registrar Nuevo Empleado</h2>
    <p style="margin:4px 0 0; color:var(--text-muted); font-size:13px;">Ingrese los datos del trabajador para darlo de alta en el sistema</p>
  </div>
  <a class="btn secondary" href="index.php?r=empleados"><i class="fa-solid fa-arrow-left"></i> Volver a la lista</a>
</div>

<div class="card">
  <form method="post" action="index.php?r=empleados/crear" enctype="multipart/form-data">
    <!-- WIDGET DE FOTO DE PERFIL -->
    <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
      <h3 style="margin:0; font-size:15px; color:var(--primary); display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-camera"></i> Foto de Perfil del Empleado
      </h3>
    </div>

    <div class="avatar-upload-card">
      <div class="avatar-preview-wrapper">
        <div class="avatar-preview-box" id="avatarPreviewBox">
          <i class="fa-solid fa-user" id="avatarFallbackIcon"></i>
        </div>
        <label for="fotoInput" class="avatar-camera-btn" title="Subir / Cambiar Foto">
          <i class="fa-solid fa-camera"></i>
        </label>
      </div>
      <div class="avatar-upload-info">
        <h4>Fotografía del Trabajador</h4>
        <p>Formato JPG, PNG o WEBP. Tamaño máximo recomendado: 5 MB.</p>
        <label for="fotoInput" class="btn-file-select">
          <i class="fa-solid fa-upload"></i> Seleccionar imagen...
        </label>
        <input type="file" name="foto" id="fotoInput" accept="image/png, image/jpeg, image/webp" style="display:none;" onchange="previewEmployeePhoto(this)">
      </div>
    </div>

    <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
      <h3 style="margin:0; font-size:15px; color:var(--primary); display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-address-card"></i> Datos Personales
      </h3>
    </div>
    <div class="form-grid" style="margin-bottom: 28px;">
      <div><label>Cédula de Identidad *</label><input type="text" name="cedula" value="<?= htmlspecialchars($_POST['cedula'] ?? '') ?>" placeholder="Ej: V-12345678" required></div>
      <div><label>Fecha de Ingreso *</label><input type="date" name="fecha_ingreso" value="<?= htmlspecialchars($_POST['fecha_ingreso'] ?? '') ?>" required></div>
      <div><label>Primer Nombre *</label><input type="text" name="primer_nombre" value="<?= htmlspecialchars($_POST['primer_nombre'] ?? '') ?>" required></div>
      <div><label>Segundo Nombre</label><input type="text" name="segundo_nombre" value="<?= htmlspecialchars($_POST['segundo_nombre'] ?? '') ?>"></div>
      <div><label>Primer Apellido *</label><input type="text" name="primer_apellido" value="<?= htmlspecialchars($_POST['primer_apellido'] ?? '') ?>" required></div>
      <div><label>Segundo Apellido</label><input type="text" name="segundo_apellido" value="<?= htmlspecialchars($_POST['segundo_apellido'] ?? '') ?>"></div>
      <div class="full"><label>Correo Electrónico</label><input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="ejemplo@empresa.com"></div>
    </div>

<script>
function previewEmployeePhoto(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      var box = document.getElementById('avatarPreviewBox');
      box.innerHTML = '<img src="' + e.target.result + '" alt="Vista Previa">';
    }
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

    <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
      <h3 style="margin:0; font-size:15px; color:var(--primary); display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-briefcase"></i> Información Laboral y Salario
      </h3>
    </div>
    <div class="form-grid" style="margin-bottom: 28px;">
      <div>
        <label>Tipo de Empleado *</label>
        <select name="id_tipo_empleado" required>
          <option value="">-- Seleccionar Tipo --</option>
          <?php foreach ($tipos as $t): ?>
            <option value="<?= $t['id_tipo_empleado'] ?>"><?= htmlspecialchars($t['nombre_tipo']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label>Jornada Laboral *</label>
        <select name="id_jornada" required>
          <option value="">-- Seleccionar Jornada --</option>
          <?php foreach ($jornadas as $j): ?>
            <option value="<?= $j['id_jornada'] ?>"><?= htmlspecialchars($j['nombre_jornada']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label>Cargo *</label>
        <select name="id_cargo" required>
          <option value="">-- Seleccionar Cargo --</option>
          <?php foreach ($cargos as $c): ?>
            <option value="<?= $c['id_cargo'] ?>"><?= htmlspecialchars($c['nombre_cargo']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div><label>Sueldo Mensual ($ USD) *</label><input type="number" step="0.01" min="0" name="sueldo_divisa" value="<?= htmlspecialchars($_POST['sueldo_divisa'] ?? '') ?>" placeholder="0.00" required></div>
    </div>

    <div style="display:flex; gap:12px; border-top:1px solid var(--border); padding-top:20px;">
      <button class="btn" type="submit"><i class="fa-solid fa-floppy-disk"></i> Guardar Empleado</button>
      <a class="btn secondary" href="index.php?r=empleados"><i class="fa-solid fa-xmark"></i> Cancelar</a>
    </div>
  </form>
</div>
