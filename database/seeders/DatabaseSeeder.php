<?php
declare(strict_types=1);

namespace App\Database\Seeders;

use MonkeysLegion\Cli\Seeder\Seeder;
use MonkeysLegion\Database\Contracts\ConnectionInterface;

/**
 * DatabaseSeeder — main orchestrator that calls all other seeders.
 *
 * Run with: php bin/ml db:seed DatabaseSeeder
 */
final class DatabaseSeeder extends Seeder
{
    protected function seed(): void
    {
        // Call dependent seeders in order.
        $this->call(UsersSeeder::class);
        $this->call(PostsSeeder::class);
    }
}
