<?php if (!empty($error)): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="view-header-bar" style="margin-bottom:20px;">
  <div>
    <h2 style="margin:0;"><i class="fa-solid fa-coins" style="color:var(--primary); margin-right:8px;"></i> Registrar Tasa de Cambio Manual</h2>
    <p style="margin:4px 0 0; color:var(--text-muted); font-size:13px;">Ingrese el valor de cotización oficial BCV</p>
  </div>
  <a class="btn secondary" href="index.php?r=tasas"><i class="fa-solid fa-arrow-left"></i> Volver a tasas</a>
</div>

<div class="card">
  <form method="post" action="index.php?r=tasas/crear">
    <div class="form-grid">
      <div><label>Fecha *</label><input type="date" name="fecha" value="<?= date('Y-m-d') ?>" required></div>
      <div><label>Valor de la tasa (Bs por unidad de divisa) *</label><input type="number" step="0.0001" min="0" name="valor_tasa" required></div>
      <div class="full"><label>Fuente</label><input type="text" name="fuente" value="BCV"></div>
    </div>
    <br>
    <button class="btn" type="submit">Guardar tasa</button>
    <a class="btn secondary" href="index.php?r=tasas">Cancelar</a>
  </form>
</div>
