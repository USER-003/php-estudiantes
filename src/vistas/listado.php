<?php require __DIR__ . '/layout.php'; layout_inicio('Estudiantes'); ?>

<div class="barra">
  <form method="get" action="/" class="buscar">
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar por nombre, carnet o carrera">
    <button>Buscar</button>
  </form>
  <a class="boton" href="/nuevo">+ Nuevo estudiante</a>
</div>

<?php if (!$estudiantes): ?>
  <p class="vacio">No hay estudiantes<?= $q !== '' ? ' que coincidan con la busqueda' : ' registrados todavia' ?>.</p>
<?php else: ?>
<div class="tabla">
<table>
  <thead><tr><th>Carnet</th><th>Nombre</th><th>Correo</th><th>Carrera</th><th>Registrado</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($estudiantes as $s): ?>
    <tr>
      <td><?= e($s['carnet']) ?></td>
      <td><?= e($s['nombre']) ?></td>
      <td><?= e($s['email']) ?></td>
      <td><?= e($s['carrera']) ?></td>
      <td><?= e(date('d/m/Y H:i', strtotime($s['creado_en']))) ?></td>
      <td class="acciones">
        <a href="/editar?id=<?= (int) $s['id'] ?>">Editar</a>
        <form method="post" action="/eliminar" onsubmit="return confirm('¿Eliminar este estudiante?')">
          <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
          <button class="peligro">Eliminar</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
<p class="meta"><?= count($estudiantes) ?> registro(s)</p>
<?php endif; ?>

<?php layout_fin(); ?>
