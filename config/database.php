<?php

return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'dsn'      => $_ENV['DB_DSN']  ?? 'mysql:host=127.0.0.1;dbname=myapp;charset=utf8mb4',
            'username' => $_ENV['DB_USER'] ?? 'root',
            'password' => $_ENV['DB_PASS'] ?? '',
            'options'  => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT         => false,
            ],
        ],
    ],
];