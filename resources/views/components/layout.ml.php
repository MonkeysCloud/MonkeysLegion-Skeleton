<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<nav>
    <a href="/">Home</a> |
    <a href="/posts">Posts</a>
</nav>

<header>
    @yield('header')
</header>

<main>
    @yield('content')
</main>

<footer>
    &copy; <?= date('Y') ?> MonkeysLegion
</footer>
</body>
</html>