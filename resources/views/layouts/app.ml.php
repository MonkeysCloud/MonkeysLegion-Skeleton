<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'MonkeysLegion - Ship Production-Ready PHP in Record Time' }}</title>

    {{-- Meta Tags --}}
    <meta name="description" content="The lightweight, modular framework that lets modern teams move from commit to cloud without the boilerplate.">

    {{-- Styles --}}
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Additional head content --}}
    @if($slots->has('head'))
    {{ $slots->head }}
    @endif

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: #1a202c;
            background: #ffffff;
        }
    </style>
</head>
<body :class="['page', 'page-' . ($page ?? 'default')]">
{{-- Navigation --}}
<x-ui.navbar />

{{-- Page Header (if provided) --}}
@if($slots->has('header'))
<header class="page-header">
    {{ $slots->header }}
</header>
@endif

{{-- Main Content --}}
<main class="main-content">
    @yield('content')
</main>

{{-- Page Footer --}}
<x-ui.footer />

{{-- Scripts --}}
<script src="<?= asset('js/app.js') ?>"></script>

@if($slots->has('scripts'))
{{ $slots->scripts }}
@endif

@env('development')
{{-- Development tools --}}
<div style="position:fixed;bottom:10px;right:10px;background:#000;color:#0f0;padding:5px 10px;font-size:10px;border-radius:4px;z-index:9999;">
    DEV MODE
</div>
@endenv
</body>
</html>