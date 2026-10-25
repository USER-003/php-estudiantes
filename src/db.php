<?php
// Conexion a PostgreSQL. Las credenciales vienen SOLO de variables de entorno:
// Kompilo inyecta DATABASE_URL (y DB_HOST, DB_PORT, DB_NAME, DB_USER,
// DB_PASSWORD) al crear la base desde el panel. Nada queda en el codigo.

function config_bd(): array
{
    $url = getenv('DATABASE_URL');
    if ($url) {
        $p = parse_url($url);
        return [
            'host' => $p['host'] ?? 'localhost',
            'port' => $p['port'] ?? 5432,
            'name' => ltrim($p['path'] ?? '/app', '/'),
            'user' => urldecode($p['user'] ?? ''),
            'pass' => urldecode($p['pass'] ?? ''),
        ];
    }
    return [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => getenv('DB_PORT') ?: 5432,
        'name' => getenv('DB_NAME') ?: 'app',
        'user' => getenv('DB_USER') ?: '',
        'pass' => getenv('DB_PASSWORD') ?: '',
    ];
}

function bd(): PDO
{
    static $pdo = null;
    if ($pdo) return $pdo;

    $c = config_bd();
    $dsn = "pgsql:host={$c['host']};port={$c['port']};dbname={$c['name']}";
    $pdo = new PDO($dsn, $c['user'], $c['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 5,
    ]);
    crear_esquema($pdo);
    return $pdo;
}

// Crea la tabla la primera vez; idempotente, se puede ejecutar en cada peticion.
function crear_esquema(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS estudiantes (
            id         SERIAL PRIMARY KEY,
            carnet     VARCHAR(20)  NOT NULL UNIQUE,
            nombre     VARCHAR(120) NOT NULL,
            email      VARCHAR(120) NOT NULL,
            carrera    VARCHAR(120) NOT NULL,
            creado_en  TIMESTAMPTZ  NOT NULL DEFAULT now()
        )
    ");
}

function servidor_bd(): string
{
    $c = config_bd();
    return "{$c['host']}:{$c['port']}/{$c['name']}";
}
