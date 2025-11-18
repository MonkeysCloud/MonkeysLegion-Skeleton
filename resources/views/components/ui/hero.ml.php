@props([
'title' => 'Ship Production-Ready PHP in Record Time',
'subtitle' => 'The lightweight, modular framework that lets modern teams move from commit to cloud without the boilerplate.',
'primaryButton' => 'Get Started',
'primaryLink' => '/get-started',
'secondaryButton' => 'View Docs',
'secondaryLink' => '/docs',
'gradient' => true
])

<section
    {{ $attrs->merge(['class' => 'hero']) }}
    :class="['hero', 'hero-gradient' => $gradient]"
    >
    <div class="hero-container">
        <div class="hero-content">
            {{-- Title --}}
            @if($slots->has('title'))
            {{ $slots->title }}
            @else
            <h1 class="hero-title">{{ $title }}</h1>
            @endif

            {{-- Subtitle --}}
            @if($slots->has('subtitle'))
            {{ $slots->subtitle }}
            @else
            <p class="hero-subtitle">{{ $subtitle }}</p>
            @endif

            {{-- CTA Buttons --}}
            <div class="hero-actions">
                @if($slots->has('actions'))
                {{ $slots->actions }}
                @else
                <a href="{{ $primaryLink }}" class="btn btn-primary btn-lg">
                    {{ $primaryButton }}
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="{{ $secondaryLink }}" class="btn btn-outline btn-lg">
                    {{ $secondaryButton }}
                </a>
                @endif
            </div>

            {{-- Quick Install --}}
            @if($slots->has('install') || !$slots->has('actions'))
            <div class="hero-install">
                @if($slots->has('install'))
                {{ $slots->install }}
                @else
                <div class="code-block">
                    <code>composer create-project "monkeyscloud/monkeyslegion-skeleton"</code>
                    <button class="code-copy" onclick="copyToClipboard(this)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                    </button>
                </div>
                @endif
            </div>
            @endif

            {{-- Stats/Features --}}
            @if($slots->has('stats'))
            <div class="hero-stats">
                {{ $slots->stats }}
            </div>
            @endif
        </div>

        {{-- Hero Image/Visual --}}
        @if($slots->has('visual'))
        <div class="hero-visual">
            {{ $slots->visual }}
        </div>
        @endif
    </div>

    {{-- Background decoration --}}
    <div class="hero-decoration">
        <div class="hero-blob hero-blob-1"></div>
        <div class="hero-blob hero-blob-2"></div>
        <div class="hero-blob hero-blob-3"></div>
    </div>
</section>

<style>
    .hero {
        position: relative;
        padding: 6rem 0;
        overflow: hidden;
        background: #ffffff;
    }

    .hero-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
    }

    .hero-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
        position: relative;
        z-index: 10;
    }

    .hero-content {
        max-width: 700px;
        margin: 0 auto;
        text-align: center;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #ffffff 0%, #e6e9ff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .hero-subtitle {
        font-size: 1.25rem;
        line-height: 1.8;
        margin-bottom: 2.5rem;
        color: rgba(255,255,255,0.95);
    }

    .hero-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 3rem;
    }

    .hero-install {
        margin-top: 2rem;
    }

    .code-block {
        display: inline-flex;
        align-items: center;
        gap: 1rem;
        background: rgba(0,0,0,0.2);
        backdrop-filter: blur(10px);
        padding: 1rem 1.5rem;
        border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.1);
    }

    .code-block code {
        font-family: 'Monaco', 'Courier New', monospace;
        font-size: 0.9rem;
        color: #e2e8f0;
    }

    .code-copy {
        background: rgba(255,255,255,0.1);
        border: none;
        color: #ffffff;
        padding: 0.5rem;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        transition: all 0.2s;
    }

    .code-copy:hover {
        background: rgba(255,255,255,0.2);
    }

    .hero-stats {
        display: flex;
        gap: 3rem;
        justify-content: center;
        margin-top: 3rem;
        flex-wrap: wrap;
    }

    .hero-decoration {
        position: absolute;
        inset: 0;
        overflow: hidden;
        z-index: 1;
        opacity: 0.3;
    }

    .hero-blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        animation: float 20s ease-in-out infinite;
    }

    .hero-blob-1 {
        width: 400px;
        height: 400px;
        background: rgba(255,255,255,0.1);
        top: -200px;
        left: -100px;
    }

    .hero-blob-2 {
        width: 500px;
        height: 500px;
        background: rgba(255,255,255,0.08);
        bottom: -250px;
        right: -150px;
        animation-delay: -5s;
    }

    .hero-blob-3 {
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.06);
        top: 50%;
        right: 10%;
        animation-delay: -10s;
    }

    @keyframes float {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        25% { transform: translate(20px, -20px) rotate(5deg); }
        50% { transform: translate(0, -40px) rotate(-5deg); }
        75% { transform: translate(-20px, -20px) rotate(5deg); }
    }

    .btn-lg {
        padding: 0.875rem 2rem;
        font-size: 1.125rem;
    }

    @media (max-width: 768px) {
        .hero {
            padding: 4rem 0;
        }

        .hero-title {
            font-size: 2.5rem;
        }

        .hero-subtitle {
            font-size: 1.125rem;
        }

        .hero-actions {
            flex-direction: column;
        }

        .btn-lg {
            width: 100%;
        }

        .code-block {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<script>
    function copyToClipboard(button) {
        const code = button.parentElement.querySelector('code').textContent;
        navigator.clipboard.writeText(code).then(() => {
            button.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>';
            setTimeout(() => {
                button.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>';
            }, 2000);
        });
    }
</script>