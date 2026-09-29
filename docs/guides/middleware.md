# Middleware

MonKeysLegion implements PSR-15 middleware. Middleware wraps the request/response cycle, enabling cross-cutting concerns like CORS, authentication, logging, and caching.

## How Middleware Works

```
Request → MW1 → MW2 → Controller → Response
                                    ←
Response ← MW1 ← MW2 ←──────────────┘
```

Each middleware can modify the request before it reaches the controller, and the response after.

## Global Middleware

Configured in `config/middleware.mlc`, executed in order for every request:

```hocon
middleware {
    global = [
        "timing",
        "cors",
        "session",
        "csrf",
        "security-headers",
    ]
}
```

## Route-Level Middleware

Applied to individual routes via the `#[Middleware]` attribute:

```php
use MonkeysLegion\Router\Attributes\Middleware;

#[Route('POST', '/api/posts')]
#[Middleware(['auth', 'throttle:60,1'])]
public function create(): Response { ... }
```

## Creating Custom Middleware

```php
<?php
declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};

final class TimingMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $start = microtime(true);

        $response = $handler->handle($request);

        $elapsed = (microtime(true) - $start) * 1000;

        return $response->withHeader(
            'X-Response-Time',
            sprintf('%.3fms', $elapsed),
        );
    }
}
```

## Built-in Middleware

| Middleware | Purpose |
|-----------|---------|
| `cors` | CORS headers for API requests |
| `csrf` | CSRF token validation |
| `session` | Session management |
| `auth` | JWT/session authentication |
| `throttle` | Rate limiting |
| `timing` | Response time measurement |
| `security-headers` | XSS, clickjacking, MIME protection |
| `csp` | Content Security Policy headers |
| `compression` | Gzip response compression |
| `conditional` | Conditional GET (ETag, If-Modified-Since) |

## Middleware Aliases

Register aliases in `config/middleware.mlc`:

```hocon
middleware {
    aliases {
        auth     = "App\\Middleware\\AuthMiddleware"
        throttle = "App\\Middleware\\ThrottleMiddleware"
        cache    = "App\\Middleware\\CacheMiddleware"
    }
}
```

## Middleware Priority

Control execution order via priority:

```hocon
middleware {
    priority = [
        "App\\Middleware\\SecurityHeadersMiddleware",
        "App\\Middleware\\CorsMiddleware",
        "App\\Middleware\\AuthMiddleware",
        "App\\Middleware\\ThrottleMiddleware",
    ]
}
```

## Terminable Middleware

Run cleanup after the response is sent:

```php
final class LogMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        // This runs after the response is sent
        register_shutdown_function(function() use ($request, $response) {
            // Log request details
        });

        return $response;
    }
}
```
