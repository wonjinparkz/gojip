<div class="w-full bg-white h-screen md:h-auto md:max-h-full md:min-h-0 md:rounded-2xl md:shadow-lg flex flex-col">
    <!-- Mobile Header (Back Button) -->
    <div class="md:hidden p-4 border-b border-gray-100 flex items-center flex-shrink-0">
        <button type="button" class="text-gray-800 hover:bg-gray-100 rounded-full p-1" wire:click="previousStep">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
    </div>

    <!-- Error Message -->
    @if (session()->has('error'))
        <div class="mx-6 mt-4 md:m-0 md:bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex-shrink-0">
            {{ session('error') }}
        </div>
    @endif

    <!-- Content -->
    <div class="flex-grow p-6 md:p-8 overflow-y-auto md:min-h-0">
        <!-- Configuration Section -->
        <div class="mb-8">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-2">층별 호실 구성 설정</h2>
                <p class="text-sm text-gray-600 mb-1">층별로 호실 타입, 월세, 호실 수를 설정해주세요.</p>
                <p class="text-xs text-gray-500">*반층(1.5층 등)은 가까운 층에 포함해서 입력해주세요.</p>
            </div>

            <!-- Branches Card Layout -->
            <div class="space-y-6">
                @foreach($branches as $branchIndex => $branch)
                    @php
                        $floors = $branch['floors'] ?? range($branch['start_floor'], $branch['end_floor']);
                        $allFloors = [...$floors, '기타'];
                        $selectedFloor = $selectedFloors[$branchIndex] ?? $floors[0] ?? 1;
                    @endphp
                    <div class="bg-card text-card-foreground shadow-sm border border-gray-200 rounded-none md:rounded-xl -mx-4 md:mx-0">
                        <!-- Card Header -->
                        <div class="flex flex-col space-y-1.5 p-6 bg-gray-50 py-3 rounded-none md:rounded-t-xl">
                            <div class="font-semibold tracking-tight text-base">{{ $branch['name'] }}</div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-6 px-4 md:px-6 pt-6 pb-2">
                            <!-- Floor Tabs -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach($floors as $floor)
                                    <button
                                        type="button"
                                        wire:click="selectFloor({{ $branchIndex }}, {{ $floor }})"
                                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 focus-visible:ring-offset-0 disabled:pointer-events-none disabled:opacity-50 border h-9 rounded-xl text-sm px-4 {{ $selectedFloor == $floor ? 'bg-[#50d0d0] text-white border-[#50d0d0] hover:bg-[#40c0c0] hover:border-[#40c0c0]' : 'bg-background border-gray-300 text-gray-600 hover:border-[#50d0d0] hover:text-[#50d0d0]' }}"
                                    >
                                        {{ $floor }}층
                                    </button>
                                @endforeach
                                <button
                                    type="button"
                                    wire:click="selectFloor({{ $branchIndex }}, '기타')"
                                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-0 focus-visible:ring-offset-0 disabled:pointer-events-none disabled:opacity-50 border h-9 rounded-xl text-sm px-4 {{ $selectedFloor === '기타' ? 'bg-[#50d0d0] text-white border-[#50d0d0] hover:bg-[#40c0c0] hover:border-[#40c0c0]' : 'bg-background border-gray-300 text-gray-600 hover:border-[#50d0d0] hover:text-[#50d0d0]' }}"
                                >
                                    기타
                                </button>
                            </div>

                            <!-- Floor Content -->
                            <div class="space-y-6">
                                @if($selectedFloor !== '기타')
                                    <div>
                                        <div class="pb-0 pt-0 floor-section">
                                            <div class="space-y-3">
                                                <!-- Floor Rooms -->
                                                @if(isset($floorRooms[$branchIndex][$selectedFloor]))
                                                    @foreach($floorRooms[$branchIndex][$selectedFloor] as $roomIndex => $room)
                                                        <div wire:key="room-{{ $branchIndex }}-{{ $selectedFloor }}-{{ $roomIndex }}" class="relative p-4 space-y-3 bg-gray-50 border border-gray-200 rounded-xl">
                                                            @if($roomIndex > 0)
                                                                <button
                                                                    type="button"
                                                                    wire:click="removeFloorRoom({{ $branchIndex }}, {{ $selectedFloor }}, {{ $roomIndex }})"
                                                                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-100 text-red-500 rounded-full flex items-center justify-center hover:bg-red-200"
                                                                >
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                </button>
                                                            @endif
                                                            
                                                            <!-- Row 1: Select boxes -->
                                                            <div class="grid grid-cols-3 gap-2 md:gap-3">
                                                                <div>
                                                                    <label class="text-xs text-gray-500 font-medium">호실 유형</label>
                                                                    <select
                                                                        wire:model.live="floorRooms.{{ $branchIndex }}.{{ $selectedFloor }}.{{ $roomIndex }}.room_category"
                                                                        class="flex h-10 w-full border border-gray-300 bg-white px-3 py-2 focus:outline-none focus:ring-0 focus:border-gray-400 mt-0.5 rounded-lg text-sm appearance-none"
                                                                    >
                                                                        <option value="">선택</option>
                                                                        @foreach($operationalSettings['room_types'] ?? ['원룸', '샤워룸', '미니룸'] as $roomType)
                                                                            <option value="{{ $roomType }}">{{ $roomType }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div>
                                                                    <label class="text-xs text-gray-500 font-medium">창 구조</label>
                                                                    <select
                                                                        wire:model.live="floorRooms.{{ $branchIndex }}.{{ $selectedFloor }}.{{ $roomIndex }}.window_structure"
                                                                        class="flex h-10 w-full border border-gray-300 bg-white px-3 py-2 focus:outline-none focus:ring-0 focus:border-gray-400 mt-0.5 rounded-lg text-sm appearance-none"
                                                                    >
                                                                        <option value="">선택</option>
                                                                        @php
                                                                            $windowSetting = $operationalSettings['window_structure'] ?? 'both';
                                                                        @endphp
                                                                        @if($windowSetting === 'outer' || $windowSetting === 'both')
                                                                            <option value="외창">외창</option>
                                                                        @endif
                                                                        @if($windowSetting === 'inner' || $windowSetting === 'both')
                                                                            <option value="내창">내창</option>
                                                                        @endif
                                                                    </select>
                                                                </div>
                                                                <div>
                                                                    <label class="text-xs text-gray-500 font-medium">남녀구분</label>
                                                                    <select
                                                                        wire:model.live="floorRooms.{{ $branchIndex }}.{{ $selectedFloor }}.{{ $roomIndex }}.gender"
                                                                        class="flex h-10 w-full border border-gray-300 bg-white px-3 py-2 focus:outline-none focus:ring-0 focus:border-gray-400 mt-0.5 rounded-lg text-sm appearance-none"
                                                                    >
                                                                        <option value="">선택</option>
                                                                        @php
                                                                            $genderSetting = $operationalSettings['gender_division'] ?? 'mixed';
                                                                        @endphp
                                                                        @if($genderSetting === 'separated')
                                                                            <option value="남성">남성</option>
                                                                            <option value="여성">여성</option>
                                                                        @endif
                                                                        <option value="성별무관">성별 무관</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <!-- Row 2: Input fields -->
                                                            <div class="grid grid-cols-3 gap-2 md:gap-3">
                                                                <div>
                                                                    <label class="text-xs text-gray-500 font-medium">호실 타입</label>
                                                                    <input
                                                                        type="text"
                                                                        wire:model.live.debounce.300ms="floorRooms.{{ $branchIndex }}.{{ $selectedFloor }}.{{ $roomIndex }}.room_type"
                                                                        placeholder="예: 스탠다드"
                                                                        class="flex h-10 w-full border border-gray-300 bg-white px-3 py-2 placeholder:text-gray-400 focus:outline-none focus:ring-0 focus:border-gray-400 mt-0.5 rounded-lg text-sm"
                                                                    />
                                                                </div>
                                                                <div>
                                                                    <label class="text-xs text-gray-500 font-medium">월 입실료</label>
                                                                    <input
                                                                        type="number"
                                                                        wire:model.live.debounce.300ms="floorRooms.{{ $branchIndex }}.{{ $selectedFloor }}.{{ $roomIndex }}.monthly_rent"
                                                                        placeholder="0"
                                                                        class="flex h-10 w-full border border-gray-300 bg-white px-3 py-2 placeholder:text-gray-400 focus:outline-none focus:ring-0 focus:border-gray-400 mt-0.5 rounded-lg text-sm"
                                                                    />
                                                                </div>
                                                                <div>
                                                                    <label class="text-xs text-gray-500 font-medium">호실 수</label>
                                                                    <input
                                                                        type="number"
                                                                        wire:model.live.debounce.300ms="floorRooms.{{ $branchIndex }}.{{ $selectedFloor }}.{{ $roomIndex }}.room_count"
                                                                        placeholder="1"
                                                                        min="1"
                                                                        class="flex h-10 w-full border border-gray-300 bg-white px-3 py-2 placeholder:text-gray-400 focus:outline-none focus:ring-0 focus:border-gray-400 mt-0.5 rounded-lg text-sm"
                                                                    />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif

                                                <!-- Add Room Type Button -->
                                                <div class="py-2">
                                                    <button
                                                        type="button"
                                                        wire:click="addFloorRoom({{ $branchIndex }}, {{ $selectedFloor }})"
                                                        class="flex items-center text-[#50d0d0] hover:text-[#40c0c0] text-sm font-semibold"
                                                    >
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M5 12h14"></path>
                                                            <path d="M12 5v14"></path>
                                                        </svg>
                                                        호실 타입 추가
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Extra Rooms Section (기타 tab) -->
                                    <div>
                                        <div class="pb-0 pt-0">
                                            <label class="flex items-center text-xs font-medium text-gray-700 cursor-pointer mb-4">
                                                <input
                                                    type="checkbox"
                                                    wire:model.live="hasExtraRooms.{{ $branchIndex }}"
                                                    class="rounded border-gray-300 text-teal-400 focus:ring-teal-400 mr-2"
                                                />
                                                위에 입력한 방 외에 추가 공간이 있나요? (옥탑 등)
                                            </label>

                                            @if(isset($hasExtraRooms[$branchIndex]) && $hasExtraRooms[$branchIndex] && isset($extraRooms[$branchIndex]))
                                                <div class="space-y-3">
                                                    @foreach($extraRooms[$branchIndex] as $roomIndex => $room)
                                                        <div wire:key="extra-room-{{ $branchIndex }}-{{ $roomIndex }}" class="relative p-4 space-y-3 bg-gray-50 border border-gray-200 rounded-xl">
                                                            @if($roomIndex > 0)
                                                                <button
                                                                    type="button"
                                                                    wire:click="removeExtraRoom({{ $branchIndex }}, {{ $roomIndex }})"
                                                                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-100 text-red-500 rounded-full flex items-center justify-center hover:bg-red-200"
                                                                >
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                </button>
                                                            @endif

                                                            <!-- Row 1: Select boxes -->
                                                            <div class="grid grid-cols-3 gap-2 md:gap-3">
                                                                <div>
                                                                    <label class="text-xs text-gray-500 font-medium">호실 유형</label>
                                                                    <select
                                                                        wire:model.live="extraRooms.{{ $branchIndex }}.{{ $roomIndex }}.room_category"
                                                                        class="flex h-10 w-full border border-gray-300 bg-white px-3 py-2 focus:outline-none focus:ring-0 focus:border-gray-400 mt-0.5 rounded-lg text-sm appearance-none"
                                                                    >
                                                                        <option value="">선택</option>
                                                                        @foreach($operationalSettings['room_types'] ?? ['원룸', '샤워룸', '미니룸'] as $roomType)
                                                                            <option value="{{ $roomType }}">{{ $roomType }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div>
                                                                    <label class="text-xs text-gray-500 font-medium">창 구조</label>
                                                                    <select
                                                                        wire:model.live="extraRooms.{{ $branchIndex }}.{{ $roomIndex }}.window_structure"
                                                                        class="flex h-10 w-full border border-gray-300 bg-white px-3 py-2 focus:outline-none focus:ring-0 focus:border-gray-400 mt-0.5 rounded-lg text-sm appearance-none"
                                                                    >
                                                                        <option value="">선택</option>
                                                                        @php
                                                                            $windowSetting = $operationalSettings['window_structure'] ?? 'both';
                                                                        @endphp
                                                                        @if($windowSetting === 'outer' || $windowSetting === 'both')
                                                                            <option value="외창">외창</option>
                                                                        @endif
                                                                        @if($windowSetting === 'inner' || $windowSetting === 'both')
                                                                            <option value="내창">내창</option>
                                                                        @endif
                                                                    </select>
                                                                </div>
                                                                <div>
                                                                    <label class="text-xs text-gray-500 font-medium">남녀구분</label>
                                                                    <select
                                                                        wire:model.live="extraRooms.{{ $branchIndex }}.{{ $roomIndex }}.gender"
                                                                        class="flex h-10 w-full border border-gray-300 bg-white px-3 py-2 focus:outline-none focus:ring-0 focus:border-gray-400 mt-0.5 rounded-lg text-sm appearance-none"
                                                                    >
                                                                        <option value="">선택</option>
                                                                        @php
                                                                            $genderSetting = $operationalSettings['gender_division'] ?? 'mixed';
                                                                        @endphp
                                                                        @if($genderSetting === 'separated')
                                                                            <option value="남성">남성</option>
                                                                            <option value="여성">여성</option>
                                                                        @endif
                                                                        <option value="성별무관">성별 무관</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <!-- Row 2: Input fields -->
                                                            <div class="grid grid-cols-3 gap-2 md:gap-3">
                                                                <div>
                                                                    <label class="text-xs text-gray-500 font-medium">호실 타입</label>
                                                                    <input
                                                                        type="text"
                                                                        wire:model.live.debounce.300ms="extraRooms.{{ $branchIndex }}.{{ $roomIndex }}.room_type"
                                                                        placeholder="예: 옥탑방"
                                                                        class="flex h-10 w-full border border-gray-300 bg-white px-3 py-2 placeholder:text-gray-400 focus:outline-none focus:ring-0 focus:border-gray-400 mt-0.5 rounded-lg text-sm"
                                                                    />
                                                                </div>
                                                                <div>
                                                                    <label class="text-xs text-gray-500 font-medium">월 입실료</label>
                                                                    <input
                                                                        type="number"
                                                                        wire:model.live.debounce.300ms="extraRooms.{{ $branchIndex }}.{{ $roomIndex }}.monthly_rent"
                                                                        placeholder="0"
                                                                        class="flex h-10 w-full border border-gray-300 bg-white px-3 py-2 placeholder:text-gray-400 focus:outline-none focus:ring-0 focus:border-gray-400 mt-0.5 rounded-lg text-sm"
                                                                    />
                                                                </div>
                                                                <div>
                                                                    <label class="text-xs text-gray-500 font-medium">호실 수</label>
                                                                    <input
                                                                        type="number"
                                                                        wire:model.live.debounce.300ms="extraRooms.{{ $branchIndex }}.{{ $roomIndex }}.room_count"
                                                                        placeholder="1"
                                                                        min="1"
                                                                        class="flex h-10 w-full border border-gray-300 bg-white px-3 py-2 placeholder:text-gray-400 focus:outline-none focus:ring-0 focus:border-gray-400 mt-0.5 rounded-lg text-sm"
                                                                    />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                    <!-- Add Extra Room Button -->
                                                    <div class="py-2">
                                                        <button
                                                            type="button"
                                                            wire:click="addExtraRoom({{ $branchIndex }})"
                                                            class="flex items-center text-[#50d0d0] hover:text-[#40c0c0] text-sm font-semibold"
                                                        >
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M5 12h14"></path>
                                                                <path d="M12 5v14"></path>
                                                            </svg>
                                                            호실 타입 추가
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Room Summary Section -->
        <div class="-mx-4 md:mx-0 rounded-lg md:rounded-2xl mt-8" style="background-color: #FAFAFC;">
            <!-- Header -->
            <div class="p-6 pb-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xl">📁</span>
                    <h3 class="text-lg font-bold text-gray-900">생성된 호실 요약</h3>
                </div>
                <div class="flex items-center gap-1.5 text-gray-500 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 16v-4"></path>
                        <path d="M12 8h.01"></path>
                    </svg>
                    <span>제외할 호실은 삭제하고, 필요한 호실은 직접 추가하세요.</span>
                </div>
            </div>

            <!-- Floor List -->
            <div class="px-6 pb-6">
                @foreach($branches as $branchIndex => $branch)
                    @php
                        $floors = $branch['floors'] ?? range($branch['start_floor'], $branch['end_floor']);
                    @endphp

                    {{-- Branch Header --}}
                    <div class="inline-block {{ !$loop->first ? 'mt-6 pt-6 border-t-2 border-gray-300 w-full' : '' }}">
                        <span class="font-bold text-base text-gray-900 bg-gray-100 rounded-lg py-2.5 pr-4 pl-3 inline-block mb-4">{{ $branch['name'] }}</span>
                    </div>

                    @foreach($floors as $floor)
                        <div class="border-t border-gray-200 pt-4 {{ !$loop->first ? 'mt-4' : '' }}">
                            <h4 class="text-lg font-bold text-gray-900 mb-3">{{ $floor }}층</h4>

                            @php
                                $floorHasRooms = isset($floorRooms[$branchIndex][$floor]) && count($floorRooms[$branchIndex][$floor]) > 0;
                                $configuredRooms = [];
                                $roomNumberOffset = 1;

                                if ($floorHasRooms) {
                                    foreach ($floorRooms[$branchIndex][$floor] as $roomIdx => $room) {
                                        // Check if user has actually configured this room (requires room_category AND monthly_rent > 0)
                                        $isConfigured = !empty($room['room_category']) && $room['monthly_rent'] > 0;

                                        if ($isConfigured) {
                                            // Build category info string
                                            $categoryParts = [];
                                            if (!empty($room['room_category'])) $categoryParts[] = $room['room_category'];
                                            if (!empty($room['window_structure'])) $categoryParts[] = $room['window_structure'];
                                            if (!empty($room['gender'])) $categoryParts[] = $room['gender'];
                                            $categoryInfo = !empty($categoryParts) ? implode(' · ', $categoryParts) : '';

                                            // Build room name with price
                                            $roomName = $room['room_type'] ?: '미입력';
                                            if (!empty($room['window_structure'])) {
                                                $roomName .= ' ' . $room['window_structure'];
                                            }
                                            $roomName .= ' · ' . number_format($room['monthly_rent']) . '원 · ' . $room['room_count'] . '실';

                                            // Get room numbers from component method or generate
                                            $roomKey = "{$branchIndex}-{$floor}-{$roomIdx}";
                                            $roomNumbers = $this->getRoomNumbersForDisplay($branchIndex, $floor, $roomIdx);

                                            $configuredRooms[] = [
                                                'roomIndex' => $roomIdx,
                                                'categoryInfo' => $categoryInfo,
                                                'roomName' => $roomName,
                                                'roomNumbers' => $roomNumbers,
                                                'roomKey' => $roomKey
                                            ];
                                        }
                                        $roomNumberOffset += (int) ($room['room_count'] ?? 1);
                                    }
                                }
                            @endphp

                            @if(count($configuredRooms) > 0)
                                @foreach($configuredRooms as $configuredRoom)
                                    @php
                                        $isEditing = $editingRoomKey === $configuredRoom['roomKey'];
                                    @endphp
                                    <div class="mb-5 last:mb-0">
                                        @if(!empty($configuredRoom['categoryInfo']))
                                            <p class="text-base text-gray-400 mb-1">{{ $configuredRoom['categoryInfo'] }}</p>
                                        @endif
                                        <p class="text-base font-normal mb-2" style="color: #3E4046;">{{ $configuredRoom['roomName'] }}</p>

                                        @if($isEditing)
                                            {{-- Edit Mode: Show editable chips --}}
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="text-lg">🚪</span>
                                                @foreach($configuredRoom['roomNumbers'] as $numIndex => $roomNum)
                                                    @if($editingRoomNumberIndex === $numIndex)
                                                        {{-- Editing this specific room number --}}
                                                        <div class="inline-flex items-center border-2 rounded-lg px-3 py-1.5" style="border-color: #51D0CE;">
                                                            <input
                                                                type="text"
                                                                wire:model="editingRoomNumberValue"
                                                                wire:keydown.enter="saveRoomNumber({{ $branchIndex }}, {{ $floor }}, {{ $configuredRoom['roomIndex'] }})"
                                                                wire:keydown.escape="cancelEditRoomNumber"
                                                                wire:blur="saveRoomNumber({{ $branchIndex }}, {{ $floor }}, {{ $configuredRoom['roomIndex'] }})"
                                                                class="w-12 text-center border-0 p-0 focus:ring-0 text-sm font-medium"
                                                                style="color: #51D0CE;"
                                                                autofocus
                                                            />
                                                        </div>
                                                    @else
                                                        {{-- Normal chip with X button --}}
                                                        <div class="inline-flex items-center bg-gray-100 rounded-lg px-3 py-1.5 gap-1.5">
                                                            <button
                                                                type="button"
                                                                wire:click="startEditRoomNumber({{ $numIndex }}, '{{ $roomNum }}')"
                                                                class="text-sm font-medium hover:opacity-70"
                                                                style="color: #6D6F76;"
                                                            >
                                                                {{ $roomNum }}
                                                            </button>
                                                            <button
                                                                type="button"
                                                                wire:click="deleteRoomNumber({{ $branchIndex }}, {{ $floor }}, {{ $configuredRoom['roomIndex'] }}, {{ $numIndex }})"
                                                                class="text-gray-400 hover:text-red-500"
                                                            >
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    @endif
                                                @endforeach
                                                {{-- Add button --}}
                                                <button
                                                    type="button"
                                                    wire:click="addRoomNumber({{ $branchIndex }}, {{ $floor }}, {{ $configuredRoom['roomIndex'] }})"
                                                    class="inline-flex items-center justify-center w-8 h-8 border-2 border-dashed border-gray-300 rounded-lg text-gray-400 hover:border-gray-400 hover:text-gray-500"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @else
                                            {{-- Normal Mode: Show room numbers with edit button --}}
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-lg">🚪</span>
                                                    <span class="font-semibold" style="color: #6D6F76;">{{ implode(' · ', $configuredRoom['roomNumbers']) }}</span>
                                                </div>
                                                <button
                                                    type="button"
                                                    wire:click="toggleEditRoomNumbers({{ $branchIndex }}, {{ $floor }}, {{ $configuredRoom['roomIndex'] }})"
                                                    class="text-sm font-medium hover:opacity-80"
                                                    style="color: #51D0CE;"
                                                >
                                                    수정
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <p class="text-gray-400 text-sm">설정 중 : 호실 정보 설정을 완료해주세요.</p>
                            @endif
                        </div>
                    @endforeach

                    {{-- Extra Rooms (기타) Summary --}}
                    @if(isset($hasExtraRooms[$branchIndex]) && $hasExtraRooms[$branchIndex] && isset($extraRooms[$branchIndex]))
                        @php
                            $configuredExtraRooms = [];
                            $extraRoomNumberOffset = 1;
                            foreach ($extraRooms[$branchIndex] as $roomIdx => $room) {
                                $isConfigured = !empty($room['room_category']) && $room['monthly_rent'] > 0;

                                if ($isConfigured) {
                                    // Build category info string
                                    $categoryParts = [];
                                    if (!empty($room['room_category'])) $categoryParts[] = $room['room_category'];
                                    if (!empty($room['window_structure'])) $categoryParts[] = $room['window_structure'];
                                    if (!empty($room['gender'])) $categoryParts[] = $room['gender'];
                                    $categoryInfo = !empty($categoryParts) ? implode(' · ', $categoryParts) : '';

                                    // Build room name with price
                                    $roomName = $room['room_type'] ?: '미입력';
                                    if (!empty($room['window_structure'])) {
                                        $roomName .= ' ' . $room['window_structure'];
                                    }
                                    $roomName .= ' · ' . number_format($room['monthly_rent']) . '원 · ' . ($room['room_count'] ?? 1) . '실';

                                    $roomCount = (int) ($room['room_count'] ?? 1);
                                    $extraRoomNumbers = [];
                                    for ($i = 0; $i < $roomCount; $i++) {
                                        $extraRoomNumbers[] = '기타' . ($extraRoomNumberOffset + $i);
                                    }
                                    $extraRoomNumberOffset += $roomCount;

                                    $configuredExtraRooms[] = [
                                        'roomIndex' => $roomIdx,
                                        'categoryInfo' => $categoryInfo,
                                        'roomName' => $roomName,
                                        'roomNumbers' => $extraRoomNumbers,
                                    ];
                                }
                            }
                        @endphp

                        @if(count($configuredExtraRooms) > 0)
                            <div class="border-t border-gray-200 pt-4 mt-4">
                                <h4 class="text-lg font-bold text-gray-900 mb-3">기타</h4>

                                @foreach($configuredExtraRooms as $configuredExtraRoom)
                                    <div class="mb-5 last:mb-0">
                                        @if(!empty($configuredExtraRoom['categoryInfo']))
                                            <p class="text-base text-gray-400 mb-1">{{ $configuredExtraRoom['categoryInfo'] }}</p>
                                        @endif
                                        <p class="text-base font-normal mb-2" style="color: #3E4046;">{{ $configuredExtraRoom['roomName'] }}</p>
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg">🚪</span>
                                            <span class="font-semibold" style="color: #6D6F76;">{{ implode(' · ', $configuredExtraRoom['roomNumbers']) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Final Confirmation Section -->
        <div class="-mx-4 md:mx-0 mt-8 bg-white">
            <!-- Header -->
            <div class="px-6 pt-6 pb-4">
                <div class="flex items-start gap-2 mb-2">
                    <svg class="w-6 h-6 text-teal-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">아래 내용대로 초기 설정을 완료하시겠어요?</h3>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 text-gray-500 text-sm ml-8">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 16v-4"></path>
                        <path d="M12 8h.01"></path>
                    </svg>
                    <span>설정은 나중에 언제든지 변경할 수 있어요.</span>
                </div>
            </div>

            <!-- Branch Summary List -->
            <div class="mb-6 border border-gray-200 rounded-2xl overflow-hidden">
                @foreach($branches as $branchIndex => $branch)
                    @php
                        $floors = $branch['floors'] ?? range($branch['start_floor'], $branch['end_floor']);
                        $totalRooms = 0;
                        $floorSummaries = [];

                        // Calculate floor summaries and total rooms
                        foreach ($floors as $floor) {
                            $floorRoomCount = 0;
                            $floorDetails = [];

                            if (isset($floorRooms[$branchIndex][$floor])) {
                                foreach ($floorRooms[$branchIndex][$floor] as $roomIdx => $room) {
                                    // Only count configured rooms (has room_category AND monthly_rent > 0)
                                    $isConfigured = !empty($room['room_category']) && $room['monthly_rent'] > 0;

                                    if ($isConfigured) {
                                        $roomCount = (int) ($room['room_count'] ?? 1);
                                        $floorRoomCount += $roomCount;

                                        $roomType = $room['room_type'] ?: '미입력';
                                        $rent = number_format($room['monthly_rent']);

                                        $floorDetails[] = "{$roomType} - {$rent}원 - {$roomCount}개";
                                    }
                                }
                            }

                            if ($floorRoomCount > 0) {
                                $floorSummaries[] = [
                                    'floor' => $floor,
                                    'count' => $floorRoomCount,
                                    'details' => $floorDetails
                                ];
                                $totalRooms += $floorRoomCount;
                            }
                        }

                        // Add extra rooms to total
                        if (isset($hasExtraRooms[$branchIndex]) && $hasExtraRooms[$branchIndex] && isset($extraRooms[$branchIndex])) {
                            foreach ($extraRooms[$branchIndex] as $room) {
                                if (!empty($room['room_type']) && $room['monthly_rent'] > 0) {
                                    $totalRooms += (int) ($room['room_count'] ?? 1);
                                }
                            }
                        }
                    @endphp

                    <div class="bg-white {{ !$loop->first ? 'border-t border-gray-200' : '' }}">
                        <!-- Branch Name Header -->
                        <div class="font-bold text-xl text-gray-900 pt-6 px-6 pb-4" style="background-color: #F9FBFC;">{{ $branch['name'] }}</div>

                        <!-- Branch Content -->
                        <div class="p-6">
                            <!-- Branch Name Item -->
                            <div class="flex items-start gap-2 mb-3">
                                <svg class="w-5 h-5 text-teal-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <div class="font-semibold text-gray-900">지점명</div>
                                    <div class="text-gray-600">{{ $branch['name'] }}</div>
                                </div>
                            </div>

                            <!-- Floor Count -->
                            <div class="flex items-start gap-2 mb-3">
                                <svg class="w-5 h-5 text-teal-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <div class="font-semibold text-gray-900">층수 설정</div>
                                    <div class="text-gray-600">
                                        총 {{ count($floors) }}개 층({{ implode(', ', array_map(fn($f) => $f.'층', $floors)) }})
                                    </div>
                                </div>
                            </div>

                            <!-- Floor Room Configuration -->
                            <div class="flex items-start gap-2 mb-3">
                                <svg class="w-5 h-5 text-teal-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900 mb-2">층별 호실 구성</div>
                                    @foreach($floorSummaries as $floorSummary)
                                        <div class="mb-2">
                                            <div class="text-gray-900 font-medium">{{ $floorSummary['floor'] }}층: {{ $floorSummary['count'] }}개</div>
                                            <ul class="ml-4 text-gray-600">
                                                @foreach($floorSummary['details'] as $detail)
                                                    <li class="list-disc ml-4">{{ $detail }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach

                                    @if(isset($hasExtraRooms[$branchIndex]) && $hasExtraRooms[$branchIndex] && isset($extraRooms[$branchIndex]))
                                        @php
                                            $extraRoomDetails = [];
                                            foreach ($extraRooms[$branchIndex] as $room) {
                                                if (!empty($room['room_type']) && $room['monthly_rent'] > 0) {
                                                    $roomType = $room['room_type'];
                                                    $rent = number_format($room['monthly_rent']);
                                                    $count = (int) ($room['room_count'] ?? 1);
                                                    $extraRoomDetails[] = "{$roomType} - {$rent}원 - {$count}개";
                                                }
                                            }
                                        @endphp
                                        @if(count($extraRoomDetails) > 0)
                                            <div class="mb-2">
                                                <div class="text-gray-900 font-medium">기타: {{ count($extraRoomDetails) }}개</div>
                                                <ul class="ml-4 text-gray-600">
                                                    @foreach($extraRoomDetails as $detail)
                                                        <li class="list-disc ml-4">{{ $detail }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Total Rooms -->
                            <div class="pt-3 text-gray-700">
                                <span class="font-semibold">총 {{ $totalRooms }}개 호실</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Desktop Action Buttons -->
        <div class="hidden md:flex gap-4 mt-8">
            <button
                type="button"
                wire:click="previousStep"
                class="flex-1 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-semibold py-3 rounded-lg transition duration-150 ease-in-out"
            >
                이전
            </button>
            <button
                type="button"
                wire:click="completeOnboarding"
                wire:loading.attr="disabled"
                class="flex-1 bg-teal-400 hover:bg-teal-500 text-white font-semibold py-3 rounded-lg transition duration-150 ease-in-out disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="completeOnboarding">설정 완료</span>
                <span wire:loading wire:target="completeOnboarding">저장 중...</span>
            </button>
        </div>
    </div>

    <!-- Mobile Bottom Button -->
    <div class="md:hidden p-4 bg-white border-t border-gray-100 safe-area-bottom mt-auto flex-shrink-0">
        <button
            type="button"
            wire:click="completeOnboarding"
            wire:loading.attr="disabled"
            class="w-full bg-teal-400 hover:bg-teal-500 text-white font-semibold py-3 rounded-lg transition duration-150 ease-in-out disabled:opacity-50"
        >
            <span wire:loading.remove wire:target="completeOnboarding">설정 완료</span>
            <span wire:loading wire:target="completeOnboarding">저장 중...</span>
        </button>
    </div>
</div>
