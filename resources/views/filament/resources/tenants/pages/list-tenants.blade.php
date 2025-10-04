<x-filament-panels::page>
    @vite('resources/js/app.js')

    @php
        $currentTab = request()->query('activeTab', 'all');
        $branchId = $currentTab === 'all' ? null : $currentTab;
        $branches = \App\Models\Branch::where('user_id', auth()->id())->orderBy('name')->get();
    @endphp

    <!-- Tabs -->
    <div style="margin-bottom: 24px;">
        <div style="border-bottom: 1px solid #e5e7eb;">
            <nav style="display: flex; gap: 24px; margin-bottom: -1px;" aria-label="Tabs">
                <a href="?activeTab=all"
                   style="border-bottom: 2px solid {{ $currentTab === 'all' ? '#2dd4bf' : 'transparent' }}; padding: 16px 4px; font-size: 14px; font-weight: 500; color: {{ $currentTab === 'all' ? '#2dd4bf' : '#6b7280' }}; text-decoration: none;">
                    전체
                </a>
                @foreach($branches as $branch)
                    <a href="?activeTab={{ $branch->id }}"
                       style="border-bottom: 2px solid {{ $currentTab == $branch->id ? '#2dd4bf' : 'transparent' }}; padding: 16px 4px; font-size: 14px; font-weight: 500; color: {{ $currentTab == $branch->id ? '#2dd4bf' : '#6b7280' }}; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        {{ $branch->name }}
                        <span style="display: inline-flex; align-items: center; border-radius: 9999px; padding: 2px 10px; font-size: 12px; font-weight: 500; background-color: #f3f4f6; color: #374151;">
                            {{ $branch->rooms()->count() }}
                        </span>
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    <!-- Tenant Scheduler Component -->
    <livewire:tenant-scheduler :branchId="$branchId" wire:key="tenant-scheduler-{{ $currentTab }}" />

    <!-- Tenant Create Modal Component -->
    <livewire:tenant-create-modal wire:key="tenant-modal" />
</x-filament-panels::page>
