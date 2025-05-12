<?php
declare(strict_types=1);

define('ML_BASE_PATH', __DIR__);

// 0) Autoload dependencies
require __DIR__ . '/vendor/autoload.php';

// 1) Build DI container
$container = (new \MonkeysLegion\DI\ContainerBuilder())
    ->addDefinitions(require __DIR__ . '/config/app.php')
    ->build();

// 2) Auto‑discover controller routes
$container
    ->get(\MonkeysLegion\Core\Routing\RouteLoader::class)
    ->loadControllers();

// 3) Create PSR‑7 request and resolve router
$request = \MonkeysLegion\Http\Message\ServerRequest::fromGlobals();
$router  = $container->get(\MonkeysLegion\Router\Router::class);

// 4) Handle CORS and dispatch through the router
$cors     = $container->get(\MonkeysLegion\Core\Middleware\CorsMiddleware::class);
$response = $cors(
    $request,
    fn(\Psr\Http\Message\ServerRequestInterface $req) => $router->dispatch($req)
);

// 5) Emit the HTTP response
(new \MonkeysLegion\Http\Emitter\SapiEmitter())->emit($response);