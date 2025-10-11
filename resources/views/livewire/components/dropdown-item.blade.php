@props([
    'icon' => null,
    'href' => null,
    'method' => null
])

@php
$tag = $href ? 'a' : 'button';
$hrefAttr = $href ? "href=\"{$href}\"" : '';
@endphp

<{{ $tag }}
    {{ $attributes->merge([
        'role' => 'menuitem',
        'tabindex' => '-1',
        'data-orientation' => 'vertical'
    ]) }}
    @if($hrefAttr) {!! $hrefAttr !!} @endif
    @if($method && $method !== 'get')
        onclick="event.preventDefault(); document.getElementById('{{ md5($href) }}').submit();"
    @endif
    style="position: relative;
           display: flex;
           cursor: default;
           user-select: none;
           align-items: center;
           gap: 0.5rem;
           outline: none;
           transition: background-color 0.15s ease-in-out, color 0.15s ease-in-out;
           font-size: 0.875rem;
           line-height: 1.25rem;
           border-radius: 0.75rem;
           padding: 0.75rem;
           border: none;
           background: none;
           width: 100%;
           text-align: left;
           color: inherit;
           text-decoration: none;"
    onmouseover="this.style.backgroundColor='rgba(64, 192, 192, 0.1)'; this.style.color='#40c0c0';"
    onmouseout="this.style.backgroundColor=''; this.style.color='inherit';">

    @if($icon)
        <svg xmlns="http://www.w3.org/2000/svg"
             width="24"
             height="24"
             viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="2"
             stroke-linecap="round"
             stroke-linejoin="round"
             style="height: 1rem; width: 1rem; margin-right: 0.75rem; pointer-events: none; flex-shrink: 0;">
            {!! $icon !!}
        </svg>
    @endif

    {{ $slot }}
</{{ $tag }}>

@if($method && $method !== 'get')
    <form id="{{ md5($href) }}" action="{{ $href }}" method="POST" style="display: none;">
        @csrf
        @method($method)
    </form>
@endif