<?php
declare(strict_types=1);

namespace App\Event;

use App\Entity\User;

/**
 * Dispatched when a user is updated.
 */
final readonly class UserUpdated
{
    public function __construct(
        public User $user,
        public \DateTimeImmutable $updatedAt = new \DateTimeImmutable(),
    ) {}
}
