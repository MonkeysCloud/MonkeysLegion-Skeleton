<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseFactoryInterface;

define('ML_BASE_PATH', dirname(__DIR__));
require ML_BASE_PATH . '/vendor/autoload.php';

/* -------------------------------------------------
 | 1) Create a mutable ContainerBuilder
 * ------------------------------------------------*/
$builder = (new MonkeysLegion\DI\ContainerBuilder())
    ->addDefinitions(require ML_BASE_PATH . '/config/app.php');

/* -------------------------------------------------
 | 1.1) Auto-register MonkeysLegion “providers”
 * ------------------------------------------------*/
$composer  = json_decode(file_get_contents(ML_BASE_PATH . '/composer.json'), true);
$providers = $composer['extra']['monkeyslegion']['providers'] ?? [];

foreach ($providers as $providerClass) {
    if (class_exists($providerClass) && method_exists($providerClass, 'register')) {
        // let the provider add its services **to the builder**
        $providerClass::register($builder);
    }
}

/* -------------------------------------------------
 | 1.2) Build the immutable container *after* providers
 * ------------------------------------------------*/
$container = $builder->build();

/* expose it globally (legacy helpers/controllers still use it) */
define('ML_CONTAINER', $container);

/* -------------------------------------------------
 | 2) Route auto-discovery
 * ------------------------------------------------*/
$container->get(MonkeysLegion\Core\Routing\RouteLoader::class)
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
$routeHandler    = $container->get(MonkeysLegion\Http\RouteRequestHandler::class);
$responseFactory = $container->get(ResponseFactoryInterface::class);
$core            = new MonkeysLegion\Http\CoreRequestHandler($routeHandler, $responseFactory);

$core->pipe($container->get(MonkeysLegion\Core\Middleware\CorsMiddleware::class));
$core->pipe($container->get(MonkeysLegion\Http\Middleware\RateLimitMiddleware::class));
$core->pipe($container->get(MonkeysLegion\Http\Middleware\AuthMiddleware::class));
$core->pipe($container->get(MonkeysLegion\Http\Middleware\LoggingMiddleware::class));

$response = $core->handle($request);

/* -------------------------------------------------
 | 5) Emit HTTP response
 * ------------------------------------------------*/
$container->get(MonkeysLegion\Http\Emitter\SapiEmitter::class)
    ->emit($response);