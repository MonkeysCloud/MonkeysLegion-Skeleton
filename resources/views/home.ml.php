@extends('layouts.app')

@section('content')

{{-- Hero Section --}}
<x-ui.hero>
    <x-slot:title>
        <h1 class="hero-title">
            Ship Production-Ready PHP<br>
            <span class="gradient-text">in Record Time</span>
        </h1>
    </x-slot:title>

    <x-slot:subtitle>
        <p class="hero-subtitle">
            The lightweight, modular framework that lets modern teams move from commit to cloud without the boilerplate. Built for speed, designed for developers.
        </p>
    </x-slot:subtitle>

    <x-slot:stats>
        <div class="stat-card">
            <div class="stat-icon">⚡</div>
            <div class="stat-value">25KB</div>
            <div class="stat-label">Core Size</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🚀</div>
            <div class="stat-value">&lt;1ms</div>
            <div class="stat-label">Router Speed</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✨</div>
            <div class="stat-value">Zero</div>
            <div class="stat-label">Config Needed</div>
        </div>
    </x-slot:stats>
</x-ui.hero>

{{-- Features Section --}}
<section class="features-section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                </svg>
                Core Features
            </span>
            <h2 class="section-title">Everything You Need,<br>Nothing You Don't</h2>
            <p class="section-subtitle">
                Cut setup from days to minutes with a framework that bundles all the essentials. Focus on building features, not configuring tools.
            </p>
        </div>

        <div class="features-grid">
            <x-ui.feature-card
                    title="Blazing-Fast Router"
                    description="Lightning-quick routing with regex support, middleware chains, and RESTful conventions. Handle thousands of routes without breaking a sweat."
            >
                <div class="feature-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                </div>
                <div class="feature-extra">
                    <div class="feature-badge">Production Ready</div>
                </div>
            </x-ui.feature-card>

            <x-ui.feature-card
                    title="Rock-Solid DI Container"
                    description="Powerful dependency injection with auto-wiring, constructor injection, and interface binding. Write testable, maintainable code that scales."
                    :highlight="true"
            >
                <div class="feature-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    </svg>
                </div>
            </x-ui.feature-card>

            <x-ui.feature-card
                    title="First-Class CLI Tools"
                    description="Build powerful command-line tools with argument parsing, colored output, progress bars, and interactive prompts. Automate everything."
            >
                <div class="feature-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <polyline points="4 17 10 11 4 5"></polyline>
                        <line x1="12" y1="19" x2="20" y2="19"></line>
                    </svg>
                </div>
            </x-ui.feature-card>

            <x-ui.feature-card
                    title="Zero-Config Docker"
                    description="Production-ready Docker setup included. Nginx, PHP-FPM, and MariaDB configured for optimal performance out of the box. Just compose up."
            >
                <div class="feature-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                </div>
            </x-ui.feature-card>

            <x-ui.feature-card
                    title="Built for Testing"
                    description="PHPUnit integration, test helpers, and mocking support. Write tests that actually help you ship faster with confidence."
            >
                <div class="feature-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <polyline points="9 11 12 14 22 4"></polyline>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                </div>
            </x-ui.feature-card>

            <x-ui.feature-card
                    title="Observability Ready"
                    description="Structured logging, error tracking, and performance monitoring built in. Know exactly what's happening in production, always."
            >
                <div class="feature-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </div>
            </x-ui.feature-card>

            <x-ui.feature-card
                    title="Modern Template Engine"
                    description="Blade-inspired templating with components, slots, and directives. Build beautiful UIs with clean, expressive syntax."
            >
                <div class="feature-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                        <polyline points="2 17 12 22 22 17"></polyline>
                        <polyline points="2 12 12 17 22 12"></polyline>
                    </svg>
                </div>
            </x-ui.feature-card>

            <x-ui.feature-card
                    title="Database Made Easy"
                    description="Eloquent-style ORM with query builder, migrations, and relationships. Work with your database the way you've always wanted."
            >
                <div class="feature-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                        <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                        <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                    </svg>
                </div>
            </x-ui.feature-card>

            <x-ui.feature-card
                    title="API Development"
                    description="RESTful API support with JSON responses, validation, rate limiting, and API versioning. Build APIs that developers love."
            >
                <div class="feature-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="2" y1="12" x2="22" y2="12"></line>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                    </svg>
                </div>
            </x-ui.feature-card>
        </div>
    </div>
</section>

{{-- Code Example Section --}}
<section class="code-section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge light">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="16 18 22 12 16 6"></polyline>
                    <polyline points="8 6 2 12 8 18"></polyline>
                </svg>
                Clean Code
            </span>
            <h2 class="section-title">See It In Action</h2>
            <p class="section-subtitle">Clean, expressive code that's a joy to write and easy to maintain. No magic, just elegant PHP.</p>
        </div>

        <div class="code-example-grid">
            <div class="code-example" data-animate="slide-up">
                <div class="code-header">
                    <div class="code-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <span class="code-label">routes/web.php</span>
                    <span class="code-lang">PHP</span>
                </div>
                <div class="code-content">
                    <pre><code class="language-php">use App\Controllers\UserController;

$router->get('/users', [UserController::class, 'index']);
$router->get('/users/{id}', [UserController::class, 'show']);
$router->post('/users', [UserController::class, 'store']);

// Group routes with middleware
$router->group(['middleware' => 'auth'], function($router) {
    $router->get('/dashboard', [DashboardController::class, 'index']);
    $router->resource('/posts', PostController::class);
});</code></pre>
                </div>
            </div>

            <div class="code-example" data-animate="slide-up" style="animation-delay: 0.1s;">
                <div class="code-header">
                    <div class="code-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <span class="code-label">app/Controllers/UserController.php</span>
                    <span class="code-lang">PHP</span>
                </div>
                <div class="code-content">
                    <pre><code class="language-php">class UserController
{
    public function __construct(
        private UserRepository $users,
        private Validator $validator
    ) {}

    public function index(): Response
    {
        $users = $this->users->all();

        return view('users.index', [
            'users' => $users
        ]);
    }
}</code></pre>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Testimonials / Stats Section --}}
<section class="stats-section">
    <div class="container">
        <div class="stats-content">
            <span class="section-badge">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Community
            </span>
            <h2 class="stats-title">Trusted by Developers Worldwide</h2>
            <p class="stats-subtitle">Join the growing community building the future of PHP development</p>

            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">100%</div>
                    <div class="stat-text">Open Source</div>
                    <div class="stat-description">MIT Licensed</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-text">Support</div>
                    <div class="stat-description">Slack community</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="cta-section">
    <div class="container">
        <div class="cta-card">
            <div class="cta-content">
                <span class="section-badge">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    Get Started
                </span>
                <h2 class="cta-title">Ready to Build Something Amazing?</h2>
                <p class="cta-subtitle">
                    Join thousands of developers who trust MonkeysLegion for their PHP projects. Get started in minutes, deploy with confidence.
                </p>
                <div class="cta-actions">
                    <a href="https://monkeyslegion.com/docs/starter" target="_blank" class="btn btn-primary btn-lg">
                        Get Started Now
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="https://github.com/MonkeysCloud/MonkeysLegion-Skeleton" class="btn btn-outline-white btn-lg" target="_blank" rel="noopener noreferrer">
                        <svg width="20" height="20" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/>
                        </svg>
                        Star on GitHub
                    </a>
                </div>
                <p class="cta-note">
                    <span class="cta-note-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Free and open source
                    </span>
                    <span class="cta-note-divider">•</span>
                    <span class="cta-note-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        MIT License
                    </span>
                    <span class="cta-note-divider">•</span>
                    <span class="cta-note-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        No vendor lock-in
                    </span>
                </p>
            </div>

            {{-- Decorative elements --}}
            <div class="cta-decoration">
                <div class="cta-blob cta-blob-1"></div>
                <div class="cta-blob cta-blob-2"></div>
            </div>
        </div>
    </div>
</section>

@endsection