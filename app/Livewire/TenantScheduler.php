<?php

namespace App\Livewire;

use App\Models\Tenant;
use App\Models\Room;
use Livewire\Component;
use Livewire\Attributes\On;
use Carbon\Carbon;

class TenantScheduler extends Component
{
    public $currentYear;
    public $currentMonth;
    public $days = [];
    public $rooms = [];
    public $tenants = [];
    public $branchId = null;

    #[On('tenant-created')]
    public function refreshData()
    {
        $this->loadData();
    }

    public function mount($branchId = null)
    {
        $this->branchId = $branchId;
        $this->currentYear = now()->year;
        $this->currentMonth = now()->month;
        $this->loadData();
    }

    public function loadData()
    {
        $this->generateCalendarDays();
        $this->loadRooms();
        $this->loadTenants();
    }

    public function generateCalendarDays()
    {
        $startDate = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $daysInMonth = $startDate->daysInMonth;

        $this->days = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($this->currentYear, $this->currentMonth, $day);
            $this->days[] = [
                'day' => $day,
                'date' => $date->format('Y-m-d'),
                'dayOfWeek' => $date->dayOfWeek, // 0 = 일요일, 6 = 토요일
                'isWeekend' => $date->isWeekend(),
            ];
        }
    }

    public function loadRooms()
    {
        \Log::info('=== loadRooms 호출 ===');

        $query = Room::with('branch')
            ->whereHas('branch', function ($query) {
                $query->where('user_id', auth()->id());
            });

        if ($this->branchId) {
            $query->where('branch_id', $this->branchId);
        }

        $rooms = $query->orderBy('room_number')->get();

        \Log::info('조회된 호실 수: ' . $rooms->count());
        foreach ($rooms as $r) {
            \Log::info("호실: {$r->room_number}, ID: {$r->id}, Branch: {$r->branch_id}");
        }

        $this->rooms = $rooms->toArray();

        \Log::info('toArray 후 rooms 데이터: ' . json_encode($this->rooms, JSON_UNESCAPED_UNICODE));
    }

    public function loadTenants()
    {
        \Log::info('=== TenantScheduler loadTenants 호출 ===');
        \Log::info('Branch ID: ' . $this->branchId);
        \Log::info('User ID: ' . auth()->id());

        $query = Tenant::with(['branch', 'room'])
            ->whereNotNull('move_in_date')
            ->whereHas('branch', function ($query) {
                $query->where('user_id', auth()->id());
            });

        if ($this->branchId) {
            $query->where('branch_id', $this->branchId);
        }

        $allTenants = $query->get();
        \Log::info('조회된 입주자 수: ' . $allTenants->count());

        foreach ($allTenants as $t) {
            \Log::info("입주자: {$t->name}, Room ID: {$t->room_id}, Move In: {$t->move_in_date}, Move Out: {$t->move_out_date}");
        }

        $this->tenants = $allTenants
            ->map(function ($tenant) {
                return [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'room_id' => $tenant->room_id,
                    'room_number' => $tenant->room_number,
                    'move_in_date' => $tenant->move_in_date?->format('Y-m-d'),
                    'move_out_date' => $tenant->move_out_date?->format('Y-m-d'),
                    'payment_status' => $tenant->payment_status,
                    'color' => match($tenant->payment_status) {
                        'paid' => '#10b981',
                        'overdue' => '#ef4444',
                        'pending' => '#f59e0b',
                        default => '#6b7280',
                    },
                ];
            })
            ->toArray();

        \Log::info('매핑된 입주자 배열 수: ' . count($this->tenants));
        \Log::info('입주자 데이터: ' . json_encode($this->tenants, JSON_UNESCAPED_UNICODE));
    }

    public function previousMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentYear = $date->year;
        $this->currentMonth = $date->month;
        $this->loadData();
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentYear = $date->year;
        $this->currentMonth = $date->month;
        $this->loadData();
    }

    public function today()
    {
        $this->currentYear = now()->year;
        $this->currentMonth = now()->month;
        $this->loadData();
    }

    public function openCreateModal($roomId, $startDate, $endDate)
    {
        $this->dispatch('open-tenant-modal',
            roomId: $roomId,
            startDate: $startDate,
            endDate: $endDate
        );
    }

    public function updateTenantDates($tenantId, $daysMoved)
    {
        $tenant = Tenant::findOrFail($tenantId);

        $newMoveInDate = Carbon::parse($tenant->move_in_date)->addDays($daysMoved);
        $newMoveOutDate = $tenant->move_out_date ? Carbon::parse($tenant->move_out_date)->addDays($daysMoved) : null;

        $tenant->update([
            'move_in_date' => $newMoveInDate,
            'move_out_date' => $newMoveOutDate,
        ]);

        if ($tenant->room) {
            $tenant->room->update([
                'move_in_date' => $newMoveInDate,
                'move_out_date' => $newMoveOutDate,
            ]);
        }

        // tenants 배열만 업데이트
        $this->loadTenants();
    }

    public function resizeTenantDates($tenantId, $resizeType, $daysChanged, $daysMovedLeft)
    {
        $tenant = Tenant::findOrFail($tenantId);

        $moveInDate = Carbon::parse($tenant->move_in_date);
        $moveOutDate = $tenant->move_out_date ? Carbon::parse($tenant->move_out_date) : null;

        if ($resizeType === 'left') {
            // 왼쪽 핸들: 시작일 변경
            $moveInDate->addDays($daysMovedLeft);
        } else if ($resizeType === 'right') {
            // 오른쪽 핸들: 종료일 변경
            if ($moveOutDate) {
                $moveOutDate->addDays($daysChanged);
            }
        }

        $tenant->update([
            'move_in_date' => $moveInDate,
            'move_out_date' => $moveOutDate,
        ]);

        if ($tenant->room) {
            $tenant->room->update([
                'move_in_date' => $moveInDate,
                'move_out_date' => $moveOutDate,
            ]);
        }

        // tenants 배열만 업데이트
        $this->loadTenants();
    }

    public function editTenant($tenantId)
    {
        return redirect()->route('filament.admin.resources.tenants.edit', ['record' => $tenantId]);
    }

    public function render()
    {
        return view('livewire.tenant-scheduler');
    }
}
