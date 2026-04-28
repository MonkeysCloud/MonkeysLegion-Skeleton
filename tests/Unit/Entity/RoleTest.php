<?php
declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\Role;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Role::class)]
final class RoleTest extends TestCase
{
    #[Test]
    public function slugIsLowercasedAndTrimmed(): void
    {
        $role = new Role();
        $role->slug = '  ADMIN  ';

        $this->assertSame('admin', $role->slug);
    }

    #[Test]
    public function nameCanBeSet(): void
    {
        $role = new Role();
        $role->name = 'Administrator';

        $this->assertSame('Administrator', $role->name);
    }

    #[Test]
    public function descriptionDefaultsToNull(): void
    {
        $role = new Role();

        $this->assertNull($role->description);
    }

    #[Test]
    public function descriptionCanBeSet(): void
    {
        $role = new Role();
        $role->description = 'Full system access';

        $this->assertSame('Full system access', $role->description);
    }

    #[Test]
    public function usersDefaultsToEmptyArray(): void
    {
        $role = new Role();

        $this->assertSame([], $role->users);
    }
}
