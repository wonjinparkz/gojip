{{-- 드롭다운 사용 예시 --}}

<x-livewire.components.dropdown align="center" width="60">
    <x-slot name="trigger">
        <button type="button"
                style="display: inline-flex;
                       align-items: center;
                       padding: 0.5rem 1rem;
                       border: 1px solid #d1d5db;
                       border-radius: 0.5rem;
                       background-color: white;
                       font-size: 0.875rem;
                       font-weight: 500;
                       color: #374151;
                       cursor: pointer;
                       transition: all 0.15s ease-in-out;"
                onmouseover="this.style.backgroundColor='#f9fafb';"
                onmouseout="this.style.backgroundColor='white';">
            액션 메뉴
            <svg style="margin-left: 0.5rem; height: 1rem; width: 1rem;"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
    </x-slot>

    <x-slot name="content">
        <x-livewire.components.dropdown-item href="#" wire:click="addSingleRoom">
            <x-slot name="icon">
                <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/>
                <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
                <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
                <path d="M10 6h4"/>
                <path d="M10 10h4"/>
                <path d="M10 14h4"/>
                <path d="M10 18h4"/>
            </x-slot>
            호실 하나씩 추가하기
        </x-livewire.components.dropdown-item>

        <x-livewire.components.dropdown-item href="#" wire:click="addMultipleRooms">
            <x-slot name="icon">
                <path d="M5 12h14"/>
                <path d="M12 5v14"/>
            </x-slot>
            호실 여러 개 추가하기
        </x-livewire.components.dropdown-item>

        <x-livewire.components.dropdown-item href="#" wire:click="addBranch">
            <x-slot name="icon">
                <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/>
                <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
                <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
                <path d="M10 6h4"/>
                <path d="M10 10h4"/>
                <path d="M10 14h4"/>
                <path d="M10 18h4"/>
            </x-slot>
            지점 추가 등록하기
        </x-livewire.components.dropdown-item>
    </x-slot>
</x-livewire.components.dropdown>