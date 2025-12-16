<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Helpers are loaded in bootstrap/autoload but we can ensure they are available
        require_once __DIR__ . '/../../src/Template/helpers.php';
    }

    public function testCsrfTokenGeneratesString(): void
    {
        // Mock session if needed, or rely on PHP's behavior (may fail in CLI if headers sent)
        // For basic CLI test, we might suppress warnings or use @
        
        $token = @csrf_token();
        $this->assertNotEmpty($token);
    }

    public function testCsrfFieldContainsToken(): void
    {
        $field = @csrf_field();
        $this->assertStringContainsString('<input type="hidden" name="_csrf"', $field);
    }
}
