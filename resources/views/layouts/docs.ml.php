<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - MonkeysLegion Stripe Documentation</title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <script src="<?= asset('js/app.js') ?>"></script>
</head>

<body>
    <x-nav-bar>
    </x-nav-bar>

    <header class="docs-header">
        @yield('header')
    </header>

    <div class="docs-layout">
        <aside class="docs-sidebar">
            <div class="sidebar-title">Stripe Documentation</div>
            <nav>
                <ul class="sidebar-nav">
                    <li><a href="/docs/payment-intent">PaymentIntent</a></li>
                    <li><a href="/docs/setup-intent">SetupIntent</a></li>
                    <li><a href="/docs/checkout-session">Checkout Session</a></li>
                    <li><a href="/docs/subscription">Subscription</a></li>
                    <li><a href="/docs/product">Product</a></li>
                </ul>
            </nav>
        </aside>

        <main class="docs-content">
            @yield('content')
        </main>
    </div>

    <footer class="page-footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} MonkeysLegion. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>