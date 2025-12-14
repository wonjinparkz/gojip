<?php

namespace App\Livewire;

use App\Models\Tenant;
use App\Models\Room;
use Livewire\Component;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class RoomAssignModal extends Component
{
    public bool $show = false;
    public ?int $tenantId = null;
    public ?int $selectedRoomId = null;
    public array $availableRooms = [];
    public $tenant = null;
    public bool $hasDates = false;

    #[On('open-room-assign-modal')]
    public function open($tenantId)
    {
        $this->tenantId = $tenantId;
        $this->tenant = Tenant::findOrFail($tenantId);
        $this->selectedRoomId = null;

        // 입실일/퇴실일 존재 여부 확인
        $this->hasDates = $this->tenant->move_in_date !== null;

        // 가능한 호실 로드
        $this->loadAvailableRooms();

        $this->show = true;
    }

    public function loadAvailableRooms()
    {
        $branchId = session('current_branch_id');

        $rooms = Room::where('branch_id', $branchId)
            ->with('tenant') // 현재 입주자 정보 로드
            ->orderBy('room_number', 'asc')
            ->get();

        // 현재 입주자가 없는 호실만 필터링
        $vacantRooms = $rooms->filter(function ($room) {
            return $room->tenant === null;
        });

        // 날짜가 있는 경우 추가 겹침 체크
        if ($this->hasDates && $this->tenant->move_in_date) {
            $moveInDate = Carbon::parse($this->tenant->move_in_date);
            $moveOutDate = $this->tenant->move_out_date ? Carbon::parse($this->tenant->move_out_date) : null;

            $this->availableRooms = $vacantRooms->filter(function ($room) use ($moveInDate, $moveOutDate) {
                // 해당 호실에 날짜가 겹치는 입주자가 있는지 확인 (미래 입주자)
                $hasOverlap = Tenant::where('room_id', $room->id)
                    ->where('id', '!=', $this->tenantId)
                    ->where(function ($query) use ($moveInDate, $moveOutDate) {
                        if ($moveOutDate) {
                            $query->where(function ($q) use ($moveInDate, $moveOutDate) {
                                $q->whereBetween('move_in_date', [$moveInDate, $moveOutDate])
                                  ->orWhereBetween('move_out_date', [$moveInDate, $moveOutDate])
                                  ->orWhere(function ($q2) use ($moveInDate, $moveOutDate) {
                                      $q2->where('move_in_date', '<=', $moveInDate)
                                         ->where(function ($q3) use ($moveOutDate) {
                                             $q3->whereNull('move_out_date')
                                                ->orWhere('move_out_date', '>=', $moveOutDate);
                                         });
                                  });
                            });
                        } else {
                            $query->where(function ($q) use ($moveInDate) {
                                $q->whereNull('move_out_date')
                                  ->orWhere('move_out_date', '>=', $moveInDate);
                            });
                        }
                    })
                    ->exists();

                return !$hasOverlap;
            })->values()->toArray();
        } else {
            // 날짜가 없으면 공실인 호실만 표시
            $this->availableRooms = $vacantRooms->values()->toArray();
        }
    }

    public function assignToRoom()
    {
        if (!$this->selectedRoomId) {
            Notification::make()
                ->danger()
                ->title('호실을 선택해주세요')
                ->send();
            return;
        }

        $room = Room::findOrFail($this->selectedRoomId);
        $tenant = Tenant::findOrFail($this->tenantId);

        // 날짜가 없는 경우: 오늘 날짜 + 퇴실일 미정
        if (!$this->hasDates) {
            $tenant->update([
                'room_id' => $this->selectedRoomId,
                'room_number' => $room->room_number,
                'room_type' => $room->room_type,
                'monthly_rent' => $room->monthly_rent ?? 0,
                'move_in_date' => now()->format('Y-m-d'),
                'move_out_date' => null,
                'indefinite_move_out' => true,
                'payment_status' => 'pending',
                'status' => 'active',
            ]);
        } else {
            // 날짜가 있는 경우: 기존 날짜 유지
            $tenant->update([
                'room_id' => $this->selectedRoomId,
                'room_number' => $room->room_number,
                'room_type' => $room->room_type,
                'monthly_rent' => $room->monthly_rent ?? 0,
                'status' => 'active',
            ]);
        }

        // 방 상태 업데이트
        $room->update([
            'status' => 'occupied',
            'tenant_name' => $tenant->name,
            'move_in_date' => $tenant->move_in_date,
            'move_out_date' => $tenant->move_out_date,
        ]);

        Notification::make()
            ->success()
            ->title('호실이 배정되었습니다')
            ->send();

        // 페이지 새로고침
        $this->dispatch('tenant-created');
        $this->js('
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent("refresh-tenants"));
                $wire.close();
            }, 100);
        ');
    }

    public function close()
    {
        $this->show = false;
        $this->reset(['tenantId', 'selectedRoomId', 'availableRooms', 'tenant', 'hasDates']);
    }

    public function render()
    {
        return view('livewire.room-assign-modal');
    }
}
