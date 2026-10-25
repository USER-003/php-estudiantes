<?php
// Cabecera y pie comunes. Uso: layout_inicio('Titulo'); ... layout_fin();

function layout_inicio(string $titulo): void
{ ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($titulo) ?> · Registro de Estudiantes</title>
  <link rel="stylesheet" href="/estilos.css">
</head>
<body>
<header>
  <a href="/" class="marca">Registro de Estudiantes</a>
  <span class="meta">PHP <?= PHP_VERSION ?> · PostgreSQL · pod <?= e(gethostname()) ?></span>
</header>
<main>
<?php if (!empty($_GET['msg'])): ?>
  <div class="aviso"><?= e($_GET['msg']) ?></div>
<?php endif;
}

function layout_fin(): void
{ ?>
</main>
<footer>App 1 (stack clasico) · desplegada en Kompilo PaaS</footer>
</body>
</html>
<?php }
