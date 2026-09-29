<?php
declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use MonkeysLegion\Cli\Factory\ModelFactory;
use MonkeysLegion\Cli\Factory\FakerProvider;

/**
 * UserFactory — test data factory for the User entity.
 *
 * Usage:
 *   $user = (new UserFactory())->make();
 *   $admin = (new UserFactory())->admin()->make();
 *   $users = (new UserFactory())->count(10)->make();
 */
final class UserFactory extends ModelFactory
{
    protected string $entityClass = User::class;

    public function definition(): array
    {
        return [
            'email'          => FakerProvider::email(),
            'name'           => FakerProvider::name(),
            'password_hash'  => password_hash('password123', PASSWORD_DEFAULT),
            'token_version'  => 1,
        ];
    }

    /**
     * State: admin user.
     */
    public function admin(): static
    {
        return $this->state('admin', [
            'email' => 'admin@example.com',
            'name'  => 'Admin User',
        ])->apply('admin');
    }
}
