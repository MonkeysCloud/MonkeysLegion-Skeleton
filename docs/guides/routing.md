# Routing

Routes are defined as PHP 8.4 attributes directly on controller methods. No route files needed — controllers are auto-discovered.

## Basic Routes

```php
use MonkeysLegion\Router\Attributes\Route;

#[Route(methods: 'GET', path: '/users', name: 'users.index')]
public function index(): Response { ... }

#[Route(methods: 'POST', path: '/users', name: 'users.create')]
public function create(): Response { ... }

#[Route(methods: 'GET', path: '/users/{id:\d+}', name: 'users.show')]
public function show(string $id): Response { ... }

#[Route(methods: 'PUT', path: '/users/{id:\d+}', name: 'users.update')]
public function update(string $id): Response { ... }

#[Route(methods: 'DELETE', path: '/users/{id:\d+}', name: 'users.destroy')]
public function destroy(string $id): Response { ... }
```

## Route Attribute Options

```php
#[Route(
    methods: 'GET',               // HTTP method(s): 'GET', 'POST', ['GET', 'POST']
    path: '/users/{id:\d+}',     // Path with regex constraints
    name: 'users.show',           // Named route for URL generation
    summary: 'Get user',          // OpenAPI summary
    tags: ['Users'],              // OpenAPI tags
    middleware: ['auth'],         // Route-level middleware
    where: ['id' => '\d+'],      // Parameter constraints
    defaults: ['id' => '1'],     // Default parameter values
)]
```

## Multiple HTTP Methods

```php
#[Route(methods: ['GET', 'POST'], path: '/contact')]
public function contact(): Response { ... }
```

## Route Prefixes

Use `#[RoutePrefix]` on API controllers:

```php
#[RoutePrefix('/api/v2/products')]
final class ProductApiController
{
    #[Route('GET', '/')]           // → GET /api/v2/products/
    public function index(): Response { ... }

    #[Route('GET', '/{id:\d+}')]   // → GET /api/v2/products/123
    public function show(string $id): Response { ... }
}
```

## Route Parameters

### Required Parameters

```php
#[Route(path: '/posts/{slug}')]
public function show(string $slug): Response { ... }
```

### Regex Constraints

```php
#[Route(path: '/users/{id:\d+}')]           // Numeric only
#[Route(path: '/posts/{slug:[a-z0-9-]+}')]   // Slug format
```

### Optional Parameters

```php
#[Route(path: '/posts/{slug?}')]
public function show(?string $slug = null): Response { ... }
```

## Route-Level Middleware

```php
use MonkeysLegion\Router\Attributes\Middleware;

#[Route('POST', '/api/posts', name: 'posts.create')]
#[Middleware(['auth', 'throttle:60,1'])]
public function create(): Response { ... }
```

## Named Routes

Named routes enable URL generation:

```php
#[Route(path: '/users/{id:\d+}', name: 'users.show')]
public function show(string $id): Response { ... }

// Generate URL
$url = route('users.show', ['id' => 42]);
// → /users/42
```

## Authentication Attributes

```php
use MonkeysLegion\Auth\Attribute\{Authenticated, RequiresRole};

#[Authenticated]                           // Require login
public function dashboard(): Response { ... }

#[RequiresRole('admin')]                   // Require specific role
public function adminPanel(): Response { ... }
```

## Rate Limiting

```php
use MonkeysLegion\Auth\Attribute\Throttle;

#[Throttle(max: 60, per: 1)]               // 60 requests per minute
#[Route('POST', '/api/login')]
public function login(): Response { ... }
```

## CLI Route Commands

```bash
php bin/ml route:list                # List all registered routes
php bin/ml route:cache                # Cache routes for production
php bin/ml route:clear                # Clear route cache
php bin/ml route:js                   # Export routes as TypeScript types
```
