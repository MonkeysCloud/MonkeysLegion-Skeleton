# Getting Started

## Installation

Create a new MonKeysLegion project:

```bash
composer create-project monkeyscloud/monkeyslegion-skeleton my-app
cd my-app
```

Generate an application key:

```bash
php bin/ml key:generate
```

## Directory Structure

```
my-app/
├── app/
│   ├── Controller/       # HTTP controllers (auto-discovered)
│   │   └── Api/          # API controllers
│   ├── Dto/              # Request DTOs with validation
│   ├── Entity/           # Database entities
│   ├── Enum/             # PHP 8.1+ enums
│   ├── Event/            # Domain events
│   ├── Job/              # Queue jobs
│   ├── Listener/         # Event listeners
│   ├── Middleware/       # Custom PSR-15 middleware
│   ├── Policy/           # Authorization policies
│   ├── Providers/        # Service providers
│   ├── Repository/       # Data access (extends EntityRepository)
│   ├── Resource/         # API response transformers
│   └── Service/          # Business logic
├── config/               # .mlc configuration files
├── database/
│   ├── migrations/       # SQL migration files
│   ├── factories/        # Test data factories
│   └── seeders/          # Database seeders
├── resources/
│   ├── views/            # .ml.php templates
│   └── css/              # Stylesheets
├── public/               # Web root (index.php)
├── tests/                # PHPUnit tests
└── bin/                  # CLI scripts
```

## Starting the Dev Server

```bash
composer serve    # Hot-reload dev server on port 8000
# or
composer dev      # Simple dev server on port 8080
```

## Your First Controller

```php
<?php
declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Template\Renderer;

final class HomeController
{
    public function __construct(
        private readonly Renderer $renderer,
    ) {}

    #[Route(methods: 'GET', path: '/', name: 'home')]
    public function index(): Response
    {
        return Response::html(
            $this->renderer->render('home', ['title' => 'Welcome'])
        );
    }
}
```

Controllers are auto-discovered — no registration needed.

## Your First Template

Create `resources/views/home.ml.php`:

```php
@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<h1>{{ $title }}</h1>
<p>Welcome to MonKeysLegion!</p>
@endsection
```

## Configuration

Configuration lives in `config/*.mlc` files (HOCON-like syntax):

```
# config/app.mlc
app {
    name = ${APP_NAME:"My App"}
    env  = ${APP_ENV:local}
    debug = ${APP_DEBUG:true}
}
```

Environment variables are loaded from `.env`.

## Database

Configure your database in `config/database.mlc`, then run migrations:

```bash
php bin/ml migrate              # Run pending migrations
php bin/ml migrate:status       # Check migration status
php bin/ml make:migration create_users_table  # Create a migration
```

## Next Steps

- [Controllers](controllers.md) — Building web and API controllers
- [Entities](entities.md) — Database entity definitions
- [Routing](routing.md) — Route attributes and URL generation
- [Templates](templates.md) — Blade-like template engine
- [CLI Commands](cli.md) — All available CLI commands
