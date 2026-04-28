<?php
declare(strict_types=1);

namespace App\Enum;

/**
 * User role classification.
 */
enum UserRole: string
{
    case Admin  = 'admin';
    case Editor = 'editor';
    case User   = 'user';

    public function label(): string
    {
        return match ($this) {
            self::Admin  => 'Administrator',
            self::Editor => 'Editor',
            self::User   => 'User',
        };
    }

    /**
     * @return list<string>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::Admin  => ['*'],
            self::Editor => ['posts.*', 'comments.*', 'media.*'],
            self::User   => ['posts.view', 'comments.create', 'profile.edit'],
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }
}
