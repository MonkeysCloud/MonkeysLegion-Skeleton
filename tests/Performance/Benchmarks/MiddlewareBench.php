<?php
declare(strict_types=1);

namespace Tests\Performance\Benchmarks;

use MonkeysLegion\Http\Middleware\MiddlewareDispatcher;
use MonkeysLegion\Http\Message\Request;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Http\Message\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Benchmark middleware pipeline performance.
 */
final class MiddlewareBench
{
    private MiddlewareDispatcher $dispatcher1;
    private MiddlewareDispatcher $dispatcher5;
    private MiddlewareDispatcher $dispatcher10;

    public function __construct()
    {
        $this->dispatcher1 = $this->buildDispatcher(1);
        $this->dispatcher5 = $this->buildDispatcher(5);
        $this->dispatcher10 = $this->buildDispatcher(10);
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchPipeline1(): void
    {
        $this->dispatcher1->handle($this->makeRequest());
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchPipeline5(): void
    {
        $this->dispatcher5->handle($this->makeRequest());
    }

    /**
     * @Revs(100)
     * @Iterations(10)
     * @Warmup(2)
     */
    public function benchPipeline10(): void
    {
        $this->dispatcher10->handle($this->makeRequest());
    }

    private function buildDispatcher(int $middlewareCount): MiddlewareDispatcher
    {
        $middleware = [];
        for ($i = 0; $i < $middlewareCount; $i++) {
            $middleware[] = new PassthroughMiddleware();
        }

        return new MiddlewareDispatcher(
            $middleware,
            new FinalHandler(),
        );
    }

    private function makeRequest(): ServerRequestInterface
    {
        return new Request('GET', 'http://localhost:8080/test', []);
    }
}

final class PassthroughMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        return $handler->handle($request);
    }
}

final class FinalHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response(Stream::createFromString('ok'), 200, []);
    }
}
