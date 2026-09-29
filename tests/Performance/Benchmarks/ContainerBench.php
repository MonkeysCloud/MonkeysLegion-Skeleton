<?php
declare(strict_types=1);

namespace Tests\Performance\Benchmarks;

use MonkeysLegion\DI\Container;
use MonkeysLegion\DI\ContainerBuilder;
use MonkeysLegion\DI\ContainerDumper;

/**
 * Benchmark DI container service resolution.
 */
final class ContainerBench
{
    private Container $container;

    public function __construct()
    {
        $builder = new ContainerBuilder();
        $builder->set(SimpleService::class, fn() => new SimpleService());
        $builder->set(DependentService::class, fn($c) => new DependentService($c->get(SimpleService::class)));
        $builder->set(Logger::class, fn() => new EchoLogger(), );
        $builder->set(ComplexService::class, fn($c) => new ComplexService(
            $c->get(SimpleService::class),
            $c->get(DependentService::class),
            $c->get(Logger::class),
        ));

        $this->container = $builder->build();
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchResolveSimple(): void
    {
        $this->container->get(SimpleService::class);
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchResolveDependent(): void
    {
        $this->container->get(DependentService::class);
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchResolveComplex(): void
    {
        $this->container->get(ComplexService::class);
    }

    /**
     * @Revs(50)
     * @Iterations(5)
     * @Warmup(1)
     */
    public function benchHasCheck(): void
    {
        $this->container->has(ComplexService::class);
    }
}

// ── Fixtures ──────────────────────────────────────────────────

final class SimpleService
{
    public function __construct() {}
}

final class DependentService
{
    public function __construct(private SimpleService $dep) {}
}

final class EchoLogger
{
    public function log(string $msg): void {}
}

final class ComplexService
{
    public function __construct(
        private SimpleService $a,
        private DependentService $b,
        private EchoLogger $c,
    ) {}
}
