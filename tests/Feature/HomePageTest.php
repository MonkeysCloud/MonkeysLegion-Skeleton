<?php
declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;

/**
 * Feature test for the home page.
 */
final class HomePageTest extends FeatureTestCase
{
    #[Test]
    public function homePageReturns200(): void
    {
        $response = $this->get('/');

        $this->assertStatus($response, 200);
    }

    #[Test]
    public function homePageContainsHtmlContentType(): void
    {
        $response = $this->get('/');

        $this->assertStringContainsString(
            'text/html',
            $response->getHeaderLine('Content-Type'),
        );
    }
}
