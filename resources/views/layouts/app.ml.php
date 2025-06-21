<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <script src="<?= asset('js/app.js') ?>"></script>
</head>

<body>
    <x-nav-bar>
    </x-nav-bar>

    <header class="page-header">
        @yield('header')
    </header>

    <main class="main-content">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <footer class="page-footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} MonkeysLegion. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>