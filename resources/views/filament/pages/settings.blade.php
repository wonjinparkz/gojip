<x-filament-panels::page>
    <style>
        .settings-tab { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.75rem 1.25rem; font-size: 0.875rem; font-weight: 500; color: #6b7280; background: none; border: none; cursor: pointer; border-bottom: 2px solid transparent; white-space: nowrap; }
        .settings-tab:hover { color: #374151; }
        .settings-tab.active { color: #111827; font-weight: 600; border-bottom-color: #111827; }
        .settings-tab svg { width: 1rem; height: 1rem; }
        .settings-section { background-color: #ffffff; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 2rem; }
        .settings-field { margin-bottom: 1.5rem; }
        .settings-field label { display: block; font-size: 0.875rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem; }
        .settings-field input, .settings-field select { width: 100%; padding: 0.75rem 1rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background-color: #ffffff; color: #111827; outline: none; box-sizing: border-box; }
        .settings-field input:focus { border-color: #059669; }
        .settings-btn { padding: 0.75rem 1.5rem; font-size: 0.875rem; font-weight: 600; color: #ffffff; background-color: #059669; border: none; border-radius: 0.5rem; cursor: pointer; }
        .settings-btn:hover { background-color: #047857; }
        .settings-btn-dark { padding: 0.75rem 1.5rem; font-size: 0.875rem; font-weight: 600; color: #ffffff; background-color: #111827; border: none; border-radius: 0.5rem; cursor: pointer; }
        .settings-toggle { position: relative; width: 3rem; height: 1.625rem; border-radius: 9999px; cursor: pointer; transition: background-color 0.2s; border: none; flex-shrink: 0; }
        .settings-toggle.on { background-color: #059669; }
        .settings-toggle.off { background-color: #d1d5db; }
        .settings-toggle::after { content: ''; position: absolute; top: 0.125rem; width: 1.375rem; height: 1.375rem; background-color: #ffffff; border-radius: 50%; transition: transform 0.2s; left: 0.125rem; }
        .settings-toggle.on::after { transform: translateX(1.375rem); }
        .settings-row { display: flex; align-items: flex-start; justify-content: space-between; padding: 1.25rem 0; border-bottom: 1px solid #f3f4f6; }
        .settings-row:last-child { border-bottom: none; }

        @media (max-width: 600px) {
            .settings-section { padding: 1rem; border-radius: 0.5rem; }
            .settings-row {
                flex-direction: column;
                align-items: stretch;
                gap: 0.75rem;
                padding: 1rem 0;
            }
            .settings-row > button.settings-toggle,
            .settings-row > .settings-btn-dark { align-self: flex-end; }
            .settings-tab { padding: 0.625rem 0.875rem; font-size: 0.8125rem; }
            .settings-field input,
            .settings-field select { padding: 0.625rem 0.75rem; font-size: 0.875rem; }
            .settings-btn,
            .settings-btn-dark { width: 100%; }
            .settings-row select { font-size: 0.75rem; padding: 0.375rem 0.5rem; }
            .settings-notice-row { gap: 0.5rem; }
            .settings-notice-title {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                min-width: 0;
                flex: 1;
            }
            .settings-support-contact { grid-template-columns: 1fr !important; gap: 0.75rem !important; }
            .settings-support-contact > div { border-left: none !important; border-top: 1px solid #e5e7eb; padding-top: 0.75rem; }
            .settings-support-contact > div:first-child { border-top: none; padding-top: 0; }
        }
    </style>

    <div x-data="{ tab: 'account' }">
        <!-- 탭 네비게이션 -->
        <div style="border-bottom: 1px solid #e5e7eb; margin-bottom: 2rem; overflow-x: auto;">
            <div style="display: flex; gap: 0;">
                <button @click="tab = 'account'" :class="tab === 'account' ? 'active' : ''" class="settings-tab">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    계정
                </button>
                <button @click="tab = 'security'" :class="tab === 'security' ? 'active' : ''" class="settings-tab">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    보안
                </button>
                <button @click="tab = 'notifications'" :class="tab === 'notifications' ? 'active' : ''" class="settings-tab">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    알림
                </button>
                <button @click="tab = 'system'" :class="tab === 'system' ? 'active' : ''" class="settings-tab">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    시스템
                </button>
                <button @click="tab = 'notices'" :class="tab === 'notices' ? 'active' : ''" class="settings-tab">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    공지사항
                </button>
                <button @click="tab = 'support'" :class="tab === 'support' ? 'active' : ''" class="settings-tab">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    고객센터
                </button>
            </div>
        </div>

        <!-- 계정 -->
        <div x-show="tab === 'account'" class="settings-section">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0;">계정 정보</h2>
            <form wire:submit="saveAccount">
                <div class="settings-field">
                    <label>이름</label>
                    <input type="text" wire:model="userName" />
                </div>
                <div class="settings-field">
                    <label>이메일</label>
                    <input type="email" wire:model="userEmail" />
                </div>
                <button type="submit" class="settings-btn">저장</button>
            </form>
        </div>

        <!-- 보안 -->
        <div x-show="tab === 'security'" class="settings-section">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0;">비밀번호 변경</h2>
            <form wire:submit="changePassword">
                <div class="settings-field">
                    <label>현재 비밀번호</label>
                    <input type="password" wire:model="currentPassword" />
                </div>
                <div class="settings-field">
                    <label>새 비밀번호</label>
                    <input type="password" wire:model="newPassword" />
                </div>
                <div class="settings-field">
                    <label>비밀번호 확인</label>
                    <input type="password" wire:model="confirmPassword" />
                </div>
                <button type="submit" class="settings-btn">비밀번호 변경</button>
            </form>
        </div>

        <!-- 알림 -->
        <div x-show="tab === 'notifications'" class="settings-section">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0;">알림 설정</h2>

            <div class="settings-row">
                <div>
                    <p style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0 0 0.25rem 0;">계약 만료 알림</p>
                    <p style="font-size: 0.813rem; color: #6b7280; margin: 0 0 0.75rem 0;">계약 만료 전에 SMS 알림을 받습니다.</p>
                    @if($expirationAlert)
                        <div style="display: inline-flex; align-items: center; border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden;">
                            <span style="padding: 0.375rem 0.75rem; font-size: 0.813rem; color: #6b7280; background-color: #f9fafb;">알림</span>
                            <select wire:model="expirationAlertDays" style="padding: 0.375rem 0.5rem; font-size: 0.813rem; border: none; border-left: 1px solid #e5e7eb; font-weight: 600; color: #111827; background-color: #ffffff;">
                                <option value="3">3일 전</option>
                                <option value="7">1주 전</option>
                                <option value="14">2주 전</option>
                                <option value="30">1개월 전</option>
                            </select>
                        </div>
                    @endif
                </div>
                <button wire:click="$toggle('expirationAlert')" class="settings-toggle {{ $expirationAlert ? 'on' : 'off' }}"></button>
            </div>

            <div class="settings-row">
                <div>
                    <p style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0 0 0.25rem 0;">결제 알림</p>
                    <p style="font-size: 0.813rem; color: #6b7280; margin: 0 0 0.75rem 0;">월세 결제 마감일 전에 SMS 알림을 받습니다.</p>
                    @if($paymentAlert)
                        <div style="display: inline-flex; align-items: center; border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden;">
                            <span style="padding: 0.375rem 0.75rem; font-size: 0.813rem; color: #6b7280; background-color: #f9fafb;">알림</span>
                            <select wire:model="paymentAlertDays" style="padding: 0.375rem 0.5rem; font-size: 0.813rem; border: none; border-left: 1px solid #e5e7eb; font-weight: 600; color: #111827; background-color: #ffffff;">
                                <option value="1">1일 전</option>
                                <option value="3">3일 전</option>
                                <option value="7">1주 전</option>
                            </select>
                        </div>
                    @endif
                </div>
                <button wire:click="$toggle('paymentAlert')" class="settings-toggle {{ $paymentAlert ? 'on' : 'off' }}"></button>
            </div>

            <div class="settings-row">
                <div>
                    <p style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0 0 0.25rem 0;">시스템 업데이트 알림</p>
                    <p style="font-size: 0.813rem; color: #6b7280; margin: 0;">시스템 업데이트 및 새로운 기능에 대한 알림을 받습니다.</p>
                </div>
                <button wire:click="$toggle('systemUpdateAlert')" class="settings-toggle {{ $systemUpdateAlert ? 'on' : 'off' }}"></button>
            </div>

            <div style="margin-top: 1.5rem;">
                <button wire:click="saveNotifications" class="settings-btn">저장</button>
            </div>
        </div>

        <!-- 시스템 -->
        <div x-show="tab === 'system'" class="settings-section">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0;">시스템 설정</h2>

            <div class="settings-row">
                <div>
                    <p style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0 0 0.25rem 0;">자동 백업</p>
                    <p style="font-size: 0.813rem; color: #6b7280; margin: 0 0 0.75rem 0;">시스템 데이터 자동 백업을 활성화합니다.</p>
                    @if($autoBackup)
                        <div style="display: inline-flex; align-items: center; border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden;">
                            <span style="padding: 0.375rem 0.75rem; font-size: 0.813rem; color: #6b7280; background-color: #f9fafb;">백업 주기</span>
                            <select wire:model="backupFrequency" style="padding: 0.375rem 0.5rem; font-size: 0.813rem; border: none; border-left: 1px solid #e5e7eb; font-weight: 600; color: #111827; background-color: #ffffff;">
                                <option value="daily">매일</option>
                                <option value="weekly">주간</option>
                                <option value="monthly">월간</option>
                            </select>
                        </div>
                    @endif
                </div>
                <button wire:click="$toggle('autoBackup')" class="settings-toggle {{ $autoBackup ? 'on' : 'off' }}"></button>
            </div>

            <div class="settings-row">
                <div>
                    <p style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0 0 0.25rem 0;">데이터 백업하기</p>
                    <p style="font-size: 0.813rem; color: #6b7280; margin: 0;">현재 시스템 데이터를 파일로 내보냅니다.</p>
                </div>
                <button class="settings-btn-dark">백업하기</button>
            </div>

            <div class="settings-row">
                <div>
                    <p style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0 0 0.25rem 0;">데이터 불러오기</p>
                    <p style="font-size: 0.813rem; color: #6b7280; margin: 0;">백업 파일에서 데이터를 불러옵니다.</p>
                </div>
                <button class="settings-btn-dark">불러오기</button>
            </div>

            <div style="margin-top: 1.5rem;">
                <button wire:click="saveSystem" class="settings-btn">저장</button>
            </div>
        </div>

        <!-- 공지사항 -->
        <div x-show="tab === 'notices'" class="settings-section">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0;">공지사항</h2>

            @php
                $notices = [
                    ['title' => '시스템 정기 점검 안내', 'date' => '2026.03.05', 'new' => true],
                    ['title' => '새로운 기능 업데이트 안내 v2.3', 'date' => '2026.02.20', 'new' => false],
                    ['title' => '개인정보 처리방침 개정 안내', 'date' => '2026.02.10', 'new' => false],
                    ['title' => '서비스 이용약관 변경 안내', 'date' => '2026.01.15', 'new' => false],
                ];
            @endphp

            @foreach($notices as $notice)
                <div class="settings-notice-row" style="display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 0; {{ !$loop->last ? 'border-bottom: 1px solid #f3f4f6;' : '' }}">
                    <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0; flex: 1;">
                        @if($notice['new'])
                            <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; font-size: 0.688rem; font-weight: 600; background-color: #059669; color: #ffffff; border-radius: 0.25rem; flex-shrink: 0;">NEW</span>
                        @endif
                        <span class="settings-notice-title" style="font-size: 0.875rem; font-weight: {{ $notice['new'] ? '600' : '400' }}; color: #111827;">{{ $notice['title'] }}</span>
                    </div>
                    <span style="font-size: 0.813rem; color: #9ca3af; flex-shrink: 0;">{{ $notice['date'] }}</span>
                </div>
            @endforeach
        </div>

        <!-- 고객센터 -->
        <div x-show="tab === 'support'" class="settings-section">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0;">고객센터</h2>

            <div class="settings-support-contact" style="background-color: #f9fafb; border-radius: 0.75rem; padding: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
                <div style="text-align: center;">
                    <p style="font-size: 0.75rem; color: #9ca3af; margin: 0 0 0.25rem 0;">운영시간</p>
                    <p style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0;">평일 09:00 - 18:00</p>
                </div>
                <div style="text-align: center; border-left: 1px solid #e5e7eb;">
                    <p style="font-size: 0.75rem; color: #9ca3af; margin: 0 0 0.25rem 0;">이메일 문의</p>
                    <p style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0;">support@gojip.com</p>
                </div>
            </div>

            <h3 style="font-size: 1rem; font-weight: 700; color: #111827; margin: 0 0 1rem 0;">자주 묻는 질문</h3>

            <div x-data="{ openFaq: null }">
                @php
                    $faqs = [
                        ['q' => 'Q. 비밀번호를 잊어버렸어요.', 'a' => '로그인 페이지에서 "비밀번호 찾기"를 클릭하여 등록된 이메일로 재설정 링크를 받을 수 있습니다.'],
                        ['q' => 'Q. 입주자 정보를 수정하려면 어떻게 하나요?', 'a' => '입주자 관리 메뉴에서 해당 입주자를 선택한 후 수정 버튼을 클릭하여 정보를 변경할 수 있습니다.'],
                        ['q' => 'Q. 결제 내역은 어디서 확인하나요?', 'a' => '수납 관리 메뉴에서 호실별 결제 내역을 확인할 수 있으며, 월별 수입 분석도 제공됩니다.'],
                    ];
                @endphp

                @foreach($faqs as $idx => $faq)
                    <div style="{{ !$loop->last ? 'border-bottom: 1px solid #f3f4f6;' : '' }}">
                        <button
                            @click="openFaq = openFaq === {{ $idx }} ? null : {{ $idx }}"
                            style="display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 1rem 0; background: none; border: none; cursor: pointer; text-align: left;"
                        >
                            <span style="font-size: 0.875rem; font-weight: 500; color: #111827;">{{ $faq['q'] }}</span>
                            <span style="font-size: 1.25rem; color: #9ca3af; transition: transform 0.2s;" :style="openFaq === {{ $idx }} ? 'transform: rotate(45deg)' : ''">+</span>
                        </button>
                        <div x-show="openFaq === {{ $idx }}" x-collapse style="padding: 0 0 1rem 0;">
                            <p style="font-size: 0.813rem; color: #6b7280; margin: 0; line-height: 1.6;">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-filament-panels::page>
