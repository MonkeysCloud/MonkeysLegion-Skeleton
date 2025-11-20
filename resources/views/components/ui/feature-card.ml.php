@props([
    'title' => '',
    'description' => '',
    'icon' => '',
    'link' => null,
    'highlight' => false
])

@php
$cardClass = $highlight ? 'feature-card feature-card-highlight' : 'feature-card';
@endphp

<div class="{{ $cardClass }}">
    {{-- Slot content (icon and extras) --}}
    {{ $slot }}

    {{-- Content --}}
    <div class="feature-content">
        @if(isset($title) && $title)
        <h3 class="feature-title">{{ $title }}</h3>
        @endif

        @if(isset($description) && $description)
        <p class="feature-description">{{ $description }}</p>
        @endif

        {{-- Link --}}
        @if(isset($link) && $link)
        <a href="{{ $link }}" class="feature-link">
            Learn more
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>
        @endif
    </div>

    {{-- Decorative corner --}}
    <div class="feature-corner"></div>
</div>

<style>
    .feature-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 2.25rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s ease;
    }

    .feature-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.15);
        border-color: rgba(102, 126, 234, 0.3);
    }

    .feature-card:hover::before {
        transform: scaleX(1);
    }

    .feature-card-highlight {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
        border: none;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .feature-card-highlight::before {
        display: none;
    }

    .feature-card-highlight:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 50px rgba(102, 126, 234, 0.4);
    }

    .feature-card-highlight .feature-title {
        color: #ffffff;
    }

    .feature-card-highlight .feature-description {
        color: rgba(255,255,255,0.95);
    }

    .feature-icon {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.75rem;
        font-size: 1.875rem;
        transition: all 0.4s ease;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }

    .feature-card:hover .feature-icon {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 12px 30px rgba(102, 126, 234, 0.4);
    }

    .feature-card-highlight .feature-icon {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .feature-card-highlight:hover .feature-icon {
        background: rgba(255,255,255,0.25);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
    }

    .feature-content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .feature-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.875rem;
        color: #1a202c;
        line-height: 1.3;
    }

    .feature-description {
        font-size: 1rem;
        line-height: 1.7;
        color: #4a5568;
        margin-bottom: 1rem;
        flex: 1;
    }

    .feature-extra {
        margin-top: 1.25rem;
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

    .feature-card-highlight .feature-badge {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
    }

    .feature-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        margin-top: 1.25rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .feature-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: #667eea;
        transition: width 0.3s ease;
    }

    .feature-link:hover {
        gap: 0.75rem;
    }

    .feature-link:hover::after {
        width: calc(100% - 24px);
    }

    .feature-card-highlight .feature-link {
        color: #ffffff;
    }

    .feature-card-highlight .feature-link::after {
        background: #ffffff;
    }

    .feature-corner {
        position: absolute;
        bottom: -30px;
        right: -30px;
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, transparent 100%);
        border-radius: 50%;
        pointer-events: none;
        transition: all 0.4s ease;
    }

    .feature-card:hover .feature-corner {
        transform: scale(1.5);
        opacity: 0.7;
    }

    .feature-card-highlight .feature-corner {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
    }

    @media (max-width: 640px) {
        .feature-card {
            padding: 1.75rem;
        }

        .feature-icon {
            width: 56px;
            height: 56px;
        }

        .feature-title {
            font-size: 1.25rem;
        }
    }
</style>


<style>
    .feature-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 2.25rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s ease;
    }

    .feature-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.15);
        border-color: rgba(102, 126, 234, 0.3);
    }

    .feature-card:hover::before {
        transform: scaleX(1);
    }

    .feature-card-highlight {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
        border: none;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .feature-card-highlight::before {
        display: none;
    }

    .feature-card-highlight:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 50px rgba(102, 126, 234, 0.4);
    }

    .feature-card-highlight .feature-title {
        color: #ffffff;
    }

    .feature-card-highlight .feature-description {
        color: rgba(255,255,255,0.95);
    }

    .feature-icon {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.75rem;
        font-size: 1.875rem;
        transition: all 0.4s ease;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }

    .feature-card:hover .feature-icon {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 12px 30px rgba(102, 126, 234, 0.4);
    }

    .feature-card-highlight .feature-icon {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .feature-card-highlight:hover .feature-icon {
        background: rgba(255,255,255,0.25);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
    }

    .feature-content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .feature-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.875rem;
        color: #1a202c;
        line-height: 1.3;
    }

    .feature-description {
        font-size: 1rem;
        line-height: 1.7;
        color: #4a5568;
        margin-bottom: 1rem;
        flex: 1;
    }

    .feature-extra {
        margin-top: 1.25rem;
    }

    .feature-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        margin-top: 1.25rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .feature-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: #667eea;
        transition: width 0.3s ease;
    }

    .feature-link:hover {
        gap: 0.75rem;
    }

    .feature-link:hover::after {
        width: calc(100% - 24px);
    }

    .feature-card-highlight .feature-link {
        color: #ffffff;
    }

    .feature-card-highlight .feature-link::after {
        background: #ffffff;
    }

    .feature-corner {
        position: absolute;
        bottom: -30px;
        right: -30px;
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, transparent 100%);
        border-radius: 50%;
        pointer-events: none;
        transition: all 0.4s ease;
    }

    .feature-card:hover .feature-corner {
        transform: scale(1.5);
        opacity: 0.7;
    }

    .feature-card-highlight .feature-corner {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
    }

    @media (max-width: 640px) {
        .feature-card {
            padding: 1.75rem;
        }

        .feature-icon {
            width: 56px;
            height: 56px;
        }

        .feature-title {
            font-size: 1.25rem;
        }
    }
</style>