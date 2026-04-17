<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for global helper functions.
 */
final class HelperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once __DIR__ . '/../../src/helpers.php';
    }

    #[Test]
    public function basepathReturnsProjectRoot(): void
    {
        if (!defined('ML_BASE_PATH')) {
            define('ML_BASE_PATH', realpath(__DIR__ . '/../../'));
        }

        $this->assertSame(ML_BASE_PATH, base_path());
        $this->assertStringEndsWith('/config', base_path('config'));
    }

    #[Test]
    public function appPathReturnsAppDir(): void
    {
        $this->assertStringEndsWith('/app', app_path());
        $this->assertStringEndsWith('/app/Entity', app_path('Entity'));
    }

    #[Test]
    public function csrfTokenGeneratesString(): void
    {
        $token = @csrf_token();
        $this->assertNotEmpty($token);
    }

    #[Test]
    public function csrfFieldContainsToken(): void
    {
        $field = @csrf_field();
        $this->assertStringContainsString('<input type="hidden" name="_csrf"', $field);
    }
}
