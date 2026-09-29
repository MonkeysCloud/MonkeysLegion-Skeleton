<?php
declare(strict_types=1);

namespace Tests\Performance\Benchmarks;

use MonkeysLegion\Query\Hydrator\EntityHydrator;
use MonkeysLegion\Entity\EntityManager;

/**
 * Benchmark entity hydration performance.
 */
final class QueryBench
{
    private EntityHydrator $hydrator;
    private array $rows1;
    private array $rows10;
    private array $rows100;

    public function __construct()
    {
        $this->hydrator = new EntityHydrator(new EntityManager());

        $this->rows1 = $this->makeRows(1);
        $this->rows10 = $this->makeRows(10);
        $this->rows100 = $this->makeRows(100);
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchHydrate1(): void
    {
        $this->hydrator->hydrateAll(BenchUser::class, $this->rows1);
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchHydrate10(): void
    {
        $this->hydrator->hydrateAll(BenchUser::class, $this->rows10);
    }

    /**
     * @Revs(50)
     * @Iterations(5)
     * @Warmup(1)
     */
    public function benchHydrate100(): void
    {
        $this->hydrator->hydrateAll(BenchUser::class, $this->rows100);
    }

    private function makeRows(int $count): array
    {
        $rows = [];
        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'id'    => $i,
                'name'  => "User {$i}",
                'email' => "user{$i}@example.com",
                'active' => 1,
            ];
        }
        return $rows;
    }
}

// ── Fixture Entity ────────────────────────────────────────────

final class BenchUser
{
    public int $id;
    public string $name;
    public string $email;
    public bool $active;
}
