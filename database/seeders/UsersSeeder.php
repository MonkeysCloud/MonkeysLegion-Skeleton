<?php
declare(strict_types=1);

namespace App\Database\Seeders;

use App\Factory\UserFactory;
use MonkeysLegion\Cli\Seeder\Seeder;
use MonkeysLegion\Database\Contracts\ConnectionInterface;

/**
 * UsersSeeder — inserts 10 users into the users table.
 *
 * Run with: php bin/ml db:seed UsersSeeder
 */
final class UsersSeeder extends Seeder
{
    protected function seed(): void
    {
        $factory = new UserFactory();

        for ($i = 0; $i < 10; $i++) {
            $user = $factory->make();

            $this->execute(
                'INSERT INTO users (email, name, password_hash, token_version, created_at, updated_at)
                 VALUES (?, ?, ?, ?, NOW(), NOW())',
                [
                    $user->email,
                    $user->name,
                    $user->password_hash,
                    $user->token_version,
                ],
            );
        }
    }
}
