<div
    class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200 cursor-pointer"
    wire:click="mountTableAction('view', '{{ $getRecord()->getKey() }}')"
>
    <!-- Card Header -->
    <div class="mb-3">
        <div class="flex items-center gap-2 mb-1">
            <h3 class="text-lg font-bold text-gray-900">{{ $getRecord()->room_number }}</h3>
            @if($getRecord()->status === 'occupied')
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-teal-100 text-teal-800">
                    입주중
                </span>
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
            <span class="font-medium text-gray-900">₩{{ number_format($getRecord()->monthly_rent) }}</span>
        </div>

        <!-- 보증금 -->
        <div class="flex justify-between">
            <span class="text-gray-600">보증금</span>
            <span class="font-medium text-gray-900">₩{{ number_format($getRecord()->deposit) }}</span>
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
