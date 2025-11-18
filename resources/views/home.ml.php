@extends('layouts.app')

@section('content')

{{-- Hero Section --}}
<x-ui.hero>
    <x-slot:title>
        <h1 class="hero-title">
            Ship Production-Ready PHP<br>in Record Time
        </h1>
    </x-slot:title>

    <x-slot:subtitle>
        <p class="hero-subtitle">
            The lightweight, modular framework that lets modern teams move from commit to cloud without the boilerplate.
        </p>
    </x-slot:subtitle>

    <x-slot:stats>
        <div class="stat">
            <div class="stat-value">25KB</div>
            <div class="stat-label">Core Size</div>
        </div>
        <div class="stat">
            <div class="stat-value">&lt;1ms</div>
            <div class="stat-label">Router Speed</div>
        </div>
        <div class="stat">
            <div class="stat-value">Zero</div>
            <div class="stat-label">Config Needed</div>
        </div>
    </x-slot:stats>
</x-ui.hero>

{{-- Features Section --}}
<section class="features-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Everything You Need, Nothing You Don't</h2>
            <p class="section-subtitle">
                Cut setup from days to minutes with a framework that bundles all the essentials
            </p>
        </div>

        <div class="features-grid">
            <x-ui.feature-card
                    title="Blazing-Fast Router"
                    description="Lightning-quick routing with regex support, middleware, and RESTful conventions. Handle thousands of routes without breaking a sweat."
            >
                <x-slot:icon>
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                </x-slot:icon>
                <x-slot:extra>
                    <div class="feature-badge">Production Ready</div>
                </x-slot:extra>
            </x-ui.feature-card>

            <x-ui.feature-card
                    title="Rock-Solid DI Container"
                    description="Powerful dependency injection with auto-wiring, constructor injection, and interface binding. Write testable, maintainable code."
                    highlight
            >
                <x-slot:icon>
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    </svg>
                </x-slot:icon>
            </x-ui.feature-card>

            <x-ui.feature-card
                    title="First-Class CLI"
                    description="Build powerful command-line tools with argument parsing, colored output, and interactive prompts. Automate everything."
            >
                <x-slot:icon>
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <polyline points="4 17 10 11 4 5"></polyline>
                        <line x1="12" y1="19" x2="20" y2="19"></line>
                    </svg>
                </x-slot:icon>
            </x-ui.feature-card>

            <x-ui.feature-card
                    title="Zero-Config Docker"
                    description="Production-ready Docker setup included. Nginx, PHP-FPM, and MariaDB configured for optimal performance out of the box."
            >
                <x-slot:icon>
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                </x-slot:icon>
            </x-ui.feature-card>

            <x-ui.feature-card
                    title="Built for Testing"
                    description="PHPUnit integration, test helpers, and mocking support. Write tests that actually help you ship faster."
            >
                <x-slot:icon>
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <polyline points="9 11 12 14 22 4"></polyline>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                </x-slot:icon>
            </x-ui.feature-card>

            <x-ui.feature-card
                    title="Observability Ready"
                    description="Structured logging, error tracking, and performance monitoring built in. Know what's happening in production."
            >
                <x-slot:icon>
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </x-slot:icon>
            </x-ui.feature-card>
        </div>
    </div>
</section>

{{-- Code Example Section --}}
<section class="code-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">See It In Action</h2>
            <p class="section-subtitle">Clean, expressive code that's a joy to write</p>
        </div>

        <div class="code-example-grid">
            <div class="code-example">
                <div class="code-header">
                    <span class="code-label">routes/web.php</span>
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

            <div class="code-example">
                <div class="code-header">
                    <span class="code-label">app/Controllers/UserController.php</span>
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

{{-- Articles Section --}}
<section class="articles-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Latest Articles</h2>
            <p class="section-subtitle">Learn from real-world implementation examples</p>
        </div>

        <div class="articles-grid">
            <x-ui.article-card
                    title="Managing files in MonkeysLegion with monkeyslegion-files"
                    excerpt="Uploads, storage drivers (local/S3/GCS), handy helper functions, and a small controller example you can use right away."
                    image="https://drupal.monkeyslegion.com/sites/default/files/2025-07/Screenshot%202025-07-26%20at%207.21.11%E2%80%AFPM.png"
                    link="/articles/managing-files"
                    :tags="['Blog', 'Backend', 'Files']"
                    date="July 26, 2025"
            />

            <x-ui.article-card
                    title="MonkeysLegion v1.0.0 is Live 🚀"
                    excerpt="Version 1.0.0 isn't the finish line—it's the starting gun. Feather-light PHP framework hits stable release."
                    image="https://drupal.monkeyslegion.com/sites/default/files/2025-06/core-components.png"
                    link="/articles/v1-release"
                    :tags="['Article', 'Release']"
                    date="June 15, 2025"
            />

            <x-ui.article-card
                    title="User accounts & authorisation in MonkeysLegion"
                    excerpt="From signup to querying 'my data' with request->getAttribute('user_id'). Complete authentication guide."
                    image="https://drupal.monkeyslegion.com/sites/default/files/2025-05/auth.png"
                    link="/articles/auth-guide"
                    :tags="['Blog', 'Auth', 'Backend', 'Users', 'Entity']"
                    date="May 20, 2025"
            />
        </div>

        <div class="section-cta">
            <a href="/articles" class="btn btn-outline btn-lg">View All Articles</a>
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="cta-section">
    <div class="container">
        <div class="cta-card">
            <h2 class="cta-title">Ready to Build Something Amazing?</h2>
            <p class="cta-subtitle">
                Join a growing community that's redefining developer productivity in PHP
            </p>
            <div class="cta-actions">
                <a href="/get-started" class="btn btn-primary btn-lg">
                    Get Started Now
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="https://github.com/monkeyscloud/monkeyslegion" class="btn btn-outline-white btn-lg" target="_blank">
                    <svg width="20" height="20" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/>
                    </svg>
                    Star on GitHub
                </a>
            </div>
            <p class="cta-note">Free and open source • MIT License</p>
        </div>
    </div>
</section>

@endsection

<style>
    /* Layout */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    /* Section Headers */
    .section-header {
        text-align: center;
        margin-bottom: 4rem;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #1a202c;
    }

    .section-subtitle {
        font-size: 1.25rem;
        color: #718096;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Hero Stats */
    .stat {
        text-align: center;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: rgba(255,255,255,0.8);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Features Section */
    .features-section {
        padding: 6rem 0;
        background: #f7fafc;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 2rem;
    }

    .feature-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Code Section */
    .code-section {
        padding: 6rem 0;
        background: #1a202c;
        color: #ffffff;
    }

    .code-section .section-title,
    .code-section .section-subtitle {
        color: #ffffff;
    }

    .code-example-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
        gap: 2rem;
    }

    .code-example {
        background: #2d3748;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.1);
    }

    .code-header {
        padding: 1rem 1.5rem;
        background: rgba(0,0,0,0.2);
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .code-label {
        font-family: 'Monaco', 'Courier New', monospace;
        font-size: 0.875rem;
        color: #a0aec0;
    }

    .code-content {
        padding: 1.5rem;
        overflow-x: auto;
    }

    .code-content pre {
        margin: 0;
    }

    .code-content code {
        font-family: 'Monaco', 'Courier New', monospace;
        font-size: 0.875rem;
        line-height: 1.7;
        color: #e2e8f0;
    }

    /* Articles Section */
    .articles-section {
        padding: 6rem 0;
    }

    .articles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .section-cta {
        text-align: center;
    }

    /* CTA Section */
    .cta-section {
        padding: 6rem 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .cta-card {
        text-align: center;
        max-width: 700px;
        margin: 0 auto;
    }

    .cta-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 1rem;
    }

    .cta-subtitle {
        font-size: 1.25rem;
        color: rgba(255,255,255,0.95);
        margin-bottom: 2.5rem;
    }

    .cta-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .cta-note {
        color: rgba(255,255,255,0.8);
        font-size: 0.9rem;
    }

    .btn-outline-white {
        background: transparent;
        border: 2px solid rgba(255,255,255,0.3);
        color: #ffffff;
    }

    .btn-outline-white:hover {
        background: rgba(255,255,255,0.1);
        border-color: rgba(255,255,255,0.5);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .section-title {
            font-size: 2rem;
        }

        .features-grid,
        .articles-grid {
            grid-template-columns: 1fr;
        }

        .code-example-grid {
            grid-template-columns: 1fr;
        }

        .cta-title {
            font-size: 2rem;
        }

        .cta-actions {
            flex-direction: column;
        }
    }
</style>