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
├─ src/helpers.php         # Global helper functions
├─ tests/
│  ├─ Unit/                # 100+ unit tests
│  ├─ Integration/         # Integration tests with DI container
│  ├─ Feature/             # Full HTTP pipeline tests
│  └─ Performance/         # Benchmark suite
├─ phpunit.xml
├─ phpstan.neon            # Level 9
└─ composer.json
```

---

## 🏗️ Architecture

### Entry Point

```php
// public/index.php
<?php declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

MonkeysLegion\Framework\Application::create(
    basePath: dirname(__DIR__),
)->run();
```

### Entities (PHP 8.4 Property Hooks)

```php
#[Entity(table: 'users')]
#[Timestamps]
class User implements AuthenticatableInterface
{
    #[Id]
    #[Field(type: 'unsignedBigInt', autoIncrement: true)]
    public private(set) int $id;

    #[Field(type: 'string', length: 255)]
    public string $email {
        set(string $value) {
            $this->email = strtolower(trim($value));
        }
    }

    #[Field(type: 'string', length: 255)]
    public string $name {
        set(string $value) {
            if (strlen($value) === 0) {
                throw new \InvalidArgumentException('Name cannot be empty');
            }
            $this->name = $value;
        }
    }

    // Computed properties — no backing field needed
    public string $displayName {
        get => "{$this->name} <{$this->email}>";
    }

    public bool $isVerified {
        get => $this->email_verified_at !== null;
    }
}
```

### Controllers (Attribute Routing)

```php
#[RoutePrefix('/api/v2/users')]
#[Middleware(['cors', 'throttle:60,1'])]
#[Authenticated]
final class UserController
{
    public function __construct(
        private readonly UserService $service,
        private readonly UserRepository $users,
    ) {}

    #[Route('GET', '/', name: 'users.index')]
    public function index(ServerRequestInterface $request): Response
    {
        return UserResource::collection($this->users->findActiveUsers());
    }

    #[Route('POST', '/', name: 'users.create')]
    #[RequiresRole('admin')]
    public function create(CreateUserRequest $dto): Response
    {
        return UserResource::make($this->service->createUser($dto), 201);
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
// Event
final readonly class UserCreated
{
    public function __construct(
        public User $user,
        public \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
    ) {}
}

// Listener — auto-discovered via attribute
#[Listener(UserCreated::class)]
final class SendWelcomeEmail
{
    public function __construct(private readonly LoggerInterface $logger) {}

    public function __invoke(UserCreated $event): void
    {
        $this->logger->info('Queuing welcome email', [
            'user_id' => $event->user->id,
        ]);
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
}
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

The only PHP config file is `config/app.php` — reserved exclusively for DI container bindings:

```php
return [
    LoggerInterface::class => fn() => new Logger('app'),
];
```

---

## 🧪 Testing

### Test Suite (139 tests, 289 assertions)

```bash
# Run all unit tests
composer test

# Run specific suites
php vendor/bin/phpunit --testsuite=Unit
php vendor/bin/phpunit --testsuite=Performance
php vendor/bin/phpunit --testsuite=Feature

# Run with coverage (requires PCOV/Xdebug)
php vendor/bin/phpunit --coverage-text
```

### Test Structure

| Suite | Tests | Coverage |
|-------|-------|----------|
| **Entity** (User, Post, Role, Comment, RBAC) | 30 | Property hooks, computed props, validation |
| **Enum** (UserRole, OrderStatus) | 11 | Backed values, business logic methods |
| **DTO** (all 4 request types) | 16 | Construction, readonly, validation attrs |
| **Service** (User, Post, Auth) | 10 | Create, find, delete, auth attempt |
| **Controller** (Home, Page, Auth, User, Post) | 17 | All endpoints, error handling |
| **Resource** (User, Post) | 9 | toArray, make, collection |
| **Event/Listener** | 9 | Construction, dispatch, attributes |
| **Policy** (PostPolicy) | 7 | Author/admin/editor authorization |
| **Job** (SendWelcomeEmail) | 4 | Handle, failed, ShouldQueue |
| **Middleware** (Timing) | 2 | Header injection, passthrough |
| **Provider** (AppProvider) | 3 | Register, attributes |
| **Helpers** | 4 | Path helpers, CSRF |
| **Performance** | 11 | Entity creation, hooks, serialization |

---

## 🚀 Performance

### Benchmarks (PHP 8.5, Apple Silicon)

| Operation | Ops/sec | vs Laravel 12 | vs Symfony 7 |
|-----------|---------|--------------|--------------|
| Entity creation | **6.3M** | ~140x | ~114x |
| DTO construction | **10.9M** | ~60x | ~54x |
| Property hooks | **11.1M** | N/A | N/A |
| Computed properties | **41M** | N/A | N/A |
| Enum operations | **8.7M** | ~25x | ~22x |
| Resource serialization | **43.8K** | ~5.5x | ~3.6x |
| **Peak memory** | **4 MB** | ~22 MB | ~14 MB |

### HTTP Throughput (estimated)

| Framework | req/sec |
|-----------|---------|
| **MonkeysLegion v2** | **~12,500** |
| Slim 4 | ~8,200 |
| Symfony 7.2 | ~4,800 |
| Laravel 12 | ~2,100 |

```bash
# Run benchmarks
php tests/Performance/benchmark_detailed.php
```

---

## 📦 Package Ecosystem

| Package | Purpose |
|---------|---------|
| `monkeyslegion` | Meta-package (complete stack) |
| `monkeyslegion-core` | Kernel, config repository, helpers |
| `monkeyslegion-di` | PSR-11 dependency injection |
| `monkeyslegion-http` | PSR-7 messages, Response factories |
| `monkeyslegion-router` | Attribute routing, middleware, OpenAPI |
| `monkeyslegion-entity` | Entity attributes, data mapper |
| `monkeyslegion-query` | Query builder, EntityRepository, UoW |
| `monkeyslegion-database` | PDO connection manager |
| `monkeyslegion-migration` | Schema diff & SQL runner |
| `monkeyslegion-auth` | JWT, RBAC, 2FA, OAuth, API keys |
| `monkeyslegion-validation` | Attribute-based DTO validation |
| `monkeyslegion-events` | PSR-14 event dispatcher |
| `monkeyslegion-template` | MLView template engine |
| `monkeyslegion-mlc` | `.mlc` config parser |
| `monkeyslegion-env` | `.env` file loader |
| `monkeyslegion-cache` | PSR-16 cache (File, Redis, Memcached) |
| `monkeyslegion-session` | Session management |
| `monkeyslegion-mail` | SMTP mailer with templates |
| `monkeyslegion-queue` | Job queue (Redis, database) |
| `monkeyslegion-schedule` | Task scheduler |
| `monkeyslegion-i18n` | Internationalization |
| `monkeyslegion-files` | Multi-driver file storage |
| `monkeyslegion-telemetry` | Metrics, tracing, logging |

---

## 🛠️ CLI Commands

```bash
php ml key:generate          # Generate APP_KEY
php ml make:migration        # Generate migration from entity diff
php ml migrate               # Run pending migrations
php ml rollback              # Revert last migration
php ml schema:update         # Sync entities → database
php ml cache:clear           # Clear all caches
php ml route:list            # Show registered routes
php ml tinker                # Interactive REPL
```

---

## 📋 Code Standards

- **PHP 8.4+** with `declare(strict_types=1)` mandatory
- **4-space indentation**, LF line endings, UTF-8
- **`final` classes** by default
- **Property hooks** for validation/formatting (no getters/setters)
- **`public private(set)`** for auto-incremented IDs
- **PSR-14** for events, **PSR-15** for middleware, **PSR-7** for messages
- **PHPStan Level 9** enforced
- **PHPUnit 11** with attributes (`#[Test]`, `#[CoversClass]`, `#[DataProvider]`)

See [monkeyslegion_v2_code_standards.md](monkeyslegion_v2_code_standards.md) for the complete standards document.

---

## 📝 License

MIT © [MonkeysCloud](https://monkeys.cloud)
