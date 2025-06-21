<?php

use Psr\Http\Message\ResponseFactoryInterface;
use Dotenv\Dotenv;
use Psr\Log\LoggerInterface;

define('ML_BASE_PATH', dirname(__DIR__));
require ML_BASE_PATH . '/vendor/autoload.php';


// ── Load environment variables ────────────────────────────────────
$dotenv = Dotenv::createImmutable(ML_BASE_PATH);
$dotenv->load();

$dotenv = Dotenv::createImmutable(ML_BASE_PATH, '.env.' . $_ENV['APP_ENV']);
$dotenv->load();

// ── Error / debug mode ────────────────────────────────────────────
if (
    ($_ENV['APP_DEBUG'] === 'true' || $_ENV['APP_DEBUG'] === '1') &&
    strpos($_ENV['APP_ENV'], 'prod') === false
) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);
}

/* -------------------------------------------------
 | 1) Build the DI-container
 * ------------------------------------------------*/
$container = new MonkeysLegion\DI\ContainerBuilder()
    ->addDefinitions(require ML_BASE_PATH . '/config/app.php');

/* -------------------------------------------------
 | 1.1) Auto-register any MonkeysLegion “providers”
 * ------------------------------------------------*/
// read composer.json → extra.monkeyslegion.providers
$composer = json_decode(
    file_get_contents(ML_BASE_PATH . '/composer.json'),
    true
);

$providers = $composer['extra']['monkeyslegion']['providers'] ?? [];
foreach ($providers as $providerClass) {
    if (
        class_exists($providerClass)
        && method_exists($providerClass, 'register')
    ) {
        // let the provider bind its services into $container
        $providerClass::register($container);
    }
}

/* expose it globally after build (legacy helpers/controllers still use it) */
$container = $container->build();
define('ML_CONTAINER', $container);

$loggers = $composer['extra']['monkeyslegion']['loggers'] ?? [];
foreach ($loggers as $loggerClass) {
    if (
        class_exists($loggerClass)
        && method_exists($loggerClass, 'setLogger')
    ) {
        // let the provider bind its services into $container
        $loggerClass::setLogger($container->get(LoggerInterface::class));
    }
}
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
$routeHandler = $container->get(MonkeysLegion\Http\RouteRequestHandler::class);
$core         = new MonkeysLegion\Http\CoreRequestHandler($routeHandler, $responseFactory);

// pipe your middleware in the desired order
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
