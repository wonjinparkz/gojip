<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\Support\Enums\Width;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->topbar(false)
            ->breadcrumbs(false)
            ->login()
            ->colors([
                'primary' => "#1EC3B0",
            ])
            ->maxContentWidth(Width::Full)
            ->darkMode(false)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render(<<<'HTML'
                <style>
                    /* Page background to white */
                    .fi-body {
                        background-color: #ffffff !important;
                    }

                    /* Sidebar background to white */
                    .fi-sidebar {
                        background-color: #ffffff !important;
                        border-right: 1px solid #e5e7eb;
                    }

                    .fi-sidebar-nav {
                        background-color: #ffffff !important;
                    }

                    /* Sidebar navigation items */
                    .fi-sidebar-item-button {
                        color: #374151 !important;
                    }

                    /* Active sidebar item */
                    .fi-sidebar-item-button.fi-active {
                        background-color: #fef3c7 !important;
                        color: #92400e !important;
                    }

                    /* Hover state for sidebar items */
                    .fi-sidebar-item-button:hover {
                        background-color: #fef3c7 !important;
                    }

                    /* Fixed footer bar - only for main content area, not sidebar */
                    .custom-footer-bar {
                        position: fixed;
                        bottom: 0;
                        left: var(--sidebar-width, 16rem);
                        right: 0;
                        background-color: #ffffff;
                        border-top: 1px solid #e5e7eb;
                        padding: 0.75rem 1rem;
                        z-index: 30;
                        display: flex;
                        align-items: center;
                        justify-content: flex-start;
                        gap: 0.175rem;
                    }

                    /* Icon button for add and menu */
                    .icon-button {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        gap: 0.5rem;
                        white-space: nowrap;
                        font-size: 0.875rem;
                        font-weight: 500;
                        height: 2.25rem;
                        flex-shrink: 0;
                        padding: 0.375rem 0.375rem;
                        background-color: #F3F4F6;
                        color: #6b7280;
                        border-radius: 0.75rem;
                        transition: all 0.2s;
                        border: none;
                        cursor: pointer;
                    }

                    .icon-button:hover {
                        background-color: #e5e7eb;
                        color: #374151;
                    }

                    .icon-button svg {
                        width: 0.875rem;
                        height: 0.875rem;
                    }

                    /* Branch badge button */
                    .branch-badge {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        gap: 0.5rem;
                        white-space: nowrap;
                        font-size: 0.875rem;
                        height: 2.25rem;
                        flex-shrink: 0;
                        margin-right: 0.5rem;
                        padding: 0.375rem 1rem;
                        border-radius: 0.75rem;
                        transition: all 0.2s;
                        font-weight: 500;
                        border: none;
                        cursor: pointer;
                    }

                    /* Active branch badge */
                    .branch-badge-active {
                        background-color: #48C8C8;
                        color: #ffffff;
                    }

                    .branch-badge-active:hover {
                        background-color: #3ab5b5;
                    }

                    /* Inactive branch badge */
                    .branch-badge-inactive {
                        background-color: #F3F4F6;
                        color: #303A48;
                    }

                    .branch-badge-inactive:hover {
                        background-color: #e5e7eb;
                        color: #1f2937;
                    }

                    /* Add padding to main content to prevent content from being hidden behind footer */
                    .fi-main {
                        padding-bottom: 4rem !important;
                    }

                    /* Force white text color for primary buttons */
                    .fi-btn-primary,
                    [data-variant="primary"] {
                        color: #ffffff !important;
                    }

                    .fi-btn-primary svg,
                    [data-variant="primary"] svg {
                        color: #ffffff !important;
                    }

                    /* Branch dropdown styles */
                    .branch-dropdown-wrapper {
                        position: relative;
                        display: inline-block;
                        margin-right: 0.25rem;
                    }

                    .branch-dropdown-content {
                        position: absolute;
                        bottom: 100%;
                        left: 0;
                        margin-bottom: 0.5rem;
                        max-height: 400px;
                        min-width: 12rem;
                        overflow-y: auto;
                        overflow-x: hidden;
                        border: 1px solid #e5e7eb;
                        background-color: white;
                        padding: 0.25rem;
                        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                        border-radius: 1rem;
                        outline: none;
                        z-index: 50;
                    }

                    .branch-dropdown-item {
                        position: relative;
                        display: flex;
                        cursor: pointer;
                        user-select: none;
                        align-items: center;
                        gap: 0.5rem;
                        outline: none;
                        transition: background-color 0.15s ease-in-out, color 0.15s ease-in-out;
                        font-size: 0.875rem;
                        line-height: 1.25rem;
                        border-radius: 0.75rem;
                        padding: 0.75rem;
                        border: none;
                        background: none;
                        width: 100%;
                        text-align: left;
                        color: #374151;
                    }

                    .branch-dropdown-item:hover {
                        background-color: rgba(64, 192, 192, 0.1);
                        color: #40c0c0;
                    }

                    .branch-dropdown-item svg {
                        height: 1rem;
                        width: 1rem;
                        margin-right: 0.75rem;
                        pointer-events: none;
                        flex-shrink: 0;
                    }

                    @media (min-width: 768px) {
                        .icon-button {
                            margin-right: 0.25rem;
                            padding: 0.5rem 0.5rem;
                        }

                        .icon-button svg {
                            width: 1rem;
                            height: 1rem;
                        }

                        .branch-badge {
                            margin-right: 0.25rem;
                            padding: 0.5rem 1rem;
                        }
                    }
                </style>
                HTML)
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render(<<<'HTML'
                <script>
                    function toggleBranchDropdown() {
                        const dropdown = document.getElementById('branch-dropdown');
                        const isHidden = dropdown.style.display === 'none' || dropdown.style.display === '';
                        dropdown.style.display = isHidden ? 'block' : 'none';
                    }

                    function addPhoneNumberField() {
                        const container = document.getElementById('phone-numbers-container');
                        const newRow = document.createElement('div');
                        newRow.className = 'phone-number-row';
                        newRow.style.cssText = 'display: flex; gap: 0.5rem; align-items: center;';

                        newRow.innerHTML = `
                            <input type="text" class="branch-phone-input" placeholder="예: 02-1234-5678" pattern="[0-9-]+" style="flex: 1; height: 2.5rem; border-radius: 0.375rem; border: 1px solid #d1d5db; background: white; padding: 0.5rem 0.75rem; font-size: 0.875rem; outline: none;">
                            <button type="button" onclick="removePhoneNumberField(this)" style="display: inline-flex; align-items: center; justify-content: center; height: 2.5rem; width: 2.5rem; border-radius: 0.375rem; border: 1px solid #d1d5db; background: white; color: #ef4444; cursor: pointer; flex-shrink: 0;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14"></path>
                                </svg>
                            </button>
                        `;

                        container.appendChild(newRow);

                        // Add input filter to new phone input
                        const newInput = newRow.querySelector('.branch-phone-input');
                        newInput.addEventListener('input', function(e) {
                            this.value = this.value.replace(/[^0-9-]/g, '');
                        });
                    }

                    function removePhoneNumberField(button) {
                        button.parentElement.remove();
                    }

                    function openAddBranchModal() {
                        document.getElementById('add-branch-modal').style.display = 'flex';

                        // Add input filter for all phone number inputs
                        document.querySelectorAll('.branch-phone-input').forEach(function(input) {
                            if (!input.dataset.listenerAdded) {
                                input.addEventListener('input', function(e) {
                                    this.value = this.value.replace(/[^0-9-]/g, '');
                                });
                                input.dataset.listenerAdded = 'true';
                            }
                        });
                    }

                    function closeAddBranchModal() {
                        document.getElementById('add-branch-modal').style.display = 'none';
                        document.getElementById('branch-form').reset();

                        // Remove all additional phone number fields
                        const container = document.getElementById('phone-numbers-container');
                        const rows = container.querySelectorAll('.phone-number-row');
                        rows.forEach((row, index) => {
                            if (index > 0) {
                                row.remove();
                            }
                        });

                        // Clear the first phone input
                        const firstInput = container.querySelector('.branch-phone-input');
                        if (firstInput) {
                            firstInput.value = '';
                        }
                    }

                    async function submitAddBranch() {
                        const name = document.getElementById('branchName').value;
                        const address = document.getElementById('branchAddress').value;

                        // Collect all phone numbers
                        const phoneInputs = document.querySelectorAll('.branch-phone-input');
                        const phoneNumbers = Array.from(phoneInputs)
                            .map(input => input.value.trim())
                            .filter(phone => phone !== '');
                        const phone = phoneNumbers.join(',');

                        if (!name) {
                            alert('지점명을 입력해주세요.');
                            return;
                        }

                        try {
                            const response = await fetch('{{ route("filament.admin.pages.add-branch") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({ name, address, phone })
                            });

                            if (response.ok) {
                                window.location.reload();
                            } else {
                                alert('지점 추가에 실패했습니다.');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('오류가 발생했습니다.');
                        }
                    }

                    // Close dropdown when clicking outside
                    document.addEventListener('click', function(event) {
                        const dropdown = document.getElementById('branch-dropdown');
                        const menuButton = document.getElementById('branch-menu-button');

                        if (dropdown && menuButton &&
                            !dropdown.contains(event.target) &&
                            !menuButton.contains(event.target)) {
                            dropdown.style.display = 'none';
                        }
                    });
                </script>

                <div class="custom-footer-bar">
                    @auth
                        <!-- Add Branch Button -->
                        <button
                            class="icon-button"
                            data-testid="button-add-branch"
                            type="button"
                            onclick="openAddBranchModal()"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14"></path>
                                <path d="M12 5v14"></path>
                            </svg>
                        </button>

                        @php
                            $user = auth()->user();
                            $branches = $user->branches;
                            $currentBranchId = session('current_branch_id', $branches->first()?->id);
                        @endphp

                        <!-- Menu Button with Dropdown -->
                        <div class="branch-dropdown-wrapper">
                            <button
                                id="branch-menu-button"
                                class="icon-button"
                                data-testid="button-branch-menu"
                                type="button"
                                onclick="toggleBranchDropdown()"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="4" x2="20" y1="12" y2="12"></line>
                                    <line x1="4" x2="20" y1="6" y2="6"></line>
                                    <line x1="4" x2="20" y1="18" y2="18"></line>
                                </svg>
                            </button>

                            <!-- Branch Dropdown Menu -->
                            <div id="branch-dropdown" class="branch-dropdown-content" style="display: none;">
                                @if($branches->count() > 0)
                                    @foreach($branches as $branch)
                                        <button
                                            class="branch-dropdown-item"
                                            onclick="window.location.href='{{ route('filament.admin.pages.switch-branch', ['branch' => $branch->id]) }}'"
                                            data-testid="dropdown-branch-{{ $branch->id }}"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path>
                                                <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path>
                                                <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path>
                                                <path d="M10 6h4"></path>
                                                <path d="M10 10h4"></path>
                                                <path d="M10 14h4"></path>
                                                <path d="M10 18h4"></path>
                                            </svg>
                                            {{ $branch->name }}
                                            @if($branch->id === $currentBranchId)
                                                <span style="margin-left: auto; color: #48C8C8; font-weight: 600;">✓</span>
                                            @endif
                                        </button>
                                    @endforeach
                                @else
                                    <div style="padding: 0.75rem; font-size: 0.875rem; color: #6b7280;">
                                        지점 정보가 없습니다
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($branches->count() > 0)
                            @foreach($branches as $branch)
                                <button
                                    class="branch-badge {{ $branch->id === $currentBranchId ? 'branch-badge-active' : 'branch-badge-inactive' }}"
                                    onclick="window.location.href='{{ route('filament.admin.pages.switch-branch', ['branch' => $branch->id]) }}'"
                                    data-testid="button-branch-{{ $branch->id }}"
                                >
                                    <span class="text-xs md:text-sm">{{ $branch->name }}</span>
                                </button>
                            @endforeach
                        @else
                            <span class="text-sm text-gray-600">지점 정보가 없습니다</span>
                        @endif
                    @endauth
                </div>

                <!-- Add Branch Modal -->
                <div id="add-branch-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 50; background-color: rgba(0, 0, 0, 0.5); align-items: center; justify-content: center;">
                    <div style="position: fixed; left: 50%; top: 50%; transform: translate(-50%, -50%); z-index: 50; display: grid; width: 100%; gap: 1rem; border: 1px solid #e5e7eb; background: white; padding: 1.5rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); max-width: 28rem; border-radius: 0.5rem;">
                        <div style="display: flex; flex-direction: column; gap: 0.375rem; text-align: center;">
                            <h2 style="font-size: 1.125rem; font-weight: 600; line-height: 1.25; letter-spacing: -0.025em;">새 지점 추가</h2>
                            <p style="font-size: 0.875rem; color: #6b7280;">새로운 지점의 정보를 입력해주세요.</p>
                        </div>
                        <form id="branch-form" style="display: flex; flex-direction: column; gap: 1rem; padding: 1rem 0;">
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <label for="branchName" style="font-size: 0.875rem; font-weight: 500; line-height: 1.25;">지점명 *</label>
                                <input id="branchName" type="text" placeholder="예: 강남점" data-testid="input-branch-name" style="display: flex; height: 2.5rem; width: 100%; border-radius: 0.375rem; border: 1px solid #d1d5db; background: white; padding: 0.5rem 0.75rem; font-size: 0.875rem; outline: none;">
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <label for="branchAddress" style="font-size: 0.875rem; font-weight: 500; line-height: 1.25;">주소</label>
                                <input id="branchAddress" type="text" placeholder="예: 서울시 강남구 테헤란로" data-testid="input-branch-address" style="display: flex; height: 2.5rem; width: 100%; border-radius: 0.375rem; border: 1px solid #d1d5db; background: white; padding: 0.5rem 0.75rem; font-size: 0.875rem; outline: none;">
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <label style="font-size: 0.875rem; font-weight: 500; line-height: 1.25;">전화번호</label>
                                <div id="phone-numbers-container" style="display: flex; flex-direction: column; gap: 0.5rem;">
                                    <div class="phone-number-row" style="display: flex; gap: 0.5rem; align-items: center;">
                                        <input type="text" class="branch-phone-input" placeholder="예: 02-1234-5678" pattern="[0-9-]+" style="flex: 1; height: 2.5rem; border-radius: 0.375rem; border: 1px solid #d1d5db; background: white; padding: 0.5rem 0.75rem; font-size: 0.875rem; outline: none;">
                                        <button type="button" onclick="addPhoneNumberField()" style="display: inline-flex; align-items: center; justify-content: center; height: 2.5rem; width: 2.5rem; border-radius: 0.375rem; border: 1px solid #d1d5db; background: white; color: #1EC3B0; cursor: pointer; flex-shrink: 0;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 12h14"></path>
                                                <path d="M12 5v14"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                                <button type="button" onclick="closeAddBranchModal()" data-testid="button-cancel-add" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; white-space: nowrap; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; height: 2.5rem; padding: 0.5rem 1rem; border: 1px solid #d1d5db; background: white; color: #374151; cursor: pointer;">
                                    취소
                                </button>
                                <button type="button" onclick="submitAddBranch()" data-testid="button-confirm-add" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; white-space: nowrap; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; height: 2.5rem; padding: 0.5rem 1rem; border: none; background: #1EC3B0; color: white; cursor: pointer;">
                                    추가
                                </button>
                            </div>
                        </form>
                        <button type="button" onclick="closeAddBranchModal()" style="position: absolute; right: 1rem; top: 1rem; border-radius: 0.125rem; opacity: 0.7; background: none; border: none; cursor: pointer;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 1rem; width: 1rem;">
                                <path d="M18 6 6 18"></path>
                                <path d="m6 6 12 12"></path>
                            </svg>
                            <span style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border-width: 0;">Close</span>
                        </button>
                    </div>
                </div>
                HTML)
            );
    }
}
