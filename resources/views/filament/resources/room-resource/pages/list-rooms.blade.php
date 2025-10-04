<x-filament-panels::page>
    <!-- Branch Tabs -->
    <div style="margin-bottom: 1.5rem; border-bottom: 1px solid #e5e7eb;">
        <div style="display: flex; gap: 1rem; overflow-x: auto;">
            @foreach($this->getBranches() as $branch)
                <button
                    wire:click="selectBranch({{ $branch->id }})"
                    type="button"
                    style="padding: 0.75rem 1rem; border: none; background: none; cursor: pointer; font-weight: 500; white-space: nowrap; border-bottom: 2px solid {{ $selectedBranchId === $branch->id ? '#f59e0b' : 'transparent' }}; color: {{ $selectedBranchId === $branch->id ? '#f59e0b' : '#6b7280' }}; transition: all 0.2s;"
                >
                    {{ $branch->name }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="room-grid" style="display: grid; gap: 1rem;">
        @foreach($this->getRooms() as $room)
            <a
                href="{{ route('filament.admin.resources.rooms.edit', ['record' => $room->id]) }}"
                style="background-color: white; border-radius: 0.5rem; border: 1px solid #e5e7eb; padding: 1rem; cursor: pointer; transition: box-shadow 0.2s; text-decoration: none; color: inherit; display: block;"
                onmouseover="this.style.boxShadow='0 4px 6px -1px rgb(0 0 0 / 0.1)'"
                onmouseout="this.style.boxShadow='none'"
            >
                <!-- Card Header -->
                <div style="margin-bottom: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <h3 style="font-size: 1.125rem; font-weight: 700; color: #111827;">{{ $room->room_number }}호</h3>
                        @if($room->tenant_name)
                            <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background-color: #42C1BF; color: white;">
                                사용중
                            </span>
                        @else
                            <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background-color: #9CA3AF; color: white;">
                                공실
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Card Body -->
                <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.875rem;">
                    <!-- 유형 -->
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #4b5563;">유형</span>
                        <span style="font-weight: 500; color: #111827;">{{ $room->room_type }}</span>
                    </div>

                    <!-- 월세 -->
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #4b5563;">월세</span>
                        <span style="font-weight: 500; color: #111827;">₩{{ number_format($room->monthly_rent) }}</span>
                    </div>

                    <!-- 보증금 -->
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #4b5563;">보증금</span>
                        <span style="font-weight: 500; color: #111827;">₩{{ number_format($room->deposit) }}</span>
                    </div>

                    <!-- Divider -->
                    <div style="border-top: 1px solid #f3f4f6; margin: 0.5rem 0;"></div>

                    <!-- 입주자 -->
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #4b5563;">입주자</span>
                        <span style="color: #111827;">{{ $room->tenant_name ?? '-' }}</span>
                    </div>

                    <!-- 입주일 -->
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #4b5563;">입주일</span>
                        <span style="color: #111827;">{{ $room->move_in_date ? $room->move_in_date->format('Y.m.d') : '-' }}</span>
                    </div>

                    <!-- 퇴실일 -->
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #4b5563;">퇴실일</span>
                        <span style="color: #111827;">{{ $room->move_out_date ? $room->move_out_date->format('Y.m.d') : '-' }}</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Pagination -->
    <div style="margin-top: 1.5rem;">
        {{ $this->getRooms()->links() }}
    </div>

    <style>
        .room-grid {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
        @media (min-width: 768px) {
            .room-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (min-width: 1280px) {
            .room-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }
    </style>
</x-filament-panels::page>
