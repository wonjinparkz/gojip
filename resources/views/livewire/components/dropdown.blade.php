@props([
    'align' => 'center',
    'width' => '60',
    'contentClasses' => '',
    'dropdownClasses' => ''
])

@php
$alignmentStyles = match ($align) {
    'left' => 'left: 0px;',
    'right' => 'right: 0px;',
    default => 'left: 50%; transform: translateX(-50%);',
};

$width = match ($width) {
    '48' => 'width: 12rem;',
    '60' => 'width: 15rem;',
    '80' => 'width: 20rem;',
    default => 'width: 15rem;',
};
@endphp

<div x-data="{ open: false }"
     @click.away="open = false"
     @close.stop="open = false"
     style="position: relative; display: inline-block;">

    <div @click="open = ! open" style="cursor: pointer;">
        {{ $trigger }}
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         @click="open = false"
         style="position: absolute; z-index: 50; {{ $alignmentStyles }} {{ $width }} {{ $dropdownClasses }}"
         x-cloak>

        <div style="max-height: var(--radix-dropdown-menu-content-available-height, 500px);
                    min-width: 8rem;
                    overflow-y: auto;
                    overflow-x: hidden;
                    border: 1px solid #e5e7eb;
                    background-color: white;
                    padding: 0.25rem;
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                    border-radius: 1rem;
                    outline: none;
                    {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>