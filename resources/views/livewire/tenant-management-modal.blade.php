<div>
    @if($show)
    <div style="position: fixed; inset: 0; z-index: 9999; overflow-y: auto;">
        <!-- Backdrop -->
        <div wire:click="close" style="position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5);"></div>

        <!-- Modal Content -->
        <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 16px;">
            <div style="background-color: white; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); width: 100%; max-width: 600px; padding: 32px; position: relative; z-index: 10000;">

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

                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
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

                        <!-- 결제 정보 -->
                        <div style="margin-bottom: 24px;">
                            <h4 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 16px 0; padding-bottom: 8px; border-bottom: 2px solid #e5e7eb;">결제 정보</h4>

                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
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
                                <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">블랙리스트 메모</label>
                                <textarea wire:model="blacklist_memo" rows="3" style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; resize: vertical;" placeholder="메모를 입력하세요"></textarea>
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
