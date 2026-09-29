{{--
    resources/views/layouts/inertia-app.ml.php

    Inertia.js root HTML template.
    The InertiaResponse class generates this HTML shell automatically,
    but you can customize it here for your needs.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'MonKeysLegion' }}</title>
    @vite(['resources/js/app.tsx'])
</head>
<body>
    <div id="app" data-page="{{ $page }}"></div>
</body>
</html>
