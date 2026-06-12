<div>
    @if($show)
    <div style="position: fixed; inset: 0; z-index: 9999; overflow-y: auto;">
        <style>
            /* 모바일 반응형 */
            @media (max-width: 640px) {
                .tenant-mgmt-modal {
                    padding: 20px !important;
                    border-radius: 12px !important;
                }
                .tenant-form-grid-3 {
                    grid-template-columns: 1fr !important;
                }
                .tenant-form-grid-2 {
                    grid-template-columns: 1fr !important;
                }
            }
        </style>

        <!-- Backdrop -->
        <div wire:click="close" style="position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5);"></div>

        <!-- Modal Content -->
        <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 16px;">
            <div class="tenant-mgmt-modal" style="background-color: white; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); width: 100%; max-width: 600px; padding: 32px; position: relative; z-index: 10000;">

                <!-- Modal Header -->
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                    <h3 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0;">
                        {{ $editingTenantId ? '입주자 정보 수정' : '입주자 생성' }}
                    </h3>
                    <button wire:click="close" type="button" style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px;">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form wire:submit="save">
                    <div style="max-height: 60vh; overflow-y: auto; padding-right: 8px;">
                        <!-- 기본 정보 -->
                        <div style="margin-bottom: 24px;">
                            <h4 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 16px 0; padding-bottom: 8px; border-bottom: 2px solid #e5e7eb;">기본 정보</h4>

                            <div class="tenant-form-grid-3" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                                <!-- 이름 -->
                                <div>
                                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">이름 *</label>
                                    <input type="text" wire:model="name" style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="이름">
                                    @error('name')
                                        <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- 연락처 -->
                                <div>
                                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">연락처 *</label>
                                    <input type="text" wire:model.live="phone" style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="010-1234-5678">
                                    @error('phone')
                                        <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- 성별 -->
                                <div>
                                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">성별</label>
                                    <select wire:model="gender" style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background-color: white;">
                                        <option value="">선택</option>
                                        <option value="male">남성</option>
                                        <option value="female">여성</option>
                                    </select>
                                    @error('gender')
                                        <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- 입주 정보 -->
                        <div style="margin-bottom: 24px;">
                            <h4 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 16px 0; padding-bottom: 8px; border-bottom: 2px solid #e5e7eb;">입주 정보</h4>

                            <div class="tenant-form-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                <!-- 입주일 -->
                                <div>
                                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">입주일</label>
                                    <input type="date" wire:model="move_in_date" style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                                    @error('move_in_date')
                                        <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- 퇴실일 -->
                                <div x-data="indefiniteMoveOut('indefinite_move_out', 'move_out_date')">
                                    <label style="display: flex; align-items: center; justify-content: space-between; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">
                                        <span>퇴실일</span>
                                        <label style="display: flex; align-items: center; gap: 8px; font-weight: 400; cursor: pointer;">
                                            <input type="checkbox"
                                                   wire:model.live="indefinite_move_out"
                                                   x-model="indefinite"
                                                   style="width: 16px; height: 16px; cursor: pointer; accent-color: #2dd4bf;">
                                            <span style="font-size: 13px; color: #6b7280;">퇴실일 미정</span>
                                        </label>
                                    </label>
                                    <input type="date"
                                           x-show="!indefinite"
                                           wire:model.blur="move_out_date"
                                           x-bind:style="getDateInputStyle()"
                                           onfocus="this.style.outline='2px solid #2dd4bf'; this.style.borderColor='transparent';"
                                           onblur="this.style.outline='none'; this.style.borderColor='#d1d5db';">
                                    <input type="text"
                                           x-show="indefinite"
                                           x-cloak
                                           value="----.--. --."
                                           disabled
                                           style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background-color: #f3f4f6; cursor: not-allowed; color: #6b7280; letter-spacing: 1px;">
                                    @error('move_out_date')
                                        <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- 숙박 정보 -->
                        <div style="margin-bottom: 24px;">
                            <h4 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 16px 0; padding-bottom: 8px; border-bottom: 2px solid #e5e7eb;">숙박 정보</h4>

                            <!-- 월 입실료 / 보증금 (단기 체크 시 비활성화) -->
                            <div class="tenant-form-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                                <div x-data="currencyInput('monthly_rent')">
                                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">월 입실료</label>
                                    <div style="position: relative;">
                                        <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 14px;">₩</span>
                                        <input type="text"
                                               x-model="displayValue"
                                               @input="handleInput($event)"
                                               x-bind:disabled="$wire.is_short_term"
                                               x-bind:style="$wire.is_short_term
                                                    ? 'width: 100%; padding: 12px 16px 12px 28px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background-color: #f3f4f6; cursor: not-allowed; color: #9ca3af;'
                                                    : 'width: 100%; padding: 12px 16px 12px 28px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;'"
                                               placeholder="예: 500,000"
                                               onfocus="if(!this.disabled) { this.style.outline='2px solid #2dd4bf'; this.style.borderColor='transparent'; }"
                                               onblur="if(!this.disabled) { this.style.outline='none'; this.style.borderColor='#d1d5db'; }">
                                    </div>
                                    @error('monthly_rent')
                                        <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div x-data="currencyInput('deposit')">
                                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">보증금</label>
                                    <div style="position: relative;">
                                        <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 14px;">₩</span>
                                        <input type="text"
                                               x-model="displayValue"
                                               @input="handleInput($event)"
                                               x-bind:disabled="$wire.is_short_term"
                                               x-bind:style="$wire.is_short_term
                                                    ? 'width: 100%; padding: 12px 16px 12px 28px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background-color: #f3f4f6; cursor: not-allowed; color: #9ca3af;'
                                                    : 'width: 100%; padding: 12px 16px 12px 28px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;'"
                                               placeholder="예: 500,000"
                                               onfocus="if(!this.disabled) { this.style.outline='2px solid #2dd4bf'; this.style.borderColor='transparent'; }"
                                               onblur="if(!this.disabled) { this.style.outline='none'; this.style.borderColor='#d1d5db'; }">
                                    </div>
                                    @error('deposit')
                                        <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- 단기 숙박 체크박스 -->
                            <div style="margin-bottom: 16px;">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="checkbox" wire:model.live="is_short_term" style="width: 16px; height: 16px; cursor: pointer; accent-color: #2dd4bf;">
                                    <span style="font-size: 14px; font-weight: 500; color: #374151;">단기 숙박</span>
                                    <span style="font-size: 12px; color: #9ca3af; font-weight: 400;">월세 거주자가 아닌 경우 선택해주세요</span>
                                </label>
                            </div>

                            @if($is_short_term)
                            <div class="tenant-form-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                <!-- 단기 숙박 월 입실료 -->
                                <div x-data="currencyInput('short_term_monthly_rent')">
                                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">단기 숙박 월 입실료 *</label>
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
                                    @error('short_term_monthly_rent')
                                        <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- 단기 숙박 보증금 -->
                                <div x-data="currencyInput('short_term_deposit')">
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
                                    @error('short_term_deposit')
                                        <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- 결제 정보 -->
                        <div style="margin-bottom: 24px;">
                            <h4 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 16px 0; padding-bottom: 8px; border-bottom: 2px solid #e5e7eb;">결제 정보</h4>

                            <div class="tenant-form-grid-3" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                                <!-- 마지막 입금일 -->
                                <div>
                                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">마지막 입금일</label>
                                    <input type="date" wire:model="last_payment_date" style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                                    @error('last_payment_date')
                                        <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- 결제 방법 -->
                                <div>
                                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">결제 방법</label>
                                    <select wire:model="payment_method" style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background-color: white;">
                                        <option value="">선택</option>
                                        <option value="card">카드</option>
                                        <option value="transfer">계좌이체</option>
                                        <option value="cash">현금</option>
                                    </select>
                                    @error('payment_method')
                                        <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- 최근 납부 상태 -->
                                <div>
                                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">최근 납부 상태 *</label>
                                    <select wire:model="payment_status" style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background-color: white;">
                                        <option value="paid">납부완료</option>
                                        <option value="pending">미납</option>
                                        <option value="overdue">연체</option>
                                        <option value="waiting">대기</option>
                                    </select>
                                    @error('payment_status')
                                        <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- 블랙리스트 -->
                        <div style="margin-bottom: 16px;">
                            <h4 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 16px 0; padding-bottom: 8px; border-bottom: 2px solid #e5e7eb;">블랙리스트</h4>

                            <!-- 블랙리스트 여부 -->
                            <div style="margin-bottom: 16px;">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <span style="font-size: 14px; font-weight: 500; color: #374151;">블랙리스트 여부</span>

                                    <!-- Toggle Switch -->
                                    <label style="position: relative; display: inline-block; width: 52px; height: 28px; cursor: pointer;">
                                        <input type="checkbox" wire:model.live="is_blacklisted" style="opacity: 0; width: 0; height: 0;">
                                        <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ $is_blacklisted ? '#2dd4bf' : '#cbd5e1' }}; transition: 0.3s; border-radius: 28px; box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);"></span>
                                        <span style="position: absolute; content: ''; height: 22px; width: 22px; left: {{ $is_blacklisted ? '27px' : '3px' }}; bottom: 3px; background-color: white; transition: 0.3s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);"></span>
                                    </label>
                                </div>
                            </div>

                            <!-- 블랙리스트 메모 (블랙리스트 체크 시에만 표시) -->
                            @if($is_blacklisted)
                            <div>
                                <textarea wire:model="blacklist_memo" rows="3" style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; resize: vertical;" placeholder="블랙리스트로 등록할 입실자의 상세 사유를 입력하세요"></textarea>
                                @error('blacklist_memo')
                                    <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                                @enderror
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div style="display: flex; gap: 12px; margin-top: 24px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
                        <button type="button" wire:click="close" style="flex: 1; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; color: #374151; font-weight: 500; background-color: white; cursor: pointer; transition: background-color 0.15s;" onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='white';">
                            취소
                        </button>
                        <button type="submit" style="flex: 1; padding: 12px 16px; background-color: #2dd4bf; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background-color 0.15s;" onmouseover="this.style.backgroundColor='#14b8a6';" onmouseout="this.style.backgroundColor='#2dd4bf';">
                            {{ $editingTenantId ? '수정' : '생성' }}
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
    if (typeof Alpine !== 'undefined' && !Alpine.__componentsRegistered) {
        Alpine.__componentsRegistered = true;

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
            getDateInputStyle() {
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
