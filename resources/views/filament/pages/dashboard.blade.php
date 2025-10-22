<x-filament-panels::page>
    @php
        $user = auth()->user();
        $currentBranchId = session('current_branch_id', $user->branches->first()?->id);
        $branch = $currentBranchId ? \App\Models\Branch::find($currentBranchId) : null;
        $currentDateCarbon = \Carbon\Carbon::parse($currentDate);
    @endphp

    <!-- 헤더 섹션 -->
    <div style="margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <h1 style="font-size: 1.25rem; font-weight: 600; color: #1f2937;">{{ $branch ? $branch->name : '대시보드' }}</h1>

            <!-- 캘린더 아이콘 버튼 -->
            <div style="position: relative;">
                <button
                    wire:click="toggleCalendar"
                    style="display: inline-flex; justify-content: center; align-items: center; gap: 0.5rem; white-space: nowrap; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; outline: none; height: 2.25rem; padding: 0.25rem; margin: -0.25rem; background: transparent; border: none; border-radius: 0.5rem; cursor: pointer;"
                    type="button"
                    onmouseover="this.querySelector('svg').style.stroke='#40c0c0'"
                    onmouseout="this.querySelector('svg').style.stroke='#000'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 1.25rem; width: 1.25rem; color: #000; transition: all 0.2s;">
                        <path d="M8 2v4"></path>
                        <path d="M16 2v4"></path>
                        <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                        <path d="M3 10h18"></path>
                    </svg>
                </button>

                <!-- 캘린더 팝오버 -->
                @if($showCalendarPopover)
                <div
                    style="position: absolute; left: 0; top: 100%; margin-top: 0.5rem; z-index: 50; background: white; border: 1px solid #e5e7eb; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); padding: 1.5rem; min-width: 280px;"
                    x-data
                    @click.away="$wire.set('showCalendarPopover', false)"
                >
                    <!-- 캘린더 헤더 -->
                    <div style="display: flex; justify-content: center; align-items: center; padding-top: 0.25rem; position: relative; margin-bottom: 0.5rem;">
                        <button
                            wire:click="previousMonth"
                            style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; white-space: nowrap; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; transition: all 0.2s; outline: none; height: 1.75rem; width: 1.75rem; background: transparent; padding: 0; opacity: 0.5; position: absolute; left: 0.25rem; border: 1px solid #d1d5db; cursor: pointer;"
                            type="button"
                            onmouseover="this.style.opacity='1'; this.style.backgroundColor='#f3f4f6';"
                            onmouseout="this.style.opacity='0.5'; this.style.backgroundColor='transparent';"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 1rem; width: 1rem;">
                                <path d="m15 18-6-6 6-6"></path>
                            </svg>
                        </button>
                        <div style="font-size: 0.875rem; font-weight: 500;">{{ $currentMonth }}월 {{ $currentYear }}</div>
                        <button
                            wire:click="nextMonth"
                            style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; white-space: nowrap; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; transition: all 0.2s; outline: none; height: 1.75rem; width: 1.75rem; background: transparent; padding: 0; opacity: 0.5; position: absolute; right: 0.25rem; border: 1px solid #d1d5db; cursor: pointer;"
                            type="button"
                            onmouseover="this.style.opacity='1'; this.style.backgroundColor='#f3f4f6';"
                            onmouseout="this.style.opacity='0.5'; this.style.backgroundColor='transparent';"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 1rem; width: 1rem;">
                                <path d="m9 18 6-6-6-6"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- 캘린더 그리드 -->
                    <div style="display: grid; grid-template-columns: repeat(7, 2.25rem); gap: 0.125rem;">
                        <!-- 요일 헤더 -->
                        <div style="color: #6b7280; text-align: center; font-weight: 400; font-size: 0.8rem; padding: 0.25rem 0;">일</div>
                        <div style="color: #6b7280; text-align: center; font-weight: 400; font-size: 0.8rem; padding: 0.25rem 0;">월</div>
                        <div style="color: #6b7280; text-align: center; font-weight: 400; font-size: 0.8rem; padding: 0.25rem 0;">화</div>
                        <div style="color: #6b7280; text-align: center; font-weight: 400; font-size: 0.8rem; padding: 0.25rem 0;">수</div>
                        <div style="color: #6b7280; text-align: center; font-weight: 400; font-size: 0.8rem; padding: 0.25rem 0;">목</div>
                        <div style="color: #6b7280; text-align: center; font-weight: 400; font-size: 0.8rem; padding: 0.25rem 0;">금</div>
                        <div style="color: #6b7280; text-align: center; font-weight: 400; font-size: 0.8rem; padding: 0.25rem 0;">토</div>

                        @php
                            $firstDay = \Carbon\Carbon::create($currentYear, $currentMonth, 1);
                            $lastDay = $firstDay->copy()->endOfMonth();
                            $startOfWeek = $firstDay->copy()->startOfWeek();
                            $endOfWeek = $lastDay->copy()->endOfWeek();
                            $today = now()->format('Y-m-d');
                            $calendarDate = $startOfWeek->copy();
                        @endphp

                        @while($calendarDate <= $endOfWeek)
                            @php
                                $dateStr = $calendarDate->format('Y-m-d');
                                $isCurrentMonth = $calendarDate->month == $currentMonth;
                                $isToday = $dateStr == $today;
                                $isSelected = $dateStr == $currentDate;
                            @endphp

                            <div style="height: 2.25rem; width: 2.25rem; text-align: center; font-size: 0.875rem; padding: 0;">
                                <button
                                    wire:click="selectCalendarDate('{{ $dateStr }}')"
                                    style="display: inline-flex; align-items: center; justify-content: center; white-space: nowrap; border-radius: 0.375rem; font-size: 0.875rem; transition: all 0.2s; outline: none; height: 2.25rem; width: 2.25rem; padding: 0; font-weight: 400; border: none; cursor: pointer; {{ $isCurrentMonth ? 'color: #374151;' : 'color: #d1d5db;' }} {{ $isToday ? 'background-color: #f3f4f6; font-weight: 600;' : 'background-color: transparent;' }} {{ $isSelected && !$isToday ? 'background-color: #f3f4f6;' : '' }}"
                                    type="button"
                                    onmouseover="if (this.style.backgroundColor === 'transparent' || this.style.backgroundColor === '') this.style.backgroundColor='#f9fafb';"
                                    onmouseout="this.style.backgroundColor='{{ $isToday || $isSelected ? '#f3f4f6' : 'transparent' }}';"
                                >
                                    {{ $calendarDate->day }}
                                </button>
                            </div>

                            @php
                                $calendarDate->addDay();
                            @endphp
                        @endwhile
                    </div>
                </div>
                @endif
            </div>

            <!-- 이전 날짜 버튼 -->
            <button
                wire:click="previousDay"
                style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; white-space: nowrap; font-size: 0.875rem; font-weight: 500; transition: all 0.2s; outline: none; height: 2.25rem; padding: 0.25rem; border: none; background: transparent; border-radius: 9999px; cursor: pointer;"
                aria-label="이전 날짜로 이동"
                data-testid="button-prev-day"
                onmouseover="this.style.backgroundColor='#f3f4f6';"
                onmouseout="this.style.backgroundColor='transparent';"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 1rem; width: 1rem; color: #4b5563;">
                    <path d="m15 18-6-6 6-6"></path>
                </svg>
            </button>

            <!-- 현재 날짜 표시 -->
            <span style="font-size: 0.875rem; color: #4b5563; font-weight: 500; margin: 0 0.25rem;" data-testid="text-current-date">
                {{ $currentDateCarbon->isoFormat('YYYY. M. D. (ddd)') }}
            </span>

            <!-- 다음 날짜 버튼 -->
            <button
                wire:click="nextDay"
                style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; white-space: nowrap; font-size: 0.875rem; font-weight: 500; transition: all 0.2s; outline: none; height: 2.25rem; padding: 0.25rem; border: none; background: transparent; border-radius: 9999px; cursor: pointer;"
                aria-label="다음 날짜로 이동"
                data-testid="button-next-day"
                onmouseover="this.style.backgroundColor='#f3f4f6';"
                onmouseout="this.style.backgroundColor='transparent';"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 1rem; width: 1rem; color: #4b5563;">
                    <path d="m9 18 6-6-6-6"></path>
                </svg>
            </button>
        </div>
    </div>

    <div>
    <!-- 호실 현황 카드 -->
    <div style="background-color: #f8f8f8; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); margin-bottom: 1.5rem;">
        <div style="padding: 0.75rem; background-color: #f8f8f8; border-radius: 1rem 1rem 0 0;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <h2 style="font-size: 0.875rem; font-weight: 500; color: #374151;">🏢 호실 현황</h2>
            </div>
        </div>
        <div style="padding: 0 0.75rem 0.75rem 0.75rem;">
            <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem;">
                <!-- 총 호실 -->
                <div style="background-color: white; border-radius: 0.75rem; padding: 1rem; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);">
                    <div>
                        <p style="font-size: 0.75rem; color: #6b7280; margin: 0 0 0.25rem 0;">총 호실</p>
                        <p style="font-size: 1.5rem; font-weight: 700; color: #374151; margin: 0;">{{ $totalRooms }}</p>
                    </div>
                    <div style="width: 3rem; height: 3rem; border-radius: 0.75rem; background-color: rgba(99, 102, 241, 0.1); display: flex; align-items: center; justify-content: center;">
                        <span style="font-size: 1.5rem;">🏠</span>
                    </div>
                </div>

                <!-- 사용중 호실 -->
                <div style="background-color: white; border-radius: 0.75rem; padding: 1rem; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);">
                    <div>
                        <p style="font-size: 0.75rem; color: #6b7280; margin: 0 0 0.25rem 0;">사용중 호실</p>
                        <p style="font-size: 1.5rem; font-weight: 700; color: #374151; margin: 0;">{{ $occupiedRooms }}</p>
                    </div>
                    <div style="width: 3rem; height: 3rem; border-radius: 0.75rem; background-color: rgba(30, 195, 176, 0.1); display: flex; align-items: center; justify-content: center;">
                        <span style="font-size: 1.5rem;">👤</span>
                    </div>
                </div>

                <!-- 빈 호실 -->
                <div onclick="openAvailableRoomsModal()" style="background-color: white; border-radius: 0.75rem; padding: 1rem; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 6px 0 rgba(0, 0, 0, 0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.1)';">
                    <div>
                        <p style="font-size: 0.75rem; color: #6b7280; margin: 0 0 0.25rem 0;">빈 호실</p>
                        <p style="font-size: 1.5rem; font-weight: 700; color: #374151; margin: 0;">{{ $availableRooms }}</p>
                    </div>
                    <div style="width: 3rem; height: 3rem; border-radius: 0.75rem; background-color: rgba(34, 197, 94, 0.1); display: flex; align-items: center; justify-content: center;">
                        <span style="font-size: 1.5rem;">✅</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 오늘의 일정 카드 -->
    <div style="background-color: #f8f8f8; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); margin-bottom: 1rem;">
        <div style="padding: 0.75rem; background-color: #f8f8f8; border-radius: 1rem 1rem 0 0;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <h2 style="font-size: 0.875rem; font-weight: 500; color: #374151;">📌 오늘의 일정</h2>
                <button style="padding: 0.5rem; border-radius: 9999px; background: none; border: none; cursor: pointer;" title="일정 관리 설정">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 1rem; width: 1rem; color: #6b7280;">
                        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
        </div>
        <div style="padding: 0 0.75rem 0.75rem 0.75rem;">
            <div style="display: grid; grid-template-columns: repeat(1, minmax(0, 1fr)); gap: 1rem;">
                <style>
                    .schedule-grid {
                        display: grid !important;
                        gap: 1rem !important;
                        grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
                    }
                    @media (min-width: 768px) {
                        .schedule-grid {
                            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                        }
                    }
                    @media (min-width: 1024px) {
                        .schedule-grid {
                            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
                        }
                    }
                    .schedule-add-button:hover {
                        background-color: rgba(64, 192, 192, 0.2) !important;
                    }
                </style>
                <div class="schedule-grid">
                    <!-- 입실 예정 -->
                    <div style="background-color: white; border-radius: 0.75rem; padding: 0.75rem; min-height: 140px; display: flex; flex-direction: column;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-size: 1.125rem;">🏠</span>
                                <h3 style="font-size: 0.875rem; font-weight: 700; color: #374151;">입실 예정 ({{ count($todayCheckInScheduled) }}건)</h3>
                            </div>
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column; gap: 0.5rem; overflow-y: auto; max-height: 200px;">
                            @if(count($todayCheckInScheduled) > 0)
                                @foreach($todayCheckInScheduled as $room)
                                <div style="background-color: #f0f9ff; border-left: 3px solid #3b82f6; padding: 0.5rem; border-radius: 0.375rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;">
                                        <div style="flex: 1;">
                                            <p style="font-size: 0.75rem; font-weight: 600; color: #1e40af; margin: 0;">{{ $room->room_number }}호 ({{ $room->floor }}층)</p>
                                            <p style="font-size: 0.625rem; color: #6b7280; margin: 0.125rem 0 0 0;">{{ $room->tenant_name ?? '입주자 정보 없음' }}</p>
                                        </div>
                                        <button wire:click="completeCheckIn({{ $room->id }})" style="background-color: #3b82f6; color: white; border: none; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.625rem; cursor: pointer; white-space: nowrap;" onmouseover="this.style.backgroundColor='#2563eb';" onmouseout="this.style.backgroundColor='#3b82f6';">
                                            완료
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div style="display: flex; align-items: center; justify-content: center; flex: 1; text-align: center; color: #9ca3af; font-size: 0.75rem;">
                                    예정된 입실이 없습니다.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 입실 완료 -->
                    <div style="background-color: white; border-radius: 0.75rem; padding: 0.75rem; min-height: 140px; display: flex; flex-direction: column;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-size: 1.125rem;">☑️</span>
                                <h3 style="font-size: 0.875rem; font-weight: 700; color: #374151;">입실 완료 ({{ count($todayCheckInCompleted) }}건)</h3>
                            </div>
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column; gap: 0.5rem; overflow-y: auto; max-height: 200px;">
                            @if(count($todayCheckInCompleted) > 0)
                                @foreach($todayCheckInCompleted as $room)
                                <div style="background-color: #d1fae5; border-left: 3px solid #10b981; padding: 0.5rem; border-radius: 0.375rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;">
                                        <div style="flex: 1;">
                                            <p style="font-size: 0.75rem; font-weight: 600; color: #065f46; margin: 0;">{{ $room->room_number }}호 ({{ $room->floor }}층)</p>
                                            <p style="font-size: 0.625rem; color: #6b7280; margin: 0.125rem 0 0 0;">{{ $room->tenant_name ?? '입주자 정보 없음' }}</p>
                                        </div>
                                        <button wire:click="undoCheckIn({{ $room->id }})" style="background-color: #6b7280; color: white; border: none; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.625rem; cursor: pointer; white-space: nowrap;" onmouseover="this.style.backgroundColor='#4b5563';" onmouseout="this.style.backgroundColor='#6b7280';" title="입실 예정으로 되돌리기">
                                            ↩️
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div style="display: flex; align-items: center; justify-content: center; flex: 1; text-align: center; color: #9ca3af; font-size: 0.75rem;">
                                    완료된 입실이 없습니다.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 퇴실 예정 -->
                    <div style="background-color: white; border-radius: 0.75rem; padding: 0.75rem; min-height: 140px; display: flex; flex-direction: column;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-size: 1.125rem;">🚪</span>
                                <h3 style="font-size: 0.875rem; font-weight: 700; color: #374151;">퇴실 예정 ({{ count($todayCheckOutScheduled) }}건)</h3>
                            </div>
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column; gap: 0.5rem; overflow-y: auto; max-height: 200px;">
                            @if(count($todayCheckOutScheduled) > 0)
                                @foreach($todayCheckOutScheduled as $room)
                                <div style="background-color: #fef3c7; border-left: 3px solid #f59e0b; padding: 0.5rem; border-radius: 0.375rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;">
                                        <div style="flex: 1;">
                                            <p style="font-size: 0.75rem; font-weight: 600; color: #92400e; margin: 0;">{{ $room->room_number }}호 ({{ $room->floor }}층)</p>
                                            <p style="font-size: 0.625rem; color: #6b7280; margin: 0.125rem 0 0 0;">{{ $room->tenant_name ?? '입주자 정보 없음' }}</p>
                                        </div>
                                        <button wire:click="completeCheckOut({{ $room->id }})" style="background-color: #f59e0b; color: white; border: none; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.625rem; cursor: pointer; white-space: nowrap;" onmouseover="this.style.backgroundColor='#d97706';" onmouseout="this.style.backgroundColor='#f59e0b';">
                                            완료
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div style="display: flex; align-items: center; justify-content: center; flex: 1; text-align: center; color: #9ca3af; font-size: 0.75rem;">
                                    예정된 퇴실이 없습니다.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 퇴실 완료 -->
                    <div style="background-color: white; border-radius: 0.75rem; padding: 0.75rem; min-height: 140px; display: flex; flex-direction: column;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-size: 1.125rem;">☑️</span>
                                <h3 style="font-size: 0.875rem; font-weight: 700; color: #374151;">퇴실 완료 ({{ count($todayCheckOutCompleted) }}건)</h3>
                            </div>
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column; gap: 0.5rem; overflow-y: auto; max-height: 200px;">
                            @if(count($todayCheckOutCompleted) > 0)
                                @foreach($todayCheckOutCompleted as $room)
                                <div style="background-color: #fef3c7; border-left: 3px solid #f59e0b; padding: 0.5rem; border-radius: 0.375rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;">
                                        <div style="flex: 1;">
                                            <p style="font-size: 0.75rem; font-weight: 600; color: #92400e; margin: 0;">{{ $room->room_number }}호 ({{ $room->floor }}층)</p>
                                            <p style="font-size: 0.625rem; color: #6b7280; margin: 0.125rem 0 0 0;">{{ $room->tenant_name ?? '입주자 정보 없음' }}</p>
                                        </div>
                                        <div style="display: flex; gap: 0.25rem; flex-shrink: 0;">
                                            <button wire:click="startCleaning({{ $room->id }})" style="background-color: #8b5cf6; color: white; border: none; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.625rem; cursor: pointer; white-space: nowrap;" onmouseover="this.style.backgroundColor='#7c3aed';" onmouseout="this.style.backgroundColor='#8b5cf6';">
                                                청소 대기
                                            </button>
                                            <button wire:click="undoCheckOut({{ $room->id }})" style="background-color: #6b7280; color: white; border: none; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.625rem; cursor: pointer; white-space: nowrap;" onmouseover="this.style.backgroundColor='#4b5563';" onmouseout="this.style.backgroundColor='#6b7280';" title="퇴실 예정으로 되돌리기">
                                                ↩️
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div style="display: flex; align-items: center; justify-content: center; flex: 1; text-align: center; color: #9ca3af; font-size: 0.75rem;">
                                    완료된 퇴실이 없습니다.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 청소 대기 -->
                    <div style="background-color: white; border-radius: 0.75rem; padding: 0.75rem; min-height: 140px; display: flex; flex-direction: column;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-size: 1.125rem;">🧹</span>
                                <h3 style="font-size: 0.875rem; font-weight: 700; color: #374151;">청소 대기 ({{ count($cleaningWaiting) }}건)</h3>
                            </div>
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column; gap: 0.5rem; overflow-y: auto; max-height: 200px;">
                            @if(count($cleaningWaiting) > 0)
                                @foreach($cleaningWaiting as $room)
                                <div style="background-color: #fef3e7; border-left: 3px solid #ec4899; padding: 0.5rem; border-radius: 0.375rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;">
                                        <div style="flex: 1;">
                                            <p style="font-size: 0.75rem; font-weight: 600; color: #831843; margin: 0;">{{ $room->room_number }}호 ({{ $room->floor }}층)</p>
                                            <p style="font-size: 0.625rem; color: #6b7280; margin: 0.125rem 0 0 0;">{{ $room->tenant_name ?? '입주자 정보 없음' }}</p>
                                        </div>
                                        <div style="display: flex; gap: 0.25rem; flex-shrink: 0;">
                                            <button wire:click="completeCleaning({{ $room->id }})" style="background-color: #10b981; color: white; border: none; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.625rem; cursor: pointer; white-space: nowrap;" onmouseover="this.style.backgroundColor='#059669';" onmouseout="this.style.backgroundColor='#10b981';">
                                                청소 완료
                                            </button>
                                            <button wire:click="undoCleaningStart({{ $room->id }})" style="background-color: #6b7280; color: white; border: none; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.625rem; cursor: pointer; white-space: nowrap;" onmouseover="this.style.backgroundColor='#4b5563';" onmouseout="this.style.backgroundColor='#6b7280';" title="퇴실 완료로 되돌리기">
                                                ↩️
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div style="display: flex; align-items: center; justify-content: center; flex: 1; text-align: center; color: #9ca3af; font-size: 0.75rem;">
                                    대기 중인 청소가 없습니다.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 청소 완료 -->
                    <div style="background-color: white; border-radius: 0.75rem; padding: 0.75rem; min-height: 140px; display: flex; flex-direction: column;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-size: 1.125rem;">✨</span>
                                <h3 style="font-size: 0.875rem; font-weight: 700; color: #374151;">청소 완료 ({{ count($cleaningCompleted) }}건)</h3>
                            </div>
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column; gap: 0.5rem; overflow-y: auto; max-height: 200px;">
                            @if(count($cleaningCompleted) > 0)
                                @foreach($cleaningCompleted as $room)
                                <div style="background-color: #d1fae5; border-left: 3px solid #10b981; padding: 0.5rem; border-radius: 0.375rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;">
                                        <div style="flex: 1;">
                                            <p style="font-size: 0.75rem; font-weight: 600; color: #065f46; margin: 0;">{{ $room->room_number }}호 ({{ $room->floor }}층)</p>
                                            <p style="font-size: 0.625rem; color: #6b7280; margin: 0.125rem 0 0 0;">{{ $room->tenant_name ?? '입주자 정보 없음' }}</p>
                                        </div>
                                        <button wire:click="undoCleaning({{ $room->id }})" style="background-color: #6b7280; color: white; border: none; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.625rem; cursor: pointer; white-space: nowrap;" onmouseover="this.style.backgroundColor='#4b5563';" onmouseout="this.style.backgroundColor='#6b7280';" title="청소 대기로 되돌리기">
                                            ↩️
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div style="display: flex; align-items: center; justify-content: center; flex: 1; text-align: center; color: #9ca3af; font-size: 0.75rem;">
                                    완료된 청소가 없습니다.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 그 외 일정 -->
                    <div style="background-color: white; border-radius: 0.75rem; padding: 0.75rem; min-height: 140px; display: flex; flex-direction: column;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-size: 1.125rem;">💡</span>
                                <h3 style="font-size: 0.875rem; font-weight: 700; color: #374151;">그 외 일정 ({{ count($customSchedules) }}건)</h3>
                            </div>
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column; gap: 0.5rem; overflow-y: auto; max-height: 200px;">
                            @if(count($customSchedules) > 0)
                                @foreach($customSchedules as $schedule)
                                @php
                                    // 카테고리별 색상 설정
                                    $isOtherCategory = $schedule->category === '기타';
                                    $bgColor = $schedule->is_completed ? '#e0e7ff' : ($isOtherCategory ? '#fef3c7' : '#f3e8ff');
                                    $borderColor = $schedule->is_completed ? '#6366f1' : ($isOtherCategory ? '#eab308' : '#a855f7');
                                    $textColor = $schedule->is_completed ? '#4338ca' : ($isOtherCategory ? '#854d0e' : '#6b21a8');
                                @endphp
                                <div style="background-color: {{ $bgColor }}; border-left: 3px solid {{ $borderColor }}; padding: 0.5rem; border-radius: 0.375rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;">
                                        <div style="flex: 1; {{ $schedule->is_completed ? 'text-decoration: line-through; opacity: 0.6;' : '' }}">
                                            <p style="font-size: 0.75rem; font-weight: 600; color: {{ $textColor }}; margin: 0; word-break: break-word;">{{ $schedule->content }}</p>
                                            @if($schedule->category !== '기타')
                                                <p style="font-size: 0.625rem; color: #6b7280; margin: 0.125rem 0 0 0;">{{ $schedule->category }}</p>
                                            @endif
                                        </div>
                                        <div style="display: flex; gap: 0.25rem; flex-shrink: 0;">
                                            @if(!$schedule->is_completed)
                                            <button wire:click="completeCustomSchedule({{ $schedule->id }})" style="background-color: #10b981; color: white; border: none; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.625rem; cursor: pointer; white-space: nowrap;" onmouseover="this.style.backgroundColor='#059669';" onmouseout="this.style.backgroundColor='#10b981';">
                                                완료
                                            </button>
                                            @endif
                                            <button wire:click="deleteCustomSchedule({{ $schedule->id }})" style="background-color: #ef4444; color: white; border: none; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.625rem; cursor: pointer; white-space: nowrap;" onmouseover="this.style.backgroundColor='#dc2626';" onmouseout="this.style.backgroundColor='#ef4444';">
                                                삭제
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div style="display: flex; align-items: center; justify-content: center; flex: 1; text-align: center; color: #9ca3af; font-size: 0.75rem;">
                                    처리할 추가 일정이 없습니다.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 오늘의 일정 추가하기 -->
                    <button onclick="openScheduleModal()" class="schedule-add-button" style="background-color: rgba(64, 192, 192, 0.1); border-radius: 0.75rem; padding: 0.75rem; min-height: 140px; display: flex; flex-direction: column; transition: background-color 0.2s; cursor: pointer; border: none;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-size: 1.125rem;">➕</span>
                                <h3 style="font-size: 0.875rem; font-weight: 700; color: #374151;">오늘의 일정 추가하기</h3>
                            </div>
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            <div style="text-align: center; color: #9ca3af; font-size: 0.75rem; width: 100%;">
                                새로운 할 일을 추가해보세요.
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 월간 일정 캘린더 -->
    <div style="background-color: #f8f8f8; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); margin-bottom: 1rem;">
        <div style="padding: 0.75rem; background-color: #f8f8f8; border-radius: 1rem 1rem 0 0;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <h2 style="font-size: 0.875rem; font-weight: 500; color: #374151;">📅 월간 일정 캘린더</h2>
            </div>
        </div>
        <div style="padding: 0 0.75rem 0.75rem 0.75rem;">
            <div style="background-color: white; border-radius: 0.75rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);">
                <!-- 캘린더 헤더 -->
                <div style="position: relative; display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background-color: white; border-radius: 0.75rem 0.75rem 0 0;">
                    <div style="display: flex; align-items: center;">
                        <select wire:model.live="scheduleFilter" style="font-size: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.75rem; padding: 0.25rem 0.5rem; background-color: white; outline: none;">
                            <option value="all">모든 일정</option>
                            <option value="checkin-checkout">입퇴실</option>
                            <option value="other">기타</option>
                        </select>
                    </div>
                    <div style="position: absolute; left: 50%; transform: translateX(-50%); display: flex; align-items: center; gap: 0.5rem;">
                        <button wire:click="previousMonth" style="padding: 0.25rem; color: #6b7280; background: none; border: none; border-radius: 9999px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.color='#40c0c0'; this.style.backgroundColor='rgba(255,255,255,0.5)';" onmouseout="this.style.color='#6b7280'; this.style.backgroundColor='transparent';">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 1rem; width: 1rem;">
                                <path d="m15 18-6-6 6-6"></path>
                            </svg>
                        </button>
                        <h3 style="font-size: 0.875rem; font-weight: 500; color: #374151; white-space: nowrap;">{{ $currentYear }}년 {{ $currentMonth }}월</h3>
                        <button wire:click="nextMonth" style="padding: 0.25rem; color: #6b7280; background: none; border: none; border-radius: 9999px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.color='#40c0c0'; this.style.backgroundColor='rgba(255,255,255,0.5)';" onmouseout="this.style.color='#6b7280'; this.style.backgroundColor='transparent';">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 1rem; width: 1rem;">
                                <path d="m9 18 6-6-6-6"></path>
                            </svg>
                        </button>
                    </div>
                    <div style="display: flex; align-items: center;">
                        <button wire:click="goToToday" title="오늘 날짜로 이동" style="padding: 0.125rem; color: #6b7280; background: none; border: none; border-radius: 9999px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.color='#40c0c0'; this.style.backgroundColor='rgba(255,255,255,0.5)';" onmouseout="this.style.color='#6b7280'; this.style.backgroundColor='transparent';">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 0.875rem; width: 0.875rem;">
                                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                <path d="M3 3v5h5"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div style="border-top: 1px solid #e5e7eb;"></div>

                <!-- 캘린더 그리드 -->
                <div style="display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 0.25rem; padding: 0.75rem;">
                    <!-- 요일 헤더 -->
                    <div style="text-align: center; font-size: 0.75rem; font-weight: 500; color: #6b7280; padding: 0.5rem 0;">일</div>
                    <div style="text-align: center; font-size: 0.75rem; font-weight: 500; color: #6b7280; padding: 0.5rem 0;">월</div>
                    <div style="text-align: center; font-size: 0.75rem; font-weight: 500; color: #6b7280; padding: 0.5rem 0;">화</div>
                    <div style="text-align: center; font-size: 0.75rem; font-weight: 500; color: #6b7280; padding: 0.5rem 0;">수</div>
                    <div style="text-align: center; font-size: 0.75rem; font-weight: 500; color: #6b7280; padding: 0.5rem 0;">목</div>
                    <div style="text-align: center; font-size: 0.75rem; font-weight: 500; color: #6b7280; padding: 0.5rem 0;">금</div>
                    <div style="text-align: center; font-size: 0.75rem; font-weight: 500; color: #6b7280; padding: 0.5rem 0;">토</div>

                    @php
                        $firstDay = \Carbon\Carbon::create($currentYear, $currentMonth, 1);
                        $lastDay = $firstDay->copy()->endOfMonth();
                        $startOfWeek = $firstDay->copy()->startOfWeek();
                        $endOfWeek = $lastDay->copy()->endOfWeek();
                        $today = now()->format('Y-m-d');

                        $currentDate = $startOfWeek->copy();
                    @endphp

                    @while($currentDate <= $endOfWeek)
                        @php
                            $dateStr = $currentDate->format('Y-m-d');
                            $isCurrentMonth = $currentDate->month == $currentMonth;
                            $isToday = $dateStr == $today;
                            $isSelected = $dateStr == $selectedDate;

                            // 해당 날짜의 일정 개수
                            $hasCheckIn = isset($monthCheckIns[$dateStr]);
                            $hasCheckOut = isset($monthCheckOuts[$dateStr]);
                            $hasCustom = isset($monthCustomSchedules[$dateStr]);

                            // 커스텀 일정의 카테고리 확인
                            $customDotColor = '#eab308'; // 기본값: 노란색 (기타)
                            if ($hasCustom && isset($monthCustomSchedules[$dateStr])) {
                                $hasNonOtherCategory = $monthCustomSchedules[$dateStr]->contains(function($schedule) {
                                    return $schedule->category !== '기타';
                                });
                                if ($hasNonOtherCategory) {
                                    $customDotColor = '#a855f7'; // 보라색
                                }
                            }

                            // 필터 적용
                            if ($scheduleFilter === 'checkin-checkout') {
                                $hasCustom = false;
                            } elseif ($scheduleFilter === 'other') {
                                $hasCheckIn = false;
                                $hasCheckOut = false;
                            }

                            $hasSchedule = $hasCheckIn || $hasCheckOut || $hasCustom;
                        @endphp

                        <div
                            wire:click="selectDate('{{ $dateStr }}')"
                            style="min-height: 2rem; padding: 0.25rem; text-align: center; font-size: 0.75rem; cursor: pointer; border-radius: 0.5rem; transition: all 0.2s; {{ $isCurrentMonth ? 'color: #374151;' : 'color: #d1d5db;' }} {{ $isToday ? 'background-color: rgba(64, 192, 192, 0.1); font-weight: 600;' : '' }} {{ $isSelected && !$isToday ? 'background-color: #f3f4f6;' : '' }}"
                            onmouseover="this.style.backgroundColor='{{ $isCurrentMonth ? '#f3f4f6' : '#f9fafb' }}';"
                            onmouseout="this.style.backgroundColor='{{ $isToday ? 'rgba(64, 192, 192, 0.1)' : ($isSelected ? '#f3f4f6' : 'transparent') }}';"
                        >
                            <div style="position: relative;">
                                {{ $currentDate->day }}
                                @if($hasSchedule)
                                    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 0.25rem; margin-top: 0.25rem;">
                                        @if($hasCheckIn)
                                            <div style="width: 0.375rem; height: 0.375rem; border-radius: 9999px; background-color: #3b82f6;" title="입실 일정"></div>
                                        @endif
                                        @if($hasCheckOut)
                                            <div style="width: 0.375rem; height: 0.375rem; border-radius: 9999px; background-color: #ef4444;" title="퇴실 일정"></div>
                                        @endif
                                        @if($hasCustom)
                                            <div style="width: 0.375rem; height: 0.375rem; border-radius: 9999px; background-color: {{ $customDotColor }};" title="{{ $customDotColor === '#a855f7' ? '관리 일정' : '기타 일정' }}"></div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        @php
                            $currentDate->addDay();
                        @endphp
                    @endwhile
                </div>

                <!-- 선택된 날짜의 일정 목록 -->
                <div style="border-top: 1px solid #e5e7eb; padding: 0.75rem 0.75rem 1rem 0.75rem; background-color: white; border-radius: 0 0 0.75rem 0.75rem;">
                    @php
                        $checkInCount = $scheduleFilter !== 'other' ? count($selectedDateSchedules['checkIns'] ?? []) : 0;
                        $checkOutCount = $scheduleFilter !== 'other' ? count($selectedDateSchedules['checkOuts'] ?? []) : 0;
                        $customCount = $scheduleFilter !== 'checkin-checkout' ? count($selectedDateSchedules['customSchedules'] ?? []) : 0;
                        $totalCount = $checkInCount + $checkOutCount + $customCount;
                    @endphp
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                        <h4 style="font-size: 0.75rem; font-weight: 700; color: #374151; margin: 0;">📅 {{ \Carbon\Carbon::parse($selectedDate)->isoFormat('YYYY년 M월 D일 dddd') }} 일정 (총 {{ $totalCount }}건)</h4>
                        <button onclick="openScheduleModal()" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; white-space: nowrap; height: 1.5rem; padding: 0 0.5rem; font-size: 0.75rem; background-color: rgba(64, 192, 192, 0.1); color: #374151; font-weight: 700; border: none; border-radius: 9999px; margin-top: 0.25rem; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.backgroundColor='rgba(64, 192, 192, 0.2)';" onmouseout="this.style.backgroundColor='rgba(64, 192, 192, 0.1)';">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 0.75rem; height: 0.75rem; margin-right: -0.125rem;">
                                <path d="M5 12h14"></path>
                                <path d="M12 5v14"></path>
                            </svg>
                            일정 추가
                        </button>
                    </div>
                    <div style="padding: 0.5rem; border-radius: 0.5rem; background-color: white;">
                        @if($totalCount > 0)
                            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                @if($scheduleFilter !== 'other')
                                    @foreach($selectedDateSchedules['checkIns'] ?? [] as $room)
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <div style="width: 0.375rem; height: 0.375rem; border-radius: 9999px; background-color: #3b82f6;"></div>
                                            <div style="font-size: 0.75rem; font-weight: 500;">입실: {{ $room->room_number }}호 ({{ $room->tenant_name ?? '입주자 정보 없음' }})</div>
                                        </div>
                                    @endforeach
                                    @foreach($selectedDateSchedules['checkOuts'] ?? [] as $room)
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <div style="width: 0.375rem; height: 0.375rem; border-radius: 9999px; background-color: #ef4444;"></div>
                                            <div style="font-size: 0.75rem; font-weight: 500;">퇴실: {{ $room->room_number }}호 ({{ $room->tenant_name ?? '입주자 정보 없음' }})</div>
                                        </div>
                                    @endforeach
                                @endif
                                @if($scheduleFilter !== 'checkin-checkout')
                                    @foreach($selectedDateSchedules['customSchedules'] ?? [] as $schedule)
                                        @php
                                            $isOtherCategory = $schedule->category === '기타';
                                            $dotColor = $isOtherCategory ? '#eab308' : '#a855f7';
                                        @endphp
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <div style="width: 0.375rem; height: 0.375rem; border-radius: 9999px; background-color: {{ $dotColor }};"></div>
                                            <div style="flex: 1;">
                                                <div style="font-size: 0.75rem; font-weight: 500;">{{ $schedule->content }}</div>
                                                @if($schedule->category !== '기타')
                                                    <div style="font-size: 0.625rem; color: #6b7280; margin-top: 0.125rem;">{{ $schedule->category }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @else
                            <div style="text-align: center; color: #9ca3af; font-size: 0.75rem; padding: 0.5rem 0;">
                                일정이 없습니다.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 빈 호실 상세 모달 -->
    <div id="availableRoomsModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 50; background-color: rgba(0, 0, 0, 0.5); align-items: center; justify-content: center;">
        <div style="position: relative; background: white; border-radius: 1rem; max-width: 56rem; width: 90%; max-height: 80vh; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
            <!-- 모달 헤더 -->
            <div style="padding: 1.5rem; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between;">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: #374151; margin: 0;">
                    ✅ 빈 호실 목록 ({{ $availableRooms }}건)
                </h3>
                <button onclick="closeAvailableRoomsModal()" style="background: none; border: none; cursor: pointer; color: #6b7280; padding: 0.5rem; border-radius: 0.375rem; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f3f4f6';" onmouseout="this.style.backgroundColor='transparent';">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 1.5rem; height: 1.5rem;">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- 모달 콘텐츠 -->
            <div style="padding: 1.5rem; overflow-y: auto; max-height: calc(80vh - 8rem);">
                @if($availableRoomsList && count($availableRoomsList) > 0)
                    <div style="display: grid; gap: 1rem;">
                        @foreach($availableRoomsList as $room)
                        <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem; transition: all 0.2s;" onmouseover="this.style.borderColor='#22c55e'; this.style.backgroundColor='#f0fdf4';" onmouseout="this.style.borderColor='#e5e7eb'; this.style.backgroundColor='#f9fafb';">
                            <div style="display: flex; align-items: start; justify-content: space-between; gap: 1rem;">
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                                        <h4 style="font-size: 1.125rem; font-weight: 700; color: #374151; margin: 0;">{{ $room->room_number }}호</h4>
                                        <span style="background-color: #22c55e; color: white; font-size: 0.75rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 500;">{{ $room->statusLabel }}</span>
                                    </div>

                                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                                        <div>
                                            <p style="font-size: 0.75rem; color: #6b7280; margin: 0 0 0.25rem 0;">층수</p>
                                            <p style="font-size: 0.875rem; font-weight: 600; color: #374151; margin: 0;">{{ $room->floor }}층</p>
                                        </div>
                                        <div>
                                            <p style="font-size: 0.75rem; color: #6b7280; margin: 0 0 0.25rem 0;">호실 타입</p>
                                            <p style="font-size: 0.875rem; font-weight: 600; color: #374151; margin: 0;">{{ $room->room_type }}</p>
                                        </div>
                                        <div>
                                            <p style="font-size: 0.75rem; color: #6b7280; margin: 0 0 0.25rem 0;">월세</p>
                                            <p style="font-size: 0.875rem; font-weight: 600; color: #374151; margin: 0;">{{ number_format($room->monthly_rent) }}원</p>
                                        </div>
                                        <div>
                                            <p style="font-size: 0.75rem; color: #6b7280; margin: 0 0 0.25rem 0;">보증금</p>
                                            <p style="font-size: 0.875rem; font-weight: 600; color: #374151; margin: 0;">{{ number_format($room->deposit) }}원</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 3rem 1rem; color: #6b7280;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 1rem; color: #d1d5db;">
                            <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                            <path d="M3 9h18"></path>
                            <path d="M9 21V9"></path>
                        </svg>
                        <p style="font-size: 1rem; font-weight: 500; margin: 0;">빈 호실이 없습니다</p>
                    </div>
                @endif
            </div>

            <!-- 모달 푸터 -->
            <div style="padding: 1rem 1.5rem; border-top: 1px solid #e5e7eb; background-color: #f9fafb; display: flex; justify-content: flex-end;">
                <button onclick="closeAvailableRoomsModal()" style="background-color: #6b7280; color: white; border: none; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; cursor: pointer; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#4b5563';" onmouseout="this.style.backgroundColor='#6b7280';">
                    닫기
                </button>
            </div>
        </div>
    </div>

    <!-- 일정 추가 모달 -->
    <div id="scheduleModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 50; background-color: rgba(0, 0, 0, 0.5); align-items: center; justify-content: center;">
        <div style="position: relative; background: white; border-radius: 1.5rem; max-width: 28rem; width: 90%; padding: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border: 2px solid #e5e7eb;">
            <!-- 모달 헤더 -->
            <div style="display: flex; flex-direction: column; margin-bottom: 0.375rem; text-align: center;">
                <h2 style="font-size: 1.125rem; font-weight: 600; color: #374151; margin: 0;">오늘의 일정 추가하기</h2>
            </div>

            <!-- 모달 콘텐츠 -->
            <div style="padding: 1rem 0;">
                <label style="cursor: pointer; opacity: 1; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;" for="category-select">
                    카테고리
                </label>
                <select
                    wire:model="customScheduleCategory"
                    id="category-select"
                    style="display: flex; border: 1px solid #d1d5db; background: white; padding: 0.5rem 0.75rem; font-size: 0.875rem; width: 100%; border-radius: 0.75rem; outline: none; margin-bottom: 1rem;"
                    onfocus="this.style.borderColor='#40c0c0'; this.style.outline='none';"
                    onblur="this.style.borderColor='#d1d5db';"
                >
                    <option value="기타">기타</option>
                    <option value="공용 공간 청소 및 위생관리">공용 공간 청소 및 위생관리</option>
                    <option value="설비 점검 및 유지보수">설비 점검 및 유지보수</option>
                    <option value="안전 및 보안 관리">안전 및 보안 관리</option>
                    <option value="소모품 관리 및 교체">소모품 관리 및 교체</option>
                    <option value="하자 및 민원 처리">하자 및 민원 처리</option>
                    <option value="시설 개선 및 리모델링">시설 개선 및 리모델링</option>
                    <option value="계절성 작업">계절성 작업</option>
                </select>

                <label style="cursor: pointer; opacity: 1; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;" for="todo-input">
                    추가할 일정을 입력하세요.
                </label>
                <textarea
                    wire:model="customScheduleContent"
                    id="todo-input"
                    placeholder="추가하고 싶은 일정 내용을 입력하세요."
                    maxlength="50"
                    style="display: flex; border: 1px solid #d1d5db; background: white; padding: 0.5rem 0.75rem; font-size: 0.875rem; width: 100%; min-height: 100px; border-radius: 0.75rem; resize: none; outline: none;"
                    onfocus="this.style.borderColor='#40c0c0'; this.style.outline='none';"
                    onblur="this.style.borderColor='#d1d5db';"
                    oninput="updateCharCount(this)"
                ></textarea>
                <div id="charCount" style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem; text-align: right;">0/50</div>
            </div>

            <!-- 모달 버튼 -->
            <div style="display: flex; gap: 0.75rem; justify-content: center;">
                <button
                    onclick="closeScheduleModal()"
                    style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; white-space: nowrap; font-weight: 500; border: 1px solid #d1d5db; background: white; height: 2.5rem; border-radius: 9999px; padding: 0.5rem 1.5rem; font-size: 0.875rem; cursor: pointer; transition: all 0.2s;"
                    onmouseover="this.style.backgroundColor='#f3f4f6'; this.style.color='#000';"
                    onmouseout="this.style.backgroundColor='white'; this.style.color='inherit';"
                >
                    취소
                </button>
                <button
                    wire:click="addCustomSchedule"
                    onclick="closeScheduleModal()"
                    id="submitSchedule"
                    style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; white-space: nowrap; font-weight: 500; height: 2.5rem; border-radius: 9999px; padding: 0.5rem 1.5rem; font-size: 0.875rem; background-color: #50d0d0; color: white; border: none; cursor: pointer; transition: all 0.2s;"
                    onmouseover="this.style.backgroundColor='#40c0c0';"
                    onmouseout="this.style.backgroundColor='#50d0d0';"
                >
                    완료
                </button>
            </div>

            <!-- 닫기 버튼 -->
            <button
                onclick="closeScheduleModal()"
                type="button"
                style="position: absolute; right: 1rem; top: 1rem; border-radius: 0.125rem; opacity: 0.7; background: none; border: none; cursor: pointer; transition: opacity 0.2s; padding: 0.25rem;"
                onmouseover="this.style.opacity='1';"
                onmouseout="this.style.opacity='0.7';"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 1rem; width: 1rem;">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- JavaScript for modal functionality -->
    <script>
        function openAvailableRoomsModal() {
            const modal = document.getElementById('availableRoomsModal');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeAvailableRoomsModal() {
            const modal = document.getElementById('availableRoomsModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('availableRoomsModal')?.addEventListener('click', function(event) {
            if (event.target === this) {
                closeAvailableRoomsModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const availableModal = document.getElementById('availableRoomsModal');
                const scheduleModal = document.getElementById('scheduleModal');

                if (availableModal && availableModal.style.display === 'flex') {
                    closeAvailableRoomsModal();
                } else if (scheduleModal && scheduleModal.style.display === 'flex') {
                    closeScheduleModal();
                }
            }
        });

        // Schedule modal functions
        function openScheduleModal() {
            const modal = document.getElementById('scheduleModal');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            document.getElementById('todo-input').focus();
        }

        function closeScheduleModal() {
            const modal = document.getElementById('scheduleModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            document.getElementById('todo-input').value = '';
            document.getElementById('charCount').textContent = '0/50';
        }

        function updateCharCount(textarea) {
            const count = textarea.value.length;
            document.getElementById('charCount').textContent = count + '/50';
        }

        // Close schedule modal when clicking outside
        document.getElementById('scheduleModal')?.addEventListener('click', function(event) {
            if (event.target === this) {
                closeScheduleModal();
            }
        });
    </script>
    </div>
</x-filament-panels::page>
