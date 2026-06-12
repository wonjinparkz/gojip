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
    <div class="flex justify-between items-start mb-2">
        <div class="flex flex-col">
            <h3 class="font-bold text-lg text-gray-800">{{ $getRecord()->room_number }}호</h3>
            <span class="text-sm text-gray-500">{{ $getRecord()->room_type }}</span>
        </div>
        <div class="flex items-center gap-2">
            @php
                $statusColors = [
                    'available' => 'bg-green-100 text-green-800',
                    'occupied' => 'bg-blue-100 text-blue-800',
                    'maintenance' => 'bg-red-100 text-red-800',
                ];
                $statusLabels = [
                    'available' => '입주가능',
                    'occupied' => '입주중',
                    'maintenance' => '수리중',
                ];
            @endphp
            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$getRecord()->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $statusLabels[$getRecord()->status] ?? '알 수 없음' }}
            </span>
            <button
                type="button"
                wire:click.stop="mountTableAction('delete', '{{ $getRecord()->key }}')"
                class="flex items-center justify-center w-7 h-7 bg-red-100/80 hover:bg-red-200 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 z-10"
                title="호실 삭제"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-red-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </button>
        </div>
    </div>
    
    <!-- Badges Row -->
    <div class="flex items-center gap-2 mb-3">
        @if($currentTenant && $currentTenant->is_short_term)
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                단기숙박
            </span>
        @endif
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
