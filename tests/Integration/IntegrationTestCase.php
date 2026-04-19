<?php
declare(strict_types=1);

namespace Tests\Integration;

use MonkeysLegion\DI\Container;
use MonkeysLegion\Framework\Application;
use MonkeysLegion\Config\Providers\MiddlewareProvider;
use MonkeysLegion\Config\Providers\RoutingProvider;
use MonkeysLegion\Config\Providers\SessionProvider;
use MonkeysLegion\Config\Providers\TemplateProvider;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Http\Message\Stream;
use MonkeysLegion\Http\MiddlewareDispatcher;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\UriFactory;

/**
 * Base class for integration tests.
 *
 * Boots the v2 Application container and provides HTTP request helpers.
 */
abstract class IntegrationTestCase extends TestCase
{
    protected Container $container;
    protected ?MiddlewareDispatcher $dispatcher = null;

    protected function setUp(): void
    {
        if (!defined('ML_BASE_PATH')) {
            define('ML_BASE_PATH', realpath(__DIR__ . '/../../'));
        }

        $this->container = Application::create(basePath: ML_BASE_PATH)
            ->withProviders([
                RoutingProvider::class,
                SessionProvider::class,
                TemplateProvider::class,
                MiddlewareProvider::class,
            ])
            ->boot();

        try {
            if ($this->container->has(MiddlewareDispatcher::class)) {
                $this->dispatcher = $this->container->get(MiddlewareDispatcher::class);
            }
        } catch (\Throwable) {
            // MiddlewareDispatcher not resolvable — dispatch() will skip tests
        }
    }

    // ── Dispatch ───────────────────────────────────────────────

    /**
     * Dispatch a request through the middleware pipeline.
     */
    protected function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->dispatcher === null) {
            $this->markTestSkipped('MiddlewareDispatcher not available in the container.');
        }

        return $this->dispatcher->handle($request);
    }

    // ── Request Factories ──────────────────────────────────────

    /**
     * @param array<string, string|string[]> $headers
     */
    protected function createRequest(
        string $method,
        string $uri,
        array $headers = [],
        ?string $body = null,
    ): ServerRequestInterface {
        $request = (new ServerRequestFactory())
            ->createServerRequest($method, (new UriFactory())->createUri($uri));

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if ($body !== null) {
            $request = $request->withBody(Stream::createFromString($body));
        }

        return $request;
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     */
    protected function json(
        string $method,
        string $uri,
        array $data = [],
        array $headers = [],
    ): ServerRequestInterface {
        $headers['Content-Type'] = 'application/json';
        $headers['Accept'] = 'application/json';

        return $this->createRequest(
            $method,
            $uri,
            $headers,
            json_encode($data, JSON_THROW_ON_ERROR),
        );
    }

    // ── Shorthand Methods ──────────────────────────────────────

    /**
     * @param array<string, string> $headers
     */
    protected function get(string $uri, array $headers = []): ServerRequestInterface
    {
        return $this->createRequest('GET', $uri, $headers);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     */
    protected function post(string $uri, array $data = [], array $headers = []): ServerRequestInterface
    {
        return $this->json('POST', $uri, $data, $headers);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     */
    protected function put(string $uri, array $data = [], array $headers = []): ServerRequestInterface
    {
        return $this->json('PUT', $uri, $data, $headers);
    }

    /**
     * @param array<string, string> $headers
     */
    protected function delete(string $uri, array $headers = []): ServerRequestInterface
    {
        return $this->createRequest('DELETE', $uri, $headers);
    }

    // ── Assertions ─────────────────────────────────────────────

    protected function assertStatus(ResponseInterface $response, int $expected): void
    {
        $this->assertSame(
            $expected,
            $response->getStatusCode(),
            sprintf(
                'Expected status %d, got %d. Body: %s',
                $expected,
                $response->getStatusCode(),
                mb_substr((string) $response->getBody(), 0, 500),
            ),
        );
    }

    /**
     * @param array<mixed> $expected
     */
    protected function assertJsonResponse(ResponseInterface $response, array $expected): void
    {
        $this->assertStringContainsString(
            'application/json',
            $response->getHeaderLine('Content-Type'),
        );

        $body = (string) $response->getBody();
        $this->assertJson($body);

        $actual = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals($expected, $actual);
    }
}
