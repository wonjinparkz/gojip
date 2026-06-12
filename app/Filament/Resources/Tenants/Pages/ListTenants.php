<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Models\Branch;
use App\Models\Tenant;
use App\Models\Room;
use App\Exports\ScheduleExport;
use App\Exports\ScheduleTemplateExport;
use App\Imports\ScheduleImport;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\Components\Tab;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\Attributes\On;

class ListTenants extends Page
{
    protected static string $resource = TenantResource::class;

    protected string $view = 'filament.resources.tenants.pages.list-tenants';

    public $activeTab = 'schedule';
    public $roomFilter = 'all'; // all, occupied, vacant
    public $waitingFilter = 'all'; // all, short_term, long_term
    public $selectedWaitingTenantId = null;
    
    // 모바일 배정용 선택 상태
    public $selectedScheduleRoomId = null;
    public $selectedScheduleDate = null;

    public function mount(): void
    {
        // 세션에 current_branch_id가 없으면 사용자의 첫 번째 지점을 자동 설정
        if (!session('current_branch_id')) {
            $firstBranch = Branch::where('user_id', auth()->id())->first();

            if ($firstBranch) {
                session(['current_branch_id' => $firstBranch->id]);
            }
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function setRoomFilter($filter)
    {
        $this->roomFilter = $filter;
    }

    public function setWaitingFilter($filter)
    {
        $this->waitingFilter = $filter;
    }

    public function openCreateModal($roomId, $tenantId, $startDate = null, $endDate = null)
    {
        // 호실에 현재 입주자가 있는지 확인
        $room = Room::with('tenant')->find($roomId);

        if (!$room) {
            Notification::make()
                ->danger()
                ->title('호실을 찾을 수 없습니다')
                ->send();
            return;
        }

        // 날짜가 전달되지 않은 경우 기본값 사용
        if (!$startDate) {
            $startDate = now()->format('Y-m-d');
        }
        if (!$endDate) {
            $endDate = now()->addMonth()->format('Y-m-d');
        }

        // 현재 입주자가 있는 경우, 입실일이 현재 입주자의 퇴실일 이후인지 확인
        if ($room->tenant !== null) {
            $currentTenant = $room->tenant;
            $requestedMoveInDate = \Carbon\Carbon::parse($startDate);

            // 현재 입주자에게 퇴실일이 없거나, 입실일이 퇴실일 이전이면 배정 불가
            if (!$currentTenant->move_out_date || $requestedMoveInDate->lte($currentTenant->move_out_date)) {
                Notification::make()
                    ->danger()
                    ->title('배정 불가')
                    ->body('해당 호실에는 이미 입주자가 배정되어 있습니다.')
                    ->persistent()
                    ->send();
                return;
            }
            // 입실일이 퇴실일 이후면 미래 입주자로 허용
        }

        $this->dispatch('open-tenant-modal',
            roomId: $roomId,
            startDate: $startDate,
            endDate: $endDate,
            tenantId: $tenantId,
            datesReadOnly: false
        );
    }

    /**
     * 미래 입주자 추가 모달 열기 (날짜 수정 가능, 최소 날짜 제약)
     */
    public function openFutureTenantModal($roomId, $tenantId = null, $assignDirectly = false)
    {
        $room = Room::find($roomId);

        if (!$room) {
            Notification::make()
                ->title('호실을 찾을 수 없습니다')
                ->danger()
                ->send();
            return;
        }

        // 현재 입주 중인 입주자 조회 (getRooms()와 동일한 조건)
        $currentTenant = Tenant::where('room_id', $roomId)
            ->where(function($q) {
                $q->whereNull('move_out_date')
                  ->orWhere('indefinite_move_out', true)
                  ->orWhereDate('move_out_date', '>=', now());
            })
            ->whereDate('move_in_date', '<=', now())
            ->first();

        // 미래 입주자들 조회
        $futureTenants = Tenant::where('room_id', $roomId)
            ->whereDate('move_in_date', '>', now())
            ->orderBy('move_in_date', 'asc')
            ->get();

        // 가장 마지막 입주자(현재 또는 미래)의 퇴실일을 기준으로 다음 입실일 설정
        $lastTenant = null;
        if ($futureTenants->isNotEmpty()) {
            // 미래 입주자가 있으면 가장 마지막 미래 입주자
            $lastTenant = $futureTenants->last();
        } elseif ($currentTenant) {
            // 미래 입주자가 없으면 현재 입주자
            $lastTenant = $currentTenant;
        }

        if ($lastTenant && $lastTenant->move_out_date) {
            $startDate = $lastTenant->move_out_date->copy()->addDay()->format('Y-m-d');
            $minMoveInDate = $startDate; // 퇴실일 다음날부터 선택 가능
        } else {
            $startDate = now()->format('Y-m-d');
            $minMoveInDate = now()->format('Y-m-d');
        }

        $endDate = now()->addMonth()->format('Y-m-d');

        // 퇴실일 미정이면 바로 배정
        if ($assignDirectly && $tenantId) {
            $tenant = Tenant::find($tenantId);
            if ($tenant && $tenant->indefinite_move_out) {
                $this->assignTenantDirectly($roomId, $tenantId, $startDate, $endDate);
                return;
            }
        }

        $this->dispatch('open-tenant-modal',
            roomId: $roomId,
            startDate: $startDate,
            endDate: $endDate,
            tenantId: $tenantId,
            datesReadOnly: false,
            minMoveInDate: $minMoveInDate
        );
    }

    /**
     * 입주자 수정 모달 열기
     */
    public function editTenant($tenantId)
    {
        $this->dispatch('open-tenant-edit-modal', tenantId: $tenantId);
    }

    /**
     * 대기자 수정 모달 열기 — '입주자 정보 수정' 디자인(TenantManagementModal)으로 통일
     */
    public function editWaitingTenant($tenantId)
    {
        $this->dispatch('edit-tenant-management', tenantId: $tenantId);
    }

    /**
     * 호실 배정 모달 열기
     */
    public function openRoomAssignModal($tenantId)
    {
        $this->dispatch('open-room-assign-modal', tenantId: $tenantId);
    }

    /**
     * 대기자 카드 선택/해제
     */
    public function selectWaitingTenant($tenantId, $targetTab = null)
    {
        if ($this->selectedWaitingTenantId === $tenantId) {
            // 이미 선택된 경우 해제
            $this->selectedWaitingTenantId = null;
        } else {
            // 새로 선택
            $this->selectedWaitingTenantId = $tenantId;
            
            // 모바일 배정 모드인지 확인 (호실과 날짜가 선택된 상태)
            if ($this->selectedScheduleRoomId && $this->selectedScheduleDate) {
                // 호실 배정 확인 준비
                $this->prepareAssignmentConfirmation();
            } else {
                // targetTab이 지정된 경우 해당 탭으로 전환 (또는 유지)
                if ($targetTab === 'schedule') {
                    $this->activeTab = 'schedule';
                    
                    // 모바일에서 대기자 선택 후 날짜 선택을 유도하기 위한 알림
                    Notification::make()
                        ->title('배정할 날짜를 선택해주세요')
                        ->info()
                        ->duration(3000)
                        ->send();
                } else {
                    // 기본 동작: 호실 현황 탭으로 자동 전환
                    $this->activeTab = 'rooms';
                }
            }
        }
    }
    
    /**
     * 모바일 스케줄러 셀 클릭 핸들러
     */
    public function handleScheduleCellClicked($roomId, $date)
    {
        $this->selectedScheduleRoomId = $roomId;
        $this->selectedScheduleDate = $date;
        
        // 대기자가 이미 선택된 상태라면 바로 배정 확인
        if ($this->selectedWaitingTenantId) {
            $this->prepareAssignmentConfirmation();
        }
    }
    
    /**
     * 모바일 배정 확인 준비
     */
    public function prepareAssignmentConfirmation()
    {
        if (!$this->selectedScheduleRoomId || !$this->selectedWaitingTenantId) {
            return;
        }
        
        $this->pendingAssignmentRoomId = $this->selectedScheduleRoomId;
        $this->showAssignmentConfirmation = true;
    }

    /**
     * 특정 호실이 선택된 대기자에게 입주 가능한지 확인
     */
    public function isRoomAvailableForSelectedTenant($roomId)
    {
        if (!$this->selectedWaitingTenantId) {
            return false;
        }

        // 호실 정보 로드
        $room = Room::with('tenant')->find($roomId);
        if (!$room) {
            return false;
        }

        // 현재 입주자가 있는 호실은 제외
        if ($room->tenant !== null) {
            return false;
        }

        $tenant = Tenant::find($this->selectedWaitingTenantId);
        if (!$tenant) {
            return false;
        }

        // 날짜가 없으면 공실인 호실은 모두 가능
        if (!$tenant->move_in_date) {
            return true;
        }

        $moveInDate = \Carbon\Carbon::parse($tenant->move_in_date);
        $moveOutDate = $tenant->move_out_date ? \Carbon\Carbon::parse($tenant->move_out_date) : null;

        // 해당 호실에 날짜가 겹치는 입주자가 있는지 확인 (미래 입주자 포함)
        $hasOverlap = Tenant::where('room_id', $roomId)
            ->where('id', '!=', $this->selectedWaitingTenantId)
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
    }

    /**
     * 입주자를 호실에 바로 배정 (퇴실일 미정인 경우)
     */
    public function handleRoomClick($roomId)
    {
        // 1. 대기자가 선택되어 있는지 확인
        if (!$this->selectedWaitingTenantId) {
            return;
        }

        // 2. 해당 호실이 배정 가능한지 확인 (기존 로직 재사용)
        if (!$this->isRoomAvailableForSelectedTenant($roomId)) {
            Notification::make()
                ->danger()
                ->title('배정 불가')
                ->body('해당 호실에는 이미 입주자가 배정되어 있거나 입실 조건이 맞지 않습니다.')
                ->send();
            return;
        }

        // 3. 배정 확인 모달 표시
        $this->pendingAssignmentRoomId = $roomId;
        $this->showAssignmentConfirmation = true;
    }

    public function processPendingAssignment()
    {
        if (!$this->pendingAssignmentRoomId || !$this->selectedWaitingTenantId) {
            $this->cancelPendingAssignment();
            return;
        }

        $tenant = Tenant::find($this->selectedWaitingTenantId);
        $room = Room::find($this->pendingAssignmentRoomId);

        if (!$tenant || !$room) {
             Notification::make()
                ->danger()
                ->title('오류 발생')
                ->body('입주자 또는 호실 정보를 찾을 수 없습니다.')
                ->send();
            $this->cancelPendingAssignment();
            return;
        }

        // 입실일과 퇴실일 결정
        // 모바일 배정의 경우 선택된 날짜를 우선 사용
        $startDate = ($this->selectedScheduleDate) 
            ? $this->selectedScheduleDate 
            : ($tenant->move_in_date ? $tenant->move_in_date->format('Y-m-d') : now()->format('Y-m-d'));
        
        // 퇴실일이 없으면 입실일과 동일하게 설정 (assignTenantDirectly 로직 참조)
        // 만약 indefinite_move_out 이면 null
        $endDate = $tenant->indefinite_move_out ? null : ($tenant->move_out_date ? $tenant->move_out_date->format('Y-m-d') : null);
        
        // endDate가 null인데 indefinite가 아니면 startDate와 동일하게 (기존 로직 유지)
        if (!$endDate && !$tenant->indefinite_move_out) {
             $endDate = $startDate;
        }


        // 바로 배정
        $this->assignTenantDirectly($this->pendingAssignmentRoomId, $this->selectedWaitingTenantId, $startDate, $endDate);

        // 상태 초기화 및 모달 닫기
        $this->cancelPendingAssignment();
    }

    public function cancelPendingAssignment()
    {
        $this->showAssignmentConfirmation = false;
        $this->pendingAssignmentRoomId = null;
        
        // 모바일 배정 상태 초기화
        $this->selectedScheduleRoomId = null;
        $this->selectedScheduleDate = null;
    }

    public function assignTenantDirectly($roomId, $tenantId, $startDate, $endDate)
    {
        $room = Room::findOrFail($roomId);
        $tenant = Tenant::findOrFail($tenantId);

        // 대기자가 아니면 에러
        if ($tenant->room_id !== null) {
            Notification::make()
                ->danger()
                ->title('이미 배정된 입주자입니다')
                ->send();
            return;
        }

        // 날짜 처리: 과거 날짜면 오늘로 조정
        $moveInDate = \Carbon\Carbon::parse($startDate);
        if ($moveInDate->isPast()) {
            $moveInDate = now();
            $startDate = $moveInDate->format('Y-m-d');
        }

        $moveOutDate = \Carbon\Carbon::parse($endDate);
        if ($moveOutDate->isPast()) {
            $moveOutDate = now();
            $endDate = $moveOutDate->format('Y-m-d');
        }

        $hasOverlap = Tenant::where('room_id', $roomId)
            ->where('id', '!=', $tenantId)
            ->where(function ($query) use ($moveInDate, $moveOutDate) {
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
            })
            ->exists();

        if ($hasOverlap) {
            Notification::make()
                ->danger()
                ->title('일정 겹침')
                ->body('해당 호실에 선택한 날짜와 겹치는 일정이 이미 존재합니다.')
                ->send();
            return;
        }

        // 입주자 배정
        $updateData = [
            'room_id' => $roomId,
            'room_number' => $room->room_number,
            'room_type' => $room->room_type,
            'monthly_rent' => $room->monthly_rent ?? 0,
            'move_in_date' => $startDate,
            'payment_status' => 'pending',
            'status' => 'active',
        ];

        // 퇴실일 미정이면 퇴실일을 null로, 아니면 지정된 날짜로
        if ($tenant->indefinite_move_out) {
            $updateData['move_out_date'] = null;
            $updateData['indefinite_move_out'] = true;
        } else {
            $updateData['move_out_date'] = $endDate;
            $updateData['indefinite_move_out'] = false;
        }

        $tenant->update($updateData);

        // 방 상태 업데이트
        $roomData = [
            'status' => 'occupied',
            'tenant_name' => $tenant->name,
            'move_in_date' => $startDate,
        ];

        // 퇴실일 미정이면 null로
        if ($tenant->indefinite_move_out) {
            $roomData['move_out_date'] = null;
        } else {
            $roomData['move_out_date'] = $endDate;
        }

        $room->update($roomData);

        Notification::make()
            ->success()
            ->title('입주자가 배정되었습니다')
            ->send();

        // 화면 갱신 이벤트 발송

        // 선택 상태 초기화 (활성화된 카드 비활성화를 위해)
        $this->selectedWaitingTenantId = null;
        $this->dispatch('tenant-created');

        // 페이지 전체 새로고침 (제거함: 탭 상태 유지를 위해 Livewire 리렌더링만 사용)
        // $this->js('
        //     setTimeout(() => {
        //         window.location.reload();
        //     }, 500);
        // ');
    }

    /**
     * 호실에서 입주자 제거 (대기자 목록으로 복귀)
     */
    public $showRemoveConfirmation = false;
    public $pendingRemovalTenantId = null;

    public function removeTenantFromRoom($tenantId)
    {
        // 확인 모달 표시
        $this->pendingRemovalTenantId = $tenantId;
        $this->showRemoveConfirmation = true;
    }

    public function confirmRemoveTenant()
    {
        if (!$this->pendingRemovalTenantId) {
            return;
        }

        $tenant = Tenant::findOrFail($this->pendingRemovalTenantId);
        $roomId = $tenant->room_id; // room_id를 미리 저장

        // room_id를 null로 설정하여 대기자 목록으로 복귀
        // 입실일과 퇴실일은 유지
        $tenant->update([
            'room_id' => null,
            'room_number' => null,
            'payment_status' => 'waiting',
        ]);

        // 호실 상태 업데이트
        if ($roomId) {
            $room = Room::find($roomId);
            if ($room) {
                $room->update([
                    'status' => 'vacant',
                    'tenant_name' => null,
                    'move_in_date' => null,
                    'move_out_date' => null,
                ]);
            }
        }

        Notification::make()
            ->title('입주자가 대기자 목록으로 이동되었습니다')
            ->success()
            ->send();

        // 확인 모달 닫기
        $this->showRemoveConfirmation = false;
        $this->pendingRemovalTenantId = null;
    }

    public function cancelRemoveTenant()
    {
        $this->showRemoveConfirmation = false;
        $this->pendingRemovalTenantId = null;
    }

    public $showAssignmentConfirmation = false;
    public $pendingAssignmentRoomId = null;

    /**
     * 입주자 생성 이벤트 리스너 - 화면 새로고침
     */
    #[On('refresh-tenants')]
    public function refreshTenants()
    {
        // Livewire 컴포넌트 자동 리렌더링
        // 아무것도 하지 않아도 리렌더링됨
    }

    /**
     * 입주자 업데이트 이벤트 리스너 - 화면 새로고침
     */
    #[On('tenant-updated')]
    public function refreshAfterTenantUpdated()
    {
        // Livewire 컴포넌트 자동 리렌더링
        // 아무것도 하지 않아도 리렌더링됨
    }

    public function getHeading(): string
    {
        $branchId = session('current_branch_id');
        $branch = Branch::find($branchId);

        return $branch ? "{$branch->name} 입실 관리" : '입실 관리';
    }

    /**
     * 입실 대기자 목록 가져오기 (호실이 배정되지 않은 입주자만)
     */
    public function getWaitingTenants()
    {
        $branchId = session('current_branch_id');

        $query = Tenant::where('branch_id', $branchId)
            ->whereNull('room_id'); // 호실이 배정되지 않은 입주자만

        // 필터 적용
        if ($this->waitingFilter === 'short_term') {
            $query->where('is_short_term', true);
        } elseif ($this->waitingFilter === 'long_term') {
            $query->where('is_short_term', false);
        }

        $tenants = $query->orderBy('created_at', 'desc')->get();

        // 같은 이름과 전화번호를 가진 입주자 중 가장 최근 레코드만 선택
        $uniqueTenants = collect();
        $seenKeys = [];

        foreach ($tenants as $tenant) {
            $key = $tenant->name . '|' . ($tenant->phone ?? '');

            if (!in_array($key, $seenKeys)) {
                $seenKeys[] = $key;
                $uniqueTenants->push($tenant);
            }
        }

        // 날짜가 있는 입주자와 없는 입주자를 분리
        $withDates = $uniqueTenants->filter(fn($t) => $t->move_in_date !== null);
        $withoutDates = $uniqueTenants->filter(fn($t) => $t->move_in_date === null);

        // 날짜가 있는 입주자: move_in_date 오름차순 정렬 (빠른 날짜가 먼저)
        $withDates = $withDates->sortBy('move_in_date');

        // 날짜가 없는 입주자: created_at 내림차순 유지 (이미 정렬되어 있음)

        // 날짜 있는 입주자를 먼저, 그 다음 날짜 없는 입주자
        return $withDates->merge($withoutDates)->values();
    }

    /**
     * 호실 현황 가져오기
     */
    public function getRooms()
    {
        $branchId = session('current_branch_id');

        $query = Room::where('branch_id', $branchId)
            ->with(['tenant', 'futureTenants']);

        // 필터 적용
        if ($this->roomFilter === 'occupied') {
            // 입주 중인 호실만
            $query->whereHas('tenant');
        } elseif ($this->roomFilter === 'vacant') {
            // 공실만
            $query->whereDoesntHave('tenant');
        }

        return $query->orderBy('room_number', 'asc')->get();
    }

    public function openAllTenantsModal()
    {
        $this->dispatch('open-all-tenants-modal');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewAllTenants')
                ->label('전체 호실 및 입주자 확인')
                ->icon('heroicon-o-rectangle-stack')
                ->color('primary')
                ->action('openAllTenantsModal')
                ->button(),
            Actions\ActionGroup::make([
                Action::make('downloadTemplate')
                    ->label('엑셀 파일 가이드 다운로드')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->action(function () {
                        $branchId = session('current_branch_id');
                        $branch = Branch::find($branchId);
                        $filename = $branch ? "{$branch->name}_일정_템플릿.xlsx" : '일정_템플릿.xlsx';

                        Notification::make()
                            ->title('템플릿 다운로드 완료')
                            ->success()
                            ->send();

                        return Excel::download(new ScheduleTemplateExport(), $filename);
                    }),
                Action::make('uploadExcel')
                    ->label('엑셀 파일 업로드')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('엑셀 파일')
                            ->acceptedFileTypes([
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->required()
                            ->helperText('엑셀 파일 가이드에 맞춰 작성한 파일을 업로드하세요.')
                            ->disk('local')
                            ->directory('temp-uploads'),
                    ])
                    ->action(function (array $data) {
                        $branchId = session('current_branch_id');

                        if (!$branchId) {
                            Notification::make()
                                ->title('지점을 선택해주세요')
                                ->danger()
                                ->send();
                            return;
                        }

                        try {
                            $filePath = storage_path('app/' . $data['file']);

                            // 엑셀 파일 헤더 검증
                            $import = new ScheduleImport($branchId, auth()->id());

                            Excel::import($import, $filePath);

                            // 에러가 있는 경우
                            if (count($import->failures()) > 0) {
                                $errorMessages = [];
                                foreach ($import->failures() as $failure) {
                                    $errorMessages[] = "행 {$failure->row()}: " . implode(', ', $failure->errors());
                                }

                                Notification::make()
                                    ->title('일부 데이터 업로드에 실패했습니다')
                                    ->body(implode("\n", array_slice($errorMessages, 0, 3)))
                                    ->danger()
                                    ->send();

                                // 임시 파일 삭제
                                @unlink($filePath);
                                return;
                            }

                            // 중복으로 건너뛴 데이터 확인
                            $skippedRows = $import->getSkippedRows();

                            if (count($skippedRows) > 0) {
                                $skippedList = [];
                                foreach (array_slice($skippedRows, 0, 5) as $skipped) {
                                    $roomInfo = !empty($skipped['room']) ? " [{$skipped['room']}호]" : '';
                                    $phoneInfo = !empty($skipped['phone']) ? " ({$skipped['phone']})" : '';
                                    $dateInfo = !empty($skipped['date']) ? " ({$skipped['date']})" : '';
                                    $skippedList[] = "• {$skipped['name']}{$roomInfo}{$phoneInfo}{$dateInfo}";
                                }

                                $moreCount = count($skippedRows) > 5 ? '외 ' . (count($skippedRows) - 5) . '건' : '';

                                Notification::make()
                                    ->title('일부 데이터가 업데이트되지 않았습니다')
                                    ->body("같은 호실에 현재 겹쳐진 일정이 존재하는 아래와 같은 데이터는 업데이트 되지 않았습니다. 해당 데이터는 직접 수정해주시길 바랍니다:\n\n" . implode("\n", $skippedList) . ($moreCount ? "\n" . $moreCount : ''))
                                    ->warning()
                                    ->duration(15000)
                                    ->send();
                            }

                            Notification::make()
                                ->title('입실 관리가 업데이트되었습니다')
                                ->success()
                                ->send();

                            // 임시 파일 삭제
                            @unlink($filePath);

                            // 페이지 새로고침
                            $this->redirect(static::getUrl());
                        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                            $failures = $e->failures();
                            $errorMessages = [];

                            foreach ($failures as $failure) {
                                $errorMessages[] = "행 {$failure->row()}: " . implode(', ', $failure->errors());
                            }

                            Notification::make()
                                ->title('엑셀 파일 형식이 올바르지 않습니다')
                                ->body('엑셀 파일 가이드를 다운로드하여 양식을 지켜 다시 작성 후 업로드해주세요. ' . implode(', ', array_slice($errorMessages, 0, 2)))
                                ->danger()
                                ->duration(10000)
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('업로드 실패')
                                ->body('엑셀 파일 가이드를 다운로드하여 양식을 지켜 다시 작성 후 업로드해주세요. 오류: ' . $e->getMessage())
                                ->danger()
                                ->duration(10000)
                                ->send();
                        }
                    })
                    ->modalWidth(Width::Large),
                Action::make('downloadExcel')
                    ->label('엑셀 파일 다운로드')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('warning')
                    ->action(function () {
                        $branchId = session('current_branch_id');
                        $branch = Branch::find($branchId);
                        $filename = $branch ? "{$branch->name}_일정_목록.xlsx" : '일정_목록.xlsx';

                        Notification::make()
                            ->title('다운로드 완료')
                            ->success()
                            ->send();

                        return Excel::download(new ScheduleExport($branchId), $filename);
                    }),
            ])
                ->label('엑셀 관리')
                ->icon('heroicon-o-table-cells')
                ->color('primary')
                ->button(),
        ];
    }
}

