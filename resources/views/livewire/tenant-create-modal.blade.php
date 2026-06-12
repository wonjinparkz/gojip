<div>
    @if($show)
    <div style="position: fixed; inset: 0; z-index: 9999; overflow-y: auto;">
        <style>
            /* 모바일 반응형 */
            @media (max-width: 640px) {
                .tenant-create-modal {
                    padding: 16px !important;
                    border-radius: 12px !important;
                    max-height: 95vh;
                    overflow-y: auto;
                }
            }
        </style>

        <!-- Backdrop -->
        <div wire:click="close" style="position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5);"></div>

        <!-- Modal Content -->
        <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 16px;">
            <div class="tenant-create-modal" style="background-color: white; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); width: 100%; max-width: 448px; padding: 24px; position: relative; z-index: 10000;">

                <!-- Modal Header -->
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                    <h3 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0;">입주자 생성</h3>
                    <button wire:click="close" type="button" style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px;">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form wire:submit="save">
                    <!-- Tenant Selection -->
                    <div style="margin-bottom: 16px; position: relative;">
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">입주자 선택 *</label>

                        <!-- Search Input -->
                        <div style="position: relative;">
                            <input type="text"
                                   wire:model.live="searchQuery"
                                   wire:click="toggleDropdown"
                                   style="width: 100%; padding: 12px 40px 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; cursor: pointer;"
                                   placeholder="입주자를 검색하세요..."
                                   onfocus="this.style.outline='2px solid #2dd4bf'; this.style.borderColor='transparent';"
                                   onblur="setTimeout(() => { this.style.outline='none'; this.style.borderColor='#d1d5db'; }, 200);">

                            <!-- Dropdown Arrow -->
                            <div style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                                <svg style="width: 20px; height: 20px; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Dropdown List -->
                        @if($showDropdown)
                            <div style="position: absolute; top: 100%; left: 0; right: 0; margin-top: 4px; background-color: white; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); max-height: 250px; overflow-y: auto; z-index: 10001;">
                                @if(count($filteredTenants) > 0)
                                    @foreach($filteredTenants as $tenant)
                                        <div wire:click="selectTenant({{ $tenant['id'] }})"
                                             style="padding: 12px 16px; cursor: pointer; border-bottom: 1px solid #f3f4f6; background-color: {{ $selectedTenantId == $tenant['id'] ? '#f0fdfa' : 'white' }};"
                                             onmouseover="this.style.backgroundColor='{{ $selectedTenantId == $tenant['id'] ? '#f0fdfa' : '#f9fafb' }}';"
                                             onmouseout="this.style.backgroundColor='{{ $selectedTenantId == $tenant['id'] ? '#f0fdfa' : 'white' }}';">
                                            <div style="font-weight: 500; color: #111827; font-size: 14px;">
                                                {{ $tenant['name'] }}
                                                @if($selectedTenantId == $tenant['id'])
                                                    <span style="color: #2dd4bf; margin-left: 8px;">✓</span>
                                                @endif
                                            </div>
                                            <div style="font-size: 12px; color: #6b7280; margin-top: 2px;">
                                                {{ $tenant['phone'] ?: '연락처 없음' }}
                                                @if($tenant['current_room'])
                                                    @if($tenant['is_future_resident'])
                                                        <span style="margin-left: 8px; color: #f59e0b;">• 현재 {{ $tenant['current_room'] }}호 입주 대기중</span>
                                                    @else
                                                        <span style="margin-left: 8px; color: #ef4444;">• 현재 {{ $tenant['current_room'] }}호 입주중</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div style="padding: 16px; text-align: center; color: #6b7280; font-size: 14px;">
                                        @if(count($allTenants) == 0)
                                            등록된 입주자가 없습니다.<br>
                                            <span style="font-size: 12px;">입주자 관리에서 먼저 입주자를 등록해주세요.</span>
                                        @else
                                            검색 결과가 없습니다.
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Payment Status -->
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">결제 상태 *</label>
                        <select wire:model="paymentStatus"
                                style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background-color: white;"
                                onfocus="this.style.outline='2px solid #2dd4bf'; this.style.borderColor='transparent';"
                                onblur="this.style.outline='none'; this.style.borderColor='#d1d5db';">
                            <option value="paid">납부완료</option>
                            <option value="pending">미납</option>
                            <option value="overdue">연체</option>
                            <option value="waiting">대기</option>
                        </select>
                        @error('paymentStatus')
                            <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Dates Info -->
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">
                            입주일 *
                            @if($minMoveInDate)
                                <span style="font-size: 12px; color: #9ca3af; font-weight: 400; margin-left: 8px;">({{ $minMoveInDate }} 이후부터 선택 가능)</span>
                            @endif
                        </label>
                        <input type="date"
                               wire:model="moveInDate"
                               {{ $datesReadOnly ? 'readonly' : '' }}
                               @if($minMoveInDate) min="{{ $minMoveInDate }}" @endif
                               style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; {{ $datesReadOnly ? 'background-color: #f9fafb; cursor: not-allowed;' : '' }}"
                               onfocus="{{ $datesReadOnly ? '' : 'this.style.outline=\'2px solid #2dd4bf\'; this.style.borderColor=\'transparent\';' }}"
                               onblur="{{ $datesReadOnly ? '' : 'this.style.outline=\'none\'; this.style.borderColor=\'#d1d5db\';' }}">
                        @error('moveInDate')
                            <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Move Out Date -->
                    <div style="margin-bottom: 24px;" x-data="indefiniteMoveOut('indefiniteMoveOut', 'moveOutDate')">
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">
                            <span>퇴실일 *</span>
                            @if(!$datesReadOnly)
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: 400; cursor: pointer;">
                                <input type="checkbox"
                                       wire:model.live="indefiniteMoveOut"
                                       x-model="indefinite"
                                       style="width: 16px; height: 16px; cursor: pointer; accent-color: #2dd4bf;">
                                <span style="font-size: 13px; color: #6b7280;">퇴실일 미정</span>
                            </label>
                            @endif
                        </label>
                        <input type="date"
                               x-show="!indefinite"
                               wire:model.blur="moveOutDate"
                               {{ $datesReadOnly ? 'readonly' : '' }}
                               @if($moveInDate) min="{{ $moveInDate }}" @endif
                               x-bind:disabled="{{ $datesReadOnly ? 'true' : 'false' }}"
                               x-bind:style="getDateInputStyle({{ $datesReadOnly ? 'true' : 'false' }})"
                               onfocus="if(!this.disabled) { this.style.outline='2px solid #2dd4bf'; this.style.borderColor='transparent'; }"
                               onblur="if(!this.disabled) { this.style.outline='none'; this.style.borderColor='#d1d5db'; }">
                        <input type="text"
                               x-show="indefinite"
                               x-cloak
                               value="----.--. --."
                               disabled
                               style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background-color: #f3f4f6; cursor: not-allowed; color: #6b7280; letter-spacing: 1px;">
                        @error('moveOutDate')
                            <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Short Term Accommodation -->
                    <div style="margin-bottom: 24px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox"
                                   wire:model.live="isShortTerm"
                                   style="width: 16px; height: 16px; cursor: pointer; accent-color: #2dd4bf;">
                            <span style="font-size: 14px; font-weight: 500; color: #374151;">단기 숙박</span>
                            <span style="font-size: 12px; color: #9ca3af; font-weight: 400;">월세 거주자가 아닌 경우 선택해주세요</span>
                        </label>
                    </div>

                    @if($isShortTerm)
                        <!-- Short Term Monthly Rent -->
                        <div style="margin-bottom: 16px;" x-data="currencyInput('shortTermMonthlyRent')">
                            <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">단기 숙박 월세 *</label>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 14px;">₩</span>
                                <input type="text"
                                       x-model="displayValue"
                                       @input="handleInput($event)"
                                       style="width: 100%; padding: 12px 16px 12px 28px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;"
                                       placeholder="예: 700,000"
                                       onfocus="this.style.outline='2px solid #2dd4bf'; this.style.borderColor='transparent';"
                                       onblur="this.style.outline='none'; this.style.borderColor='#d1d5db';">
                            </div>
                            @error('shortTermMonthlyRent')
                                <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Short Term Deposit -->
                        <div style="margin-bottom: 24px;" x-data="currencyInput('shortTermDeposit')">
                            <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">단기 숙박 보증금 (선택)</label>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 14px;">₩</span>
                                <input type="text"
                                       x-model="displayValue"
                                       @input="handleInput($event)"
                                       style="width: 100%; padding: 12px 16px 12px 28px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;"
                                       placeholder="예: 500,000"
                                       onfocus="this.style.outline='2px solid #2dd4bf'; this.style.borderColor='transparent';"
                                       onblur="this.style.outline='none'; this.style.borderColor='#d1d5db';">
                            </div>
                            @error('shortTermDeposit')
                                <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    <!-- Buttons -->
                    <div style="display: flex; gap: 12px;">
                        <button type="button"
                                wire:click="close"
                                style="flex: 1; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; color: #374151; font-weight: 500; background-color: white; cursor: pointer; transition: background-color 0.15s;"
                                onmouseover="this.style.backgroundColor='#f9fafb';"
                                onmouseout="this.style.backgroundColor='white';">
                            취소
                        </button>
                        <button type="submit"
                                style="flex: 1; padding: 12px 16px; background-color: #2dd4bf; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background-color 0.15s;"
                                onmouseover="this.style.backgroundColor='#14b8a6';"
                                onmouseout="this.style.backgroundColor='#2dd4bf';">
                            생성
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
document.addEventListener('livewire:init', () => {
    if (typeof Alpine !== 'undefined' && !Alpine.__componentsRegisteredCreateModal) {
        Alpine.__componentsRegisteredCreateModal = true;

        // 퇴실일 미정 컴포넌트
        Alpine.data('indefiniteMoveOut', (wireProperty = 'indefiniteMoveOut', moveOutDateProperty = 'moveOutDate') => ({
            indefinite: false,
            init() {
                // Livewire 값으로 초기화
                this.$nextTick(() => {
                    if (this.$wire && wireProperty) {
                        const initialValue = this.$wire.get(wireProperty);
                        this.indefinite = initialValue || false;
                    }
                });

                // indefinite 값 변경 감시
                this.$watch('indefinite', value => {
                    if (value) {
                        this.clearMoveOutDate();
                    }
                });

                // Livewire 값 변경 감시
                if (this.$wire) {
                    this.$wire.$watch(wireProperty, value => {
                        this.indefinite = value || false;
                    });
                }
            },
            clearMoveOutDate() {
                if (this.$wire && moveOutDateProperty) {
                    this.$wire.set(moveOutDateProperty, null);
                }
            },
            getDateInputStyle(isReadOnly = false) {
                if (isReadOnly) {
                    return 'width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background-color: #f3f4f6; cursor: not-allowed;';
                }
                return 'width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;';
            }
        }));

        // 금액 포맷팅 컴포넌트
        Alpine.data('currencyInput', (wireProperty) => ({
            displayValue: '',
            init() {
                this.$nextTick(() => {
                    if (this.$wire && wireProperty) {
                        const initialValue = this.$wire.get(wireProperty);
                        if (initialValue) {
                            this.displayValue = this.formatNumber(initialValue);
                        }
                    }
                });
            },
            formatNumber(value) {
                if (!value && value !== 0) return '';
                const num = String(value).replace(/[^\d]/g, '');
                return num.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            },
            handleInput(event) {
                const input = event.target;
                const rawValue = input.value.replace(/[^\d]/g, '');
                this.displayValue = this.formatNumber(rawValue);
                input.value = this.displayValue;
                if (this.$wire && wireProperty) {
                    this.$wire.set(wireProperty, rawValue ? parseInt(rawValue) : null);
                }
            }
        }));
    }
});
</script>
