# Templates

MonKeysLegion uses a Blade-like template engine with `.ml.php` files in `resources/views/`.

## Layouts & Sections

```php
{{-- resources/views/layouts/app.ml.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'Default Title')</title>
</head>
<body>
    @yield('content')
</body>
</html>
```

```php
{{-- resources/views/home.ml.php --}}
@extends('layouts.app')

@section('title', 'Home Page')

@section('content')
<h1>Welcome!</h1>
<p>This is the home page.</p>
@endsection
```

## Variable Output

```php
{{ $variable }}          {{-- Escaped output (htmlspecialchars) --}}
{!! $html !!}            {{-- Raw HTML output --}}
```

## Conditionals

```php
@if($user->isAdmin())
    <admin-panel />
@elseif($user->isModerator())
    <moderator-panel />
@else
    <guest-panel />
@endif

@isset($optional)
    {{ $optional }}
@endisset

@empty($items)
    No items found.
@endempty

@env('production')
    <analytics />
@endenv
```

## Loops

```php
@foreach($products as $product)
    <div>{{ $product->name }} — {{ $product->formattedPrice }}</div>
@endforeach

@for($i = 0; $i < 10; $i++)
    <span>{{ $i }}</span>
@endfor

@while($row = $results->fetch())
    {{ $row['name'] }}
@endwhile
```

## Includes

```php
@include('partials.header')
@include('partials.product-card', ['product' => $product])
```

## Stacks (Push/Stack)

```php
{{-- In a child template --}}
@push('scripts')
    <script src="/app.js"></script>
@endpush

{{-- In the layout --}}
@stack('scripts')
```

## Raw PHP

```php
@php
    $total = array_sum($items);
    $tax = $total * 0.21;
@endphp
```

## Comments

```php
{{-- This comment is stripped from output --}}
```

## Directive Reference

| Directive | Purpose |
|-----------|---------|
| `@extends('layout')` | Inherit from layout |
| `@section('name') ... @endsection` | Define content block |
| `@yield('name', 'default')` | Output a section |
| `{{ $var }}` | Escaped output |
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
| `{{-- comment --}}` | Template comment |

## Rendering in Controllers

```php
$html = $this->renderer->render('products.index', [
    'title'    => 'Products',
    'products' => $products,
]);

return Response::html($html);
```

The dot notation `products.index` maps to `resources/views/products/index.ml.php`.
