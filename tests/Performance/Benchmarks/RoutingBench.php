<?php
declare(strict_types=1);

namespace Tests\Performance\Benchmarks;

use MonkeysLegion\Router\RouteCollection;
use MonkeysLegion\Router\RouteCompiler;
use MonkeysLegion\Router\CompiledRoutes;

/**
 * Benchmark route matching performance.
 *
 * Measures the time to match a URL against compiled routes.
 */
final class RoutingBench
{
    private CompiledRoutes $routes10;
    private CompiledRoutes $routes100;
    private CompiledRoutes $routes500;

    public function __construct()
    {
        $this->routes10 = $this->buildRoutes(10);
        $this->routes100 = $this->buildRoutes(100);
        $this->routes500 = $this->buildRoutes(500);
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchMatch10Routes(): void
    {
        $this->routes10->match('GET', '/users/42');
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchMatch100Routes(): void
    {
        $this->routes100->match('GET', '/users/42');
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchMatch500Routes(): void
    {
        $this->routes500->match('GET', '/users/42');
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchMatchStaticRoute(): void
    {
        $this->routes100->match('GET', '/health');
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchMatchPostRoute(): void
    {
        $this->routes100->match('POST', '/api/users');
    }

    private function buildRoutes(int $count): CompiledRoutes
    {
        $collection = new RouteCollection();

        // Always add some standard routes
        $collection->add('GET', '/health', fn() => null, 'health');
        $collection->add('GET', '/', fn() => null, 'home');

        for ($i = 0; $i < $count; $i++) {
            $collection->add('GET', "/users/{id:\d+}", fn() => null, "users.show.{$i}");
            $collection->add('POST', "/api/users", fn() => null, "api.users.create.{$i}");
            $collection->add('GET', "/posts/{slug}", fn() => null, "posts.show.{$i}");
        }

        $compiler = new RouteCompiler();
        return $compiler->compile($collection);
    }
}
