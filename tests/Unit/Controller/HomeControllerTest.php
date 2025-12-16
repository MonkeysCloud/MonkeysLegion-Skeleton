<?php
declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\HomeController;
use MonkeysLegion\Template\Renderer;
use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase
{
    public function testIndexRendersHomeTemplate(): void
    {
        // 1. Setup temporary template file
        $tempDir = sys_get_temp_dir() . '/ml_test_views';
        if (!is_dir($tempDir)) {
            mkdir($tempDir);
        }
        $templatePath = $tempDir . '/home.ml.php';
        file_put_contents($templatePath, '<html>Home</html>');

        // 2. Mock Loader to return the temp file path
        $loader = $this->createMock(\MonkeysLegion\Template\Loader::class);
        $loader->method('getSourcePath')
            ->with('home')
            ->willReturn($templatePath);
        
        // Mock getCompiledPath to avoid cache issues or use a temp cache dir
        $loader->method('getCompiledPath')
            ->willReturn($tempDir . '/home.php');

        // 3. Create real Parser and Compiler (lightweight)
        $parser = new \MonkeysLegion\Template\Parser();
        $compiler = new \MonkeysLegion\Template\Compiler($parser);

        // 4. Instantiate real Renderer with mocked Loader
        // We use a temp cache dir for the renderer
        $renderer = new Renderer(
            $parser,
            $compiler,
            $loader,
            false, // cache enabled (false for testing to avoid complexity?) 
                   // actually if false, it still writes to compiled path? 
                   // Let's use false and ensure we have write permissions
            $tempDir
        );

        // 5. Inject into Controller
        $controller = new HomeController($renderer);
        $response = $controller->index();

        // 6. Assertions
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/html', $response->getHeaderLine('Content-Type'));
        $this->assertStringContainsString('<html>Home</html>', (string) $response->getBody());
        
        // Cleanup
        @unlink($templatePath);
        @unlink($tempDir . '/home.php');
        @rmdir($tempDir);
    }
}
