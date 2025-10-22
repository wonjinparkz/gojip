<?php

namespace App\Filament\Pages;

use App\Models\Room;
use App\Models\Branch;
use App\Models\CustomSchedule;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    public $customScheduleContent = '';
    public $customScheduleCategory = '기타';
    public $currentYear;
    public $currentMonth;
    public $selectedDate;
    public $scheduleFilter = 'all';
    public $currentDate; // 현재 보고 있는 날짜
    public $showCalendarPopover = false;

    public function mount()
    {
        $this->currentYear = now()->year;
        $this->currentMonth = now()->month;
        $this->selectedDate = now()->format('Y-m-d');
        $this->currentDate = now()->format('Y-m-d');
    }

    public function getHeading(): string
    {
        return ''; // 헤더를 비워서 커스텀 헤더만 표시
    }

    protected function getViewData(): array
    {
        $user = Auth::user();
        $currentBranchId = session('current_branch_id', $user->branches->first()?->id);

        // 현재 선택된 지점의 호실 현황
        $currentBranchStats = [
            'totalRooms' => 0,
            'occupiedRooms' => 0,
            'availableRooms' => 0,
        ];

        $availableRoomsList = [];
        $todayCheckInScheduled = [];
        $todayCheckOutScheduled = [];
        $today = $this->currentDate; // 현재 보고 있는 날짜 사용

        if ($currentBranchId) {
            $currentBranchStats = [
                'totalRooms' => Room::where('branch_id', $currentBranchId)->count(),
                'occupiedRooms' => Room::where('branch_id', $currentBranchId)
                    ->where('status', 'occupied')
                    ->count(),
                'availableRooms' => Room::where('branch_id', $currentBranchId)
                    ->where('status', 'available')
                    ->count(),
            ];

            // 빈 호실 목록 가져오기
            $availableRoomsList = Room::where('branch_id', $currentBranchId)
                ->where('status', 'available')
                ->orderBy('floor', 'asc')
                ->orderBy('room_number', 'asc')
                ->get();

            // 오늘 입실 예정 (입주일이 오늘이고 아직 입실 완료 안됨)
            $todayCheckInScheduled = Room::where('branch_id', $currentBranchId)
                ->whereDate('move_in_date', $today)
                ->whereNull('check_in_completed_at')
                ->orderBy('room_number', 'asc')
                ->get();

            // 오늘 입실 완료 (입주일이 오늘이고 입실 완료됨)
            $todayCheckInCompleted = Room::where('branch_id', $currentBranchId)
                ->whereDate('move_in_date', $today)
                ->whereNotNull('check_in_completed_at')
                ->orderBy('room_number', 'asc')
                ->get();

            // 오늘 퇴실 예정 (퇴실일이 오늘이고 아직 퇴실 완료 안됨)
            $todayCheckOutScheduled = Room::where('branch_id', $currentBranchId)
                ->whereDate('move_out_date', $today)
                ->whereNull('check_out_completed_at')
                ->orderBy('room_number', 'asc')
                ->get();

            // 오늘 퇴실 완료 (퇴실일이 오늘이고 퇴실 완료됨, 청소 시작 안됨)
            $todayCheckOutCompleted = Room::where('branch_id', $currentBranchId)
                ->whereDate('move_out_date', $today)
                ->whereNotNull('check_out_completed_at')
                ->whereNull('cleaning_status')
                ->orderBy('room_number', 'asc')
                ->get();

            // 청소 대기 (오늘 기준 청소 대기 상태)
            $cleaningWaiting = Room::where('branch_id', $currentBranchId)
                ->where('cleaning_status', 'waiting')
                ->orderBy('room_number', 'asc')
                ->get();

            // 청소 완료 (오늘 기준 청소 완료 상태)
            $cleaningCompleted = Room::where('branch_id', $currentBranchId)
                ->where('cleaning_status', 'completed')
                ->whereDate('updated_at', $today)
                ->orderBy('room_number', 'asc')
                ->get();

            // 그 외 일정 (오늘 기준 커스텀 일정)
            $customSchedules = CustomSchedule::where('branch_id', $currentBranchId)
                ->whereDate('schedule_date', $today)
                ->orderBy('created_at', 'desc')
                ->get();

            // 월간 캘린더용: 해당 월의 모든 일정 가져오기
            $firstDayOfMonth = "{$this->currentYear}-{$this->currentMonth}-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfMonth));

            // 해당 월의 입실 일정
            $monthCheckIns = Room::where('branch_id', $currentBranchId)
                ->whereYear('move_in_date', $this->currentYear)
                ->whereMonth('move_in_date', $this->currentMonth)
                ->get()
                ->groupBy(function($room) {
                    return $room->move_in_date->format('Y-m-d');
                });

            // 해당 월의 퇴실 일정
            $monthCheckOuts = Room::where('branch_id', $currentBranchId)
                ->whereYear('move_out_date', $this->currentYear)
                ->whereMonth('move_out_date', $this->currentMonth)
                ->get()
                ->groupBy(function($room) {
                    return $room->move_out_date->format('Y-m-d');
                });

            // 해당 월의 커스텀 일정
            $monthCustomSchedules = CustomSchedule::where('branch_id', $currentBranchId)
                ->whereYear('schedule_date', $this->currentYear)
                ->whereMonth('schedule_date', $this->currentMonth)
                ->get()
                ->groupBy(function($schedule) {
                    return $schedule->schedule_date->format('Y-m-d');
                });

            // 선택된 날짜의 일정
            $selectedDateSchedules = [];
            if ($this->selectedDate) {
                $selectedDateSchedules = [
                    'checkIns' => Room::where('branch_id', $currentBranchId)
                        ->whereDate('move_in_date', $this->selectedDate)
                        ->get(),
                    'checkOuts' => Room::where('branch_id', $currentBranchId)
                        ->whereDate('move_out_date', $this->selectedDate)
                        ->get(),
                    'customSchedules' => CustomSchedule::where('branch_id', $currentBranchId)
                        ->whereDate('schedule_date', $this->selectedDate)
                        ->get(),
                ];
            }
        } else {
            $customSchedules = collect();
            $monthCheckIns = collect();
            $monthCheckOuts = collect();
            $monthCustomSchedules = collect();
            $selectedDateSchedules = [];
        }

        return [
            'totalRooms' => $currentBranchStats['totalRooms'],
            'occupiedRooms' => $currentBranchStats['occupiedRooms'],
            'availableRooms' => $currentBranchStats['availableRooms'],
            'availableRoomsList' => $availableRoomsList,
            'todayCheckInScheduled' => $todayCheckInScheduled,
            'todayCheckInCompleted' => $todayCheckInCompleted,
            'todayCheckOutScheduled' => $todayCheckOutScheduled,
            'todayCheckOutCompleted' => $todayCheckOutCompleted,
            'cleaningWaiting' => $cleaningWaiting,
            'cleaningCompleted' => $cleaningCompleted,
            'customSchedules' => $customSchedules,
            'monthCheckIns' => $monthCheckIns,
            'monthCheckOuts' => $monthCheckOuts,
            'monthCustomSchedules' => $monthCustomSchedules,
            'selectedDateSchedules' => $selectedDateSchedules,
        ];
    }

    public function getWidgets(): array
    {
        return [];
    }

    public function getColumns(): int | array
    {
        return 1;
    }

    public function getView(): string
    {
        return 'filament.pages.dashboard';
    }

    // 입실 완료 처리
    public function completeCheckIn($roomId)
    {
        $room = Room::find($roomId);
        if ($room) {
            $room->update([
                'check_in_completed_at' => now(),
            ]);
        }
    }

    // 퇴실 완료 처리
    public function completeCheckOut($roomId)
    {
        $room = Room::find($roomId);
        if ($room) {
            $room->update([
                'check_out_completed_at' => now(),
            ]);
        }
    }

    // 청소 대기로 이동
    public function startCleaning($roomId)
    {
        $room = Room::find($roomId);
        if ($room) {
            $room->update([
                'cleaning_status' => 'waiting',
            ]);
        }
    }

    // 청소 완료 처리
    public function completeCleaning($roomId)
    {
        $room = Room::find($roomId);
        if ($room) {
            $room->update([
                'cleaning_status' => 'completed',
            ]);
        }
    }

    // 커스텀 일정 추가
    public function addCustomSchedule()
    {
        if (empty(trim($this->customScheduleContent))) {
            return;
        }

        $user = Auth::user();
        $currentBranchId = session('current_branch_id', $user->branches->first()?->id);

        if ($currentBranchId) {
            CustomSchedule::create([
                'branch_id' => $currentBranchId,
                'content' => $this->customScheduleContent,
                'category' => $this->customScheduleCategory,
                'schedule_date' => $this->currentDate, // 현재 보고 있는 날짜에 일정 추가
                'is_completed' => false,
            ]);

            $this->customScheduleContent = '';
            $this->customScheduleCategory = '기타'; // 기본값으로 리셋
        }
    }

    // 커스텀 일정 완료 처리
    public function completeCustomSchedule($scheduleId)
    {
        $schedule = CustomSchedule::find($scheduleId);
        if ($schedule) {
            $schedule->update([
                'is_completed' => true,
            ]);
        }
    }

    // 커스텀 일정 삭제
    public function deleteCustomSchedule($scheduleId)
    {
        $schedule = CustomSchedule::find($scheduleId);
        if ($schedule) {
            $schedule->delete();
        }
    }

    // 입실 완료 되돌리기
    public function undoCheckIn($roomId)
    {
        $room = Room::find($roomId);
        if ($room) {
            $room->update([
                'check_in_completed_at' => null,
            ]);
        }
    }

    // 청소 완료 되돌리기
    public function undoCleaning($roomId)
    {
        $room = Room::find($roomId);
        if ($room) {
            $room->update([
                'cleaning_status' => 'waiting',
            ]);
        }
    }

    // 퇴실 완료 되돌리기
    public function undoCheckOut($roomId)
    {
        $room = Room::find($roomId);
        if ($room) {
            $room->update([
                'check_out_completed_at' => null,
            ]);
        }
    }

    // 청소 대기 되돌리기
    public function undoCleaningStart($roomId)
    {
        $room = Room::find($roomId);
        if ($room) {
            $room->update([
                'cleaning_status' => null,
            ]);
        }
    }

    // 캘린더: 이전 월로 이동
    public function previousMonth()
    {
        if ($this->currentMonth == 1) {
            $this->currentMonth = 12;
            $this->currentYear--;
        } else {
            $this->currentMonth--;
        }
    }

    // 캘린더: 다음 월로 이동
    public function nextMonth()
    {
        if ($this->currentMonth == 12) {
            $this->currentMonth = 1;
            $this->currentYear++;
        } else {
            $this->currentMonth++;
        }
    }

    // 캘린더: 오늘로 이동
    public function goToToday()
    {
        $this->currentYear = now()->year;
        $this->currentMonth = now()->month;
        $this->selectedDate = now()->format('Y-m-d');
    }

    // 캘린더: 날짜 선택
    public function selectDate($date)
    {
        $this->selectedDate = $date;
    }

    // 날짜 네비게이션: 이전 날짜
    public function previousDay()
    {
        $currentDate = \Carbon\Carbon::parse($this->currentDate);
        $this->currentDate = $currentDate->subDay()->format('Y-m-d');
        $this->currentYear = $currentDate->year;
        $this->currentMonth = $currentDate->month;
    }

    // 날짜 네비게이션: 다음 날짜
    public function nextDay()
    {
        $currentDate = \Carbon\Carbon::parse($this->currentDate);
        $this->currentDate = $currentDate->addDay()->format('Y-m-d');
        $this->currentYear = $currentDate->year;
        $this->currentMonth = $currentDate->month;
    }

    // 캘린더 팝오버 토글
    public function toggleCalendar()
    {
        $this->showCalendarPopover = !$this->showCalendarPopover;
    }

    // 캘린더에서 날짜 선택
    public function selectCalendarDate($date)
    {
        $this->currentDate = $date;
        $this->selectedDate = $date;
        $selectedDate = \Carbon\Carbon::parse($date);
        $this->currentYear = $selectedDate->year;
        $this->currentMonth = $selectedDate->month;
        $this->showCalendarPopover = false;
    }
}
