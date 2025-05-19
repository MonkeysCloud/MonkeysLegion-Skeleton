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
use MonkeysLegion\Http\{CoreRequestHandler,
    Middleware\AuthMiddleware,
    Middleware\LoggingMiddleware,
    Middleware\RateLimitMiddleware,
    MiddlewareDispatcher,
    Emitter\SapiEmitter,
    RouteRequestHandler};
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

    /* --------------------------------------------------------------------- */
    /*  PSR-17 factories                                                     */
    /* --------------------------------------------------------------------- */
    ResponseFactoryInterface::class      => fn() => new ResponseFactory(),
    StreamFactoryInterface::class        => fn() => new StreamFactory(),
    UploadedFileFactoryInterface::class  => fn() => new UploadedFileFactory(),
    UriFactoryInterface::class           => fn() => new UriFactory(),

    /* --------------------------------------------------------------------- */
    /*  PSR-7 ServerRequest (one-off from globals)                           */
    /* --------------------------------------------------------------------- */
    ServerRequestInterface::class        => fn() => new ServerRequestFactory()->fromGlobals(),

    /* --------------------------------------------------------------------- */
    /*  Config (.mlc) support                                                */
    /* --------------------------------------------------------------------- */
    MlcParser::class                     => fn()   => new MlcParser(),
    MlcLoader::class                     => fn($c) => new MlcLoader(
        $c->get(MlcParser::class),
        base_path('config')
    ),
    MlcConfig::class                     => fn($c) => $c->get(MlcLoader::class)
        ->load(['app', 'cors', 'cache']),

    /* --------------------------------------------------------------------- */
    /*  Template engine                                                      */
    /* --------------------------------------------------------------------- */
    TemplateParser::class                => fn()   => new TemplateParser(),
    TemplateCompiler::class              => fn($c) => new TemplateCompiler(
        $c->get(TemplateParser::class)
    ),
    TemplateLoader::class                => fn()   => new TemplateLoader(
        base_path('resources/views'),
        base_path('var/cache/views')
    ),
    TemplateRenderer::class              => fn($c) => new TemplateRenderer(
        $c->get(TemplateParser::class),
        $c->get(TemplateCompiler::class),
        $c->get(TemplateLoader::class),
        (bool) $c->get(MlcConfig::class)->get('cache.enabled', true)
    ),

    /* --------------------------------------------------------------------- */
    /*  Database connection                                                   */
    /* --------------------------------------------------------------------- */
    Connection::class                    => fn() => new Connection(
        require __DIR__ . '/database.php'
    ),

    /* --------------------------------------------------------------------- */
    /*  Entity scanner + migration generator                                 */
    /* --------------------------------------------------------------------- */
    EntityScanner::class                 => fn() => new EntityScanner(),
    MigrationGenerator::class            => fn() => new MigrationGenerator(),

    /* --------------------------------------------------------------------- */
    /*  Routing                                                              */
    /* --------------------------------------------------------------------- */
    RouteCollection::class => fn() => new RouteCollection(),
    Router::class          => fn($c) => new Router($c->get(RouteCollection::class)),
    RouteLoader::class     => fn($c) => new RouteLoader(
        $c->get(Router::class),
        $c,
        base_path('app/Controller'),
        'App\\Controller'
    ),

    RouteRequestHandler::class => fn($c) => new RouteRequestHandler(
        $c->get(Router::class)
    ),

    CoreRequestHandler::class => fn($c) => new CoreRequestHandler(
        $c->get(RouteRequestHandler::class),
        $c->get(ResponseFactoryInterface::class)
    ),

    MiddlewareDispatcher::class => fn($c) => new MiddlewareDispatcher(
        [
            $c->get(CorsMiddleware::class),
            $c->get(AuthMiddleware::class),
            $c->get(LoggingMiddleware::class),
            $c->get(RateLimitMiddleware::class),
        ],
        $c->get(CoreRequestHandler::class)
    ),

    SapiEmitter::class => fn() => new SapiEmitter(),

    /* --------------------------------------------------------------------- */
    /*  CORS middleware                                                      */
    /* --------------------------------------------------------------------- */
    CorsMiddleware::class                => fn($c) => new CorsMiddleware(
        is_array($origin = $c->get(MlcConfig::class)->get('cors.allow_origin', '*'))
            ? implode(',', $origin) : $origin,
        is_array($methods = $c->get(MlcConfig::class)->get('cors.allow_methods', ['GET','POST','OPTIONS']))
            ? implode(',', $methods) : $methods,
        is_array($headers = $c->get(MlcConfig::class)->get('cors.allow_headers', ['Content-Type','Authorization']))
            ? implode(',', $headers) : $headers
    ),

    /* --------------------------------------------------------------------- */
    /*  CLI                                                                  */
    /* --------------------------------------------------------------------- */
    ClearCacheCommand::class             => fn()   => new ClearCacheCommand(),
    KeyGenerateCommand::class            => fn()   => new KeyGenerateCommand(),
    MigrateCommand::class                => fn($c) => new MigrateCommand(
        $c->get(Connection::class)
    ),
    RollbackCommand::class               => fn($c) => new RollbackCommand(
        $c->get(Connection::class)
    ),
    DatabaseMigrationCommand::class      => fn($c) => new DatabaseMigrationCommand(
        $c->get(Connection::class),
        $c->get(EntityScanner::class),
        $c->get(MigrationGenerator::class)
    ),
    MakeEntityCommand::class             => fn()   => new MakeEntityCommand(),

    CliKernel::class                     => fn($c) => new CliKernel(
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