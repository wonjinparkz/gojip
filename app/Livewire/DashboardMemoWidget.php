<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\DashboardMemo;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardMemoWidget extends Component
{
    public $type = 'daily';
    public $content = '';
    public $isCollapsed = false;
    public $isEditing = false;
    public $currentDate; // 대시보드에서 전달받는 날짜
    public $showCalendarPopover = false;
    public $calendarYear;
    public $calendarMonth;

    protected $listeners = [
        'memoSaved' => '$refresh',
        'memoTypeChanged' => 'handleTypeChange'
    ];

    public function mount($currentDate = null)
    {
        // Set current date
        $this->currentDate = $currentDate ?? now()->format('Y-m-d');

        // Initialize calendar to current date
        $date = Carbon::parse($this->currentDate);
        $this->calendarYear = $date->year;
        $this->calendarMonth = $date->month;

        // Load the memo for current type
        $this->loadMemo();
    }

    public function updated($propertyName)
    {
        // currentDate가 변경되면 메모 다시 로드
        if ($propertyName === 'currentDate') {
            $this->loadMemo();
        }
    }

    public function updatedType()
    {
        $this->loadMemo();
        // 타입이 변경되면 부모 컴포넌트에 알림
        $this->dispatch('memoTypeChanged', type: $this->type);
    }

    public function handleTypeChange($type)
    {
        $this->type = $type;
        $this->loadMemo();
    }

    /**
     * Get current branch ID from session
     */
    protected function getCurrentBranchId()
    {
        $user = Auth::user();
        return session('current_branch_id', $user->branches->first()?->id);
    }

    public function loadMemo()
    {
        $userId = Auth::id();
        $memo = $this->getCurrentMemo();

        if ($memo) {
            $this->content = $memo->content ?? '';
            $this->isCollapsed = $memo->is_collapsed ?? false;
        } else {
            $this->content = '';
            $this->isCollapsed = false;
        }

        $this->isEditing = false;
    }

    public function getCurrentMemo()
    {
        $userId = Auth::id();
        $date = Carbon::parse($this->currentDate);
        $branchId = $this->getCurrentBranchId();

        $query = DashboardMemo::forUser($userId);

        if ($branchId) {
            $query->forBranch($branchId);
        } else {
            $query->whereNull('branch_id');
        }

        switch ($this->type) {
            case 'daily':
                // 선택된 날짜의 일간 메모
                return $query->where('type', 'daily')
                    ->where('date', $date->toDateString())
                    ->first();
            case 'weekly':
                // 선택된 날짜가 속한 주의 주간 메모 (월요일 시작)
                $weekStart = $date->copy()->startOfWeek()->toDateString();
                return $query->where('type', 'weekly')
                    ->where('week_start', $weekStart)
                    ->first();
            case 'monthly':
                // 선택된 날짜가 속한 월의 월간 메모
                $month = $date->format('Y-m');
                return $query->where('type', 'monthly')
                    ->where('month', $month)
                    ->first();
        }

        return null;
    }

    public function saveMemo()
    {
        $userId = Auth::id();
        $date = Carbon::parse($this->currentDate);
        $branchId = $this->getCurrentBranchId();

        $attributes = [
            'user_id' => $userId,
            'branch_id' => $branchId,
            'type' => $this->type,
        ];

        switch ($this->type) {
            case 'daily':
                $attributes['date'] = $date->toDateString();
                break;
            case 'weekly':
                $attributes['week_start'] = $date->copy()->startOfWeek()->toDateString();
                break;
            case 'monthly':
                $attributes['month'] = $date->format('Y-m');
                break;
        }

        DashboardMemo::updateOrCreate(
            $attributes,
            [
                'content' => $this->content,
                'is_collapsed' => $this->isCollapsed,
            ]
        );

        $this->isEditing = false;
        $this->dispatch('memoSaved');
    }

    public function toggleCollapse()
    {
        $this->isCollapsed = !$this->isCollapsed;

        $memo = $this->getCurrentMemo();
        if ($memo) {
            $memo->update(['is_collapsed' => $this->isCollapsed]);
        }
    }

    public function startEditing()
    {
        $this->isEditing = true;
    }

    public function cancelEditing()
    {
        $this->isEditing = false;
        $this->loadMemo();
    }

    public function previousPeriod()
    {
        $date = Carbon::parse($this->currentDate);

        switch ($this->type) {
            case 'daily':
                $this->currentDate = $date->subDay()->format('Y-m-d');
                break;
            case 'weekly':
                $this->currentDate = $date->subWeek()->format('Y-m-d');
                break;
            case 'monthly':
                $this->currentDate = $date->subMonth()->format('Y-m-d');
                break;
        }

        $this->loadMemo();
    }

    public function nextPeriod()
    {
        $date = Carbon::parse($this->currentDate);

        switch ($this->type) {
            case 'daily':
                $this->currentDate = $date->addDay()->format('Y-m-d');
                break;
            case 'weekly':
                $this->currentDate = $date->addWeek()->format('Y-m-d');
                break;
            case 'monthly':
                $this->currentDate = $date->addMonth()->format('Y-m-d');
                break;
        }

        $this->loadMemo();
    }

    public function getPlaceholderProperty()
    {
        return match($this->type) {
            'daily' => '오늘의 메모를 입력해보세요.',
            'weekly' => '이번 주 메모를 입력해보세요.',
            'monthly' => '이번 달 메모를 입력해보세요.',
        };
    }

    public function getEmptyStateTextProperty()
    {
        return match($this->type) {
            'daily' => '<span class="font-bold">여기</span>를 눌러 오늘의 생각이나 계획을 메모해두세요.',
            'weekly' => '<span class="font-bold">여기</span>를 눌러 이번 주의 생각이나 계획을 메모해두세요.',
            'monthly' => '<span class="font-bold">여기</span>를 눌러 이번 달의 생각이나 계획을 메모해두세요.',
        };
    }

    public function toggleCalendar()
    {
        $this->showCalendarPopover = !$this->showCalendarPopover;
    }

    public function previousCalendarMonth()
    {
        $date = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->subMonth();
        $this->calendarYear = $date->year;
        $this->calendarMonth = $date->month;
    }

    public function nextCalendarMonth()
    {
        $date = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->addMonth();
        $this->calendarYear = $date->year;
        $this->calendarMonth = $date->month;
    }

    public function selectCalendarDate($dateStr)
    {
        $this->currentDate = $dateStr;
        $this->loadMemo();
        $this->showCalendarPopover = false;

        // Update calendar to selected date's month
        $date = Carbon::parse($dateStr);
        $this->calendarYear = $date->year;
        $this->calendarMonth = $date->month;
    }

    public function render()
    {
        return view('livewire.dashboard-memo-widget');
    }
}
