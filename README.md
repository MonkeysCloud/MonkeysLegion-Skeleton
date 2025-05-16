# MonkeysLegion Skeleton

A starter project for building web applications with the MonkeysLegion PHP framework.  
Includes:

- **PSR‑11 DI Container** with config-driven definitions
- **PSR‑7 HTTP Layer** (ServerRequest, Response, Emitter)
- **Attribute‑based Routing** (`#[Route]`)
- **MLView** templating engine with components & slots
- **Dev‑server** with hot‑reload
- **CLI tools** for migrations, cache, key generation, code scaffolding
- **Entity → Migration** SQL diff generator

---

## 🚀 Quickstart

1. **Create project**
   ```bash
   composer create-project \
   --stability=dev \
   monkeyscloud/monkeyslegion-skeleton \
   my-app \
   "dev-main"
   ```
   
2. **Environment**
   ```bash
   cp .env.example .env
   composer install
   php vendor/bin/ml key:generate
   ```
   
3. **Run dev server**
   ```bash
   composer serve
   # or
   php vendor/bin/dev-server
   ```
   
4. **Browse**
   Open http://127.0.0.1:8000 in your browser.

## 🔧 Configuration

All services are wired in config/app.php.

You can customize:

- Database DSN & credentials (config/database.php)
- CORS settings (.mlc files in config/)
- Template cache path, extensions
- CLI commands registered in DI

## 📁 Project Structure

```aiignore
my-app/
├── app/  
│   ├── Controller/       # HTTP controllers with #[Route]  
│   ├── Entity/           # Your Doctrine‑style entity classes  
│   └── ...  
├── config/  
│   ├── app.php           # DI definitions  
│   ├── database.php      # DB config  
│   └── *.mlc             # MonkeysLegion‑LC config files  
├── public/               # Document root (index.php + assets)  
├── resources/  
│   └── views/            # MLView templates (`.ml.php`)  
├── var/  
│   ├── cache/            # Compiled templates & cache  
│   └── migrations/       # Auto‑generated SQL diffs  
├── vendor/               # Composer dependencies  
├── bin/                  # Local executables (ml, dev-server)  
├── composer.json  
└── README.md
```

## ⚙️ Routing & Controllers
Declare routes with PHP attributes:

```php
namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Http\Message\Stream;

final class HomeController
{
    #[Route('GET', '/')]
    public function index(): Response
    {
        $html = /* use Renderer to render a view */;
        return new Response(
            Stream::createFromString($html),
            200,
            ['Content-Type' => 'text/html']
        );
    }
}
```
The DI container auto‑discovers all controllers under app/Controller.

## 🖼 MLView Templating

Place templates in resources/views/ with .ml.php extension.

### Layout + Component

resources/views/layouts/app.ml.php:

```php
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ $title }}</title>
</head>
<body>
  <?= $slots['header']() ?>
  <main><?= $slotContent ?></main>
</body>
</html>
```

resources/views/home.ml.php:
```php
<x-layout title="{{ $title }}">
  @slot('header')
    <h1>Welcome, {{ $title }}!</h1>
  @endslot

  <p>This is the home page content.</p>
</x-layout>
```

- {{ }} escapes output
- {!! !!} renders raw HTML
- <x-foo> includes views/components/foo.ml.php
- @slot('name')…@endslot defines named slots

## 💾 Entities & Migrations
Define entities in app/Entity/ using attributes:
```php
namespace App\Entity;

use MonkeysLegion\Entity\Attributes\Field;

class User
{
    #[Field(type: 'string', length: 255)]
    private string $email;

    // … getters/setters …
}
```
### Generate Migration
```bash
php vendor/bin/ml make:migration
```
This creates an SQL file in var/migrations/ reflecting schema changes.

## 🛠 CLI Commands
- php vendor/bin/ml key:generate
- php vendor/bin/ml cache:clear
- php vendor/bin/ml make:entity [Name] (scaffold & update Entity)
- php vendor/bin/ml make:migration
- php vendor/bin/ml migrate
- php vendor/bin/ml rollback

## ✅ Testing
```bash
composer test
```
Runs PHPUnit against the tests/ directory.

## 🤝 Contributing
	1.	Fork this repository
	2.	Create a feature branch
	3.	Submit a PR

Happy hacking with MonkeysLegion!
