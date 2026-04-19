<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Welcome — MonkeysLegion v2' }}</title>

    {{-- SEO --}}
    <meta name="description" content="MonkeysLegion v2 — The lightweight, modular PHP framework for building modern web applications.">
    <meta name="theme-color" content="#0f172a">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        /* ── Reset ────────────────────────────────────────────── */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        /* ── Design Tokens ────────────────────────────────────── */
        :root {
            --bg:          #1a2332;
            --bg-card:     rgba(30, 41, 59, 0.5);
            --bg-card-hover: rgba(51, 65, 85, 0.5);
            --border:      rgba(148, 163, 184, 0.12);
            --border-hover: rgba(148, 163, 184, 0.25);
            --text:        #f1f5f9;
            --text-muted:  #94a3b8;
            --text-dim:    #64748b;
            --accent-1:    #818cf8;
            --accent-2:    #a78bfa;
            --accent-3:    #c084fc;
            --accent-green: #34d399;
            --accent-amber: #fbbf24;
            --accent-blue:  #60a5fa;
            --font-sans:   'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            --font-mono:   'JetBrains Mono', 'Fira Code', monospace;
            --radius:      12px;
            --radius-sm:   8px;
            --radius-pill: 100px;
        }

        /* ── Base ─────────────────────────────────────────────── */
        html { scroll-behavior: smooth; }
        body {
            font-family: var(--font-sans);
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
        }

        ::selection { background: rgba(129, 140, 248, 0.3); color: #fff; }

        /* ── Background Effects ───────────────────────────────── */
        body::before {
            content: '';
            position: fixed;
            top: -40%; left: -20%;
            width: 60vw; height: 60vw;
            background: radial-gradient(circle, rgba(129, 140, 248, 0.08) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -30%; right: -10%;
            width: 50vw; height: 50vw;
            background: radial-gradient(circle, rgba(192, 132, 252, 0.06) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        /* ── Container ────────────────────────────────────────── */
        .welcome-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 820px;
            padding: 3rem 2rem 2rem;
        }

        /* ── Hero ─────────────────────────────────────────────── */
        .welcome-hero {
            text-align: center;
            padding: 2rem 0 1.5rem;
        }

        .welcome-logo {
            width: 280px;
            margin: 0 auto 1.5rem;
            animation: float 6s ease-in-out infinite;
            text-align: center;
        }
        .welcome-logo img { width: 100%; height: auto; object-fit: contain; }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .welcome-version-badge {
            display: inline-block;
            padding: 0.3rem 1.2rem;
            border-radius: var(--radius-pill);
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            background: linear-gradient(135deg, var(--accent-1), var(--accent-3));
            color: #fff;
            margin-bottom: 0.75rem;
        }

        .welcome-subtitle {
            font-size: 1.25rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
            font-weight: 400;
        }

        .welcome-divider {
            width: 120px;
            height: 2px;
            margin: 2rem auto;
            background: linear-gradient(90deg, transparent, var(--accent-1), var(--accent-3), transparent);
            border-radius: 1px;
        }

        /* ── Cards Grid ───────────────────────────────────────── */
        .welcome-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin: 1rem 0 2.5rem;
        }

        .welcome-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            text-decoration: none;
            color: var(--text);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .welcome-card:hover {
            background: var(--bg-card-hover);
            border-color: var(--border-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }
        .welcome-card:focus-visible {
            outline: 2px solid var(--accent-1);
            outline-offset: 2px;
        }

        .card-icon {
            flex-shrink: 0;
            width: 40px; height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-sm);
            background: linear-gradient(135deg, rgba(129, 140, 248, 0.15), rgba(192, 132, 252, 0.1));
        }
        .card-icon svg { width: 20px; height: 20px; color: var(--accent-2); }

        .card-body { flex: 1; min-width: 0; }
        .card-title {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0.15rem;
        }
        .card-desc {
            font-size: 0.8rem;
            color: var(--text-dim);
            line-height: 1.4;
        }

        .card-arrow {
            flex-shrink: 0;
            width: 16px; height: 16px;
            color: var(--text-dim);
            transition: transform 0.3s ease, color 0.3s ease;
        }
        .welcome-card:hover .card-arrow {
            transform: translateX(3px);
            color: var(--accent-1);
        }

        /* ── Badges ───────────────────────────────────────────── */
        .welcome-badges {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-bottom: 3rem;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 0.85rem;
            border-radius: var(--radius-pill);
            font-size: 0.78rem;
            font-weight: 500;
            border: 1px solid var(--border);
            background: var(--bg-card);
            backdrop-filter: blur(8px);
        }
        .badge-blue  { color: var(--accent-blue); border-color: rgba(96, 165, 250, 0.2); }
        .badge-purple { color: var(--accent-2); border-color: rgba(167, 139, 250, 0.2); }
        .badge-green { color: var(--accent-green); border-color: rgba(52, 211, 153, 0.2); }
        .badge-amber { color: var(--accent-amber); border-color: rgba(251, 191, 36, 0.2); }

        /* ── Showcase ─────────────────────────────────────────── */
        .welcome-showcase {
            margin-bottom: 3rem;
        }
        .showcase-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .showcase-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.9rem;
            border-radius: var(--radius-pill);
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--accent-2);
            background: rgba(167, 139, 250, 0.08);
            border: 1px solid rgba(167, 139, 250, 0.15);
            margin-bottom: 1rem;
        }
        .showcase-title {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .showcase-subtitle {
            color: var(--text-muted);
            margin-top: 0.5rem;
            font-size: 0.95rem;
        }
        .showcase-subtitle code {
            font-family: var(--font-mono);
            font-size: 0.85rem;
            color: var(--accent-1);
            background: rgba(129, 140, 248, 0.1);
            padding: 0.15rem 0.45rem;
            border-radius: 4px;
        }

        .showcase-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 0.75rem;
        }
        .showcase-item {
            padding: 1rem 1.25rem;
            border-radius: var(--radius-sm);
            background: var(--bg-card);
            border: 1px solid var(--border);
        }
        .showcase-syntax {
            font-family: var(--font-mono);
            font-size: 0.82rem;
            color: var(--accent-1);
            margin-bottom: 0.3rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .showcase-label {
            font-size: 0.78rem;
            color: var(--text-dim);
        }

        /* ── Code Window ──────────────────────────────────────── */
        .welcome-code-section {
            margin-bottom: 3rem;
        }
        .code-window {
            border-radius: var(--radius);
            overflow: hidden;
            border: 1px solid var(--border);
            background: rgba(15, 23, 42, 0.8);
        }
        .code-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.25rem;
            background: rgba(30, 41, 59, 0.6);
            border-bottom: 1px solid var(--border);
        }
        .code-dots { display: flex; gap: 6px; }
        .dot {
            width: 12px; height: 12px;
            border-radius: 50%;
        }
        .dot-red    { background: #ef4444; }
        .dot-yellow { background: #eab308; }
        .dot-green  { background: #22c55e; }
        .code-filename {
            font-family: var(--font-mono);
            font-size: 0.78rem;
            color: var(--text-dim);
        }
        .code-content {
            padding: 1.25rem 1.5rem;
            overflow-x: auto;
        }
        .code-content code {
            font-family: var(--font-mono);
            font-size: 0.82rem;
            line-height: 1.7;
            color: #cbd5e1;
        }
        .kw   { color: #c084fc; }
        .cls  { color: #67e8f9; }
        .fn   { color: #fbbf24; }
        .str  { color: #34d399; }
        .var  { color: #f472b6; }
        .attr { color: #94a3b8; }

        /* ── Footer ───────────────────────────────────────────── */
        .welcome-footer {
            text-align: center;
            padding: 2rem 0 3rem;
            border-top: 1px solid var(--border);
        }
        .welcome-footer p {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            font-size: 0.85rem;
            color: var(--text-dim);
        }
        .welcome-footer a {
            color: var(--accent-2);
            text-decoration: none;
        }
        .welcome-footer a:hover { text-decoration: underline; }
        .heart-icon { color: #ef4444; }
        .footer-hint {
            margin-top: 0.5rem;
            font-size: 0.78rem !important;
        }
        .footer-hint code {
            font-family: var(--font-mono);
            font-size: 0.75rem;
            color: var(--accent-1);
            background: rgba(129, 140, 248, 0.1);
            padding: 0.1rem 0.4rem;
            border-radius: 4px;
        }

        /* ── Responsive ───────────────────────────────────────── */
        @media (max-width: 640px) {
            .welcome-container { padding: 2rem 1.25rem; }
            .welcome-grid { grid-template-columns: 1fr; }
            .showcase-grid { grid-template-columns: repeat(2, 1fr); }
        }

        /* ── Animations ───────────────────────────────────────── */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .welcome-hero       { animation: fadeIn 0.6s ease-out; }
        .welcome-grid       { animation: fadeIn 0.6s ease-out 0.15s both; }
        .welcome-badges     { animation: fadeIn 0.6s ease-out 0.3s both; }
        .welcome-showcase   { animation: fadeIn 0.6s ease-out 0.45s both; }
        .welcome-code-section { animation: fadeIn 0.6s ease-out 0.6s both; }
        .welcome-footer     { animation: fadeIn 0.6s ease-out 0.75s both; }

        /* ── Reduced Motion ───────────────────────────────────── */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        @yield('content')
    </div>
</body>
</html>
