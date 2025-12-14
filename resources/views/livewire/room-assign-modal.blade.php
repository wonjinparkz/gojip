<div>
    @if($show)
    <div style="position: fixed; inset: 0; z-index: 9999; overflow-y: auto;">
        <!-- Backdrop -->
        <div wire:click="close" style="position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5);"></div>

        <!-- Modal Content -->
        <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 16px;">
            <div style="background-color: white; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); width: 100%; max-width: 448px; padding: 24px; position: relative; z-index: 10000;">

                <!-- Modal Header -->
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                    <h3 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0;">호실 배정</h3>
                    <button wire:click="close" type="button" style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px;">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                @if($tenant)
                <!-- Tenant Info -->
                <div style="margin-bottom: 24px; padding: 16px; background-color: #f9fafb; border-radius: 8px;">
                    <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 8px;">{{ $tenant->name }}</div>
                    <div style="font-size: 12px; color: #6b7280;">
                        @if($hasDates)
                            <div>입실일: {{ $tenant->move_in_date ? $tenant->move_in_date->format('Y.m.d') : '-' }}</div>
                            <div>퇴실일: {{ $tenant->move_out_date ? $tenant->move_out_date->format('Y.m.d') : '미정' }}</div>
                        @else
                            <div style="color: #f59e0b;">입실일/퇴실일이 지정되지 않았습니다.</div>
                            <div style="margin-top: 4px;">호실 선택 시 입실일은 오늘, 퇴실일은 미정으로 설정됩니다.</div>
                        @endif
                    </div>
                </div>

                <!-- Room Selection -->
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">
                        호실 선택 *
                        @if($hasDates)
                            <span style="font-size: 12px; color: #6b7280; font-weight: 400; margin-left: 8px;">(입주 가능한 호실만 표시)</span>
                        @endif
                    </label>
                    <select
                        wire:model="selectedRoomId"
                        style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background-color: white; cursor: pointer;"
                        onfocus="this.style.outline='2px solid #2dd4bf'; this.style.borderColor='transparent';"
                        onblur="this.style.outline='none'; this.style.borderColor='#d1d5db';">
                        <option value="">호실을 선택하세요</option>
                        @forelse($availableRooms as $room)
                            <option value="{{ $room['id'] }}">
                                {{ $room['room_number'] }}호 - {{ $room['room_type'] ?? '스탠다드룸' }} ({{ number_format($room['monthly_rent']) }}원)
                            </option>
                        @empty
                            <option value="" disabled>입주 가능한 호실이 없습니다</option>
                        @endforelse
                    </select>
                    @if(count($availableRooms) === 0)
                        <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">
                            선택한 날짜에 입주 가능한 호실이 없습니다. 날짜를 변경해주세요.
                        </span>
                    @endif
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 12px;">
                    <button type="button"
                            wire:click="close"
                            style="flex: 1; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; color: #374151; font-weight: 500; background-color: white; cursor: pointer; transition: background-color 0.15s;"
                            onmouseover="this.style.backgroundColor='#f9fafb';"
                            onmouseout="this.style.backgroundColor='white';">
                        취소
                    </button>
                    <button type="button"
                            wire:click="assignToRoom"
                            style="flex: 1; padding: 12px 16px; background-color: #2dd4bf; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background-color 0.15s;"
                            onmouseover="this.style.backgroundColor='#14b8a6';"
                            onmouseout="this.style.backgroundColor='#2dd4bf';">
                        배정
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
