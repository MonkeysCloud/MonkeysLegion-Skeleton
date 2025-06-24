<?php
declare(strict_types=1);
if (function_exists('opcache_reset')) {
    opcache_reset();
}

use App\Repository\UserRepository;
use Laminas\Diactoros\ServerRequestFactory;

use MonkeysLegion\Auth\AuthService;
use MonkeysLegion\Auth\JwtService;
use MonkeysLegion\Auth\Middleware\AuthorizationMiddleware;
use MonkeysLegion\Auth\Middleware\JwtAuthMiddleware;
use MonkeysLegion\Auth\PasswordHasher;
use MonkeysLegion\AuthService\AuthorizationService;
use MonkeysLegion\Cli\Support\CommandFinder;
use MonkeysLegion\Query\QueryBuilder;
use MonkeysLegion\Repository\RepositoryFactory;
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
use MonkeysLegion\Http\Factory\HttpFactory;

use MonkeysLegion\Cli\CliKernel;

use MonkeysLegion\Core\Routing\RouteLoader;
use MonkeysLegion\Database\MySQL\Connection;
use MonkeysLegion\Entity\Scanner\EntityScanner;

use MonkeysLegion\Http\{CoreRequestHandler,
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

use MonkeysLegion\I18n\Translator;

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
    HttpFactory::class                  => fn() => new HttpFactory(),

    ResponseFactoryInterface::class     => fn($c) => $c->get(HttpFactory::class),
    StreamFactoryInterface::class       => fn($c) => $c->get(HttpFactory::class),
    UploadedFileFactoryInterface::class => fn($c) => $c->get(HttpFactory::class),
    UriFactoryInterface::class          => fn($c) => $c->get(HttpFactory::class),

    /* ----------------------------------------------------------------- */
    /* PSR-7 ServerRequest (create once from globals)                    */
    /* ----------------------------------------------------------------- */
    ServerRequestInterface::class       => fn() =>
    new ServerRequestFactory()->fromGlobals(),

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

    /* -----------------------------------------------------------------
     | Dynamic .mlc config loader
     | – picks up every *.mlc file in config/ at runtime
     * ---------------------------------------------------------------- */
    MlcConfig::class => static function ($c) {
        /** @var MlcLoader $loader */
        $loader = $c->get(MlcLoader::class);

        // 1 grab every *.mlc in the config dir
        $files = glob(base_path('config/*.mlc')) ?: [];

        // 2 turn "config/foo.mlc" into just "foo"
        $names = array_map(
            static fn (string $path) => pathinfo($path, PATHINFO_FILENAME),
            $files
        );

        // 3 deterministic order (alpha) so overrides are stable
        sort($names);

        return $loader->load($names);
    },

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

    Translator::class => fn($c) => new Translator(
    // fetch locale from env or request (here default 'en')
        $c->get(MlcConfig::class)->get('app.locale', 'en'),
        base_path('resources/lang'),
        'en'
    ),

    /* ----------------------------------------------------------------- */
    /* Database                                                            */
    /* ----------------------------------------------------------------- */
    Connection::class         => fn() => new Connection(require __DIR__ . '/database.php'),

    /* ----------------------------------------------------------------- */
    /* Query Builder & Repositories                                       */
    /* ----------------------------------------------------------------- */
    QueryBuilder::class   => fn($c) => new QueryBuilder($c->get(Connection::class)),

    RepositoryFactory::class => fn($c) => new RepositoryFactory(
        $c->get(QueryBuilder::class)
    ),

    /* ----------------------------------------------------------------- */
    /* Entity scanner + migration generator                               */
    /* ----------------------------------------------------------------- */
    EntityScanner::class      => fn() => new EntityScanner(base_path('app/Entity')),
    MigrationGenerator::class => fn($c) => new MigrationGenerator(
        $c->get(Connection::class)
    ),

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
    AuthMiddleware::class => fn($c) => new AuthMiddleware(
        // 1) Response factory
        $c->get(ResponseFactoryInterface::class),

        // 2) Realm name (stays the same)
        'Protected',

        // 3) Your token from app.mlc
        (string) $c->get(MlcConfig::class)->get('auth.token'),

        // 4) *All* public paths from app.mlc (fallback to ['/'] if key missing)
        $c->get(MlcConfig::class)
            ->get('auth.public_paths', ['/']),
    ),

    /* ----------------------------------------------------------------- */
    /* Simple logging middleware                                          */
    /* ----------------------------------------------------------------- */
    LoggingMiddleware::class    => fn() => new LoggingMiddleware(
    // you can inject LoggerInterface here if your middleware takes it
    ),

    PasswordHasher::class => fn() => new PasswordHasher(),
    JwtService::class      => fn($c) => new JwtService(
        $c->get(MlcConfig::class)->get('auth.jwt_secret'),
        (int)$c->get(MlcConfig::class)->get('auth.jwt_ttl', 3600)
    ),
    AuthService::class     => fn($c) => new AuthService(
        $c->get(UserRepository::class),
        $c->get(PasswordHasher::class),
        $c->get(JwtService::class)
    ),
    JwtAuthMiddleware::class => fn($c) => new JwtAuthMiddleware(
        $c->get(JwtService::class),
        $c->get(ResponseFactoryInterface::class)
    ),

    // Authorization service & middleware
    //    AuthorizationService::class => fn() => tap(new AuthorizationService(), function($svc) {
    //        $svc->registerPolicy(App\Entity\Post::class, App\Policy\PostPolicy::class);
    //        // register more policies here...
    //    }),

    AuthorizationMiddleware::class => fn($c) => new AuthorizationMiddleware(
        $c->get(AuthorizationService::class)
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

    /* -----------------------------------------------------------------
     | PSR-15 pipeline — driven *solely* by config/middleware.mlc
     * ---------------------------------------------------------------- */
    MiddlewareDispatcher::class => static function ($c) {
        $ids = $c->get(MlcConfig::class)->get('middleware.global', []);

        // If the list is empty, fail fast so the bug is obvious
        if ($ids === []) {
            throw new RuntimeException(
                'No middleware configured. Did you forget config/middleware.mlc?'
            );
        }

        $stack = array_map([$c, 'get'], $ids);

        return new MiddlewareDispatcher(
            $stack,
            $c->get(CoreRequestHandler::class)
        );
    },

    /* ----------------------------------------------------------------- */
    /* SAPI emitter                                                       */
    /* ----------------------------------------------------------------- */
    SapiEmitter::class          => fn() => new SapiEmitter(),

    /* ----------------------------------------------------------------- */
    /* CLI commands + kernel                                            */
    /* ----------------------------------------------------------------- */
    CliKernel::class => fn($c) => new CliKernel(
        $c,
        CommandFinder::all()
    ),
];