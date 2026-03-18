<?php

return [

    /*━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━*
     * Session Driver
     *━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━*/

    // The default session driver to use.
    // Supported: "file", "database", "redis"
    'default' => $_ENV['SESSION_DRIVER'] ?? 'file',

    // Supported session drivers and their configurations.
    'drivers' => [
        'file' => [
            'path' => $_ENV['SESSION_FILE_PATH'] ?? base_path('var/sessions'),
            'lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 7200),
        ],
        'database' => [
            'table' => $_ENV['SESSION_TABLE'] ?? 'sessions',
            'lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 7200),
        ],
        'redis' => [
            'connection' => $_ENV['REDIS_SESSION_CONNECTION'] ?? 'default',
            'lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 7200),
        ],
    ],

    /*━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━*
     * Session Cookie Configuration
     *━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━*/

    'cookie_name'     => $_ENV['SESSION_COOKIE_NAME'] ?? 'ml_session',
    'cookie_lifetime' => (int) ($_ENV['SESSION_COOKIE_LIFETIME'] ?? 7200),
    'cookie_path'     => $_ENV['SESSION_COOKIE_PATH'] ?? '/',
    'cookie_domain'   => $_ENV['SESSION_COOKIE_DOMAIN'] ?? '',
    'cookie_secure'   => filter_var($_ENV['SESSION_COOKIE_SECURE'] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
    'cookie_httponly'  => filter_var($_ENV['SESSION_COOKIE_HTTPONLY'] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
    'cookie_samesite' => $_ENV['SESSION_COOKIE_SAMESITE'] ?? 'Lax',

    /*━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━*
     * Session Encryption
     *━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━*/

    'encrypt' => filter_var($_ENV['SESSION_ENCRYPT'] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,

    'keys' => [
        'main_key' => $_ENV['APP_KEY'] ?? null,
    ],
];
