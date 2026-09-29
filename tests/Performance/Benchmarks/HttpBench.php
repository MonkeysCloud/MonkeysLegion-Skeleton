<?php
declare(strict_types=1);

namespace Tests\Performance\Benchmarks;

use MonkeysLegion\Http\Message\Request;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Http\Message\Stream;

/**
 * Benchmark HTTP message creation and serialization.
 */
final class HttpBench
{
    private string $jsonPayload;

    public function __construct()
    {
        $this->jsonPayload = json_encode([
            'data' => array_map(fn($i) => ['id' => $i, 'name' => "Item {$i}"], range(1, 50)),
            'meta' => ['total' => 50, 'page' => 1],
        ]);
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchCreateJsonResponse(): void
    {
        Response::json(['data' => ['id' => 1, 'name' => 'Test']]);
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchCreateHtmlResponse(): void
    {
        Response::html('<html><body><h1>Hello</h1></body></html>');
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchCreateRequest(): void
    {
        new Request('GET', 'http://localhost:8080/api/users/42', ['Accept' => 'application/json']);
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchCreateLargeJsonResponse(): void
    {
        Response::json(json_decode($this->jsonPayload, true));
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchCreateStream(): void
    {
        Stream::createFromString(str_repeat('x', 4096));
    }
}
