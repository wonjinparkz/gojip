<div>
    @script
    <script>
        $wire.on('open-external-url', ({ url }) => {
            if (url) window.open(url, '_blank', 'noopener');
        });
    </script>
    @endscript

    @if($isOpen)
        <!-- Backdrop -->
        <div
            wire:click="closeModal"
            style="position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 40;"
        ></div>

        <!-- Modal -->
        <div style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; padding: 1rem;">
            <div style="background-color: #ffffff; border-radius: 0.75rem; max-width: 26rem; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">

                @if($mode === 'issue')
                    {{-- ② 현금영수증 발행 모달 --}}
                    <!-- Header -->
                    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e5e7eb;">
                        <p style="font-size: 0.75rem; color: #6b7280; margin: 0 0 0.25rem 0; letter-spacing: 0.05em;">국세청 연동 · NTS</p>
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">현금영수증 발행</h3>
                    </div>

                    <div style="padding: 1.5rem;">
                        <!-- 결제 금액 표시 -->
                        <div style="background-color: #111827; border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1.5rem; text-align: center;">
                            <p style="font-size: 0.75rem; color: #9ca3af; margin: 0 0 0.375rem 0;">결제 금액</p>
                            <p style="font-size: 1.5rem; font-weight: 700; color: #ffffff; margin: 0;">
                                ₩{{ $tenant?->room?->monthly_rent ? number_format($tenant->room->monthly_rent) : '0' }}
                            </p>
                        </div>

                        <!-- 발행 유형 선택 -->
                        <div style="margin-bottom: 1.25rem;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                                <button
                                    wire:click="$set('receiptType', 'personal')"
                                    type="button"
                                    style="padding: 0.75rem; font-size: 0.875rem; font-weight: 500; border-radius: 0.5rem; cursor: pointer; text-align: center;
                                        {{ $receiptType === 'personal'
                                            ? 'background-color: #ffffff; color: #059669; border: 2px solid #059669;'
                                            : 'background-color: #f9fafb; color: #6b7280; border: 1px solid #e5e7eb;' }}"
                                >
                                    개인 소득공제
                                </button>
                                <button
                                    wire:click="$set('receiptType', 'business')"
                                    type="button"
                                    style="padding: 0.75rem; font-size: 0.875rem; font-weight: 500; border-radius: 0.5rem; cursor: pointer; text-align: center;
                                        {{ $receiptType === 'business'
                                            ? 'background-color: #ffffff; color: #059669; border: 2px solid #059669;'
                                            : 'background-color: #f9fafb; color: #6b7280; border: 1px solid #e5e7eb;' }}"
                                >
                                    사업자 지출증빙
                                </button>
                            </div>
                        </div>

                        <!-- 번호 입력 -->
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">
                                {{ $receiptType === 'personal' ? '휴대폰 번호' : '사업자등록번호' }}
                            </label>
                            <div style="position: relative;">
                                <input
                                    type="text"
                                    wire:model="identifierNumber"
                                    placeholder="{{ $receiptType === 'personal' ? '010-0000-0000' : '000-00-00000' }}"
                                    maxlength="{{ $receiptType === 'personal' ? '13' : '12' }}"
                                    style="width: 100%; padding: 0.75rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background-color: #ffffff; color: #111827; outline: none; box-sizing: border-box;"
                                    wire:keydown.enter="issue"
                                    wire:keydown.escape="closeModal"
                                />
                                <span style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem; color: #9ca3af;">
                                    {{ strlen($identifierNumber) }}/{{ $receiptType === 'personal' ? '11' : '10' }}자리
                                </span>
                            </div>
                            @error('identifierNumber')
                                <span style="font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- 버튼 -->
                        <div style="display: flex; gap: 0.75rem;">
                            <button
                                wire:click="closeModal"
                                type="button"
                                style="flex: 1; padding: 0.75rem; font-size: 0.875rem; font-weight: 500; background-color: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 0.5rem; cursor: pointer;"
                            >
                                취소
                            </button>
                            <button
                                wire:click="issue"
                                type="button"
                                style="flex: 1; padding: 0.75rem; font-size: 0.875rem; font-weight: 500; border: none; border-radius: 0.5rem; cursor: pointer;
                                    {{ strlen($identifierNumber) >= 10
                                        ? 'background-color: #059669; color: #ffffff;'
                                        : 'background-color: #d1d5db; color: #9ca3af; pointer-events: none;' }}"
                                {{ strlen($identifierNumber) < 10 ? 'disabled' : '' }}
                            >
                                발행하기
                            </button>
                        </div>

                        <!-- 안내문 -->
                        <p style="margin-top: 1rem; font-size: 0.75rem; color: #9ca3af; text-align: center;">
                            ※ 국세청 현금영수증 시스템을 통해 발행됩니다.
                        </p>
                    </div>

                @elseif($mode === 'view' && $receipt)
                    {{-- ③ 현금영수증 확인 모달 --}}
                    <!-- Header -->
                    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e5e7eb;">
                        @if($receipt->isCanceled())
                            <p style="font-size: 0.75rem; color: #dc2626; margin: 0 0 0.25rem 0; letter-spacing: 0.05em;">취소발행 완료</p>
                        @else
                            <p style="font-size: 0.75rem; color: #059669; margin: 0 0 0.25rem 0; letter-spacing: 0.05em;">발행 완료</p>
                        @endif
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">현금영수증</h3>
                    </div>

                    <div style="padding: 1.5rem;">
                        <!-- 영수증 정보 -->
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid #f3f4f6;">
                                <span style="font-size: 0.875rem; color: #6b7280;">영수증 번호</span>
                                <span style="font-size: 0.875rem; font-weight: 500; color: #111827;">{{ $receipt->receipt_number }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid #f3f4f6;">
                                <span style="font-size: 0.875rem; color: #6b7280;">발행일</span>
                                <span style="font-size: 0.875rem; font-weight: 500; color: #111827;">{{ $receipt->issued_date->format('Y.m.d') }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid #f3f4f6;">
                                <span style="font-size: 0.875rem; color: #6b7280;">입주자 이름</span>
                                <span style="font-size: 0.875rem; font-weight: 500; color: #111827;">{{ $tenant?->name ?? '-' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid #f3f4f6;">
                                <span style="font-size: 0.875rem; color: #6b7280;">호실 번호</span>
                                <span style="font-size: 0.875rem; font-weight: 500; color: #111827;">{{ $tenant?->room?->room_number ?? '-' }}호</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid #f3f4f6;">
                                <span style="font-size: 0.875rem; color: #6b7280;">결제 방법</span>
                                <span style="font-size: 0.875rem; font-weight: 500; color: #111827;">{{ $tenant?->payment_method_label ?? '-' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.875rem; color: #6b7280;">금액</span>
                                <span style="font-size: 1.125rem; font-weight: 700; color: #111827;">₩{{ number_format($receipt->amount) }}</span>
                            </div>
                            @if($receipt->confirm_num)
                                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.75rem; border-top: 1px solid #f3f4f6;">
                                    <span style="font-size: 0.75rem; color: #6b7280;">국세청 승인번호</span>
                                    <span style="font-size: 0.75rem; font-family: monospace; color: #111827;">{{ $receipt->confirm_num }}</span>
                                </div>
                            @endif
                            @if($receipt->isCanceled() && $receipt->revoke_confirm_num)
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 0.75rem; color: #dc2626;">취소 승인번호</span>
                                    <span style="font-size: 0.75rem; font-family: monospace; color: #dc2626;">{{ $receipt->revoke_confirm_num }}</span>
                                </div>
                            @endif
                        </div>

                        @error('cancel')
                            <p style="margin-top: 0.75rem; font-size: 0.75rem; color: #ef4444;">{{ $message }}</p>
                        @enderror
                        @error('popup')
                            <p style="margin-top: 0.75rem; font-size: 0.75rem; color: #ef4444;">{{ $message }}</p>
                        @enderror

                        <!-- 버튼 -->
                        <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem; flex-wrap: wrap;">
                            <button
                                wire:click="closeModal"
                                type="button"
                                style="flex: 1 1 40%; padding: 0.75rem; font-size: 0.875rem; font-weight: 500; background-color: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 0.5rem; cursor: pointer;"
                            >
                                닫기
                            </button>
                            @if(!$receipt->isCanceled() && $receipt->confirm_num)
                                <button
                                    wire:click="openPopupUrl"
                                    wire:loading.attr="disabled"
                                    type="button"
                                    style="flex: 1 1 40%; padding: 0.75rem; font-size: 0.875rem; font-weight: 500; background-color: #2563eb; color: #ffffff; border: none; border-radius: 0.5rem; cursor: pointer;"
                                >
                                    팝빌에서 보기
                                </button>
                                <button
                                    wire:click="cancel"
                                    wire:confirm="정말 현금영수증을 취소발행하시겠습니까? (국세청 전송됨)"
                                    wire:loading.attr="disabled"
                                    type="button"
                                    style="flex: 1 1 100%; padding: 0.75rem; font-size: 0.875rem; font-weight: 500; background-color: #ffffff; color: #dc2626; border: 1px solid #dc2626; border-radius: 0.5rem; cursor: pointer;"
                                >
                                    현금영수증 취소
                                </button>
                            @endif
                        </div>

                        <!-- 안내문 -->
                        <p style="margin-top: 1rem; font-size: 0.75rem; color: #9ca3af; text-align: center;">
                            ※ 국세청 현금영수증 시스템을 통해 발행됩니다.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
