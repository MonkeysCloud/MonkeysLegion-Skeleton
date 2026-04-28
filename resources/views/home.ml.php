@extends('layouts.welcome')

@section('content')

{{-- ─────────────────────────────────────────────────────────────
     MonkeysLegion v2 — Welcome Page
     This template demonstrates the ML Template Engine features:
     • @extends / @section / @yield — Layouts & sections
     • {{ $var }} — Escaped output
     • {!! $html !!} — Raw HTML output
     • @if / @else / @endif — Conditionals
     • @foreach / @endforeach — Loops
     • @env('dev') — Environment checks
     • @push / @stack — Stack-based asset management
     • <x-component> — Blade-style components
     ───────────────────────────────────────────────────────────── --}}

{{-- Hero --}}
<div class="welcome-hero">
    <div class="welcome-logo" aria-hidden="true">
        <img src="/assets/images/MonkeysLegion.svg" alt="MonkeysLegion" width="280">
    </div>

    <span class="welcome-version-badge">v2</span>
    <p class="welcome-subtitle">You're ready to build.</p>

    <div class="welcome-divider" aria-hidden="true"></div>
</div>

{{-- Quick-Start Cards --}}
<div class="welcome-grid">
    @foreach($cards as $card)
    <a href="{{ $card['url'] }}" class="welcome-card" @if($card['external'] ?? false) target="_blank" rel="noopener noreferrer" @endif>
        <div class="card-icon">{!! $card['icon'] !!}</div>
        <div class="card-body">
            <h3 class="card-title">{{ $card['title'] }}</h3>
            <p class="card-desc">{{ $card['description'] }}</p>
        </div>
        <svg class="card-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M5 12h14M12 5l7 7-7 7"/>
        </svg>
    </a>
    @endforeach
</div>

{{-- Environment Badges --}}
<div class="welcome-badges">
    <span class="badge badge-blue">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
            <ellipse cx="12" cy="5" rx="9" ry="3"/>
            <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
            <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
        </svg>
        PHP {{ $phpVersion }}
    </span>
    <span class="badge badge-purple">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
            <polygon points="12 2 2 7 12 12 22 7 12 2"/>
            <polyline points="2 17 12 22 22 17"/>
            <polyline points="2 12 12 17 22 12"/>
        </svg>
        MonkeysLegion {{ $mlVersion }}
    </span>
    @if($environment === 'dev')
    <span class="badge badge-green">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
        Development
    </span>
    @else
    <span class="badge badge-amber">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
        Production
    </span>
    @endif
</div>

{{-- Template Engine Showcase --}}
<div class="welcome-showcase">
    <div class="showcase-header">
        <span class="showcase-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                <polyline points="16 18 22 12 16 6"/>
                <polyline points="8 6 2 12 8 18"/>
            </svg>
            Template Engine
        </span>
        <h2 class="showcase-title">This page is the template</h2>
        <p class="showcase-subtitle">
            Every feature you see is powered by the ML Template Engine.
            Edit <code>resources/views/home.ml.php</code> to start building.
        </p>
    </div>

    <div class="showcase-grid">
        @foreach($features as $feature)
        <div class="showcase-item">
            <div class="showcase-syntax">{{ $feature['syntax'] }}</div>
            <div class="showcase-label">{{ $feature['label'] }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- Code Example --}}
<div class="welcome-code-section">
    <div class="code-window">
        <div class="code-header">
            <div class="code-dots">
                <span class="dot dot-red"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-green"></span>
            </div>
            <span class="code-filename">app/Controller/HomeController.php</span>
        </div>
        <pre class="code-content"><code><span class="kw">use</span> MonkeysLegion\Router\Attributes\<span class="cls">Route</span>;
<span class="kw">use</span> MonkeysLegion\Http\Message\<span class="cls">Response</span>;

<span class="kw">final class</span> <span class="cls">HomeController</span>
{
    <span class="attr">#[Route(methods: 'GET', path: '/')]</span>
    <span class="kw">public function</span> <span class="fn">index</span>(): <span class="cls">Response</span>
    {
        <span class="kw">return</span> <span class="cls">Response</span>::<span class="fn">html</span>(
            <span class="var">$this</span>-><span class="var">renderer</span>-><span class="fn">render</span>(<span class="str">'home'</span>, [
                <span class="str">'title'</span> => <span class="str">'Welcome'</span>,
            ])
        );
    }
}</code></pre>
    </div>
</div>

{{-- Footer --}}
<footer class="welcome-footer">
    <p>
        Built with
        <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14" class="heart-icon">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        by the <a href="https://monkeys.cloud" target="_blank" rel="noopener">MonkeysCloud</a> team
    </p>
    <p class="footer-hint">
        Edit <code>resources/views/home.ml.php</code> to replace this page.
    </p>
</footer>

@endsection