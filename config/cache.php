<?php

return [
    // The default cache driver to use: 'file', 'redis', or 'memory'
    'default' => $_ENV['CACHE_DRIVER'] ?? 'file',

    // Available cache drivers. Keys must match the CacheType enum values.
    'drivers' => [
        // File-based cache configuration
        'file' => [
            // Directory for cache files (optional, defaults to system temp)
            'directory' => $_ENV['CACHE_FILE_DIRECTORY'] ?? '/path/to/cache',
            // Optional: auto-cleanup settings, lock expiration, etc.
            // 'auto_cleanup' => [
            //     'enabled' => $_ENV['CACHE_FILE_AUTO_CLEANUP_ENABLED'] ?? true,
            //     'probability' => $_ENV['CACHE_FILE_AUTO_CLEANUP_PROBABILITY'] ?? 1000,
            //     'interval' => $_ENV['CACHE_FILE_AUTO_CLEANUP_INTERVAL'] ?? 3600,
            // ],
            // 'lock_expiration' => $_ENV['CACHE_FILE_LOCK_EXPIRATION'] ?? 30,
        ],

        // Redis cache configuration
        'redis' => [
            'host' => $_ENV['CACHE_REDIS_HOST'] ?? '127.0.0.1',
            'port' => $_ENV['CACHE_REDIS_PORT'] ?? 6379,
            // 'auth' => $_ENV['CACHE_REDIS_AUTH'] ?? 'your_password', // optional
            // 'database' => $_ENV['CACHE_REDIS_DATABASE'] ?? 0,           // optional
            // 'timeout' => $_ENV['CACHE_REDIS_TIMEOUT'] ?? 2.0,          // optional
            // 'prefix' => $_ENV['CACHE_REDIS_PREFIX'] ?? 'myapp:',      // optional namespace prefix
        ],

        // In-memory array cache (no config needed)
        'memcached' => [
            // No options required
        ],
    ],
];
