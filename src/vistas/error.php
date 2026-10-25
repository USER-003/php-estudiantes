<?php require __DIR__ . '/layout.php'; layout_inicio('Sin base de datos'); ?>
<h1>No se pudo conectar a la base de datos</h1>
<p>Servidor: <code><?= e(servidor_bd()) ?></code></p>
<p>En Kompilo: abre la app &gt; "Base de datos" &gt; "Crear base PostgreSQL". La plataforma inyecta
<code>DATABASE_URL</code> y la app se reconecta sola.</p>
<pre><?= e($error_bd) ?></pre>
<?php layout_fin(); ?>
