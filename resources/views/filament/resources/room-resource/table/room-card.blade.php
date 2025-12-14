@php
    // 현재 입주 중인 tenant 정보 가져오기
    $today = now()->format('Y-m-d');
    $currentTenant = \App\Models\Tenant::where('room_id', $getRecord()->id)
        ->whereNotNull('move_in_date')
        ->whereDate('move_in_date', '<=', $today)
        ->where(function($query) use ($today) {
            $query->whereNull('move_out_date')
                ->orWhereDate('move_out_date', '>=', $today);
        })
        ->orderBy('move_in_date', 'desc')
        ->first();

    // 디버깅 로그 - 모든 호실
    \Log::info('호실 카드 렌더링:', [
        'room_number' => $getRecord()->room_number,
        'room_id' => $getRecord()->id,
        'today' => $today,
        'found_tenant' => $currentTenant ? $currentTenant->name : 'null',
        'is_short_term' => $currentTenant ? $currentTenant->is_short_term : 'null',
        'short_term_rent' => $currentTenant ? $currentTenant->short_term_monthly_rent : 'null',
        'short_term_deposit' => $currentTenant ? $currentTenant->short_term_deposit : 'null',
    ]);
@endphp

<div
    class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200 cursor-pointer"
    wire:click="mountTableAction('view', '{{ $getRecord()->getKey() }}')"
>
    <!-- Card Header -->
    <div class="mb-3">
        <div class="flex items-center gap-2 mb-1">
            <h3 class="text-lg font-bold text-gray-900">{{ $getRecord()->room_number }}</h3>

            {{-- 디버그 정보 표시 --}}
            @if($getRecord()->room_number == '301')
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                    DEBUG: {{ $currentTenant ? 'T:' . $currentTenant->name . ' ST:' . ($currentTenant->is_short_term ? 'Y' : 'N') : 'NO_TENANT' }}
                </span>
            @endif

            @if($getRecord()->status === 'occupied')
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-teal-100 text-teal-800">
                    입주중
                </span>
                @if($currentTenant && $currentTenant->is_short_term)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                        단기숙박
                    </span>
                @endif
            @elseif($getRecord()->status === 'available')
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                    입주가능
                </span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                    수리중
                </span>
            @endif
        </div>
        <p class="text-sm text-gray-600">{{ $getRecord()->room_type }}</p>
    </div>

    <!-- Card Body -->
    <div class="space-y-2 text-sm">
        <!-- 월세 -->
        <div class="flex justify-between">
            <span class="text-gray-600">월세</span>
            <div class="text-right">
                <div class="font-medium text-gray-900">₩{{ number_format($getRecord()->monthly_rent) }}</div>
                @if($currentTenant && $currentTenant->is_short_term && $currentTenant->short_term_monthly_rent)
                    <div class="text-xs text-amber-600 mt-0.5">
                        단기 ₩{{ number_format($currentTenant->short_term_monthly_rent) }}
                    </div>
                @endif
            </div>
        </div>

        <!-- 보증금 -->
        <div class="flex justify-between">
            <span class="text-gray-600">보증금</span>
            <div class="text-right">
                <div class="font-medium text-gray-900">₩{{ number_format($getRecord()->deposit) }}</div>
                @if($currentTenant && $currentTenant->is_short_term && $currentTenant->short_term_deposit !== null)
                    <div class="text-xs text-amber-600 mt-0.5">
                        단기 ₩{{ number_format($currentTenant->short_term_deposit) }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-100 my-2"></div>

        <!-- 입주자 -->
        <div class="flex justify-between">
            <span class="text-gray-600">입주자</span>
            <span class="text-gray-900">{{ $getRecord()->tenant_name ?? '-' }}</span>
        </div>

        <!-- 입주일 -->
        <div class="flex justify-between">
            <span class="text-gray-600">입주일</span>
            <span class="text-gray-900">{{ $getRecord()->move_in_date ? $getRecord()->move_in_date->format('Y.m.d') : '-' }}</span>
        </div>

        <!-- 퇴실일 -->
        <div class="flex justify-between">
            <span class="text-gray-600">퇴실일</span>
            <span class="text-gray-900">{{ $getRecord()->move_out_date ? $getRecord()->move_out_date->format('Y.m.d') : '-' }}</span>
        </div>
    </div>
</div>
