<?php
declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\PageController;
use MonkeysLegion\Template\Compiler;
use MonkeysLegion\Template\Loader;
use MonkeysLegion\Template\Parser;
use MonkeysLegion\Template\Renderer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(PageController::class)]
final class PageControllerTest extends TestCase
{
    #[Test]
    public function aboutReturns200(): void
    {
        $tempDir = sys_get_temp_dir() . '/ml_test_' . uniqid();
        mkdir($tempDir, 0755, true);
        $templatePath = $tempDir . '/home.ml.php';
        file_put_contents($templatePath, '<html><body>About Page</body></html>');

        $loader = $this->createMock(Loader::class);
        $loader->method('getSourcePath')->with('home')->willReturn($templatePath);
        $loader->method('getCompiledPath')->willReturn($tempDir . '/home.compiled.php');

        $parser = new Parser();
        $compiler = new Compiler($parser);
        $renderer = new Renderer($parser, $compiler, $loader, false, $tempDir);

        $controller = new PageController($renderer);
        $response = $controller->about();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('text/html', $response->getHeaderLine('Content-Type'));
        $this->assertStringContainsString('About Page', (string) $response->getBody());

        @unlink($templatePath);
        @unlink($tempDir . '/home.compiled.php');
        @rmdir($tempDir);
    }
}
