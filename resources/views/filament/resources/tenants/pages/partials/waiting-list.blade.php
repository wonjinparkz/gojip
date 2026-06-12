<div class="mobile-white-bg mobile-height-auto" style="display: flex; flex-direction: column; border-radius: 16px; background-color: #f8f8f8; border: none; box-shadow: none; height: {{ !empty($mobileHeightAuto) ? 'auto' : '100%' }};">
    <style>
        /* Mobile Specific Styles */
        @media (max-width: 1023px) {
            .mobile-hidden {
                display: none !important;
            }
            .mobile-grid-container {
                display: grid !important;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                padding-right: 0 !important; /* Remove default padding for grid */
            }
            .mobile-font-size {
                font-size: 11px !important;
            }
            .mobile-header-padding {
                padding: 16px 16px 0 16px !important;
                background-color: transparent !important;
            }
            .mobile-white-bg {
                background-color: white !important;
            }
            .mobile-filter-container {
                justify-content: space-between !important;
                width: 100%;
            }
            .mobile-close-btn {
                display: flex !important;
                align-items: center;
                justify-content: center;
                width: 28px;
                height: 28px;
                border-radius: 50%;
                background-color: #f3f4f6;
                color: #6b7280;
                border: none;
                cursor: pointer;
            }
            .mobile-height-auto {
                height: auto !important;
            }
            .mobile-card-padding {
                padding: 8px !important;
            }
            .mobile-card-padding > div {
                padding: 0 !important;
            }
            /* Force cards to use content height only on mobile */
            .mobile-grid-container > div[class="mobile-card-padding"] {
                height: auto !important;
                min-height: auto !important;
            }
            /* Mobile: let content flow naturally; outer sheet handles scroll */
            .waiting-list-content {
                flex: 0 0 auto !important;
                min-height: auto !important;
                overflow: visible !important;
            }
            .waiting-list-grid {
                height: auto !important;
                overflow-y: visible !important;
            }
        }
    </style>

    <!-- Header -->
    <div class="mobile-header-padding" style="padding: 24px; display: flex; flex-direction: column; gap: 16px; background-color: #f8f8f8; border-radius: 16px 16px 0 0;">
        <div class="mobile-hidden" style="display: flex; align-items: center; justify-content: space-between;">
            <div style="font-size: 18px; font-weight: 500; line-height: 1;">입실 대기자 목록</div>
        </div>
        <!-- Filter Buttons & Close Button -->
        <div class="mobile-filter-container" style="display: flex; align-items: center; gap: 4px;">
            <div style="display: flex; align-items: center; gap: 4px;">
                <button
                    wire:click="setWaitingFilter('all')"
                    style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; white-space: nowrap; font-weight: 500; padding: 0 12px; height: 28px; border-radius: 9999px; font-size: 12px; transition: all 0.2s; cursor: pointer; background-color: {{ $waitingFilter === 'all' ? 'black' : 'white' }}; color: {{ $waitingFilter === 'all' ? 'white' : '#374151' }}; border: 1px solid {{ $waitingFilter === 'all' ? 'black' : '#e5e7eb' }};"
                    @if($waitingFilter !== 'all') onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='white'" @else onmouseover="this.style.backgroundColor='#374151'" onmouseout="this.style.backgroundColor='black'" @endif>
                    전체
                </button>
                <button
                    wire:click="setWaitingFilter('short_term')"
                    style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; white-space: nowrap; font-weight: 500; padding: 0 12px; height: 28px; border-radius: 9999px; font-size: 12px; transition: all 0.2s; cursor: pointer; background-color: {{ $waitingFilter === 'short_term' ? 'black' : 'white' }}; color: {{ $waitingFilter === 'short_term' ? 'white' : '#374151' }}; border: 1px solid {{ $waitingFilter === 'short_term' ? 'black' : '#e5e7eb' }};"
                    @if($waitingFilter !== 'short_term') onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='white'" @else onmouseover="this.style.backgroundColor='#374151'" onmouseout="this.style.backgroundColor='black'" @endif>
                    단기
                </button>
                <button
                    wire:click="setWaitingFilter('long_term')"
                    style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; white-space: nowrap; font-weight: 500; padding: 0 12px; height: 28px; border-radius: 9999px; font-size: 12px; transition: all 0.2s; cursor: pointer; background-color: {{ $waitingFilter === 'long_term' ? 'black' : 'white' }}; color: {{ $waitingFilter === 'long_term' ? 'white' : '#374151' }}; border: 1px solid {{ $waitingFilter === 'long_term' ? 'black' : '#e5e7eb' }};"
                    @if($waitingFilter !== 'long_term') onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='white'" @else onmouseover="this.style.backgroundColor='#374151'" onmouseout="this.style.backgroundColor='black'" @endif>
                    중장기
                </button>
            </div>
            
            <!-- Mobile Close Button -->
            <button
                class="mobile-close-btn"
                style="display: none;"
                @click="showMobileWaitingList = false">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>
    <!-- Content: Waiting List -->
    <div class="waiting-list-content" style="padding: 0; flex: 1; min-height: 0; overflow: hidden;">
        <div class="mobile-grid-container waiting-list-grid" style="overflow-y: auto; padding: 0 16px 4px 16px; height: 100%;">
            @forelse($waitingTenants as $tenant)
                <!-- Waiting Resident Card -->
                <div
                    class="mobile-card-padding"
                    wire:click="selectWaitingTenant({{ $tenant->id }})"
                    draggable="true"
                    data-tenant-id="{{ $tenant->id }}"
                    data-tenant-name="{{ $tenant->name }}"
                    @dragstart="
                        $event.dataTransfer.effectAllowed = 'copy';
                        $event.dataTransfer.setData('tenantId', '{{ $tenant->id }}');
                        $event.dataTransfer.setData('tenantName', '{{ $tenant->name }}');
                        $event.dataTransfer.setData('moveInDate', '{{ $tenant->move_in_date?->format('Y-m-d') ?? '' }}');
                        $event.dataTransfer.setData('moveOutDate', '{{ $tenant->move_out_date?->format('Y-m-d') ?? '' }}');
                        $event.dataTransfer.setData('isShortTerm', '{{ $tenant->is_short_term ? '1' : '0' }}');
                        $event.dataTransfer.setData('shortTermMonthlyRent', '{{ $tenant->short_term_monthly_rent ?? '' }}');
                        $event.dataTransfer.setData('shortTermDeposit', '{{ $tenant->short_term_deposit ?? '' }}');
                        $event.dataTransfer.setData('indefiniteMoveOut', '{{ $tenant->indefinite_move_out ? '1' : '0' }}');
                        $event.target.style.opacity = '0.5';
                    "
                    @dragend="$event.target.style.opacity = '1'"
                    @click="
                        if (window.matchMedia('(max-width: 1023px)').matches) {
                            $dispatch('close-mobile-waiting-list');
                        }
                    "
                    style="position: relative; margin-top: 0; margin-bottom: 12px; padding: 14px; background-color: white; border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: all 0.2s; cursor: pointer; {{ $selectedWaitingTenantId === $tenant->id ? 'border: 3px solid #6CE0CF;' : '' }}"
                    onmouseover="this.style.backgroundColor='rgba(64, 192, 192, 0.1)'"
                    onmouseout="this.style.backgroundColor='white'">
                    <div style="display: flex; flex-direction: column; gap: 4px; padding: 4px 8px;">
                        <!-- Tenant Name -->
                        <h3 style="font-weight: 700; font-size: 18px; margin: 0; color: black;">{{ $tenant->name }}</h3>
                        
                        <!-- Move-in Date with Arrow -->
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span style="color: #10b981; font-size: 14px; font-weight: 600;">→</span>
                            <span style="font-size: 13px; color: #000000; font-weight: 600;">
                                {{ $tenant->move_in_date ? $tenant->move_in_date->format('Y.m.d') : '-' }}
                            </span>
                            @if($tenant->move_in_date && $tenant->move_in_date->isToday())
                                <span style="display: inline-flex; padding: 2px 8px; background-color: #10b981; color: white; border-radius: 9999px; font-size: 11px; font-weight: 600;">오늘</span>
                            @endif
                        </div>
                        
                        <!-- Move-out Date with Arrow -->
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span style="color: #ef4444; font-size: 14px; font-weight: 600;">←</span>
                            <span style="font-size: 13px; color: #000000; font-weight: 600;">
                                {{ $tenant->indefinite_move_out ? '미정' : ($tenant->move_out_date ? $tenant->move_out_date->format('Y.m.d') : '-') }}
                            </span>
                        </div>
                        
                        <!-- Divider -->
                        <div style="border-top: 1px solid #e5e7eb; margin: 6px 0 4px 0;"></div>
                        
                        <!-- Monthly Rent with House Icon -->
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#334655" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            <span style="font-size: 14px; color: #334655; font-weight: 600;">{{ number_format($tenant->monthly_rent) }}원</span>
                        </div>
                        
                        <!-- Hidden on Mobile (Desktop only fields) -->
                        <div class="mobile-hidden" style="display: flex; align-items: center; gap: 4px; margin-top: 4px;">
                            <span style="font-size: 12px;">📞</span>
                            <p style="font-size: 12px; color: #4b5563; margin: 0;">{{ $tenant->phone ?? '-' }}</p>
                        </div>
                        <p class="mobile-hidden" style="font-size: 12px; margin: 4px 0 0 0; color: #4b5563;">📝 {{ $tenant->is_short_term ? '단기 입주' : '장기 입주' }}</p>
                    </div>
                    
                    <!-- Hidden on Mobile (Action Buttons) - Desktop only -->
                    <div class="mobile-hidden" style="display: flex; flex-direction: column; gap: 4px; position: absolute; right: 16px; top: 16px;">
                        <button
                            wire:click.stop="editWaitingTenant({{ $tenant->id }})"
                            style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; white-space: nowrap; font-weight: 500; height: 36px; border-radius: 6px; padding: 0 12px; background: transparent; border: none; cursor: pointer; transition: all 0.2s;"
                            onmouseover="this.style.backgroundColor='transparent'; this.style.fontWeight='bold'"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.fontWeight='500'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"></path><path d="m15 5 4 4"></path></svg>
                        </button>
                        <button
                            wire:click.stop="openRoomAssignModal({{ $tenant->id }})"
                            style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; white-space: nowrap; font-weight: 500; height: 36px; border-radius: 6px; padding: 0 12px; background: transparent; border: none; cursor: pointer; transition: all 0.2s;"
                            onmouseover="this.style.backgroundColor='transparent'; this.style.fontWeight='bold'"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.fontWeight='500'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>
            @empty
                <div style="display: flex; align-items: center; justify-content: center; height: 200px; color: #9ca3af; grid-column: 1 / -1;">
                    <p style="font-size: 14px;">입실 대기자가 없습니다.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
