# MonkeysLegion v2 — AI Agent Guide

> This document helps AI coding agents (Claude, Copilot, Cursor, etc.) understand the MonkeysLegion framework conventions and architecture. Place this file at the project root as `AGENTS.md`.

## Quick Facts

- **Language:** PHP 8.4+ (uses property hooks, `readonly`, enums, attributes)
- **Architecture:** PSR-7/PSR-15 HTTP, PSR-11 DI container, attribute-based routing
- **Config format:** `.mlc` files (MonkeysLegion Config — custom HOCON-like format)
- **Template engine:** `.ml.php` files (Blade-like syntax)
- **Entry point:** `public/index.php` → `Application::create(basePath: ...)->run()`
- **Namespace:** App code lives under `App\` (PSR-4 mapped to `app/`)
- **Tests:** PHPUnit 11+ in `tests/` (Unit, Integration, Feature, Performance)

---

## Project Structure

```
├── app/                    # Application code (App\ namespace)
│   ├── Controller/         # HTTP controllers (auto-discovered)
│   │   └── Api/            # API controllers (usually prefixed)
│   ├── Dto/                # Request DTOs with validation attributes
│   ├── Entity/             # Database entities with attribute mappings
│   ├── Enum/               # PHP 8.1+ enums
│   ├── Event/              # Domain events
│   ├── Job/                # Queue jobs
│   ├── Listener/           # Event listeners
│   ├── Middleware/          # Custom PSR-15 middleware
│   ├── Policy/             # Authorization policies
│   ├── Providers/          # Service providers (DI definitions)
│   ├── Repository/         # Data access (extends EntityRepository)
│   ├── Resource/           # API response transformers
│   └── Service/            # Business logic layer
├── config/                 # Configuration
│   ├── app.mlc             # Core app settings
│   ├── app.php             # DI container overrides (interface bindings)
│   ├── database.mlc        # Database connections
│   ├── middleware.mlc       # Global middleware pipeline
│   └── *.mlc               # Other config (auth, cache, cors, etc.)
├── resources/views/        # Templates (.ml.php files)
│   └── layouts/            # Layout templates
├── public/                 # Web root
│   ├── index.php           # Front controller (DO NOT MODIFY)
│   └── assets/             # Static files (CSS, JS, images)
├── database/migrations/    # SQL migration files
├── tests/                  # PHPUnit test suites
├── server.php              # PHP built-in server router
└── bin/                    # CLI scripts
```

---

## Creating a Controller

Controllers live in `app/Controller/` and are auto-discovered. **No registration needed.**

### Web Controller (returns HTML)
```php
<?php
declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Template\Renderer;

final class ProductController
{
    public function __construct(
        private readonly Renderer $renderer,
    ) {}

    #[Route(methods: 'GET', path: '/products', name: 'products.index')]
    public function index(): Response
    {
        return Response::html($this->renderer->render('products.index', [
            'title' => 'Products',
            'products' => [], // pass data to template
        ]));
    }

    #[Route(methods: 'GET', path: '/products/{id:\d+}', name: 'products.show')]
    public function show(ServerRequestInterface $request, string $id): Response
    {
        // $id comes from the URL parameter
        return Response::html($this->renderer->render('products.show', [
            'product' => $this->repo->findOrFail((int) $id),
        ]));
    }
}
```

### API Controller (returns JSON)
```php
<?php
declare(strict_types=1);

namespace App\Controller\Api;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Router\Attributes\RoutePrefix;
use MonkeysLegion\Router\Attributes\Middleware;
use MonkeysLegion\Auth\Attribute\Authenticated;
use MonkeysLegion\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

#[RoutePrefix('/api/v2/products')]
#[Middleware(['cors'])]
final class ProductApiController
{
    public function __construct(
        private readonly ProductService $service,
        private readonly ProductRepository $products,
    ) {}

    #[Route('GET', '/', name: 'api.products.index', summary: 'List products', tags: ['Products'])]
    public function index(): Response
    {
        return Response::json(['data' => $this->products->findAll()]);
    }

    #[Route('POST', '/', name: 'api.products.create', summary: 'Create product', tags: ['Products'])]
    #[Authenticated]
    public function create(CreateProductRequest $dto): Response
    {
        $product = $this->service->create($dto);
        return Response::json(['data' => $product], 201);
    }
}
```

### Route Attribute Options
```php
#[Route(
    methods: 'GET',              // HTTP method(s): 'GET', 'POST', ['GET', 'POST']
    path: '/users/{id:\d+}',    // Path with regex constraints
    name: 'users.show',         // Named route for URL generation
    summary: 'Get user',        // OpenAPI summary
    tags: ['Users'],            // OpenAPI tags
    middleware: ['auth'],       // Route-level middleware
    where: ['id' => '\d+'],    // Parameter constraints
    defaults: ['id' => '1'],   // Default parameter values
)]
```

---

## Creating an Entity

Entities use PHP 8.4 property hooks and attribute-based mapping:

```php
<?php
declare(strict_types=1);

namespace App\Entity;

use MonkeysLegion\Entity\Attributes\{Entity, Field, Id, Fillable, Hidden, Index, Timestamps, SoftDeletes};
use MonkeysLegion\Entity\Attributes\{ManyToOne, OneToMany};

#[Entity(table: 'products')]
#[Timestamps]
#[SoftDeletes]
#[Index(columns: ['slug'], name: 'idx_products_slug')]
class Product
{
    #[Id]
    #[Field(type: 'unsignedBigInt', autoIncrement: true)]
    public private(set) int $id;

    #[Field(type: 'string', length: 255)]
    #[Fillable]
    public string $name;

    #[Field(type: 'string', length: 300)]
    public string $slug {
        set(string $value) {
            $this->slug = strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($value)), '-'));
        }
    }

    #[Field(type: 'decimal', precision: 10, scale: 2)]
    #[Fillable]
    public float $price;

    #[Field(type: 'boolean')]
    public bool $active = true;

    #[Field(type: 'datetime')]
    public private(set) \DateTimeImmutable $created_at;

    #[Field(type: 'datetime')]
    public private(set) \DateTimeImmutable $updated_at;

    // Computed property (PHP 8.4 property hook, not stored in DB)
    public string $formattedPrice {
        get => '$' . number_format($this->price, 2);
    }
}
```

### Field Types
`string`, `text`, `int`, `unsignedBigInt`, `float`, `decimal`, `boolean`, `datetime`, `date`, `json`, `binary`

---

## Repository Pattern

```php
<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use MonkeysLegion\Query\Repository\EntityRepository;

/**
 * @extends EntityRepository<Product>
 */
class ProductRepository extends EntityRepository
{
    protected string $table = 'products';
    protected string $entityClass = Product::class;

    /** @return list<Product> */
    public function findActive(): array
    {
        return $this->findBy(
            criteria: ['active' => true, 'deleted_at' => null],
            orderBy: ['name' => 'ASC'],
        );
    }

    /** @return list<Product> */
    public function search(string $term): array
    {
        $ids = array_column(
            $this->query()
                ->where('active', '=', true)
                ->whereNull('deleted_at')
                ->where('name', 'LIKE', "%{$term}%")
                ->orderBy('name', 'ASC')
                ->get(),
            'id',
        );
        return $this->findByIds($ids);
    }
}
```

### Key Repository Methods
- `find(int $id): ?Entity` — Find by ID
- `findOrFail(int $id): Entity` — Find or throw
- `findBy(array $criteria, array $orderBy, ?int $limit): array`
- `findByIds(array $ids): array`
- `persist(Entity $entity): void` — Insert or update
- `delete(int $id): void`
- `query(): QueryBuilder` — Raw query builder

---

## Request DTOs & Validation

DTOs are auto-hydrated from the request body:

```php
<?php
declare(strict_types=1);

namespace App\Dto;

use MonkeysLegion\Validation\Attributes\{NotBlank, Length, Email, Range};

final readonly class CreateProductRequest
{
    public function __construct(
        #[NotBlank]
        #[Length(min: 2, max: 255)]
        public string $name,

        #[NotBlank]
        #[Range(min: 0.01)]
        public float $price,

        public string $description = '',
        public bool $active = true,
    ) {}
}
```

In the controller, type-hint the DTO to auto-validate:
```php
#[Route('POST', '/', name: 'products.create')]
public function create(CreateProductRequest $dto): Response
{
    // $dto is already validated; if invalid, a 422 is returned automatically
}
```

---

## Service Layer

Business logic goes in `app/Service/`. Use constructor injection:

```php
<?php
declare(strict_types=1);

namespace App\Service;

use MonkeysLegion\DI\Attributes\Singleton;
use Psr\Log\LoggerInterface;

#[Singleton]
final class ProductService
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly LoggerInterface $logger,
    ) {}

    public function create(CreateProductRequest $dto): Product
    {
        $product = new Product();
        $product->name = $dto->name;
        $product->slug = $dto->name;
        $product->price = $dto->price;

        $this->products->persist($product);
        $this->logger->info('Product created', ['name' => $product->name]);

        return $product;
    }
}
```

---

## API Resources (Response Transformers)

```php
<?php
declare(strict_types=1);

namespace App\Resource;

use App\Entity\Product;
use MonkeysLegion\Http\Message\Response;

final class ProductResource
{
    public static function toArray(Product $p): array
    {
        return [
            'id' => $p->id,
            'type' => 'products',
            'attributes' => [
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => $p->price,
                'formatted_price' => $p->formattedPrice,
                'active' => $p->active,
            ],
        ];
    }

    public static function make(Product $p, int $status = 200): Response
    {
        return Response::json(['data' => self::toArray($p)], $status);
    }

    /** @param list<Product> $products */
    public static function collection(array $products): Response
    {
        return Response::json([
            'data' => array_map(self::toArray(...), $products),
            'meta' => ['total' => count($products)],
        ]);
    }
}
```

---

## Template Engine (.ml.php)

Templates use Blade-like syntax. Files live in `resources/views/`.

### Layouts & Sections
```php
{{-- resources/views/layouts/app.ml.php --}}
<!DOCTYPE html>
<html>
<head><title>@yield('title', 'Default')</title></head>
<body>
    @yield('content')
</body>
</html>

{{-- resources/views/products/index.ml.php --}}
@extends('layouts.app')

@section('title', 'Products')

@section('content')
<h1>Products</h1>
@foreach($products as $product)
    <p>{{ $product->name }} — {{ $product->formattedPrice }}</p>
@endforeach
@endsection
```

### Directive Reference
| Directive | Purpose |
|-----------|---------|
| `@extends('layout')` | Inherit from layout |
| `@section('name') ... @endsection` | Define content block |
| `@yield('name', 'default')` | Output a section |
| `{{ $var }}` | Escaped output (htmlspecialchars) |
| `{!! $html !!}` | Raw HTML output |
| `@if / @elseif / @else / @endif` | Conditionals |
| `@foreach($items as $item) / @endforeach` | Loops |
| `@for / @endfor` | For loops |
| `@while / @endwhile` | While loops |
| `@isset($var) / @endisset` | Isset check |
| `@empty($var) / @endempty` | Empty check |
| `@env('dev') / @endenv` | Environment check |
| `@push('scripts') / @endpush` | Push to stack |
| `@stack('scripts')` | Render stack |
| `@include('partial')` | Include sub-template |
| `@php / @endphp` | Raw PHP block |
| `{{-- comment --}}` | Template comment (stripped from output) |

### Rendering in Controllers
```php
$html = $this->renderer->render('products.index', [
    'title'    => 'Products',
    'products' => $products,
]);
return Response::html($html);
```

The dot notation `products.index` maps to `resources/views/products/index.ml.php`.

---

## Configuration (.mlc files)

MonkeysLegion uses `.mlc` files (HOCON-like syntax) in `config/`:

```
# config/app.mlc
app {
    name     = ${APP_NAME:"My App"}
    env      = ${APP_ENV:production}
    debug    = ${APP_DEBUG:false}
    url      = ${APP_URL:http://localhost:8000}
    key      = ${APP_KEY}
    timezone = "UTC"
}
```

- `${VAR:default}` — Environment variable with default
- Nested blocks with `{ }`
- Arrays with `[ ]`
- Comments with `#`
- Strings with or without quotes

### DI Container Overrides (config/app.php)
```php
return [
    SomeInterface::class => fn($c) => $c->get(ConcreteImpl::class),
];
```

---

## Middleware

### Global Middleware (config/middleware.mlc)
Defined in `middleware.global` array, executed in order.

### Route-Level Middleware
```php
#[Middleware(['auth', 'throttle:60,1'])]
```

### Custom Middleware
```php
<?php
declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};

final class TimingMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $start = microtime(true);
        $response = $handler->handle($request);
        $elapsed = microtime(true) - $start;

        return $response->withHeader('X-Response-Time', sprintf('%.3fms', $elapsed * 1000));
    }
}
```

---

## Response Helpers

```php
use MonkeysLegion\Http\Message\Response;

Response::html($html);                      // 200 text/html
Response::json($data, 200);                 // 200 application/json
Response::json($data, 201);                 // 201 Created
Response::noContent();                       // 204 No Content
Response::redirect('/login');                // 302 redirect
Response::redirect('/login', 301);           // 301 redirect
```

---

## Events & Listeners

```php
// app/Event/ProductCreated.php
final readonly class ProductCreated
{
    public function __construct(public Product $product) {}
}

// app/Listener/NotifyOnProduct.php
final class NotifyOnProduct
{
    public function __invoke(ProductCreated $event): void
    {
        // handle event
    }
}

// Dispatching
$this->events->dispatch(new ProductCreated($product));
```

---

## CLI Commands

Run with `php vendor/bin/ml <command>` or `./ml <command>`:

```bash
php vendor/bin/ml key:generate          # Generate APP_KEY
php vendor/bin/ml list                  # List all commands
composer serve                          # Start dev server (port 8000, hot-reload)
composer dev                            # Start simple dev server (port 8080)
composer test                           # Run all tests
```

---

## Testing

```bash
composer test                    # All tests
composer test:unit               # Unit tests only
composer test:feature            # Feature tests only
composer test:integration        # Integration tests only
```

Test base classes provide framework bootstrapping:
- `Tests\Unit\*` — Pure unit tests, no framework boot
- `Tests\Feature\*` — Full HTTP pipeline tests
- `Tests\Integration\*` — DI container + service tests

---

## Key Conventions

1. **Always `declare(strict_types=1)`** at the top of every PHP file
2. **Final classes** by default — only make non-final if inheritance is needed
3. **Constructor promotion** with `readonly` for dependencies
4. **Attribute routing** — no route files, routes are on controller methods
5. **Controllers are auto-discovered** in `app/Controller/` — no registration
6. **Services use `#[Singleton]`** attribute for single-instance DI
7. **DTOs are `readonly`** — immutable request objects
8. **Entity property hooks** — use PHP 8.4 `set` hooks for validation/transformation
9. **Repository pattern** — extend `EntityRepository`, never put queries in controllers
10. **Config in `.mlc`**, DI bindings in `config/app.php` — separate concerns

---

## Common Packages

| Package | Purpose |
|---------|---------|
| `monkeyslegion` | Core framework (Application, Kernel, Providers) |
| `monkeyslegion-router` | Attribute routing, compiled matching |
| `monkeyslegion-http` | PSR-7 messages, PSR-15 middleware pipeline |
| `monkeyslegion-template` | Blade-like template engine (.ml.php) |
| `monkeyslegion-di` | Dependency injection container |
| `monkeyslegion-entity` | Entity attribute mappings |
| `monkeyslegion-query` | Query builder & repository base |
| `monkeyslegion-validation` | Attribute-based validation |
| `monkeyslegion-auth` | Authentication guards & middleware |
| `monkeyslegion-session` | Session management, CSRF |
| `monkeyslegion-cli` | CLI kernel & commands |
| `monkeyslegion-mlc` | MLC config file parser |
| `monkeyslegion-i18n` | Internationalization |
| `monkeyslegion-cache` | PSR-16 cache (Redis, File, Array) |
| `monkeyslegion-mail` | Email sending |
| `monkeyslegion-queue` | Background job processing |
| `monkeyslegion-schedule` | Task scheduling |
| `monkeyslegion-dev-server` | Development server with hot-reload |
