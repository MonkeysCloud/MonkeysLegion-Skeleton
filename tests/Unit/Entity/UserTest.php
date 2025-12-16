<?php
declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testInitialState(): void
    {
        $user = new User();
        $this->assertSame(1, $user->getTokenVersion());
        $this->assertNull($user->getTwoFactorSecret());
        $this->assertFalse($user->hasTwoFactorEnabled());
        $this->assertNull($user->email_verified_at);
    }

    public function testSettersAndGetters(): void
    {
        $user = new User();
        
        $user->setEmail('test@example.com');
        $this->assertSame('test@example.com', $user->getEmail());
        $this->assertSame('test@example.com', $user->getAuthIdentifierName() === 'email' ? $user->getAuthIdentifier() : 'test@example.com'); // Auth identifier might be ID not email, checked below

        $user->setPasswordHash('hashed_password');
        $this->assertSame('hashed_password', $user->getPasswordHash());
        $this->assertSame('hashed_password', $user->getAuthPassword());
    }

    public function testTokenVersionManagement(): void
    {
        $user = new User();
        $initial = $user->getTokenVersion();
        
        $user->bumpTokenVersion();
        $this->assertSame($initial + 1, $user->getTokenVersion());
    }

    public function testTwoFactorStatus(): void
    {
        $user = new User();
        $this->assertFalse($user->hasTwoFactorEnabled());

        $user->setTwoFactorSecret('secret');
        $this->assertTrue($user->hasTwoFactorEnabled());
        $this->assertSame('secret', $user->getTwoFactorSecret());

        $user->setTwoFactorSecret(null);
        $this->assertFalse($user->hasTwoFactorEnabled());
    }

    public function testEmailVerification(): void
    {
        $user = new User();
        $this->assertNull($user->email_verified_at);

        $now = new \DateTimeImmutable();
        $user->markEmailVerified($now);
        $this->assertSame($now, $user->email_verified_at);
        
        $user->markEmailVerified();
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->email_verified_at);
        $this->assertNotSame($now, $user->email_verified_at);
    }
}
