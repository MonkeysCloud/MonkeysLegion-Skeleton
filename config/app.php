<?php
declare(strict_types=1);

/**
 * MonkeysLegion v2 — DI Container Overrides.
 *
 * This file is for interface-to-concrete bindings and complex
 * factory definitions ONLY. All typed configuration belongs
 * in the .mlc config files.
 *
 * @see https://monkeyslegion.com/docs/di
 */
return [
    // Example: bind a cache interface to Redis implementation
    // Psr\SimpleCache\CacheInterface::class
    //     => fn($c) => $c->get(MonkeysLegion\Cache\Stores\RedisStore::class),

    // Example: bind a custom queue connection
    // MonkeysLegion\Queue\Contracts\QueueInterface::class
    //     => fn($c) => new MonkeysLegion\Queue\Driver\DatabaseQueue(
    //         $c->get(MonkeysLegion\Database\MySQL\Connection::class),
    //         'jobs',
    //     ),

    // Add your overrides below:
];