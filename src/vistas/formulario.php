<?php
require __DIR__ . '/layout.php';
$editando = $est['id'] !== '';
layout_inicio($editando ? 'Editar estudiante' : 'Nuevo estudiante');
?>

<h1><?= $editando ? 'Editar estudiante' : 'Nuevo estudiante' ?></h1>

<?php if ($errores): ?>
  <ul class="errores"><?php foreach ($errores as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
<?php endif; ?>

<form method="post" action="/guardar" class="formulario">
  <input type="hidden" name="id" value="<?= e((string) $est['id']) ?>">
  <label>Carnet <input name="carnet" value="<?= e($est['carnet']) ?>" maxlength="20" required></label>
  <label>Nombre completo <input name="nombre" value="<?= e($est['nombre']) ?>" maxlength="120" required></label>
  <label>Correo <input type="email" name="email" value="<?= e($est['email']) ?>" maxlength="120" required></label>
  <label>Carrera <input name="carrera" value="<?= e($est['carrera']) ?>" maxlength="120" required></label>
  <div class="botones">
    <button><?= $editando ? 'Guardar cambios' : 'Crear' ?></button>
    <a href="/">Cancelar</a>
  </div>
</form>

<?php layout_fin(); ?>
