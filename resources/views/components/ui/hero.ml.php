@props([
    'title' => 'Ship Production-Ready PHP in Record Time',
    'subtitle' => 'The lightweight, modular framework that lets modern teams move from commit to cloud without the boilerplate.',
    'primaryButton' => 'Get Started',
    'primaryLink' => 'https://monkeyslegion.com/docs/starter',
    'secondaryButton' => 'View Docs',
    'secondaryLink' => 'https://monkeyslegion.com/docs',
    'gradient' => true,
])

<section {{ $attrs->merge([
    'class' => 'hero' . (!isset($gradient) || $gradient ? ' hero-gradient' : ''),
    ]) }}>
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
                <a href="{{ $primaryLink }}" target="_blank" class="btn btn-primary btn-lg hero-btn-primary">
                    {{ $primaryButton }}
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="{{ $secondaryLink }}" class="btn btn-outline-hero btn-lg" target="_blank" rel="noopener noreferrer">
                    {{ $secondaryButton }}
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
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
                    <span class="code-prompt">$</span>
                    <code>composer create-project "monkeyscloud/monkeyslegion-skeleton"</code>
                    <button class="code-copy" onclick="copyInstallCommand(this)" title="Copy to clipboard">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
        <div class="hero-grid"></div>
    </div>
</section>

<style>
    .hero {
        position: relative;
        padding: 8rem 0 6rem;
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
        z-index: 2;
    }

    .hero-content {
        max-width: 800px;
        margin: 0 auto;
        text-align: center;
        animation: fadeInUp 0.8s ease-out;
    }

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

    .hero-title {
        font-size: 3.5rem;
        font-weight: 800;
        line-height: 1.15;
        margin-bottom: 1.5rem;
        letter-spacing: -0.02em;
    }

    .hero-gradient .hero-title {
        color: #ffffff;
    }

    .hero-subtitle {
        font-size: 1.35rem;
        line-height: 1.7;
        margin-bottom: 2.5rem;
        opacity: 0.95;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }

    .hero-gradient .hero-subtitle {
        color: rgba(255, 255, 255, 0.95);
    }

    .hero-actions {
        display: flex;
        gap: 1.25rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 3rem;
        animation: fadeInUp 0.8s ease-out 0.2s both;
    }

    .hero-btn-primary {
        background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%);
        color: #667eea;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .hero-gradient .hero-btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
    }

    .btn-outline-hero {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: #ffffff;
    }

    .btn-outline-hero:hover {
        background: rgba(255, 255, 255, 0.25);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-3px);
    }

    .hero-install {
        max-width: 650px;
        margin: 0 auto 3rem;
        animation: fadeInUp 0.8s ease-out 0.4s both;
    }

    .code-block {
        background: rgba(26, 32, 44, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        font-family: 'Monaco', 'Courier New', monospace;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .code-block:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        border-color: rgba(255, 255, 255, 0.2);
    }

    .code-prompt {
        color: #4ade80;
        font-weight: 600;
        flex-shrink: 0;
    }

    .code-block code {
        color: #e2e8f0;
        flex: 1;
        overflow-x: auto;
        white-space: nowrap;
    }

    .code-copy {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 6px;
        padding: 0.5rem;
        color: #e2e8f0;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .code-copy:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: scale(1.05);
    }

    .code-copy:active {
        transform: scale(0.95);
    }

    .hero-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1.5rem;
        animation: fadeInUp 0.8s ease-out 0.6s both;
    }

    .hero-decoration {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 0;
        overflow: hidden;
        pointer-events: none;
    }

    .hero-blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.5;
        animation: float 15s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% {
            transform: translate(0, 0) scale(1);
        }
        33% {
            transform: translate(30px, -30px) scale(1.1);
        }
        66% {
            transform: translate(-20px, 20px) scale(0.9);
        }
    }

    .hero-blob-1 {
        width: 500px;
        height: 500px;
        background: rgba(240, 147, 251, 0.3);
        top: -100px;
        right: -100px;
    }

    .hero-blob-2 {
        width: 400px;
        height: 400px;
        background: rgba(102, 126, 234, 0.3);
        bottom: -100px;
        left: -100px;
        animation-delay: -5s;
    }

    .hero-blob-3 {
        width: 350px;
        height: 350px;
        background: rgba(118, 75, 162, 0.2);
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        animation-delay: -10s;
    }

    .hero-grid {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image:
                linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
        background-size: 50px 50px;
        mask-image: radial-gradient(circle at center, black, transparent 80%);
    }

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

    @media (max-width: 968px) {
        .hero {
            padding: 5rem 0 4rem;
        }

        .hero-title {
            font-size: 2.5rem;
        }

        .hero-subtitle {
            font-size: 1.15rem;
        }
    }

    @media (max-width: 640px) {
        .hero {
            padding: 4rem 0 3rem;
        }

        .hero-title {
            font-size: 2rem;
        }

        .hero-subtitle {
            font-size: 1rem;
        }

        .hero-actions {
            flex-direction: column;
        }

        .btn-lg {
            width: 100%;
            justify-content: center;
        }

        .code-block {
            flex-direction: column;
            align-items: stretch;
            text-align: left;
        }

        .code-block code {
            font-size: 0.85rem;
        }
    }
</style>

<script>
    function copyInstallCommand(button) {
        const code = button.previousElementSibling;
        const text = code.textContent;

        navigator.clipboard.writeText(text).then(() => {
            // Visual feedback
            const originalHTML = button.innerHTML;
            button.innerHTML = `
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            `;
            button.style.background = 'rgba(74, 222, 128, 0.3)';

            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.style.background = '';
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy:', err);
        });
    }
</script>