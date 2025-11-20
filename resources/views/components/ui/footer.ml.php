@props([
    'logo' => 'https://monkeyslegion.com/images/MonkeysLegion.svg',
    'year' => null,
])

@php
if ($year === null || $year === '') {
    $year = date('Y');
}
@endphp

<footer {{ $attrs->merge(['class' => 'footer']) }}>
    <div class="footer-container">
        <div class="footer-grid">
            {{-- Brand Section --}}
            <div class="footer-brand">
                <img src="{{ $logo }}" alt="MonkeysLegion" height="40" class="footer-logo">
                <p class="footer-tagline">
                    The lightweight, modular framework that lets modern teams move from commit to cloud without the boilerplate.
                </p>
                <div class="footer-social">
                    {{-- GitHub --}}
                    <a href="https://github.com/MonkeysCloud/MonkeysLegion-Skeleton"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="footer-social-link"
                       aria-label="GitHub">
                        <svg width="20" height="20" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38
                                     0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13
                                     -.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52
                                     .28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15
                                     -.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27
                                     1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15
                                     0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48
                                     0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8
                                     c0-4.42-3.58-8-8-8z"/>
                        </svg>
                    </a>

                    {{-- X / Twitter (MonkeysCloud org) --}}
                    <a href="https://twitter.com/MonkeysCloud"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="footer-social-link"
                       aria-label="Twitter">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775
                                     4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184
                                     a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162
                                     a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096
                                     a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827
                                     4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417
                                     9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067
                                     a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496
                                     13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </a>

                    {{-- Slack workspace --}}
                    <a href="https://monkeyslegion.slack.com"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="footer-social-link"
                       aria-label="Slack">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037
                                     c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0
                                     12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037
                                     A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027
                                     C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057
                                     19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028
                                     c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106
                                     13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128
                                     10.2 10.2 0 00.372-.292.074.074 0 01.077-.01
                                     c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01
                                     c.12.098.246.198.373.292a.077.077 0 01-.006.127
                                     12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107
                                     c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028
                                     19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054
                                     c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03z"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Framework --}}
            <div class="footer-column">
                <h3 class="footer-title">Framework</h3>
                <ul class="footer-links">
                    <li><a href="https://monkeyslegion.com/docs" target="_blank" rel="noopener noreferrer">Documentation</a></li>
                    <li><a href="https://monkeyslegion.com/docs/starter" target="_blank" rel="noopener noreferrer">Getting Started</a></li>
                    <li><a href="https://monkeyslegion.com/docs/packages/router" target="_blank" rel="noopener noreferrer">Routing</a></li>
                    <li><a href="https://monkeyslegion.com/docs/packages/template" target="_blank" rel="noopener noreferrer">Template Engine</a></li>
                    <li><a href="https://monkeyslegion.com/docs/package/cli" target="_blank" rel="noopener noreferrer">CLI Tools</a></li>
                </ul>
            </div>

            {{-- Resources --}}
            <div class="footer-column">
                <h3 class="footer-title">Resources</h3>
                <ul class="footer-links">
                    <li><a href="https://monkeyslegion.com/articles" target="_blank" rel="noopener noreferrer">Articles</a></li>
                    <li><a href="https://monkeyslegion.com/docs/starter/first-project" target="_blank" rel="noopener noreferrer">First Project Tutorial</a></li>
                    <li><a href="https://monkeyslegion.com/docs/packages" target="_blank" rel="noopener noreferrer">Package Map</a></li>
                    <li><a href="https://monkeyslegion.com/docs/deployment" target="_blank" rel="noopener noreferrer">Deployment Guide</a></li>
                    <li><a href="https://monkeyslegion.com/community" target="_blank" rel="noopener noreferrer">Community Overview</a></li>
                </ul>
            </div>

            {{-- Community --}}
            <div class="footer-column">
                <h3 class="footer-title">Community</h3>
                <ul class="footer-links">
                    <li><a href="https://github.com/MonkeysCloud/MonkeysLegion-Skeleton" target="_blank" rel="noopener noreferrer">GitHub</a></li>
                    <li><a href="https://monkeyslegion.slack.com" target="_blank" rel="noopener noreferrer">Slack</a></li>
                    <li><a href="mailto:info@monkeyslegion.com">Contact</a></li>
                </ul>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="footer-bottom">
            <p class="footer-copyright">
                &copy; {{ $year }} MonkeysLegion. Built with ❤️ by the community.
            </p>
            <div class="footer-legal">
                <a href="https://monkeyslegion.com/docs" target="_blank" rel="noopener noreferrer">Docs</a>
                <a href="https://monkeyslegion.com/community" target="_blank" rel="noopener noreferrer">Community</a>
                <a href="https://github.com/MonkeysCloud/MonkeysLegion-Skeleton/blob/main/LICENSE"
                   target="_blank"
                   rel="noopener noreferrer">
                    MIT License
                </a>
            </div>
        </div>
    </div>
</footer>

<style>
    .footer {
        background: linear-gradient(135deg, #1a202c 0%, #2d3748 50%, #1a202c 100%);
        color: #e2e8f0;
        padding: 5rem 0 2rem;
        margin-top: 6rem;
        position: relative;
        overflow: hidden;
    }

    .footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent 0%, rgba(102, 126, 234, 0.5) 50%, transparent 100%);
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
        position: relative;
        z-index: 1;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        gap: 4rem;
        margin-bottom: 3rem;
    }

    .footer-brand {
        max-width: 380px;
    }

    .footer-logo {
        margin-bottom: 1.25rem;
        filter: brightness(0) invert(1);
        transition: transform 0.3s ease;
    }

    .footer-logo:hover {
        transform: scale(1.05);
    }

    .footer-tagline {
        color: #cbd5e0;
        font-size: 0.95rem;
        line-height: 1.7;
        margin-bottom: 1.75rem;
    }

    .footer-social {
        display: flex;
        gap: 0.75rem;
    }

    .footer-social-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        background: rgba(255,255,255,0.08);
        backdrop-filter: blur(10px);
        border-radius: 10px;
        color: #e2e8f0;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .footer-social-link:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        border-color: transparent;
    }

    .footer-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1.25rem;
        color: #ffffff;
        position: relative;
        padding-bottom: 0.75rem;
    }

    .footer-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 2px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px;
    }

    .footer-links {
        list-style: none;
        padding: 0;
    }

    .footer-links li {
        margin-bottom: 0.85rem;
    }

    .footer-links a {
        color: #cbd5e0;
        text-decoration: none;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        display: inline-block;
        position: relative;
    }

    .footer-links a::before {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: width 0.3s ease;
    }

    .footer-links a:hover {
        color: #ffffff;
        transform: translateX(5px);
    }

    .footer-links a:hover::before {
        width: 100%;
    }

    .footer-bottom {
        padding-top: 2.5rem;
        border-top: 1px solid rgba(255,255,255,0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .footer-copyright {
        color: #a0aec0;
        font-size: 0.95rem;
    }

    .footer-legal {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
    }

    .footer-legal a {
        color: #a0aec0;
        text-decoration: none;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .footer-legal a::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 1px;
        background: #667eea;
        transition: width 0.3s ease;
    }

    .footer-legal a:hover {
        color: #ffffff;
    }

    .footer-legal a:hover::after {
        width: 100%;
    }

    @media (max-width: 968px) {
        .footer-grid {
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
        }

        .footer-brand {
            grid-column: 1 / -1;
            max-width: 100%;
        }
    }

    @media (max-width: 640px) {
        .footer {
            padding: 4rem 0 2rem;
        }

        .footer-grid {
            grid-template-columns: 1fr;
            gap: 2.5rem;
        }

        .footer-bottom {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }

        .footer-legal {
            flex-direction: column;
            gap: 0.75rem;
            align-items: center;
        }
    }
</style>