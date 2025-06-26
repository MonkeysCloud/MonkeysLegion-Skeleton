<?php
declare(strict_types=1);

/**
 * Project-specific overrides for MonkeysLegion DI definitions.
 *
 * Any definitions here will override the framework defaults provided by
 * MonkeysLegion\Config\AppConfig. Add only what you need to customize.
 */
return [
    // Example: swap out the default metrics implementation:
    // MonkeysLegion\Telemetry\MetricsInterface::class
    //     => fn() => new MonkeysLegion\Telemetry\StatsDMetrics('127.0.0.1', 8125),

    // Example: override the rate-limit cache path:
    // Psr\SimpleCache\CacheInterface::class
    //     => fn() => new MonkeysLegion\Http\SimpleFileCache(
    //         base_path('var/custom_cache/rate_limit')
    //     ),

    // Example: bind a custom repository:
    // App\Repository\UserRepository::class
    //     => fn($c) => new App\Repository\UserRepository(
    //         $c->get(MonkeysLegion\Database\MySQL\Connection::class)
    //     ),

    // Add your overrides below:
];