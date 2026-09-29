# MonKeysLegion Skeleton v2.1

[![PHP Version](https://img.shields.io/badge/php-8.4%2B-8892BF.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Tests](https://img.shields.io/badge/tests-139%20passed-brightgreen.svg)](#-testing)
[![Packagist](https://img.shields.io/packagist/v/monkeyscloud/monkeyslegion-skeleton.svg)](https://packagist.org/packages/monkeyscloud/monkeyslegion-skeleton)

**Production-ready PHP 8.4 skeleton for building web apps & APIs with the MonKeysLegion framework v2.1.**

Built on attribute-first routing, property hooks, asymmetric visibility, and a zero-magic PSR-15 pipeline. Now with Inertia.js SSR, Vite asset pipeline, feature flags, webhooks, Markdown rendering, search indexing, and multi-channel notifications.

---

## ✨ What's New in v2.1

| Feature | v2.0 | v2.1 |
|---------|------|------|
| **Inertia.js** | — | Full adapter with SSR support |
| **Vite** | — | Asset pipeline with HMR + manifest |
| **Feature Flags** | — | Memory/Database/Redis drivers |
| **Webhooks** | — | Signing, delivery, retry, multi-driver |
| **Markdown** | — | Pure PHP renderer with extensions |
| **Search** | — | Null/Database/Meilisearch engines |
| **Notifications** | Mail only | + Slack, Teams, Webhook channels |
| **OAuth** | — | Google, GitHub, GitLab, Facebook, X, Microsoft |
| **HTTP Middleware** | 6 | 12+ (CSP, Compression, Conditional, Correlation, Signed URLs, Deprecation) |
| **Entity Casts** | — | JSON, Boolean, DateTime, Decimal, Enum, Array |
| **Query Builder** | Basic | + `whereHas`, `whereDoesntHave`, `has`, `doesntHave`, `load()` |
| **Database** | — | Model factories, nested transactions |
| **Schedule** | Basic | + Health monitor (degraded/unhealthy detection) |
| **Core** | — | GDPR module, health checks, OPcache preload |
| **Telemetry** | Basic | + Prometheus exporter, OTLP/HTTP tracing |
| **CLI** | 15 commands | 35+ commands, React/Vue presets, TypeScript generator |
| **Validation** | 7 rules | + `#[NotPwned]` (HIBP API check) |
| **Router** | — | + `#[ApiVersion]`, `#[Deprecated]` attributes |
| **DevTools** | 4 panels | + Request, Routes, Session, Timeline panels |

---

## ✨ Features Overview

| Category                 | Features                                                 |
| ------------------------ | -------------------------------------------------------- |
| **HTTP Stack**           | PSR-7/15 compliant, middleware pipeline, SAPI emitter    |
| **Routing**              | Attribute-based v2, auto-discovery, constraints, caching |
| **Dependency Injection** | PSR-11 container with `#[Singleton]`, `#[Provider]`      |
| **Database**             | Native PDO MySQL 8.4, Query Builder, Micro-ORM, Factories |
| **Authentication**       | JWT, RBAC, 2FA, OAuth (6 providers), API keys            |
| **API Documentation**    | Live OpenAPI 3.1 & Swagger UI                            |
| **Validation**           | DTO binding with attribute constraints                   |
| **Rate Limiting**        | `#[Throttle]` attribute, sliding-window (IP + User)      |
| **Templating**           | MLView with components, slots, caching                   |
| **CLI**                  | 35+ commands, React/Vue presets, TypeScript generator    |
| **Files**                | Multi-driver storage, image processing, chunked uploads  |
| **I18n**                 | Full internationalization & localization support         |
| **Telemetry**            | Prometheus metrics, OTLP tracing, PSR-3 logging          |
| **Mail**                 | SMTP, Markdown templates, DKIM support                   |
| **Caching**              | Multiple drivers (File, Redis, Memcached)                |
| **Feature Flags**        | Memory, Database, Redis drivers                          |
| **Webhooks**             | HMAC signing, retry, delivery tracking                   |
| **Search**               | Database full-text, Meilisearch integration              |
| **Notifications**        | Mail, Slack, Teams, Webhook channels                     |
| **Markdown**             | Pure PHP renderer with extensions                        |
| **Inertia.js**           | SSR + CSR fallback, lazy props, flash bridge             |
| **Vite**                 | Asset pipeline, HMR, manifest, route exporter            |
| **Security**             | CSP, GDPR, audit log, signed URLs, SQL guard             |
| **Health Checks**        | Database, cache, disk space monitoring                   |

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

### Frontend Setup (Inertia.js + Vite)

```bash
php ml frontend:install --preset=react
# or
php ml frontend:install --preset=vue

npm run dev    # Start Vite dev server (HMR)
npm run build  # Production build
```

---

## 📁 Project Structure

```text
my-app/
├─ app/
│  ├─ Controller/          # Attribute-routed controllers
│  │  └─ Api/              # API controllers (UserController, PostController, AuthController)
│  ├─ Dto/                 # Request DTOs with validation attributes
│  ├─ Entity/              # Entities with PHP 8.4 property hooks + casts
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
│  ├─ auth.mlc             # JWT, guards, 2FA, OAuth
│  ├─ cache.mlc            # Cache drivers
│  ├─ compression.mlc      # HTTP compression
│  ├─ cors.mlc             # CORS policy
│  ├─ database.mlc         # Database connection
│  ├─ feature-flags.mlc    # Feature flag drivers
│  ├─ inertia.mlc          # Inertia.js SSR config
│  ├─ logging.mlc          # Log channels
│  ├─ mail.mlc             # SMTP/mailer
│  ├─ markdown.mlc         # Markdown renderer
│  ├─ middleware.mlc        # Middleware pipeline + aliases
│  ├─ notifications.mlc    # Notification channels
│  ├─ queue.mlc            # Queue drivers
│  ├─ search.mlc           # Search engine config
│  ├─ security.mlc         # CSP, CSRF, GDPR, audit, signed URLs
│  ├─ session.mlc          # Session config
│  ├─ telemetry.mlc        # Metrics, tracing, logging
│  ├─ vite.mlc             # Vite asset pipeline
│  └─ webhooks.mlc         # Webhook signing + delivery
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

### Entities (PHP 8.4 Property Hooks + Asymmetric Visibility + Casts)

```php
use MonkeysLegion\Entity\Attributes\Entity;
use MonkeysLegion\Entity\Attributes\Field;
use MonkeysLegion\Entity\Attributes\Id;
use MonkeysLegion\Entity\Attributes\Timestamps;
use MonkeysLegion\Entity\Casts\CastManager;
use MonkeysLegion\Auth\Contract\AuthenticatableInterface;

#[Entity(table: 'users')]
#[Timestamps]
final class User implements AuthenticatableInterface
{
    #[Id]
    #[Field(type: 'unsignedBigInt', autoIncrement: true)]
    public private(set) int $id;

    #[Field(type: 'string', length: 255, unique: true)]
    public string $email {
        set(string $value) { $this->email = strtolower(trim($value)); }
    }

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
}
```

**Entity Casts (v2.1):**

```php
use MonkeysLegion\Entity\Casts\{JsonCast, BooleanCast, DatetimeCast, EnumCast};

// In your entity:
#[Field(type: 'json')]
#[Cast(JsonCast::class)]
public array $metadata = [];

#[Field(type: 'boolean')]
#[Cast(BooleanCast::class)]
public bool $active = true;

#[Field(type: 'string')]
#[Cast(EnumCast::class, enum: UserRole::class)]
public UserRole $role = UserRole::User;
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
}
```

### Controllers (Attribute Routing + Authorization)

```php
use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Router\Attributes\RoutePrefix;
use MonkeysLegion\Router\Attributes\Middleware;
use MonkeysLegion\Router\Attributes\ApiVersion;
use MonkeysLegion\Router\Attributes\Deprecated;
use MonkeysLegion\Auth\Attribute\Authenticated;
use MonkeysLegion\Auth\Attribute\RequiresRole;
use MonkeysLegion\Http\Message\Response;

#[RoutePrefix('/api/v2/users')]
#[Middleware(['cors', 'throttle:60,1'])]
#[ApiVersion('2')]
final class UserController
{
    #[Route('GET', '/', name: 'users.index', summary: 'List users', tags: ['Users'])]
    public function index(ServerRequestInterface $request): Response
    {
        return UserResource::collection($this->users->findActiveUsers());
    }

    #[Route('POST', '/', name: 'users.create')]
    #[Authenticated]
    #[RequiresRole('admin')]
    public function create(CreateUserRequest $dto): Response
    {
        $user = $this->service->createUser($dto);
        return UserResource::make($user, 201);
    }

    #[Route('DELETE', '/{id:\d+}', name: 'users.destroy')]
    #[Authenticated]
    #[RequiresRole('admin')]
    #[Deprecated(sunset: '2026-12-31', link: '/docs/api/v3')]
    public function destroy(string $id): Response
    {
        $this->service->deleteUser((int) $id);
        return Response::noContent();
    }
}
```

### OAuth Social Login (v2.1)

```php
use MonkeysLegion\Auth\OAuth\OAuthManager;

#[Route('GET', '/oauth/{provider}', name: 'oauth.redirect')]
public function redirect(string $provider, OAuthManager $oauth): Response
{
    return $oauth->provider($provider)->redirect();
}

#[Route('GET', '/oauth/{provider}/callback', name: 'oauth.callback')]
public function callback(string $provider, OAuthManager $oauth): Response
{
    $user = $oauth->provider($provider)->user();
    $localUser = $this->userProvider->findOrCreateFromOAuth($provider, $user);
    // Generate JWT or session...
    return Response::redirect('/dashboard');
}
```

**Supported providers:** Google, GitHub, GitLab, Facebook, X (Twitter), Microsoft

### Inertia.js + Vite (v2.1)

```php
use MonkeysLegion\Inertia\Inertia;

// In a controller — returns an Inertia response
return Inertia::render('Users/Index', [
    'users' => $users,
    'filters' => $request->getQueryParams(),
]);

// Lazy props (only loaded when needed)
return Inertia::render('Dashboard', [
    'stats' => Inertia::lazy(fn() => $this->stats->compute()),
]);

// SSR is automatic when enabled in config/inertia.mlc
```

```php
{{-- In templates --}}
@vite(['resources/css/app.css', 'resources/js/app.tsx'])
```

### Feature Flags (v2.1)

```php
use MonkeysLegion\FeatureFlags\FeatureManager;

if ($flags->isEnabled('new_checkout_flow')) {
    return $this->renderer->render('checkout.v2');
}
return $this->renderer->render('checkout.v1');

// Route-level middleware
#[Route('GET', '/beta', name: 'beta')]
#[Middleware(['feature:new_checkout_flow'])]
public function beta(): Response { }
```

### Notifications (v2.1)

```php
use MonkeysLegion\Notifications\NotificationDispatcher;
use MonkeysLegion\Notifications\Messages\SlackMessage;

$notifier->send('alerts', new SlackMessage(
    channel: '#alerts',
    text: 'Deploy completed successfully',
));
```

### Webhooks (v2.1)

```php
use MonkeysLegion\Webhooks\WebhookManager;

$webhooks->dispatch('order.created', [
    'order_id' => $order->id,
    'total'    => $order->total,
]);
// HMAC-signed, retried with exponential backoff
```

### Markdown (v2.1)

```php
use MonkeysLegion\Markdown\MarkdownRenderer;

$html = $renderer->render('# Hello **World**');
```

### Search (v2.1)

```php
use MonkeysLegion\Search\SearchManager;

$results = $search->index('posts')->search('monkeyslegion', limit: 10);
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
}
```

```mlc
# config/feature-flags.mlc
feature_flags {
    driver = ${FEATURE_FLAGS_DRIVER:memory}
    table  = "feature_flags"
}
```

```mlc
# config/inertia.mlc
inertia {
    root_view = "layouts.inertia-app"
    ssr {
        enabled = ${INERTIA_SSR:false}
        url     = "http://localhost:13714"
    }
}
```

The only PHP config file is `config/app.php` — reserved exclusively for DI container bindings.

---

## 📦 Package Ecosystem

The `monkeyscloud/monkeyslegion` meta-package (^2.1) requires all sub-packages transitively. You only need one dependency:

```json
{
    "require": {
        "monkeyscloud/monkeyslegion": "^2.1"
    }
}
```

### Core Packages

| Package | Version | Purpose |
|---------|---------|---------|
| `monkeyslegion` | 2.1.2 | Meta-package — bridges skeleton to all sub-packages |
| `monkeyslegion-core` | 2.1.0 | Kernel, Application builder, GDPR, health checks, OPcache |
| `monkeyslegion-di` | 2.0.1 | PSR-11 DI container with `#[Singleton]`, `#[Provider]` |
| `monkeyslegion-mlc` | 2.0.0 | `.mlc` config parser |
| `monkeyslegion-contracts` | 2.0.0 | Shared interfaces |
| `monkeyslegion-env` | 2.0.0 | Environment loader |

### HTTP & Routing

| Package | Version | Purpose |
|---------|---------|---------|
| `monkeyslegion-http` | 2.1.0 | PSR-7 messages + 12 middleware (CSP, Compression, etc.) |
| `monkeyslegion-router` | 2.2.0 | Attribute routing, `#[ApiVersion]`, `#[Deprecated]` |
| `monkeyslegion-session` | 2.0.0 | Session management, CSRF |
| `monkeyslegion-validation` | 2.1.0 | DTO validation, `#[NotPwned]` HIBP check |

### Database & ORM

| Package | Version | Purpose |
|---------|---------|---------|
| `monkeyslegion-database` | 2.2.0 | PDO MySQL, factories, nested transactions |
| `monkeyslegion-query` | 2.1.0 | Query builder, `whereHas`, `load()`, SQL safety |
| `monkeyslegion-entity` | 2.1.0 | Entity mapper, casts (JSON, Enum, DateTime, etc.) |
| `monkeyslegion-migration` | 2.0.0 | Schema diff + migration runner |

### Auth & Security

| Package | Version | Purpose |
|---------|---------|---------|
| `monkeyslegion-auth` | 2.2.0 | JWT, RBAC, 2FA, OAuth (6 providers), API keys, audit |
| `monkeyslegion-permissions` | 1.1.0 | Fine-grained permission system |
| `monkeyslegion-encryption` | 1.0.1 | Encryption utilities |

### Frontend

| Package | Version | Purpose |
|---------|---------|---------|
| `monkeyslegion-inertia` | 1.0.0 | Inertia.js adapter with SSR |
| `monkeyslegion-vite` | 1.0.0 | Vite asset pipeline, `@vite` directive, route exporter |
| `monkeyslegion-template` | 2.0.0 | MLView Blade-like template engine |

### New Ecosystem Packages (v2.1)

| Package | Version | Purpose |
|---------|---------|---------|
| `monkeyslegion-feature-flags` | 1.0.1 | Memory/Database/Redis feature flag drivers |
| `monkeyslegion-webhooks` | 1.0.1 | HMAC signing, retry, delivery tracking |
| `monkeyslegion-markdown` | 1.0.1 | Pure PHP Markdown renderer |
| `monkeyslegion-search` | 1.1.0 | Null/Database/Meilisearch search engines |
| `monkeyslegion-notifications` | 1.1.0 | Mail/Slack/Teams/Webhook channels |
| `monkeyslegion-testing` | 1.0.0 | HTTP testing DSL, fakes, snapshots |

### Infrastructure

| Package | Version | Purpose |
|---------|---------|---------|
| `monkeyslegion-cli` | 2.1.0 | 35+ commands, React/Vue presets, TypeScript generator |
| `monkeyslegion-cache` | 2.0.1 | PSR-16 cache (File, Redis, Memcached) |
| `monkeyslegion-queue` | 2.0.0 | Background job processing |
| `monkeyslegion-schedule` | 1.2.0 | Task scheduling + health monitor |
| `monkeyslegion-mail` | 2.0.0 | SMTP, DKIM, queued mail |
| `monkeyslegion-files` | 2.0.0 | Multi-driver storage, image processing |
| `monkeyslegion-events` | 2.0.0 | PSR-14 event dispatcher |
| `monkeyslegion-i18n` | 2.1.0 | Internationalization |
| `monkeyslegion-telemetry` | 2.1.0 | Prometheus, OTLP tracing, structured logging |
| `monkeyslegion-devtools` | 2.1.0 | Debug toolbar with 8 panels |
| `monkeyslegion-openapi` | 1.1.0 | OpenAPI 3.1 + Swagger UI + versioning |
| `monkeyslegion-resources` | 1.1.0 | JSON:API resources + TypeScript generator |

---

## 🔧 Helper Functions

```php
// Path helpers
base_path('config/app.mlc');
app_path('Entity');
config_path('auth.mlc');
storage_path('logs/app.log');

// Asset helpers (versioned URLs)
asset('css/app.css');

// Translation helpers
trans('messages.welcome');
trans('messages.greeting', ['name' => 'Jorge']);

// CSRF helpers
csrf_token();
csrf_field();

// Auth helpers
auth_user_id();
auth_check();
```

---

## 🧪 Testing

```bash
composer test                    # All tests
composer test:unit               # Unit tests only
composer test:feature            # Feature tests only
composer test:integration        # Integration tests only
composer test:performance        # Benchmarks
composer test:coverage           # Coverage report
```

### Testing Toolkit (v2.1)

```php
use MonkeysLegion\Testing\TestCase;
use MonkeysLegion\Testing\Concerns\RefreshDatabase;

final class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_user(): void
    {
        $response = $this->post('/api/users', [
            'name' => 'Bob',
            'email' => 'bob@example.com',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('data.name', 'Bob');
    }
}
```

**Available fakes:** `QueueFake`, `MailFake`, `EventFake` — all with assertion methods.

---

## 🚀 Performance

### Benchmarks (PHP 8.5, Apple Silicon)

| Operation | Ops/sec |
|-----------|---------|
| Entity creation | **6.3M** |
| DTO construction | **10.9M** |
| Property hooks (email normalize) | **11.1M** |
| Computed properties (displayName) | **41M** |
| Enum operations | **8.7M** |
| Resource serialization (50-item) | **43.8K** |
| **Peak memory** | **4 MB** |

```bash
php tests/Performance/benchmark_detailed.php
```

---

## 📋 Requirements

- **PHP 8.4+** — Required for property hooks and asymmetric visibility
- **MySQL 8.4** — Recommended database
- **Composer 2.x** — Dependency management
- **Node.js 18+** — For Vite + Inertia.js (optional, frontend only)

### Recommended PHP Extensions

| Extension | Purpose |
|-----------|---------|
| `pdo_mysql` | Database connectivity |
| `redis` | Caching, rate limiting, sessions, feature flags |
| `mbstring` | Multi-byte string handling |
| `gd` or `imagick` | Image processing |
| `intl` | Advanced I18n formatting |

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
- **PHPUnit 11** with attributes

---

## 🤝 Contributing

1. Fork 🍴
2. Create a feature branch 🌱
3. Submit a PR 🚀

Happy hacking with **MonKeysLegion**! 🎉

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
