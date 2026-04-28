<?php
declare(strict_types=1);

namespace App\Service;

use MonkeysLegion\DI\Attributes\Singleton;

use Psr\Log\LoggerInterface;

use App\Entity\User;
use App\Repository\UserRepository;

/**
 * Authentication service — credentials verification and token lifecycle.
 */
#[Singleton]
final class AuthService
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * Attempt to authenticate a user by email/password.
     */
    public function attempt(string $email, string $password): ?User
    {
        $user = $this->users->findByEmail($email);

        if ($user === null) {
            $this->logger->info('Auth attempt: user not found', ['email' => $email]);
            return null;
        }

        if (!password_verify($password, $user->password_hash)) {
            $this->logger->warning('Auth attempt: invalid password', ['email' => $email]);
            return null;
        }

        // Rehash if cost/algorithm has changed
        if (password_needs_rehash($user->password_hash, PASSWORD_DEFAULT)) {
            $user->password_hash = password_hash($password, PASSWORD_DEFAULT);
            $this->users->persist($user);
            $this->logger->info('Password rehashed', ['email' => $email]);
        }

        return $user;
    }

    /**
     * Invalidate all existing tokens for a user by bumping token_version.
     */
    public function invalidateTokens(User $user): void
    {
        $user->bumpTokenVersion();
        $this->users->persist($user);
        $this->logger->info('Tokens invalidated', ['email' => $user->email]);
    }
}
