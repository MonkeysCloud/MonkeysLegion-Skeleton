@props([
'logo' => 'https://monkeyslegion.com/images/MonkeysLegion.svg',
'transparent' => false,
'sticky' => true
])

<nav
    {{ $attrs->merge(['class' => 'navbar']) }}
    :class="[
    'navbar',
    'navbar-transparent' => $transparent,
    'navbar-sticky' => $sticky
    ]"
    >
    <div class="navbar-container">
        {{-- Logo --}}
        <div class="navbar-brand">
            <a href="/" class="navbar-logo">
                <img src="{{ $logo }}" alt="MonkeysLegion" height="40">
                <span class="navbar-logo-text">MonkeysLegion</span>
            </a>
        </div>

        {{-- Navigation Links --}}
        <div class="navbar-menu" id="navbar-menu">
            <a href="/" :class="['navbar-link', 'active' => $currentPage === 'home']">Home</a>
            <a href="/docs" :class="['navbar-link', 'active' => $currentPage === 'docs']">Docs</a>
            <a href="/articles" :class="['navbar-link', 'active' => $currentPage === 'articles']">Articles</a>
            <a href="/community" :class="['navbar-link', 'active' => $currentPage === 'community']">Community</a>

            {{-- Custom menu items from slot --}}
            @if($slots->has('menu'))
            {{ $slots->menu }}
            @endif
        </div>

        {{-- CTA Buttons --}}
        <div class="navbar-actions">
            @auth
            <a href="/dashboard" class="btn btn-outline">Dashboard</a>
            <a href="/logout" class="btn btn-primary">Logout</a>
            @endauth

            @guest
            <a href="https://github.com/monkeyscloud/monkeyslegion" class="btn btn-outline" target="_blank">
                <svg width="20" height="20" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/>
                </svg>
                GitHub
            </a>
            <a href="/get-started" class="btn btn-primary">Get Started</a>
            @endguest
        </div>

        {{-- Mobile Menu Toggle --}}
        <button class="navbar-toggle" onclick="toggleMobileMenu()">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</nav>

<style>
    .navbar {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 0;
        transition: all 0.3s ease;
    }

    .navbar-sticky {
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .navbar-transparent {
        background: transparent;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .navbar-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 2rem;
    }

    .navbar-brand {
        flex-shrink: 0;
    }

    .navbar-logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        color: #1a202c;
        font-weight: 600;
        font-size: 1.25rem;
    }

    .navbar-logo img {
        display: block;
    }

    .navbar-menu {
        display: flex;
        gap: 2rem;
        align-items: center;
    }

    .navbar-link {
        text-decoration: none;
        color: #4a5568;
        font-weight: 500;
        transition: color 0.2s;
        position: relative;
    }

    .navbar-link:hover {
        color: #2d3748;
    }

    .navbar-link.active {
        color: #3182ce;
    }

    .navbar-link.active::after {
        content: '';
        position: absolute;
        bottom: -0.5rem;
        left: 0;
        right: 0;
        height: 2px;
        background: #3182ce;
    }

    .navbar-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .navbar-toggle {
        display: none;
        flex-direction: column;
        gap: 4px;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
    }

    .navbar-toggle span {
        width: 24px;
        height: 2px;
        background: #4a5568;
        transition: all 0.3s;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1.25rem;
        border-radius: 6px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
        border: 2px solid transparent;
    }

    .btn-primary {
        background: #3182ce;
        color: white;
    }

    .btn-primary:hover {
        background: #2c5aa0;
    }

    .btn-outline {
        border-color: #cbd5e0;
        color: #4a5568;
    }

    .btn-outline:hover {
        background: #f7fafc;
        border-color: #a0aec0;
    }

    @media (max-width: 768px) {
        .navbar-menu,
        .navbar-actions {
            display: none;
        }

        .navbar-toggle {
            display: flex;
        }
    }
</style>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('navbar-menu');
        menu.classList.toggle('active');
    }
</script>