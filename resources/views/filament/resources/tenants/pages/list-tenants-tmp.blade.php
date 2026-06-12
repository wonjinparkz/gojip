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

        /* GoshiwonManager Style Room Card */
        .gm-room-card {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            cursor: pointer;
            transition: box-shadow 0.2s ease;
            position: relative;
        }
        .gm-room-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
        }
        .gm-room-card.vacant-card {
            background-color: #f3f4f6;
        }
        .gm-room-card.available-highlight {
            border: 3px solid #6CE0CF;
        }

        /* Status bar at top */
        .gm-status-bar {
            height: 4px;
            width: 100%;
        }
        .gm-status-bar.occupied { background-color: #111827; }
        .gm-status-bar.vacant { background-color: #9ca3af; }
        .gm-status-bar.overdue { background-color: #ef4444; }
        .gm-status-bar.expiring { background-color: #f59e0b; }

        /* Badge styles */
        .gm-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 700;
            line-height: 1.4;
        }
        .gm-badge.occupied { background-color: #111827; color: white; }
        .gm-badge.vacant { background-color: #9ca3af; color: white; }
        .gm-badge.overdue { background-color: #ef4444; color: white; }
        .gm-badge.expiring { background-color: #f59e0b; color: white; }

        /* Info grid */
        .gm-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 8px;
            font-size: 12px;
        }
        .gm-info-grid .label {
            color: #9ca3af;
        }
        .gm-info-grid .value {
            text-align: right;
            color: #374151;
        }
        .gm-info-grid .value.bold {
            font-weight: 600;
        }
        .gm-info-grid .value.overdue {
            color: #ef4444;
            font-weight: 700;
        }

        .gm-divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 8px 0;
        }

        /* D-day badge */
        .gm-dday {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
        }
        .gm-dday.normal { background-color: #f3f4f6; color: #374151; }
        .gm-dday.warning { background-color: #fef3c7; color: #d97706; }
        .gm-dday.danger { background-color: #fee2e2; color: #ef4444; }

        /* Remove button */
        .gm-remove-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            opacity: 0;
            transition: opacity 0.2s;
            background: white;
            border: none;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
            padding: 0;
        }
        .gm-room-card:hover .gm-remove-btn {
            opacity: 1;
        }

        /* 모바일 전용 스타일 */
        @@media (max-width: 1023px) {
            .desktop-only {
                display: none !important;
            }

            @@media (max-width: 1023px) {
                .fi-layout-sidebar-toggle-btn-ctn {
                    display: flex !important;
                    align-items: center;
                    gap: 12px;
                }

                .fi-header-heading {
                    display: none !important;
                }

                .fi-breadcrumbs {
                    display: none !important;
                }

                .mobile-header-title {
                    font-size: 18px;
                    font-weight: 700;
                    color: #111827;
                    margin: 0;
                    white-space: nowrap;
                }

                .fi-page-header-main-ctn {
                    padding-top: 8px !important;
                    margin-top: 0 !important;
                }

                .mobile-white-bg.mobile-no-padding {
                    padding: 16px 0 12px 0 !important;
                }

                .mobile-white-bg.mobile-no-padding > div {
                    padding: 3px !important;
                    gap: 3px !important;
                }

                .mobile-white-bg.mobile-no-padding button {
                    padding: 6px 16px !important;
                    font-size: 13px !important;
                }

                .main-grid-container {
                    margin-top: -16px !important;
                    padding-top: 0 !important;
                }

                .mobile-white-bg.mobile-no-padding + div button,
                div[style*="gap: 4px"] > button {
                    height: 36px !important;
                    padding: 0 16px !important;
                    font-size: 14px !important;
                }

                .mobile-white-bg.mobile-no-padding > div[style*="justify-content: space-between"] {
                    display: none !important;
                }

                .mobile-white-bg.mobile-no-padding[style*="padding: 0 24px"] {
                    padding-left: 0 !important;
                    padding-right: 0 !important;
                }
            }

            .footer-toggle-button {
                display: none !important;
            }

            .room-grid-container {
                grid-template-columns: 1fr 1fr !important;
                gap: 12px !important;
                padding-left: 8px !important;
                padding-right: 8px !important;
            }

            .fi-main {
                padding-left: 12px !important;
                padding-right: 12px !important;
            }

            .mobile-white-bg {
                background-color: white !important;
                border-radius: 12px !important;
            }
            .mobile-no-padding {
                padding-left: 12px !important;
                padding-right: 12px !important;
            }
            .mobile-hidden-text {
                display: none !important;
            }

            .mobile-bottom-btn-container {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 16px;
                background: linear-gradient(to top, white 80%, rgba(255,255,255,0));
                z-index: 40;
                display: flex;
                justify-content: center;
                pointer-events: none;
            }
            .mobile-bottom-btn {
                pointer-events: auto;
                width: 100%;
                height: 56px;
                border-radius: 12px;
                background-color: white;
                color: black;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                border: 1px solid #e5e7eb;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
            }
            .mobile-bottom-btn:active {
                background-color: #f9fafb;
                transform: scale(0.98);
            }
            .bottom-sheet-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 50;
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
            }
            .bottom-sheet-content {
                background-color: white;
                border-radius: 20px 20px 0 0;
                padding-bottom: env(safe-area-inset-bottom);
                max-height: 85vh;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                animation: slideUp 0.3s ease-out;
            }
            @@keyframes slideUp {
                from { transform: translateY(100%); }
                to { transform: translateY(0); }
            }
        }
        @@media (min-width: 1024px) {
            .mobile-only {
                display: none !important;
            }
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
                    collapsed.style.display = 'none';
                    expanded.style.display = 'flex';
                    card.style.height = '180px';
                    card.style.zIndex = '10';
                } else {
                    collapsed.style.display = 'flex';
                    expanded.style.display = 'none';
                    card.style.height = '60px';
                    card.style.zIndex = '1';
                }
            }
        }

        function updateUrlTab(tab) {
            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.pushState({}, '', url);
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth < 1024) {
                const toggleContainer = document.querySelector('.fi-layout-sidebar-toggle-btn-ctn');
                if (toggleContainer && !document.querySelector('.mobile-header-title')) {
                    const title = document.createElement('h1');
                    title.className = 'mobile-header-title';
                    title.textContent = '{{ $this->getHeading() }}';
                    toggleContainer.appendChild(title);
                }
            }

            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam && (tabParam === 'rooms' || tabParam === 'schedule')) {
                window.Livewire.find('{{ $this->getId() }}').set('activeTab', tabParam);
            }
        });

    </script>

    <!-- Main 2-Column Grid: Always visible -->
    <div class="main-grid-container"
         x-data="{ showMobileWaitingList: false }"
         @refresh-tenants.window="$wire.$refresh()"
         @schedule-cell-clicked.window="
            showMobileWaitingList = true;
            $wire.handleScheduleCellClicked($event.detail.roomId, $event.detail.date);
         "
         @close-mobile-waiting-list.window="showMobileWaitingList = false">

        <!-- Left Column: 입실 대기자 목록 (Fixed) - Desktop Only -->
        <div class="desktop-only">
            @include('filament.resources.tenants.pages.partials.waiting-list')
        </div>

        <!-- Mobile Bottom Floating Button -->
        <div class="mobile-only mobile-bottom-btn-container">
            @php
                $selectedTenant = $selectedWaitingTenantId ? $waitingTenants->firstWhere('id', $selectedWaitingTenantId) : null;
            @endphp

            @if($selectedTenant)
                <div class="mobile-bottom-btn" style="justify-content: space-between; padding: 0 24px; cursor: default; border-color: #6CE0CF; border-width: 2px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-weight: 600; color: #111827;">{{ $selectedTenant->name }}</span>
                        <span style="font-size: 14px; font-weight: 400; color: #6b7280;">선택됨</span>
                    </div>
                    <button
                        wire:click="selectWaitingTenant({{ $selectedTenant->id }})"
                        onclick="return confirm('배정을 취소하시겠습니까?');"
                        style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; background-color: #f3f4f6; border: none; cursor: pointer; color: #4b5563; transition: background-color 0.2s;"
                        onmouseover="this.style.backgroundColor='#e5e7eb'"
                        onmouseout="this.style.backgroundColor='#f3f4f6'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            @else
                <button class="mobile-bottom-btn" @click="showMobileWaitingList = true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    입실 대기자 보기
                </button>
            @endif
        </div>

        <!-- Mobile Bottom Sheet -->
        <div
            x-show="showMobileWaitingList"
            style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: transparent; z-index: 50; display: block; pointer-events: none;"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="bottom-sheet-content" style="position: absolute; bottom: 0; left: 0; right: 0; width: 100%; max-height: 85vh; border-radius: 20px 20px 0 0; overflow: hidden; pointer-events: auto;">
                <div style="height: auto; max-height: 85vh; overflow-y: auto;">
                    @include('filament.resources.tenants.pages.partials.waiting-list', ['mobileHeightAuto' => true])
                </div>
            </div>
        </div>

        <!-- Right Column: Dynamic Content Box -->
        <div style="min-width: 0; overflow: hidden;">
            <div class="mobile-white-bg" style="display: flex; flex-direction: column; border-radius: 16px; background-color: #f8f8f8; border: none; box-shadow: none; height: 100%; width: 100%; max-width: 100%; overflow: hidden;">
                <!-- Tabs Navigation (Inside right box) -->
                <div class="mobile-white-bg mobile-no-padding" style="padding: 24px 24px 16px 24px; background-color: #f8f8f8; border-radius: 16px 16px 0 0; flex-shrink: 0;">
                    <div style="display: inline-flex; background-color: #ffffff; border-radius: 999px; padding: 4px; gap: 4px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                        <button
                            wire:click="setActiveTab('rooms')"
                            onclick="updateUrlTab('rooms')"
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
                            onclick="updateUrlTab('schedule')"
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
                            <div class="mobile-no-padding mobile-white-bg" style="padding: 0 24px 16px 24px; display: flex; flex-direction: column; gap: 16px; background-color: #f8f8f8; flex-shrink: 0;">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div class="mobile-hidden-text" style="font-size: 18px; font-weight: 500; line-height: 1;">호실 현황</div>
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

                            <!-- Content: Room Cards (GoshiwonManager Style) -->
                            <div style="padding: 0; flex: 1; min-height: 0; overflow: hidden;">
                                <div class="room-grid-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; padding: 4px 16px 32px 16px; height: 100%; overflow-y: auto; overflow-x: hidden; scroll-behavior: smooth;">

                                @forelse($rooms as $room)
                                    @php
                                        $currentTenant = $room->tenant;
                                        $isOccupied = $currentTenant !== null;
                                        $isAvailableForSelectedTenant = $this->isRoomAvailableForSelectedTenant($room->id);

                                        // 퇴실일 관련 계산
                                        $isOverdueCheckout = false;
                                        $daysRemaining = null;
                                        $isExpiringSoon = false;
                                        if ($isOccupied && $currentTenant->move_out_date) {
                                            $moveOutDate = \Carbon\Carbon::parse($currentTenant->move_out_date);
                                            $today = \Carbon\Carbon::today();
                                            $daysRemaining = $today->diffInDays($moveOutDate, false);
                                            $isOverdueCheckout = $moveOutDate->isPast();
                                            $isExpiringSoon = !$isOverdueCheckout && $daysRemaining <= 7;
                                        }

                                        // 상태 결정
                                        $statusClass = 'vacant';
                                        $statusLabel = '공실';
                                        if ($isOccupied) {
                                            if ($isOverdueCheckout) {
                                                $statusClass = 'overdue';
                                                $statusLabel = '초과';
                                            } elseif ($isExpiringSoon) {
                                                $statusClass = 'expiring';
                                                $statusLabel = '만료임박';
                                            } else {
                                                $statusClass = 'occupied';
                                                $statusLabel = '사용중';
                                            }
                                        }
                                    @endphp

                                    <!-- Room Card (GoshiwonManager Style) -->
                                    <div style="display: flex; flex-direction: column; gap: 6px;">
                                        <div
                                            class="gm-room-card {{ !$isOccupied ? 'vacant-card' : '' }} {{ $isAvailableForSelectedTenant ? 'available-highlight' : '' }}"
                                            wire:click="handleRoomClick({{ $room->id }})"
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

                                                    if (moveInDate && (indefiniteMoveOut === '1' || moveOutDate)) {
                                                        const finalMoveOutDate = moveOutDate || moveInDate;
                                                        $wire.assignTenantDirectly({{ $room->id }}, tenantId, moveInDate, finalMoveOutDate);
                                                    } else {
                                                        $wire.openCreateModal({{ $room->id }}, tenantId, moveInDate, moveOutDate);
                                                    }
                                                }
                                            "
                                        >
                                            <!-- Status indicator bar at top -->
                                            <div class="gm-status-bar {{ $statusClass }}"></div>

                                            <!-- Card body -->
                                            <div style="padding: 12px;">
                                                <!-- Header: Room number + Badge -->
                                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                                    <span style="font-weight: 700; font-size: 15px; color: #1f2937;">{{ $room->room_number }}호</span>
                                                    <span class="gm-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                                </div>

                                                <!-- Room Info Grid -->
                                                <div class="gm-info-grid">
                                                    <span class="label">유형</span>
                                                    <span class="value">{{ $room->room_type ?? '스탠다드룸' }}</span>
                                                    <span class="label">월세</span>
                                                    <span class="value bold">{{ number_format($room->monthly_rent) }}원</span>
                                                    <span class="label">보증금</span>
                                                    <span class="value bold">{{ number_format($room->deposit ?? 0) }}원</span>
                                                </div>

                                                <!-- Divider -->
                                                <div class="gm-divider"></div>

                                                <!-- Resident Info -->
                                                @if($isOccupied)
                                                    <div class="gm-info-grid">
                                                        <span class="label">입주자</span>
                                                        <span class="value bold" wire:click.stop="editTenant({{ $currentTenant->id }})" style="cursor: pointer; color: #111827;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">{{ $currentTenant->name }}</span>
                                                        <span class="label">입실일</span>
                                                        <span class="value">{{ $currentTenant->move_in_date ? $currentTenant->move_in_date->format('y.m.d') : '-' }}</span>
                                                        <span class="label">퇴실일</span>
                                                        <span class="value {{ $isOverdueCheckout ? 'overdue' : '' }}">
                                                            {{ $currentTenant->indefinite_move_out ? '미정' : ($currentTenant->move_out_date ? $currentTenant->move_out_date->format('y.m.d') : '-') }}
                                                        </span>
                                                    </div>

                                                    <!-- D-day display -->
                                                    @if($currentTenant->move_out_date && !$currentTenant->indefinite_move_out)
                                                        <div style="display: flex; justify-content: flex-end; margin-top: 6px;">
                                                            @if($isOverdueCheckout)
                                                                <span class="gm-dday danger">D+{{ abs($daysRemaining) }}</span>
                                                            @elseif($isExpiringSoon)
                                                                <span class="gm-dday warning">D-{{ $daysRemaining }}</span>
                                                            @else
                                                                <span class="gm-dday normal">D-{{ $daysRemaining }}</span>
                                                            @endif
                                                        </div>
                                                    @endif

                                                    <!-- Remove button (hover) -->
                                                    <button class="gm-remove-btn"
                                                        wire:click.stop="removeTenantFromRoom({{ $currentTenant->id }})"
                                                        title="배정 취소">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                                        </svg>
                                                    </button>
                                                @else
                                                    <div class="gm-info-grid">
                                                        <span class="label">입주자</span>
                                                        <span class="value" style="color: #d1d5db;">-</span>
                                                        <span class="label">입실일</span>
                                                        <span class="value" style="color: #d1d5db;">-</span>
                                                        <span class="label">퇴실일</span>
                                                        <span class="value" style="color: #d1d5db;">-</span>
                                                    </div>
                                                    <div style="display: flex; justify-content: flex-end; margin-top: 6px;">
                                                        <span style="font-size: 11px; color: #9ca3af;">입주 가능</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Future tenants card (동일 유지) --}}
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
                                                height: 48px;
                                                display: flex;
                                                flex-direction: column;
                                                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                                                transition: all 0.3s ease-in-out;
                                                cursor: pointer;
                                                width: 100%;
                                                position: relative;
                                                z-index: 1;
                                                isolation: isolate;
                                                background-color: {{ $futureTenantsCount > 0 ? 'rgba(64, 192, 192, 0.1)' : '#EEF1F7' }};
                                            "
                                        >
                                            @if($futureTenantsCount > 0)
                                                <div id="future-collapsed-{{ $room->id }}" style="display: flex; align-items: center; justify-content: space-between; height: 100%; padding: 0 12px;">
                                                    <span style="color: #6b7280; font-size: 14px; font-weight: 500;">다음 입실자 배정</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #6b7280;">
                                                        <path d="m6 9 6 6 6-6"/>
                                                    </svg>
                                                </div>

                                                <div id="future-expanded-{{ $room->id }}" style="display: none; flex-direction: column; justify-content: center; height: 100%;">
                                                    <div style="padding: 8px; transition: opacity 0.2s; transition-delay: 0.3s; opacity: 1; overflow-y: auto;">
                                                        <div style="display: flex; flex-direction: column; gap: 8px;">
                                                            @foreach($futureTenants as $index => $futureTenant)
                                                                @php
                                                                    $tenantGapDays = null;
                                                                    if ($index === 0) {
                                                                        if ($isOccupied && $currentTenant && $currentTenant->move_out_date && $futureTenant->move_in_date) {
                                                                            $prevMoveOutDate = \Carbon\Carbon::parse($currentTenant->move_out_date);
                                                                            $currMoveInDate = \Carbon\Carbon::parse($futureTenant->move_in_date);
                                                                            $tenantGapDays = $prevMoveOutDate->copy()->addDay()->diffInDays($currMoveInDate, false);
                                                                            if ($tenantGapDays <= 0) {
                                                                                $tenantGapDays = null;
                                                                            }
                                                                        }
                                                                    } else {
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
                                                                    @php
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
                                                                            <svg wire:click.stop="removeTenantFromRoom({{ $futureTenant->id }})" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #6b7280; margin-left: 8px; transition: color 0.2s; cursor: pointer;" onmouseover="this.style.stroke='#ef4444'" onmouseout="this.style.stroke='#6b7280'"><circle cx="12" cy="12" r="10"></circle><path d="m15 9-6 6"></path><path d="m9 9 6 6"></path></svg>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach

                                                            @php
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
                                                    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 0 12px;">
                                                        <span style="color: #6b7280; font-size: 14px; font-weight: 500;">다음 입실자 배정</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #6b7280;">
                                                            <path d="m6 9 6 6 6-6"/>
                                                        </svg>
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
                        <div class="mobile-no-padding" style="padding: 16px 24px 24px 24px; height: 100%; width: 100%; max-width: 100%; overflow: hidden; display: flex; flex-direction: column; box-sizing: border-box;">
                            <div style="flex: 1; min-height: 0; width: 100%; max-width: 100%; overflow: hidden;">
                                <!-- Tenant Scheduler Component -->
                                <livewire:tenant-scheduler
                                    :branchId="$branchId"
                                    :selected-waiting-tenant-id="$selectedWaitingTenantId"
                                    wire:key="tenant-scheduler-{{ $branchId }}" />
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

    {{-- Removal Confirmation Modal --}}
    @if($showRemoveConfirmation)
        <div style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background-color: rgba(0, 0, 0, 0.5); padding: 0 20px;">
            <div style="background-color: white; border-radius: 20px; padding: 32px 24px; width: 100%; max-width: 400px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                <h3 style="font-size: 18px; font-weight: bold; margin-bottom: 24px; text-align: center;">배정 취소하시겠어요?</h3>
                <div style="display: flex; gap: 12px;">
                    <button
                        wire:click="cancelRemoveTenant"
                        style="flex: 1; padding: 14px; border-radius: 12px; background-color: #f3f4f6; color: #374151; font-weight: 600; font-size: 16px; transition: background-color 0.2s; border: none; cursor: pointer;"
                        onmouseover="this.style.backgroundColor='#e5e7eb'"
                        onmouseout="this.style.backgroundColor='#f3f4f6'">
                        아니오
                    </button>
                    <button
                        wire:click="confirmRemoveTenant"
                        style="flex: 1; padding: 14px; border-radius: 12px; background-color: #15B9A6; color: white; font-weight: 600; font-size: 16px; transition: background-color 0.2s; border: none; cursor: pointer;"
                        onmouseover="this.style.backgroundColor='#129A89'"
                        onmouseout="this.style.backgroundColor='#15B9A6'">
                        예
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Assignment Confirmation Modal --}}
    @if($showAssignmentConfirmation)
        <div style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background-color: rgba(0, 0, 0, 0.5); padding: 0 20px;">
            <div style="background-color: white; border-radius: 20px; padding: 32px 24px; width: 100%; max-width: 400px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                <h3 style="font-size: 18px; font-weight: bold; margin-bottom: 24px; text-align: center;">배정 진행할까요?</h3>
                <div style="display: flex; gap: 12px;">
                    <button
                        wire:click="cancelPendingAssignment"
                        style="flex: 1; padding: 14px; border-radius: 12px; background-color: #f3f4f6; color: #374151; font-weight: 600; font-size: 16px; transition: background-color 0.2s; border: none; cursor: pointer;"
                        onmouseover="this.style.backgroundColor='#e5e7eb'"
                        onmouseout="this.style.backgroundColor='#f3f4f6'">
                        아니오
                    </button>
                    <button
                        wire:click="processPendingAssignment"
                        style="flex: 1; padding: 14px; border-radius: 12px; background-color: #15B9A6; color: white; font-weight: 600; font-size: 16px; transition: background-color 0.2s; border: none; cursor: pointer;"
                        onmouseover="this.style.backgroundColor='#129A89'"
                        onmouseout="this.style.backgroundColor='#15B9A6'">
                        예
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
