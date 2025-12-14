<div>
    @if($show)
    <style>
        @media (max-width: 768px) {
            .all-tenants-modal-container {
                max-width: calc(100vw - 32px) !important;
                margin: 16px !important;
            }
            .all-tenants-modal-header {
                padding: 16px !important;
            }
            .all-tenants-modal-header h2 {
                font-size: 18px !important;
            }
            .all-tenants-modal-content {
                padding: 16px !important;
            }
            .all-tenants-modal-footer {
                flex-direction: column !important;
                gap: 8px !important;
            }
            .all-tenants-modal-footer button {
                width: 100%;
            }
        }
    </style>

    <!-- Modal Backdrop -->
    <div style="position: fixed; inset: 0; z-index: 9999; background-color: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; padding: 16px; overflow-y: auto;">
        <!-- Modal Container -->
        <div class="all-tenants-modal-container" style="position: relative; z-index: 10000; width: 100%; max-width: 900px; max-height: calc(100vh - 32px); background-color: white; border-radius: 12px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; display: flex; flex-direction: column;">

            <!-- Close Button -->
            <button
                wire:click="close"
                type="button"
                style="position: absolute; right: 16px; top: 16px; padding: 4px; background: none; border: none; cursor: pointer; color: #6b7280; transition: color 0.2s; z-index: 10;"
                onmouseover="this.style.color='#111827'"
                onmouseout="this.style.color='#6b7280'">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>

            <!-- Modal Header -->
            <div class="all-tenants-modal-header" style="padding: 24px; border-bottom: 1px solid #e5e7eb;">
                <h2 style="font-size: 20px; font-weight: 600; color: #111827; margin: 0; padding-right: 40px;">전체 입실자 정보</h2>
                <p style="font-size: 14px; color: #6b7280; margin-top: 8px;">모든 입실자의 정보를 확인할 수 있습니다.</p>
            </div>

            <!-- Modal Content (Scrollable) -->
            <div class="all-tenants-modal-content" style="overflow-x: auto; overflow-y: auto; flex: 1; padding: 24px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px; min-width: 700px;">
                    <thead>
                        <tr style="border-bottom: 1px solid #e5e7eb; background-color: #f9fafb;">
                            <th style="padding: 16px 20px; text-align: left; font-weight: 600; color: #374151; white-space: nowrap;">방 번호</th>
                            <th style="padding: 16px 20px; text-align: left; font-weight: 600; color: #374151; white-space: nowrap;">이름</th>
                            <th style="padding: 16px 20px; text-align: left; font-weight: 600; color: #374151; white-space: nowrap;">연락처</th>
                            <th style="padding: 16px 20px; text-align: left; font-weight: 600; color: #374151; white-space: nowrap;">입주일</th>
                            <th style="padding: 16px 20px; text-align: left; font-weight: 600; color: #374151; white-space: nowrap;">방 유형</th>
                            <th style="padding: 16px 20px; text-align: left; font-weight: 600; color: #374151; white-space: nowrap;">월세</th>
                            <th style="padding: 16px 20px; text-align: left; font-weight: 600; color: #374151; white-space: nowrap;">상태</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rooms as $room)
                            @php
                                $tenant = $room['tenant'] ?? null;
                                $isOccupied = $tenant !== null;
                            @endphp
                            <tr style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s;"
                                onmouseover="this.style.backgroundColor='#f9fafb'"
                                onmouseout="this.style.backgroundColor='white'">
                                <td style="padding: 20px; font-weight: 600; color: #111827;">{{ $room['room_number'] }}호</td>
                                <td style="padding: 20px; color: #374151;">{{ $isOccupied ? $tenant['name'] : '-' }}</td>
                                <td style="padding: 20px; color: #374151;">{{ $isOccupied && $tenant['phone'] ? $tenant['phone'] : '-' }}</td>
                                <td style="padding: 20px; color: #374151;">{{ $isOccupied && $tenant['move_in_date'] ? \Carbon\Carbon::parse($tenant['move_in_date'])->format('Y.m.d') : '-' }}</td>
                                <td style="padding: 20px; color: #374151;">{{ $room['room_type'] ?? '스탠다드룸' }}</td>
                                <td style="padding: 20px; color: #374151; white-space: nowrap;">{{ number_format($room['monthly_rent']) }}원</td>
                                <td style="padding: 20px;">
                                    @if($isOccupied)
                                        <div style="display: inline-flex; align-items: center; padding: 6px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; background-color: #d1fae5; color: #065f46; white-space: nowrap;">
                                            입주중
                                        </div>
                                    @else
                                        <div style="display: inline-flex; align-items: center; padding: 6px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; background-color: #f3f4f6; color: #374151; white-space: nowrap;">
                                            공실
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 40px; text-align: center; color: #9ca3af;">
                                    등록된 호실이 없습니다.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Modal Footer -->
            <div class="all-tenants-modal-footer" style="padding: 20px 24px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                <button
                    wire:click="goToTenantsPage"
                    type="button"
                    style="padding: 12px 24px; background-color: #2dd4bf; color: white; border: none; border-radius: 9999px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background-color 0.2s; white-space: nowrap;"
                    onmouseover="this.style.backgroundColor='#14b8a6'"
                    onmouseout="this.style.backgroundColor='#2dd4bf'">
                    입주자 관리 페이지로 이동
                </button>
                <button
                    wire:click="close"
                    type="button"
                    style="padding: 12px 24px; background-color: white; color: #111827; border: 1px solid #d1d5db; border-radius: 9999px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background-color 0.2s; white-space: nowrap;"
                    onmouseover="this.style.backgroundColor='#f3f4f6'"
                    onmouseout="this.style.backgroundColor='white'">
                    닫기
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
