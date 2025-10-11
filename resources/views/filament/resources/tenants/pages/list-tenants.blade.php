<x-filament-panels::page>
    @vite('resources/js/app.js')

    @php
        $branchId = session('current_branch_id');
    @endphp

    <!-- Tenant Scheduler Component -->
    <livewire:tenant-scheduler :branchId="$branchId" wire:key="tenant-scheduler-{{ $branchId }}" />

    <!-- Tenant Create Modal Component -->
    <livewire:tenant-create-modal wire:key="tenant-modal" />
</x-filament-panels::page>
