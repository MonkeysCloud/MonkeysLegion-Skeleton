# Controllers

Controllers handle HTTP requests and return responses. They live in `app/Controller/` and are **auto-discovered** — no registration needed.

## Web Controllers

Web controllers return HTML responses using the template renderer:

```php
<?php
declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Template\Renderer;
use Psr\Http\Message\ServerRequestInterface;

final class ProductController
{
    public function __construct(
        private readonly Renderer $renderer,
        private readonly ProductRepository $products,
    ) {}

    #[Route(methods: 'GET', path: '/products', name: 'products.index')]
    public function index(): Response
    {
        return Response::html($this->renderer->render('products.index', [
            'title'    => 'Products',
            'products' => $this->products->findAll(),
        ]));
    }

    #[Route(methods: 'GET', path: '/products/{id:\d+}', name: 'products.show')]
    public function show(string $id): Response
    {
        return Response::html($this->renderer->render('products.show', [
            'product' => $this->products->findOrFail((int) $id),
        ]));
    }
}
```

## API Controllers

API controllers return JSON responses and typically use a route prefix:

```php
<?php
declare(strict_types=1);

namespace App\Controller\Api;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Router\Attributes\RoutePrefix;
use MonkeysLegion\Router\Attributes\Middleware;
use MonkeysLegion\Auth\Attribute\Authenticated;
use MonkeysLegion\Http\Message\Response;

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

## Request DTOs

DTOs are auto-hydrated from the request body and validated:

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

Type-hint the DTO in the controller — if validation fails, a 422 response is returned automatically.

## Route Parameters

Route parameters are injected as method arguments:

```php
#[Route(methods: 'GET', path: '/users/{id:\d+}/posts/{slug}')]
public function show(int $id, string $slug): Response
{
    // $id and $slug are automatically typed
}
```

## Dependency Injection

Controller dependencies are injected via the constructor:

```php
public function __construct(
    private readonly ProductService $service,
    private readonly ProductRepository $products,
    private readonly LoggerInterface $logger,
) {}
```

## API Resources

Transform entities for API responses:

```php
final class ProductResource
{
    public static function toArray(Product $p): array
    {
        return [
            'id'         => $p->id,
            'type'       => 'products',
            'attributes' => [
                'name'  => $p->name,
                'price' => $p->price,
            ],
        ];
    }

    public static function collection(array $products): Response
    {
        return Response::json([
            'data' => array_map(self::toArray(...), $products),
            'meta' => ['total' => count($products)],
        ]);
    }
}
```

## Response Helpers

```php
Response::html($html);                      // 200 text/html
Response::json($data, 200);                 // 200 application/json
Response::json($data, 201);                 // 201 Created
Response::noContent();                      // 204 No Content
Response::redirect('/login');               // 302 redirect
Response::redirect('/login', 301);          // 301 permanent redirect
```
