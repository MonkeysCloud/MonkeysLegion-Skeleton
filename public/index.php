<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use MonkeysLegion\Http\CoreRequestHandler;

define('ML_BASE_PATH', dirname(__DIR__));
require ML_BASE_PATH . '/vendor/autoload.php';

/* -------------------------------------------------
 | 1) Build the DI-container
 * ------------------------------------------------*/
$container = (new MonkeysLegion\DI\ContainerBuilder())
    ->addDefinitions(require ML_BASE_PATH . '/config/app.php')
    ->build();

/* expose it globally (legacy helpers/controllers still use it) */
define('ML_CONTAINER', $container);

/* -------------------------------------------------
 | 2) Route auto-discovery
 * ------------------------------------------------*/
$container
    ->get(MonkeysLegion\Core\Routing\RouteLoader::class)
    ->loadControllers();

/* -------------------------------------------------
 | 3) Create PSR-7 request + obtain final router handler
 * ------------------------------------------------*/
$request  = MonkeysLegion\Http\Message\ServerRequest::fromGlobals();
/** @var MonkeysLegion\Router\Router $router */
$router   = $container->get(MonkeysLegion\Router\Router::class);
/** @var ResponseFactoryInterface $responseFactory */
$responseFactory = $container->get(ResponseFactoryInterface::class);

/* -------------------------------------------------
 | 4) Build PSR-15 middleware pipeline
 * ------------------------------------------------*/
$core = new CoreRequestHandler($router, $responseFactory);

/* pipe middlewares in the **order you want them to run**  */
$core->pipe($container->get(MonkeysLegion\Core\Middleware\CorsMiddleware::class));
$core->pipe($container->get(MonkeysLegion\Http\Middleware\RateLimitMiddleware::class));
$core->pipe($container->get(MonkeysLegion\Http\Middleware\AuthMiddleware::class));
$core->pipe($container->get(MonkeysLegion\Http\Middleware\LoggingMiddleware::class));

$response = $core->handle($request);

/* -------------------------------------------------
 | 5) Emit HTTP response
 * ------------------------------------------------*/
$container
    ->get(MonkeysLegion\Http\Emitter\SapiEmitter::class)
    ->emit($response);