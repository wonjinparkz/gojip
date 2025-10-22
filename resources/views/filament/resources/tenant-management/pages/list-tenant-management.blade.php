<x-filament-panels::page>
    <!-- Tenant Management Table -->
    <div x-data @tenant-management-saved.window="$wire.$refresh()">
        {{ $this->table }}
    </div>

    <!-- Tenant Management Modal -->
    <livewire:tenant-management-modal wire:key="tenant-management-modal" />
</x-filament-panels::page>
