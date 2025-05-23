# MonkeysLegion Skeleton

**A production‑ready starter for building web apps & APIs with the MonkeysLegion framework.**

Includes:

* **PSR‑11 DI Container** with config‑first definitions
* **PSR‑7/15 HTTP stack** (Request, Response, Middleware, Emitter)
* **Attribute‑based Router v2** with auto‑discovery
* **Live OpenAPI 3.1 & Swagger UI** (`/openapi.json`, `/docs`)
* **Validation layer** (DTO binding + attribute constraints)
* **Sliding‑window Rate‑Limiter** (IP + User buckets)
* **MLView** component templating
* **CLI toolbox** (migrations, cache, key‑gen, scaffolding)
* **Entity → SQL Migration** diff generator
* **Dev‑server** with hot reload

---

## 🚀 Quick‑start

```bash
composer create-project --stability=dev \
    monkeyscloud/monkeyslegion-skeleton my-app "dev-main"
cd my-app

cp .env.example .env       # configure DB, secrets
composer install
php vendor/bin/ml key:generate

composer serve             # or php vendor/bin/dev-server
open http://127.0.0.1:8000 # your first MonkeysLegion page
```

---

## 📁 Project layout

```text
my-app/
├─ app/
│  ├─ Controller/     # HTTP controllers (auto‑scanned)
│  ├─ Dto/            # Request DTOs with validation attributes
│  └─ Entity/         # DB entities
├─ config/
│  ├─ app.php         # DI definitions (services & middleware)
│  ├─ database.php    # DSN + creds
│  └─ *.mlc           # key‑value config (CORS, cache, auth,…)
├─ public/            # Web root (index.php, assets)
├─ resources/
│  └─ views/          # MLView templates & components
├─ var/
│  ├─ cache/          # Twig, rate‑limit buckets, etc.
│  └─ migrations/     # Auto‑generated SQL
├─ vendor/            # Composer deps
├─ bin/               # Dev helpers (ml, dev‑server)
└─ README.md
```

---

## ⚙️ Routing & Controllers

### Attribute syntax v2

```php
use MonkeysLegion\Router\Attributes\Route;
use Psr\Http\Message\ResponseInterface;

final class UserController
{
    #[Route('GET', '/users', summary: 'List users', tags: ['User'])]
    public function index(): ResponseInterface { /* … */ }

    #[Route('POST', '/login', name: 'user_login', tags: ['Auth'])]
    public function login(): ResponseInterface { /* … */ }

    #[Route(['PUT','PATCH'], '/users/{id}', summary: 'Update user')]
    public function update(string $id): ResponseInterface { /* … */ }
}
```

* Controllers under `app/Controller` are auto‑registered at boot.
* Imperative routes are still possible via `$router->add()`.

### Live API docs

| Endpoint            | Description                                              |
| ------------------- | -------------------------------------------------------- |
| `GET /openapi.json` | Machine‑readable OpenAPI 3.1 spec generated from routes. |
| `GET /docs`         | Swagger UI consuming that spec.                          |

---

## 🔒 Validation Layer

```php
namespace App\Dto;

use MonkeysLegion\Validation\Attributes as Assert;

final readonly class SignupRequest
{
    public function __construct(
        #[Assert\NotBlank, Assert\Email]
        public string $email,

        #[Assert\Length(min: 8, max: 64)]
        public string $password,
    ) {}
}
```

* Middleware binds JSON + query params into DTO → auto‑validates.
* On failure returns **400** with `errors[]` array.

---

## 🚦 Rate Limiting

* **Hybrid buckets**: per‑user (`uid` attribute) or per‑IP (anonymous).
* Defaults: **200 req / 60 s**. Change in `config/app.php`.

Headers returned:

```
X-RateLimit-Limit: 200
X-RateLimit-Remaining: 123
X-RateLimit-Reset: 1716509930
```

429 responses include `Retry-After`.

---

## 🖼 MLView Templating

`resources/views/layouts/app.ml.php`:

```html
<!DOCTYPE html>
<html lang="en">
<head><title>{{ $title }}</title></head>
<body>
  {{ $slots['header']() }}
  <main>{{ $slotContent }}</main>
</body>
</html>
```

`resources/views/home.ml.php`:

```html
<x-layout title="Home">
  @slot('header')<h1>Hello World!</h1>@endslot
  <p>Welcome to MonkeysLegion.</p>
</x-layout>
```

* `{{ }}` → escaped, `{!! !!}` → raw.
* `<x-foo>` includes `views/components/foo.ml.php`.
* `@slot('name') … @endslot` for named slots.

---

## 💾 Entities & Migrations

```php
use MonkeysLegion\Entity\Attributes\Field;

class User
{
    #[Field(type: 'string', length: 255)]
    private string $email;
}
```

```bash
php vendor/bin/ml make:migration   # diff & SQL → var/migrations/
php vendor/bin/ml migrate          # apply
```

---

## 🛠 CLI Cheatsheet

```bash
php vendor/bin/ml key:generate     # 32‑byte APP_KEY
php vendor/bin/ml cache:clear
php vendor/bin/ml make:entity User
php vendor/bin/ml make:migration
php vendor/bin/ml migrate
php vendor/bin/ml rollback
php vendor/bin/ml route:list
php vendor/bin/ml openapi:export
php vendor/bin/ml openapi:export api.json
```

---

## ✅ Testing

```bash
composer test   # runs PHPUnit in tests/
```

---

## 🤝 Contributing

1. Fork 🍴
2. Create a feature branch 🌱
3. Submit a PR 🚀

Happy hacking with **MonkeysLegion**! 🎉
