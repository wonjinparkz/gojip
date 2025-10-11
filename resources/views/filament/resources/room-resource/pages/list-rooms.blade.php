<x-filament-panels::page>
    <!-- Filters -->
    <div style="background-color: #f8f8f8; border-radius: 1rem 1rem 0 0; box-shadow: none; border: none;">
        <div style="padding: 0.75rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;">
                <div style="display: flex; flex-wrap: nowrap; gap: 0.5rem; overflow-x: auto;">
                    <!-- 층수 필터 -->
                    <select wire:model.live="filterFloor"
                            style="display: flex; align-items: center; justify-content: space-between; border: 1px solid #e5e7eb; background: white; padding: 0.5rem 0.75rem; border-radius: 0.75rem; white-space: nowrap; font-size: 0.75rem; height: 2rem; min-width: 5rem; cursor: pointer;">
                        <option value="">전체 층</option>
                        @foreach($this->getAvailableFloors() as $floor)
                            <option value="{{ $floor }}">{{ $floor }}층</option>
                        @endforeach
                    </select>

                    <!-- 방 유형 필터 -->
                    <select wire:model.live="filterRoomType"
                            style="display: flex; align-items: center; justify-content: space-between; border: 1px solid #e5e7eb; background: white; padding: 0.5rem 0.75rem; border-radius: 0.75rem; white-space: nowrap; font-size: 0.75rem; height: 2rem; min-width: 7rem; cursor: pointer;">
                        <option value="">전체 방 유형</option>
                        @foreach($this->getAvailableRoomTypes() as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>

                    <!-- 상태 필터 -->
                    <select wire:model.live="filterStatus"
                            style="display: flex; align-items: center; justify-content: space-between; border: 1px solid #e5e7eb; background: white; padding: 0.5rem 0.75rem; border-radius: 0.75rem; white-space: nowrap; font-size: 0.75rem; height: 2rem; min-width: 6rem; cursor: pointer;">
                        <option value="">전체 상태</option>
                        <option value="available">입주가능</option>
                        <option value="occupied">입주중</option>
                        <option value="maintenance">수리중</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Divider -->
    <div style="border-top: 1px solid #e5e7eb;"></div>

    <!-- Room Cards Container -->
    <div class="room-cards-container" style="background-color: #f8f8f8; border-radius: 0 0 1rem 1rem; box-shadow: none; border: none; padding: 0.75rem; margin-bottom: 1rem; margin-top: 0;">
        <div class="room-grid" style="display: grid; gap: 1rem;">
        @foreach($this->getRooms() as $room)
            <div
                wire:click="viewRoom({{ $room->id }})"
                style="background-color: white; border-radius: 0.5rem; border: 1px solid #e5e7eb; cursor: pointer; transition: box-shadow 0.2s;"
                onmouseover="this.style.boxShadow='0 4px 6px -1px rgb(0 0 0 / 0.1)'"
                onmouseout="this.style.boxShadow='none'"
                onclick="console.log('Card clicked, Room ID: {{ $room->id }}');
                         console.log('Livewire component:', @this);
                         @this.call('viewRoom', {{ $room->id }});"
            >
                <div style="padding: 0.75rem; max-height: 200px; overflow-y: auto;">
                    <!-- Card Header -->
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                        <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 0.25rem;">
                            <span style="font-weight: 500; color: #1f2937; font-size: 0.875rem;">{{ $room->room_number }}호</span>
                            @if($room->tenant_name)
                                <div style="display: inline-flex; align-items: center; border-radius: 9999px; border: 1px solid #40c0c0; font-weight: 600; margin-left: 0.25rem; padding: 0 0.25rem; height: 1rem; font-size: 10px; background-color: #40c0c0; color: white;">
                                    사용중
                                </div>
                            @else
                                <div style="display: inline-flex; align-items: center; border-radius: 9999px; border: 1px solid #9ca3af; font-weight: 600; margin-left: 0.25rem; padding: 0 0.25rem; height: 1rem; font-size: 10px; background-color: #9ca3af; color: white;">
                                    공실
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.25rem; font-size: 0.75rem;">
                        <div style="color: #6b7280;">유형</div>
                        <div style="text-align: right;">{{ $room->room_type }}</div>

                        <div style="color: #6b7280;">월세</div>
                        <div style="text-align: right; font-weight: 500;">₩{{ number_format($room->monthly_rent) }}</div>

                        <div style="color: #6b7280;">보증금</div>
                        <div style="text-align: right; font-weight: 500;">₩{{ number_format($room->deposit) }}</div>
                    </div>

                    <!-- Divider and Tenant Info -->
                    <div style="margin-top: 0.75rem; padding-top: 0.25rem; position: relative;">
                        <div style="position: absolute; top: -0.25rem; left: 0; right: 0; height: 1px; background-color: #e5e7eb;"></div>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.25rem; font-size: 0.75rem;">
                            <div style="color: #6b7280;">입주자</div>
                            <div style="text-align: right;">{{ $room->tenant_name ?? '-' }}</div>

                            <div style="color: #6b7280;">입실일</div>
                            <div style="text-align: right;">{{ $room->move_in_date ? $room->move_in_date->format('Y-m-d') : '-' }}</div>

                            <div style="color: #6b7280;">퇴실일</div>
                            <div style="text-align: right;">{{ $room->move_out_date ? $room->move_out_date->format('Y-m-d') : '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        </div>

        <!-- Pagination -->
        <div style="margin-top: 1.5rem;">
            {{ $this->getRooms()->links() }}
        </div>
    </div>

    <!-- View Modal -->
    <div x-data="{ isOpen: @entangle('showViewModal').live }"
         x-show="isOpen"
         x-cloak
         style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 50;"
         @click.self="$wire.closeModal()">

        <!-- Overlay Background -->
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.5);"
             @click="$wire.closeModal()"></div>

        <!-- Modal Container with Centering -->
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center; padding: 1rem; pointer-events: none;">

            <!-- Modal Content -->
            <div style="position: relative; background: white; border-radius: 1rem; max-width: 56rem; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); pointer-events: auto;">
            @if($selectedRoom)
                <!-- Modal Header -->
                <div style="padding: 1rem 1rem 0 1rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <h2 style="font-size: 1.3rem; font-weight: 600; color: #111827;">
                            {{ $selectedRoom->room_number }}호 상세정보
                        </h2>
                        <button wire:click="closeModal" style="color: #6b7280; hover:color: #111827; background: none; border: none; font-size: 1.5rem; cursor: pointer; padding: 0.25rem;">
                            ×
                        </button>
                    </div>
                </div>

                <!-- Modal Content -->
                @include('filament.resources.room-resource.modals.view-room', ['record' => $selectedRoom])

                <!-- Modal Footer -->
                <div style="padding: 0 1rem 1rem 1rem; display: flex; justify-content: flex-end; gap: 0.75rem;" x-data="{ editMode: @entangle('editMode').live }">
                    <!-- View Mode Buttons -->
                    <template x-if="!editMode">
                        <div style="display: flex; gap: 0.75rem;">
                            <button wire:click="closeModal"
                                    style="padding: 0.5rem 1rem; border-radius: 0.5rem; border: 1px solid #d1d5db; background: white; color: #374151; font-weight: 500; cursor: pointer;">
                                닫기
                            </button>
                            <button wire:click="openEditModal()"
                                    style="padding: 0.5rem 1rem; border-radius: 0.5rem; border: none; background: #1EC3B0; color: white; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                수정하기
                            </button>
                        </div>
                    </template>

                    <!-- Edit Mode Buttons -->
                    <template x-if="editMode">
                        <div style="display: flex; gap: 0.75rem;">
                            <button wire:click="cancelEdit"
                                    style="padding: 0.5rem 1rem; border-radius: 0.5rem; border: 1px solid #d1d5db; background: white; color: #374151; font-weight: 500; cursor: pointer;">
                                취소
                            </button>
                            <button wire:click="saveRoom"
                                    style="padding: 0.5rem 1rem; border-radius: 0.5rem; border: none; background: #1EC3B0; color: white; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                저장
                            </button>
                        </div>
                    </template>
                </div>
            @endif
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    @if($showEditModal && $selectedRoom)
    <x-filament::modal
        id="edit-room-modal"
        width="4xl"
        :close-by-clicking-away="false"
        x-data="{ isOpen: @entangle('showEditModal') }"
        x-show="isOpen"
    >
            <x-slot name="heading">
                {{ $selectedRoom->room_number }}호 수정
            </x-slot>

            <form wire:submit.prevent="saveRoom">
                <div class="space-y-6">
                    {{ $this->form }}
                </div>
            </form>

            <x-slot name="footerActions">
                <x-filament::button
                    color="gray"
                    wire:click="closeModal"
                >
                    취소
                </x-filament::button>

                <x-filament::button
                    type="submit"
                    color="primary"
                    wire:click="saveRoom"
                >
                    저장
                </x-filament::button>
            </x-slot>
    </x-filament::modal>
    @endif

    <style>
        .room-grid {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
        @media (min-width: 768px) {
            .room-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .room-cards-container {
                padding: 1rem !important;
            }
        }
        @media (min-width: 1280px) {
            .room-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }
        .fi-page-content {
            row-gap:0!important;
        }
    </style>
</x-filament-panels::page>
