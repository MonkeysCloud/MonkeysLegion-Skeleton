<?php
declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the User entity — v2 property hooks and computed properties.
 */
#[CoversClass(User::class)]
final class UserTest extends TestCase
{
    #[Test]
    public function emailIsLowercasedAndTrimmed(): void
    {
        $user = new User();
        $user->email = '  Test@Example.COM  ';

        $this->assertSame('test@example.com', $user->email);
    }

    #[Test]
    public function nameCannotBeEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $user = new User();
        $user->name = '';
    }

    #[Test]
    public function tokenVersionStartsAtOneAndIncrements(): void
    {
        $user = new User();

        $this->assertSame(1, $user->token_version);

        $user->bumpTokenVersion();

        $this->assertSame(2, $user->token_version);
    }

    #[Test]
    public function emailVerification(): void
    {
        $user = new User();

        $this->assertFalse($user->isVerified);
        $this->assertNull($user->email_verified_at);

        $user->markEmailVerified();

        $this->assertTrue($user->isVerified);
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->email_verified_at);
    }

    #[Test]
    public function twoFactorComputedProperty(): void
    {
        $user = new User();

        $this->assertFalse($user->hasTwoFactor);

        $user->two_factor_secret = 'secret123';

        $this->assertTrue($user->hasTwoFactor);

        $user->two_factor_secret = null;

        $this->assertFalse($user->hasTwoFactor);
    }

    #[Test]
    public function displayNameCombinesNameAndEmail(): void
    {
        $user = new User();
        $user->name = 'Jorge';
        $user->email = 'jorge@example.com';

        $this->assertSame('Jorge <jorge@example.com>', $user->displayName);
    }

    #[Test]
    public function authInterfaceMethods(): void
    {
        $user = new User();
        $user->password_hash = 'hashed_password';

        $this->assertSame('id', $user->getAuthIdentifierName());
        $this->assertSame('hashed_password', $user->getAuthPassword());
    }
}
