<div>
    @if($isOpen)
        <!-- Backdrop -->
        <div
            wire:click="closeModal"
            style="position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 40;"
        ></div>

        <!-- Modal -->
        <div style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; padding: 1rem;">
            <div style="background-color: #ffffff; border-radius: 0.75rem; max-width: 28rem; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
                <!-- Header -->
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb;">
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin: 0;">수납 정보 수정</h3>
                    <button
                        wire:click="closeModal"
                        style="padding: 0.5rem; background: none; border: none; cursor: pointer; color: #6b7280;"
                    >
                        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div style="padding: 1.5rem;">
                    @if($tenant)
                        <!-- Tenant Info -->
                        <div style="background-color: #f9fafb; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                            <div style="display: flex; gap: 1rem; align-items: center;">
                                <div>
                                    <span style="font-weight: 700; font-size: 1.125rem; color: #111827;">{{ $tenant->room?->room_number ?? '-' }}호</span>
                                </div>
                                <div>
                                    <span style="font-weight: 500; color: #111827;">{{ $tenant->name }}</span>
                                </div>
                            </div>
                            <div style="margin-top: 0.5rem; font-size: 0.875rem; color: #6b7280;">
                                월세: {{ $tenant->room?->monthly_rent ? number_format($tenant->room->monthly_rent) . '원' : '-' }}
                            </div>
                        </div>

                        <!-- Form -->
                        <form wire:submit="save">
                            <div style="display: flex; flex-direction: column; gap: 1rem;">
                                <!-- 결제일 -->
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">결제일 (매월)</label>
                                    <select
                                        wire:model="payment_due_day"
                                        style="width: 100%; padding: 0.625rem 0.75rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background-color: #ffffff; color: #111827;"
                                    >
                                        <option value="">선택</option>
                                        @for($i = 1; $i <= 31; $i++)
                                            <option value="{{ $i }}">{{ $i }}일</option>
                                        @endfor
                                    </select>
                                    @error('payment_due_day')
                                        <span style="font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- 실제 결제일 -->
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">실제 결제일</label>
                                    <input
                                        type="date"
                                        wire:model="actual_payment_date"
                                        style="width: 100%; padding: 0.625rem 0.75rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background-color: #ffffff; color: #111827;"
                                    />
                                    @error('actual_payment_date')
                                        <span style="font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- 결제 방법 -->
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">결제 방법</label>
                                    <select
                                        wire:model="payment_method"
                                        style="width: 100%; padding: 0.625rem 0.75rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background-color: #ffffff; color: #111827;"
                                    >
                                        <option value="">선택</option>
                                        <option value="card">카드</option>
                                        <option value="transfer">계좌이체</option>
                                        <option value="cash">현금</option>
                                    </select>
                                    @error('payment_method')
                                        <span style="font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- 납부 상태 -->
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">납부 상태</label>
                                    <select
                                        wire:model="payment_status"
                                        style="width: 100%; padding: 0.625rem 0.75rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background-color: #ffffff; color: #111827;"
                                    >
                                        <option value="">선택</option>
                                        <option value="paid">납부완료</option>
                                        <option value="pending">미납</option>
                                        <option value="overdue">연체</option>
                                        <option value="waiting">대기</option>
                                    </select>
                                    @error('payment_status')
                                        <span style="font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Actions -->
                            <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                                <button
                                    type="button"
                                    wire:click="closeModal"
                                    style="flex: 1; padding: 0.75rem; font-size: 0.875rem; font-weight: 500; background-color: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 0.5rem; cursor: pointer;"
                                >
                                    취소
                                </button>
                                <button
                                    type="submit"
                                    style="flex: 1; padding: 0.75rem; font-size: 0.875rem; font-weight: 500; background-color: #111827; color: #ffffff; border: none; border-radius: 0.5rem; cursor: pointer;"
                                >
                                    저장
                                </button>
                            </div>
                        </form>
                    @else
                        <div style="text-align: center; padding: 2rem; color: #6b7280;">
                            입주자 정보를 찾을 수 없습니다.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
