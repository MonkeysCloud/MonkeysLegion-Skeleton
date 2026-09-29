<?php
declare(strict_types=1);

namespace Tests\Performance\Benchmarks;

use MonkeysLegion\Validation\Validator;

/**
 * Benchmark DTO validation performance.
 */
final class ValidationBench
{
    private Validator $validator;
    private array $data5;
    private array $data10;
    private array $data20;

    public function __construct()
    {
        $this->validator = new Validator();

        $this->data5 = [
            'name'  => 'John Doe',
            'email' => 'john@example.com',
            'age'   => 30,
            'city'  => 'NYC',
            'phone' => '555-1234',
        ];

        $this->data10 = array_merge($this->data5, [
            'address'  => '123 Main St',
            'zip'      => '10001',
            'country'  => 'USA',
            'company'  => 'Acme Inc',
            'website'  => 'https://example.com',
        ]);

        $this->data20 = array_merge($this->data10, [
            'bio'      => 'Software developer',
            'twitter'  => '@johndoe',
            'github'   => 'johndoe',
            'linkedin' => 'johndoe',
            'salary'   => 75000,
            'bonus'    => 5000,
            'start_date' => '2024-01-15',
            'department' => 'Engineering',
            'level'    => 'Senior',
            'remote'   => true,
        ]);
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchValidate5Fields(): void
    {
        $this->validator->validate($this->data5);
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchValidate10Fields(): void
    {
        $this->validator->validate($this->data10);
    }

    /**
     * @Revs(50)
     * @Iterations(5)
     * @Warmup(1)
     */
    public function benchValidate20Fields(): void
    {
        $this->validator->validate($this->data20);
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchValidateEmpty(): void
    {
        $this->validator->validate([]);
    }
}
