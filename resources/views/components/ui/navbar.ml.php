@props([
    'logo' => 'https://monkeyslegion.com/images/MonkeysLegion.svg',
    'transparent' => false,
    'sticky' => true,
    'currentPage' => null,
])

@php
// Build the base classes using your @class helper logic
$navbarClasses = AttributeBag::conditional([
    'navbar',
    'navbar-transparent' => $transparent,
    'navbar-sticky'      => $sticky,
]);

// Merge into the component's attribute bag
// (this keeps any extra classes/attrs passed from the parent)
$attrs = $attrs->merge([
    'class' => $navbarClasses,
]);
@endphp

<nav {{ $attrs }}>
    <div class="navbar-container">
        {{-- Logo --}}
        <div class="navbar-brand">
            <a href="/" class="navbar-logo">
                <img src="{{ $logo }}" alt="MonkeysLegion" height="40">
            </a>
        </div>

        {{-- Navigation Links --}}
        <div class="navbar-menu" id="navbar-menu">
            <a href="/" class="@class(['navbar-link', 'active' => $currentPage === 'home'])">Home</a>
            <a href="https://monkeyslegion.com/docs" class="navbar-link" target="_blank" rel="noopener noreferrer">
                Docs
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity: 0.6;">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                    <polyline points="15 3 21 3 21 9"></polyline>
                    <line x1="10" y1="14" x2="21" y2="3"></line>
                </svg>
            </a>
            <a href="https://monkeyslegion.com/articles" class="navbar-link" target="_blank" rel="noopener noreferrer">
                Articles
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity: 0.6;">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                    <polyline points="15 3 21 3 21 9"></polyline>
                    <line x1="10" y1="14" x2="21" y2="3"></line>
                </svg>
            </a>
            <a href="https://monkeyslegion.com/community" class="navbar-link" target="_blank" rel="noopener noreferrer">
                Community
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity: 0.6;">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                    <polyline points="15 3 21 3 21 9"></polyline>
                    <line x1="10" y1="14" x2="21" y2="3"></line>
                </svg>
            </a>

            {{-- Custom menu items from slot --}}
            {{ $menu ?? '' }}
        </div>

        {{-- CTA Buttons --}}
        <div class="navbar-actions">
            @auth
            <a href="/dashboard" class="btn btn-outline">Dashboard</a>
            <a href="/logout" class="btn btn-primary">Logout</a>
            @endauth

            @guest
            <a href="https://github.com/MonkeysCloud/MonkeysLegion-Skeleton" class="btn btn-outline" target="_blank" rel="noopener noreferrer">
                <svg width="20" height="20" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/>
                </svg>
                GitHub
            </a>
            <a href="https://monkeyslegion.com/docs/starter" target="_blank" class="btn btn-primary">Get Started</a>
            @endguest
        </div>

        {{-- Mobile Menu Toggle --}}
        <button class="navbar-toggle" onclick="toggleMobileMenu()" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</nav>

<style>
    .navbar {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        padding: 1rem 0;
        transition: all 0.3s ease;
    }

    .navbar-sticky {
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .navbar-transparent {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
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
        transition: opacity 0.2s;
    }

    .navbar-logo:hover {
        opacity: 0.8;
    }

    .navbar-logo img {
        display: block;
        transition: transform 0.3s ease;
    }

    .navbar-logo:hover img {
        transform: scale(1.05);
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
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.5rem 0;
    }

    .navbar-link:hover {
        color: #667eea;
    }

    .navbar-link.active {
        color: #667eea;
    }

    .navbar-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px;
    }

    .navbar-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .navbar-toggle {
        display: none;
        flex-direction: column;
        gap: 5px;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
        transition: transform 0.3s ease;
    }

    .navbar-toggle:hover {
        transform: scale(1.1);
    }

    .navbar-toggle span {
        width: 24px;
        height: 2px;
        background: #4a5568;
        transition: all 0.3s;
        border-radius: 2px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        border-radius: 8px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        font-size: 0.95rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-outline {
        border-color: #e2e8f0;
        color: #4a5568;
        background: white;
    }

    .btn-outline:hover {
        background: #f7fafc;
        border-color: #cbd5e0;
        transform: translateY(-1px);
    }

    /* Mobile Menu Styles */
    @media (max-width: 968px) {
        .navbar-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            flex-direction: column;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            gap: 0;
        }

        .navbar-menu.active {
            display: flex;
        }

        .navbar-link {
            width: 100%;
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .navbar-link:last-child {
            border-bottom: none;
        }

        .navbar-actions {
            display: none;
        }

        .navbar-toggle {
            display: flex;
        }
    }

    @media (max-width: 640px) {
        .navbar-container {
            padding: 0 1rem;
        }

        .navbar-logo-text {
            display: none;
        }
    }
</style>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('navbar-menu');
        const toggle = document.querySelector('.navbar-toggle');

        menu.classList.toggle('active');
        toggle.classList.toggle('active');
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        const navbar = document.querySelector('.navbar');
        const menu = document.getElementById('navbar-menu');
        const toggle = document.querySelector('.navbar-toggle');

        if (!navbar.contains(event.target) && menu.classList.contains('active')) {
            menu.classList.remove('active');
            toggle.classList.remove('active');
        }
    });
</script>