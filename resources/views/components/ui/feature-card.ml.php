@props([
'title' => '',
'description' => '',
'icon' => '',
'link' => null,
'highlight' => false
])

<div
    {{ $attrs->merge(['class' => 'feature-card']) }}
    :class="['feature-card', 'feature-card-highlight' => $highlight]"
    >
    {{-- Icon --}}
    @if($slots->has('icon'))
    <div class="feature-icon">
        {{ $slots->icon }}
    </div>
    @elseif($icon)
    <div class="feature-icon" style="@style(['background: ' . $icon => $icon])">
        {{ $icon }}
    </div>
    @endif

    {{-- Content --}}
    <div class="feature-content">
        @if($slots->has('title'))
        {{ $slots->title }}
        @else
        <h3 class="feature-title">{{ $title }}</h3>
        @endif

        @if($slots->has('description'))
        {{ $slots->description }}
        @else
        <p class="feature-description">{{ $description }}</p>
        @endif

        {{-- Extra content slot --}}
        @if($slots->has('extra'))
        <div class="feature-extra">
            {{ $slots->extra }}
        </div>
        @endif

        {{-- Link --}}
        @if($link)
        <a href="{{ $link }}" class="feature-link">
            Learn more
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>
        @endif
    </div>
</div>

<style>
    .feature-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 2rem;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .feature-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
        border-color: #cbd5e0;
    }

    .feature-card-highlight {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
        border: none;
    }

    .feature-card-highlight .feature-title {
        color: #ffffff;
    }

    .feature-card-highlight .feature-description {
        color: rgba(255,255,255,0.95);
    }

    .feature-icon {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        font-size: 1.75rem;
    }

    .feature-card-highlight .feature-icon {
        background: rgba(255,255,255,0.2);
    }

    .feature-content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .feature-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: #1a202c;
    }

    .feature-description {
        font-size: 1rem;
        line-height: 1.7;
        color: #4a5568;
        margin-bottom: 1rem;
        flex: 1;
    }

    .feature-extra {
        margin-top: 1rem;
    }

    .feature-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
        margin-top: 1rem;
        transition: gap 0.2s;
    }

    .feature-link:hover {
        gap: 0.75rem;
    }

    .feature-card-highlight .feature-link {
        color: #ffffff;
    }
</style>