<?php
$env = $_ENV + $_SERVER;

/*
|--------------------------------------------------------------------------
| If no DB_HOST is defined the skeleton falls back to an SQLite in-memory
| database so that it boots without any external database.
|--------------------------------------------------------------------------
*/

$dbHost = $env['DB_HOST'] ?? null;

if ($dbHost === null || $dbHost === '') {
    return [
        'default' => 'sqlite',
        'connections' => [
            'sqlite' => [
                'driver'   => 'sqlite',
                'database' => ':memory:',
            ],
        ],
    ];
}

$dbPort     = $env['DB_PORT'] ?? '3306';
$dbName     = $env['DB_DATABASE'] ?? 'app';
$dbCharset  = $env['DB_CHARSET'] ?? 'utf8mb4';

/* recognise BOTH DB_USER and DB_USERNAME, ditto for password */
$dbUser     = $env['DB_USERNAME']
    ?? $env['DB_USER']
    ?? 'root';

$dbPass     = $env['DB_PASSWORD']
    ?? $env['DB_PASS']
    ?? '';

return [
    'default' => 'mysql',

    'connections' => [
        'mysql' => [
            'dsn'      => sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $dbHost, $dbPort, $dbName, $dbCharset
            ),
            'username' => $dbUser,
            'password' => $dbPass,
            'options'  => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ],
        ],
    ],
];