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
            <!-- Title Header -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-3">현재 운영 중인 지점 규모를 알려주세요.</h2>
                <div class="space-y-1">
                    <p class="flex items-center text-sm text-gray-600">
                        <span class="mr-2 text-yellow-400">✨</span>
                        선택에 따라 관리 기능이 자동으로 설정돼요.
                    </p>
                    <p class="flex items-center text-sm text-gray-600">
                        <span class="mr-2 text-gray-400">ⓘ</span>
                        설정은 나중에 언제든지 변경할 수 있어요.
                    </p>
                </div>
            </div>

            <!-- Selection Cards -->
            <div class="space-y-4 mb-8">
                <!-- Option 1: Single Branch -->
                <div 
                    wire:click="selectType('single')"
                    class="relative flex items-center p-4 border rounded-xl cursor-pointer transition-all duration-200 {{ $selectedType === 'single' ? 'border-teal-400 bg-teal-50' : 'border-gray-200 hover:border-teal-200' }}"
                >
                    <div class="flex-shrink-0 w-12 h-12 {{ $selectedType === 'single' ? 'bg-teal-100' : 'bg-gray-100' }} rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 {{ $selectedType === 'single' ? 'text-teal-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-base font-bold text-gray-900">1개의 지점을 운영 중이에요</h3>
                        <p class="text-sm text-gray-500">단일 지점 관리에 최적화</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-6 h-6 rounded-full border flex items-center justify-center {{ $selectedType === 'single' ? 'border-teal-400' : 'border-gray-300' }}">
                            @if($selectedType === 'single')
                                <div class="w-3 h-3 rounded-full bg-teal-400"></div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Option 2: Multiple Branches -->
                <div 
                    wire:click="selectType('multiple')"
                    class="relative flex items-center p-4 border rounded-xl cursor-pointer transition-all duration-200 {{ $selectedType === 'multiple' ? 'border-teal-400 bg-teal-50' : 'border-gray-200 hover:border-teal-200' }}"
                >
                    <div class="flex-shrink-0 w-12 h-12 {{ $selectedType === 'multiple' ? 'bg-teal-100' : 'bg-gray-100' }} rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 {{ $selectedType === 'multiple' ? 'text-teal-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-base font-bold text-gray-900">1개 이상의 지점을 운영 중이에요</h3>
                        <p class="text-sm text-gray-500">다중 지점 관리에 최적화</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-6 h-6 rounded-full border flex items-center justify-center {{ $selectedType === 'multiple' ? 'border-teal-400' : 'border-gray-300' }}">
                            @if($selectedType === 'multiple')
                                <div class="w-3 h-3 rounded-full bg-teal-400"></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Desktop Button Position -->
            <div class="hidden md:block">
                <button
                    type="button"
                    wire:click="nextStep"
                    @if(!$selectedType) disabled @endif
                    class="w-full bg-teal-400 hover:bg-teal-500 text-white font-semibold py-3 rounded-lg transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
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
                @if(!$selectedType) disabled @endif
                class="w-full bg-teal-400 hover:bg-teal-500 text-white font-semibold py-3 rounded-lg transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
            >
                다음
            </button>
        </div>
    </div>
