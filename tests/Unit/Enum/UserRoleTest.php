<?php
declare(strict_types=1);

namespace Tests\Unit\Enum;

use App\Enum\UserRole;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(UserRole::class)]
final class UserRoleTest extends TestCase
{
    #[Test]
    public function adminHasWildcardPermissions(): void
    {
        $this->assertSame(['*'], UserRole::Admin->permissions());
        $this->assertTrue(UserRole::Admin->isAdmin());
    }

    #[Test]
    public function editorHasContentPermissions(): void
    {
        $perms = UserRole::Editor->permissions();

        $this->assertContains('posts.*', $perms);
        $this->assertContains('comments.*', $perms);
        $this->assertFalse(UserRole::Editor->isAdmin());
    }

    #[Test]
    public function userHasLimitedPermissions(): void
    {
        $perms = UserRole::User->permissions();

        $this->assertContains('posts.view', $perms);
        $this->assertNotContains('posts.*', $perms);
        $this->assertFalse(UserRole::User->isAdmin());
    }

    #[Test]
    public function labelReturnsHumanReadable(): void
    {
        $this->assertSame('Administrator', UserRole::Admin->label());
        $this->assertSame('Editor', UserRole::Editor->label());
        $this->assertSame('User', UserRole::User->label());
    }

    #[Test]
    public function backedValuesAreStrings(): void
    {
        $this->assertSame('admin', UserRole::Admin->value);
        $this->assertSame('editor', UserRole::Editor->value);
        $this->assertSame('user', UserRole::User->value);
    }
}
