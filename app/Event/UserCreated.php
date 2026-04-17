<?php
declare(strict_types=1);

namespace App\Event;

use App\Entity\User;

/**
 * Dispatched when a new user is created.
 */
final readonly class UserCreated
{
    public function __construct(
        public User $user,
        public \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
    ) {}
}
