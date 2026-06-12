<x-filament-panels::page>
    <style>
        .tenant-desktop-view { display: none; }
        .tenant-mobile-view { display: block; }
        @media (min-width: 768px) {
            .tenant-desktop-view { display: block; }
            .tenant-mobile-view { display: none; }
        }

        /* 바텀시트 모달 애니메이션 */
        .filter-backdrop {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 40;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .filter-backdrop.active {
            opacity: 1;
        }
        .filter-bottomsheet {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #ffffff;
            border-radius: 1rem 1rem 0 0;
            z-index: 50;
            transform: translateY(100%);
            transition: transform 0.3s ease;
            max-height: 80vh;
            overflow-y: auto;
        }
        .filter-bottomsheet.active {
            transform: translateY(0);
        }
    </style>

    <!-- Desktop: Filament Table -->
    <div class="tenant-desktop-view" x-data @tenant-management-saved.window="$wire.$refresh()">
        {{ $this->table }}
    </div>


    <!-- Mobile: Custom Card View -->
    <div class="tenant-mobile-view" x-data @tenant-management-saved.window="$wire.$refresh()">
        <!-- 검색 및 필터 -->
        <div style="margin-bottom: 1rem;">
            <!-- 검색 입력 -->
            <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
                <div style="flex: 1; position: relative;">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="mobileSearch"
                        placeholder="이름 또는 연락처 검색"
                        style="width: 100%; padding: 0.625rem 0.75rem 0.625rem 2.5rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background-color: #ffffff; color: #111827; outline: none;"
                    />
                    <svg style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); width: 1rem; height: 1rem; color: #9ca3af;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                @php
                    $activeFilterCount = $this->getActiveFilterCount();
                @endphp
                <button
                    wire:click="toggleMobileFilters"
                    style="padding: 0.5rem 0.625rem; font-size: 0.813rem; font-weight: 500; border-radius: 0.375rem; border: 1px solid {{ $activeFilterCount > 0 ? '#06CBBB' : '#d1d5db' }}; cursor: pointer; background-color: {{ $activeFilterCount > 0 ? '#06CBBB' : '#ffffff' }}; color: {{ $activeFilterCount > 0 ? '#ffffff' : '#374151' }}; display: flex; align-items: center; gap: 0.25rem;"
                >
                    <svg style="width: 0.875rem; height: 0.875rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    필터
                    @if($activeFilterCount > 0)
                        <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 1rem; height: 1rem; font-size: 0.625rem; font-weight: 600; background-color: #ffffff; color: #06CBBB; border-radius: 9999px; margin-left: 0.125rem;">
                            {{ $activeFilterCount }}
                        </span>
                    @endif
                </button>
            </div>

            <!-- 적용된 필터 표시 -->
            @if($mobileSearch || $this->getActiveFilterCount() > 0)
                <div style="display: flex; flex-wrap: wrap; gap: 0.375rem; margin-bottom: 0.75rem;">
                    @if($mobileSearch)
                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.188rem 0.5rem; font-size: 0.688rem; background-color: #e0f7f5; color: #047857; border-radius: 9999px;">
                            {{ $mobileSearch }}
                            <button wire:click="$set('mobileSearch', '')" style="background: none; border: none; cursor: pointer; padding: 0; color: #047857;">
                                <svg style="width: 0.75rem; height: 0.75rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    @endif
                    @if($mobileFilterStatus)
                        @php $statusLabels = ['checked_in' => '입실자', 'scheduled' => '입실예정', 'checked_out' => '퇴실자', 'pending' => '대기']; @endphp
                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.188rem 0.5rem; font-size: 0.688rem; background-color: #e0f7f5; color: #047857; border-radius: 9999px;">
                            {{ $statusLabels[$mobileFilterStatus] ?? $mobileFilterStatus }}
                            <button wire:click="$set('mobileFilterStatus', '')" style="background: none; border: none; cursor: pointer; padding: 0; color: #047857;">
                                <svg style="width: 0.75rem; height: 0.75rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    @endif
                    @if($mobileFilterGender)
                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.188rem 0.5rem; font-size: 0.688rem; background-color: #e0f7f5; color: #047857; border-radius: 9999px;">
                            {{ $mobileFilterGender === 'male' ? '남성' : '여성' }}
                            <button wire:click="$set('mobileFilterGender', '')" style="background: none; border: none; cursor: pointer; padding: 0; color: #047857;">
                                <svg style="width: 0.75rem; height: 0.75rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    @endif
                    @if($mobileFilterRoomType)
                        @php $roomTypeLabels = ['single' => '1인실', 'double' => '2인실', 'triple' => '3인실', 'quad' => '4인실']; @endphp
                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.188rem 0.5rem; font-size: 0.688rem; background-color: #e0f7f5; color: #047857; border-radius: 9999px;">
                            {{ $roomTypeLabels[$mobileFilterRoomType] ?? $mobileFilterRoomType }}
                            <button wire:click="$set('mobileFilterRoomType', '')" style="background: none; border: none; cursor: pointer; padding: 0; color: #047857;">
                                <svg style="width: 0.75rem; height: 0.75rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    @endif
                    @if($mobileFilterWindowStructure)
                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.188rem 0.5rem; font-size: 0.688rem; background-color: #e0f7f5; color: #047857; border-radius: 9999px;">
                            {{ $mobileFilterWindowStructure === 'with_window' ? '창문있음' : '창문없음' }}
                            <button wire:click="$set('mobileFilterWindowStructure', '')" style="background: none; border: none; cursor: pointer; padding: 0; color: #047857;">
                                <svg style="width: 0.75rem; height: 0.75rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    @endif
                    @if($mobileFilterRoomCategory)
                        @php $categoryLabels = ['standard' => '일반', 'premium' => '프리미엄', 'deluxe' => '디럭스']; @endphp
                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.188rem 0.5rem; font-size: 0.688rem; background-color: #e0f7f5; color: #047857; border-radius: 9999px;">
                            {{ $categoryLabels[$mobileFilterRoomCategory] ?? $mobileFilterRoomCategory }}
                            <button wire:click="$set('mobileFilterRoomCategory', '')" style="background: none; border: none; cursor: pointer; padding: 0; color: #047857;">
                                <svg style="width: 0.75rem; height: 0.75rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    @endif
                    @if($mobileFilterMonthlyRentFrom || $mobileFilterMonthlyRentTo)
                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.188rem 0.5rem; font-size: 0.688rem; background-color: #e0f7f5; color: #047857; border-radius: 9999px;">
                            {{ $mobileFilterMonthlyRentFrom ? number_format($mobileFilterMonthlyRentFrom).'원' : '' }}{{ $mobileFilterMonthlyRentFrom && $mobileFilterMonthlyRentTo ? '~' : '' }}{{ $mobileFilterMonthlyRentTo ? number_format($mobileFilterMonthlyRentTo).'원' : '' }}
                            <button wire:click="$set('mobileFilterMonthlyRentFrom', null); $set('mobileFilterMonthlyRentTo', null)" style="background: none; border: none; cursor: pointer; padding: 0; color: #047857;">
                                <svg style="width: 0.75rem; height: 0.75rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    @endif
                    @if($mobileFilterPaymentStatus)
                        @php $paymentLabels = ['paid' => '납부완료', 'pending' => '미납', 'overdue' => '연체', 'waiting' => '대기']; @endphp
                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.188rem 0.5rem; font-size: 0.688rem; background-color: #e0f7f5; color: #047857; border-radius: 9999px;">
                            {{ $paymentLabels[$mobileFilterPaymentStatus] ?? $mobileFilterPaymentStatus }}
                            <button wire:click="$set('mobileFilterPaymentStatus', '')" style="background: none; border: none; cursor: pointer; padding: 0; color: #047857;">
                                <svg style="width: 0.75rem; height: 0.75rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    @endif
                    @if($mobileFilterBlacklist !== '')
                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.188rem 0.5rem; font-size: 0.688rem; background-color: #e0f7f5; color: #047857; border-radius: 9999px;">
                            {{ $mobileFilterBlacklist === '1' ? '블랙리스트만' : '정상만' }}
                            <button wire:click="$set('mobileFilterBlacklist', '')" style="background: none; border: none; cursor: pointer; padding: 0; color: #047857;">
                                <svg style="width: 0.75rem; height: 0.75rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <!-- 테이블 헤더 -->
        <div style="display: flex; align-items: center; padding: 0.75rem 1rem; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.5rem 0.5rem 0 0; margin-bottom: -1px; gap: 0.5rem;">
            <span style="flex: 0 0 auto; font-size: 0.75rem; font-weight: 600; color: #374151; min-width: 60px;">입주 상태</span>
            <span style="flex: 1; font-size: 0.75rem; font-weight: 600; color: #374151; text-align: center;">이름</span>
            <span style="flex: 0 0 auto; font-size: 0.75rem; font-weight: 600; color: #374151; min-width: 50px; text-align: center;">호실</span>
            <span style="flex: 0 0 auto; font-size: 0.75rem; font-weight: 600; color: #374151; min-width: 70px; text-align: right;">상세 관리</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0;">
            @foreach($this->getTenants() as $tenant)
                @php
                    $isExpanded = in_array($tenant->id, $expandedCards ?? []);
                    $statusLabel = $tenant->process_status_label;

                    // 퇴실 필요 여부 확인 (입실자이면서 퇴실일이 지난 경우)
                    $needsCheckout = false;
                    $checkoutOverdueDays = 0;
                    if ($tenant->process_status === 'checked_in' && $tenant->room?->move_out_date) {
                        $moveOutDate = $tenant->room->move_out_date;
                        if ($moveOutDate->isPast()) {
                            $needsCheckout = true;
                            $checkoutOverdueDays = (int) $moveOutDate->diffInDays(now());
                        }
                    }

                    // 퇴실 필요 시 진한 빨간색 배지로 변경
                    $statusBg = match(true) {
                        $needsCheckout => '#dc2626',
                        $tenant->process_status === 'checked_in' => $tenant->is_blacklisted ? '#fee2e2' : '#dcfce7',
                        $tenant->process_status === 'scheduled' => $tenant->is_blacklisted ? '#fee2e2' : '#fef9c3',
                        $tenant->process_status === 'checked_out' => '#f3f4f6',
                        default => '#f3f4f6',
                    };
                    $statusText = match(true) {
                        $needsCheckout => '#ffffff',
                        $tenant->process_status === 'checked_in' => $tenant->is_blacklisted ? '#991b1b' : '#166534',
                        $tenant->process_status === 'scheduled' => $tenant->is_blacklisted ? '#991b1b' : '#854d0e',
                        $tenant->process_status === 'checked_out' => '#374151',
                        default => '#4b5563',
                    };
                    $genderLabel = match($tenant->gender) {
                        'male' => '남성',
                        'female' => '여성',
                        default => '-',
                    };
                    $roomTypeLabel = match($tenant->room?->room_type) {
                        'single' => '1인실',
                        'double' => '2인실',
                        'triple' => '3인실',
                        'quad' => '4인실',
                        default => '-',
                    };
                    $windowLabel = match($tenant->room?->window_structure) {
                        'with_window' => '창문있음',
                        'without_window' => '창문없음',
                        default => '-',
                    };
                    $categoryLabel = match($tenant->room?->room_category) {
                        'standard' => '일반',
                        'premium' => '프리미엄',
                        'deluxe' => '디럭스',
                        default => '-',
                    };
                    $textColor = $tenant->is_blacklisted ? '#dc2626' : '#111827';
                    $borderColor = $tenant->is_blacklisted ? '#fca5a5' : '#e5e7eb';
                @endphp

                <div style="background-color: #ffffff; {{ $loop->last ? 'border-radius: 0 0 0.5rem 0.5rem;' : 'border-radius: 0;' }} box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); border: 1px solid {{ $borderColor }}; border-top: none; overflow: hidden;">
                    <!-- Card Header -->
                    <div style="padding: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <!-- 입주 상태 뱃지 -->
                        <div style="flex: 0 0 auto; min-width: 60px;">
                            @if($needsCheckout)
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 0.125rem;">
                                    <span style="font-size: 0.6rem; color: #dc2626; font-weight: 600;">퇴실 예정</span>
                                    <span style="display: inline-flex; align-items: center; padding: 0 0.5rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 500; background-color: {{ $statusBg }}; color: {{ $statusText }}; white-space: nowrap;">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                            @else
                                <span style="display: inline-flex; align-items: center; padding: 0 0.5rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 500; background-color: {{ $statusBg }}; color: {{ $statusText }}; white-space: nowrap;">
                                    {{ $statusLabel }}
                                </span>
                            @endif
                        </div>

                        <!-- 이름 -->
                        <div style="flex: 1; text-align: center; min-width: 0;">
                            @if($tenant->is_blacklisted)
                                <div style="font-size: 0.7rem; color: #ef4444; font-weight: 600;">블랙리스트</div>
                            @endif
                            <span style="font-weight: 600; color: {{ $textColor }}; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $tenant->name }}
                            </span>
                        </div>

                        <!-- 호실 번호 -->
                        <div style="flex: 0 0 auto; min-width: 50px; text-align: center;">
                            <span style="color: {{ $tenant->is_blacklisted ? '#ef4444' : '#4b5563' }}; white-space: nowrap;">
                                {{ $tenant->room?->room_number ?? '-' }}호
                            </span>
                        </div>

                        <!-- 상세보기/닫기 버튼 -->
                        <div style="flex: 0 0 auto; min-width: 70px; text-align: right;">
                            <button
                                wire:click="toggleCard({{ $tenant->id }})"
                                style="padding: 0.375rem 0.75rem; font-size: 0.875rem; font-weight: 500; border-radius: 0.375rem; border: 1px solid #d1d5db; cursor: pointer; transition: background-color 0.15s; background-color: #ffffff; color: #374151;"
                            >
                                {{ $isExpanded ? '닫기' : '상세보기' }}
                            </button>
                        </div>
                    </div>

                    <!-- Card Body (펼쳐진 상태) -->
                    @if($isExpanded)
                        <div style="border-top: 1px solid #e5e7eb; padding: 1rem; background-color: #f9fafb;">
                            <!-- 상세 정보 그리드 -->
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; font-size: 0.875rem;">
                                <div>
                                    <span style="color: #6b7280;">성별</span>
                                    <p style="font-weight: 500; color: {{ $textColor }}; margin: 0.25rem 0 0 0;">{{ $genderLabel }}</p>
                                </div>
                                <div>
                                    <span style="color: #6b7280;">연락처</span>
                                    <p style="font-weight: 500; color: {{ $textColor }}; margin: 0.25rem 0 0 0;">{{ $tenant->phone ?? '-' }}</p>
                                </div>
                                <div>
                                    <span style="color: #6b7280;">호실 유형</span>
                                    <p style="font-weight: 500; color: {{ $textColor }}; margin: 0.25rem 0 0 0;">{{ $roomTypeLabel }}</p>
                                </div>
                                <div>
                                    <span style="color: #6b7280;">창 구조</span>
                                    <p style="font-weight: 500; color: {{ $textColor }}; margin: 0.25rem 0 0 0;">{{ $windowLabel }}</p>
                                </div>
                                <div>
                                    <span style="color: #6b7280;">호실 타입</span>
                                    <p style="font-weight: 500; color: {{ $textColor }}; margin: 0.25rem 0 0 0;">{{ $categoryLabel }}</p>
                                </div>
                                <div>
                                    <span style="color: #6b7280;">입실일</span>
                                    <p style="font-weight: 500; color: {{ $textColor }}; margin: 0.25rem 0 0 0;">{{ $tenant->room?->move_in_date?->format('Y.m.d') ?? '-' }}</p>
                                </div>
                                <div>
                                    <span style="color: #6b7280;">월 입실료</span>
                                    <p style="font-weight: 500; color: {{ $textColor }}; margin: 0.25rem 0 0 0;">{{ $tenant->room?->monthly_rent ? number_format($tenant->room->monthly_rent) . '원' : '-' }}</p>
                                </div>
                                <div>
                                    <span style="color: #6b7280;">퇴실일</span>
                                    @if($needsCheckout)
                                        <p style="font-weight: 500; color: #dc2626; margin: 0.25rem 0 0 0;">
                                            {{ $tenant->room->move_out_date->format('Y.m.d') }}
                                            <span style="font-size: 0.75rem; font-weight: 600;">(+{{ $checkoutOverdueDays }}일)</span>
                                        </p>
                                    @else
                                        <p style="font-weight: 500; color: {{ $textColor }}; margin: 0.25rem 0 0 0;">{{ $tenant->room?->move_out_date?->format('Y.m.d') ?? '미정' }}</p>
                                    @endif
                                </div>
                                <div style="grid-column: span 2;">
                                    <span style="color: #6b7280;">블랙리스트 등록 여부</span>
                                    <p style="font-weight: 500; color: {{ $tenant->is_blacklisted ? '#dc2626' : '#16a34a' }}; margin: 0.25rem 0 0 0;">
                                        {{ $tenant->is_blacklisted ? '등록' : '미등록' }}
                                    </p>
                                </div>
                            </div>

                            <!-- 액션 버튼들 -->
                            <div style="margin-top: 1rem; display: flex; flex-direction: column; gap: 0.5rem;">
                                @if($needsCheckout)
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button
                                            wire:click="completeCheckout({{ $tenant->id }})"
                                            wire:confirm="퇴실 완료 처리하시겠습니까?"
                                            style="flex: 1; padding: 0.625rem; text-align: center; background-color: #FECACA; color: #991b1b; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; border: none; cursor: pointer;"
                                        >
                                            퇴실 완료 처리하기
                                        </button>
                                        @if($tenant->phone)
                                            <a href="tel:{{ $tenant->phone }}" style="flex: 1; padding: 0.625rem; text-align: center; background-color: #f3f4f6; color: #374151; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; text-decoration: none; display: block;">
                                                연락하기
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    @if($tenant->phone)
                                        <a href="tel:{{ $tenant->phone }}" style="width: 100%; padding: 0.625rem; text-align: center; background-color: #f3f4f6; color: #374151; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; text-decoration: none; display: block;">
                                            연락하기
                                        </a>
                                    @endif
                                @endif
                                <button
                                    wire:click="$dispatch('edit-tenant-management', { tenantId: {{ $tenant->id }} })"
                                    style="width: 100%; padding: 0.625rem; text-align: center; background-color: #111827; color: #ffffff; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; border: none; cursor: pointer;"
                                >
                                    수정하기
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach

            @if($this->getTenants()->isEmpty())
                <div style="text-align: center; padding: 3rem 1rem; background-color: #ffffff; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                    <svg style="margin: 0 auto; height: 3rem; width: 3rem; color: #9ca3af;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 style="margin-top: 0.5rem; font-size: 0.875rem; font-weight: 500; color: #111827;">입주자가 없습니다</h3>
                    <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #6b7280;">새 입주자를 추가해보세요.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Tenant Management Modal -->
    <livewire:tenant-management-modal wire:key="tenant-management-modal" />

    <!-- 필터 바텀시트 모달 -->
    @if($showMobileFilters)
        <!-- 배경 오버레이 -->
        <div
            class="filter-backdrop active"
            wire:click="toggleMobileFilters"
            style="position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 40;"
        ></div>

        <!-- 바텀시트 -->
        <div
            class="filter-bottomsheet active"
            style="position: fixed; bottom: 0; left: 0; right: 0; background-color: #ffffff; border-radius: 1rem 1rem 0 0; z-index: 50; max-height: 85vh; box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1); display: flex; flex-direction: column;"
        >
            <!-- 고정 헤더 -->
            <div style="flex-shrink: 0;">
                <!-- 핸들 바 -->
                <div style="display: flex; justify-content: center; padding: 0.5rem;">
                    <div style="width: 2.5rem; height: 0.25rem; background-color: #d1d5db; border-radius: 9999px;"></div>
                </div>

                <!-- 헤더 -->
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0 1rem 0.75rem 1rem; border-bottom: 1px solid #e5e7eb;">
                    <h3 style="font-size: 1rem; font-weight: 600; color: #111827; margin: 0;">필터</h3>
                    @if($this->getActiveFilterCount() > 0)
                        <button
                            wire:click="resetMobileFilters"
                            style="padding: 0.25rem 0.5rem; background: none; border: none; cursor: pointer; color: #ef4444; font-size: 0.875rem; font-weight: 500;"
                        >
                            초기화
                        </button>
                    @endif
                </div>
            </div>

            <!-- 스크롤 가능한 필터 내용 -->
            <div style="flex: 1; overflow-y: auto; padding: 1rem;">
                <!-- 1열 레이아웃 -->
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <!-- 입주 상태 필터 -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">입주 상태</label>
                        <select
                            wire:model.live="mobileFilterStatus"
                            style="width: 100%; padding: 0.5rem 0.625rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background-color: #ffffff; color: #111827;"
                        >
                            <option value="">전체</option>
                            <option value="checked_in">입실자</option>
                            <option value="scheduled">입실예정</option>
                            <option value="checked_out">퇴실자</option>
                            <option value="pending">대기</option>
                        </select>
                    </div>

                    <!-- 호실 유형 필터 -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">호실 유형</label>
                        <select
                            wire:model.live="mobileFilterRoomType"
                            style="width: 100%; padding: 0.5rem 0.625rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background-color: #ffffff; color: #111827;"
                        >
                            <option value="">전체</option>
                            <option value="single">1인실</option>
                            <option value="double">2인실</option>
                            <option value="triple">3인실</option>
                            <option value="quad">4인실</option>
                        </select>
                    </div>

                    <!-- 창 구조 필터 -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">창 구조</label>
                        <select
                            wire:model.live="mobileFilterWindowStructure"
                            style="width: 100%; padding: 0.5rem 0.625rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background-color: #ffffff; color: #111827;"
                        >
                            <option value="">전체</option>
                            <option value="with_window">창문있음</option>
                            <option value="without_window">창문없음</option>
                        </select>
                    </div>

                    <!-- 호실 타입 필터 -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">호실 타입</label>
                        <select
                            wire:model.live="mobileFilterRoomCategory"
                            style="width: 100%; padding: 0.5rem 0.625rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background-color: #ffffff; color: #111827;"
                        >
                            <option value="">전체</option>
                            <option value="standard">일반</option>
                            <option value="premium">프리미엄</option>
                            <option value="deluxe">디럭스</option>
                        </select>
                    </div>

                    <!-- 성별 필터 -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">성별</label>
                        <select
                            wire:model.live="mobileFilterGender"
                            style="width: 100%; padding: 0.5rem 0.625rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background-color: #ffffff; color: #111827;"
                        >
                            <option value="">전체</option>
                            <option value="male">남성</option>
                            <option value="female">여성</option>
                        </select>
                    </div>

                    <!-- 월 입실료 필터 -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">월 입실료</label>
                        <div style="display: flex; align-items: center; gap: 0.375rem;">
                            <input
                                type="number"
                                wire:model.live.debounce.500ms="mobileFilterMonthlyRentFrom"
                                placeholder="최소"
                                style="flex: 1; min-width: 0; padding: 0.5rem 0.5rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background-color: #ffffff; color: #111827;"
                            />
                            <span style="color: #9ca3af; flex-shrink: 0;">~</span>
                            <input
                                type="number"
                                wire:model.live.debounce.500ms="mobileFilterMonthlyRentTo"
                                placeholder="최대"
                                style="flex: 1; min-width: 0; padding: 0.5rem 0.5rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background-color: #ffffff; color: #111827;"
                            />
                            <span style="color: #6b7280; font-size: 0.813rem; flex-shrink: 0;">원</span>
                        </div>
                    </div>

                    <!-- 납부 상태 필터 -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">납부 상태</label>
                        <select
                            wire:model.live="mobileFilterPaymentStatus"
                            style="width: 100%; padding: 0.5rem 0.625rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background-color: #ffffff; color: #111827;"
                        >
                            <option value="">전체</option>
                            <option value="paid">납부완료</option>
                            <option value="pending">미납</option>
                            <option value="overdue">연체</option>
                            <option value="waiting">대기</option>
                        </select>
                    </div>

                    <!-- 블랙리스트 필터 -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">블랙리스트</label>
                        <select
                            wire:model.live="mobileFilterBlacklist"
                            style="width: 100%; padding: 0.5rem 0.625rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background-color: #ffffff; color: #111827;"
                        >
                            <option value="">전체</option>
                            <option value="1">블랙리스트만</option>
                            <option value="0">정상만</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 고정 하단 버튼 -->
            <div style="flex-shrink: 0; padding: 1rem; border-top: 1px solid #e5e7eb; background-color: #ffffff;">
                <button
                    wire:click="toggleMobileFilters"
                    style="width: 100%; padding: 0.625rem; font-size: 0.875rem; font-weight: 600; color: #ffffff; background-color: #06CBBB; border: none; border-radius: 0.375rem; cursor: pointer;"
                >
                    적용하기
                </button>
                <!-- 하단 안전 영역 (iOS) -->
                <div style="height: env(safe-area-inset-bottom, 0);"></div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
