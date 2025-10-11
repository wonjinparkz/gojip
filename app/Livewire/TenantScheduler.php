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
    public $startDate; // 시작 날짜 (YYYY-MM-DD)
    public $endDate;   // 종료 날짜 (YYYY-MM-DD)
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
        // 세션에서 선택된 지점 가져오기
        $this->branchId = session('current_branch_id', $branchId);
        $this->currentYear = now()->year;
        $this->currentMonth = now()->month;

        // 초기 범위: 이전달 1일 ~ 다음달 말일 (3개월)
        $previousMonth = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $nextMonth = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();

        $this->startDate = $previousMonth->format('Y-m-d');
        $this->endDate = $nextMonth->endOfMonth()->format('Y-m-d');

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
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        $today = now()->format('Y-m-d');

        $this->days = [];

        $current = $start->copy();
        while ($current->lte($end)) {
            $this->days[] = [
                'day' => $current->day,
                'date' => $current->format('Y-m-d'),
                'dayOfWeek' => $current->dayOfWeek,
                'isWeekend' => $current->isWeekend(),
                'isToday' => $current->format('Y-m-d') === $today,
                'month' => $current->format('n'),
            ];
            $current->addDay();
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

    public function moveTenantToRoom($tenantId, $newRoomId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $newRoom = Room::findOrFail($newRoomId);

        // 같은 호실이면 아무것도 하지 않음
        if ($tenant->room_id == $newRoomId) {
            return;
        }

        // 입주자의 호실 변경
        $tenant->update([
            'room_id' => $newRoomId,
            'room_number' => $newRoom->room_number,
        ]);

        // 데이터 새로고침
        $this->loadData();
    }

    public function loadMorePrevious()
    {
        // 현재 시작일에서 1개월 이전 추가
        $currentStart = Carbon::parse($this->startDate);
        $newStart = $currentStart->copy()->subMonth()->startOfMonth();

        $this->startDate = $newStart->format('Y-m-d');
        $this->loadData();

        return [
            'scrollTarget' => $currentStart->format('Y-m-d'), // 이전 시작일로 스크롤
        ];
    }

    public function loadMoreNext()
    {
        // 현재 종료일에서 1개월 이후 추가
        $currentEnd = Carbon::parse($this->endDate);
        $newEnd = $currentEnd->copy()->addMonth()->endOfMonth();

        $this->endDate = $newEnd->format('Y-m-d');
        $this->loadData();

        return [
            'scrollTarget' => $currentEnd->format('Y-m-d'), // 이전 종료일로 스크롤
        ];
    }

    public function render()
    {
        return view('livewire.tenant-scheduler');
    }
}
