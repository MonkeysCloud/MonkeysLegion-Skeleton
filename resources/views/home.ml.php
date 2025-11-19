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
            <span class="section-badge">Features</span>
            <h2 class="section-title">Everything You Need, Nothing You Don't</h2>
            <p class="section-subtitle">
                Cut setup from days to minutes with a framework that bundles all the essentials
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
            <span class="section-badge light">Code Example</span>
            <h2 class="section-title">See It In Action</h2>
            <p class="section-subtitle">Clean, expressive code that's a joy to write and easy to maintain</p>
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
            <h2 class="stats-title">Trusted by Developers Worldwide</h2>
            <p class="stats-subtitle">Join the growing community building the future of PHP</p>

            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">10K+</div>
                    <div class="stat-text">Downloads</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-text">GitHub Stars</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100%</div>
                    <div class="stat-text">Open Source</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-text">Community Support</div>
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
                <h2 class="cta-title">Ready to Build Something Amazing?</h2>
                <p class="cta-subtitle">
                    Join a growing community that's redefining developer productivity in PHP. Get started in minutes, deploy with confidence.
                </p>
                <div class="cta-actions">
                    <a href="/get-started" class="btn btn-primary btn-lg">
                        Get Started Now
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="https://github.com/monkeyscloud/MonkeysLegion-Skeleton" class="btn btn-outline-white btn-lg" target="_blank" rel="noopener noreferrer">
                        <svg width="20" height="20" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/>
                        </svg>
                        Star on GitHub
                    </a>
                </div>
                <p class="cta-note">
                    <span class="cta-note-icon">✓</span> Free and open source
                    <span class="cta-note-divider">•</span>
                    <span class="cta-note-icon">✓</span> MIT License
                    <span class="cta-note-divider">•</span>
                    <span class="cta-note-icon">✓</span> No vendor lock-in
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

<style>
    /* Layout */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-20px);
        }
    }

    @keyframes glow {
        0%, 100% {
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
        }
        50% {
            box-shadow: 0 0 30px rgba(102, 126, 234, 0.6);
        }
    }

    [data-animate="slide-up"] {
        animation: fadeInUp 0.6s ease-out;
    }

    /* Gradient Text */
    .gradient-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Section Headers */
    .section-header {
        text-align: center;
        margin-bottom: 4rem;
    }

    .section-badge {
        display: inline-block;
        padding: 0.5rem 1.25rem;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        color: #667eea;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 1rem;
    }

    .section-badge.light {
        background: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.9);
    }

    .section-title {
        font-size: 2.75rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #1a202c;
        line-height: 1.2;
    }

    .section-subtitle {
        font-size: 1.25rem;
        color: #718096;
        max-width: 700px;
        margin: 0 auto;
        line-height: 1.7;
    }

    /* Hero Stats */
    .stat-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.15);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .stat-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2.25rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: rgba(255,255,255,0.9);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Features Section */
    .features-section {
        padding: 8rem 0;
        background: linear-gradient(180deg, #f7fafc 0%, #ffffff 100%);
        position: relative;
        overflow: hidden;
    }

    .features-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 1000px;
        height: 1000px;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.05) 0%, transparent 70%);
        pointer-events: none;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
        gap: 2rem;
        position: relative;
        z-index: 1;
    }

    .feature-badge {
        display: inline-block;
        padding: 0.35rem 0.85rem;
        background: rgba(102, 126, 234, 0.15);
        color: #667eea;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    /* Code Section */
    .code-section {
        padding: 8rem 0;
        background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
        color: #ffffff;
        position: relative;
        overflow: hidden;
    }

    .code-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .code-section .section-title,
    .code-section .section-subtitle {
        color: #ffffff;
    }

    .code-example-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(480px, 1fr));
        gap: 2rem;
        position: relative;
        z-index: 1;
    }

    .code-example {
        background: rgba(45, 55, 72, 0.8);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.1);
        transition: all 0.3s ease;
    }

    .code-example:hover {
        transform: translateY(-5px);
        border-color: rgba(102, 126, 234, 0.5);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .code-header {
        padding: 1rem 1.5rem;
        background: rgba(0,0,0,0.3);
        border-bottom: 1px solid rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .code-dots {
        display: flex;
        gap: 6px;
    }

    .code-dots span {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
    }

    .code-dots span:nth-child(1) { background: #ff5f57; }
    .code-dots span:nth-child(2) { background: #ffbd2e; }
    .code-dots span:nth-child(3) { background: #28ca42; }

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
        font-size: 0.9rem;
        line-height: 1.7;
        color: #e2e8f0;
    }

    /* Stats Section */
    .stats-section {
        padding: 6rem 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        position: relative;
        overflow: hidden;
    }

    .stats-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="60" height="60" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="1" fill="white" opacity="0.1"/></svg>');
        pointer-events: none;
    }

    .stats-content {
        text-align: center;
        position: relative;
        z-index: 1;
    }

    .stats-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: white;
    }

    .stats-subtitle {
        font-size: 1.25rem;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 3rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
        max-width: 900px;
        margin: 0 auto;
    }

    .stat-item {
        padding: 2rem;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        transition: all 0.3s ease;
    }

    .stat-item:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.15);
    }

    .stat-number {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        background: linear-gradient(135deg, #ffffff 0%, rgba(255, 255, 255, 0.8) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-text {
        font-size: 1rem;
        color: rgba(255, 255, 255, 0.9);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* CTA Section */
    .cta-section {
        padding: 8rem 0;
        background: #1a202c;
        position: relative;
        overflow: hidden;
    }

    .cta-card {
        position: relative;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 24px;
        padding: 4rem 3rem;
        overflow: hidden;
    }

    .cta-content {
        position: relative;
        z-index: 2;
        text-align: center;
        max-width: 800px;
        margin: 0 auto;
    }

    .cta-title {
        font-size: 3rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 1rem;
        line-height: 1.2;
    }

    .cta-subtitle {
        font-size: 1.35rem;
        color: rgba(255,255,255,0.95);
        margin-bottom: 2.5rem;
        line-height: 1.7;
    }

    .cta-actions {
        display: flex;
        gap: 1.25rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 2rem;
    }

    .cta-note {
        color: rgba(255,255,255,0.85);
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .cta-note-icon {
        color: #4ade80;
    }

    .cta-note-divider {
        color: rgba(255, 255, 255, 0.5);
    }

    .cta-decoration {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1;
        pointer-events: none;
    }

    .cta-blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        opacity: 0.3;
        animation: float 8s ease-in-out infinite;
    }

    .cta-blob-1 {
        width: 400px;
        height: 400px;
        background: rgba(240, 147, 251, 0.4);
        top: -100px;
        right: -100px;
    }

    .cta-blob-2 {
        width: 300px;
        height: 300px;
        background: rgba(102, 126, 234, 0.4);
        bottom: -50px;
        left: -50px;
        animation-delay: -4s;
    }

    .btn-outline-white {
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255,255,255,0.3);
        color: #ffffff;
        backdrop-filter: blur(10px);
    }

    .btn-outline-white:hover {
        background: rgba(255,255,255,0.2);
        border-color: rgba(255,255,255,0.5);
        transform: translateY(-2px);
    }

    /* Button Styles */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.75rem;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        font-size: 1rem;
    }

    .btn-lg {
        padding: 1rem 2rem;
        font-size: 1.1rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3182ce 0%, #2c5aa0 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(49, 130, 206, 0.4);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(49, 130, 206, 0.5);
    }

    /* Responsive */
    @media (max-width: 968px) {
        .section-title {
            font-size: 2.25rem;
        }

        .cta-title {
            font-size: 2.25rem;
        }

        .features-grid {
            grid-template-columns: 1fr;
        }

        .code-example-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .section-title {
            font-size: 1.875rem;
        }

        .cta-title {
            font-size: 1.875rem;
        }

        .cta-actions {
            flex-direction: column;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .cta-card {
            padding: 3rem 2rem;
        }
    }
</style>