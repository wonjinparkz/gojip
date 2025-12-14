<div>
    {{-- View Type and Navigation Section --}}
    <div style="margin-bottom: 1rem;">
        {{-- Type Switcher with Calendar Icon --}}
        <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 0.75rem; gap: 0.5rem;">
            <div style="display: flex; background-color: #E8E8E8; border-radius: 0.75rem; padding: 0.25rem; width: fit-content;">
                <button
                    wire:click="$set('type', 'daily')"
                    style="padding: 0.375rem 1.5rem; font-size: 0.875rem; border-radius: 0.75rem; transition: all 0.2s; border: none; cursor: pointer; {{ $type === 'daily' ? 'background-color: #000000; color: #ffffff; font-weight: 600;' : 'background-color: transparent; color: #6b7280; font-weight: 500;' }}"
                    onmouseover="if(this.style.backgroundColor === 'transparent') this.style.color='#374151';"
                    onmouseout="if(this.style.backgroundColor === 'transparent') this.style.color='#6b7280';"
                    data-testid="button-view-daily">
                    일간
                </button>
                <button
                    wire:click="$set('type', 'weekly')"
                    style="padding: 0.375rem 1.5rem; font-size: 0.875rem; border-radius: 0.75rem; transition: all 0.2s; border: none; cursor: pointer; {{ $type === 'weekly' ? 'background-color: #000000; color: #ffffff; font-weight: 600;' : 'background-color: transparent; color: #6b7280; font-weight: 500;' }}"
                    onmouseover="if(this.style.backgroundColor === 'transparent') this.style.color='#374151';"
                    onmouseout="if(this.style.backgroundColor === 'transparent') this.style.color='#6b7280';"
                    data-testid="button-view-weekly">
                    주간
                </button>
                <button
                    wire:click="$set('type', 'monthly')"
                    style="padding: 0.375rem 1.5rem; font-size: 0.875rem; border-radius: 0.75rem; transition: all 0.2s; border: none; cursor: pointer; {{ $type === 'monthly' ? 'background-color: #000000; color: #ffffff; font-weight: 600;' : 'background-color: transparent; color: #6b7280; font-weight: 500;' }}"
                    onmouseover="if(this.style.backgroundColor === 'transparent') this.style.color='#374151';"
                    onmouseout="if(this.style.backgroundColor === 'transparent') this.style.color='#6b7280';"
                    data-testid="button-view-monthly">
                    월간
                </button>
            </div>

            {{-- Calendar Icon Button --}}
            <div style="position: relative;">
                <button
                    wire:click="toggleCalendar"
                    style="display: inline-flex; justify-content: center; align-items: center; gap: 0.5rem; white-space: nowrap; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; outline: none; height: 2.25rem; width: 2.25rem; padding: 0.25rem; background: transparent; border: none; border-radius: 0.5rem; cursor: pointer;"
                    type="button"
                    onmouseover="this.querySelector('svg').style.stroke='#40c0c0'"
                    onmouseout="this.querySelector('svg').style.stroke='#6b7280'"
                    title="캘린더 보기">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 1.25rem; width: 1.25rem; color: #6b7280; transition: all 0.2s;">
                        <path d="M8 2v4"></path>
                        <path d="M16 2v4"></path>
                        <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                        <path d="M3 10h18"></path>
                    </svg>
                </button>

                {{-- Calendar Popover --}}
                @if($showCalendarPopover)
                <div
                    style="position: absolute; right: 0; top: 100%; margin-top: 0.5rem; z-index: 50; background: white; border: 1px solid #e5e7eb; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); padding: 1.5rem; min-width: 280px;"
                    x-data
                    @click.away="$wire.set('showCalendarPopover', false)"
                >
                    {{-- Calendar Header --}}
                    <div style="display: flex; justify-content: center; align-items: center; padding-top: 0.25rem; position: relative; margin-bottom: 0.5rem;">
                        <button
                            wire:click="previousCalendarMonth"
                            style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; white-space: nowrap; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; transition: all 0.2s; outline: none; height: 1.75rem; width: 1.75rem; background: transparent; padding: 0; opacity: 0.5; position: absolute; left: 0.25rem; border: 1px solid #d1d5db; cursor: pointer;"
                            type="button"
                            onmouseover="this.style.opacity='1'; this.style.backgroundColor='#f3f4f6';"
                            onmouseout="this.style.opacity='0.5'; this.style.backgroundColor='transparent';"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 1rem; width: 1rem;">
                                <path d="m15 18-6-6 6-6"></path>
                            </svg>
                        </button>
                        <div style="font-size: 0.875rem; font-weight: 500;">{{ $calendarMonth }}월 {{ $calendarYear }}</div>
                        <button
                            wire:click="nextCalendarMonth"
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

                    {{-- Calendar Grid --}}
                    <div style="display: grid; grid-template-columns: repeat(7, 2.25rem); gap: 0.125rem;">
                        {{-- Day Headers --}}
                        <div style="color: #6b7280; text-align: center; font-weight: 400; font-size: 0.8rem; padding: 0.25rem 0;">일</div>
                        <div style="color: #6b7280; text-align: center; font-weight: 400; font-size: 0.8rem; padding: 0.25rem 0;">월</div>
                        <div style="color: #6b7280; text-align: center; font-weight: 400; font-size: 0.8rem; padding: 0.25rem 0;">화</div>
                        <div style="color: #6b7280; text-align: center; font-weight: 400; font-size: 0.8rem; padding: 0.25rem 0;">수</div>
                        <div style="color: #6b7280; text-align: center; font-weight: 400; font-size: 0.8rem; padding: 0.25rem 0;">목</div>
                        <div style="color: #6b7280; text-align: center; font-weight: 400; font-size: 0.8rem; padding: 0.25rem 0;">금</div>
                        <div style="color: #6b7280; text-align: center; font-weight: 400; font-size: 0.8rem; padding: 0.25rem 0;">토</div>

                        @php
                            $firstDay = \Carbon\Carbon::create($calendarYear, $calendarMonth, 1);
                            $lastDay = $firstDay->copy()->endOfMonth();
                            $startOfWeek = $firstDay->copy()->startOfWeek();
                            $endOfWeek = $lastDay->copy()->endOfWeek();
                            $today = now()->format('Y-m-d');
                            $calendarDate = $startOfWeek->copy();
                        @endphp

                        @while($calendarDate <= $endOfWeek)
                            @php
                                $dateStr = $calendarDate->format('Y-m-d');
                                $isCurrentMonth = $calendarDate->month == $calendarMonth;
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
        </div>

        {{-- Date Navigation (centered) --}}
        <div style="display: flex; align-items: center; justify-content: center; gap: 0.75rem;">
            <button
                wire:click="previousPeriod"
                style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; white-space: nowrap; font-size: 0.875rem; font-weight: 500; height: 2.25rem; padding: 0.25rem; border: none; background: transparent; border-radius: 9999px; cursor: pointer; transition: background-color 0.2s;"
                aria-label="이전으로 이동"
                data-testid="button-prev"
                onmouseover="this.style.backgroundColor='#f3f4f6';"
                onmouseout="this.style.backgroundColor='transparent';">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 1.25rem; width: 1.25rem; color: #4b5563;">
                    <path d="m15 18-6-6 6-6"></path>
                </svg>
            </button>

            @php
                $date = \Carbon\Carbon::parse($currentDate);
                $dateText = match($type) {
                    'daily' => $date->isoFormat('YYYY.M.D (ddd)'),
                    'weekly' => $date->startOfWeek()->isoFormat('M.D') . ' ~ ' . $date->endOfWeek()->isoFormat('M.D'),
                    'monthly' => $date->isoFormat('YYYY년 M월'),
                };
            @endphp
            <button
                style="font-size: 1rem; color: #1f2937; font-weight: 600; min-width: 200px; text-align: center; background: transparent; border: none; cursor: pointer; transition: color 0.2s;"
                data-testid="text-current-date"
                type="button"
                onmouseover="this.style.color='#40c0c0';"
                onmouseout="this.style.color='#1f2937';">
                {{ $dateText }}
            </button>

            <button
                wire:click="nextPeriod"
                style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; white-space: nowrap; font-size: 0.875rem; font-weight: 500; height: 2.25rem; padding: 0.25rem; border: none; background: transparent; border-radius: 9999px; cursor: pointer; transition: background-color 0.2s;"
                aria-label="다음으로 이동"
                data-testid="button-next"
                onmouseover="this.style.backgroundColor='#f3f4f6';"
                onmouseout="this.style.backgroundColor='transparent';">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 1.25rem; width: 1.25rem; color: #4b5563;">
                    <path d="m9 18 6-6-6-6"></path>
                </svg>
            </button>
        </div>
    </div>

    {{-- Memo Card --}}
    <div style="background-color: #f8f8f8; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); margin-bottom: 1rem;">
        {{-- Header --}}
        <div style="padding: 0.75rem; background-color: #f8f8f8; border-radius: 1rem 1rem 0 0;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <h2 style="font-size: 0.875rem; font-weight: 500; color: #374151;">메모장</h2>
                </div>

                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    {{-- Collapse Toggle Button --}}
                    <button
                        wire:click="toggleCollapse"
                        style="padding: 0.5rem; border-radius: 9999px; transition: background-color 0.2s; background: transparent; border: none; cursor: pointer;"
                        title="{{ $isCollapsed ? '펼치기' : '접기' }}"
                        onmouseover="this.style.backgroundColor='#f3f4f6';"
                        onmouseout="this.style.backgroundColor='transparent';">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 1rem; width: 1rem; color: #6b7280; transition: transform 0.2s; {{ $isCollapsed ? 'transform: rotate(180deg);' : '' }}">
                            <path d="m18 15-6-6-6 6"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Content Area --}}
        @if(!$isCollapsed)
            <div style="background-color: white; border-radius: 0 0 0.75rem 0.75rem;">
                @if($isEditing)
                    {{-- Editing State --}}
                    <div style="padding: 0.75rem 0.75rem 1rem 0.75rem;">
                        <textarea
                            wire:model.live.debounce.500ms="content"
                            placeholder="{{ $this->placeholder }}"
                            style="width: 100%; font-size: 0.75rem; color: #374151; border: 0; resize: none; min-height: 39px; max-height: 200px; overflow-y: auto; outline: none;"
                            rows="3"
                            x-init="$el.focus()"
                            onfocus="this.style.outline='none';"
                        ></textarea>

                        <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                            <button
                                wire:click="saveMemo"
                                style="padding: 0.25rem 0.75rem; font-size: 0.75rem; background-color: #000000; color: #ffffff; border-radius: 0.5rem; transition: background-color 0.2s; border: none; cursor: pointer;"
                                onmouseover="this.style.backgroundColor='#1f2937';"
                                onmouseout="this.style.backgroundColor='#000000';">
                                저장
                            </button>
                            <button
                                wire:click="cancelEditing"
                                style="padding: 0.25rem 0.75rem; font-size: 0.75rem; background-color: #e5e7eb; color: #374151; border-radius: 0.5rem; transition: background-color 0.2s; border: none; cursor: pointer;"
                                onmouseover="this.style.backgroundColor='#d1d5db';"
                                onmouseout="this.style.backgroundColor='#e5e7eb';">
                                취소
                            </button>
                        </div>
                    </div>
                @else
                    {{-- Display State --}}
                    @if(empty($content))
                        {{-- Empty State --}}
                        <div
                            wire:click="startEditing"
                            style="padding: 0.75rem 0.75rem 1rem 0.75rem; cursor: pointer; transition: background-color 0.2s;">
                            <div style="font-size: 0.75rem; color: #9ca3af; padding-left: 1rem; transition: color 0.2s; min-height: 39px; display: flex; align-items: center;"
                                onmouseover="this.style.color='#374151';"
                                onmouseout="this.style.color='#9ca3af';">
                                {!! $this->emptyStateText !!}
                            </div>
                        </div>
                    @else
                        {{-- Content Display --}}
                        <div
                            wire:click="startEditing"
                            style="padding: 0.75rem 0.75rem 1rem 0.75rem; cursor: pointer; transition: background-color 0.2s;"
                            onmouseover="this.style.backgroundColor='#f9fafb';"
                            onmouseout="this.style.backgroundColor='transparent';">
                            <div style="font-size: 0.75rem; color: #374151; white-space: pre-wrap; padding-left: 1rem; min-height: 39px; display: flex; align-items: center; {{ strlen($content) > 200 ? 'max-height: 120px; overflow-y: auto;' : '' }}">{{ $content }}</div>
                        </div>
                    @endif
                @endif
            </div>
        @endif
    </div>
</div>
