# CLI Commands

MonKeysLegion includes a powerful CLI with code generators, migration tools, and maintenance commands. Run via `php bin/ml <command>` or `./ml <command>`.

## Available Commands

### Core

| Command | Description |
|---------|-------------|
| `ml list` | List all available commands |
| `ml about` | Show framework information |
| `ml key:generate` | Generate application key |
| `ml env:sync` | Sync .env.example with .env |

### Code Generators (make:)

| Command | Description |
|---------|-------------|
| `ml make:controller ExampleController` | Generate a controller |
| `ml make:entity Product` | Generate an entity |
| `ml make:dto CreateProductRequest` | Generate a request DTO |
| `ml make:service ProductService` | Generate a service class |
| `ml make:repository ProductRepository` | Generate a repository |
| `ml make:middleware TimingMiddleware` | Generate middleware |
| `ml make:job SendEmailJob` | Generate a queue job |
| `ml make:event UserCreated` | Generate an event class |
| `ml make:listener SendWelcomeEmail` | Generate an event listener |
| `ml make:enum OrderStatus` | Generate an enum |
| `ml make:resource ProductResource` | Generate an API resource |
| `ml make:policy PostPolicy` | Generate an authorization policy |
| `ml make:rule StrongPassword` | Generate a validation rule |
| `ml make:cast JsonCast` | Generate an entity cast |
| `ml make:factory UserFactory` | Generate a test factory |
| `ml make:seeder UsersSeeder` | Generate a database seeder |
| `ml make:test UserTest` | Generate a test class |
| `ml make:command CustomCommand` | Generate a CLI command |

### Database & Migrations

| Command | Description |
|---------|-------------|
| `ml migrate` | Run pending migrations |
| `ml migrate:status` | Show migration status |
| `ml migrate:rollback` | Rollback last batch |
| `ml migrate:refresh` | Rollback + re-run all |
| `ml migrate:fresh` | Drop all tables + re-run |
| `ml make:migration create_users_table` | Generate a migration |
| `ml db:create` | Create the database |
| `ml db:wipe` | Drop all tables |
| `ml db:monitor` | Monitor database queries |
| `ml seed` | Run database seeders |

### Routes

| Command | Description |
|---------|-------------|
| `ml route:list` | List all registered routes |
| `ml route:cache` | Cache routes for production |
| `ml route:clear` | Clear route cache |
| `ml route:js` | Export routes as TypeScript |

### Optimization

| Command | Description |
|---------|-------------|
| `ml optimize` | Run all optimizations |
| `ml config:cache` | Cache configuration |
| `ml config:clear` | Clear config cache |
| `ml container:compile` | Compile DI container |
| `ml container:clear` | Clear container cache |
| `ml view:cache` | Cache templates |
| `ml view:clear` | Clear template cache |
| `ml opcache:preload` | Generate OPcache preload |
| `ml opcache:status` | Show OPcache status |

### Application

| Command | Description |
|---------|-------------|
| `ml up` | Bring app out of maintenance mode |
| `ml down` | Put app in maintenance mode |
| `ml health:check` | Run health checks |
| `ml test` | Run tests |
| `ml tinker` | Start an interactive REPL |

### Dev Tools

| Command | Description |
|---------|-------------|
| `ml security:check` | Audit security configuration |
| `ml log:tail` | Tail application logs |
| `ml benchmark` | Run performance benchmarks |
| `ml types:generate` | Generate TypeScript types |
| `ml frontend:install` | Install frontend SPA tooling |
| `ml openapi:export` | Export OpenAPI spec |
| `ml openapi:serve` | Serve Swagger UI |
| `ml openapi:validate` | Validate OpenAPI spec |
| `ml schedule:monitor` | Show scheduled task health |

## Development Servers

```bash
composer serve    # Hot-reload dev server (port 8000)
composer dev      # Simple dev server (port 8080)
```
