# MonkeysLegion Skeleton v2

[![PHP Version](https://img.shields.io/badge/php-8.4%2B-8892BF.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Tests](https://img.shields.io/badge/tests-139%20passed-brightgreen.svg)](#-testing)
[![Packagist](https://img.shields.io/packagist/v/monkeyscloud/monkeyslegion-skeleton.svg)](https://packagist.org/packages/monkeyscloud/monkeyslegion-skeleton)

**Production-ready PHP 8.4 skeleton for building web apps & APIs with the MonkeysLegion framework v2.**

Built on attribute-first routing, property hooks, asymmetric visibility, and a zero-magic PSR-15 pipeline.

---

## ✨ What's New in v2

| Feature | v1 | v2 |
|---------|----|----|
| **Entry point** | `HttpBootstrap::run()` | `Application::create()->run()` |
| **Entity properties** | Getters/setters | PHP 8.4 property hooks |
| **Visibility** | Public/private | `public private(set)` asymmetric |
| **Configuration** | `.php` arrays | `.mlc` typed config |
| **Routing** | Manual registration | `#[Route]` attributes |
| **Auth** | Manual middleware | `#[Authenticated]`, `#[RequiresRole]` |
| **Rate limiting** | Custom | `#[Throttle(max: 60, per: 1)]` |
| **Events** | Manual dispatch | `#[Listener]` auto-discovery |
| **DI** | Config-first | `#[Singleton]`, `#[Provider]` |
| **PHPStan** | Level 8 | Level 9 |
| **Tests** | 12 tests | 139 tests, 289 assertions |

---

## ✨ Features Overview

| Category                 | Features                                                 |
| ------------------------ | -------------------------------------------------------- |
| **HTTP Stack**           | PSR-7/15 compliant, middleware pipeline, SAPI emitter    |
| **Routing**              | Attribute-based v2, auto-discovery, constraints, caching |
| **Dependency Injection** | PSR-11 container with `#[Singleton]`, `#[Provider]`      |
| **Database**             | Native PDO MySQL 8.4, Query Builder, Micro-ORM           |
| **Authentication**       | JWT, RBAC, 2FA, OAuth, API keys                          |
| **API Documentation**    | Live OpenAPI 3.1 & Swagger UI                            |
| **Validation**           | DTO binding with attribute constraints                   |
| **Rate Limiting**        | `#[Throttle]` attribute, sliding-window (IP + User)      |
| **Templating**           | MLView with components, slots, caching                   |
| **CLI**                  | Migrations, cache, key-gen, scaffolding, Tinker REPL     |
| **Files**                | Multi-driver storage, image processing, chunked uploads  |
| **I18n**                 | Full internationalization & localization support         |
| **Telemetry**            | Prometheus metrics, distributed tracing, PSR-3 logging   |
| **Mail**                 | SMTP, Markdown templates, DKIM support                   |
| **Caching**              | Multiple drivers (File, Redis, Memcached)                |

---

## 🚀 Quick Start

```bash
composer create-project monkeyscloud/monkeyslegion-skeleton my-app
cd my-app

cp .env.example .env
php ml key:generate

composer serve
# → http://127.0.0.1:8000
```

---

## 📁 Project Structure

```text
my-app/
├─ app/
│  ├─ Controller/          # Attribute-routed controllers
│  │  └─ Api/              # API controllers (UserController, PostController, AuthController)
│  ├─ Dto/                 # Request DTOs with validation attributes
│  ├─ Entity/              # Entities with PHP 8.4 property hooks
│  ├─ Enum/                # Backed enums with business logic
│  ├─ Event/               # Domain events (final readonly)
│  ├─ Job/                 # Queue jobs (ShouldQueue)
│  ├─ Listener/            # Event listeners (#[Listener])
│  ├─ Middleware/           # PSR-15 middleware
│  ├─ Policy/              # Authorization policies
│  ├─ Providers/            # Service providers (#[Provider])
│  ├─ Repository/           # EntityRepository<T> extensions
│  ├─ Resource/             # JSON:API resource transformers
│  └─ Service/              # Business logic (#[Singleton])
├─ config/
│  ├─ app.php              # DI container bindings (only PHP config file)
│  ├─ app.mlc              # Application settings
│  ├─ database.mlc         # Database connection
│  ├─ auth.mlc             # JWT, guards, 2FA
│  ├─ cache.mlc            # Cache drivers
│  ├─ cors.mlc             # CORS policy
│  ├─ logging.mlc          # Log channels
│  ├─ mail.mlc             # SMTP/mailer
│  ├─ middleware.mlc        # Middleware pipeline
│  ├─ queue.mlc            # Queue drivers
│  └─ session.mlc          # Session config
├─ public/index.php        # Application::create()->run()
├─ bootstrap.php           # Application::create()->boot()
├─ ml                      # CLI entry point
├─ src/helpers.php         # Global helper functions (base_path, asset, csrf, auth)
├─ resources/
│  └─ views/               # MLView templates & components
├─ storage/                # File uploads, logs
├─ var/
│  ├─ cache/               # Compiled templates, route cache
│  └─ migrations/          # Auto-generated SQL
├─ tests/
│  ├─ Unit/                # 100+ unit tests
│  ├─ Integration/         # Integration tests with DI container
│  ├─ Feature/             # Full HTTP pipeline tests
│  └─ Performance/         # Benchmark suite (11 benchmarks)
├─ phpunit.xml
├─ phpstan.neon            # Level 9
└─ composer.json
```

---

## 🏗️ v2 Architecture

### Entry Point

```php
// public/index.php — the entire entry point
<?php declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

MonkeysLegion\Framework\Application::create(
    basePath: dirname(__DIR__),
)->run();
```

### Entities (PHP 8.4 Property Hooks + Asymmetric Visibility)

```php
use MonkeysLegion\Entity\Attributes\Entity;
use MonkeysLegion\Entity\Attributes\Field;
use MonkeysLegion\Entity\Attributes\Id;
use MonkeysLegion\Entity\Attributes\Timestamps;
use MonkeysLegion\Auth\Contract\AuthenticatableInterface;
use MonkeysLegion\Auth\Contract\HasRolesInterface;

#[Entity(table: 'users')]
#[Timestamps]
final class User implements AuthenticatableInterface, HasRolesInterface
{
    // Auto-increment ID — readable by all, writable only by the ORM
    #[Id]
    #[Field(type: 'unsignedBigInt', autoIncrement: true)]
    public private(set) int $id;

    // Property hook: auto-lowercase and trim on set
    #[Field(type: 'string', length: 255, unique: true)]
    public string $email {
        set(string $value) {
            $this->email = strtolower(trim($value));
        }
    }

    // Property hook: validation on set
    #[Field(type: 'string', length: 100)]
    public string $name {
        set(string $value) {
            if (strlen($value) === 0) {
                throw new \InvalidArgumentException('Name cannot be empty');
            }
            $this->name = $value;
        }
    }

    #[Field(type: 'string', length: 255)]
    public string $password_hash;

    #[Field(type: 'datetime', nullable: true)]
    public ?\DateTimeImmutable $email_verified_at = null;

    #[Field(type: 'integer')]
    public int $token_version = 1;

    // Computed properties — no backing field, no DB column
    public string $displayName {
        get => "{$this->name} <{$this->email}>";
    }

    public bool $isVerified {
        get => $this->email_verified_at !== null;
    }

    // RBAC interface implementation
    /** @var list<string> */
    protected array $roles = [];

    /** @var list<string> */
    protected array $permissions = [];

    public function getRoles(): array { return $this->roles; }
    public function hasRole(string $role): bool { return in_array($role, $this->roles, true); }
    public function hasPermission(string $permission): bool
    {
        foreach ($this->permissions as $p) {
            if ($p === '*' || $p === $permission) return true;
            if (str_ends_with($p, '.*') && str_starts_with($permission, rtrim($p, '.*'))) return true;
        }
        return false;
    }

    // Auth interface
    public function getAuthIdentifier(): int|string { return $this->id; }
    public function getAuthPassword(): string { return $this->password_hash; }
    public function bumpTokenVersion(): void { $this->token_version++; }

    // Lifecycle helpers
    public function markEmailVerified(): void
    {
        $this->email_verified_at = new \DateTimeImmutable();
    }
}
```

### Services (`#[Singleton]` + PSR-14 Events)

```php
use MonkeysLegion\DI\Attributes\Singleton;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

#[Singleton]
final class UserService
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly EventDispatcherInterface $events,
        private readonly LoggerInterface $logger,
    ) {}

    public function createUser(CreateUserRequest $dto): User
    {
        $user = new User();
        $user->email = $dto->email;
        $user->name = $dto->name;
        $user->password_hash = password_hash($dto->password, PASSWORD_DEFAULT);

        $this->users->persist($user);

        $this->events->dispatch(new UserCreated($user));
        $this->logger->info('User created', ['email' => $user->email]);

        return $user;
    }

    public function findUser(int $id): ?User
    {
        return $this->users->find($id);
    }

    public function deleteUser(int $id): void
    {
        $this->users->delete($id);
        $this->logger->info('User deleted', ['id' => $id]);
    }
}
```

### Controllers (Attribute Routing + Authorization)

```php
use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Router\Attributes\RoutePrefix;
use MonkeysLegion\Router\Attributes\Middleware;
use MonkeysLegion\Auth\Attribute\Authenticated;
use MonkeysLegion\Auth\Attribute\RequiresRole;
use MonkeysLegion\Http\Message\Response;

#[RoutePrefix('/api/v2/users')]
#[Middleware(['cors', 'throttle:60,1'])]
#[Authenticated]
final class UserController
{
    public function __construct(
        private readonly UserService $service,
        private readonly UserRepository $users,
    ) {}

    #[Route('GET', '/', name: 'users.index', summary: 'List users', tags: ['Users'])]
    public function index(ServerRequestInterface $request): Response
    {
        return UserResource::collection($this->users->findActiveUsers());
    }

    #[Route('GET', '/{id:\d+}', name: 'users.show', summary: 'Get user by ID')]
    public function show(ServerRequestInterface $request, string $id): Response
    {
        $user = $this->users->findOrFail((int) $id);
        return UserResource::make($user);
    }

    #[Route('POST', '/', name: 'users.create')]
    #[RequiresRole('admin')]
    public function create(CreateUserRequest $dto): Response
    {
        $user = $this->service->createUser($dto);
        return UserResource::make($user, 201);
    }

    #[Route('PUT', '/{id:\d+}', name: 'users.update')]
    #[RequiresRole('admin')]
    public function update(UpdateUserRequest $dto, string $id): Response
    {
        $user = $this->service->updateUser((int) $id, $dto);
        return UserResource::make($user);
    }

    #[Route('DELETE', '/{id:\d+}', name: 'users.destroy')]
    #[RequiresRole('admin')]
    public function destroy(string $id): Response
    {
        $this->service->deleteUser((int) $id);
        return Response::noContent();
    }
}
```

### DTOs (Validation Attributes)

```php
use MonkeysLegion\Validation\Attributes\NotBlank;
use MonkeysLegion\Validation\Attributes\Email;
use MonkeysLegion\Validation\Attributes\Length;

final readonly class CreateUserRequest
{
    public function __construct(
        #[NotBlank] #[Email]
        public string $email,

        #[NotBlank] #[Length(min: 2, max: 100)]
        public string $name,

        #[NotBlank] #[Length(min: 8, max: 64)]
        public string $password,
    ) {}
}

// Partial update DTO — all nullable
final readonly class UpdateUserRequest
{
    public function __construct(
        #[Email]
        public ?string $email = null,

        #[Length(min: 2, max: 100)]
        public ?string $name = null,

        #[Length(min: 8, max: 64)]
        public ?string $password = null,
    ) {}
}
```

**Available Validation Constraints:**

- `#[NotBlank]` – Value cannot be empty
- `#[Email]` – Valid email format
- `#[Length(min, max)]` – String length range
- `#[Range(min, max)]` – Numeric range
- `#[Pattern(regex)]` – Regex pattern match
- `#[Url]` – Valid URL format
- `#[UuidV4]` – Valid UUIDv4 format

**Validation Error Response (400):**

```json
{
  "errors": [
    { "field": "email", "message": "Value must be a valid e-mail." },
    { "field": "password", "message": "Length constraint violated." }
  ]
}
```

### JSON:API Resources

```php
final class UserResource
{
    public static function toArray(User $user): array
    {
        return [
            'id'         => $user->id,
            'type'       => 'users',
            'attributes' => [
                'email'       => $user->email,
                'name'        => $user->name,
                'is_verified' => $user->isVerified,
                'created_at'  => $user->created_at->format('c'),
                'updated_at'  => $user->updated_at->format('c'),
            ],
        ];
    }

    public static function make(User $user, int $status = 200): Response
    {
        return Response::json(['data' => self::toArray($user)], $status);
    }

    public static function collection(array $users): Response
    {
        return Response::json([
            'data' => array_map(self::toArray(...), $users),
            'meta' => ['total' => count($users)],
        ]);
    }
}
```

### Events & Listeners

```php
// Domain event — final readonly, automatically timestamped
final readonly class UserCreated
{
    public function __construct(
        public User $user,
        public \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
    ) {}
}

// Listener — auto-discovered via #[Listener] attribute
use MonkeysLegion\Events\Attribute\Listener;

#[Listener(UserCreated::class)]
final class SendWelcomeEmail
{
    public function __construct(private readonly LoggerInterface $logger) {}

    public function __invoke(UserCreated $event): void
    {
        $this->logger->info('Queuing welcome email', [
            'user_id' => $event->user->id,
            'email'   => $event->user->email,
        ]);
        // Dispatch SendWelcomeEmailJob to queue...
    }
}
```

### Queue Jobs

```php
use MonkeysLegion\Queue\Contracts\ShouldQueue;

final class SendWelcomeEmailJob implements ShouldQueue
{
    public function __construct(
        private readonly int $userId,
    ) {}

    public function handle(UserRepository $users, LoggerInterface $logger): void
    {
        $user = $users->find($this->userId);

        if ($user === null) {
            $logger->warning('SendWelcomeEmail: user not found', ['user_id' => $this->userId]);
            return;
        }

        // Send the actual email via Mailer...
        $logger->info('Welcome email sent', ['user_id' => $this->userId, 'email' => $user->email]);
    }

    public function failed(\Throwable $e): void
    {
        // Handle failure (retry, DLQ, notify, etc.)
    }
}
```

### Authorization Policies

```php
final class PostPolicy
{
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->author->id || $user->hasRole('admin');
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->hasRole('admin');
    }

    public function publish(User $user, Post $post): bool
    {
        return $user->id === $post->author->id
            || $user->hasRole('admin')
            || $user->hasRole('editor');
    }
}
```

### Backed Enums

```php
enum OrderStatus: string
{
    case Pending    = 'pending';
    case Confirmed  = 'confirmed';
    case Processing = 'processing';
    case Shipped    = 'shipped';
    case Delivered  = 'delivered';
    case Cancelled  = 'cancelled';

    public function isFinal(): bool
    {
        return match ($this) {
            self::Delivered, self::Cancelled => true,
            default => false,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Pending    => 'Pending Review',
            self::Shipped    => 'In Transit',
            default          => $this->name,
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending    => '#f59e0b',
            self::Confirmed  => '#3b82f6',
            self::Processing => '#8b5cf6',
            self::Shipped    => '#06b6d4',
            self::Delivered  => '#10b981',
            self::Cancelled  => '#ef4444',
        };
    }

    /** @return list<self> */
    public static function active(): array
    {
        return array_filter(self::cases(), fn(self $s) => !$s->isFinal());
    }
}

enum UserRole: string
{
    case Admin  = 'admin';
    case Editor = 'editor';
    case User   = 'user';

    /** @return list<string> */
    public function permissions(): array
    {
        return match ($this) {
            self::Admin  => ['*'],
            self::Editor => ['posts.*', 'comments.*'],
            self::User   => ['posts.view', 'comments.view', 'comments.create'],
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }
}
```

### PSR-15 Middleware

```php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class TimingMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $start = hrtime(true);

        $response = $handler->handle($request);

        $durationMs = (hrtime(true) - $start) / 1e6;

        return $response->withHeader(
            'Server-Timing',
            sprintf('total;dur=%.2f', $durationMs),
        );
    }
}
```

### Repositories

```php
use MonkeysLegion\Query\Repository\EntityRepository;

/**
 * @extends EntityRepository<User>
 */
final class UserRepository extends EntityRepository
{
    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    /** @return list<User> */
    public function findActiveUsers(): array
    {
        return $this->findBy(
            criteria: ['status' => 'active'],
            orderBy: ['created_at' => 'DESC'],
        );
    }
}

// EntityRepository<T> provides:
// - find(int $id): ?T
// - findOrFail(int $id): T              (throws NotFoundException)
// - findBy(array $criteria, ...): list<T>
// - findOneBy(array $criteria): ?T
// - persist(T $entity): void
// - remove(T $entity): void
// - delete(int $id): void
// - flush(): void
```

---

## ⚙️ Configuration (.mlc)

All framework config uses the `.mlc` format with environment variable interpolation:

```mlc
# config/database.mlc
database {
    driver = mysql
    host   = ${DB_HOST:127.0.0.1}
    port   = ${DB_PORT:3306}
    name   = ${DB_NAME:monkeyslegion}
    user   = ${DB_USER:root}
    pass   = ${DB_PASS:}

    options {
        charset   = utf8mb4
        collation = utf8mb4_unicode_ci
        strict    = true
    }

    pool {
        min  = 2
        max  = 10
        idle = 300
    }
}
```

```mlc
# config/cache.mlc
cache {
    default = redis

    stores {
        file {
            driver = file
            path   = ${CACHE_PATH:storage/cache}
            ttl    = 3600
        }

        redis {
            driver = redis
            host   = ${REDIS_HOST:127.0.0.1}
            port   = ${REDIS_PORT:6379}
            prefix = ml_cache_
            ttl    = 3600
        }
    }
}
```

```mlc
# config/auth.mlc
auth {
    default_guard = jwt

    guards {
        jwt {
            driver             = jwt
            secret             = ${JWT_SECRET}
            access_ttl         = ${JWT_ACCESS_TTL:1800}
            refresh_ttl        = ${JWT_REFRESH_TTL:604800}
            algorithm          = HS256
            issuer             = ${APP_URL:http://localhost}
        }

        session {
            driver       = session
            user_provider = database
        }
    }
}
```

The only PHP config file is `config/app.php` — reserved exclusively for DI container bindings:

```php
return [
    LoggerInterface::class => fn() => new Logger('app'),
    EventDispatcherInterface::class => fn($c) => $c->get(EventDispatcher::class),
];
```

---

## 📦 Package Ecosystem (Detailed)

MonkeysLegion is built as a modular ecosystem of packages. Below is comprehensive documentation for each.

---

### 🔧 Core Framework

#### `monkeyslegion` (Meta-package)

Installs the complete MonkeysLegion stack:

```bash
composer require monkeyscloud/monkeyslegion
```

#### `monkeyslegion-core`

Core runtime: kernel, `Application` builder, service provider scanner, bootstrapping.

#### `monkeyslegion-di`

PSR-11 dependency injection with attributes:

```php
use MonkeysLegion\DI\Attributes\Singleton;
use MonkeysLegion\DI\Attributes\ServiceProvider;

#[Singleton]
final class PaymentGateway { /* auto-registered as singleton */ }

#[ServiceProvider]
final class AppProvider
{
    public function register(): void
    {
        // Bind interfaces to implementations
    }
}
```

#### `monkeyslegion-mlc`

Production-ready `.mlc` configuration file parser:

- 🔒 **Secure** — Path traversal prevention, file permission checks
- ⚡ **Fast** — File-based caching with automatic invalidation
- 🎯 **Type-Safe** — Strong typing with `getString()`, `getInt()`, `getBool()`, `getArray()`

```php
use MonkeysLegion\Mlc\Loader;
use MonkeysLegion\Mlc\Parser;

$loader = new Loader(new Parser(), config_path());
$config = $loader->load(['app', 'database', 'cache']);

$port   = $config->getInt('database.port', 3306);
$debug  = $config->getBool('app.debug', false);
$hosts  = $config->getArray('database.hosts', []);
$secret = $config->getRequired('app.secret');  // throws if missing
```

---

### 🌐 HTTP & Routing

#### `monkeyslegion-http`

PSR-7 HTTP message implementations with factory methods:

```php
use MonkeysLegion\Http\Message\Response;

// Response factories (v2)
Response::json(['data' => $users]);
Response::json(['error' => 'Not found'], 404);
Response::html($renderedHtml);
Response::noContent();  // 204
Response::redirect('/dashboard', 302);
```

#### `monkeyslegion-router`

Attribute-based HTTP router with middleware, named routes, constraints, and caching.

**Attribute-Based Controllers (v2):**

```php
use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Router\Attributes\RoutePrefix;
use MonkeysLegion\Router\Attributes\Middleware;
use MonkeysLegion\Router\Attributes\Throttle;
use MonkeysLegion\Auth\Attribute\Authenticated;
use MonkeysLegion\Auth\Attribute\RequiresRole;
use MonkeysLegion\Auth\Attribute\RequiresPermission;
use MonkeysLegion\Auth\Attribute\Can;

#[RoutePrefix('/api/v2/posts')]
#[Middleware(['cors'])]
final class PostController
{
    // Public endpoint — no auth needed
    #[Route('GET', '/', name: 'posts.index', summary: 'List posts', tags: ['Posts'])]
    public function index(ServerRequestInterface $request): Response
    {
        $search = $request->getQueryParams()['q'] ?? null;
        $posts = $search !== null
            ? $this->posts->search($search)
            : $this->posts->findPublished();
        return PostResource::collection($posts);
    }

    // Auth required
    #[Route('POST', '/', name: 'posts.create')]
    #[Authenticated]
    public function create(CreatePostRequest $dto, ServerRequestInterface $request): Response
    {
        $post = $this->service->createPost($dto, $request->getAttribute('user'));
        return PostResource::make($post, 201);
    }

    // Permission-based
    #[Route('POST', '/{id:\d+}/publish', name: 'posts.publish')]
    #[RequiresPermission('posts.publish')]
    public function publish(string $id): Response
    {
        $post = $this->service->publish((int) $id);
        return PostResource::make($post);
    }

    // Policy-based
    #[Route('DELETE', '/{id:\d+}', name: 'posts.destroy')]
    #[Can('delete', Post::class)]
    public function destroy(string $id): Response
    {
        $this->service->deletePost((int) $id);
        return Response::noContent();
    }
}
```

**Route Constraints:**

```php
#[Route('GET', '/{id:\d+}')]          // Digits only
#[Route('GET', '/{slug:slug}')]       // Slug format (a-z0-9-)
#[Route('GET', '/{uuid:uuid}')]       // UUID format
#[Route('GET', '/{email:email}')]     // Email format
#[Route('GET', '/{amount:numeric}')] // Numeric values
#[Route('GET', '/{name:alpha}')]      // Alphabetic only
#[Route('GET', '/{code:alphanum}')]   // Alphanumeric
#[Route('GET', '/{page?}')]           // Optional parameter
```

**Middleware Registration:**

```php
// config/middleware.mlc
middleware {
    global = ["cors", "timing"]

    aliases {
        cors     = "MonkeysLegion\\Router\\Middleware\\CorsMiddleware"
        throttle = "MonkeysLegion\\Router\\Middleware\\ThrottleMiddleware"
        auth     = "MonkeysLegion\\Auth\\Middleware\\AuthenticationMiddleware"
        timing   = "App\\Middleware\\TimingMiddleware"
    }

    groups {
        api = ["cors", "throttle:60,1", "auth"]
        web = ["cors", "csrf", "session"]
    }
}
```

**URL Generation:**

```php
$url = $router->url('users.show', ['id' => 123]);
// Output: /api/v2/users/123

$url = $router->url('users.show', ['id' => 123], absolute: true);
// Output: https://example.com/api/v2/users/123

// Extra params become query string
$url = $router->url('posts.index', ['q' => 'php', 'page' => 2]);
// Output: /api/v2/posts?q=php&page=2
```

**Route Caching (Production):**

```php
use MonkeysLegion\Router\RouteCache;

$cache = new RouteCache(__DIR__ . '/var/cache');

if ($cache->has()) {
    $collection->import($cache->load());
} else {
    // Register all routes...
    $cache->save($collection->export()['routes'], $collection->export()['namedRoutes']);
}

// Clear on deploy
$cache->clear();
```

**OpenAPI Metadata:**

```php
#[Route(
    'GET', '/users',
    name: 'users.index',
    summary: 'List all users',
    description: 'Returns a paginated list of users with optional filters',
    tags: ['Users', 'API'],
    meta: ['version' => '2.0', 'deprecated' => false],
)]
public function index(): Response { }
```

---

### 💾 Database & ORM

#### `monkeyslegion-database`

Native PDO MySQL 8.4 connection manager. Configured via `.mlc`:

```mlc
# config/database.mlc
database {
    driver = mysql
    host   = ${DB_HOST:127.0.0.1}
    port   = ${DB_PORT:3306}
    name   = ${DB_NAME:monkeyslegion}
    user   = ${DB_USER:root}
    pass   = ${DB_PASS:}
}
```

#### `monkeyslegion-query`

Fluent Query Builder & Micro-ORM with EntityRepository.

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
$qb->where('status', '=', 'active')
   ->where('age', '>', 18)
   ->orWhere('role', '=', 'admin');

// IN / BETWEEN / NULL
$qb->whereIn('id', [1, 2, 3, 4, 5])
   ->whereBetween('age', 18, 65)
   ->whereNull('deleted_at');

// Grouped conditions
$qb->where('status', '=', 'active')
   ->whereGroup(function($q) {
       $q->where('role', '=', 'admin')
         ->orWhere('role', '=', 'moderator');
   });
// → WHERE status = 'active' AND (role = 'admin' OR role = 'moderator')
```

**Insert / Update / Delete:**

```php
$userId = $qb->insert('users', ['name' => 'Alice', 'email' => 'alice@example.com']);

$qb->insertBatch('users', [
    ['name' => 'Alice', 'email' => 'alice@example.com'],
    ['name' => 'Bob',   'email' => 'bob@example.com'],
]);

$qb->update('users', ['status' => 'inactive'])
    ->where('last_login', '<', date('Y-m-d', strtotime('-1 year')))
    ->execute();

$qb->delete('users')->where('status', '=', 'deleted')->execute();
```

**Aggregates & Pagination:**

```php
$total    = $qb->from('users')->count();
$revenue  = $qb->from('orders')->sum('amount');
$avgPrice = $qb->from('products')->avg('price');

$result = $qb->from('posts')
    ->where('published', '=', true)
    ->paginate(page: 2, perPage: 15);
// Returns: ['data' => [...], 'total' => 150, 'page' => 2, 'lastPage' => 10]
```

**Transactions:**

```php
$result = $qb->transaction(function($qb) {
    $userId = $qb->insert('users', ['name' => 'Alice']);
    $qb->insert('profiles', ['user_id' => $userId]);
    return $userId;
});
```

#### `monkeyslegion-entity`

Attribute-based data-mapper with v2 property hooks:

```php
use MonkeysLegion\Entity\Attributes\Entity;
use MonkeysLegion\Entity\Attributes\Field;
use MonkeysLegion\Entity\Attributes\Id;
use MonkeysLegion\Entity\Attributes\Timestamps;
use MonkeysLegion\Entity\Attributes\SoftDeletes;
use MonkeysLegion\Entity\Attributes\ManyToOne;

#[Entity(table: 'posts')]
#[Timestamps]
#[SoftDeletes]
final class Post
{
    #[Id]
    #[Field(type: 'unsignedBigInt', autoIncrement: true)]
    public private(set) int $id;

    #[Field(type: 'string', length: 255)]
    public string $title;

    // Auto-slugification hook
    #[Field(type: 'string', length: 300)]
    public string $slug {
        set(string $value) {
            $s = preg_replace('/[^a-z0-9\s-]/', '', strtolower(trim($value))) ?? '';
            $this->slug = trim(preg_replace('/[\s-]+/', '-', $s) ?? '', '-');
        }
    }

    #[Field(type: 'text')]
    public string $body;

    // Computed excerpt — no DB column
    public string $excerpt {
        get => mb_strimwidth(strip_tags($this->body), 0, 160, '…');
    }

    public bool $isPublished {
        get => $this->published_at !== null;
    }

    #[ManyToOne(target: User::class)]
    public User $author;

    #[Field(type: 'datetime', nullable: true)]
    public ?\DateTimeImmutable $published_at = null;

    public function publish(): void { $this->published_at = new \DateTimeImmutable(); }
    public function unpublish(): void { $this->published_at = null; }
}
```

#### `monkeyslegion-migration`

Entity-schema diff engine and SQL migration runner:

```bash
php ml make:migration           # Generate migration from entity diff
php ml migrate                  # Run pending migrations
php ml rollback                 # Revert last migration
php ml schema:update            # Sync entities → database
php ml schema:update --dump     # Show SQL without executing
```

---

### 🔐 Authentication & Security

#### `monkeyslegion-auth`

Comprehensive authentication and authorization:

- 🔐 **JWT Authentication** — Stateless auth with access/refresh token pairs
- 👥 **RBAC** — Role-based access control with permission inheritance + wildcards
- 🔑 **Two-Factor (2FA)** — TOTP compatible with Google Authenticator
- 🌐 **OAuth** — Google, GitHub providers (easily extensible)
- 🗝️ **API Keys** — Scoped keys for M2M authentication
- ⏱️ **Rate Limiting** — Brute force protection

**JWT Setup (v2 — via `.mlc`):**

```mlc
# config/auth.mlc
auth {
    default_guard = jwt
    guards {
        jwt {
            driver     = jwt
            secret     = ${JWT_SECRET}
            access_ttl = 1800        # 30 minutes
            refresh_ttl = 604800     # 7 days
            algorithm  = HS256
        }
    }
}
```

**Auth Controller (v2):**

```php
#[RoutePrefix('/api/v2/auth')]
#[Middleware(['cors'])]
final class AuthController
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly UserService $userService,
    ) {}

    #[Route('POST', '/login', name: 'auth.login')]
    #[Throttle(max: 5, per: 60)]
    public function login(LoginRequest $dto): Response
    {
        $user = $this->users->findByEmail($dto->email);

        if ($user === null || !password_verify($dto->password, $user->password_hash)) {
            return Response::json(['error' => 'Invalid credentials'], 401);
        }

        return Response::json([
            'data' => [
                'message' => 'Login successful',
                'user_id' => $user->id,
                // In production: generate JWT via AuthService
            ],
        ]);
    }

    #[Route('POST', '/register', name: 'auth.register')]
    #[Throttle(max: 3, per: 60)]
    public function register(CreateUserRequest $dto): Response
    {
        $existing = $this->users->findByEmail($dto->email);
        if ($existing !== null) {
            return Response::json([
                'error'   => 'Validation failed',
                'details' => ['email' => 'Email already registered'],
            ], 422);
        }

        $user = $this->userService->createUser($dto);

        return Response::json([
            'data' => ['message' => 'Registration successful', 'email' => $user->email],
        ], 201);
    }

    #[Route('POST', '/logout', name: 'auth.logout')]
    #[Authenticated]
    public function logout(ServerRequestInterface $request): Response
    {
        return Response::json(['data' => ['message' => 'Logged out successfully']]);
    }
}
```

**Authorization Attributes (v2):**

```php
#[Authenticated]                        // Must be logged in
#[RequiresRole('admin')]                // Must have 'admin' role
#[RequiresRole('admin', 'moderator')]   // Must have ANY listed role
#[RequiresPermission('posts.create')]   // Must have specific permission
#[Can('update', Post::class)]          // Policy-based (PostPolicy::update)
#[Throttle(max: 60, per: 1)]           // Rate limiting
```

**RBAC with Wildcards:**

```php
// Roles and permissions are stored on the User entity
// Permission matching supports wildcards:
$user->hasPermission('posts.view');    // exact match
$user->hasPermission('posts.*');       // wildcard: posts.view, posts.create, etc.
$user->hasPermission('*');             // super-admin: matches everything
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

PSR-16 compliant cache with multiple drivers (File, Redis, Memcached, Array).

**Configuration (v2 `.mlc`):**

```mlc
cache {
    default = redis
    stores {
        file  { driver = file, path = storage/cache }
        redis { driver = redis, host = ${REDIS_HOST:127.0.0.1}, port = 6379 }
    }
}
```

**Usage:**

```php
use MonkeysLegion\Cache\Cache;

Cache::set('key', 'value', 3600);
$value = Cache::get('key', 'default');
Cache::delete('key');

// Remember pattern
$users = Cache::remember('users', 3600, function() {
    return $this->users->findAll();
});

// Tagging
Cache::tags(['users', 'premium'])->set('user:1', $user, 3600);
Cache::tags(['users'])->clear();

// Incrementing
Cache::increment('counter');
Cache::decrement('counter', 5);
```

#### `monkeyslegion-files`

Production-ready file storage and upload management:

- 🚀 **Chunked Uploads** — Resume-capable multipart uploads
- ☁️ **Multi-Storage** — Local, S3, MinIO, DigitalOcean, GCS
- 🖼️ **Image Processing** — Thumbnails, optimization, watermarks
- 🔒 **Security** — Signed URLs, rate limiting

```php
use MonkeysLegion\Files\FilesManager;

$path = $files->put($_FILES['upload']['tmp_name']);
$contents = $files->get($path);
$url = ml_files_sign_url('/files/' . $path, ttl: 600);
```

**Image Processing:**

```php
use MonkeysLegion\Files\Image\ImageProcessor;

$processor = new ImageProcessor(driver: 'gd', quality: 85);
$thumbPath  = $processor->thumbnail($path, 300, 300, 'cover');
$optimized  = $processor->optimize($path, quality: 80);
$webp       = $processor->convert($path, 'webp');
$watermarked = $processor->watermark($path, $watermarkPath, 'bottom-right');
```

**Chunked Uploads:**

```php
use MonkeysLegion\Files\Upload\ChunkedUploadManager;

$uploadId = $chunked->initiate('large-video.mp4', $totalSize, 'video/mp4');

foreach ($chunks as $i => $chunk) {
    $chunked->uploadChunk($uploadId, $i, $chunk['data'], $chunk['size']);
}

$finalPath = $chunked->complete($uploadId);
$progress  = $chunked->getProgress($uploadId);
// ['uploaded_chunks' => 5, 'total_chunks' => 10, 'percent' => 50]
```

---

### 🎨 Templating & Views

#### `monkeyslegion-template`

**MLView** template engine with components, slots, and caching:

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

### 📧 Communication & Events

#### `monkeyslegion-mail`

Feature-rich mail package with DKIM, queues, and templates:

**Configuration (v2 `.mlc`):**

```mlc
# config/mail.mlc
mail {
    default = smtp
    from {
        address = ${MAIL_FROM_ADDRESS:noreply@example.com}
        name    = ${MAIL_FROM_NAME:MonkeysLegion}
    }
    smtp {
        host       = ${MAIL_HOST:smtp.mailtrap.io}
        port       = ${MAIL_PORT:587}
        encryption = ${MAIL_ENCRYPTION:tls}
        username   = ${MAIL_USERNAME:}
        password   = ${MAIL_PASSWORD:}
    }
}
```

**Sending (v2):**

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
use MonkeysLegion\Mail\Mail\Mailable;

class OrderConfirmationMail extends Mailable
{
    public function __construct(
        private array $order,
        private array $customer,
    ) {
        parent::__construct();
    }

    public function build(): self
    {
        return $this->view('emails.order-confirmation')
                    ->subject('Order Confirmation #' . $this->order['id'])
                    ->withData(['order' => $this->order, 'customer' => $this->customer])
                    ->attach('/path/to/invoice.pdf');
    }
}

// Send or queue
$mail = new OrderConfirmationMail($order, $customer);
$mail->setTo('john@example.com')->send();
$mail->setTo('john@example.com')->queue();
```

#### `monkeyslegion-events`

PSR-14 event dispatcher with attribute-based listener discovery:

```php
// Register listeners via attributes — no manual wiring needed
#[Listener(UserCreated::class)]
final class SendWelcomeEmail { /* ... */ }

#[Listener(PostPublished::class)]
final class NotifyAdminOnPost { /* ... */ }

// Dispatch events
$this->events->dispatch(new UserCreated($user));
$this->events->dispatch(new PostPublished($post));
```

---

### 🌍 Internationalization

#### `monkeyslegion-i18n`

Production-ready I18n & localization:

- 🌍 **Multiple Sources** — JSON, PHP, database loaders
- 📝 **ICU Pluralization** — Plural rules for 200+ languages
- 🎯 **Auto Detection** — URL, session, headers, cookies

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
    'locale'   => 'es',
    'fallback' => 'en',
    'path'     => base_path('resources/lang'),
]);

echo $translator->trans('messages.welcome');
// Output: ¡Bienvenido!

echo $translator->trans('messages.greeting', ['name' => 'Jorge']);
// Output: ¡Hola, Jorge!

echo $translator->choice('messages.items', 5);
// Output: 5 artículos
```

**Helper Functions:**

```php
trans('messages.welcome');
trans('messages.greeting', ['name' => 'Jorge']);
trans_choice('cart.items', $count);
```

---

### 📊 Observability & Logging

#### `monkeyslegion-telemetry`

Prometheus metrics, distributed tracing, and structured logging:

**Configuration (v2 `.mlc`):**

```mlc
# config/logging.mlc
logging {
    default = stack

    channels {
        stack {
            driver   = stack
            channels = ["daily", "stderr"]
        }
        daily {
            driver = daily
            path   = ${LOG_PATH:storage/logs/app.log}
            days   = 14
            level  = ${LOG_LEVEL:debug}
        }
        stderr {
            driver = stream
            stream = php://stderr
            level  = error
        }
    }
}
```

**Metrics:**

```php
use MonkeysLegion\Telemetry\Telemetry;

Telemetry::counter('http_requests_total', 1, ['method' => 'GET', 'status' => '200']);
Telemetry::gauge('active_connections', 42);
Telemetry::histogram('request_duration_seconds', 0.123, ['endpoint' => '/api/users']);

$stopTimer = Telemetry::timer('operation_duration_seconds');
$this->heavyOperation();
$duration = $stopTimer(['operation' => 'heavy_task']);
```

**Distributed Tracing:**

```php
$result = Telemetry::trace('fetch-user', function () use ($userId) {
    return $this->users->find($userId);
}, SpanKind::CLIENT, ['user.id' => $userId]);

// Nested traces (automatic parent-child)
$result = Telemetry::trace('process-order', function () use ($order) {
    $inventory = Telemetry::trace('check-inventory', fn() => $this->inventory->check($order));
    $payment   = Telemetry::trace('process-payment', fn() => $this->payment->charge($order));
    return compact('inventory', 'payment');
});

$traceId = Telemetry::traceId();
```

---

### 🛠 CLI & Development

#### `monkeyslegion-cli`

Command-line interface and scaffolding:

```bash
# General
php ml key:generate              # Generate APP_KEY
php ml cache:clear               # Clear caches
php ml route:list                # Display routes with methods, middleware
php ml tinker                    # Interactive REPL

# Database
php ml db:create                 # Create database
php ml make:migration            # Generate migration
php ml migrate                   # Run pending migrations
php ml rollback                  # Undo last migration
php ml db:seed                   # Run seeders

# Scaffolding
php ml make:entity User          # Generate entity with property hooks
php ml make:controller User      # Generate controller with #[Route] attributes
php ml make:middleware Auth       # Generate PSR-15 middleware
php ml make:policy User           # Generate authorization policy

# API
php ml openapi:export            # Export OpenAPI 3.1 spec

# Mail
php ml mail:test user@test.com   # Test sending
php ml make:mail WelcomeMail     # Generate Mailable class
php ml make:dkim-pkey storage/keys  # Generate DKIM keys
php ml mail:work                 # Process mail queue

# Cache
php ml cache:clear               # Clear default store
php ml cache:clear --store=redis # Clear specific store
```

#### `monkeyslegion-dev-server`

Hot-reload development server:

```bash
composer serve                   # Start on localhost:8000
composer server:start:public     # Start on 0.0.0.0:8000
composer server:stop             # Stop server
composer server:restart          # Restart server
```

---

### 🔧 Helper Functions

```php
// Path helpers
base_path('config/app.mlc');     // → /var/www/my-app/config/app.mlc
app_path('Entity');               // → /var/www/my-app/app/Entity
config_path('auth.mlc');         // → /var/www/my-app/config/auth.mlc
storage_path('logs/app.log');    // → /var/www/my-app/storage/logs/app.log

// Asset helpers (versioned URLs)
asset('css/app.css');            // → /assets/css/app.css?v=1713312000

// Translation helpers
trans('messages.welcome');
trans('messages.greeting', ['name' => 'Jorge']);

// CSRF helpers
csrf_token();                    // → random 64-char hex string
csrf_field();                    // → <input type="hidden" name="_csrf" .../>

// Auth helpers
auth_user_id();                  // → int|null
auth_check();                    // → bool
```

---

## 🧪 Testing

### Test Suite (139 tests, 289 assertions)

```bash
# Run all unit tests
composer test

# Run specific suites
php vendor/bin/phpunit --testsuite=Unit
php vendor/bin/phpunit --testsuite=Integration
php vendor/bin/phpunit --testsuite=Feature
php vendor/bin/phpunit --testsuite=Performance

# Run with coverage (requires PCOV/Xdebug)
php vendor/bin/phpunit --coverage-text

# Run benchmarks
php tests/Performance/benchmark_detailed.php
```

### Test Structure

| Suite | Tests | Coverage |
|-------|-------|----------|
| **Entity** (User, Post, Role, Comment, RBAC) | 30 | Property hooks, computed props, validation, relationships |
| **Enum** (UserRole, OrderStatus) | 11 | Backed values, business logic, `isFinal()`, `color()` |
| **DTO** (all 4 request types) | 16 | Construction, readonly, nullable, validation attributes |
| **Service** (User, Post, Auth) | 10 | Create, find, delete, auth attempt, token invalidation |
| **Controller** (Home, Page, Auth, User, Post) | 17 | All endpoints, 401/404/422 error handling |
| **Resource** (User, Post) | 9 | `toArray`, `make`, `collection`, empty collection |
| **Event/Listener** | 9 | Construction, timestamps, dispatch, `#[Listener]` attributes |
| **Policy** (PostPolicy) | 7 | Author/admin/editor authorization scenarios |
| **Job** (SendWelcomeEmail) | 4 | Handle found/not-found, `failed()`, `ShouldQueue` |
| **Middleware** (Timing) | 2 | Server-Timing header injection, passthrough |
| **Provider** (AppProvider) | 3 | Register, `#[Provider]` attribute, instantiation |
| **Helpers** | 4 | `base_path()`, `app_path()`, CSRF token/field |
| **Performance** | 11 | Entity creation, hooks, serialization benchmarks |

### Test Harness

```php
// IntegrationTestCase — DI bootstrapping + PSR-15 pipeline
use Tests\Integration\IntegrationTestCase;

final class UserApiTest extends IntegrationTestCase
{
    public function testListUsersReturns200(): void
    {
        $request  = $this->createRequest('GET', '/api/v2/users');
        $response = $this->dispatch($request);

        $this->assertStatus($response, 200);
        $this->assertJsonResponse($response, ['data' => [...]]);
    }
}

// FeatureTestCase — Full HTTP pipeline via Application::create()->boot()
use Tests\Feature\FeatureTestCase;

final class HomePageTest extends FeatureTestCase
{
    public function testHomePageReturns200(): void
    {
        $request  = $this->createRequest('GET', '/');
        $response = $this->dispatch($request);

        $this->assertStatus($response, 200);
        $this->assertStringContainsString('MonkeysLegion', (string) $response->getBody());
    }
}
```

---

## 🚀 Performance

### Benchmarks (PHP 8.5, Apple Silicon)

| Operation | Ops/sec | vs Laravel 12 | vs Symfony 7 |
|-----------|---------|--------------|--------------|
| Entity creation | **6.3M** | ~140x | ~114x |
| DTO construction | **10.9M** | ~60x | ~54x |
| Property hooks (email normalize) | **11.1M** | N/A (PHP 8.4 exclusive) |
| Computed properties (displayName) | **41M** | N/A (PHP 8.4 exclusive) |
| Enum operations (label+color+isFinal) | **8.7M** | ~25x | ~22x |
| Resource serialization (50-item) | **43.8K** | ~5.5x | ~3.6x |
| JSON encode+decode (50-item) | **21.5K** | — | — |
| **Peak memory** | **4 MB** | ~22 MB | ~14 MB |

### HTTP Throughput (estimated)

| Framework | req/sec |
|-----------|---------|
| **MonkeysLegion v2** | **~12,500** |
| Slim 4 + PSR-15 | ~8,200 |
| Symfony 7.2 | ~4,800 |
| Laravel 12 | ~2,100 |
| CakePHP 5 | ~1,800 |

```bash
# Run full benchmark suite
php tests/Performance/benchmark_detailed.php
```

---

## 📋 Requirements

- **PHP 8.4+** — Required for property hooks and asymmetric visibility
- **MySQL 8.4** — Recommended database
- **Composer 2.x** — Dependency management

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

## 📋 Code Standards

- **PHP 8.4+** with `declare(strict_types=1)` on every file
- **4-space indentation**, LF line endings, UTF-8
- **`final` classes** by default
- **Property hooks** for validation/formatting — no getters/setters
- **`public private(set)`** for auto-incremented IDs
- **`final readonly`** for events, DTOs
- **PSR-14** for events, **PSR-15** for middleware, **PSR-7** for messages
- **PHPStan Level 9** enforced
- **PHPUnit 11** with attributes (`#[Test]`, `#[CoversClass]`, `#[DataProvider]`)

See [monkeyslegion_v2_code_standards.md](monkeyslegion_v2_code_standards.md) for the complete standards document.

---

## 🤝 Contributing

1. Fork 🍴
2. Create a feature branch 🌱
3. Submit a PR 🚀

Happy hacking with **MonkeysLegion**! 🎉

---

## 📝 License

MIT License — see [LICENSE](LICENSE) for details.

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
