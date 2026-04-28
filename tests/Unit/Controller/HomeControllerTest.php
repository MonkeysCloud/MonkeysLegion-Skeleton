<?php
declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\HomeController;
use MonkeysLegion\Template\Renderer;
use MonkeysLegion\Template\Compiler;
use MonkeysLegion\Template\Loader;
use MonkeysLegion\Template\Parser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(HomeController::class)]
final class HomeControllerTest extends TestCase
{
    #[Test]
    public function indexReturns200WithHtml(): void
    {
        // Renderer is final — create a real instance with a mock Loader
        $tempDir = sys_get_temp_dir() . '/ml_test_' . uniqid();
        mkdir($tempDir, 0755, true);
        $templatePath = $tempDir . '/home.ml.php';
        file_put_contents($templatePath, '<html><body>Home Page</body></html>');

        $loader = $this->createMock(Loader::class);
        $loader->method('getSourcePath')->with('home')->willReturn($templatePath);
        $loader->method('getCompiledPath')->willReturn($tempDir . '/home.compiled.php');

        $parser = new Parser();
        $compiler = new Compiler($parser);
        $renderer = new Renderer($parser, $compiler, $loader, false, $tempDir);

        $controller = new HomeController($renderer);
        $response = $controller->index();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString(
            'text/html',
            $response->getHeaderLine('Content-Type'),
        );
        $this->assertStringContainsString(
            'Home Page',
            (string) $response->getBody(),
        );

        // Cleanup
        @unlink($templatePath);
        @unlink($tempDir . '/home.compiled.php');
        @rmdir($tempDir);
    }
}
