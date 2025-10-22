<div>
    @if($show)
    <div style="position: fixed; inset: 0; z-index: 9999; overflow-y: auto;">
        <!-- Backdrop -->
        <div wire:click="close" style="position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5);"></div>

        <!-- Modal Content -->
        <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 16px;">
            <div style="background-color: white; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); width: 100%; max-width: 500px; padding: 24px; position: relative; z-index: 10000;">

                <!-- Modal Header -->
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                    <h3 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0;">입주자 정보 수정</h3>
                    <button wire:click="close" type="button" style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px;">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form wire:submit="save">
                    <!-- Tenant Info (Read Only) -->
                    <div style="margin-bottom: 16px; padding: 16px; background-color: #F9FBFC; border-radius: 8px;">
                        <p style="font-size: 14px; color: #4b5563; margin: 0;">
                            <span style="font-weight: 500;">입주자:</span> {{ $tenantName }}
                        </p>
                        <p style="font-size: 14px; color: #4b5563; margin: 8px 0 0 0;">
                            <span style="font-weight: 500;">호실:</span> {{ $roomNumber }}호
                        </p>
                    </div>

                    <!-- Payment Status -->
                    <div style="margin-bottom: 16px;">
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

                    <!-- Move In Date -->
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">입주일 *</label>
                        <input type="date"
                               wire:model="moveInDate"
                               style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;"
                               onfocus="this.style.outline='2px solid #2dd4bf'; this.style.borderColor='transparent';"
                               onblur="this.style.outline='none'; this.style.borderColor='#d1d5db';">
                        @error('moveInDate')
                            <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Move Out Date -->
                    <div style="margin-bottom: 24px;" x-data="{
                        indefinite: @entangle('indefiniteMoveOut').live,
                        updateDate() {
                            if (this.indefinite) {
                                const today = new Date();
                                const year = today.getFullYear();
                                const month = String(today.getMonth() + 1).padStart(2, '0');
                                const day = String(today.getDate()).padStart(2, '0');
                                return `${year}-${month}-${day}`;
                            }
                            return '';
                        }
                    }" x-init="
                        setInterval(() => {
                            if (indefinite) {
                                $wire.set('moveOutDate', updateDate());
                            }
                        }, 60000);
                    ">
                        <label style="display: flex; align-items: center; justify-content: space-between; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">
                            <span>퇴실일 *</span>
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: 400; cursor: pointer;">
                                <input type="checkbox"
                                       wire:model.live="indefiniteMoveOut"
                                       style="width: 16px; height: 16px; cursor: pointer; accent-color: #2dd4bf;">
                                <span style="font-size: 13px; color: #6b7280;">퇴실일 미정</span>
                            </label>
                        </label>
                        <input type="date"
                               wire:model="moveOutDate"
                               x-bind:disabled="indefinite"
                               x-bind:style="indefinite ? 'width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background-color: #f3f4f6; cursor: not-allowed;' : 'width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;'"
                               onfocus="this.style.outline='2px solid #2dd4bf'; this.style.borderColor='transparent';"
                               onblur="this.style.outline='none'; this.style.borderColor='#d1d5db';">
                        @error('moveOutDate')
                            <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div style="display: flex; gap: 12px; margin-bottom: 16px;">
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
                            수정
                        </button>
                    </div>

                    <!-- Delete Button -->
                    <div style="border-top: 1px solid #e5e7eb; padding-top: 16px;">
                        <button type="button"
                                wire:click="delete"
                                onclick="return confirm('이 입주자 일정을 삭제하시겠습니까?')"
                                style="width: 100%; padding: 12px 16px; background-color: #ef4444; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background-color 0.15s;"
                                onmouseover="this.style.backgroundColor='#dc2626';"
                                onmouseout="this.style.backgroundColor='#ef4444';">
                            일정 삭제
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
