<div>
    @if($show)
    <div style="position: fixed; inset: 0; z-index: 9999; overflow-y: auto;">
        <!-- Backdrop -->
        <div wire:click="close" style="position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5);"></div>

        <!-- Modal Content -->
        <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 16px;">
            <div style="background-color: white; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); width: 100%; max-width: 448px; padding: 24px; position: relative; z-index: 10000;">

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
                                                    <span style="margin-left: 8px; color: #ef4444;">• 현재 {{ $tenant['current_room'] }}호 입주중</span>
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
                        </select>
                        @error('paymentStatus')
                            <span style="font-size: 12px; color: #ef4444; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Dates Info -->
                    <div style="margin-bottom: 24px; padding: 16px; background-color: #F9FBFC; border-radius: 8px;">
                        <p style="font-size: 14px; color: #4b5563; margin: 0;">
                            <span style="font-weight: 500;">입주일:</span> {{ $moveInDate }}
                        </p>
                        <p style="font-size: 14px; color: #4b5563; margin: 8px 0 0 0;">
                            <span style="font-weight: 500;">퇴실일:</span> {{ $moveOutDate }}
                        </p>
                    </div>

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
