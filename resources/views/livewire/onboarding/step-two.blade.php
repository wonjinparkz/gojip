<div>
    <!-- Error Message -->
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Configuration Card -->
    <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
        <!-- Title -->
        <div class="mb-6">
            <div class="flex items-center mb-2">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <h2 class="text-xl font-bold text-gray-900">층별 호실 구성 설정</h2>
            </div>
            <p class="text-sm text-gray-600 mb-1">층별로 호실 타입, 월세, 호실 수를 설정해주세요.</p>
            <p class="text-sm text-gray-600 mb-1">반층에도 호실이 있다면 포함해서 입력해주세요.(예: 1.5층은 1층, 2.5층은 2층)</p>
            <p class="text-sm text-gray-500 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                모든 정보는 초기 설정 완료 후 호실 관리 페이지에서 수정할 수 있어요.
            </p>
        </div>

        <!-- Branches Accordion -->
        <div class="space-y-4">
            @foreach($branches as $branchIndex => $branch)
                <div class="border border-gray-200 rounded-lg">
                    <!-- Accordion Header -->
                    <button
                        type="button"
                        wire:click="toggleBranch({{ $branchIndex }})"
                        class="w-full px-4 py-3 flex items-center justify-between bg-[#F9FBFC] hover:bg-gray-100 rounded-lg"
                    >
                        <span class="font-semibold text-gray-900">{{ $branch['name'] }}</span>
                        <svg
                            class="w-5 h-5 transition-transform {{ $expandedBranches[$branchIndex] ? 'rotate-180' : '' }}"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Accordion Content -->
                    @if($expandedBranches[$branchIndex])
                        <div class="px-4 pb-4 space-y-6">
                            @foreach(range($branch['start_floor'], $branch['end_floor']) as $floor)
                                <div class="border-t border-gray-100 pt-4">
                                    <h4 class="font-semibold text-gray-900 mb-4">{{ $floor }}층</h4>

                                    <!-- Floor Rooms -->
                                    @if(isset($floorRooms[$branchIndex][$floor]))
                                        @foreach($floorRooms[$branchIndex][$floor] as $roomIndex => $room)
                                            <div class="grid grid-cols-3 gap-3 mb-3">
                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1">호실 타입</label>
                                                    <input
                                                        type="text"
                                                        wire:model="floorRooms.{{ $branchIndex }}.{{ $floor }}.{{ $roomIndex }}.room_type"
                                                        placeholder="예: 스탠다드룸"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400"
                                                    />
                                                </div>

                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1">월세 (원)</label>
                                                    <input
                                                        type="number"
                                                        wire:model="floorRooms.{{ $branchIndex }}.{{ $floor }}.{{ $roomIndex }}.monthly_rent"
                                                        placeholder="0"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400"
                                                    />
                                                </div>

                                                <div class="flex items-end gap-2">
                                                    <div class="flex-1">
                                                        <label class="block text-xs text-gray-600 mb-1">호실 수</label>
                                                        <input
                                                            type="number"
                                                            wire:model="floorRooms.{{ $branchIndex }}.{{ $floor }}.{{ $roomIndex }}.room_count"
                                                            min="1"
                                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400"
                                                        />
                                                    </div>
                                                    @if($roomIndex > 0)
                                                        <button
                                                            type="button"
                                                            wire:click="removeFloorRoom({{ $branchIndex }}, {{ $floor }}, {{ $roomIndex }})"
                                                            class="px-2 py-2 text-red-500 hover:text-red-700"
                                                        >
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    <!-- Add Room Button -->
                                    <button
                                        type="button"
                                        wire:click="addFloorRoom({{ $branchIndex }}, {{ $floor }})"
                                        class="text-sm text-teal-400 hover:text-teal-500 flex items-center mt-2"
                                    >
                                        <span class="mr-1">+</span>
                                        호실 타입 추가
                                    </button>

                                    <!-- Excluded Rooms Checkbox -->
                                    <div class="mt-4">
                                        <label class="flex items-center text-sm text-gray-700 mb-3">
                                            <input
                                                type="checkbox"
                                                wire:model.live="showExcludedRooms.{{ $branchIndex }}.{{ $floor }}"
                                                class="rounded border-gray-300 text-teal-400 focus:ring-teal-400 mr-2"
                                            />
                                            혹시 해당 층에서 제외해야 하는 호실 번호가 있나요?
                                        </label>

                                        @if(isset($showExcludedRooms[$branchIndex][$floor]) && $showExcludedRooms[$branchIndex][$floor])
                                            <div class="ml-6 mt-2">
                                                <label class="block text-xs text-gray-600 mb-2">제외할 호실 번호 (쉼표로 구분)</label>
                                                <input
                                                    type="text"
                                                    wire:model="excludedRooms.{{ $branchIndex }}.{{ $floor }}"
                                                    placeholder="예: 201, 204, 205"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400"
                                                />
                                                <p class="text-xs text-gray-500 mt-1">호실 번호를 쉼표(,)로 구분하여 입력해주세요.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <!-- Extra Rooms Section -->
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="font-semibold text-gray-900 mb-3">기타</h4>
                                <label class="flex items-center text-sm text-gray-700 mb-4">
                                    <input
                                        type="checkbox"
                                        wire:model.live="hasExtraRooms.{{ $branchIndex }}"
                                        class="rounded border-gray-300 text-teal-400 focus:ring-teal-400 mr-2"
                                    />
                                    위에 입력한 방 외에 추가적으로 관리해야 하는 방이 있다면 선택해주세요.
                                </label>

                                @if(isset($hasExtraRooms[$branchIndex]) && $hasExtraRooms[$branchIndex] && isset($extraRooms[$branchIndex]))
                                    @foreach($extraRooms[$branchIndex] as $roomIndex => $room)
                                        <div class="grid grid-cols-3 gap-3 mb-3">
                                            <div>
                                                <label class="block text-xs text-gray-600 mb-1">호실 타입</label>
                                                <input
                                                    type="text"
                                                    wire:model="extraRooms.{{ $branchIndex }}.{{ $roomIndex }}.room_type"
                                                    placeholder="예: 옥탑방"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400"
                                                />
                                            </div>

                                            <div>
                                                <label class="block text-xs text-gray-600 mb-1">월세 (원)</label>
                                                <input
                                                    type="number"
                                                    wire:model="extraRooms.{{ $branchIndex }}.{{ $roomIndex }}.monthly_rent"
                                                    placeholder="0"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400"
                                                />
                                            </div>

                                            <div class="flex items-end gap-2">
                                                <div class="flex-1">
                                                    <label class="block text-xs text-gray-600 mb-1">호실 수</label>
                                                    <input
                                                        type="number"
                                                        wire:model="extraRooms.{{ $branchIndex }}.{{ $roomIndex }}.room_count"
                                                        min="1"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400"
                                                    />
                                                </div>
                                                @if($roomIndex > 0)
                                                    <button
                                                        type="button"
                                                        wire:click="removeExtraRoom({{ $branchIndex }}, {{ $roomIndex }})"
                                                        class="px-2 py-2 text-red-500 hover:text-red-700"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach

                                    <button
                                        type="button"
                                        wire:click="addExtraRoom({{ $branchIndex }})"
                                        class="text-sm text-teal-400 hover:text-teal-500 flex items-center"
                                    >
                                        <span class="mr-1">+</span>
                                        기타 호실 추가
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Confirmation Card -->
    <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
        <div class="mb-4">
            <div class="flex items-center text-teal-400 mb-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <h3 class="text-lg font-bold">아래 내용대로 초기 설정을 완료하시겠어요?</h3>
            </div>
            <p class="text-sm text-gray-500 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                모든 정보는 초기 설정 완료 후 호실 관리 페이지에서 수정할 수 있어요.
            </p>
        </div>

        <!-- Summary Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($branches as $branchIndex => $branch)
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-bold text-gray-900 mb-3">{{ $branch['name'] }}</h4>

                    <!-- Branch Info -->
                    <div class="mb-3">
                        <div class="flex items-center text-sm text-gray-700 mb-1">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="font-semibold">지점명</span>
                        </div>
                        <p class="text-sm text-gray-600 ml-5">{{ $branch['name'] }}</p>
                    </div>

                    <!-- Floors Info -->
                    <div class="mb-3">
                        <div class="flex items-center text-sm text-gray-700 mb-1">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="font-semibold">층수 설정</span>
                        </div>
                        <p class="text-sm text-gray-600 ml-5">총 {{ $branch['end_floor'] - $branch['start_floor'] + 1 }}개 층({{ $branch['start_floor'] }}층, {{ $branch['end_floor'] }}층)</p>
                    </div>

                    <!-- Floor Rooms Summary -->
                    <div class="mb-3">
                        <div class="flex items-center text-sm text-gray-700 mb-1">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="font-semibold">층별 호실 구성</span>
                        </div>
                        @if(isset($floorRooms[$branchIndex]))
                            @foreach($floorRooms[$branchIndex] as $floor => $rooms)
                                <div class="ml-5 mb-2">
                                    <p class="text-sm font-medium text-gray-700">{{ $floor }}층: {{ count($rooms) }}개</p>
                                    @foreach($rooms as $room)
                                        <p class="text-xs text-gray-600 ml-3">• {{ $room['room_type'] }} - {{ number_format($room['monthly_rent']) }}원 - {{ $room['room_count'] }}개</p>
                                    @endforeach
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- Extra Rooms Summary -->
                    @if($hasExtraRooms[$branchIndex] && isset($extraRooms[$branchIndex]) && count($extraRooms[$branchIndex]) > 0)
                        <div class="mb-3">
                            <p class="text-sm font-medium text-gray-700 mb-1">기타: {{ count($extraRooms[$branchIndex]) }}개</p>
                            @foreach($extraRooms[$branchIndex] as $room)
                                <p class="text-xs text-gray-600 ml-3">• {{ $room['room_type'] }} - {{ number_format($room['monthly_rent']) }}원 - {{ $room['room_count'] }}개</p>
                            @endforeach
                        </div>
                    @endif

                    <!-- Total Rooms -->
                    @php
                        $totalRooms = 0;
                        if(isset($floorRooms[$branchIndex])) {
                            foreach($floorRooms[$branchIndex] as $rooms) {
                                foreach($rooms as $room) {
                                    $totalRooms += $room['room_count'];
                                }
                            }
                        }
                        if($hasExtraRooms[$branchIndex] && isset($extraRooms[$branchIndex])) {
                            foreach($extraRooms[$branchIndex] as $room) {
                                $totalRooms += $room['room_count'];
                            }
                        }
                    @endphp
                    <div class="border-t border-gray-200 pt-2">
                        <p class="text-sm font-semibold text-gray-900">총 {{ $totalRooms }}개 호실</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-4">
        <button
            type="button"
            wire:click="previousStep"
            class="flex-1 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-semibold py-3 rounded-lg transition duration-150 ease-in-out"
        >
            이전 단계
        </button>
        <button
            type="button"
            wire:click="completeOnboarding"
            wire:loading.attr="disabled"
            class="flex-1 bg-teal-400 hover:bg-teal-500 text-white font-semibold py-3 rounded-lg transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <span wire:loading.remove wire:target="completeOnboarding">초기 설정 완료</span>
            <span wire:loading wire:target="completeOnboarding">저장 중...</span>
        </button>
    </div>
</div>
