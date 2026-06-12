<x-filament-panels::page>
    <style>
        .report-desktop-view { display: none; }
        .report-mobile-view { display: block; }
        @media (min-width: 768px) {
            .report-desktop-view { display: block; }
            .report-mobile-view { display: none; }
        }

        .donut-chart-container {
            position: relative;
            width: 220px;
            height: 220px;
            margin: 0 auto;
        }
        .donut-center-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .trend-chart-container {
            position: relative;
            height: 200px;
            display: flex;
            align-items: flex-end;
            gap: 0;
            padding: 0 1rem;
        }
        .trend-bar-group {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }
        .trend-bar {
            width: 70%;
            background-color: #e5e7eb;
            border-radius: 0.25rem 0.25rem 0 0;
            position: relative;
        }
    </style>

    @php
        $stats = $this->roomStats;
        $trend = $this->monthlyOccupancyTrend;
        $branchName = $this->currentBranch?->name ?? '지점';
        $currentMonthData = end($trend);
        $total = $stats['totalRooms'] ?: 1;
        $occupiedPct = ($stats['occupiedRooms'] / $total) * 100;
        $vacantPct = ($stats['vacantRooms'] / $total) * 100;
        $maxRate = max(array_column($trend, 'rate')) ?: 100;
        $chartMax = min(ceil($maxRate / 25) * 25 + 25, 100);
        $lastTrendIdx = count($trend) - 1;
    @endphp

    <!-- Desktop View -->
    <div class="report-desktop-view">

        <!-- 통계 카드 4종 -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem;">
            <!-- 총 호실 -->
            <div style="background-color: #059669; border-radius: 0.75rem; padding: 1.25rem; color: #ffffff; position: relative;">
                <div style="position: absolute; top: 1rem; right: 1rem; width: 2.5rem; height: 2.5rem; background-color: rgba(255,255,255,0.2); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
                <p style="font-size: 0.75rem; margin: 0 0 0.25rem 0; opacity: 0.8;">총 호실</p>
                <p style="font-size: 2rem; font-weight: 700; margin: 0;">{{ $stats['totalRooms'] }}</p>
                <p style="font-size: 0.75rem; margin: 0.25rem 0 0 0; opacity: 0.7;">총 호실</p>
            </div>

            <!-- 사용중 -->
            <div style="background-color: #ffffff; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb; position: relative;">
                <div style="position: absolute; top: 1rem; right: 1rem; width: 2.5rem; height: 2.5rem; background-color: #dcfce7; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: #16a34a;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <p style="font-size: 0.75rem; color: #6b7280; margin: 0 0 0.25rem 0;">사용중</p>
                <p style="font-size: 2rem; font-weight: 700; color: #111827; margin: 0;">{{ $stats['occupiedRooms'] }}</p>
                <p style="font-size: 0.75rem; margin: 0.25rem 0 0 0;">입주율 <span style="color: #16a34a; font-weight: 500;">{{ $stats['occupancyRate'] }}%</span></p>
            </div>

            <!-- 공실 -->
            <div style="background-color: #ffffff; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb; position: relative;">
                <div style="position: absolute; top: 1rem; right: 1rem; width: 2.5rem; height: 2.5rem; background-color: #fef3c7; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: #f59e0b;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                </div>
                <p style="font-size: 0.75rem; color: #6b7280; margin: 0 0 0.25rem 0;">공실</p>
                <p style="font-size: 2rem; font-weight: 700; color: #111827; margin: 0;">{{ $stats['vacantRooms'] }}</p>
                <p style="font-size: 0.75rem; margin: 0.25rem 0 0 0;">공실률 <span style="color: #f59e0b; font-weight: 500;">{{ $stats['vacancyRate'] }}%</span></p>
            </div>

            <!-- 만료 예정 -->
            <div style="background-color: #ffffff; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #e5e7eb; position: relative;">
                <div style="position: absolute; top: 1rem; right: 1rem; width: 2.5rem; height: 2.5rem; background-color: #fee2e2; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: #ef4444;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                </div>
                <p style="font-size: 0.75rem; color: #6b7280; margin: 0 0 0.25rem 0;">만료 예정</p>
                <p style="font-size: 2rem; font-weight: 700; color: #111827; margin: 0;">{{ $stats['expiringRooms'] }}</p>
                <p style="font-size: 0.75rem; margin: 0.25rem 0 0 0;">7일 이내 <span style="color: #ef4444; font-weight: 500;">{{ $stats['expiringWithin7Days'] }}건</span></p>
            </div>
        </div>

        <!-- 입주율 현황 섹션 -->
        <div class="fi-ta rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5" style="padding: 1.5rem; margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
                <div style="width: 3px; height: 1.25rem; background-color: #059669; border-radius: 2px;"></div>
                <h2 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin: 0;">입주율 현황</h2>
            </div>

            <div style="display: flex; gap: 2rem;">
                <!-- 도넛 차트 -->
                <div style="flex: 1; display: flex; align-items: center; justify-content: center;">
                    <div style="position: relative; width: 240px; height: 240px;">
                        <svg viewBox="0 0 36 36" style="width: 100%; height: 100%; transform: rotate(-90deg);">
                            <!-- 공실 (노란색) -->
                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#F59E0B" stroke-width="3.5"
                                stroke-dasharray="{{ $vacantPct }} {{ 100 - $vacantPct }}"
                                stroke-dashoffset="-{{ $occupiedPct }}" />
                            <!-- 사용중 (초록색) -->
                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#10B981" stroke-width="3.5"
                                stroke-dasharray="{{ $occupiedPct }} {{ 100 - $occupiedPct }}"
                                stroke-dashoffset="0" />
                        </svg>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                            <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">입주율</p>
                            <p style="font-size: 1.75rem; font-weight: 700; color: #111827; margin: 0;">{{ $stats['occupancyRate'] }}%</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 1.5rem; margin-top: 1rem; justify-content: center;">
                        <div style="display: flex; align-items: center; gap: 0.375rem;">
                            <div style="width: 0.625rem; height: 0.625rem; background-color: #10B981; border-radius: 50%;"></div>
                            <span style="font-size: 0.75rem; color: #6b7280;">사용중</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.375rem;">
                            <div style="width: 0.625rem; height: 0.625rem; background-color: #F59E0B; border-radius: 50%;"></div>
                            <span style="font-size: 0.75rem; color: #6b7280;">공실</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.375rem;">
                            <div style="width: 0.625rem; height: 0.625rem; background-color: #EF4444; border-radius: 50%;"></div>
                            <span style="font-size: 0.75rem; color: #6b7280;">만료 예정</span>
                        </div>
                    </div>
                </div>

                <!-- 입주율 분석 패널 -->
                <div style="flex: 0 0 320px; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.5rem;">
                    <h3 style="font-size: 1rem; font-weight: 700; color: #111827; margin: 0 0 1.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="font-size: 1.125rem;">🔍</span> 입주율 분석
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 0.875rem;">
                        <div style="display: flex; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid #f3f4f6;">
                            <span style="font-size: 0.875rem; color: #6b7280;">총 호실</span>
                            <span style="font-size: 0.875rem; font-weight: 600; color: #111827;">{{ $stats['totalRooms'] }}개</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid #f3f4f6;">
                            <span style="font-size: 0.875rem; color: #6b7280;">사용중인 호실</span>
                            <span style="font-size: 0.875rem; font-weight: 600; color: #16a34a; font-style: italic;">{{ $stats['occupiedRooms'] }}개</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid #f3f4f6;">
                            <span style="font-size: 0.875rem; color: #6b7280;">공실</span>
                            <span style="font-size: 0.875rem; font-weight: 600; color: #f59e0b; font-style: italic;">{{ $stats['vacantRooms'] }}개</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid #f3f4f6;">
                            <span style="font-size: 0.875rem; color: #6b7280;">만료 예정</span>
                            <span style="font-size: 0.875rem; font-weight: 600; color: #ef4444; font-style: italic;">{{ $stats['expiringRooms'] }}개</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.25rem;">
                            <span style="font-size: 0.875rem; color: #6b7280;">입주율</span>
                            <span style="font-size: 1.25rem; font-weight: 700; color: #059669;">{{ $stats['occupancyRate'] }}%</span>
                        </div>
                        <div style="width: 100%; height: 0.375rem; background-color: #e5e7eb; border-radius: 9999px; overflow: hidden;">
                            <div style="height: 100%; background-color: #059669; border-radius: 9999px; width: {{ $stats['occupancyRate'] }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 월간 입주율 추이 -->
        <div class="fi-ta rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5" style="padding: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                <div style="width: 3px; height: 1.25rem; background-color: #059669; border-radius: 2px;"></div>
                <h2 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin: 0;">월간 입주율 추이</h2>
            </div>
            <p style="font-size: 0.813rem; color: #9ca3af; margin: 0 0 1.25rem 0;">최근 6개월 입주율 (%)</p>

            <!-- 월별 카드 -->
            <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 0.75rem; margin-bottom: 2rem;">
                @foreach($trend as $idx => $item)
                    @php $isCurrentMonth = ($idx === $lastTrendIdx); @endphp
                    <div style="border-radius: 0.75rem; padding: 1rem; text-align: left;
                        {{ $isCurrentMonth
                            ? 'background-color: #059669; color: #ffffff;'
                            : 'background-color: #ffffff; border: 1px solid #e5e7eb; color: #111827;' }}">
                        <p style="font-size: 0.75rem; margin: 0 0 0.375rem 0; {{ $isCurrentMonth ? 'opacity: 0.8;' : 'color: #6b7280;' }}">
                            {{ $item['year'] }}년 {{ $item['month'] }}월
                        </p>
                        <p style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $item['rate'] }}%</p>
                        <p style="font-size: 0.688rem; margin: 0.25rem 0 0 0; {{ $isCurrentMonth ? 'opacity: 0.7;' : 'color: #9ca3af;' }}">
                            {{ $item['occupiedRooms'] }}실 사용
                        </p>
                    </div>
                @endforeach
            </div>

            <!-- 바 + 라인 차트 -->
            <div style="position: relative; height: 220px; padding: 0 0.5rem;">
                <!-- Y축 가이드라인 -->
                @foreach([100, 75, 50, 25, 0] as $yVal)
                    @if($yVal <= $chartMax)
                        <div style="position: absolute; left: 0; right: 0; bottom: {{ ($yVal / $chartMax) * 100 }}%; border-top: 1px dashed #e5e7eb; z-index: 0;">
                            <span style="position: absolute; left: -0.5rem; top: -0.625rem; font-size: 0.625rem; color: #9ca3af; transform: translateX(-100%); padding-right: 0.5rem;">{{ $yVal }}</span>
                        </div>
                    @endif
                @endforeach

                <!-- 바 + 포인트 -->
                <div style="display: flex; align-items: flex-end; height: 100%; gap: 0; position: relative; z-index: 1;">
                    @foreach($trend as $idx => $item)
                        @php
                            $barHeight = $chartMax > 0 ? ($item['rate'] / $chartMax) * 100 : 0;
                            $isCurrentMonth = ($idx === $lastTrendIdx);
                        @endphp
                        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; position: relative;">
                            @if($isCurrentMonth)
                                <div style="position: absolute; top: {{ 100 - $barHeight - 8 }}%; font-size: 0.688rem; font-weight: 600; background-color: #111827; color: #ffffff; padding: 0.125rem 0.375rem; border-radius: 0.25rem; white-space: nowrap; z-index: 2;">
                                    {{ $item['rate'] }}%
                                </div>
                            @endif
                            <div style="width: 60%; background-color: {{ $isCurrentMonth ? '#e5e7eb' : '#e5e7eb' }}; border-radius: 0.25rem 0.25rem 0 0; height: {{ $barHeight }}%; position: relative; {{ $isCurrentMonth ? 'border: 2px dashed #059669; background-color: rgba(5,150,105,0.08);' : '' }}">
                                <div style="position: absolute; top: 0; left: 50%; transform: translate(-50%, -50%); width: 0.5rem; height: 0.5rem; background-color: #059669; border-radius: 50%; z-index: 2;"></div>
                            </div>
                            <span style="font-size: 0.625rem; color: #9ca3af; margin-top: 0.375rem; white-space: nowrap;">{{ $item['year'] }}년 {{ $item['month'] }}월</span>
                        </div>
                    @endforeach
                </div>

                <!-- 라인 연결 (SVG) -->
                <svg style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none;" viewBox="0 0 600 220" preserveAspectRatio="none">
                    @php
                        $points = [];
                        $segmentWidth = 600 / count($trend);
                        foreach ($trend as $idx => $item) {
                            $x = ($idx + 0.5) * $segmentWidth;
                            $y = 220 - (($item['rate'] / $chartMax) * 220);
                            $points[] = "$x,$y";
                        }
                        $polyline = implode(' ', $points);
                    @endphp
                    <polyline points="{{ $polyline }}" fill="none" stroke="#059669" stroke-width="2" />
                </svg>
            </div>
        </div>
    </div>

    <!-- ============================== -->
    <!-- Mobile View -->
    <!-- ============================== -->
    <div class="report-mobile-view">
        <!-- 통계 카드 -->
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-bottom: 1.25rem;">
            <div style="background-color: #059669; border-radius: 0.75rem; padding: 1rem; color: #ffffff;">
                <p style="font-size: 0.688rem; margin: 0 0 0.125rem 0; opacity: 0.8;">총 호실</p>
                <p style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $stats['totalRooms'] }}</p>
            </div>
            <div style="background-color: #ffffff; border-radius: 0.75rem; padding: 1rem; border: 1px solid #e5e7eb;">
                <p style="font-size: 0.688rem; color: #6b7280; margin: 0 0 0.125rem 0;">사용중</p>
                <p style="font-size: 1.5rem; font-weight: 700; color: #111827; margin: 0;">{{ $stats['occupiedRooms'] }}</p>
                <p style="font-size: 0.625rem; margin: 0;">입주율 <span style="color: #16a34a;">{{ $stats['occupancyRate'] }}%</span></p>
            </div>
            <div style="background-color: #ffffff; border-radius: 0.75rem; padding: 1rem; border: 1px solid #e5e7eb;">
                <p style="font-size: 0.688rem; color: #6b7280; margin: 0 0 0.125rem 0;">공실</p>
                <p style="font-size: 1.5rem; font-weight: 700; color: #111827; margin: 0;">{{ $stats['vacantRooms'] }}</p>
                <p style="font-size: 0.625rem; margin: 0;">공실률 <span style="color: #f59e0b;">{{ $stats['vacancyRate'] }}%</span></p>
            </div>
            <div style="background-color: #ffffff; border-radius: 0.75rem; padding: 1rem; border: 1px solid #e5e7eb;">
                <p style="font-size: 0.688rem; color: #6b7280; margin: 0 0 0.125rem 0;">만료 예정</p>
                <p style="font-size: 1.5rem; font-weight: 700; color: #111827; margin: 0;">{{ $stats['expiringRooms'] }}</p>
                <p style="font-size: 0.625rem; margin: 0;">7일 이내 <span style="color: #ef4444;">{{ $stats['expiringWithin7Days'] }}건</span></p>
            </div>
        </div>

        <!-- 입주율 현황 -->
        <div style="background-color: #ffffff; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem; margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.375rem; margin-bottom: 1.25rem;">
                <div style="width: 3px; height: 1rem; background-color: #059669; border-radius: 2px;"></div>
                <h2 style="font-size: 1rem; font-weight: 700; color: #111827; margin: 0;">입주율 현황</h2>
            </div>

            <!-- 도넛 차트 -->
            <div style="display: flex; justify-content: center; margin-bottom: 1rem;">
                <div style="position: relative; width: 180px; height: 180px;">
                    <svg viewBox="0 0 36 36" style="width: 100%; height: 100%; transform: rotate(-90deg);">
                        <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#F59E0B" stroke-width="3.5"
                            stroke-dasharray="{{ $vacantPct }} {{ 100 - $vacantPct }}"
                            stroke-dashoffset="-{{ $occupiedPct }}" />
                        <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#10B981" stroke-width="3.5"
                            stroke-dasharray="{{ $occupiedPct }} {{ 100 - $occupiedPct }}"
                            stroke-dashoffset="0" />
                    </svg>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                        <p style="font-size: 0.625rem; color: #6b7280; margin: 0;">입주율</p>
                        <p style="font-size: 1.5rem; font-weight: 700; color: #111827; margin: 0;">{{ $stats['occupancyRate'] }}%</p>
                    </div>
                </div>
            </div>

            <!-- 분석 요약 -->
            <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.813rem;">
                <div style="display: flex; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid #f3f4f6;">
                    <span style="color: #6b7280;">총 호실</span>
                    <span style="font-weight: 600;">{{ $stats['totalRooms'] }}개</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid #f3f4f6;">
                    <span style="color: #6b7280;">사용중</span>
                    <span style="font-weight: 600; color: #16a34a;">{{ $stats['occupiedRooms'] }}개</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid #f3f4f6;">
                    <span style="color: #6b7280;">공실</span>
                    <span style="font-weight: 600; color: #f59e0b;">{{ $stats['vacantRooms'] }}개</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #6b7280;">만료 예정</span>
                    <span style="font-weight: 600; color: #ef4444;">{{ $stats['expiringRooms'] }}개</span>
                </div>
            </div>
        </div>

        <!-- 월간 입주율 추이 -->
        <div style="background-color: #ffffff; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.375rem; margin-bottom: 0.375rem;">
                <div style="width: 3px; height: 1rem; background-color: #059669; border-radius: 2px;"></div>
                <h2 style="font-size: 1rem; font-weight: 700; color: #111827; margin: 0;">월간 입주율 추이</h2>
            </div>
            <p style="font-size: 0.75rem; color: #9ca3af; margin: 0 0 1rem 0;">최근 6개월 입주율 (%)</p>

            <!-- 월별 카드 (가로 스크롤) -->
            <div style="overflow-x: auto; margin-bottom: 1rem;">
                <div style="display: flex; gap: 0.5rem; min-width: 500px;">
                    @foreach($trend as $idx => $item)
                        @php $isCurrentMonth = ($idx === $lastTrendIdx); @endphp
                        <div style="flex: 1; border-radius: 0.5rem; padding: 0.75rem; text-align: left;
                            {{ $isCurrentMonth
                                ? 'background-color: #059669; color: #ffffff;'
                                : 'background-color: #ffffff; border: 1px solid #e5e7eb;' }}">
                            <p style="font-size: 0.625rem; margin: 0 0 0.25rem 0; {{ $isCurrentMonth ? 'opacity: 0.8;' : 'color: #6b7280;' }}">
                                {{ $item['year'] }}년 {{ $item['month'] }}월
                            </p>
                            <p style="font-size: 1.125rem; font-weight: 700; margin: 0;">{{ $item['rate'] }}%</p>
                            <p style="font-size: 0.563rem; margin: 0.125rem 0 0 0; {{ $isCurrentMonth ? 'opacity: 0.7;' : 'color: #9ca3af;' }}">
                                {{ $item['occupiedRooms'] }}실 사용
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
