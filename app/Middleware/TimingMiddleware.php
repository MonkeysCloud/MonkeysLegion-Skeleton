<?php
declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Adds Server-Timing headers to all responses.
 */
final class TimingMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $start = hrtime(true);
        $response = $handler->handle($request);
        $durationMs = (hrtime(true) - $start) / 1e6;

        return $response->withHeader(
            'Server-Timing',
            sprintf('total;dur=%.2f', $durationMs),
        );
    }
}
