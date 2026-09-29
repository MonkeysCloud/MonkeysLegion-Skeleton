# Architecture

## Request Lifecycle

```
HTTP Request
    ↓
public/index.php              → Front controller (DO NOT MODIFY)
    ↓
Application::create()->run()  → Framework bootstrap
    ↓
Kernel                        → Dispatches through middleware pipeline
    ↓
Middleware Pipeline            → Global middleware (CORS, session, auth, etc.)
    ↓
Router                        → Compiled trie router matches route attributes
    ↓
Controller                    → Route handler with DI-injected dependencies
    ↓
Response                      → PSR-7 Response (HTML, JSON, redirect)
    ↓
SAPI Emitter                  → Sends HTTP response to client
```

## Dependency Injection

MonKeysLegion uses a PSR-11 compliant DI container with auto-wiring.

### Constructor Injection

```php
final class UserService
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly LoggerInterface $logger,
    ) {}
}
```

### Singleton Services

Use the `#[Singleton]` attribute for single-instance services:

```php
#[Singleton]
final class CacheService
{
    // Only one instance exists in the container
}
```

### Interface Bindings

Bind interfaces to implementations in `config/app.php`:

```php
return [
    SomeInterface::class => fn($c) => $c->get(ConcreteImpl::class),
];
```

## Service Providers

Service providers register services with the DI container:

```php
final class AppProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container): void
    {
        $container->set(PaymentGateway::class, function($c) {
            return new StripeGateway($c->get('config')->get('stripe.key'));
        });
    }
}
```

## Configuration System

Configuration uses `.mlc` files (MonKeysLegion Config — HOCON-like format):

```
# config/database.mlc
database {
    default = "mysql"

    connections {
        mysql {
            driver   = "mysql"
            host     = ${DB_HOST:localhost}
            port     = ${DB_PORT:3306}
            database = ${DB_DATABASE:monkeyslegion}
            username = ${DB_USERNAME:root}
            password = ${DB_PASSWORD:""}
        }
    }
}
```

- `${VAR:default}` — Environment variable with default
- Nested blocks with `{ }`
- Arrays with `[ ]`
- Comments with `#`

## Middleware Pipeline

MonKeysLegion implements PSR-15 middleware:

```
Request → Middleware1 → Middleware2 → Controller → Response
                                              ←
Response ← Middleware1 ← Middleware2 ←────────┘
```

Global middleware is configured in `config/middleware.mlc`:

```
middleware {
    global = [
        "timing",
        "cors",
        "session",
        "csrf",
    ]
}
```

## Package Architecture

MonKeysLegion is built on 30+ composable packages:

| Layer | Packages |
|-------|----------|
| **Core** | `core`, `di`, `env`, `contracts`, `events`, `logger`, `mlc` |
| **HTTP** | `http`, `router`, `session`, `validation`, `http-client` |
| **Data** | `database`, `entity`, `query`, `migration`, `cache`, `files` |
| **Business** | `auth`, `mail`, `queue`, `schedule` |
| **Presentation** | `template`, `i18n` |
| **Real-time** | `sockets` |
| **Dev Tools** | `cli`, `dev-server`, `devtools`, `telemetry` |
| **AI** | `apex`, `mcp` |

## Auto-Discovery

The framework auto-discovers:
- **Controllers** in `app/Controller/`
- **Route attributes** on controller methods
- **Event listeners** via `#[Listener]` attribute
- **Service providers** in `app/Providers/`
- **Entities** in `app/Entity/`

No manual registration needed for these components.
