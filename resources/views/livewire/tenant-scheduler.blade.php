<div x-data="{
    isDragging: false,
    dragStartRoomId: null,
    dragStartDate: null,
    dragEndDate: null,
    dragEndRoomId: null,
    barDragging: false,
    draggedTenantId: null,
    draggedBarStartRoom: null,
    draggedTenantData: null,
    contextMenuTenantId: null,
    contextMenuX: 0,
    contextMenuY: 0,
    editingTenantId: null, // 현재 편집 중인 입주자 ID

    isEditingTenant(tenantId) {
        return this.editingTenantId === tenantId;
    },

    startDrag(roomId, date, event) {
        // 박스나 리사이즈 핸들을 클릭한 경우 셀 드래그 비활성화
        if (event.target.closest('[data-tenant-bar]') ||
            event.target.closest('[data-resize-handle]') ||
            event.target.closest('[data-draggable-bar]')) {
            return;
        }
        this.isDragging = true;
        this.dragStartRoomId = roomId;
        this.dragStartDate = date;
        this.dragEndDate = date;
        this.dragEndRoomId = roomId;
    },

    onDrag(roomId, date) {
        if (!this.isDragging) return;
        this.dragEndDate = date;
        this.dragEndRoomId = roomId;
    },

    endDrag() {
        if (this.isDragging && this.dragStartRoomId && this.dragStartDate && this.dragEndDate) {
            const startDate = this.dragStartDate < this.dragEndDate ? this.dragStartDate : this.dragEndDate;
            const endDate = this.dragStartDate > this.dragEndDate ? this.dragStartDate : this.dragEndDate;
            $wire.openCreateModal(this.dragEndRoomId || this.dragStartRoomId, startDate, endDate);
        }
        this.isDragging = false;
        this.dragStartRoomId = null;
        this.dragStartDate = null;
        this.dragEndDate = null;
        this.dragEndRoomId = null;
    },

    isInDragRange(roomId, date) {
        if (!this.isDragging) return false;

        // 같은 호실이거나 드래그 중인 호실인 경우만 하이라이트
        if (roomId !== this.dragStartRoomId && roomId !== this.dragEndRoomId) return false;

        const start = this.dragStartDate < this.dragEndDate ? this.dragStartDate : this.dragEndDate;
        const end = this.dragStartDate > this.dragEndDate ? this.dragStartDate : this.dragEndDate;
        return date >= start && date <= end;
    },

    startBarDrag(tenantId, roomId, tenantData) {
        this.barDragging = true;
        this.draggedTenantId = tenantId;
        this.draggedBarStartRoom = roomId;
        this.draggedTenantData = tenantData;
    },

    endBarDrag(roomId) {
        if (this.barDragging && this.draggedTenantId && roomId !== this.draggedBarStartRoom) {
            const currentEditingId = window.editingTenantId; // 현재 편집 중인 ID 저장
            $wire.moveTenantToRoom(this.draggedTenantId, roomId).then(() => {
                // 업데이트 후 편집 모드 복원
                if (currentEditingId) {
                    window.editingTenantId = currentEditingId;
                    console.log('편집 모드 복원됨 (호실 이동):', currentEditingId);
                    // 편집 모드 변경 이벤트 발생
                    window.dispatchEvent(new CustomEvent('edit-mode-changed'));
                }
            });
        }
        this.barDragging = false;
        this.draggedTenantId = null;
        this.draggedBarStartRoom = null;
        this.draggedTenantData = null;
    },

    showContextMenu(tenantId, event) {
        this.contextMenuTenantId = tenantId;
        this.contextMenuX = event.detail.x;
        this.contextMenuY = event.detail.y;
    },

    hideContextMenu() {
        this.contextMenuTenantId = null;
    },

    enableEditing(tenantId) {
        this.editingTenantId = tenantId;
        window.editingTenantId = tenantId; // 전역 변수에도 저장
        console.log('편집 모드 활성화:', { tenantId, editingTenantId: this.editingTenantId, global: window.editingTenantId });
        this.hideContextMenu();
        // 모든 일정 바에 편집 모드 변경 알림
        window.dispatchEvent(new CustomEvent('edit-mode-changed'));
    },

    disableEditing() {
        this.editingTenantId = null;
        window.editingTenantId = null; // 전역 변수도 초기화
        // 모든 일정 바에 편집 모드 변경 알림
        window.dispatchEvent(new CustomEvent('edit-mode-changed'));
    }
}"
    @mouseup.window="endDrag()"
    @click.window="hideContextMenu()"
    @bar-drag-start.window="startBarDrag($event.detail.tenantId, $event.detail.roomId, $event.detail.tenantData)"
    @open-context-menu.window="contextMenuTenantId = $event.detail.tenantId; contextMenuX = $event.detail.x; contextMenuY = $event.detail.y"
    @mouseup.window="if (barDragging) { barDragging = false; draggedTenantId = null; draggedBarStartRoom = null; draggedTenantData = null; }"
    @tenant-created.window="console.log('tenant-created 이벤트 수신'); $wire.$refresh()"
    @show-alert.window="alert($event.detail.message)"
    x-init="console.log('TenantScheduler 초기화:', { branchId: '{{ $branchId }}', tenants: @js($tenants) });
            window.editingTenantId = null;">

    <!-- Context Menu -->
    <div x-show="contextMenuTenantId !== null"
         x-transition
         @click.stop
         :style="`position: fixed; top: ${contextMenuY}px; left: ${contextMenuX}px; z-index: 1000;`"
         style="display: none;">
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); min-width: 150px; padding: 4px;">
            <button @click="enableEditing(contextMenuTenantId)"
                    style="width: 100%; text-align: left; padding: 8px 12px; border: none; background: none; cursor: pointer; font-size: 14px; color: #374151; border-radius: 4px; transition: background-color 0.2s;"
                    onmouseover="this.style.backgroundColor='#f3f4f6'"
                    onmouseout="this.style.backgroundColor='transparent'">
                일정 변경
            </button>
            <button @click="$wire.editTenant(contextMenuTenantId); hideContextMenu();"
                    style="width: 100%; text-align: left; padding: 8px 12px; border: none; background: none; cursor: pointer; font-size: 14px; color: #374151; border-radius: 4px; transition: background-color 0.2s;"
                    onmouseover="this.style.backgroundColor='#f3f4f6'"
                    onmouseout="this.style.backgroundColor='transparent'">
                입주자 정보 수정
            </button>
        </div>
    </div>
    <!-- 편집 모드 알림 -->
    <div x-show="editingTenantId !== null"
         x-transition
         style="background-color: #dbeafe; border: 1px solid #3b82f6; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px; display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 14px; color: #1e40af; font-weight: 500;">
                일정 편집 모드 - 드래그하여 날짜를 이동하거나 핸들을 드래그하여 기간을 조정하세요
            </span>
            <button @click="disableEditing()"
                    style="padding: 6px 16px; background-color: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600;">
                편집 완료
            </button>
        </div>
    </div>

    <!-- Scheduler Container -->
    <div style="background-color: white; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); padding: 32px;">
        <!-- Title Section -->
        <div style="margin-bottom: 24px;">
            <h2 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0 0 8px 0;">입주자 일정 관리</h2>
            <p style="font-size: 14px; color: #4b5563; margin: 0;">호실별 입주자 일정을 확인하고 관리할 수 있습니다.</p>
        </div>

        <!-- Scheduler Table -->
        <div id="scheduler-container"
             style="border: 1px solid #e5e7eb; border-radius: 8px; overflow-x: auto; position: relative; isolation: isolate;"
             x-data="{
                isLoading: false,
                scrollToDate(dateStr) {
                    $nextTick(() => {
                        const selector = '[data-date=' + String.fromCharCode(34) + dateStr + String.fromCharCode(34) + ']';
                        const targetCell = this.$el.querySelector(selector);
                        if (targetCell) {
                            const containerWidth = this.$el.clientWidth;
                            const cellLeft = targetCell.offsetLeft;
                            const cellWidth = targetCell.offsetWidth;
                            this.$el.scrollLeft = cellLeft - (containerWidth / 2) + (cellWidth / 2);
                        }
                    });
                }
             }"
             @scroll.debounce.150ms="
                const scrollLeft = $el.scrollLeft;
                const scrollWidth = $el.scrollWidth;
                const clientWidth = $el.clientWidth;

                // 왼쪽 끝에 도달 (100px 여유)
                if (scrollLeft < 100 && !isLoading) {
                    isLoading = true;
                    const currentScrollLeft = scrollLeft;
                    $wire.loadMorePrevious().then((result) => {
                        if (result && result.scrollTarget) {
                            scrollToDate(result.scrollTarget);
                        }
                        isLoading = false;
                    });
                }

                // 오른쪽 끝에 도달 (100px 여유)
                if (scrollLeft + clientWidth > scrollWidth - 100 && !isLoading) {
                    isLoading = true;
                    $wire.loadMoreNext().then((result) => {
                        if (result && result.scrollTarget) {
                            scrollToDate(result.scrollTarget);
                        }
                        isLoading = false;
                    });
                }
             "
             x-init="
                // 현재 날짜 셀로 스크롤
                $nextTick(() => {
                    const todayCell = $el.querySelector('[data-is-today=true]');
                    if (todayCell) {
                        const containerWidth = $el.clientWidth;
                        const cellLeft = todayCell.offsetLeft;
                        const cellWidth = todayCell.offsetWidth;
                        // 현재 날짜를 중앙에 위치
                        $el.scrollLeft = cellLeft - (containerWidth / 2) + (cellWidth / 2);
                    }
                });
             ">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; min-width: 1200px;">
                <!-- Header: Days -->
                <thead>
                    <!-- Month Row -->
                    <tr>
                        <th rowspan="2" style="position: sticky; left: 0; z-index: 20; background-color: #F9FBFC; padding: 12px; border: 1px solid #e5e7eb; font-weight: 600; color: #374151; text-align: left; min-width: 100px;">
                            호실
                        </th>
                        @php
                            $monthGroups = [];
                            $currentMonth = null;
                            $monthStart = 0;

                            foreach($days as $index => $day) {
                                if ($currentMonth !== $day['month']) {
                                    if ($currentMonth !== null) {
                                        $monthGroups[] = [
                                            'month' => $currentMonth,
                                            'colspan' => $index - $monthStart
                                        ];
                                    }
                                    $currentMonth = $day['month'];
                                    $monthStart = $index;
                                }
                            }
                            // 마지막 월 추가
                            if ($currentMonth !== null) {
                                $monthGroups[] = [
                                    'month' => $currentMonth,
                                    'colspan' => count($days) - $monthStart
                                ];
                            }
                        @endphp

                        @foreach($monthGroups as $group)
                            <th colspan="{{ $group['colspan'] }}" style="padding: 8px 4px; border: 1px solid #e5e7eb; font-weight: 600; color: #374151; text-align: center; font-size: 14px; background-color: #F9FBFC;">
                                {{ $group['month'] }}월
                            </th>
                        @endforeach
                    </tr>

                    <!-- Day Row -->
                    <tr>
                        @foreach($days as $day)
                            <th data-is-today="{{ $day['isToday'] ? 'true' : 'false' }}" style="padding: 8px 4px; border: 1px solid #e5e7eb; font-weight: 600; text-align: center; min-width: 40px; font-size: 12px;
                                {{ $day['isToday'] ? 'background-color: rgba(45, 212, 191, 0.2); color: #2dd4bf;' : ($day['dayOfWeek'] == 6 ? 'background-color: #dbeafe; color: #374151;' : ($day['dayOfWeek'] == 0 ? 'background-color: #fee2e2; color: #374151;' : 'background-color: #F9FBFC; color: #374151;')) }}">
                                {{ $day['day'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <!-- Body: Rooms & Events -->
                <tbody>
                    @foreach($rooms as $room)
                        <tr>
                            <!-- Room Header -->
                            <td @mouseup="endBarDrag({{ $room['id'] }})"
                                :class="barDragging && draggedBarStartRoom !== {{ $room['id'] }} ? 'room-drop-target' : ''"
                                style="position: sticky; left: 0; z-index: 60; padding: 12px; border: 1px solid #e5e7eb; font-weight: 500; color: #374151; isolation: isolate; min-height: 60px; height: 60px; vertical-align: middle; background-color: #F9FBFC;">
                                <style>
                                    .room-drop-target {
                                        background-color: #e0f2fe !important;
                                        cursor: copy !important;
                                    }
                                </style>
                                {{ $room['room_number'] }}호
                            </td>

                            <!-- Days Grid -->
                            @foreach($days as $dayIndex => $day)
                                @php
                                    // 이 날짜에 이 호실에 해당하는 입주자 찾기
                                    $matchedTenant = null;
                                    $isStartOutsideRange = false; // 표시 범위 밖에서 시작된 일정인지

                                    foreach ($tenants as $t) {
                                        if ($t['room_id'] != $room['id']) continue;

                                        $dateMatch = $day['date'] >= $t['move_in_date'] &&
                                               ($t['move_out_date'] === null || $day['date'] <= $t['move_out_date']);

                                        if ($dateMatch) {
                                            $matchedTenant = $t;
                                            break;
                                        }
                                    }

                                    $tenant = $matchedTenant;

                                    // 입주 시작일인지 확인
                                    $isStart = $tenant && $day['date'] == $tenant['move_in_date'];

                                    // 첫 번째 날짜인데 입주자가 있지만 시작일이 아니면, 범위 밖에서 시작된 것
                                    if ($dayIndex == 0 && $tenant && !$isStart) {
                                        $isStartOutsideRange = true;
                                        $isStart = true; // 박스를 그리기 위해 시작으로 처리
                                    }
                                @endphp

                                <td wire:key="cell-{{ $room['id'] }}-{{ $day['date'] }}"
                                    style="padding: 0 !important;
                                           border: 1px solid #e5e7eb;
                                           height: 60px !important;
                                           min-height: 60px !important;
                                           max-height: 60px !important;
                                           width: 40px;
                                           min-width: 40px;
                                           max-width: 40px;"
                                    :style="isInDragRange({{ $room['id'] }}, '{{ $day['date'] }}') ? 'background-color: #a5f3fc !important;' : '{{ $day['isToday'] ? 'background-color: rgba(45, 212, 191, 0.2);' : ($day['dayOfWeek'] == 6 ? 'background-color: #dbeafe;' : ($day['dayOfWeek'] == 0 ? 'background-color: #fee2e2;' : 'background-color: white;')) }}'"
                                    @mousedown="startDrag({{ $room['id'] }}, '{{ $day['date'] }}', $event)"
                                    @mouseenter="onDrag({{ $room['id'] }}, '{{ $day['date'] }}')"
                                    @mouseup="endBarDrag({{ $room['id'] }})"
                                    data-room-id="{{ $room['id'] }}"
                                    data-date="{{ $day['date'] }}"
                                    data-day="{{ $day['day'] }}"
                                    data-is-today="{{ $day['isToday'] ? 'true' : 'false' }}"
                                    data-has-tenant="{{ $isStart ? 'yes' : 'no' }}">

                                    <div style="position: relative !important;
                                                width: 100%;
                                                height: 100%;
                                                overflow: visible !important;
                                                pointer-events: none !important;"
                                         data-cell-wrapper>

                                    @if($isStart)
                                        <!-- Tenant Event Bar -->
                                        @php
                                            $start = \Carbon\Carbon::parse($tenant['move_in_date'])->startOfDay();
                                            $end = $tenant['move_out_date'] ? \Carbon\Carbon::parse($tenant['move_out_date'])->startOfDay() : \Carbon\Carbon::parse($endDate)->startOfDay();

                                            // 날짜 차이 계산 (시작일과 종료일 포함)
                                            // 종료일까지 포함하려면 종료일 다음날까지의 차이를 계산
                                            $duration = $start->diffInDays($end->copy()->addDay());

                                            // 표시 범위의 마지막 날짜
                                            $displayEnd = \Carbon\Carbon::parse($endDate)->startOfDay();
                                            $endDateInRange = $end->lessThanOrEqualTo($displayEnd) ? $end : $displayEnd;
                                            // 정확한 일수 계산: 시작일부터 종료일까지 포함
                                            $displayDuration = $start->diffInDays($endDateInRange->copy()->addDay());

                                            // 각 셀의 실제 너비 = 40px
                                            $cellWidth = 40;
                                            $calculatedWidth = $displayDuration * $cellWidth;

                                            \Log::info("박스 너비 계산:", [
                                                'tenant' => $tenant['name'],
                                                'start' => $start->format('Y-m-d'),
                                                'end' => $end->format('Y-m-d'),
                                                'displayEnd' => $displayEnd->format('Y-m-d'),
                                                'endDateInRange' => $endDateInRange->format('Y-m-d'),
                                                'duration' => $duration,
                                                'displayDuration' => $displayDuration,
                                                'currentDay' => $day['day'],
                                                'calculatedWidth' => $calculatedWidth . 'px',
                                                'color' => $tenant['color']
                                            ]);
                                        @endphp

                                        <div x-data="{
                                                barDragging: false,
                                                barResizing: false,
                                                resizeType: null,
                                                startX: 0,
                                                baseWidth: {{ $calculatedWidth }},
                                                currentWidth: {{ $calculatedWidth }},
                                                currentLeft: 1,
                                                isEditMode: false,

                                                get startWidth() {
                                                    return this.baseWidth;
                                                },

                                                get startLeft() {
                                                    return 1;
                                                },

                                                isEditing() {
                                                    return window.editingTenantId === {{ $tenant['id'] }};
                                                },

                                                checkEditMode() {
                                                    this.isEditMode = window.editingTenantId === {{ $tenant['id'] }};
                                                },

                                                startBarDrag(e) {
                                                    // 편집 모드가 아니면 드래그 불가
                                                    console.log('startBarDrag 체크:', {
                                                        editingTenantId: window.editingTenantId,
                                                        thisTenantId: {{ $tenant['id'] }},
                                                        canDrag: window.editingTenantId === {{ $tenant['id'] }}
                                                    });
                                                    if (window.editingTenantId !== {{ $tenant['id'] }}) {
                                                        console.log('드래그 거부됨');
                                                        return;
                                                    }
                                                    console.log('드래그 허용됨');
                                                    if (this.barResizing) return;
                                                    this.barDragging = true;
                                                    this.startX = e.clientX;
                                                    this.startLeft = this.currentLeft;

                                                    // 전역 드래그 상태 설정
                                                    window.dispatchEvent(new CustomEvent('bar-drag-start', {
                                                        detail: {
                                                            tenantId: {{ $tenant['id'] }},
                                                            roomId: {{ $room['id'] }},
                                                            tenantData: {
                                                                name: '{{ $tenant['name'] }}',
                                                                color: '{{ $tenant['color'] }}',
                                                                moveInDate: '{{ $tenant['move_in_date'] }}',
                                                                moveOutDate: '{{ $tenant['move_out_date'] ?? '' }}',
                                                                displayDuration: {{ $displayDuration }}
                                                            }
                                                        }
                                                    }));

                                                    e.stopPropagation();
                                                    e.preventDefault();
                                                },

                                                onBarDragMove(e) {
                                                    if (!this.barDragging) return;
                                                    const diff = e.clientX - this.startX;
                                                    this.currentLeft = this.startLeft + diff;
                                                },

                                                endBarDrag(e) {
                                                    if (!this.barDragging) return;
                                                    this.barDragging = false;

                                                    // 이동한 거리를 날짜로 변환 (40px per day)
                                                    const cellWidth = 40;
                                                    const daysMoved = Math.round((this.currentLeft - 1) / cellWidth);

                                                    if (daysMoved !== 0) {
                                                        const currentEditingId = window.editingTenantId; // 현재 편집 중인 ID 저장
                                                        // Livewire 업데이트 - wire:key 변경으로 자동 재렌더링됨
                                                        $wire.updateTenantDates({{ $tenant['id'] }}, daysMoved).then(() => {
                                                            // 업데이트 후 편집 모드 복원
                                                            if (currentEditingId) {
                                                                window.editingTenantId = currentEditingId;
                                                                console.log('편집 모드 복원됨:', currentEditingId);
                                                                // 편집 모드 변경 이벤트 발생
                                                                window.dispatchEvent(new CustomEvent('edit-mode-changed'));
                                                            }
                                                        });
                                                    }
                                                    // 원위치는 제거 - 새로 렌더링되므로 불필요
                                                },

                                                startBarResize(type, e) {
                                                    // 편집 모드가 아니면 리사이즈 불가
                                                    console.log('startBarResize 체크:', {
                                                        editingTenantId: window.editingTenantId,
                                                        thisTenantId: {{ $tenant['id'] }},
                                                        canResize: window.editingTenantId === {{ $tenant['id'] }}
                                                    });
                                                    if (window.editingTenantId !== {{ $tenant['id'] }}) {
                                                        console.log('리사이즈 거부됨');
                                                        return;
                                                    }
                                                    console.log('리사이즈 허용됨');
                                                    this.barResizing = true;
                                                    this.resizeType = type;
                                                    this.startX = e.clientX;
                                                    // 현재 표시된 값이 아닌 베이스 값 사용
                                                    e.stopPropagation();
                                                    e.preventDefault();
                                                },

                                                onBarResizeMove(e) {
                                                    if (!this.barResizing) return;
                                                    const diff = e.clientX - this.startX;

                                                    if (this.resizeType === 'left') {
                                                        this.currentLeft = 1 + diff;
                                                        this.currentWidth = this.baseWidth - diff;
                                                    } else if (this.resizeType === 'right') {
                                                        this.currentWidth = this.baseWidth + diff;
                                                    }
                                                },

                                                endBarResize(e) {
                                                    if (!this.barResizing) return;
                                                    this.barResizing = false;

                                                    const cellWidth = 40;
                                                    const daysChanged = Math.round((this.currentWidth - this.baseWidth) / cellWidth);
                                                    const daysMovedLeft = Math.round((this.currentLeft - 1) / cellWidth);

                                                    if (daysChanged !== 0 || daysMovedLeft !== 0) {
                                                        const currentEditingId = window.editingTenantId; // 현재 편집 중인 ID 저장
                                                        // Livewire 업데이트 - wire:key 변경으로 자동 재렌더링됨
                                                        $wire.resizeTenantDates({{ $tenant['id'] }}, this.resizeType, daysChanged, daysMovedLeft).then(() => {
                                                            // 업데이트 후 편집 모드 복원
                                                            if (currentEditingId) {
                                                                window.editingTenantId = currentEditingId;
                                                                console.log('편집 모드 복원됨 (리사이즈):', currentEditingId);
                                                                // 편집 모드 변경 이벤트 발생
                                                                window.dispatchEvent(new CustomEvent('edit-mode-changed'));
                                                            }
                                                        });
                                                    }
                                                    // 원위치는 제거 - 새로 렌더링되므로 불필요
                                                }
                                             }"
                                             @mousemove.window="barDragging ? onBarDragMove($event) : (barResizing ? onBarResizeMove($event) : null)"
                                             @mouseup.window="barDragging ? endBarDrag($event) : (barResizing ? endBarResize($event) : null)"
                                             @contextmenu.prevent.stop="$dispatch('open-context-menu', { tenantId: {{ $tenant['id'] }}, x: $event.clientX, y: $event.clientY })"
                                             @edit-mode-changed.window="checkEditMode()"
                                             x-init="checkEditMode()"
                                             wire:key="tenant-bar-{{ $tenant['id'] }}-{{ $tenant['move_in_date'] }}-{{ $tenant['move_out_date'] }}-{{ $calculatedWidth }}"
                                             data-tenant-bar="visible"
                                             data-tenant-id="{{ $tenant['id'] }}"
                                             data-width="{{ $calculatedWidth }}"
                                             :style="`position: absolute !important;
                                                    top: 50% !important;
                                                    left: ${currentLeft}px !important;
                                                    transform: translateY(-50%) !important;
                                                    height: 32px !important;
                                                    {{ $isStartOutsideRange ? 'background: linear-gradient(to right, transparent, ' . $tenant['color'] . ' 20px) !important;' : 'background-color: ' . $tenant['color'] . ' !important;' }}
                                                    border-radius: 4px !important;
                                                    padding: 4px 8px !important;
                                                    color: white !important;
                                                    font-size: 12px !important;
                                                    font-weight: 500 !important;
                                                    overflow: visible !important;
                                                    text-overflow: ellipsis !important;
                                                    white-space: nowrap !important;
                                                    cursor: ${barDragging || barResizing ? 'grabbing' : (isEditing() ? 'grab' : 'context-menu')} !important;
                                                    z-index: 50 !important;
                                                    width: ${currentWidth}px !important;
                                                    box-shadow: ${isEditing() ? '0 0 0 3px rgba(59, 130, 246, 0.5), 0 2px 4px rgba(0,0,0,0.2)' : '0 2px 4px rgba(0,0,0,0.2)'} !important;
                                                    display: flex !important;
                                                    align-items: center !important;
                                                    border: 2px solid ${isEditing() ? 'rgba(59, 130, 246, 0.8)' : 'rgba(255,255,255,0.3)'} !important;
                                                    pointer-events: auto !important;`">

                                            <!-- Left Resize Handle -->
                                            @if(!$isStartOutsideRange)
                                            <div x-show="isEditMode"
                                                 @mousedown.stop="startBarResize('left', $event)"
                                                 data-resize-handle="left"
                                                 style="position: absolute;
                                                        left: -2px;
                                                        top: 0;
                                                        bottom: 0;
                                                        width: 8px;
                                                        cursor: ew-resize !important;
                                                        background: rgba(255,255,255,0.6);
                                                        border-radius: 4px 0 0 4px;
                                                        z-index: 51;
                                                        display: none;">
                                            </div>
                                            @endif

                                            <!-- Content (draggable area) -->
                                            <div @mousedown.stop="startBarDrag($event)"
                                                 @dblclick.stop="$wire.editTenant({{ $tenant['id'] }})"
                                                 data-draggable-bar="true"
                                                 style="flex: 1; display: flex; align-items: center; justify-content: center; overflow: hidden; text-overflow: ellipsis; user-select: none; white-space: nowrap;">
                                                {{ $tenant['name'] }} ({{ $tenant['room_number'] }}호) [{{ $displayDuration }}일]
                                            </div>

                                            <!-- Right Resize Handle -->
                                            <div x-show="isEditMode"
                                                 @mousedown.stop="startBarResize('right', $event)"
                                                 data-resize-handle="right"
                                                 style="position: absolute;
                                                        right: -2px;
                                                        top: 0;
                                                        bottom: 0;
                                                        width: 8px;
                                                        cursor: ew-resize !important;
                                                        background: rgba(255,255,255,0.6);
                                                        border-radius: 0 4px 4px 0;
                                                        z-index: 51;
                                                        display: none;">
                                            </div>
                                        </div>
                                    @endif

                                    </div><!-- end position relative wrapper -->
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Legend -->
        <div style="margin-top: 16px; display: flex; gap: 16px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <div style="width: 16px; height: 16px; background-color: #10b981; border-radius: 3px;"></div>
                <span style="font-size: 13px; color: #6b7280;">납부완료</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <div style="width: 16px; height: 16px; background-color: #f59e0b; border-radius: 3px;"></div>
                <span style="font-size: 13px; color: #6b7280;">미납</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <div style="width: 16px; height: 16px; background-color: #ef4444; border-radius: 3px;"></div>
                <span style="font-size: 13px; color: #6b7280;">연체</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <div style="width: 16px; height: 16px; background-color: #9ca3af; border-radius: 3px;"></div>
                <span style="font-size: 13px; color: #6b7280;">대기</span>
            </div>
        </div>
    </div>
</div>
