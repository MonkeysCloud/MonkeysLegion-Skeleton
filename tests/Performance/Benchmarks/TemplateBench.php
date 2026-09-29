<?php
declare(strict_types=1);

namespace Tests\Performance\Benchmarks;

use MonkeysLegion\Template\Compiler;
use MonkeysLegion\Template\Renderer;

/**
 * Benchmark template rendering performance.
 */
final class TemplateBench
{
    private Renderer $renderer;

    public function __construct()
    {
        // Create a renderer with compiled templates
        $cacheDir = sys_get_temp_dir() . '/ml-bench-templates-' . uniqid();
        @mkdir($cacheDir, 0755, true);

        $this->renderer = new Renderer(
            viewsPath: __DIR__ . '/templates',
            cachePath: $cacheDir,
        );
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchRender10Vars(): void
    {
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $data["var{$i}"] = "value{$i}";
        }
        $this->renderer->render('simple', $data);
    }

    /**
     * @Revs(50)
     * @Iterations(5)
     * @Warmup(1)
     */
    public function benchRender100Vars(): void
    {
        $data = [];
        for ($i = 0; $i < 100; $i++) {
            $data["var{$i}"] = "value{$i}";
        }
        $this->renderer->render('simple', $data);
    }

    /**
     * @Revs(20)
     * @Iterations(5)
     * @Warmup(1)
     */
    public function benchRenderLoop1000(): void
    {
        $items = array_map(fn($i) => ['id' => $i, 'name' => "Item {$i}"], range(1, 1000));
        $this->renderer->render('loop', ['items' => $items]);
    }
}
