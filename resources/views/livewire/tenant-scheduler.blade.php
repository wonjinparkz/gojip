<div style="width: 100%; height: 100%; max-width: 100%; overflow: hidden; display: flex; flex-direction: column;"
     x-data="{
    isDragging: false,
    dragStartRoomId: null,
    dragStartDate: null,
    dragEndDate: null,
    dragEndRoomId: null,
    barDragging: false,
    draggedTenantId: null,
    draggedBarStartRoom: null,
    draggedTenantData: null,
    contextMenuX: 0,
    contextMenuY: 0,
    contextMenuTenantId: null,
    editingTenantId: null, // 현재 편집 중인 입주자 ID
    
    // 외부에서 주입된 선택된 대기자 ID
    selectedWaitingTenantId: @js($selectedWaitingTenantId),
    
    // 모바일 선택 상태
    selectedRoomId: null,
    selectedDate: null,

    isEditingTenant(tenantId) {
        return this.editingTenantId === tenantId;
    },
    
    // 모바일 셀 클릭 핸들러
    handleCellClick(roomId, date, event) {
        // 모바일 환경에서만 동작 (1024px 미만)
        if (window.matchMedia('(min-width: 1024px)').matches) return;
        
        // 이미 선택된 셀을 다시 클릭하면 선택 해제
        if (this.selectedRoomId === roomId && this.selectedDate === date) {
            this.selectedRoomId = null;
            this.selectedDate = null;
            return;
        }
        
        this.selectedRoomId = roomId;
        this.selectedDate = date;
        
        // 부모 컴포넌트(ListTenants)로 이벤트 발송
        $dispatch('schedule-cell-clicked', { roomId: roomId, date: date });
    },

    startDrag(roomId, date, event) {
        // 박스나 리사이즈 핸들을 클릭한 경우 셀 드래그 비활성화
        if (event.target.closest('[data-tenant-bar]') ||
            event.target.closest('[data-resize-handle]') ||
            event.target.closest('[data-draggable-bar]')) {
            return;
        }
        
        // 모바일 환경에서는 드래그 생성 비활성화
        if (window.matchMedia('(max-width: 1023px)').matches) return;

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

    <!-- Legend (Moved to top) -->
    <div style="margin-bottom: 16px; display: flex; gap: 16px; flex-wrap: wrap; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <div style="width: 16px; height: 16px; background-color: #2ECC71; border-radius: 3px;"></div>
                <span style="font-size: 13px; color: #6b7280;">입실 중</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <div style="width: 16px; height: 16px; background-color: #BFF5D1; border-radius: 3px;"></div>
                <span style="font-size: 13px; color: #6b7280;">입실 예정</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <div style="width: 16px; height: 16px; background-color: #FCA5A5; border-radius: 3px;"></div>
                <span style="font-size: 13px; color: #6b7280;">퇴실 지연</span>
            </div>
        </div>
        
        <!-- Return to Today Button -->
        <button wire:click="returnToToday" 
                @click="$nextTick(() => { 
                    const todayCell = document.querySelector('[data-is-today=true]'); 
                    if (todayCell) { 
                        const container = document.getElementById('scheduler-container');
                        const containerWidth = container.clientWidth; 
                        const cellLeft = todayCell.offsetLeft; 
                        const cellWidth = todayCell.offsetWidth; 
                        container.scrollLeft = cellLeft - (containerWidth / 2) + (cellWidth / 2); 
                    } 
                })"
                style="padding: 8px 16px; background-color: transparent; color: #6b7280; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500; transition: all 0.2s; display: flex; align-items: center; gap: 6px;"
                onmouseover="this.style.backgroundColor='#f3f4f6';"
                onmouseout="this.style.backgroundColor='transparent';">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
            오늘 날짜로 돌아가기
        </button>
    </div>

    <!-- Scheduler Container -->
    <div style="width: 100%; height: 100%; display: flex; flex-direction: column; overflow: hidden;">

        <!-- Month/Year Display Block (Above Table) -->
        <div id="current-month-display" 
             style="background-color: white; 
                    border: 1px solid #f3f4f6;
                    border-bottom: 0;
                    border-radius: 12px 12px 0 0;
                    padding: 12px 24px; 
                    text-align: center;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
            <span id="month-display-text" style="font-size: 16px; font-weight: 600; color: #1f2937;">
                @php
                    $today = \Carbon\Carbon::now('Asia/Seoul');
                @endphp
                {{ $today->year }}년 {{ $today->month }}월
            </span>
        </div>

        <!-- Scheduler Table -->
        <div id="scheduler-container"
             style="border: 1px solid #f3f4f6; 
                    border-radius: 0 0 8px 8px; 
                    overflow-x: auto; 
                    overflow-y: auto; 
                    position: relative; 
                    isolation: isolate; 
                    flex: 1;"
             x-data="{
                isLoading: false,
                updateCurrentMonth() {
                    const container = document.getElementById('scheduler-container');
                    const centerX = container.scrollLeft + (container.clientWidth / 2);
                    
                    // Find the date cell at center position
                    const dateCells = container.querySelectorAll('[data-date]');
                    let centerCell = null;
                    
                    for (let cell of dateCells) {
                        const cellLeft = cell.offsetLeft;
                        const cellRight = cellLeft + cell.offsetWidth;
                        
                        if (centerX >= cellLeft && centerX <= cellRight) {
                            centerCell = cell;
                            break;
                        }
                    }
                    
                    if (centerCell) {
                        const dateStr = centerCell.getAttribute('data-date');
                        if (dateStr) {
                            const date = new Date(dateStr);
                            const year = date.getFullYear();
                            const month = date.getMonth() + 1;
                            const monthText = year + '년 ' + month + '월';
                            
                            // Update month display
                            const displayText = document.getElementById('month-display-text');
                            if (displayText) {
                                displayText.textContent = monthText;
                            }
                        }
                    }
                },
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
                updateCurrentMonth();
                
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
                        // 월 헤더 업데이트
                        updateCurrentMonth();
                    }
                });
             ">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; min-width: 1200px;">
                <!-- Header: Days -->
                <thead>
                    <!-- Day Row -->
                    <tr>
                        <th style="position: sticky; 
                                   left: 0; 
                                   top: 0;
                                   z-index: 25; 
                                   background-color: #ffffff; 
                                   padding: 12px;
                                   border-right: 1px solid #f3f4f6;
                                   border-bottom: 1px solid #f3f4f6;
                                   font-weight: 600; 
                                   color: #374151; 
                                   text-align: center; 
                                   min-width: 100px;">
                            호실
                        </th>
                        @foreach($days as $dayIndex => $day)
                            @php
                                $weekDays = ['일', '월', '화', '수', '목', '금', '토'];
                                $weekDayName = $weekDays[$day['dayOfWeek']];
                                $isWeekend = $day['dayOfWeek'] == 0 || $day['dayOfWeek'] == 6;
                            @endphp
                            <th data-is-today="{{ $day['isToday'] ? 'true' : 'false' }}" 
                                data-date="{{ $day['date'] }}"
                                style="position: sticky;
                                       top: 0;
                                       z-index: 10;
                                       border-bottom: 1px solid #f3f4f6;
                                       border-left: 0;
                                       border-right: 0;
                                       border-top: 0;
                                       padding: 8px 4px; 
                                       min-width: 60px; 
                                       background-color: white; 
                                       vertical-align: middle;">
                                <div style="text-align: center;">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 2px;">
                                        <div style="width: 32px; 
                                                    height: 32px; 
                                                    display: flex; 
                                                    align-items: center; 
                                                    justify-content: center; 
                                                    font-size: 15px; 
                                                    font-weight: 600; 
                                                    color: {{ $day['isToday'] ? 'white' : ($isWeekend ? '#ef4444' : '#1f2937') }};
                                                    {{ $day['isToday'] ? 'background-color: #ef4444; border-radius: 50%;' : '' }}">
                                            {{ $day['day'] }}
                                        </div>
                                        <div style="font-size: 11px; color: {{ $day['isToday'] ? '#ef4444' : ($isWeekend ? '#ef4444' : '#9ca3af') }}; font-weight: 500;">
                                            {{ $weekDayName }}
                                        </div>
                                    </div>
                                </div>
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
                                style="position: sticky; left: 0; z-index: 60; padding: 12px; border-right: 1px solid #f3f4f6; border-bottom: 1px solid #f3f4f6; font-weight: 500; color: #374151; isolation: isolate; min-height: 47px; height: 47px; vertical-align: middle; text-align: center; background-color: #ffffff;">
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

                                    // 최적화: 전체 tenants 루프 대신 해당 호실의 입주자만 순회
                                    // $tenantsByRoom은 배열이므로 해당 키가 있는지 확인
                                    $roomTenants = $tenantsByRoom[$room['id']] ?? [];

                                    foreach ($roomTenants as $t) {
                                        // $tenantsByRoom으로 이미 필터링 되었으므로 room_id 체크 불필요

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
                                    style="padding: 4px !important;
                                           border-bottom: 1px solid #e5e7eb;
                                           border-right: 1px solid #e5e7eb;
                                           border-left: 0;
                                           border-top: 0;
                                           height: 47px !important;
                                           min-height: 47px !important;
                                           max-height: 47px !important;
                                           width: 61px;
                                           min-width: 61px;
                                           max-width: 61px;"
                                    :style="
                                        (selectedRoomId === {{ $room['id'] }} && selectedDate === '{{ $day['date'] }}') 
                                            ? 'background-color: #fef3c7 !important; border: 2px solid #f59e0b !important; box-sizing: border-box;' 
                                            : (isInDragRange({{ $room['id'] }}, '{{ $day['date'] }}') 
                                                ? 'background-color: #a5f3fc !important; border-bottom: 1px solid #e5e7eb; border-right: 1px solid #e5e7eb; border-left: 0; border-top: 0;' 
                                                : 'background-color: white; border-bottom: 1px solid #e5e7eb; border-right: 1px solid #e5e7eb; border-left: 0; border-top: 0;')
                                    "
                                    @click="handleCellClick({{ $room['id'] }}, '{{ $day['date'] }}', $event)"
                                    @mousedown="startDrag({{ $room['id'] }}, '{{ $day['date'] }}', $event)"
                                    @mouseenter="onDrag({{ $room['id'] }}, '{{ $day['date'] }}')"
                                    @mouseup="endBarDrag({{ $room['id'] }})"
                                    @dragover.prevent="$event.currentTarget.style.backgroundColor='#bfdbfe'"
                                    @dragleave="$event.currentTarget.style.backgroundColor=''"
                                    @drop.prevent="
                                        const tenantId = $event.dataTransfer.getData('tenantId');
                                        if (tenantId) {
                                            $event.currentTarget.style.backgroundColor='';
                                            const moveInDate = $event.dataTransfer.getData('moveInDate') || '{{ $day['date'] }}';
                                            const moveOutDate = $event.dataTransfer.getData('moveOutDate') || '{{ $day['date'] }}';
                                            $wire.openCreateModal({{ $room['id'] }}, moveInDate, moveOutDate, tenantId);
                                        }
                                    "
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
                                             data-occupancy-status="{{ $tenant['occupancy_status'] }}"
                                             data-overdue-days="{{ $tenant['overdue_days'] }}"
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
            
            <!-- Today's Date Indicator (Red Line) -->
            @php
                $todayIndex = collect($days)->search(function($day) {
                    return $day['isToday'];
                });
            @endphp
            
            @if($todayIndex !== false)
                <!-- Red Circle at top -->
                <div style="position: absolute; 
                            top: 65px; 
                            left: {{ 100 + ($todayIndex * 61) + 27 }}px; 
                            width: 8px; 
                            height: 8px; 
                            background-color: #EF4444; 
                            border-radius: 50%;
                            z-index: 15; 
                            pointer-events: none;">
                </div>
                <!-- Vertical Line -->
                <div style="position: absolute; 
                            top: 73px; 
                            bottom: 0; 
                            left: {{ 100 + ($todayIndex * 61) + 30.5 }}px; 
                            width: 2px; 
                            background-color: #EF4444; 
                            z-index: 5; 
                            pointer-events: none;">
                </div>
            @endif
        </div>
    </div>
    
    <!-- Tooltip Container -->
    <div x-data="{ 
            showTooltip: false, 
            tooltipX: 0, 
            tooltipY: 0, 
            tooltipText: '' 
        }"
        @mouseover.window="
            if ($event.target.closest('[data-tenant-bar]')) {
                const bar = $event.target.closest('[data-tenant-bar]');
                const status = bar.getAttribute('data-occupancy-status');
                const overdueDays = bar.getAttribute('data-overdue-days');
                
                if (status === 'overdue') {
                    tooltipText = `퇴실 처리가 필요합니다.\n퇴실일로부터 지난 날짜: +${overdueDays}`;
                } else if (status === 'reserved') {
                    tooltipText = '입실 예정입니다.';
                } else {
                    tooltipText = '';
                    return;
                }
                
                showTooltip = true;
                tooltipX = $event.clientX + 10;
                tooltipY = $event.clientY + 10;
            }
        "
        @mousemove.window="
            if (showTooltip) {
                tooltipX = $event.clientX + 10;
                tooltipY = $event.clientY + 10;
            }
        "
        @mouseout.window="
            if (!$event.target.closest('[data-tenant-bar]')) {
                showTooltip = false;
            }
        ">
        
        <!-- Tooltip Display -->
        <div x-show="showTooltip"
             x-transition
             :style="`position: fixed; 
                      top: ${tooltipY}px; 
                      left: ${tooltipX}px; 
                      background-color: rgba(0, 0, 0, 0.85); 
                      color: white; 
                      padding: 8px 12px; 
                      border-radius: 6px; 
                      font-size: 12px; 
                      white-space: pre-line; 
                      z-index: 1000; 
                      pointer-events: none; 
                      max-width: 250px;
                      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);`"
             style="display: none;">
            <span x-text="tooltipText"></span>
        </div>
    </div>
</div>
