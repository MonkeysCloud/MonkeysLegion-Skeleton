<?php
declare(strict_types=1);

// 1) Define base path and include Composer’s autoloader
use Psr\Http\Message\ServerRequestInterface;

define('ML_BASE_PATH', dirname(__DIR__));
require ML_BASE_PATH . '/vendor/autoload.php';

// 2) Build the DI container
$container = (new MonkeysLegion\DI\ContainerBuilder())
    ->addDefinitions(require ML_BASE_PATH . '/config/app.php')
    ->build();

// 3) Auto‑discover and register any #[Route] controllers
$container
    ->get(MonkeysLegion\Core\Routing\RouteLoader::class)
    ->loadControllers();

// 4) Create PSR‑7 request and resolve the router
$request = MonkeysLegion\Http\Message\ServerRequest::fromGlobals();
$router  = $container->get(MonkeysLegion\Router\Router::class);

// 5) Handle CORS preflight or add CORS headers, then dispatch
$cors     = $container->get(MonkeysLegion\Core\Middleware\CorsMiddleware::class);
$response = $cors(
    $request,
    fn(ServerRequestInterface $req) => $router->dispatch($req)
);

// 6) Emit the HTTP response
(new MonkeysLegion\Http\Emitter\SapiEmitter())->emit($response);