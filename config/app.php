<?php
declare(strict_types=1);

use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UploadedFileFactory;
use Laminas\Diactoros\UriFactory;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

use Psr\SimpleCache\CacheInterface;
use MonkeysLegion\Http\SimpleFileCache;

use MonkeysLegion\Cli\CliKernel;
use MonkeysLegion\Cli\Command\{
    ClearCacheCommand,
    DatabaseMigrationCommand,
    KeyGenerateCommand,
    MakeEntityCommand,
    MigrateCommand,
    RollbackCommand
};

use MonkeysLegion\Core\Middleware\CorsMiddleware;
use MonkeysLegion\Core\Routing\RouteLoader;
use MonkeysLegion\Database\MySQL\Connection;
use MonkeysLegion\Entity\Scanner\EntityScanner;

use MonkeysLegion\Http\{
    CoreRequestHandler,
    RouteRequestHandler,
    Middleware\AuthMiddleware,
    Middleware\LoggingMiddleware,
    Middleware\RateLimitMiddleware,
    MiddlewareDispatcher,
    Emitter\SapiEmitter
};

use MonkeysLegion\Migration\MigrationGenerator;
use MonkeysLegion\Mlc\{
    Config as MlcConfig,
    Loader as MlcLoader,
    Parser as MlcParser
};

use MonkeysLegion\Router\{
    RouteCollection,
    Router
};

use MonkeysLegion\Template\{
    Compiler as TemplateCompiler,
    Loader   as TemplateLoader,
    Parser   as TemplateParser,
    Renderer as TemplateRenderer
};

/*
|-------------------------------------------------------------------------
| Dependency-injection definitions
|-------------------------------------------------------------------------
*/
return [

    /* ----------------------------------------------------------------- */
    /* PSR-17 factories                                                   */
    /* ----------------------------------------------------------------- */
    ResponseFactoryInterface::class     => fn() => new ResponseFactory(),
    StreamFactoryInterface::class       => fn() => new StreamFactory(),
    UploadedFileFactoryInterface::class => fn() => new UploadedFileFactory(),
    UriFactoryInterface::class          => fn() => new UriFactory(),

    /* ----------------------------------------------------------------- */
    /* PSR-7 ServerRequest (create once from globals)                    */
    /* ----------------------------------------------------------------- */
    ServerRequestInterface::class       => fn() => (new ServerRequestFactory())->fromGlobals(),

    /* ----------------------------------------------------------------- */
    /* PSR-16 Cache (file-based fallback for rate-limiting)              */
    /* ----------------------------------------------------------------- */
    CacheInterface::class               => fn() => new SimpleFileCache(
        __DIR__ . '/../var/cache/rate_limit'
    ),

    /* ----------------------------------------------------------------- */
    /* .mlc config support                                                */
    /* ----------------------------------------------------------------- */
    MlcParser::class                    => fn()   => new MlcParser(),
    MlcLoader::class                    => fn($c) => new MlcLoader(
        $c->get(MlcParser::class),
        base_path('config')
    ),
    MlcConfig::class                    => fn($c) => $c
        ->get(MlcLoader::class)
        ->load(['app', 'cors', 'cache', 'auth']),

    /* ----------------------------------------------------------------- */
    /* Template engine                                                    */
    /* ----------------------------------------------------------------- */
    TemplateParser::class   => fn()   => new TemplateParser(),
    TemplateCompiler::class => fn($c) => new TemplateCompiler($c->get(TemplateParser::class)),
    TemplateLoader::class   => fn()   => new TemplateLoader(
        base_path('resources/views'),
        base_path('var/cache/views')
    ),
    TemplateRenderer::class => fn($c) => new TemplateRenderer(
        $c->get(TemplateParser::class),
        $c->get(TemplateCompiler::class),
        $c->get(TemplateLoader::class),
        (bool) $c->get(MlcConfig::class)->get('cache.enabled', true)
    ),

    /* ----------------------------------------------------------------- */
    /* Database                                                            */
    /* ----------------------------------------------------------------- */
    Connection::class         => fn() => new Connection(require __DIR__ . '/database.php'),

    /* ----------------------------------------------------------------- */
    /* Entity scanner + migration generator                               */
    /* ----------------------------------------------------------------- */
    EntityScanner::class      => fn() => new EntityScanner(base_path('app/Entity')),
    MigrationGenerator::class => fn() => new MigrationGenerator(),

    /* ----------------------------------------------------------------- */
    /* Routing                                                             */
    /* ----------------------------------------------------------------- */
    RouteCollection::class    => fn()   => new RouteCollection(),
    Router::class             => fn($c) => new Router($c->get(RouteCollection::class)),
    RouteLoader::class        => fn($c) => new RouteLoader(
        $c->get(Router::class),
        $c,
        base_path('app/Controller'),
        'App\\Controller'
    ),

    /* ----------------------------------------------------------------- */
    /* Adapt Router to PSR-15 RequestHandlerInterface                     */
    /* ----------------------------------------------------------------- */
    RouteRequestHandler::class => fn($c) => new RouteRequestHandler(
        $c->get(Router::class)
    ),

    /* ----------------------------------------------------------------- */
    /* Core PSR-15 dispatcher + final application handler                 */
    /* ----------------------------------------------------------------- */
    CoreRequestHandler::class => fn($c) => new CoreRequestHandler(
        $c->get(RouteRequestHandler::class),
        $c->get(ResponseFactoryInterface::class)
    ),

    /* ----------------------------------------------------------------- */
    /* Rate-limit middleware                                              */
    /* ----------------------------------------------------------------- */
    RateLimitMiddleware::class => fn($c) => new RateLimitMiddleware(
        $c->get(ResponseFactoryInterface::class),
        $c->get(CacheInterface::class),  // comment out to force in-memory only
        100,                              // max 100 requests
        60                                // per 60s window
    ),

    /* ----------------------------------------------------------------- */
    /* Authentication middleware                                          */
    /* ----------------------------------------------------------------- */
    AuthMiddleware::class       => fn($c) => new AuthMiddleware(
        $c->get(ResponseFactoryInterface::class),
        'Protected',
        (string) $c->get(MlcConfig::class)->get('auth.token'),
        ['/']                           // public paths
    ),

    /* ----------------------------------------------------------------- */
    /* Simple logging middleware                                          */
    /* ----------------------------------------------------------------- */
    LoggingMiddleware::class    => fn() => new LoggingMiddleware(),

    /* ----------------------------------------------------------------- */
    /* PSR-15 minimal middleware pipeline                                 */
    /* ----------------------------------------------------------------- */
    MiddlewareDispatcher::class => fn($c) => new MiddlewareDispatcher(
        [
            $c->get(CorsMiddleware::class),
            $c->get(RateLimitMiddleware::class),
            $c->get(AuthMiddleware::class),
            $c->get(LoggingMiddleware::class),
        ],
        $c->get(CoreRequestHandler::class)
    ),

    /* ----------------------------------------------------------------- */
    /* SAPI emitter                                                       */
    /* ----------------------------------------------------------------- */
    SapiEmitter::class          => fn() => new SapiEmitter(),

    /* ----------------------------------------------------------------- */
    /* CLI commands + kernel                                              */
    /* ----------------------------------------------------------------- */
    ClearCacheCommand::class        => fn() => new ClearCacheCommand(),
    KeyGenerateCommand::class       => fn() => new KeyGenerateCommand(),
    MigrateCommand::class           => fn($c) => new MigrateCommand($c->get(Connection::class)),
    RollbackCommand::class          => fn($c) => new RollbackCommand($c->get(Connection::class)),
    DatabaseMigrationCommand::class => fn($c) => new DatabaseMigrationCommand(
        $c->get(Connection::class),
        $c->get(EntityScanner::class),
        $c->get(MigrationGenerator::class)
    ),
    MakeEntityCommand::class        => fn() => new MakeEntityCommand(),

    CliKernel::class                => fn($c) => new CliKernel(
        $c,
        [
            ClearCacheCommand::class,
            KeyGenerateCommand::class,
            MigrateCommand::class,
            RollbackCommand::class,
            DatabaseMigrationCommand::class,
            MakeEntityCommand::class,
        ]
    ),
];