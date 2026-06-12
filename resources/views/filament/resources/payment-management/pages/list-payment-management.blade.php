<x-filament-panels::page>
    <style>
        .payment-desktop-view { display: none; }
        .payment-mobile-view { display: block; }
        @media (min-width: 768px) {
            .payment-desktop-view { display: block; }
            .payment-mobile-view { display: none; }
        }

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

        .payment-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .payment-table th {
            padding: 1rem 0.75rem;
            font-size: 0.813rem;
            font-weight: 500;
            color: #6b7280;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }
        .payment-table td {
            padding: 1.25rem 0.75rem;
            font-size: 0.875rem;
            color: #111827;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        .payment-table tbody tr:hover {
            background-color: #f9fafb;
        }
        .payment-table .expanded-row td {
            background-color: #f9fafb;
            padding: 0.75rem;
            font-size: 0.813rem;
        }

        /* 입주 상태 배지 */
        .status-badge-checked-in {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            background-color: #22c55e;
            color: #ffffff;
        }
        .status-badge-scheduled {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            background-color: #ffffff;
            color: #374151;
            border: 1px solid #d1d5db;
        }
        .status-badge-checked-out {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            background-color: #6b7280;
            color: #ffffff;
        }

        /* 납부 상태 배지 */
        .payment-badge-paid {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            background-color: #111827;
            color: #ffffff;
        }
        .payment-badge-unpaid {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            background-color: #ef4444;
            color: #ffffff;
        }

        /* 액션 버튼 */
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.5rem 1rem;
            font-size: 0.813rem;
            font-weight: 500;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            background-color: #ffffff;
            color: #374151;
            cursor: pointer;
            transition: background-color 0.15s;
            white-space: nowrap;
        }
        .action-btn:hover {
            background-color: #f9fafb;
        }
        .action-btn-disabled {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.5rem 1rem;
            font-size: 0.813rem;
            font-weight: 500;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            background-color: #f3f4f6;
            color: #9ca3af;
            white-space: nowrap;
            cursor: default;
        }

        /* 바 차트 */
        .bar-chart-container {
            display: flex;
            align-items: flex-end;
            gap: 0.5rem;
            height: 180px;
            padding: 0 0.5rem;
        }
        .bar-chart-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.375rem;
            height: 100%;
            justify-content: flex-end;
        }
        .bar-chart-bar {
            width: 100%;
            max-width: 2.5rem;
            background-color: #059669;
            border-radius: 0.25rem 0.25rem 0 0;
            transition: height 0.3s ease;
            position: relative;
        }
        .bar-chart-label {
            font-size: 0.688rem;
            color: #6b7280;
        }

        /* 수익률 바 차트 */
        .profit-bar {
            height: 2rem;
            border-radius: 0.25rem;
            display: flex;
            align-items: center;
            padding: 0 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: #ffffff;
            min-width: 2rem;
        }
    </style>

    @php
        $groupedTenants = $this->groupedTenants;
        $allTenants = $groupedTenants->flatten();
        $monthlyIncome = $this->monthlyIncomeData;
        $totalIncome = array_sum($monthlyIncome);
        $monthsWithData = count(array_filter($monthlyIncome, fn($v) => $v > 0));
        $avgIncome = $monthsWithData > 0 ? (int)($totalIncome / $monthsWithData) : 0;
        $maxIncome = max($monthlyIncome) ?: 1;
        $profitData = $this->profitAnalysisData;
        $maxBar = max($profitData['totalRent'], 1);
        $branchName = $this->currentBranch?->name ?? '지점';
        $activeFilterCount = $this->getActiveFilterCount();
    @endphp

    <!-- ============================== -->
    <!-- ① 수납 실시간 확인 섹션 - Desktop -->
    <!-- ============================== -->
    <div class="payment-desktop-view" x-data @payment-updated.window="$wire.$refresh()">

        <div class="fi-ta rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <!-- Search & Filter -->
            <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #f3f4f6;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="position: relative; flex: 0 0 auto; width: 280px;">
                        <svg style="position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); width: 1rem; height: 1rem; color: #9ca3af;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="검색"
                            style="width: 100%; padding: 0.5rem 0.75rem 0.5rem 2.5rem; font-size: 0.875rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background-color: #ffffff; color: #111827; outline: none;"
                        />
                    </div>
                    <button wire:click="toggleMobileFilters" style="padding: 0.5rem; border: none; background: none; cursor: pointer; color: #9ca3af;">
                        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Table -->
            <table class="payment-table">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">호실 번호</th>
                        <th>입주 상태</th>
                        <th>이름</th>
                        <th style="text-align: right;">결제 금액</th>
                        <th style="text-align: center;">결제일</th>
                        <th style="text-align: center;">실제 결제일</th>
                        <th style="text-align: center;">결제 방법</th>
                        <th style="text-align: center;">납부 상태</th>
                        <th style="text-align: center; padding-right: 1.5rem;">액션</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allTenants as $tenant)
                        @php
                            $isExpanded = in_array($tenant->id, $expandedDesktopTenants ?? []);
                            $hasCashReceipt = $tenant->latestCashReceipt !== null;
                        @endphp
                        <tr>
                            <!-- 호실 번호 (클릭 시 드릴다운) -->
                            <td style="padding-left: 1.5rem;">
                                <button
                                    wire:click="toggleDesktopTenant({{ $tenant->id }})"
                                    style="font-weight: 600; color: #111827; background: none; border: none; cursor: pointer; padding: 0; display: flex; align-items: center; gap: 0.375rem;"
                                >
                                    <svg style="width: 0.75rem; height: 0.75rem; color: #9ca3af; transition: transform 0.2s; {{ $isExpanded ? 'transform: rotate(90deg);' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                    {{ $tenant->room?->room_number ?? '-' }}호
                                </button>
                            </td>

                            <!-- 입주 상태 -->
                            <td>
                                @if($tenant->process_status === 'checked_in')
                                    <span class="status-badge-checked-in">입실</span>
                                @elseif($tenant->process_status === 'scheduled')
                                    <span class="status-badge-scheduled">입실 예정</span>
                                @elseif($tenant->process_status === 'checked_out')
                                    <span class="status-badge-checked-out">퇴실</span>
                                @else
                                    <span class="status-badge-scheduled">대기</span>
                                @endif
                            </td>

                            <!-- 이름 -->
                            <td>
                                <span style="color: #111827;">{{ $tenant->name }}</span>
                            </td>

                            <!-- 결제 금액 -->
                            <td style="text-align: right;">
                                <span style="color: #111827;">{{ $tenant->room?->monthly_rent ? number_format($tenant->room->monthly_rent) . '원' : '-' }}</span>
                            </td>

                            <!-- 결제일 -->
                            <td style="text-align: center;">
                                <span style="color: #6b7280;">{{ $tenant->payment_due_day ? '매월 ' . $tenant->payment_due_day . '일' : '-' }}</span>
                            </td>

                            <!-- 실제 결제일 -->
                            <td style="text-align: center;">
                                <span style="color: #6b7280;">{{ $tenant->actual_payment_date?->format('Y-m-d') ?? '-' }}</span>
                            </td>

                            <!-- 결제 방법 -->
                            <td style="text-align: center;">
                                <span style="color: #6b7280;">{{ $tenant->payment_method_label }}</span>
                            </td>

                            <!-- 납부 상태 -->
                            <td style="text-align: center;">
                                @if($tenant->payment_status === 'paid')
                                    <span class="payment-badge-paid">완납</span>
                                @else
                                    <span class="payment-badge-unpaid">미납</span>
                                @endif
                            </td>

                            <!-- 액션 -->
                            <td style="text-align: center; padding-right: 1.5rem;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                    @if($tenant->payment_status !== 'paid')
                                        {{-- 미납: 결제 처리하기 + 연락하기 --}}
                                        <button
                                            wire:click="markAsPaid({{ $tenant->id }})"
                                            wire:confirm="납부 완료 처리하시겠습니까?"
                                            class="action-btn"
                                        >
                                            결제 처리하기
                                        </button>
                                        @if($tenant->phone)
                                            <a href="tel:{{ $tenant->phone }}" class="action-btn">연락하기</a>
                                        @endif
                                    @elseif($hasCashReceipt)
                                        {{-- 완납 + 현금영수증 발행 완료 --}}
                                        <button
                                            wire:click="viewCashReceipt({{ $tenant->id }})"
                                            class="action-btn-disabled"
                                            style="cursor: pointer; background-color: #f3f4f6;"
                                        >
                                            발행 완료 ✓
                                        </button>
                                        @if($tenant->phone)
                                            <a href="tel:{{ $tenant->phone }}" class="action-btn">연락하기</a>
                                        @endif
                                    @else
                                        {{-- 완납 + 현금영수증 미발행 --}}
                                        <button
                                            wire:click="openCashReceiptModal({{ $tenant->id }})"
                                            class="action-btn"
                                        >
                                            <svg style="width: 1rem; height: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            현금영수증 발행하기
                                        </button>
                                        @if($tenant->phone)
                                            <a href="tel:{{ $tenant->phone }}" class="action-btn">연락하기</a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- 드릴다운: 월별 납부 이력 --}}
                        @if($isExpanded)
                            <tr class="expanded-row">
                                <td colspan="9" style="padding: 0 1.5rem 1rem 3rem; background-color: #f9fafb;">
                                    <div style="font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem; padding-top: 0.75rem;">
                                        {{ $tenant->name }} 님의 납부 이력
                                    </div>
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th style="padding: 0.5rem 0.5rem; font-size: 0.75rem; font-weight: 500; color: #9ca3af; text-align: left; border-bottom: 1px solid #e5e7eb;">월</th>
                                                <th style="padding: 0.5rem 0.5rem; font-size: 0.75rem; font-weight: 500; color: #9ca3af; text-align: right; border-bottom: 1px solid #e5e7eb;">결제 금액</th>
                                                <th style="padding: 0.5rem 0.5rem; font-size: 0.75rem; font-weight: 500; color: #9ca3af; text-align: center; border-bottom: 1px solid #e5e7eb;">결제일</th>
                                                <th style="padding: 0.5rem 0.5rem; font-size: 0.75rem; font-weight: 500; color: #9ca3af; text-align: center; border-bottom: 1px solid #e5e7eb;">결제 방법</th>
                                                <th style="padding: 0.5rem 0.5rem; font-size: 0.75rem; font-weight: 500; color: #9ca3af; text-align: center; border-bottom: 1px solid #e5e7eb;">납부 상태</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $currentYear = now()->year;
                                                $currentMonth = now()->month;
                                                $moveInYear = $tenant->move_in_date ? $tenant->move_in_date->year : $currentYear;
                                                $moveInMonth = $tenant->move_in_date ? $tenant->move_in_date->month : 1;
                                                $startMonth = ($moveInYear < $currentYear) ? 1 : $moveInMonth;
                                            @endphp
                                            @for($m = $startMonth; $m <= $currentMonth; $m++)
                                                @php
                                                    $isPaidThisMonth = $tenant->actual_payment_date
                                                        && $tenant->actual_payment_date->year === $currentYear
                                                        && $tenant->actual_payment_date->month === $m;
                                                    $isCurrentMonth = ($m === $currentMonth);
                                                @endphp
                                                <tr>
                                                    <td style="padding: 0.5rem; font-size: 0.813rem; color: #374151; border-bottom: 1px solid #f3f4f6;">{{ $m }}월</td>
                                                    <td style="padding: 0.5rem; font-size: 0.813rem; color: #374151; text-align: right; border-bottom: 1px solid #f3f4f6;">
                                                        {{ $tenant->room?->monthly_rent ? number_format($tenant->room->monthly_rent) . '원' : '-' }}
                                                    </td>
                                                    <td style="padding: 0.5rem; font-size: 0.813rem; color: #6b7280; text-align: center; border-bottom: 1px solid #f3f4f6;">
                                                        @if($isPaidThisMonth)
                                                            {{ $tenant->actual_payment_date->format('Y-m-d') }}
                                                        @elseif($tenant->payment_due_day)
                                                            매월 {{ $tenant->payment_due_day }}일
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td style="padding: 0.5rem; font-size: 0.813rem; color: #6b7280; text-align: center; border-bottom: 1px solid #f3f4f6;">
                                                        {{ $tenant->payment_method_label }}
                                                    </td>
                                                    <td style="padding: 0.5rem; text-align: center; border-bottom: 1px solid #f3f4f6;">
                                                        @if($isCurrentMonth && $tenant->payment_status === 'paid')
                                                            <span class="payment-badge-paid" style="font-size: 0.688rem; padding: 0.125rem 0.5rem;">완납</span>
                                                        @elseif($isCurrentMonth)
                                                            <span class="payment-badge-unpaid" style="font-size: 0.688rem; padding: 0.125rem 0.5rem;">미납</span>
                                                        @else
                                                            <span style="font-size: 0.688rem; color: #9ca3af;">—</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 3rem 1rem;">
                                <svg style="margin: 0 auto; height: 3rem; width: 3rem; color: #9ca3af;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 style="margin-top: 0.5rem; font-size: 0.875rem; font-weight: 500; color: #111827;">수납 데이터가 없습니다</h3>
                                <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #6b7280;">입주자를 등록하면 수납 관리가 가능합니다.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- ============================== -->
        <!-- ④ 월 수입 분석 섹션 - Desktop -->
        <!-- ============================== -->
        <div class="fi-ta rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10" style="margin-top: 1.5rem; padding: 1.5rem;">
            <!-- 헤더 -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                <h2 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin: 0;">월 수입 분석</h2>
                <select
                    wire:model.live="analysisYear"
                    style="padding: 0.375rem 0.75rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background-color: #ffffff; color: #111827;"
                >
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}">{{ $y }}년</option>
                    @endfor
                </select>
            </div>

            <!-- 요약 카드 -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="background-color: #17B7A5; border-radius: 0.75rem; padding: 1.25rem; color: #ffffff;">
                    <p style="font-size: 0.75rem; margin: 0 0 0.375rem 0; opacity: 0.8;">총 신고 금액</p>
                    <p style="font-size: 1.375rem; font-weight: 700; margin: 0;">₩{{ number_format($totalIncome) }}</p>
                </div>
                <div style="background-color: #17B7A5; border-radius: 0.75rem; padding: 1.25rem; color: #ffffff;">
                    <p style="font-size: 0.75rem; margin: 0 0 0.375rem 0; opacity: 0.8;">평균 월 신고 금액</p>
                    <p style="font-size: 1.375rem; font-weight: 700; margin: 0;">₩{{ number_format($avgIncome) }}</p>
                </div>
            </div>

            <!-- 월별 바 차트 -->
            <div class="bar-chart-container" style="margin-bottom: 1.5rem;">
                @for($m = 1; $m <= 12; $m++)
                    @php
                        $val = $monthlyIncome[$m] ?? 0;
                        $height = $maxIncome > 0 ? max(($val / $maxIncome) * 100, 0) : 0;
                        $isMax = $val === $maxIncome && $val > 0;
                    @endphp
                    <div class="bar-chart-item">
                        @if($isMax && $val > 0)
                            <div style="font-size: 0.625rem; color: #059669; font-weight: 600; background-color: #ecfdf5; padding: 0.125rem 0.375rem; border-radius: 0.25rem; white-space: nowrap;">
                                ₩{{ number_format($val) }}
                            </div>
                        @endif
                        <div class="bar-chart-bar" style="height: {{ $height > 0 ? $height . '%' : '2px' }}; {{ $val === 0 ? 'background-color: #e5e7eb;' : '' }}"></div>
                        <span class="bar-chart-label">{{ $m }}월</span>
                    </div>
                @endfor
            </div>

            <!-- 월별 금액 카드 그리드 -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem;">
                @for($m = 1; $m <= 12; $m++)
                    @php
                        $val = $monthlyIncome[$m] ?? 0;
                        $isMax = $val === $maxIncome && $val > 0;
                    @endphp
                    <div style="background-color: #f9fafb; border-radius: 0.5rem; padding: 0.75rem; text-align: center; position: relative;">
                        @if($isMax)
                            <span style="position: absolute; top: 0.375rem; right: 0.375rem; font-size: 0.625rem; font-weight: 600; background-color: #059669; color: #ffffff; padding: 0.063rem 0.375rem; border-radius: 9999px;">최고</span>
                        @endif
                        <p style="font-size: 0.75rem; color: #6b7280; margin: 0 0 0.25rem 0;">{{ $m }}월</p>
                        <p style="font-size: 0.875rem; font-weight: 600; color: {{ $val > 0 ? '#111827' : '#d1d5db' }}; margin: 0;">
                            {{ $val > 0 ? '₩' . number_format($val) : '—' }}
                        </p>
                    </div>
                @endfor
            </div>
        </div>

        <!-- ============================== -->
        <!-- ⑤ 월 수익률 분석 섹션 - Desktop -->
        <!-- ============================== -->
        <div class="fi-ta rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10" style="margin-top: 1.5rem; padding: 1.5rem;">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0;">월 수익률 분석</h2>

            <!-- 요약 카드 5종 -->
            <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 0.625rem; margin-bottom: 1.5rem;">
                <div style="background-color: #ffffff; border: 1.5px solid #e5e7eb; border-radius: 0.75rem; padding: 0.875rem 0.875rem 0.625rem; text-align: left; overflow: hidden;">
                    <p style="font-size: clamp(11px,1.4vw,13px); color: #9ca3af; font-weight: 500; margin: 0 0 0.25rem 0;">총 입실료</p>
                    <p style="font-size: clamp(12px,1.7vw,18px); font-weight: 700; color: #111827; margin: 0; overflow: hidden; text-overflow: ellipsis;">{{ number_format($profitData['totalRent']) }}</p>
                </div>
                <div style="background-color: #ffffff; border: 1.5px solid #e5e7eb; border-radius: 0.75rem; padding: 0.875rem 0.875rem 0.625rem; text-align: left; overflow: hidden;">
                    <p style="font-size: clamp(11px,1.4vw,13px); color: #9ca3af; font-weight: 500; margin: 0 0 0.25rem 0;">고시원 월세</p>
                    <p style="font-size: clamp(12px,1.7vw,18px); font-weight: 700; color: #ef4444; margin: 0; overflow: hidden; text-overflow: ellipsis;">-{{ number_format($profitData['goshiwonRent']) }}</p>
                </div>
                <div style="background-color: #ffffff; border: 1.5px solid #e5e7eb; border-radius: 0.75rem; padding: 0.875rem 0.875rem 0.625rem; text-align: left; overflow: hidden;">
                    <p style="font-size: clamp(11px,1.4vw,13px); color: #9ca3af; font-weight: 500; margin: 0 0 0.25rem 0;">공과금</p>
                    <p style="font-size: clamp(12px,1.7vw,18px); font-weight: 700; color: #f97316; margin: 0; overflow: hidden; text-overflow: ellipsis;">-{{ number_format($profitData['utilities']) }}</p>
                </div>
                <div style="background-color: #ffffff; border: 1.5px solid #e5e7eb; border-radius: 0.75rem; padding: 0.875rem 0.875rem 0.625rem; text-align: left; overflow: hidden;">
                    <p style="font-size: clamp(11px,1.4vw,13px); color: #9ca3af; font-weight: 500; margin: 0 0 0.25rem 0;">기타 비용</p>
                    <p style="font-size: clamp(12px,1.7vw,18px); font-weight: 700; color: #6b7280; margin: 0; overflow: hidden; text-overflow: ellipsis;">-{{ number_format($profitData['otherCosts']) }}</p>
                </div>
                <div style="background-color: #F6FBF9; border: 1px solid #DCEFE8; border-radius: 0.75rem; padding: 0.875rem 0.875rem 0.625rem; text-align: left; overflow: hidden;">
                    <p style="font-size: clamp(11px,1.4vw,13px); color: #9ca3af; font-weight: 500; margin: 0 0 0.25rem 0;">순수익</p>
                    <p style="font-size: clamp(12px,1.7vw,18px); font-weight: 700; color: #22c55e; margin: 0; overflow: hidden; text-overflow: ellipsis;">{{ number_format($profitData['netProfit']) }}</p>
                </div>
            </div>

            <!-- 비교 바 차트 (세로형) -->
            @php
                $profitItems = [
                    ['label' => '총 입실료', 'value' => $profitData['totalRent'], 'color' => '#6b7280'],
                    ['label' => '고시원 월세', 'value' => $profitData['goshiwonRent'], 'color' => '#ef4444'],
                    ['label' => '공과금', 'value' => $profitData['utilities'], 'color' => '#f97316'],
                    ['label' => '기타 비용', 'value' => $profitData['otherCosts'], 'color' => '#9ca3af'],
                    ['label' => '순수익', 'value' => $profitData['netProfit'], 'color' => '#22c55e'],
                ];
            @endphp
            <div style="display: flex; align-items: flex-end; justify-content: center; gap: 1.375rem; height: 220px; margin-bottom: 1.5rem;">
                @foreach($profitItems as $item)
                    @php $barH = $maxBar > 0 ? max(($item['value'] / $maxBar) * 185, 4) : 4; @endphp
                    <div style="display: flex; flex-direction: column; align-items: center; min-width: 3.5rem;">
                        <span style="font-size: 0.688rem; font-weight: 600; color: #6b7280; margin-bottom: 0.313rem;">₩{{ number_format($item['value']) }}</span>
                        <div style="width: 3.125rem; height: {{ $barH }}px; background-color: {{ $item['color'] }}; border-radius: 0.313rem 0.313rem 0 0;"></div>
                        <span style="font-size: 0.688rem; color: #6b7280; margin-top: 0.438rem;">{{ $item['label'] }}</span>
                    </div>
                @endforeach
            </div>

            <!-- 연간 수익률 배너 -->
            <div style="display: flex; justify-content: center;">
                <button
                    wire:click="toggleRentSimulation"
                    style="width: 23rem; padding: 0.875rem 1.5rem; background-color: #1A1A1A; color: #ffffff; border: none; border-radius: 0.625rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.375rem; font-size: 0.875rem; font-weight: 600;"
                >
                    {{ $branchName }} 연간 수익률 👉 {{ $profitData['profitRate'] }}%
                </button>
            </div>
        </div>
    </div>

    <!-- ============================== -->
    <!-- Mobile View -->
    <!-- ============================== -->
    <div class="payment-mobile-view" x-data @payment-updated.window="$wire.$refresh()">
        <!-- 검색 및 필터 -->
        <div style="margin-bottom: 1rem;">
            <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
                <div style="flex: 1; position: relative;">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="이름 또는 호실 검색"
                        style="width: 100%; padding: 0.625rem 0.75rem 0.625rem 2.5rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background-color: #ffffff; color: #111827; outline: none;"
                    />
                    <svg style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); width: 1rem; height: 1rem; color: #9ca3af;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button
                    wire:click="toggleMobileFilters"
                    style="padding: 0.5rem 0.625rem; font-size: 0.813rem; font-weight: 500; border-radius: 0.375rem; border: 1px solid {{ $activeFilterCount > 0 ? '#06CBBB' : '#d1d5db' }}; cursor: pointer; background-color: {{ $activeFilterCount > 0 ? '#06CBBB' : '#ffffff' }}; color: {{ $activeFilterCount > 0 ? '#ffffff' : '#374151' }}; display: flex; align-items: center; gap: 0.25rem;"
                >
                    <svg style="width: 0.875rem; height: 0.875rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    필터
                    @if($activeFilterCount > 0)
                        <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 1rem; height: 1rem; font-size: 0.625rem; font-weight: 600; background-color: #ffffff; color: #06CBBB; border-radius: 9999px; margin-left: 0.125rem;">{{ $activeFilterCount }}</span>
                    @endif
                </button>
            </div>

            <!-- 적용된 필터 표시 -->
            @if($search || $activeFilterCount > 0)
                <div style="display: flex; flex-wrap: wrap; gap: 0.375rem; margin-bottom: 0.75rem;">
                    @if($search)
                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.188rem 0.5rem; font-size: 0.688rem; background-color: #e0f7f5; color: #047857; border-radius: 9999px;">
                            {{ $search }}
                            <button wire:click="$set('search', '')" style="background: none; border: none; cursor: pointer; padding: 0; color: #047857;">
                                <svg style="width: 0.75rem; height: 0.75rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </span>
                    @endif
                    @if($filterProcessStatus)
                        @php $statusLabels = ['checked_in' => '입실자', 'scheduled' => '입실예정', 'checked_out' => '퇴실자']; @endphp
                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.188rem 0.5rem; font-size: 0.688rem; background-color: #e0f7f5; color: #047857; border-radius: 9999px;">
                            {{ $statusLabels[$filterProcessStatus] ?? $filterProcessStatus }}
                            <button wire:click="$set('filterProcessStatus', '')" style="background: none; border: none; cursor: pointer; padding: 0; color: #047857;">
                                <svg style="width: 0.75rem; height: 0.75rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </span>
                    @endif
                    @if($filterPaymentStatus)
                        @php $paymentLabels = ['paid' => '납부완료', 'pending' => '미납', 'overdue' => '연체', 'waiting' => '대기']; @endphp
                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.188rem 0.5rem; font-size: 0.688rem; background-color: #e0f7f5; color: #047857; border-radius: 9999px;">
                            {{ $paymentLabels[$filterPaymentStatus] ?? $filterPaymentStatus }}
                            <button wire:click="$set('filterPaymentStatus', '')" style="background: none; border: none; cursor: pointer; padding: 0; color: #047857;">
                                <svg style="width: 0.75rem; height: 0.75rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </span>
                    @endif
                    @if($filterPaymentMethod)
                        @php $methodLabels = ['card' => '카드', 'transfer' => '계좌이체', 'cash' => '현금']; @endphp
                        <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.188rem 0.5rem; font-size: 0.688rem; background-color: #e0f7f5; color: #047857; border-radius: 9999px;">
                            {{ $methodLabels[$filterPaymentMethod] ?? $filterPaymentMethod }}
                            <button wire:click="$set('filterPaymentMethod', '')" style="background: none; border: none; cursor: pointer; padding: 0; color: #047857;">
                                <svg style="width: 0.75rem; height: 0.75rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Mobile Room Groups -->
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            @forelse($groupedTenants as $roomNumber => $tenants)
                @php
                    $primaryTenant = $tenants->first();
                    $hasMultipleTenants = $tenants->count() > 1;
                    $isExpanded = in_array($roomNumber, $expandedRooms ?? []);
                    $hasCashReceipt = $primaryTenant->latestCashReceipt !== null;
                @endphp

                <div style="background-color: #ffffff; border-radius: 0.5rem; border: 1px solid #e5e7eb; overflow: hidden;">
                    <!-- Primary Row Header -->
                    <div
                        @if($hasMultipleTenants) wire:click="toggleRoom('{{ $roomNumber }}')" @endif
                        style="padding: 1rem; display: flex; align-items: center; gap: 0.75rem; {{ $hasMultipleTenants ? 'cursor: pointer;' : '' }}"
                    >
                        @if($hasMultipleTenants)
                            <svg style="width: 1rem; height: 1rem; color: #6b7280; flex-shrink: 0; transition: transform 0.2s; {{ $isExpanded ? 'transform: rotate(90deg);' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        @endif

                        <div style="flex: 0 0 auto;">
                            <span style="font-weight: 700; font-size: 1rem; color: #111827;">{{ $roomNumber }}호</span>
                            @if($hasMultipleTenants)
                                <span style="font-size: 0.75rem; color: #6b7280; margin-left: 0.25rem;">({{ $tenants->count() }})</span>
                            @endif
                        </div>

                        <div style="flex: 0 0 auto;">
                            @if($primaryTenant->process_status === 'checked_in')
                                <span class="status-badge-checked-in" style="font-size: 0.7rem; padding: 0.125rem 0.5rem;">입실</span>
                            @elseif($primaryTenant->process_status === 'scheduled')
                                <span class="status-badge-scheduled" style="font-size: 0.7rem; padding: 0.125rem 0.5rem;">입실 예정</span>
                            @elseif($primaryTenant->process_status === 'checked_out')
                                <span class="status-badge-checked-out" style="font-size: 0.7rem; padding: 0.125rem 0.5rem;">퇴실</span>
                            @else
                                <span class="status-badge-scheduled" style="font-size: 0.7rem; padding: 0.125rem 0.5rem;">대기</span>
                            @endif
                        </div>

                        <div style="flex: 1; min-width: 0;">
                            <span style="font-weight: 500; color: #111827;">{{ $primaryTenant->name }}</span>
                        </div>

                        <div style="flex: 0 0 auto;">
                            @if($primaryTenant->payment_status === 'paid')
                                <span class="payment-badge-paid" style="font-size: 0.7rem; padding: 0.125rem 0.5rem;">완납</span>
                            @else
                                <span class="payment-badge-unpaid" style="font-size: 0.7rem; padding: 0.125rem 0.5rem;">미납</span>
                            @endif
                        </div>
                    </div>

                    <!-- Primary Tenant Details -->
                    <div style="padding: 0 1rem 1rem 1rem; border-top: 1px solid #f3f4f6;">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; font-size: 0.813rem; padding-top: 0.75rem;">
                            <div>
                                <span style="color: #6b7280;">결제 금액</span>
                                <p style="font-weight: 500; color: #111827; margin: 0.125rem 0 0 0;">{{ $primaryTenant->room?->monthly_rent ? number_format($primaryTenant->room->monthly_rent) . '원' : '-' }}</p>
                            </div>
                            <div>
                                <span style="color: #6b7280;">결제일</span>
                                <p style="font-weight: 500; color: #111827; margin: 0.125rem 0 0 0;">{{ $primaryTenant->payment_due_day ? '매월 ' . $primaryTenant->payment_due_day . '일' : '-' }}</p>
                            </div>
                            <div>
                                <span style="color: #6b7280;">실제 결제일</span>
                                <p style="font-weight: 500; color: #111827; margin: 0.125rem 0 0 0;">{{ $primaryTenant->actual_payment_date?->format('Y.m.d') ?? '-' }}</p>
                            </div>
                            <div>
                                <span style="color: #6b7280;">결제 방법</span>
                                <p style="font-weight: 500; color: #111827; margin: 0.125rem 0 0 0;">{{ $primaryTenant->payment_method_label }}</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                            @if($primaryTenant->payment_status !== 'paid')
                                <button
                                    wire:click="markAsPaid({{ $primaryTenant->id }})"
                                    wire:confirm="납부 완료 처리하시겠습니까?"
                                    style="flex: 1; padding: 0.5rem; font-size: 0.813rem; font-weight: 500; background-color: #22c55e; color: #ffffff; border: none; border-radius: 0.375rem; cursor: pointer;"
                                >
                                    결제 처리하기
                                </button>
                                @if($primaryTenant->phone)
                                    <a href="tel:{{ $primaryTenant->phone }}" style="flex: 1; padding: 0.5rem; font-size: 0.813rem; font-weight: 500; background-color: #ffffff; color: #374151; border: 1px solid #d1d5db; border-radius: 0.375rem; text-align: center; text-decoration: none;">
                                        연락하기
                                    </a>
                                @endif
                            @elseif($hasCashReceipt)
                                <button
                                    wire:click="viewCashReceipt({{ $primaryTenant->id }})"
                                    style="flex: 1; padding: 0.5rem; font-size: 0.813rem; font-weight: 500; background-color: #f3f4f6; color: #9ca3af; border: 1px solid #e5e7eb; border-radius: 0.375rem; cursor: pointer;"
                                >
                                    발행 완료 ✓
                                </button>
                                @if($primaryTenant->phone)
                                    <a href="tel:{{ $primaryTenant->phone }}" style="flex: 1; padding: 0.5rem; font-size: 0.813rem; font-weight: 500; background-color: #ffffff; color: #374151; border: 1px solid #d1d5db; border-radius: 0.375rem; text-align: center; text-decoration: none;">
                                        연락하기
                                    </a>
                                @endif
                            @else
                                <button
                                    wire:click="openCashReceiptModal({{ $primaryTenant->id }})"
                                    style="flex: 1; padding: 0.5rem; font-size: 0.813rem; font-weight: 500; background-color: #111827; color: #ffffff; border: none; border-radius: 0.375rem; cursor: pointer;"
                                >
                                    현금영수증 발행하기
                                </button>
                                @if($primaryTenant->phone)
                                    <a href="tel:{{ $primaryTenant->phone }}" style="flex: 1; padding: 0.5rem; font-size: 0.813rem; font-weight: 500; background-color: #ffffff; color: #374151; border: 1px solid #d1d5db; border-radius: 0.375rem; text-align: center; text-decoration: none;">
                                        연락하기
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Expanded Sub-tenants -->
                    @if($hasMultipleTenants && $isExpanded)
                        <div style="background-color: #f3f4f6; border-top: 1px solid #e5e7eb;">
                            @foreach($tenants as $index => $tenant)
                                @php $tenantHasCashReceipt = $tenant->latestCashReceipt !== null; @endphp
                                <div style="padding: 1rem; {{ !$loop->last ? 'border-bottom: 1px solid #e5e7eb;' : '' }}">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                        <span style="color: #9ca3af; font-size: 0.75rem;">└</span>
                                        @if($tenant->process_status === 'checked_in')
                                            <span class="status-badge-checked-in" style="font-size: 0.7rem; padding: 0.125rem 0.5rem;">입실</span>
                                        @elseif($tenant->process_status === 'scheduled')
                                            <span class="status-badge-scheduled" style="font-size: 0.7rem; padding: 0.125rem 0.5rem;">입실 예정</span>
                                        @elseif($tenant->process_status === 'checked_out')
                                            <span class="status-badge-checked-out" style="font-size: 0.7rem; padding: 0.125rem 0.5rem;">퇴실</span>
                                        @else
                                            <span class="status-badge-scheduled" style="font-size: 0.7rem; padding: 0.125rem 0.5rem;">대기</span>
                                        @endif
                                        <span style="font-weight: 500; color: #374151;">{{ $tenant->name }}</span>
                                        @if($tenant->payment_status === 'paid')
                                            <span class="payment-badge-paid" style="font-size: 0.7rem; padding: 0.125rem 0.5rem; margin-left: auto;">완납</span>
                                        @else
                                            <span class="payment-badge-unpaid" style="font-size: 0.7rem; padding: 0.125rem 0.5rem; margin-left: auto;">미납</span>
                                        @endif
                                    </div>

                                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; font-size: 0.75rem; margin-left: 1rem;">
                                        <div>
                                            <span style="color: #6b7280;">결제 금액</span>
                                            <p style="font-weight: 500; color: #374151; margin: 0.125rem 0 0 0;">{{ $tenant->room?->monthly_rent ? number_format($tenant->room->monthly_rent) . '원' : '-' }}</p>
                                        </div>
                                        <div>
                                            <span style="color: #6b7280;">결제일</span>
                                            <p style="font-weight: 500; color: #374151; margin: 0.125rem 0 0 0;">{{ $tenant->payment_due_day ? '매월 ' . $tenant->payment_due_day . '일' : '-' }}</p>
                                        </div>
                                        <div>
                                            <span style="color: #6b7280;">실제 결제일</span>
                                            <p style="font-weight: 500; color: #374151; margin: 0.125rem 0 0 0;">{{ $tenant->actual_payment_date?->format('Y.m.d') ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <span style="color: #6b7280;">결제 방법</span>
                                            <p style="font-weight: 500; color: #374151; margin: 0.125rem 0 0 0;">{{ $tenant->payment_method_label }}</p>
                                        </div>
                                    </div>

                                    <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem; margin-left: 1rem;">
                                        @if($tenant->payment_status !== 'paid')
                                            <button
                                                wire:click="markAsPaid({{ $tenant->id }})"
                                                wire:confirm="납부 완료 처리하시겠습니까?"
                                                style="flex: 1; padding: 0.375rem; font-size: 0.75rem; font-weight: 500; background-color: #22c55e; color: #ffffff; border: none; border-radius: 0.375rem; cursor: pointer;"
                                            >
                                                결제 처리하기
                                            </button>
                                        @elseif($tenantHasCashReceipt)
                                            <button
                                                wire:click="viewCashReceipt({{ $tenant->id }})"
                                                style="flex: 1; padding: 0.375rem; font-size: 0.75rem; font-weight: 500; background-color: #f3f4f6; color: #9ca3af; border: 1px solid #e5e7eb; border-radius: 0.375rem; cursor: pointer;"
                                            >
                                                발행 완료 ✓
                                            </button>
                                        @else
                                            <button
                                                wire:click="openCashReceiptModal({{ $tenant->id }})"
                                                style="flex: 1; padding: 0.375rem; font-size: 0.75rem; font-weight: 500; background-color: #374151; color: #ffffff; border: none; border-radius: 0.375rem; cursor: pointer;"
                                            >
                                                현금영수증 발행하기
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div style="text-align: center; padding: 3rem 1rem; background-color: #ffffff; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                    <svg style="margin: 0 auto; height: 3rem; width: 3rem; color: #9ca3af;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 style="margin-top: 0.5rem; font-size: 0.875rem; font-weight: 500; color: #111827;">수납 데이터가 없습니다</h3>
                    <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #6b7280;">입주자를 등록하면 수납 관리가 가능합니다.</p>
                </div>
            @endforelse
        </div>

        <!-- ============================== -->
        <!-- ④ 월 수입 분석 섹션 - Mobile -->
        <!-- ============================== -->
        <div style="background-color: #ffffff; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem; margin-top: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                <h2 style="font-size: 1rem; font-weight: 700; color: #111827; margin: 0;">월 수입 분석</h2>
                <select wire:model.live="analysisYear" style="padding: 0.25rem 0.5rem; font-size: 0.813rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background-color: #ffffff; color: #111827;">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}">{{ $y }}년</option>
                    @endfor
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem;">
                <div style="background-color: #17B7A5; border-radius: 0.5rem; padding: 1rem; color: #ffffff;">
                    <p style="font-size: 0.688rem; margin: 0 0 0.25rem 0; opacity: 0.8;">총 신고 금액</p>
                    <p style="font-size: 1.125rem; font-weight: 700; margin: 0;">₩{{ number_format($totalIncome) }}</p>
                </div>
                <div style="background-color: #17B7A5; border-radius: 0.5rem; padding: 1rem; color: #ffffff;">
                    <p style="font-size: 0.688rem; margin: 0 0 0.25rem 0; opacity: 0.8;">평균 월 신고 금액</p>
                    <p style="font-size: 1.125rem; font-weight: 700; margin: 0;">₩{{ number_format($avgIncome) }}</p>
                </div>
            </div>

            <!-- 바 차트 (모바일 - 가로 스크롤) -->
            <div style="overflow-x: auto; margin-bottom: 1.25rem;">
                <div class="bar-chart-container" style="min-width: 500px;">
                    @for($m = 1; $m <= 12; $m++)
                        @php
                            $val = $monthlyIncome[$m] ?? 0;
                            $height = $maxIncome > 0 ? max(($val / $maxIncome) * 100, 0) : 0;
                        @endphp
                        <div class="bar-chart-item">
                            <div class="bar-chart-bar" style="height: {{ $height > 0 ? $height . '%' : '2px' }}; {{ $val === 0 ? 'background-color: #e5e7eb;' : '' }}"></div>
                            <span class="bar-chart-label">{{ $m }}월</span>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- 월별 금액 그리드 -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem;">
                @for($m = 1; $m <= 12; $m++)
                    @php
                        $val = $monthlyIncome[$m] ?? 0;
                        $isMax = $val === $maxIncome && $val > 0;
                    @endphp
                    <div style="background-color: #f9fafb; border-radius: 0.375rem; padding: 0.5rem; text-align: center; position: relative;">
                        @if($isMax)
                            <span style="position: absolute; top: 0.25rem; right: 0.25rem; font-size: 0.563rem; font-weight: 600; background-color: #059669; color: #ffffff; padding: 0 0.25rem; border-radius: 9999px;">최고</span>
                        @endif
                        <p style="font-size: 0.688rem; color: #6b7280; margin: 0 0 0.125rem 0;">{{ $m }}월</p>
                        <p style="font-size: 0.75rem; font-weight: 600; color: {{ $val > 0 ? '#111827' : '#d1d5db' }}; margin: 0;">
                            {{ $val > 0 ? '₩' . number_format($val) : '—' }}
                        </p>
                    </div>
                @endfor
            </div>
        </div>

        <!-- ============================== -->
        <!-- ⑤ 월 수익률 분석 - Mobile -->
        <!-- ============================== -->
        <div style="background-color: #ffffff; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1.25rem; margin-top: 1rem;">
            <h2 style="font-size: 1rem; font-weight: 700; color: #111827; margin: 0 0 1rem 0;">월 수익률 분석</h2>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; margin-bottom: 1rem;">
                <div style="background-color: #ffffff; border: 1.5px solid #e5e7eb; border-radius: 0.75rem; padding: 0.75rem; text-align: left; overflow: hidden;">
                    <p style="font-size: 11px; color: #9ca3af; font-weight: 500; margin: 0 0 0.125rem 0;">총 입실료</p>
                    <p style="font-size: 14px; font-weight: 700; color: #111827; margin: 0;">{{ number_format($profitData['totalRent']) }}</p>
                </div>
                <div style="background-color: #ffffff; border: 1.5px solid #e5e7eb; border-radius: 0.75rem; padding: 0.75rem; text-align: left; overflow: hidden;">
                    <p style="font-size: 11px; color: #9ca3af; font-weight: 500; margin: 0 0 0.125rem 0;">고시원 월세</p>
                    <p style="font-size: 14px; font-weight: 700; color: #ef4444; margin: 0;">-{{ number_format($profitData['goshiwonRent']) }}</p>
                </div>
                <div style="background-color: #ffffff; border: 1.5px solid #e5e7eb; border-radius: 0.75rem; padding: 0.75rem; text-align: left; overflow: hidden;">
                    <p style="font-size: 11px; color: #9ca3af; font-weight: 500; margin: 0 0 0.125rem 0;">공과금</p>
                    <p style="font-size: 14px; font-weight: 700; color: #f97316; margin: 0;">-{{ number_format($profitData['utilities']) }}</p>
                </div>
                <div style="background-color: #F6FBF9; border: 1px solid #DCEFE8; border-radius: 0.75rem; padding: 0.75rem; text-align: left; overflow: hidden;">
                    <p style="font-size: 11px; color: #9ca3af; font-weight: 500; margin: 0 0 0.125rem 0;">순수익</p>
                    <p style="font-size: 14px; font-weight: 700; color: #22c55e; margin: 0;">{{ number_format($profitData['netProfit']) }}</p>
                </div>
            </div>

            <div style="display: flex; justify-content: center;">
                <button
                    wire:click="toggleRentSimulation"
                    style="width: 20rem; max-width: 100%; padding: 0.75rem 1rem; background-color: #1A1A1A; color: #ffffff; border: none; border-radius: 0.625rem; cursor: pointer; font-size: 0.813rem; font-weight: 600; text-align: center;"
                >
                    {{ $branchName }} 연간 수익률 👉 {{ $profitData['profitRate'] }}%
                </button>
            </div>
        </div>
    </div>

    <!-- ============================== -->
    <!-- Modals -->
    <!-- ============================== -->

    <!-- Payment Edit Modal -->
    <livewire:payment-management-modal wire:key="payment-management-modal" />

    <!-- Cash Receipt Modal -->
    <livewire:cash-receipt-modal wire:key="cash-receipt-modal" />

    <!-- ⑥ 월세 인상 시뮬레이션 모달 -->
    @if($showRentSimulation)
        @php $simResult = $this->getSimulationResult(); @endphp
        <div
            wire:click="toggleRentSimulation"
            style="position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 40;"
        ></div>

        <div style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; padding: 1rem;">
            <div style="background-color: #ffffff; border-radius: 0.75rem; max-width: 26rem; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
                <!-- Header -->
                <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e5e7eb;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">월세 인상 시뮬레이션</h3>
                </div>

                <div style="padding: 1.5rem;">
                    <!-- 금액 조절 컨트롤 -->
                    <div style="display: flex; align-items: center; justify-content: center; gap: 1.5rem; margin-bottom: 2rem;">
                        <button
                            wire:click="adjustRent(-1000)"
                            style="width: 4rem; height: 2.5rem; font-size: 0.875rem; font-weight: 600; background-color: #fef2f2; color: #ef4444; border: 1px solid #fecaca; border-radius: 0.5rem; cursor: pointer;"
                        >
                            -1,000원
                        </button>
                        <div style="text-align: center; min-width: 5rem;">
                            <span style="font-size: 1.25rem; font-weight: 700; color: {{ $rentAdjustment >= 0 ? '#059669' : '#ef4444' }};">
                                {{ $rentAdjustment >= 0 ? '+' : '' }}{{ number_format($rentAdjustment) }}원
                            </span>
                        </div>
                        <button
                            wire:click="adjustRent(1000)"
                            style="width: 4rem; height: 2.5rem; font-size: 0.875rem; font-weight: 600; background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 0.5rem; cursor: pointer;"
                        >
                            +1,000원
                        </button>
                    </div>

                    <!-- 결과 표시 -->
                    <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background-color: #f9fafb; border-radius: 0.5rem;">
                            <span style="font-size: 0.875rem; color: #6b7280;">월 추가 수입</span>
                            <span style="font-size: 1rem; font-weight: 700; color: {{ $simResult['monthlyExtra'] >= 0 ? '#059669' : '#ef4444' }};">
                                {{ $simResult['monthlyExtra'] >= 0 ? '+' : '' }}₩{{ number_format($simResult['monthlyExtra']) }}
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background-color: #f9fafb; border-radius: 0.5rem;">
                            <span style="font-size: 0.875rem; color: #6b7280;">연간 추가 수입</span>
                            <span style="font-size: 1rem; font-weight: 700; color: {{ $simResult['annualExtra'] >= 0 ? '#059669' : '#ef4444' }};">
                                {{ $simResult['annualExtra'] >= 0 ? '+' : '' }}₩{{ number_format($simResult['annualExtra']) }}
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background-color: #f9fafb; border-radius: 0.5rem;">
                            <span style="font-size: 0.875rem; color: #6b7280;">새로운 연간 수익률</span>
                            <span style="font-size: 1rem; font-weight: 700; color: {{ $simResult['newRate'] >= 0 ? '#059669' : '#ef4444' }};">
                                {{ $simResult['newRate'] }}%
                            </span>
                        </div>
                    </div>

                    <!-- CTA -->
                    <div style="background-color: #f9fafb; border-radius: 0.75rem; padding: 1.25rem; text-align: center; margin-bottom: 1rem;">
                        <p style="font-size: 0.813rem; color: #6b7280; margin: 0 0 0.75rem 0;">
                            💡 공실을 빠르게 채우는 방법이 궁금하신가요?<br>고집 컨설팅 팀이 무료로 상담해드립니다.
                        </p>
                        <button
                            type="button"
                            style="padding: 0.625rem 1.5rem; font-size: 0.875rem; font-weight: 600; background-color: #059669; color: #ffffff; border: none; border-radius: 0.5rem; cursor: pointer;"
                        >
                            무료 상담 받기
                        </button>
                    </div>

                    <!-- 닫기 -->
                    <button
                        wire:click="toggleRentSimulation"
                        type="button"
                        style="width: 100%; padding: 0.5rem; font-size: 0.875rem; color: #6b7280; background: none; border: none; cursor: pointer;"
                    >
                        닫기
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Filter Bottom Sheet -->
    @if($showMobileFilters)
        <div
            class="filter-backdrop active"
            wire:click="toggleMobileFilters"
            style="position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 40;"
        ></div>

        <div
            class="filter-bottomsheet active"
            style="position: fixed; bottom: 0; left: 0; right: 0; background-color: #ffffff; border-radius: 1rem 1rem 0 0; z-index: 50; max-height: 70vh; box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1); display: flex; flex-direction: column;"
        >
            <div style="flex-shrink: 0;">
                <div style="display: flex; justify-content: center; padding: 0.5rem;">
                    <div style="width: 2.5rem; height: 0.25rem; background-color: #d1d5db; border-radius: 9999px;"></div>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0 1rem 0.75rem 1rem; border-bottom: 1px solid #e5e7eb;">
                    <h3 style="font-size: 1rem; font-weight: 600; color: #111827; margin: 0;">필터</h3>
                    @if($activeFilterCount > 0)
                        <button wire:click="resetFilters" style="padding: 0.25rem 0.5rem; background: none; border: none; cursor: pointer; color: #ef4444; font-size: 0.875rem; font-weight: 500;">초기화</button>
                    @endif
                </div>
            </div>

            <div style="flex: 1; overflow-y: auto; padding: 1rem;">
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">입주 상태</label>
                        <select wire:model.live="filterProcessStatus" style="width: 100%; padding: 0.5rem 0.625rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background-color: #ffffff; color: #111827;">
                            <option value="">전체</option>
                            <option value="checked_in">입실자</option>
                            <option value="scheduled">입실예정</option>
                            <option value="checked_out">퇴실자</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">납부 상태</label>
                        <select wire:model.live="filterPaymentStatus" style="width: 100%; padding: 0.5rem 0.625rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background-color: #ffffff; color: #111827;">
                            <option value="">전체</option>
                            <option value="paid">납부완료</option>
                            <option value="pending">미납</option>
                            <option value="overdue">연체</option>
                            <option value="waiting">대기</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">결제 방법</label>
                        <select wire:model.live="filterPaymentMethod" style="width: 100%; padding: 0.5rem 0.625rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background-color: #ffffff; color: #111827;">
                            <option value="">전체</option>
                            <option value="card">카드</option>
                            <option value="transfer">계좌이체</option>
                            <option value="cash">현금</option>
                        </select>
                    </div>
                </div>
            </div>

            <div style="flex-shrink: 0; padding: 1rem; border-top: 1px solid #e5e7eb; background-color: #ffffff;">
                <button wire:click="toggleMobileFilters" style="width: 100%; padding: 0.625rem; font-size: 0.875rem; font-weight: 600; color: #ffffff; background-color: #06CBBB; border: none; border-radius: 0.375rem; cursor: pointer;">적용하기</button>
                <div style="height: env(safe-area-inset-bottom, 0);"></div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
