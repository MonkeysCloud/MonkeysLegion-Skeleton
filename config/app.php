<?php
declare(strict_types=1);

use MonkeysLegion\Database\MySQL\Connection;
use MonkeysLegion\Router\RouteCollection;
use MonkeysLegion\Router\Router;
use MonkeysLegion\Core\Routing\RouteLoader;
use MonkeysLegion\Core\Middleware\CorsMiddleware;
use MonkeysLegion\Mlc\Parser      as MlcParser;
use MonkeysLegion\Mlc\Loader      as MlcLoader;
use MonkeysLegion\Mlc\Config      as MlcConfig;

return [

    // ------------------------------------------------------------------------
    // MonkeysLegion Config (.mlc) support
    // ------------------------------------------------------------------------
    MlcParser::class => fn() => new MlcParser(),
    MlcLoader::class => fn($c) => new MlcLoader(
        $c->get(MlcParser::class),
        base_path('config')
    ),
    // Load app.mlc and cors.mlc (later files override)
    MlcConfig::class => fn($c) => $c
        ->get(MlcLoader::class)
        ->load(['app','cors']),

    // ------------------------------------------------------------------------
    // Database connection (MySQL 8.4+)
    // ------------------------------------------------------------------------
    Connection::class => fn() => new Connection(
        require __DIR__ . '/database.php'
    ),

    // ------------------------------------------------------------------------
    // HTTP routing
    // ------------------------------------------------------------------------
    RouteCollection::class => fn() => new RouteCollection(),
    Router::class          => fn($c) => new Router(
        $c->get(RouteCollection::class)
    ),
    RouteLoader::class     => fn($c) => new RouteLoader(
        $c->get(Router::class),
        $c,
        base_path('app/Controller'),
        'App\\Controller'
    ),

    // ------------------------------------------------------------------------
    // CORS middleware configuration (via .mlc)
    // ------------------------------------------------------------------------
    CorsMiddleware::class => function($c) {
        /** @var MlcConfig $cfg */
        $cfg = $c->get(MlcConfig::class);

        $origin  = $cfg->get('cors.allow_origin', '*');
        $methods = $cfg->get('cors.allow_methods', ['GET','POST','OPTIONS']);
        $headers = $cfg->get('cors.allow_headers', ['Content-Type','Authorization']);

        return new CorsMiddleware(
            is_array($origin)  ? implode(',', $origin)  : $origin,
            is_array($methods) ? implode(',', $methods) : $methods,
            is_array($headers) ? implode(',', $headers) : $headers
        );
    },
];