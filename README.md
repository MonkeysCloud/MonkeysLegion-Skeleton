# MonkeysLegion Skeleton

[![PHP Version](https://img.shields.io/badge/php-8.4%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Packagist](https://img.shields.io/packagist/v/monkeyscloud/monkeyslegion-skeleton.svg)](https://packagist.org/packages/monkeyscloud/monkeyslegion-skeleton)

**A production-ready starter for building web apps & APIs with the MonkeysLegion framework.**

---

## ✨ Features Overview

| Category                 | Features                                                 |
| ------------------------ | -------------------------------------------------------- |
| **HTTP Stack**           | PSR-7/15 compliant, middleware pipeline, SAPI emitter    |
| **Routing**              | Attribute-based v2, auto-discovery, constraints, caching |
| **Dependency Injection** | PSR-11 container with config-first definitions           |
| **Database**             | Native PDO MySQL 8.4, Query Builder, Micro-ORM           |
| **Authentication**       | JWT, RBAC, 2FA, OAuth, API keys                          |
| **API Documentation**    | Live OpenAPI 3.1 & Swagger UI                            |
| **Validation**           | DTO binding with attribute constraints                   |
| **Rate Limiting**        | Sliding-window (IP + User buckets)                       |
| **Templating**           | MLView with components, slots, caching                   |
| **CLI**                  | Migrations, cache, key-gen, scaffolding, Tinker REPL     |
| **Files**                | Multi-driver storage, image processing, chunked uploads  |
| **I18n**                 | Full internationalization & localization support         |
| **Telemetry**            | Prometheus metrics, distributed tracing, PSR-3 logging   |
| **Mail**                 | SMTP, Markdown templates, DKIM support                   |
| **Caching**              | Multiple drivers (File, Redis, Memcached)                |

---

## 🚀 Quick-start

```bash
composer create-project monkeyscloud/monkeyslegion-skeleton my-app
cd my-app

cp .env.example .env       # configure DB, secrets
composer install
php ml key:generate

composer serve             # or php vendor/bin/dev-server
open http://127.0.0.1:8000 # your first MonkeysLegion page
```

---

## 📁 Project Layout

```text
my-app/
├─ app/
│  ├─ Controller/     # HTTP controllers (auto-scanned)
│  ├─ Dto/            # Request DTOs with validation attributes
│  ├─ Entity/         # DB entities
│  └─ Auth/           # Authentication contracts & traits
├─ config/
│  ├─ app.php         # DI definitions (services & middleware)
│  ├─ database.php    # DSN + creds
│  └─ *.mlc           # key-value config (CORS, cache, auth,…)
├─ public/            # Web root (index.php, assets)
├─ resources/
│  └─ views/          # MLView templates & components
├─ var/
│  ├─ cache/          # compiled templates, rate-limit buckets
│  └─ migrations/     # auto-generated SQL
├─ database/
│  └─ seeders/        # generated seeder stubs
├─ storage/           # file uploads, logs
├─ tests/             # PHPUnit integration/unit tests
│  └─ IntegrationTestCase.php
├─ vendor/            # Composer deps
├─ bin/               # Dev helpers (ml, dev-server)
├─ phpunit.xml        # PHPUnit config
└─ README.md
```

---

## 📦 Package Ecosystem (Detailed)

MonkeysLegion is built as a modular ecosystem of packages. Below is comprehensive documentation for each package with examples.

---

### 🔧 Core Framework

#### `monkeyslegion` (Meta-package)

Installs the complete MonkeysLegion stack with a single command.

```bash
composer require monkeyscloud/monkeyslegion
```

---

#### `monkeyslegion-core`

Core runtime providing kernel, events, and helpers.

---

#### `monkeyslegion-di`

Tiny PSR-11 compliant dependency injection container.

---

#### `monkeyslegion-mlc`

Production-ready `.mlc` configuration file parser with environment variable support.

**Features:**

- 🔒 **Secure** - Path traversal prevention, file permission checks
- ⚡ **Fast** - File-based caching with automatic invalidation
- 🎯 **Type-Safe** - Strong typing with getters (`getString()`, `getInt()`, etc.)

**MLC File Format:**

```mlc
# Comments start with #
app_name = "My Application"
port = 8080
debug = true

# Sections with nesting
database {
    host = ${DB_HOST:localhost}    # Env vars with defaults
    port = 3306
    credentials {
        username = root
        password = ${DB_PASSWORD}
    }
}

# Arrays
allowed_origins = ["https://example.com", "https://app.example.com"]
```

**Usage:**

```php
use MonkeysLegion\Mlc\Loader;
use MonkeysLegion\Mlc\Parser;

$loader = new Loader(new Parser(), '/path/to/config');

// Load and merge multiple files
$config = $loader->load(['app', 'database', 'cache']);

// Type-safe getters
$port = $config->getInt('database.port', 3306);
$debug = $config->getBool('app.debug', false);
$hosts = $config->getArray('database.hosts', []);

// Required values (throws if missing)
$secret = $config->getRequired('app.secret');
```

---

### 🌐 HTTP & Routing

#### `monkeyslegion-http`

PSR-7 HTTP message implementations and SAPI emitter.

---

#### `monkeyslegion-router`

Comprehensive HTTP router with attribute-based routing, middleware, named routes, and caching.

**Features:**

- ✅ **Attribute-Based Routing** - PHP 8 attributes on controller methods
- ✅ **Middleware Support** - Route, controller, and global middleware
- ✅ **Named Routes** - URL generation from route names
- ✅ **Route Constraints** - Built-in and custom parameter validation
- ✅ **Route Groups** - Organize routes with shared prefixes and middleware
- ✅ **Optional Parameters** - Support for optional route segments
- ✅ **Route Caching** - Production-ready caching
- ✅ **CORS Support** - Built-in CORS middleware

**Basic Routes:**

```php
use MonkeysLegion\Router\Router;
use MonkeysLegion\Router\RouteCollection;

$router = new Router(new RouteCollection());

// Simple routes
$router->get('/users', fn($request) => new Response(...));
$router->post('/users', fn($request) => new Response(...));
$router->put('/users/{id}', fn($request, $id) => new Response(...));
$router->patch('/users/{id}', fn($request, $id) => new Response(...));
$router->delete('/users/{id}', fn($request, $id) => new Response(...));

// Multiple methods
$router->match(['GET', 'POST'], '/submit', $handler);

// Any HTTP method
$router->any('/webhook', $handler);
```

**Route with Parameters:**

```php
// Required parameter
$router->get('/users/{id}', function ($request, string $id) {
    return new Response("User ID: {$id}");
});

// Parameter with regex constraint
$router->get('/users/{id:\d+}', $handler);  // Only digits

// Multiple parameters
$router->get('/posts/{year}/{month}/{slug}', function ($request, $year, $month, $slug) {
    // Access all parameters
});

// Optional parameters
$router->get('/posts/{page?}', function ($request, ?string $page = '1') {
    // $page defaults to '1' if not provided
});

// Multiple optional parameters
$router->get('/archive/{year}/{month?}/{day?}', $handler);
```

**Built-in Constraints:**

```php
$router->get('/users/{id:int}', $handler);        // Integer only
$router->get('/users/{id:\d+}', $handler);        // Alternative regex

$router->get('/posts/{slug:slug}', $handler);     // Slug format (a-z0-9-)
$router->get('/posts/{slug:[a-z0-9-]+}', $handler); // Alternative regex

$router->get('/items/{uuid:uuid}', $handler);     // UUID format
$router->get('/verify/{email:email}', $handler);  // Email format
$router->get('/price/{amount:numeric}', $handler); // Numeric values
$router->get('/category/{name:alpha}', $handler); // Alphabetic only
$router->get('/code/{code:alphanum}', $handler);  // Alphanumeric
```

**Attribute-Based Controllers:**

```php
use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Router\Attributes\RoutePrefix;
use MonkeysLegion\Router\Attributes\Middleware;

#[RoutePrefix('/api/users')]
#[Middleware(['cors', 'throttle'])]
class UserController
{
    #[Route('GET', '/', name: 'users.index', summary: 'List all users', tags: ['Users'])]
    public function index(): Response
    {
        // List users
    }

    #[Route('GET', '/{id:\d+}', name: 'users.show', summary: 'Get user by ID')]
    public function show(string $id): Response
    {
        // Show single user
    }

    #[Route('POST', '/', name: 'users.create')]
    #[Middleware('auth')]
    public function create(): Response
    {
        // Create user (requires auth)
    }

    #[Route(['PUT', 'PATCH'], '/{id:\d+}', name: 'users.update')]
    #[Middleware(['auth', 'can:update,user'])]
    public function update(string $id): Response
    {
        // Update user
    }

    #[Route('DELETE', '/{id:\d+}', name: 'users.destroy')]
    #[Middleware(['auth', 'admin'])]
    public function destroy(string $id): Response
    {
        // Delete user
    }
}

// Register the controller
$router->registerController(new UserController());
```

**Middleware:**

```php
use MonkeysLegion\Router\Middleware\MiddlewareInterface;

// Create custom middleware
class AuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        if (!$this->isAuthenticated($request)) {
            return new Response(Stream::createFromString('Unauthorized'), 401);
        }
        return $next($request);
    }
}

// Register middleware by name
$router->registerMiddleware('auth', AuthMiddleware::class);
$router->registerMiddleware('cors', new CorsMiddleware());
$router->registerMiddleware('throttle', new ThrottleMiddleware(60, 1));

// Middleware groups
$router->registerMiddlewareGroup('api', ['cors', 'throttle', 'json']);
$router->registerMiddlewareGroup('web', ['cors', 'csrf', 'session']);

// Global middleware (applied to all routes)
$router->addGlobalMiddleware('cors');
$router->addGlobalMiddleware('logging');

// Route-specific middleware
$router->add('GET', '/admin', $handler, 'admin.dashboard', ['auth', 'admin']);
```

**Route Groups:**

```php
// Group with prefix and middleware
$router->group(function (Router $router) {
    $router->get('/users', $usersHandler);      // /api/v1/users
    $router->get('/posts', $postsHandler);      // /api/v1/posts
    $router->get('/comments', $commentsHandler); // /api/v1/comments
})
->prefix('/api/v1')
->middleware(['cors', 'throttle', 'auth'])
->group(fn() => null);

// Nested groups
$router->group(function (Router $router) {
    // Admin routes: /admin/*
    $router->get('/dashboard', $dashboardHandler);

    $router->group(function (Router $router) {
        // User management: /admin/users/*
        $router->get('/', $listHandler);
        $router->get('/{id}', $showHandler);
        $router->post('/', $createHandler);
    })
    ->prefix('/users')
    ->middleware(['can:manage-users'])
    ->group(fn() => null);
})
->prefix('/admin')
->middleware(['auth', 'admin'])
->group(fn() => null);
```

**URL Generation:**

```php
// Define named routes
$router->get('/users', $handler, 'users.index');
$router->get('/users/{id}', $handler, 'users.show');
$router->get('/posts/{year}/{slug}', $handler, 'posts.show');

// Get URL generator
$urlGen = $router->getUrlGenerator();
$urlGen->setBaseUrl('https://example.com');

// Generate URLs
echo $router->url('users.index');
// Output: /users

echo $router->url('users.show', ['id' => 123]);
// Output: /users/123

echo $router->url('users.show', ['id' => 123], absolute: true);
// Output: https://example.com/users/123

// Extra parameters become query string
echo $router->url('posts.show', ['year' => '2024', 'slug' => 'hello', 'preview' => 1]);
// Output: /posts/2024/hello?preview=1
```

**CORS Middleware:**

```php
use MonkeysLegion\Router\Middleware\CorsMiddleware;

$cors = new CorsMiddleware([
    'allowed_origins' => ['https://example.com', 'https://app.example.com'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'],
    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
    'exposed_headers' => ['X-Total-Count', 'X-Page-Count'],
    'max_age' => 86400,
    'credentials' => true,
]);

$router->registerMiddleware('cors', $cors);
```

**Throttle Middleware:**

```php
use MonkeysLegion\Router\Middleware\ThrottleMiddleware;

// 60 requests per minute
$throttle = new ThrottleMiddleware(maxRequests: 60, perMinutes: 1);
$router->registerMiddleware('throttle', $throttle);

// Different limits for different routes
$router->registerMiddleware('throttle:strict', new ThrottleMiddleware(10, 1));
$router->registerMiddleware('throttle:relaxed', new ThrottleMiddleware(200, 1));
```

**Route Caching (Production):**

```php
use MonkeysLegion\Router\RouteCache;

$cache = new RouteCache(__DIR__ . '/var/cache');
$collection = new RouteCollection();

// Load from cache if available
if ($cache->has()) {
    $data = $cache->load();
    $collection->import($data);
} else {
    // Register all routes
    $router = new Router($collection);
    // ... register routes ...

    // Save to cache
    $exported = $collection->export();
    $cache->save($exported['routes'], $exported['namedRoutes']);
}

// Clear cache when deploying
$cache->clear();

// Check cache stats
$stats = $cache->getStats();
```

**Custom Error Handlers:**

```php
// Custom 404 handler
$router->setNotFoundHandler(function (ServerRequestInterface $request) {
    return new Response(
        Stream::createFromString(json_encode([
            'error' => 'Not Found',
            'path' => $request->getUri()->getPath(),
        ])),
        404,
        ['Content-Type' => 'application/json']
    );
});

// Custom 405 handler
$router->setMethodNotAllowedHandler(
    function (ServerRequestInterface $request, array $allowedMethods) {
        return new Response(
            Stream::createFromString(json_encode([
                'error' => 'Method Not Allowed',
                'allowed' => $allowedMethods,
            ])),
            405,
            [
                'Content-Type' => 'application/json',
                'Allow' => implode(', ', $allowedMethods),
            ]
        );
    }
);
```

**Dispatching Requests:**

```php
// Get request from globals
$request = ServerRequestFactory::fromGlobals();

// Dispatch and get response
$response = $router->dispatch($request);

// Send response to client
header('HTTP/1.1 ' . $response->getStatusCode());
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header("{$name}: {$value}", false);
    }
}
echo $response->getBody();
```

**Route Metadata for OpenAPI:**

```php
#[Route(
    'GET',
    '/users',
    name: 'users.index',
    summary: 'List all users',
    description: 'Returns a paginated list of users with optional filters',
    tags: ['Users', 'API'],
    meta: ['version' => '1.0', 'deprecated' => false]
)]
public function index(): Response { }
```

---

#### `monkeyslegion-validation`

Attribute-driven DTO binding and validation layer.

**Define a DTO:**

```php
namespace App\Dto;

use MonkeysLegion\Validation\Attributes as Assert;

final readonly class CreateUserRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,

        #[Assert\NotBlank]
        #[Assert\Length(min: 8, max: 64)]
        public string $password,

        #[Assert\Range(min: 0.01, max: 9999.99)]
        public float $price,

        #[Assert\Url]
        public string $website,

        #[Assert\UuidV4]
        public string $categoryId,
    ) {}
}
```

**Available Constraints:**

- `#[NotBlank]` - Value cannot be empty
- `#[Email]` - Valid email format
- `#[Length(min, max)]` - String length range
- `#[Range(min, max)]` - Numeric range
- `#[Pattern(regex)]` - Regex pattern match
- `#[Url]` - Valid URL format
- `#[UuidV4]` - Valid UUIDv4 format

**Validation Response (400):**

```json
{
  "errors": [
    { "field": "email", "message": "Value must be a valid e-mail." },
    { "field": "password", "message": "Length constraint violated." }
  ]
}
```

---

### 💾 Database & ORM

#### `monkeyslegion-database`

Native PDO-powered MySQL 8.4 connection and query helpers.

---

#### `monkeyslegion-query`

Powerful, fluent Query Builder & Micro-ORM.

**Features:**

- 🔗 **Fluent API** - Chainable, expressive query building
- 🛡️ **SQL Injection Protection** - Automatic parameter binding
- 🔄 **Transactions** - Full ACID compliance with savepoints
- 📊 **Advanced Queries** - Joins, subqueries, unions, CTEs

**Basic Queries:**

```php
use MonkeysLegion\Query\QueryBuilder;

$qb = new QueryBuilder($connection);

// Simple query
$users = $qb->from('users')
    ->where('status', '=', 'active')
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->fetchAll();

// With joins
$posts = $qb->from('posts', 'p')
    ->leftJoin('users', 'u', 'u.id', '=', 'p.user_id')
    ->select(['p.*', 'u.name as author'])
    ->where('p.published', '=', true)
    ->fetchAll();
```

**WHERE Clauses:**

```php
// Basic conditions
$qb->where('status', '=', 'active')
   ->where('age', '>', 18)
   ->orWhere('role', '=', 'admin');

// IN / BETWEEN
$qb->whereIn('id', [1, 2, 3, 4, 5])
   ->whereBetween('age', 18, 65)
   ->whereNull('deleted_at');

// Grouped conditions
$qb->where('status', '=', 'active')
   ->whereGroup(function($q) {
       $q->where('role', '=', 'admin')
         ->orWhere('role', '=', 'moderator');
   });
// Produces: WHERE status = 'active' AND (role = 'admin' OR role = 'moderator')
```

**Insert / Update / Delete:**

```php
// Insert
$userId = $qb->insert('users', [
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);

// Batch insert
$qb->insertBatch('users', [
    ['name' => 'Alice', 'email' => 'alice@example.com'],
    ['name' => 'Bob', 'email' => 'bob@example.com'],
]);

// Update
$qb->update('users', ['status' => 'inactive'])
    ->where('last_login', '<', date('Y-m-d', strtotime('-1 year')))
    ->execute();

// Delete
$qb->delete('users')
    ->where('status', '=', 'deleted')
    ->execute();
```

**Aggregates & Pagination:**

```php
$total = $qb->from('users')->count();
$revenue = $qb->from('orders')->sum('amount');
$avgPrice = $qb->from('products')->avg('price');

// Pagination
$result = $qb->from('posts')
    ->where('published', '=', true)
    ->paginate(page: 2, perPage: 15);
// Returns: ['data' => [...], 'total' => 150, 'page' => 2, 'lastPage' => 10, ...]
```

**Transactions:**

```php
$result = $qb->transaction(function($qb) {
    $userId = $qb->insert('users', ['name' => 'Alice']);
    $qb->insert('profiles', ['user_id' => $userId]);
    return $userId;
});
```

---

#### `monkeyslegion-entity`

Attribute-based data-mapper, entity scanner, and repository layer.

```php
use MonkeysLegion\Entity\Attributes\Entity;
use MonkeysLegion\Entity\Attributes\Field;

#[Entity(table: 'users')]
class User
{
    #[Field(type: 'integer')]
    public int $id;

    #[Field(type: 'string', length: 255)]
    public string $email;

    #[Field(type: 'datetime')]
    public \DateTimeImmutable $created_at;
}
```

---

#### `monkeyslegion-migration`

Entity-schema diff engine and SQL migration runner.

```bash
php ml make:migration   # Generate migration from entity differences
php ml migrate          # Run pending migrations
php ml rollback         # Revert last migration
php ml schema:update    # Compare entities → database and apply missing tables/columns (use --dump or --force)
```

---

### 🔐 Authentication & Security

#### `monkeyslegion-auth`

Comprehensive authentication and authorization package.

**Features:**

- 🔐 **JWT Authentication** - Stateless auth with access/refresh token pairs
- 👥 **RBAC** - Role-based access control with permission inheritance
- 🔑 **Two-Factor (2FA)** - TOTP compatible with Google Authenticator
- 🌐 **OAuth** - Google, GitHub providers (easily extensible)
- 🗝️ **API Keys** - Scoped keys for M2M authentication
- ⏱️ **Rate Limiting** - Brute force protection

**Basic Setup:**

```php
use MonkeysLegion\Auth\Service\AuthService;
use MonkeysLegion\Auth\Service\JwtService;
use MonkeysLegion\Auth\Service\PasswordHasher;

$jwt = new JwtService(
    secret: $_ENV['JWT_SECRET'],
    accessTtl: 1800,    // 30 minutes
    refreshTtl: 604800, // 7 days
);

$auth = new AuthService(
    users: $userProvider,
    hasher: new PasswordHasher(),
    jwt: $jwt,
);
```

**Login Flow:**

```php
try {
    $result = $auth->login($email, $password, $request->ip());

    if ($result->requires2FA) {
        // Show 2FA form
        return response()->json([
            'requires_2fa' => true,
            'challenge' => $result->challengeToken,
        ]);
    }

    return response()->json([
        'access_token' => $result->tokens->accessToken,
        'refresh_token' => $result->tokens->refreshToken,
        'expires_at' => $result->tokens->accessExpiresAt,
    ]);
} catch (InvalidCredentialsException $e) {
    return response()->json(['error' => 'Invalid credentials'], 401);
}
```

**User Entity Setup:**

```php
use MonkeysLegion\Auth\Contract\AuthenticatableInterface;
use MonkeysLegion\Auth\Contract\HasRolesInterface;
use MonkeysLegion\Auth\Trait\AuthenticatableTrait;
use MonkeysLegion\Auth\Trait\HasRolesTrait;

class User implements AuthenticatableInterface, HasRolesInterface
{
    use AuthenticatableTrait;
    use HasRolesTrait;

    public function getAuthIdentifier(): int|string
    {
        return $this->id;
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_secret !== null;
    }
}
```

**PHP Attributes for Authorization:**

```php
use MonkeysLegion\Auth\Attribute\Authenticated;
use MonkeysLegion\Auth\Attribute\RequiresRole;
use MonkeysLegion\Auth\Attribute\RequiresPermission;
use MonkeysLegion\Auth\Attribute\Can;

#[Authenticated]
class PostController
{
    #[RequiresPermission('posts.create')]
    public function create(): Response { }

    #[Can('update', Post::class)]
    public function update(Post $post): Response { }

    #[RequiresRole('admin', 'moderator')]
    public function delete(Post $post): Response { }
}
```

**RBAC Configuration:**

```php
use MonkeysLegion\Auth\RBAC\RoleRegistry;

$roles = new RoleRegistry();
$roles->registerFromConfig([
    'super-admin' => [
        'permissions' => ['*'],
    ],
    'admin' => [
        'permissions' => ['users.*', 'posts.*'],
    ],
    'editor' => [
        'permissions' => ['posts.*'],
        'inherits' => ['viewer'],
    ],
    'viewer' => [
        'permissions' => ['posts.view'],
    ],
]);
```

**2FA Setup:**

```php
use MonkeysLegion\Auth\TwoFactor\TotpProvider;
use MonkeysLegion\Auth\Service\TwoFactorService;

$twoFactor = new TwoFactorService(new TotpProvider(), issuer: 'YourApp');

// Generate setup data (QR code)
$setup = $twoFactor->generateSetup($user->email);
// Returns: secret, qr_code (base64), uri, recovery_codes

// Verify and enable
$twoFactor->enable($setup['secret'], $code, $user->id);
```

---

### 📁 Caching & Storage

#### `monkeyslegion-cache`

PSR-16 compliant cache with multiple drivers.

**Drivers:** File, Redis, Memcached, Array (in-memory)

**Configuration:**

```php
return [
    'default' => 'redis',
    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => __DIR__ . '/../storage/cache',
        ],
        'redis' => [
            'driver' => 'redis',
            'host' => '127.0.0.1',
            'port' => 6379,
        ],
    ],
];
```

**Usage:**

```php
use MonkeysLegion\Cache\Cache;

// Basic operations
Cache::set('key', 'value', 3600);
$value = Cache::get('key', 'default');
Cache::delete('key');

// Remember pattern
$users = Cache::remember('users', 3600, function() {
    return User::all();
});

// Cache tagging
Cache::tags(['users', 'premium'])->set('user:1', $user, 3600);
Cache::tags(['users'])->clear(); // Clear all tagged

// Incrementing
Cache::increment('counter');
Cache::decrement('counter', 5);
```

**CLI Commands:**

```bash
php ml cache:clear              # Clear default store
php ml cache:clear --store=redis # Clear specific store
php ml cache:get user:123       # Get value
php ml cache:set key value      # Set value
```

---

#### `monkeyslegion-files`

Production-ready file storage and upload management.

**Features:**

- 🚀 **Chunked Uploads** - Resume-capable multipart uploads
- ☁️ **Multi-Storage** - Local, S3, MinIO, DigitalOcean, GCS, Firebase
- 🖼️ **Image Processing** - Thumbnails, optimization, watermarks
- 🔒 **Security** - Signed URLs, rate limiting, virus scanning
- 📊 **Database Tracking** - File metadata with soft deletes

**Basic Usage:**

```php
use MonkeysLegion\Files\FilesManager;

// Store a file
$path = $files->put($_FILES['upload']['tmp_name']);
$path = $files->putString("Hello World", 'text/plain');

// Read / Check
$contents = $files->get($path);
if ($files->exists($path)) {
    $size = $files->size($path);
    $mime = $files->mimeType($path);
}

// Signed URLs
$url = ml_files_sign_url('/files/' . $path, ttl: 600);
```

**Image Processing:**

```php
use MonkeysLegion\Files\Image\ImageProcessor;

$processor = new ImageProcessor(driver: 'gd', quality: 85);

$thumbPath = $processor->thumbnail($path, 300, 300, 'cover');
$optimized = $processor->optimize($path, quality: 80);
$webp = $processor->convert($path, 'webp');
$watermarked = $processor->watermark($path, $watermarkPath, 'bottom-right');

// Batch conversions
$conversions = $processor->processConversions($path, [
    'thumb' => ['width' => 150, 'height' => 150, 'fit' => 'cover'],
    'medium' => ['width' => 800, 'height' => 600],
    'webp' => ['format' => 'webp', 'quality' => 80],
]);
```

**Chunked Uploads:**

```php
use MonkeysLegion\Files\Upload\ChunkedUploadManager;

// Initialize upload session
$uploadId = $chunked->initiate('large-video.mp4', $totalSize, 'video/mp4');

// Upload chunks
foreach ($chunks as $index => $chunk) {
    $chunked->uploadChunk($uploadId, $index, $chunk['data'], $chunk['size']);
}

// Complete
$finalPath = $chunked->complete($uploadId);

// Check progress
$progress = $chunked->getProgress($uploadId);
// ['uploaded_chunks' => 5, 'total_chunks' => 10, 'percent' => 50]
```

**S3 Storage:**

```php
use MonkeysLegion\Files\Storage\S3Storage;

$s3 = new S3Storage(
    bucket: 'my-bucket',
    region: 'us-east-1',
    accessKey: $key,
    secretKey: $secret,
);

// Pre-signed URLs
$uploadUrl = $s3->getUploadUrl('uploads/file.jpg', 'image/jpeg', 3600);
$downloadUrl = $s3->getTemporaryUrl('private/doc.pdf', 3600);
```

---

### 🎨 Templating & Views

#### `monkeyslegion-template`

**MLView** template engine with components, slots, and caching.

**Syntax:**

```php
// resources/views/welcome.ml.php

{{-- Escaped output --}}
<h1>{{ $title }}</h1>

{{-- Raw HTML --}}
{!! $html !!}

{{-- Control structures --}}
@if ($user->isAdmin())
    <span class="badge">Admin</span>
@endif

@foreach ($items as $item)
    <li>{{ $item->name }}</li>
@endforeach

{{-- Components --}}
<x-alert type="success">
    Operation completed!
</x-alert>

{{-- Layout inheritance --}}
@extends('layouts.app')

@section('content')
    <p>Page content here</p>
@endsection

{{-- Slots --}}
<x-card>
    @slot('header')
        Card Title
    @endslot
    Card body content
</x-card>
```

---

### 🛠 CLI & Development

#### `monkeyslegion-cli`

Command-line interface and developer tooling.

```bash
# General Commands
php ml key:generate             # Generate APP_KEY
php ml cache:clear              # Clear view cache
php ml route:list               # Display routes
php ml tinker                   # Interactive REPL

# Database
php ml db:create                # Create database
php ml make:migration           # Generate migration
php ml migrate                  # Run migrations
php ml rollback                 # Undo migrations
php ml db:seed                  # Run seeders

# Scaffolding
php ml make:entity User         # Generate Entity
php ml make:controller User     # Generate Controller
php ml make:middleware Auth     # Generate Middleware
php ml make:policy User         # Generate Policy

# API
php ml openapi:export           # Export OpenAPI spec
```

---

#### `monkeyslegion-dev-server`

Hot-reload development server.

```bash
composer serve                  # Start dev server
composer server:start:public    # Start on 0.0.0.0:8000
composer server:stop            # Stop server
composer server:restart         # Restart server
```

---

### 📧 Communication & Events

#### `monkeyslegion-mail`

Feature-rich mail package with DKIM, queues, and templates.

**Features:**

- 📧 **Multiple Transports** - SMTP, Sendmail, Mailgun, Null
- 🛡️ **DKIM Signing** - Email authentication
- 📊 **Queue System** - Background processing with Redis
- 🎨 **Mailable Classes** - Object-oriented email composition

**Direct Sending:**

```php
use MonkeysLegion\Mail\Mailer;

$mailer->send(
    'user@example.com',
    'Welcome to Our App',
    '<h1>Welcome!</h1><p>Thanks for joining us.</p>',
    'text/html'
);
```

**Mailable Classes:**

```php
// Generate: php ml make:mail OrderConfirmation

use MonkeysLegion\Mail\Mail\Mailable;

class OrderConfirmationMail extends Mailable
{
    public function __construct(
        private array $order,
        private array $customer
    ) {
        parent::__construct();
    }

    public function build(): self
    {
        return $this->view('emails.order-confirmation')
                    ->subject('Order Confirmation #' . $this->order['id'])
                    ->withData([
                        'order' => $this->order,
                        'customer' => $this->customer,
                    ])
                    ->attach('/path/to/invoice.pdf');
    }
}

// Send
$mail = new OrderConfirmationMail($order, $customer);
$mail->setTo('john@example.com')->send();
// Or queue
$mail->setTo('john@example.com')->queue();
```

**CLI Commands:**

```bash
php ml mail:test user@example.com  # Test sending
php ml make:mail WelcomeMail       # Generate Mailable
php ml make:dkim-pkey storage/keys # Generate DKIM keys
php ml mail:work                   # Process queue
php ml mail:list                   # List pending jobs
```

---

#### `monkeyslegion-events`

PSR-14 event-bus for core lifecycle events.

---

### 🌍 Internationalization

#### `monkeyslegion-i18n`

Production-ready I18n & localization.

**Features:**

- 🌍 **Multiple Sources** - JSON, PHP, Database loaders
- 📝 **ICU Pluralization** - Plural rules for 200+ languages
- 🎯 **Auto Detection** - URL, Session, Headers, Cookies
- 📦 **Namespacing** - Package-level translations

**Translation Files:**

```json
// resources/lang/en/messages.json
{
  "welcome": "Welcome!",
  "greeting": "Hello, :name!",
  "items": "{0} No items|{1} One item|[2,*] :count items"
}
```

**Usage:**

```php
use MonkeysLegion\I18n\TranslatorFactory;

$translator = TranslatorFactory::create([
    'locale' => 'es',
    'fallback' => 'en',
    'path' => __DIR__ . '/resources/lang'
]);

echo $translator->trans('messages.welcome');
// Output: ¡Bienvenido!

echo $translator->trans('messages.greeting', ['name' => 'John']);
// Output: ¡Hola, John!

// Pluralization
echo $translator->choice('messages.items', 5);
// Output: 5 artículos
```

**Helper Functions:**

```php
__('messages.welcome');
__('messages.greeting', ['name' => 'John']);
trans_choice('cart.items', $count);
lang();       // Get current locale
lang('es');   // Set locale
```

---

### 📊 Observability & Logging

#### `monkeyslegion-logger`

PSR-3 compliant advanced logger.

---

#### `monkeyslegion-telemetry`

Comprehensive telemetry with metrics, logging, and distributed tracing.

**Features:**

- 📈 **Prometheus Metrics** - Counter, Gauge, Histogram, Summary
- 📊 **StatsD Support** - Alternative metrics backend
- 🔗 **Distributed Tracing** - W3C Trace Context propagation
- 🎯 **Head Sampling** - Configurable trace sampling

**Initialization:**

```php
use MonkeysLegion\Telemetry\Telemetry;

Telemetry::init([
    'metrics' => [
        'driver' => 'prometheus',
        'namespace' => 'myapp',
    ],
    'tracing' => [
        'enabled' => true,
        'service_name' => 'myapp',
        'sample_rate' => 1.0,
    ],
    'logging' => [
        'stream' => 'php://stderr',
        'json' => true,
    ],
]);
```

**Metrics:**

```php
// Counter
Telemetry::counter('http_requests_total', 1, ['method' => 'GET', 'status' => '200']);

// Gauge
Telemetry::gauge('active_connections', 42);

// Histogram
Telemetry::histogram('http_request_duration_seconds', 0.123, ['endpoint' => '/api/users']);

// Timer
$stopTimer = Telemetry::timer('operation_duration_seconds');
$this->heavyOperation();
$duration = $stopTimer(['operation' => 'heavy_task']);
```

**Distributed Tracing:**

```php
use MonkeysLegion\Telemetry\Tracing\SpanKind;

// Simple tracing
$result = Telemetry::trace('fetch-user', function () use ($userId) {
    return $this->userRepository->find($userId);
}, SpanKind::CLIENT, ['user.id' => $userId]);

// Nested traces (automatic parent-child)
$result = Telemetry::trace('process-order', function () use ($order) {
    $inventory = Telemetry::trace('check-inventory', function () use ($order) {
        return $this->inventory->check($order->items);
    }, SpanKind::CLIENT);

    $payment = Telemetry::trace('process-payment', function () use ($order) {
        return $this->payment->charge($order);
    }, SpanKind::CLIENT);

    return ['inventory' => $inventory, 'payment' => $payment];
});

// Get trace ID for correlation
$traceId = Telemetry::traceId();
```

**Logging with Trace Correlation:**

```php
$logger = Telemetry::log();

$logger->info('User logged in', ['user_id' => 123]);
// JSON output includes trace_id and span_id automatically
```

---

## 🔨 Configuration & DI

All services are wired in **`config/app.php`**. Customize:

- Database DSN & credentials (`config/database.php`)
- CORS, cache, auth (`.mlc` files)
- Middleware order, validation, rate-limit thresholds
- CLI commands registered in `CliKernel`

---

## ✅ Testing & Build

### Test Harness

A base PHPUnit class **`tests/IntegrationTestCase.php`** provides:

- **DI bootstrapping** from `config/app.php`
- **PSR-15 pipeline** via `MiddlewareDispatcher`
- `createRequest($method, $uri, $headers, $body)` to craft HTTP requests
- `dispatch($request)` to get a `ResponseInterface`
- **Assertions**:
  - `assertStatus(Response, int)`
  - `assertJsonResponse(Response, array)`

**Example:**

```php
namespace Tests\Controller;

use Tests\IntegrationTestCase;

final class HomeControllerTest extends IntegrationTestCase
{
    public function testIndexReturnsHtml(): void
    {
        $request  = $this->createRequest('GET', '/');
        $response = $this->dispatch($request);

        $this->assertStatus($response, 200);
        $this->assertStringContainsString('<h1>', (string)$response->getBody());
    }
}
```

### Running Tests

```bash
composer test              # Run PHPUnit tests
./vendor/bin/phpunit       # Direct PHPUnit execution
```

---

## 📋 Requirements

- **PHP 8.4+** - Required for all packages
- **MySQL 8.4** - Recommended database
- **Composer 2.x** - Dependency management

### Recommended PHP Extensions

| Extension         | Purpose                                 |
| ----------------- | --------------------------------------- |
| `pdo_mysql`       | Database connectivity                   |
| `redis`           | Caching, rate limiting, session storage |
| `mbstring`        | Multi-byte string handling              |
| `json`            | JSON processing                         |
| `gd` or `imagick` | Image processing                        |
| `intl`            | Advanced I18n formatting                |
| `posix`           | CLI process management                  |
| `pcntl`           | Signal handling                         |

---

## 🤝 Contributing

1. Fork 🍴
2. Create a feature branch 🌱
3. Submit a PR 🚀

Happy hacking with **MonkeysLegion**! 🎉

---

## 📜 License

MIT License - see [LICENSE](LICENSE) for details.

---

## Contributors

<table>
  <tr>
    <td>
      <a href="https://github.com/yorchperaza">
        <img src="https://github.com/yorchperaza.png" width="100px;" alt="Jorge Peraza"/><br />
        <sub><b>Jorge Peraza</b></sub>
      </a>
    </td>
    <td>
      <a href="https://github.com/Amanar-Marouane">
        <img src="https://github.com/Amanar-Marouane.png" width="100px;" alt="Amanar Marouane"/><br />
        <sub><b>Amanar Marouane</b></sub>
      </a>
    </td>
  </tr>
</table>
