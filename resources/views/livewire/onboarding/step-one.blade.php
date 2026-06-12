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
            <div class="flex items-center mb-2">
                <svg class="w-6 h-6 text-gray-900 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h2 class="text-xl font-bold text-gray-900">지점 정보 설정</h2>
            </div>
            <p class="text-sm text-gray-600">운영 중인 고시원의 기본 정보를 입력해주세요.</p>
        </div>

        <!-- Branches Repeater -->
        <div class="space-y-6 mb-8">
            @foreach($branches as $index => $branch)
                <div class="border border-gray-200 rounded-xl overflow-hidden bg-gray-50/50">
                    <!-- Card Header -->
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-100/50 border-b border-gray-200">
                        <h3 class="text-sm font-bold text-gray-900">지점 {{ $index + 1 }}</h3>
                        @if($index > 0)
                            <button
                                type="button"
                                wire:click="removeBranch({{ $index }})"
                                class="text-red-500 hover:text-red-700 text-xs font-medium"
                            >
                                삭제
                            </button>
                        @endif
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 space-y-4">
                        <!-- Branch Name -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">지점명</label>
                            <input
                                type="text"
                                wire:model="branches.{{ $index }}.name"
                                placeholder="예: ㅇㅇ스테이 ㅇㅇ점, ㅇㅇㅇ하우스"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent text-sm transition duration-150"
                            />
                            @error('branches.' . $index . '.name')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Floors Selection -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">고시원이 있는 층을 모두 선택하세요.</label>
                            
                            <!-- Floor Selector Grid -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach(range(1, 5) as $f)
                                    <button 
                                        type="button"
                                        wire:click="toggleFloor({{ $index }}, {{ $f }})"
                                        class="w-12 h-12 flex items-center justify-center rounded-lg border text-sm font-bold transition-all duration-150 {{ in_array($f, $branches[$index]['selected_floors']) ? 'border-teal-400 bg-teal-50 text-teal-600' : 'border-gray-200 bg-white text-gray-400 hover:border-gray-300' }}"
                                    >
                                        {{ $f }}
                                    </button>
                                @endforeach
                            </div>

                            <!-- Custom Floors Toggle -->
                            <div class="space-y-3">
                                <label class="flex items-center text-xs text-gray-600 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        wire:model.live="branches.{{ $index }}.show_custom_input"
                                        class="rounded border-gray-300 text-teal-400 focus:ring-teal-400 mr-2"
                                    >
                                    목록에 없는 층은 직접 입력해주세요.(선택)
                                </label>

                                @if($branches[$index]['show_custom_input'])
                                    <div class="animate-fadeIn">
                                        <input
                                            type="text"
                                            wire:model="branches.{{ $index }}.custom_floors"
                                            placeholder="예: 6, 7"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent text-sm transition duration-150 shadow-sm"
                                        />
                                        <p class="text-[10px] text-gray-400 mt-1">층 번호를 쉼표(,)로 구분해서 입력해주세요.</p>
                                    </div>
                                @endif
                            </div>

                            @error('branches.' . $index . '.selected_floors')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Add Branch Button -->
        <button
            type="button"
            wire:click="addBranch"
            class="w-full py-4 border-2 border-dashed border-gray-200 rounded-xl text-gray-500 text-sm font-medium hover:border-teal-200 hover:text-teal-500 transition duration-150 mb-8"
        >
            + 지점 추가
        </button>

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
