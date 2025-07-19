<?php
$env = $_ENV + $_SERVER;

$dbHost     = $env['DB_HOST'];
$dbPort     = $env['DB_PORT'];
$dbName     = $env['DB_DATABASE'];
$dbCharset  = $env['DB_CHARSET'];

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