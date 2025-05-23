<?php
declare(strict_types=1);
if (function_exists('opcache_reset')) {
    opcache_reset();
}
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UploadedFileFactory;
use Laminas\Diactoros\UriFactory;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use MonkeysLegion\Http\SimpleFileCache;

use MonkeysLegion\Cli\CliKernel;
use MonkeysLegion\Cli\Command\{
    ClearCacheCommand,
    DatabaseMigrationCommand,
    KeyGenerateCommand,
    MakeEntityCommand,
    MigrateCommand,
    RollbackCommand,
    RouteListCommand,
    OpenApiExportCommand
};

use MonkeysLegion\Core\Middleware\CorsMiddleware;
use MonkeysLegion\Core\Routing\RouteLoader;
use MonkeysLegion\Database\MySQL\Connection;
use MonkeysLegion\Entity\Scanner\EntityScanner;

use MonkeysLegion\Http\{CoreRequestHandler,
    Middleware\ContentNegotiationMiddleware,
    RouteRequestHandler,
    Middleware\AuthMiddleware,
    Middleware\LoggingMiddleware,
    Middleware\RateLimitMiddleware,
    MiddlewareDispatcher,
    Emitter\SapiEmitter};

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

use Prometheus\Storage\APC;               // or Redis
use MonkeysLegion\Telemetry\{
    MetricsInterface,
    NullMetrics,
    PrometheusMetrics,
    StatsDMetrics
};

use MonkeysLegion\Events\{
    ListenerProvider,
    EventDispatcher
};

use MonkeysLegion\Http\OpenApi\{
    OpenApiGenerator,
    OpenApiMiddleware
};

use MonkeysLegion\Validation\ValidatorInterface;
use MonkeysLegion\Validation\AttributeValidator;
use MonkeysLegion\Validation\DtoBinder;
use MonkeysLegion\Validation\Middleware\ValidationMiddleware;

/*
|--------------------------------------------------------------------------
| Dependency-injection definitions
|--------------------------------------------------------------------------
*/
return [

    /*
    |--------------------------------------------------------------------------
    | PSR-3 Logger (Monolog)
    |--------------------------------------------------------------------------
    */
    LoggerInterface::class => function() {
        $log = new Logger('app');
        $log->pushHandler(
            new StreamHandler(
                base_path('var/log/app.log'),
                Logger::DEBUG
            )
        );
        return $log;
    },

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
    /* Metrics / Telemetry (choose one)                                   */
    /* ----------------------------------------------------------------- */
    // 1) No-op (default)
    MetricsInterface::class => fn() => new NullMetrics(),

    // 2) Prometheus (APC in dev – swap to Redis in prod)
    //MetricsInterface::class => fn() => new PrometheusMetrics(new APC()),

    // 3) StatsD
    //MetricsInterface::class => fn() => new StatsDMetrics('127.0.0.1', 8125),

    /* ———————————————————————————————————————————————
    *  Event dispatcher (PSR-14)
    * ——————————————————————————————————————————————— */
    ListenerProvider::class        => fn () => new ListenerProvider(),
    EventDispatcherInterface::class => fn ($c) => new EventDispatcher(
        $c->get(ListenerProvider::class)
    ),

    // Example: register a listener right here (commented)
    /*
    App\Listeners\AuditLogger::class => function ($c) {
        $cb = [$c->get(LoggerInterface::class), 'info'];
        $c->get(ListenerProvider::class)
           ->add(App\Events\UserDeleted::class, $cb, priority: 10);

        return new App\Listeners\AuditLogger();
    },
    */

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
    RateLimitMiddleware::class =>
        fn($c) => new RateLimitMiddleware(
            $c->get(ResponseFactoryInterface::class),
            $c->get(CacheInterface::class),
            200,   // limit
            60     // window (seconds)
        ),

    /* ----------------------------------------------------------------- */
    /* Authentication middleware                                          */
    /* ----------------------------------------------------------------- */
    AuthMiddleware::class       => fn($c) => new AuthMiddleware(
        $c->get(ResponseFactoryInterface::class),
        'Protected',
        (string) $c->get(MlcConfig::class)->get('auth.token'),
        ['/']  // public paths
    ),

    /* ----------------------------------------------------------------- */
    /* Simple logging middleware                                          */
    /* ----------------------------------------------------------------- */
    LoggingMiddleware::class    => fn() => new LoggingMiddleware(
    // you can inject LoggerInterface here if your middleware takes it
    ),

    /* ----------------------------------------------------------------- */
    /* Validation layer                                                  */
    /* ----------------------------------------------------------------- */
    ValidatorInterface::class => fn() => new AttributeValidator(),

    DtoBinder::class          => fn($c) => new DtoBinder(
        $c->get(ValidatorInterface::class)
    ),

    /**
     * Map <router-name ⇒ DTO class>.  Adjust to your routes.
     * Example assumes you have a CreateUserRequest DTO.
     */
    ValidationMiddleware::class => fn($c) => new ValidationMiddleware(
        $c->get(DtoBinder::class),
        [
            // 'user_create' => \App\Http\Dto\CreateUserRequest::class,
            // 'order_create' => \App\Http\Dto\CreateOrderRequest::class,
        ]
    ),

    /*----------------------------------------------------*/
    /*  OpenAPI                                           */
    /*----------------------------------------------------*/
    OpenApiGenerator::class => fn($c) => new MonkeysLegion\Http\OpenApi\OpenApiGenerator(
        $c->get(RouteCollection::class)
    ),

    OpenApiMiddleware::class => fn($c) => new MonkeysLegion\Http\OpenApi\OpenApiMiddleware(
        $c->get(OpenApiGenerator::class),
        $c->get(ResponseFactoryInterface::class)
    ),

    /* ----------------------------------------------------------------- */
    /* PSR-15 minimal middleware pipeline                                 */
    /* ----------------------------------------------------------------- */
    MiddlewareDispatcher::class => fn($c) => new MiddlewareDispatcher(
        [
            $c->get(CorsMiddleware::class),
            $c->get(RateLimitMiddleware::class),
            $c->get(AuthMiddleware::class),
            $c->get(LoggingMiddleware::class),
            $c->get(ContentNegotiationMiddleware::class),
            $c->get(ValidationMiddleware::class),
            $c->get(OpenApiMiddleware::class),
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
    RouteListCommand::class         => fn($c) => new RouteListCommand(
            $c->get(RouteCollection::class)
    ),
    OpenApiExportCommand::class     => fn($c) => new OpenApiExportCommand(
            $c->get(OpenApiGenerator::class)
    ),

    CliKernel::class                => fn($c) => new CliKernel(
        $c,
        [
            ClearCacheCommand::class,
            KeyGenerateCommand::class,
            MigrateCommand::class,
            RollbackCommand::class,
            DatabaseMigrationCommand::class,
            MakeEntityCommand::class,
            RouteListCommand::class,
            OpenApiExportCommand::class,
        ]
    ),
];