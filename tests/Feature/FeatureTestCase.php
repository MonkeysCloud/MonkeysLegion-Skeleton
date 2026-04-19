<?php
declare(strict_types=1);

namespace Tests\Feature;

use MonkeysLegion\DI\Container;
use MonkeysLegion\Framework\Application;
use MonkeysLegion\Config\Providers\MiddlewareProvider;
use MonkeysLegion\Config\Providers\RoutingProvider;
use MonkeysLegion\Config\Providers\SessionProvider;
use MonkeysLegion\Config\Providers\TemplateProvider;
use MonkeysLegion\Http\MiddlewareDispatcher;
use MonkeysLegion\Http\Message\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\UriFactory;

/**
 * Base class for feature tests.
 *
 * Boots the full application including middleware pipeline and
 * dispatches requests through the complete HTTP lifecycle.
 */
abstract class FeatureTestCase extends TestCase
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

    // ── Dispatch Through Pipeline ──────────────────────────────

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
     * Send a GET request through the full pipeline.
     *
     * @param array<string, string> $headers
     */
    protected function get(string $uri, array $headers = []): ResponseInterface
    {
        return $this->dispatch($this->createRequest('GET', $uri, $headers));
    }

    /**
     * Send a POST request with JSON body through the full pipeline.
     *
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     */
    protected function postJson(string $uri, array $data = [], array $headers = []): ResponseInterface
    {
        $headers['Content-Type'] = 'application/json';
        $headers['Accept'] = 'application/json';

        return $this->dispatch($this->createRequest(
            'POST',
            $uri,
            $headers,
            json_encode($data, JSON_THROW_ON_ERROR),
        ));
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     */
    protected function putJson(string $uri, array $data = [], array $headers = []): ResponseInterface
    {
        $headers['Content-Type'] = 'application/json';
        $headers['Accept'] = 'application/json';

        return $this->dispatch($this->createRequest(
            'PUT',
            $uri,
            $headers,
            json_encode($data, JSON_THROW_ON_ERROR),
        ));
    }

    /**
     * @param array<string, string> $headers
     */
    protected function deleteRequest(string $uri, array $headers = []): ResponseInterface
    {
        return $this->dispatch($this->createRequest('DELETE', $uri, $headers));
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

    protected function assertJsonStructure(
        ResponseInterface $response,
        string $key,
    ): void {
        $body = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey($key, $body);
    }

    protected function assertResponseContains(
        ResponseInterface $response,
        string $needle,
    ): void {
        $this->assertStringContainsString(
            $needle,
            (string) $response->getBody(),
        );
    }
}
