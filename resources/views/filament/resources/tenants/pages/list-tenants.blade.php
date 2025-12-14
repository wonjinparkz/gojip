<x-filament-panels::page>
    @vite('resources/js/app.js')

    @php
        $branchId = session('current_branch_id');
        $waitingTenants = $this->getWaitingTenants();
    @endphp

    <style>
        .main-grid-container {
            display: grid;
            gap: 24px;
            margin-bottom: 16px;
        }

        /* 모바일: 1단 레이아웃 */
        @@media (max-width: 1023px) {
            .main-grid-container {
                grid-template-columns: 1fr !important;
            }
        }

        /* 데스크톱: 2단 고정 레이아웃 (좌측 400px, 우측 나머지) */
        @@media (min-width: 1024px) {
            .main-grid-container {
                grid-template-columns: 400px 1fr !important;
            }
        }

        /* 좌측 컬럼 고정 */
        .left-column-fixed {
            min-width: 400px;
            max-width: 400px;
        }
    </style>

    <script>
        // 미래 입주자 정보 토글 함수
        function toggleFutureTenants(roomId) {
            const card = document.getElementById('future-card-' + roomId);
            const collapsed = document.getElementById('future-collapsed-' + roomId);
            const expanded = document.getElementById('future-expanded-' + roomId);

            if (card && collapsed && expanded) {
                if (collapsed.style.display !== 'none') {
                    // 확장
                    collapsed.style.display = 'none';
                    expanded.style.display = 'flex';
                    card.style.height = '180px';
                    card.style.zIndex = '10';
                } else {
                    // 축소
                    collapsed.style.display = 'flex';
                    expanded.style.display = 'none';
                    card.style.height = '60px';
                    card.style.zIndex = '1';
                }
            }
        }

    </script>

    <!-- Main 2-Column Grid: Always visible -->
    <div class="main-grid-container"
         x-data="{}"
         @refresh-tenants.window="$wire.$refresh()">

        <!-- Left Column: 입실 대기자 목록 (Fixed) -->
        <div>
            <div style="display: flex; flex-direction: column; border-radius: 16px; background-color: #f8f8f8; border: none; box-shadow: none; height: 100%;">
                <!-- Header -->
                <div style="padding: 24px; display: flex; flex-direction: column; gap: 16px; background-color: #f8f8f8; border-radius: 16px 16px 0 0;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="font-size: 18px; font-weight: 500; line-height: 1;">입실 대기자 목록</div>
                    </div>
                    <!-- Filter Buttons -->
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
                </div>
                <!-- Content: Waiting List -->
                <div style="padding: 0;">
                    <div style="overflow-y: auto; padding: 0 16px 4px 16px; min-height: 250px; max-height: 65vh;">
                        @forelse($waitingTenants as $tenant)
                            <!-- Waiting Resident Card -->
                            <div
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
                                style="margin-top: 0; margin-bottom: 12px; padding: 16px 16px 16px 24px; background-color: white; max-height: 200px; overflow-y: auto; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: all 0.2s; cursor: pointer; {{ $selectedWaitingTenantId === $tenant->id ? 'border: 3px solid #6CE0CF;' : '' }}"
                                onmouseover="this.style.backgroundColor='rgba(64, 192, 192, 0.1)'"
                                onmouseout="this.style.backgroundColor='white'">
                                <div style="display: flex; justify-content: space-between;">
                                    <div style="padding-right: 8px;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <h3 style="font-weight: 500;">{{ $tenant->name }}</h3>
                                        </div>
                                        <div style="margin-top: 4px;">
                                            <div style="font-size: 12px; color: #4b5563;">
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <span>📅 입실일: {{ $tenant->move_in_date ? $tenant->move_in_date->format('Y.m.d') : '-' }}</span>
                                                    @if($tenant->move_in_date && $tenant->move_in_date->isToday())
                                                        <span style="display: inline-flex; padding: 2px 8px; background-color: #10b981; color: white; border-radius: 9999px; font-size: 11px; font-weight: 600;">오늘</span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <span>📅 퇴실일: {{ $tenant->indefinite_move_out ? '미정' : ($tenant->move_out_date ? $tenant->move_out_date->format('Y.m.d') : '-') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <p style="font-size: 12px; margin-top: 4px; color: #4b5563;">🏠 월세 {{ number_format($tenant->monthly_rent) }}원</p>
                                        <div style="display: flex; align-items: center; gap: 4px; margin-top: 4px;">
                                            <span style="font-size: 12px;">📞</span>
                                            <p style="font-size: 12px; color: #4b5563;">{{ $tenant->phone ?? '-' }}</p>
                                        </div>
                                        <p style="font-size: 12px; margin-top: 4px; color: #4b5563;">📝 {{ $tenant->is_short_term ? '단기 입주' : '장기 입주' }}</p>
                                    </div>
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
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
                            </div>
                        @empty
                            <div style="display: flex; align-items: center; justify-content: center; height: 200px; color: #9ca3af;">
                                <p style="font-size: 14px;">입실 대기자가 없습니다.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Dynamic Content Box -->
        <div style="min-width: 0; overflow: hidden;">
            <div style="display: flex; flex-direction: column; border-radius: 16px; background-color: #f8f8f8; border: none; box-shadow: none; height: 100%; width: 100%; max-width: 100%; overflow: hidden;">
                <!-- Tabs Navigation (Inside right box) -->
                <div style="padding: 24px 24px 16px 24px; background-color: #f8f8f8; border-radius: 16px 16px 0 0; flex-shrink: 0;">
                    <div style="display: inline-flex; background-color: #ffffff; border-radius: 999px; padding: 4px; gap: 4px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                        <button
                            wire:click="setActiveTab('rooms')"
                            style="
                                padding: 10px 24px;
                                border-radius: 999px;
                                font-weight: 500;
                                font-size: 14px;
                                border: none;
                                cursor: pointer;
                                transition: all 0.3s ease;
                                background-color: {{ $activeTab === 'rooms' ? '#000000' : 'transparent' }};
                                color: {{ $activeTab === 'rooms' ? '#ffffff' : '#6b7280' }};
                                box-shadow: {{ $activeTab === 'rooms' ? '0 2px 4px rgba(0, 0, 0, 0.15)' : 'none' }};
                            "
                            onmouseover="if ('{{ $activeTab }}' !== 'rooms') { this.style.color='#374151'; }"
                            onmouseout="if ('{{ $activeTab }}' !== 'rooms') { this.style.color='#6b7280'; }"
                        >
                            호실 현황
                        </button>
                        <button
                            wire:click="setActiveTab('schedule')"
                            style="
                                padding: 10px 24px;
                                border-radius: 999px;
                                font-weight: 500;
                                font-size: 14px;
                                border: none;
                                cursor: pointer;
                                transition: all 0.3s ease;
                                background-color: {{ $activeTab === 'schedule' ? '#000000' : 'transparent' }};
                                color: {{ $activeTab === 'schedule' ? '#ffffff' : '#6b7280' }};
                                box-shadow: {{ $activeTab === 'schedule' ? '0 2px 4px rgba(0, 0, 0, 0.15)' : 'none' }};
                            "
                            onmouseover="if ('{{ $activeTab }}' !== 'schedule') { this.style.color='#374151'; }"
                            onmouseout="if ('{{ $activeTab }}' !== 'schedule') { this.style.color='#6b7280'; }"
                        >
                            입실 관리
                        </button>
                    </div>
                </div>

                <!-- Dynamic Content Area -->
                <div style="flex: 1; min-height: 0; overflow: hidden; width: 100%; max-width: 100%;">
                    @if($activeTab === 'rooms')
                        <!-- 호실 현황 Content -->
                        <div style="height: 100%; width: 100%; display: flex; flex-direction: column; overflow: hidden;">
                            @php
                                $rooms = $this->getRooms();
                            @endphp

                            <!-- Header -->
                            <div style="padding: 0 24px 16px 24px; display: flex; flex-direction: column; gap: 16px; background-color: #f8f8f8; flex-shrink: 0;">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="font-size: 18px; font-weight: 500; line-height: 1;">호실 현황</div>
                                </div>
                                <!-- Filter Buttons -->
                                <div style="display: flex; align-items: center; gap: 4px;">
                                    <button
                                        wire:click="setRoomFilter('all')"
                                        style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; white-space: nowrap; font-weight: 500; padding: 0 12px; height: 28px; border-radius: 9999px; font-size: 12px; transition: all 0.2s; cursor: pointer; background-color: {{ $roomFilter === 'all' ? 'black' : 'white' }}; color: {{ $roomFilter === 'all' ? 'white' : '#374151' }}; border: 1px solid {{ $roomFilter === 'all' ? 'black' : '#e5e7eb' }};"
                                        @if($roomFilter !== 'all') onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='white'" @else onmouseover="this.style.backgroundColor='#374151'" onmouseout="this.style.backgroundColor='black'" @endif>
                                        전체
                                    </button>
                                    <button
                                        wire:click="setRoomFilter('occupied')"
                                        style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; white-space: nowrap; font-weight: 500; padding: 0 12px; height: 28px; border-radius: 9999px; font-size: 12px; transition: all 0.2s; cursor: pointer; background-color: {{ $roomFilter === 'occupied' ? 'black' : 'white' }}; color: {{ $roomFilter === 'occupied' ? 'white' : '#374151' }}; border: 1px solid {{ $roomFilter === 'occupied' ? 'black' : '#e5e7eb' }};"
                                        @if($roomFilter !== 'occupied') onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='white'" @else onmouseover="this.style.backgroundColor='#374151'" onmouseout="this.style.backgroundColor='black'" @endif>
                                        입실
                                    </button>
                                    <button
                                        wire:click="setRoomFilter('vacant')"
                                        style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; white-space: nowrap; font-weight: 500; padding: 0 12px; height: 28px; border-radius: 9999px; font-size: 12px; transition: all 0.2s; cursor: pointer; background-color: {{ $roomFilter === 'vacant' ? 'black' : 'white' }}; color: {{ $roomFilter === 'vacant' ? 'white' : '#374151' }}; border: 1px solid {{ $roomFilter === 'vacant' ? 'black' : '#e5e7eb' }};"
                                        @if($roomFilter !== 'vacant') onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='white'" @else onmouseover="this.style.backgroundColor='#374151'" onmouseout="this.style.backgroundColor='black'" @endif>
                                        공실
                                    </button>
                                </div>
                            </div>

                            <!-- Content: Room Cards -->
                            <div style="padding: 0; flex: 1; min-height: 0; overflow: hidden;">
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; padding: 4px 16px 32px 16px; height: 100%; overflow-y: auto; overflow-x: hidden; scroll-behavior: smooth;">

                                @forelse($rooms as $room)
                                    @php
                                        $currentTenant = $room->tenant;
                                        $isOccupied = $currentTenant !== null;
                                        $bgColor = $isOccupied ? 'white' : '#e5e7eb';
                                        $isAvailableForSelectedTenant = $this->isRoomAvailableForSelectedTenant($room->id);
                                    @endphp

                                    <!-- Room Card -->
                                    <div style="display: flex; flex-direction: column; gap: 2px;">
                                        <div
                                            @dragover.prevent="$event.currentTarget.style.borderColor='#40BCBC'; $event.currentTarget.style.borderWidth='3px'; $event.currentTarget.style.borderStyle='dashed';"
                                            @dragleave="$event.currentTarget.style.borderColor=''; $event.currentTarget.style.borderWidth=''; $event.currentTarget.style.borderStyle='';"
                                            @drop.prevent="
                                                const tenantId = $event.dataTransfer.getData('tenantId');
                                                if (tenantId) {
                                                    $event.currentTarget.style.borderColor='';
                                                    $event.currentTarget.style.borderWidth='';
                                                    $event.currentTarget.style.borderStyle='';
                                                    const moveInDate = $event.dataTransfer.getData('moveInDate');
                                                    const moveOutDate = $event.dataTransfer.getData('moveOutDate');
                                                    const indefiniteMoveOut = $event.dataTransfer.getData('indefiniteMoveOut');

                                                    // 퇴실일 미정이고 입실일이 있으면 바로 배정
                                                    if (indefiniteMoveOut === '1' && moveInDate) {
                                                        // 퇴실일이 없으면 입실일과 동일하게 설정
                                                        const finalMoveOutDate = moveOutDate || moveInDate;
                                                        $wire.assignTenantDirectly({{ $room->id }}, tenantId, moveInDate, finalMoveOutDate);
                                                    } else {
                                                        $wire.openCreateModal({{ $room->id }}, tenantId, moveInDate, moveOutDate);
                                                    }
                                                }
                                            "
                                            style="border-radius: 16px; padding: 12px; height: 180px; display: flex; flex-direction: column; transition: all 0.3s; cursor: pointer; width: 100%; position: relative; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); background-color: {{ $bgColor }}; {{ $isAvailableForSelectedTenant ? 'border: 3px solid #6CE0CF;' : '' }}">
                                            <div style="flex: none; padding-top: 8px; padding-left: 8px;">
                                                <div style="display: flex; justify-content: space-between;">
                                                    <h3 style="font-weight: bold;">{{ $room->room_number }}호</h3>
                                                </div>
                                                <p style="font-size: 14px;">{{ $room->room_type ?? '스탠다드룸' }} | {{ number_format($room->monthly_rent) }}원</p>
                                            </div>
                                            <div style="margin-top: auto; flex: none;">
                                                @if($isOccupied)
                                                    <div style="margin-top: 8px; background-color: white; border-radius: 6px; padding: 8px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                            <div
                                                                wire:click.stop="editTenant({{ $currentTenant->id }})"
                                                                style="flex-grow: 1; cursor: pointer; border-radius: 4px; padding: 4px; transition: background-color 0.2s;"
                                                                onmouseover="this.style.backgroundColor='#f3f4f6'"
                                                                onmouseout="this.style.backgroundColor='transparent'">
                                                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                                                    <div style="display: flex; align-items: center; gap: 8px;">
                                                                        <span style="font-size: 14px; font-weight: 500; color: black;">{{ $currentTenant->name }}</span>
                                                                    </div>
                                                                    <svg
                                                                        wire:click.stop="removeTenantFromRoom({{ $currentTenant->id }})"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        width="14"
                                                                        height="14"
                                                                        viewBox="0 0 24 24"
                                                                        fill="none"
                                                                        stroke="currentColor"
                                                                        stroke-width="2"
                                                                        stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        style="color: #6b7280; margin-left: 8px; transition: color 0.2s; cursor: pointer;"
                                                                        onmouseover="this.style.stroke='#ef4444'"
                                                                        onmouseout="this.style.stroke='#6b7280'">
                                                                        <circle cx="12" cy="12" r="10"></circle>
                                                                        <path d="m15 9-6 6"></path>
                                                                        <path d="m9 9 6 6"></path>
                                                                    </svg>
                                                                </div>
                                                                <div style="font-size: 12px; color: #4b5563; text-align: left;">
                                                                    <div style="margin-bottom: 2px; white-space: nowrap;">
                                                                        <span>입실일: {{ $currentTenant->move_in_date ? $currentTenant->move_in_date->format('Y.m.d') : '-' }}</span>
                                                                    </div>
                                                                    <div style="white-space: nowrap;">
                                                                        <span>퇴실일: {{ $currentTenant->indefinite_move_out ? '미정' : ($currentTenant->move_out_date ? $currentTenant->move_out_date->format('Y.m.d') : '-') }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div style="display: flex; align-items: start; justify-content: center; padding-top: 8px; height: 64px; background-color: transparent; border-radius: 6px; color: #6b7280;">
                                                        <span style="font-size: 12px; opacity: 0; transition: opacity 0.2s;">공실입니다</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @php
                                            $futureTenants = $room->futureTenants;
                                            $futureTenantsCount = $futureTenants->count();
                                        @endphp

                                        <div
                                            id="future-card-{{ $room->id }}"
                                            onclick="toggleFutureTenants({{ $room->id }})"
                                            class="group"
                                            style="
                                                border-radius: 16px;
                                                padding: 12px;
                                                height: 60px;
                                                display: flex;
                                                flex-direction: column;
                                                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                                                transition: all 0.3s ease-in-out;
                                                cursor: pointer;
                                                width: 100%;
                                                position: relative;
                                                z-index: 1;
                                                isolation: isolate;
                                                background-color: {{ $futureTenantsCount > 0 ? 'rgba(64, 192, 192, 0.1)' : '#d1d5db' }};
                                            "
                                        >
                                            @if($futureTenantsCount > 0)
                                                <!-- 미래 입주자가 있는 경우 -->
                                                <div id="future-collapsed-{{ $room->id }}" style="display: flex; flex-direction: column; justify-content: center; height: 100%;">
                                                    <div style="text-align: center; transition: opacity 0.2s; transition-delay: 0.3s; opacity: 1;">
                                                        <div class="default-text" style="color: #9ca3af; font-size: 14px; text-align: center;">
                                                            🛌 +<span style="font-weight: bold;">{{ $futureTenantsCount }}</span>
                                                        </div>
                                                        <span class="hover-text" style="color: #9ca3af; font-size: 12px; text-align: center; display: none;">
                                                            {{ $room->room_number }}호에 다음 입실자 배정하기
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- 미래 입주자 리스트 (확장) -->
                                                <div id="future-expanded-{{ $room->id }}" style="display: none; flex-direction: column; justify-content: center; height: 100%;">
                                                    <div style="padding: 8px; transition: opacity 0.2s; transition-delay: 0.3s; opacity: 1; overflow-y: auto;">
                                                        <div style="display: flex; flex-direction: column; gap: 8px;">
                                                            @foreach($futureTenants as $index => $futureTenant)
                                                                @php
                                                                    // 이전 입주자와의 간격 계산
                                                                    $tenantGapDays = null;
                                                                    if ($index === 0) {
                                                                        // 첫 번째 미래 입주자: 현재 입주자와 비교
                                                                        if ($isOccupied && $currentTenant && $currentTenant->move_out_date && $futureTenant->move_in_date) {
                                                                            $prevMoveOutDate = \Carbon\Carbon::parse($currentTenant->move_out_date);
                                                                            $currMoveInDate = \Carbon\Carbon::parse($futureTenant->move_in_date);
                                                                            $tenantGapDays = $prevMoveOutDate->copy()->addDay()->diffInDays($currMoveInDate, false);
                                                                            if ($tenantGapDays <= 0) {
                                                                                $tenantGapDays = null;
                                                                            }
                                                                        }
                                                                    } else {
                                                                        // 이후 미래 입주자: 이전 미래 입주자와 비교
                                                                        $previousTenant = $futureTenants[$index - 1];
                                                                        if ($previousTenant->move_out_date && $futureTenant->move_in_date) {
                                                                            $prevMoveOutDate = \Carbon\Carbon::parse($previousTenant->move_out_date);
                                                                            $currMoveInDate = \Carbon\Carbon::parse($futureTenant->move_in_date);
                                                                            $tenantGapDays = $prevMoveOutDate->copy()->addDay()->diffInDays($currMoveInDate, false);
                                                                            if ($tenantGapDays <= 0) {
                                                                                $tenantGapDays = null;
                                                                            }
                                                                        }
                                                                    }
                                                                @endphp

                                                                @if($tenantGapDays !== null && $tenantGapDays > 0)
                                                                    <!-- 간격 표시 (드래그앤 드롭 가능) -->
                                                                    @php
                                                                        // 빈 기간의 시작일과 종료일 계산
                                                                        if ($index === 0 && $isOccupied && $currentTenant && $currentTenant->move_out_date) {
                                                                            $gapStartDate = \Carbon\Carbon::parse($currentTenant->move_out_date)->addDay()->format('Y-m-d');
                                                                        } elseif ($index > 0) {
                                                                            $previousTenant = $futureTenants[$index - 1];
                                                                            $gapStartDate = \Carbon\Carbon::parse($previousTenant->move_out_date)->addDay()->format('Y-m-d');
                                                                        }
                                                                        $gapEndDate = \Carbon\Carbon::parse($futureTenant->move_in_date)->subDay()->format('Y-m-d');
                                                                    @endphp
                                                                    <div
                                                                        @dragover.prevent="$event.currentTarget.style.backgroundColor='#e0f2f1'"
                                                                        @dragleave="$event.currentTarget.style.backgroundColor='transparent'"
                                                                        @drop.prevent="
                                                                            const tenantId = $event.dataTransfer.getData('tenantId');
                                                                            if (tenantId) {
                                                                                $event.currentTarget.style.backgroundColor='transparent';
                                                                                const indefiniteMoveOut = $event.dataTransfer.getData('indefiniteMoveOut');
                                                                                const moveInDate = $event.dataTransfer.getData('moveInDate');

                                                                                // 퇴실일 미정이고 입실일이 있으면 바로 배정
                                                                                if (indefiniteMoveOut === '1' && moveInDate) {
                                                                                    $wire.assignTenantDirectly({{ $room->id }}, tenantId, '{{ $gapStartDate }}', '{{ $gapEndDate }}');
                                                                                } else {
                                                                                    $wire.openCreateModal({{ $room->id }}, tenantId, '{{ $gapStartDate }}', '{{ $gapEndDate }}');
                                                                                }
                                                                            }
                                                                        "
                                                                        onclick="event.stopPropagation();"
                                                                        style="text-align: center; padding: 4px 8px; font-size: 10px; color: #9ca3af; cursor: pointer; border-radius: 4px; transition: background-color 0.2s;"
                                                                        onmouseover="this.style.backgroundColor='#f3f4f6'"
                                                                        onmouseout="this.style.backgroundColor='transparent'">
                                                                        {{ $tenantGapDays }}일 뒤
                                                                    </div>
                                                                @endif

                                                                <div style="padding-bottom: 8px; {{ !$loop->last ? 'border-bottom: 1px solid #e5e7eb;' : '' }}">
                                                                    <div style="text-align: left;">
                                                                        <div style="font-size: 14px; font-weight: 500; color: black; margin-bottom: 4px;">
                                                                            {{ $futureTenant->name }}
                                                                        </div>
                                                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                                                            <div style="font-size: 12px; color: #4b5563; text-align: left;">
                                                                                <div style="margin-bottom: 2px; display: flex; align-items: center; gap: 8px;">
                                                                                    <span>입실일: {{ $futureTenant->move_in_date ? $futureTenant->move_in_date->format('Y.m.d') : '-' }}</span>
                                                                                </div>
                                                                                <div>
                                                                                    <span style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border-width: 0;">📅</span>퇴실일: {{ $futureTenant->indefinite_move_out ? '미정' : ($futureTenant->move_out_date ? $futureTenant->move_out_date->format('Y.m.d') : '-') }}
                                                                                </div>
                                                                            </div>
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #6b7280; margin-left: 8px; transition: color 0.2s; cursor: pointer;" onmouseover="this.style.stroke='#ef4444'" onmouseout="this.style.stroke='#6b7280'" onclick="event.stopPropagation();"><circle cx="12" cy="12" r="10"></circle><path d="m15 9-6 6"></path><path d="m9 9 6 6"></path></svg>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach

                                                            <!-- 다음 입주자 추가 버튼 (드래그앤 드롭 가능) -->
                                                            @php
                                                                // 마지막 미래 입주자의 퇴실일 이후 날짜 계산
                                                                $lastFutureTenant = $futureTenants->last();
                                                                $nextTenantStartDate = null;
                                                                if ($lastFutureTenant && $lastFutureTenant->move_out_date) {
                                                                    $nextTenantStartDate = \Carbon\Carbon::parse($lastFutureTenant->move_out_date)->addDay()->format('Y-m-d');
                                                                }
                                                            @endphp
                                                            <div
                                                                wire:click.stop="openFutureTenantModal({{ $room->id }})"
                                                                @dragover.prevent="$event.currentTarget.style.backgroundColor='#e0f2f1'; $event.currentTarget.style.borderColor='#2dd4bf'; $event.currentTarget.style.borderStyle='solid';"
                                                                @dragleave="$event.currentTarget.style.backgroundColor='transparent'; $event.currentTarget.style.borderColor='#d1d5db'; $event.currentTarget.style.borderStyle='dashed';"
                                                                @drop.prevent="
                                                                    const tenantId = $event.dataTransfer.getData('tenantId');
                                                                    if (tenantId) {
                                                                        $event.currentTarget.style.backgroundColor='transparent';
                                                                        $event.currentTarget.style.borderColor='#d1d5db';
                                                                        $event.currentTarget.style.borderStyle='dashed';
                                                                        const indefiniteMoveOut = $event.dataTransfer.getData('indefiniteMoveOut');
                                                                        const moveInDate = $event.dataTransfer.getData('moveInDate');

                                                                        // 퇴실일 미정이고 입실일이 있으면 바로 배정
                                                                        $wire.openFutureTenantModal({{ $room->id }}, tenantId, indefiniteMoveOut === '1' && moveInDate);
                                                                    }
                                                                "
                                                                onclick="event.stopPropagation();"
                                                                style="margin-top: 8px; padding: 12px; border: 1px dashed #d1d5db; border-radius: 8px; cursor: pointer; text-align: center; transition: all 0.2s;"
                                                                onmouseover="this.style.backgroundColor='#f9fafb'; this.style.borderColor='#2dd4bf';"
                                                                onmouseout="this.style.backgroundColor='transparent'; this.style.borderColor='#d1d5db';">
                                                                <div style="display: flex; align-items: center; justify-content: center; gap: 4px;">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #6b7280;">
                                                                        <path d="M12 5v14"></path>
                                                                        <path d="M5 12h14"></path>
                                                                    </svg>
                                                                    <span style="font-size: 12px; color: #6b7280; font-weight: 500;">다음 입주자 추가</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <!-- 미래 입주자가 없는 경우 -->
                                                <div
                                                    wire:click.stop="openFutureTenantModal({{ $room->id }})"
                                                    @dragover.prevent="$event.currentTarget.style.backgroundColor='#bfdbfe'"
                                                    @dragleave="$event.currentTarget.style.backgroundColor=''"
                                                    @drop.prevent="
                                                        const tenantId = $event.dataTransfer.getData('tenantId');
                                                        if (tenantId) {
                                                            $event.currentTarget.style.backgroundColor='';
                                                            $wire.openFutureTenantModal({{ $room->id }}, tenantId);
                                                        }
                                                    "
                                                    style="display: flex; align-items: center; justify-content: center; height: 100%; cursor: pointer;">
                                                    <div style="text-align: center; transition: opacity 0.2s; opacity: 1;">
                                                        <span style="color: #6b7280; font-size: 12px;">+</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <style>
                                            .group:hover .default-text {
                                                display: none;
                                            }
                                            .group:hover .hover-text {
                                                display: block !important;
                                            }
                                        </style>
                                    </div>
                                @empty
                                    <div style="display: flex; align-items: center; justify-content: center; grid-column: 1 / -1; height: 200px; color: #9ca3af;">
                                        <p style="font-size: 14px;">호실 정보가 없습니다.</p>
                                    </div>
                                @endforelse

                                </div>
                            </div>
                        </div>
                    @elseif($activeTab === 'schedule')
                        <!-- 입실 관리 Content (Existing Scheduler) -->
                        <div style="padding: 16px 24px 24px 24px; height: 100%; width: 100%; max-width: 100%; overflow: hidden; display: flex; flex-direction: column; box-sizing: border-box;">
                            <div style="flex: 1; min-height: 0; width: 100%; max-width: 100%; overflow: hidden;">
                                <!-- Tenant Scheduler Component -->
                                <livewire:tenant-scheduler :branchId="$branchId" wire:key="tenant-scheduler-{{ $branchId }}" />
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <!-- Tenant Create Modal Component (공통) -->
    <livewire:tenant-create-modal wire:key="tenant-modal" />

    <!-- Tenant Edit Modal Component (공통) -->
    <livewire:tenant-edit-modal wire:key="tenant-edit-modal" />

    <!-- All Tenants Modal Component -->
    <livewire:all-tenants-modal wire:key="all-tenants-modal" />

    <!-- Room Assign Modal Component -->
    <livewire:room-assign-modal wire:key="room-assign-modal" />
</x-filament-panels::page>
