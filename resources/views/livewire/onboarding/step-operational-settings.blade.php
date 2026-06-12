<div class="w-full bg-white h-screen md:h-auto md:max-h-full md:min-h-0 md:rounded-2xl md:shadow-lg flex flex-col">
    <!-- Mobile Header (Back Button) -->
    <div class="md:hidden p-4 border-b border-gray-100 flex items-center flex-shrink-0">
        <button type="button" class="text-gray-800 hover:bg-gray-100 rounded-full p-1" wire:click="previousStep">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
    </div>

    <!-- Content -->
    <div class="flex-grow p-6 md:p-8 overflow-y-auto md:min-h-0">
        <!-- Title -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-2">운영 방식에 맞는 설정을 도와드릴게요.</h2>
        </div>

        <!-- 1. Room Types (Multiple Selection) -->
        <div class="mb-8">
            <label class="block text-base font-bold text-gray-900 mb-1">1. 운영 중인 호실 유형을 선택해주세요.</label>
            <p class="text-xs text-red-500 mb-4">*고시원 형태에 따라 복수 선택이 가능해요.</p>
            
            <div class="space-y-3">
                @foreach(['원룸', '샤워룸', '미니룸'] as $type)
                    <div 
                        wire:click="toggleRoomType('{{ $type }}')"
                        class="flex items-center p-4 border rounded-xl cursor-pointer transition-all duration-200 {{ in_array($type, $roomTypes) ? 'border-teal-400 bg-teal-50' : 'border-gray-200 hover:border-teal-200' }}"
                    >
                        <div class="flex-grow text-sm {{ in_array($type, $roomTypes) ? 'text-teal-600 font-bold' : 'text-gray-700' }}">
                            {{ $type }}
                        </div>
                        <div class="flex-shrink-0">
                            <div class="w-5 h-5 rounded border flex items-center justify-center {{ in_array($type, $roomTypes) ? 'border-teal-400 bg-teal-400' : 'border-gray-300' }}">
                                @if(in_array($type, $roomTypes))
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @error('roomTypes') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- 2. Window Structure (Single Selection) -->
        <div class="mb-8">
            <label class="block text-base font-bold text-gray-900 mb-4">2. 호실의 창 구조를 선택해주세요.</label>
            
            <div class="space-y-3">
                @foreach([
                    'inner' => '내창만 있음',
                    'both' => '내창, 외창 모두 있음',
                    'outer' => '외창만 있음'
                ] as $key => $label)
                    <div 
                        wire:click="selectWindowStructure('{{ $key }}')"
                        class="flex items-center p-4 border rounded-xl cursor-pointer transition-all duration-200 {{ $windowStructure === $key ? 'border-teal-400 bg-teal-50' : 'border-gray-200 hover:border-teal-200' }}"
                    >
                        <div class="flex-grow text-sm {{ $windowStructure === $key ? 'text-teal-600 font-bold' : 'text-gray-700' }}">
                            {{ $label }}
                        </div>
                        <div class="flex-shrink-0">
                            <div class="w-5 h-5 rounded-full border flex items-center justify-center {{ $windowStructure === $key ? 'border-teal-400' : 'border-gray-300' }}">
                                @if($windowStructure === $key)
                                    <div class="w-2.5 h-2.5 rounded-full bg-teal-400"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @error('windowStructure') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- 3. Gender Division (Single Selection) -->
        <div class="mb-10">
            <label class="block text-base font-bold text-gray-900 mb-4">3. 입실자 배정 시 남녀 구분 여부를 선택해주세요.</label>
            
            <div class="space-y-3">
                @foreach([
                    'separated' => '남녀 구분 있음',
                    'mixed' => '남녀 구분 없음'
                ] as $key => $label)
                    <div 
                        wire:click="selectGenderDivision('{{ $key }}')"
                        class="flex items-center p-4 border rounded-xl cursor-pointer transition-all duration-200 {{ $genderDivision === $key ? 'border-teal-400 bg-teal-50' : 'border-gray-200 hover:border-teal-200' }}"
                    >
                        <div class="flex-grow text-sm {{ $genderDivision === $key ? 'text-teal-600 font-bold' : 'text-gray-700' }}">
                            {{ $label }}
                        </div>
                        <div class="flex-shrink-0">
                            <div class="w-5 h-5 rounded-full border flex items-center justify-center {{ $genderDivision === $key ? 'border-teal-400' : 'border-gray-300' }}">
                                @if($genderDivision === $key)
                                    <div class="w-2.5 h-2.5 rounded-full bg-teal-400"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @error('genderDivision') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Desktop Action Buttons -->
        <div class="hidden md:flex gap-4">
            <button
                type="button"
                wire:click="previousStep"
                class="flex-1 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-semibold py-3 rounded-lg transition duration-150 ease-in-out"
            >
                이전
            </button>
            <button
                type="button"
                wire:click="nextStep"
                class="flex-1 bg-teal-400 hover:bg-teal-500 text-white font-semibold py-3 rounded-lg transition duration-150 ease-in-out"
            >
                다음
            </button>
        </div>
    </div>

    <!-- Mobile Bottom Button -->
    <div class="md:hidden p-4 bg-white border-t border-gray-100 safe-area-bottom mt-auto flex-shrink-0">
        <button
            type="button"
            wire:click="nextStep"
            class="w-full bg-teal-400 hover:bg-teal-500 text-white font-semibold py-3 rounded-lg transition duration-150 ease-in-out"
        >
            다음
        </button>
    </div>
</div>
