<div x-data="{
    isDragging: false,
    dragStartRoomId: null,
    dragStartDate: null,
    dragEndDate: null,

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
    },

    onDrag(roomId, date) {
        if (!this.isDragging || this.dragStartRoomId !== roomId) return;
        this.dragEndDate = date;
    },

    endDrag() {
        if (this.isDragging && this.dragStartRoomId && this.dragStartDate && this.dragEndDate) {
            const startDate = this.dragStartDate < this.dragEndDate ? this.dragStartDate : this.dragEndDate;
            const endDate = this.dragStartDate > this.dragEndDate ? this.dragStartDate : this.dragEndDate;
            $wire.openCreateModal(this.dragStartRoomId, startDate, endDate);
        }
        this.isDragging = false;
        this.dragStartRoomId = null;
        this.dragStartDate = null;
        this.dragEndDate = null;
    },

    isInDragRange(roomId, date) {
        if (!this.isDragging || this.dragStartRoomId !== roomId) return false;
        const start = this.dragStartDate < this.dragEndDate ? this.dragStartDate : this.dragEndDate;
        const end = this.dragStartDate > this.dragEndDate ? this.dragStartDate : this.dragEndDate;
        return date >= start && date <= end;
    }
}"
    @mouseup.window="endDrag()"
    @tenant-created.window="console.log('tenant-created 이벤트 수신'); $wire.$refresh()"
    x-init="console.log('TenantScheduler 초기화:', { branchId: '{{ $branchId }}', tenants: @js($tenants) })">
    <!-- Scheduler Container -->
    <div style="background-color: white; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); padding: 32px;">
        <!-- Title Section & Controls -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div>
                <h2 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0 0 8px 0;">입주자 일정 관리</h2>
                <p style="font-size: 14px; color: #4b5563; margin: 0;">호실별 입주자 일정을 확인하고 관리할 수 있습니다.</p>
            </div>

            <!-- Month Navigation -->
            <div style="display: flex; gap: 12px; align-items: center;">
                <button wire:click="previousMonth" type="button" style="padding: 8px 16px; background-color: white; border: 1px solid #d1d5db; border-radius: 8px; cursor: pointer; font-weight: 500; color: #374151;">
                    ← 이전
                </button>
                <button wire:click="today" type="button" style="padding: 8px 16px; background-color: #2dd4bf; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; color: white;">
                    오늘
                </button>
                <span style="font-size: 16px; font-weight: 600; color: #111827; min-width: 120px; text-align: center;">
                    {{ $currentYear }}년 {{ $currentMonth }}월
                </span>
                <button wire:click="nextMonth" type="button" style="padding: 8px 16px; background-color: white; border: 1px solid #d1d5db; border-radius: 8px; cursor: pointer; font-weight: 500; color: #374151;">
                    다음 →
                </button>
            </div>
        </div>

        <!-- Scheduler Table -->
        <div style="border: 1px solid #e5e7eb; border-radius: 8px; overflow-x: auto; position: relative; isolation: isolate;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; min-width: 1200px; position: relative;">
                <!-- Header: Days -->
                <thead>
                    <tr>
                        <th style="position: sticky; left: 0; z-index: 20; background-color: #F9FBFC; padding: 12px; border: 1px solid #e5e7eb; font-weight: 600; color: #374151; text-align: left; min-width: 100px;">
                            호실
                        </th>
                        @foreach($days as $day)
                            <th style="padding: 8px 4px; border: 1px solid #e5e7eb; font-weight: 600; color: #374151; text-align: center; min-width: 40px; font-size: 12px;
                                {{ $day['dayOfWeek'] == 6 ? 'background-color: #dbeafe;' : ($day['dayOfWeek'] == 0 ? 'background-color: #fee2e2;' : 'background-color: #F9FBFC;') }}">
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
                            <td style="position: sticky; left: 0; z-index: 10; background-color: #F9FBFC; padding: 12px; border: 1px solid #e5e7eb; font-weight: 500; color: #374151; isolation: isolate;">
                                {{ $room['room_number'] }}호
                            </td>

                            <!-- Days Grid -->
                            @foreach($days as $dayIndex => $day)
                                @php
                                    // 이 날짜에 이 호실에 해당하는 입주자 찾기
                                    $matchedTenant = null;

                                    foreach ($tenants as $t) {
                                        // 디버깅: room_id 비교 (첫 번째 날짜만)
                                        if ($dayIndex == 0) {
                                            \Log::info("Room ID 비교 - Room: {$room['id']}, Tenant: {$t['name']}, Tenant Room: {$t['room_id']}, Match: " . ($t['room_id'] == $room['id'] ? 'YES' : 'NO'));
                                        }

                                        if ($t['room_id'] != $room['id']) continue;

                                        $dateMatch = $day['date'] >= $t['move_in_date'] &&
                                               ($t['move_out_date'] === null || $day['date'] <= $t['move_out_date']);

                                        if ($dateMatch && $dayIndex == 0) {
                                            \Log::info("날짜 매칭 성공 - Day: {$day['date']}, Move In: {$t['move_in_date']}, Move Out: {$t['move_out_date']}");
                                        }

                                        if ($dateMatch) {
                                            $matchedTenant = $t;
                                            break;
                                        }
                                    }

                                    $tenant = $matchedTenant;

                                    // 입주 시작일인지 확인
                                    $isStart = $tenant && $day['date'] == $tenant['move_in_date'];

                                    if ($isStart) {
                                        \Log::info("입주 시작일 발견! - Room: {$room['room_number']}, Tenant: {$tenant['name']}, Date: {$day['date']}");
                                    }
                                @endphp

                                <td wire:key="cell-{{ $room['id'] }}-{{ $day['date'] }}"
                                    style="padding: 0 !important;
                                           border: 1px solid #e5e7eb;
                                           height: 40px;
                                           width: 40px;
                                           min-width: 40px;
                                           max-width: 40px;"
                                    :style="isInDragRange({{ $room['id'] }}, '{{ $day['date'] }}') ? 'background-color: #a5f3fc !important;' : '{{ $day['dayOfWeek'] == 6 ? 'background-color: #dbeafe;' : ($day['dayOfWeek'] == 0 ? 'background-color: #fee2e2;' : 'background-color: white;') }}'"
                                    data-room-id="{{ $room['id'] }}"
                                    data-date="{{ $day['date'] }}"
                                    data-day="{{ $day['day'] }}"
                                    data-has-tenant="{{ $isStart ? 'yes' : 'no' }}">

                                    <div style="position: relative !important;
                                                width: 100%;
                                                height: 100%;
                                                overflow: visible !important;"
                                         @mousedown="startDrag({{ $room['id'] }}, '{{ $day['date'] }}', $event)"
                                         @mouseenter="onDrag({{ $room['id'] }}, '{{ $day['date'] }}')">

                                    @if($isStart)
                                        <!-- Tenant Event Bar -->
                                        @php
                                            $start = \Carbon\Carbon::parse($tenant['move_in_date']);
                                            $end = $tenant['move_out_date'] ? \Carbon\Carbon::parse($tenant['move_out_date']) : \Carbon\Carbon::create($currentYear, $currentMonth, count($days));

                                            // 날짜 차이 계산 (시작일과 종료일 포함)
                                            $duration = $start->diffInDays($end) + 1;

                                            // 현재 월에서 표시할 수 있는 최대 일수
                                            $monthEnd = \Carbon\Carbon::create($currentYear, $currentMonth, count($days));
                                            $endDateInMonth = $end->lessThanOrEqualTo($monthEnd) ? $end : $monthEnd;
                                            $displayDuration = $start->diffInDays($endDateInMonth) + 1;

                                            // 각 셀의 실제 너비는 width(40px) + border(1px 좌우) + spacing = 약 53px
                                            // 7일 = 370px이므로 역산하면 약 52.86px per cell
                                            $cellWidth = 53; // 실제 렌더링되는 셀 너비
                                            $calculatedWidth = ($displayDuration * $cellWidth) - 2;

                                            \Log::info("박스 너비 계산:", [
                                                'tenant' => $tenant['name'],
                                                'start' => $start->format('Y-m-d'),
                                                'end' => $end->format('Y-m-d'),
                                                'monthEnd' => $monthEnd->format('Y-m-d'),
                                                'endDateInMonth' => $endDateInMonth->format('Y-m-d'),
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

                                                get startWidth() {
                                                    return this.baseWidth;
                                                },

                                                get startLeft() {
                                                    return 1;
                                                },

                                                startBarDrag(e) {
                                                    if (this.barResizing) return;
                                                    this.barDragging = true;
                                                    this.startX = e.clientX;
                                                    this.startLeft = this.currentLeft;
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

                                                    // 이동한 거리를 날짜로 변환 (53px per day)
                                                    const cellWidth = 53;
                                                    const daysMoved = Math.round((this.currentLeft - 1) / cellWidth);

                                                    if (daysMoved !== 0) {
                                                        // Livewire 업데이트 - wire:key 변경으로 자동 재렌더링됨
                                                        $wire.updateTenantDates({{ $tenant['id'] }}, daysMoved);
                                                    }
                                                    // 원위치는 제거 - 새로 렌더링되므로 불필요
                                                },

                                                startBarResize(type, e) {
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

                                                    const cellWidth = 53;
                                                    const daysChanged = Math.round((this.currentWidth - this.baseWidth) / cellWidth);
                                                    const daysMovedLeft = Math.round((this.currentLeft - 1) / cellWidth);

                                                    if (daysChanged !== 0 || daysMovedLeft !== 0) {
                                                        // Livewire 업데이트 - wire:key 변경으로 자동 재렌더링됨
                                                        $wire.resizeTenantDates({{ $tenant['id'] }}, this.resizeType, daysChanged, daysMovedLeft);
                                                    }
                                                    // 원위치는 제거 - 새로 렌더링되므로 불필요
                                                }
                                             }"
                                             @mousemove.window="barDragging ? onBarDragMove($event) : (barResizing ? onBarResizeMove($event) : null)"
                                             @mouseup.window="barDragging ? endBarDrag($event) : (barResizing ? endBarResize($event) : null)"
                                             wire:key="tenant-bar-{{ $tenant['id'] }}-{{ $tenant['move_in_date'] }}-{{ $tenant['move_out_date'] }}-{{ $calculatedWidth }}"
                                             data-tenant-bar="visible"
                                             data-tenant-id="{{ $tenant['id'] }}"
                                             data-width="{{ $calculatedWidth }}"
                                             :style="`position: absolute !important;
                                                    top: 50% !important;
                                                    left: ${currentLeft}px !important;
                                                    transform: translateY(-50%) !important;
                                                    height: 32px !important;
                                                    background-color: {{ $tenant['color'] }} !important;
                                                    border-radius: 4px !important;
                                                    padding: 4px 8px !important;
                                                    color: white !important;
                                                    font-size: 12px !important;
                                                    font-weight: 500 !important;
                                                    overflow: visible !important;
                                                    text-overflow: ellipsis !important;
                                                    white-space: nowrap !important;
                                                    cursor: ${barDragging || barResizing ? 'grabbing' : 'grab'} !important;
                                                    z-index: 50 !important;
                                                    width: ${currentWidth}px !important;
                                                    box-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
                                                    display: flex !important;
                                                    align-items: center !important;
                                                    border: 2px solid rgba(255,255,255,0.3) !important;`">

                                            <!-- Left Resize Handle -->
                                            <div @mousedown.stop="startBarResize('left', $event)"
                                                 data-resize-handle="left"
                                                 style="position: absolute;
                                                        left: -2px;
                                                        top: 0;
                                                        bottom: 0;
                                                        width: 8px;
                                                        cursor: ew-resize !important;
                                                        background: rgba(255,255,255,0.4);
                                                        border-radius: 4px 0 0 4px;
                                                        z-index: 51;">
                                            </div>

                                            <!-- Content (draggable area) -->
                                            <div @mousedown.stop="startBarDrag($event)"
                                                 @dblclick.stop="$wire.editTenant({{ $tenant['id'] }})"
                                                 data-draggable-bar="true"
                                                 style="flex: 1; overflow: hidden; text-overflow: ellipsis; user-select: none;">
                                                {{ $tenant['name'] }} ({{ $tenant['room_number'] }}호) [{{ $displayDuration }}일]
                                            </div>

                                            <!-- Right Resize Handle -->
                                            <div @mousedown.stop="startBarResize('right', $event)"
                                                 data-resize-handle="right"
                                                 style="position: absolute;
                                                        right: -2px;
                                                        top: 0;
                                                        bottom: 0;
                                                        width: 8px;
                                                        cursor: ew-resize !important;
                                                        background: rgba(255,255,255,0.4);
                                                        border-radius: 0 4px 4px 0;
                                                        z-index: 51;">
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
        </div>
    </div>
</div>
