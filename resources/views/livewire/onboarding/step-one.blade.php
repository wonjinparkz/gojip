<div>
    <!-- Main Card -->
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <!-- Title -->
        <div class="mb-6">
            <div class="flex items-center mb-2">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h2 class="text-xl font-bold text-gray-900">지점 정보 설정</h2>
            </div>
            <p class="text-sm text-gray-600">운영 중인 고시원의 기본 정보를 입력해주세요.</p>
        </div>

        <!-- Branches Repeater -->
        @foreach($branches as $index => $branch)
            <div class="mb-6 border border-gray-300 rounded-lg overflow-hidden">
                <!-- Card Header -->
                <div class="flex items-center justify-between mb-4 bg-[#F9FBFC] px-4 py-3">
                    <h3 class="text-base font-semibold text-gray-900">지점 {{ $index + 1 }}</h3>
                    @if($index > 0)
                        <button
                            type="button"
                            wire:click="removeBranch({{ $index }})"
                            class="text-red-500 hover:text-red-700 text-sm"
                        >
                            삭제
                        </button>
                    @endif
                </div>

                <!-- Card Body -->
                <div class="px-4 pb-4">
                    <!-- Branch Name -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">지점명</label>
                        <input
                            type="text"
                            wire:model="branches.{{ $index }}.name"
                            placeholder="예: 강남점, 홍대점"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent"
                        />
                        @error('branches.' . $index . '.name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Start Floor and End Floor -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">시작 층수</label>
                            <input
                                type="number"
                                wire:model="branches.{{ $index }}.start_floor"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent"
                            />
                            @error('branches.' . $index . '.start_floor')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">끝 층수</label>
                            <input
                                type="number"
                                wire:model="branches.{{ $index }}.end_floor"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent"
                            />
                            @error('branches.' . $index . '.end_floor')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Add Branch Button -->
        <button
            type="button"
            wire:click="addBranch"
            class="w-full py-3 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-teal-400 hover:text-teal-400 transition duration-150 mb-6"
        >
            <span class="text-lg mr-2">+</span>
            지점 추가
        </button>

        <!-- Next Button -->
        <button
            type="button"
            wire:click="nextStep"
            class="w-full bg-teal-400 hover:bg-teal-500 text-white font-semibold py-3 rounded-lg transition duration-150 ease-in-out"
        >
            다음 단계
        </button>
    </div>
</div>
