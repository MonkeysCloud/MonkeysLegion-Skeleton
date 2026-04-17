<?php
declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Tests for User's RBAC interface methods (roles + permissions).
 */
#[CoversClass(User::class)]
final class UserRbacTest extends TestCase
{
    private function makeUserWithRoles(array $roles, array $permissions = []): User
    {
        $user = new User();
        $user->name = 'Test';
        $user->email = 'test@test.com';
        $user->password_hash = 'hash';

        $ref = new \ReflectionProperty($user, 'roles');
        $ref->setValue($user, $roles);

        $ref = new \ReflectionProperty($user, 'permissions');
        $ref->setValue($user, $permissions);

        return $user;
    }

    #[Test]
    public function getRolesReturnsAssignedRoles(): void
    {
        $user = $this->makeUserWithRoles(['admin', 'editor']);

        $this->assertSame(['admin', 'editor'], $user->getRoles());
    }

    #[Test]
    public function hasRoleReturnsTrueWhenAssigned(): void
    {
        $user = $this->makeUserWithRoles(['admin']);

        $this->assertTrue($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('editor'));
    }

    #[Test]
    public function hasAnyRoleMatchesAtLeastOne(): void
    {
        $user = $this->makeUserWithRoles(['editor']);

        $this->assertTrue($user->hasAnyRole(['admin', 'editor']));
        $this->assertFalse($user->hasAnyRole(['admin', 'superadmin']));
    }

    #[Test]
    public function hasAllRolesRequiresAll(): void
    {
        $user = $this->makeUserWithRoles(['admin', 'editor']);

        $this->assertTrue($user->hasAllRoles(['admin', 'editor']));
        $this->assertFalse($user->hasAllRoles(['admin', 'editor', 'superadmin']));
    }

    #[Test]
    public function getPermissionsReturnsAssigned(): void
    {
        $user = $this->makeUserWithRoles([], ['posts.view', 'posts.create']);

        $this->assertSame(['posts.view', 'posts.create'], $user->getPermissions());
    }

    #[Test]
    public function hasPermissionExactMatch(): void
    {
        $user = $this->makeUserWithRoles([], ['posts.view']);

        $this->assertTrue($user->hasPermission('posts.view'));
        $this->assertFalse($user->hasPermission('posts.delete'));
    }

    #[Test]
    public function hasPermissionWildcardMatch(): void
    {
        $user = $this->makeUserWithRoles([], ['posts.*']);

        $this->assertTrue($user->hasPermission('posts.view'));
        $this->assertTrue($user->hasPermission('posts.delete'));
        $this->assertFalse($user->hasPermission('users.view'));
    }

    #[Test]
    public function hasPermissionGlobalWildcard(): void
    {
        $user = $this->makeUserWithRoles([], ['*']);

        $this->assertTrue($user->hasPermission('anything'));
        $this->assertTrue($user->hasPermission('posts.view'));
    }

    #[Test]
    public function hasAnyPermissionMatchesAtLeastOne(): void
    {
        $user = $this->makeUserWithRoles([], ['posts.view']);

        $this->assertTrue($user->hasAnyPermission(['posts.view', 'posts.delete']));
        $this->assertFalse($user->hasAnyPermission(['users.view', 'users.delete']));
    }

    #[Test]
    public function hasAllPermissionsRequiresAll(): void
    {
        $user = $this->makeUserWithRoles([], ['posts.view', 'posts.create']);

        $this->assertTrue($user->hasAllPermissions(['posts.view', 'posts.create']));
        $this->assertFalse($user->hasAllPermissions(['posts.view', 'posts.delete']));
    }

    #[Test]
    public function getRememberTokenAndSet(): void
    {
        $user = new User();
        $user->name = 'Test';
        $user->email = 'test@test.com';

        $this->assertNull($user->getRememberToken());

        $user->setRememberToken('remember-me-123');
        $this->assertSame('remember-me-123', $user->getRememberToken());

        $user->setRememberToken(null);
        $this->assertNull($user->getRememberToken());
    }

    #[Test]
    public function getTokenVersionReturnsCurrentVersion(): void
    {
        $user = new User();
        $user->name = 'Test';
        $user->email = 'test@test.com';

        $this->assertSame(1, $user->getTokenVersion());

        $user->bumpTokenVersion();
        $this->assertSame(2, $user->getTokenVersion());
    }
}
