<?php
declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Middleware\TimingMiddleware;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Http\Message\Stream;

#[CoversClass(TimingMiddleware::class)]
final class TimingMiddlewareTest extends TestCase
{
    #[Test]
    public function addsServerTimingHeader(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $response = new Response(Stream::createFromString('OK'), 200);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($response);

        $middleware = new TimingMiddleware();
        $result = $middleware->process($request, $handler);

        $this->assertTrue($result->hasHeader('Server-Timing'));

        $timing = $result->getHeaderLine('Server-Timing');
        $this->assertMatchesRegularExpression('/total;dur=\d+\.\d+/', $timing);
    }

    #[Test]
    public function passesRequestToHandler(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = new Response(Stream::createFromString(''), 200);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        $middleware = new TimingMiddleware();
        $middleware->process($request, $handler);
    }
}
