<?php
// Router minimo: una sola entrada para todas las rutas (servidor embebido de PHP).
//   GET  /                 listado (con busqueda ?q=)
//   GET  /nuevo            formulario de alta
//   POST /guardar          crea o actualiza
//   GET  /editar?id=N      formulario de edicion
//   POST /eliminar         borra
//   GET  /healthz          sonda de salud para Kompilo (responde si la BD responde)

require __DIR__ . '/../src/db.php';

$ruta = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$metodo = $_SERVER['REQUEST_METHOD'];

function e(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

function redirigir(string $a, string $msg = ''): never
{
    header('Location: ' . $a . ($msg ? '?msg=' . urlencode($msg) : ''));
    exit;
}

if ($ruta === '/healthz') {
    try {
        bd()->query('SELECT 1');
        header('Content-Type: text/plain');
        echo 'ok';
    } catch (Throwable $ex) {
        http_response_code(503);
        echo 'bd no disponible';
    }
    exit;
}

// Archivos estaticos (estilos)
if ($ruta !== '/' && is_file(__DIR__ . $ruta) && pathinfo($ruta, PATHINFO_EXTENSION) !== 'php') {
    return false;
}

try {
    $pdo = bd();
} catch (Throwable $ex) {
    http_response_code(503);
    $error_bd = $ex->getMessage();
    require __DIR__ . '/../src/vistas/error.php';
    exit;
}

$errores = [];
$est = ['id' => '', 'carnet' => '', 'nombre' => '', 'email' => '', 'carrera' => ''];

switch (true) {
    case $ruta === '/guardar' && $metodo === 'POST':
        $est = [
            'id'      => trim($_POST['id'] ?? ''),
            'carnet'  => trim($_POST['carnet'] ?? ''),
            'nombre'  => trim($_POST['nombre'] ?? ''),
            'email'   => trim($_POST['email'] ?? ''),
            'carrera' => trim($_POST['carrera'] ?? ''),
        ];
        if ($est['carnet'] === '')  $errores[] = 'El carnet es obligatorio.';
        if ($est['nombre'] === '')  $errores[] = 'El nombre es obligatorio.';
        if (!filter_var($est['email'], FILTER_VALIDATE_EMAIL)) $errores[] = 'El correo no es valido.';
        if ($est['carrera'] === '') $errores[] = 'La carrera es obligatoria.';

        if (!$errores) {
            try {
                if ($est['id'] === '') {
                    $st = $pdo->prepare('INSERT INTO estudiantes (carnet, nombre, email, carrera) VALUES (?, ?, ?, ?)');
                    $st->execute([$est['carnet'], $est['nombre'], $est['email'], $est['carrera']]);
                    redirigir('/', 'Estudiante creado');
                }
                $st = $pdo->prepare('UPDATE estudiantes SET carnet = ?, nombre = ?, email = ?, carrera = ? WHERE id = ?');
                $st->execute([$est['carnet'], $est['nombre'], $est['email'], $est['carrera'], (int) $est['id']]);
                redirigir('/', 'Estudiante actualizado');
            } catch (PDOException $ex) {
                // 23505 = unique_violation
                $errores[] = $ex->getCode() === '23505' ? 'Ya existe un estudiante con ese carnet.' : 'Error al guardar: ' . $ex->getMessage();
            }
        }
        require __DIR__ . '/../src/vistas/formulario.php';
        break;

    case $ruta === '/nuevo':
        require __DIR__ . '/../src/vistas/formulario.php';
        break;

    case $ruta === '/editar':
        $st = $pdo->prepare('SELECT * FROM estudiantes WHERE id = ?');
        $st->execute([(int) ($_GET['id'] ?? 0)]);
        $est = $st->fetch() ?: redirigir('/', 'Estudiante no encontrado');
        require __DIR__ . '/../src/vistas/formulario.php';
        break;

    case $ruta === '/eliminar' && $metodo === 'POST':
        $st = $pdo->prepare('DELETE FROM estudiantes WHERE id = ?');
        $st->execute([(int) ($_POST['id'] ?? 0)]);
        redirigir('/', 'Estudiante eliminado');

    case $ruta === '/':
        $q = trim($_GET['q'] ?? '');
        if ($q !== '') {
            $st = $pdo->prepare('SELECT * FROM estudiantes WHERE nombre ILIKE ? OR carnet ILIKE ? OR carrera ILIKE ? ORDER BY id DESC');
            $st->execute(array_fill(0, 3, "%$q%"));
        } else {
            $st = $pdo->query('SELECT * FROM estudiantes ORDER BY id DESC');
        }
        $estudiantes = $st->fetchAll();
        require __DIR__ . '/../src/vistas/listado.php';
        break;

    default:
        http_response_code(404);
        echo 'No encontrado';
}
