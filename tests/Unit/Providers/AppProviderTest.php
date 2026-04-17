<?php
declare(strict_types=1);

namespace Tests\Unit\Providers;

use App\Providers\AppProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(AppProvider::class)]
final class AppProviderTest extends TestCase
{
    #[Test]
    public function registerDoesNotThrow(): void
    {
        $provider = new AppProvider();
        $provider->register();

        $this->assertTrue(true); // No exception
    }

    #[Test]
    public function hasProviderAttribute(): void
    {
        $ref = new \ReflectionClass(AppProvider::class);
        $attrs = $ref->getAttributes();
        $names = array_map(fn(\ReflectionAttribute $a) => $a->getName(), $attrs);

        $this->assertContains('MonkeysLegion\Framework\Attributes\Provider', $names);
    }

    #[Test]
    public function canBeInstantiated(): void
    {
        $provider = new AppProvider();

        $this->assertInstanceOf(AppProvider::class, $provider);
    }
}
