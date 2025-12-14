<?php

namespace App\Livewire;

use App\Models\Tenant;
use App\Models\Room;
use Livewire\Component;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;

class TenantCreateModal extends Component
{
    public bool $show = false;
    public ?string $roomId = null;
    public ?string $moveInDate = null;
    public ?string $moveOutDate = null;
    public ?string $selectedTenantId = null;
    public string $searchQuery = '';
    public string $paymentStatus = 'pending';
    public array $allTenants = [];
    public array $filteredTenants = [];
    public ?int $branchId = null;
    public bool $showDropdown = false;
    public bool $isShortTerm = false;
    public ?int $shortTermMonthlyRent = null;
    public ?int $shortTermDeposit = null;
    public bool $indefiniteMoveOut = false;
    public bool $datesReadOnly = false;
    public ?string $minMoveInDate = null;

    #[On('open-tenant-modal')]
    public function open($roomId, $startDate, $endDate, $tenantId = null, $datesReadOnly = false, $minMoveInDate = null)
    {
        $this->roomId = $roomId;
        $this->moveInDate = $startDate;
        $this->moveOutDate = $endDate;
        $this->selectedTenantId = null;
        $this->searchQuery = '';
        $this->paymentStatus = 'pending';
        $this->showDropdown = false;
        $this->isShortTerm = false;
        $this->shortTermMonthlyRent = null;
        $this->shortTermDeposit = null;
        $this->indefiniteMoveOut = false;
        $this->datesReadOnly = $datesReadOnly;
        $this->minMoveInDate = $minMoveInDate;

        // 현재 호실의 지점 ID 가져오기
        $room = Room::find($roomId);
        $this->branchId = $room ? $room->branch_id : null;

        // 지점의 모든 입주자 로드
        $this->loadTenants();

        // tenantId가 제공된 경우 해당 입주자 자동 선택
        if ($tenantId) {
            $this->selectTenant($tenantId);
        }

        $this->show = true;
    }

    public function loadTenants()
    {
        $query = Tenant::where('user_id', auth()->id());

        if ($this->branchId) {
            $query->where('branch_id', $this->branchId);
        }

        $today = now()->format('Y-m-d');

        $tenants = $query->orderBy('created_at', 'desc')->get();

        // 같은 이름과 전화번호를 가진 입주자 중 가장 최근 레코드만 선택
        $uniqueTenants = [];
        $seenKeys = [];

        foreach ($tenants as $tenant) {
            $key = $tenant->name . '|' . $tenant->phone;

            if (!in_array($key, $seenKeys)) {
                $seenKeys[] = $key;
                $isFutureResident = false;
                $currentRoom = $tenant->room_number ?? null;

                // 입주일이 미래 날짜인 경우
                if ($tenant->move_in_date && $tenant->move_in_date->format('Y-m-d') > $today) {
                    $isFutureResident = true;
                }

                $uniqueTenants[] = [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'phone' => $tenant->phone ?? '',
                    'current_room' => $currentRoom,
                    'is_future_resident' => $isFutureResident,
                ];
            }
        }

        $this->allTenants = $uniqueTenants;
        $this->filteredTenants = $this->allTenants;
    }

    public function close()
    {
        $this->show = false;
        $this->reset(['roomId', 'moveInDate', 'moveOutDate', 'selectedTenantId', 'searchQuery', 'paymentStatus', 'allTenants', 'filteredTenants', 'branchId', 'showDropdown', 'isShortTerm', 'shortTermMonthlyRent', 'shortTermDeposit', 'indefiniteMoveOut', 'datesReadOnly', 'minMoveInDate']);
    }

    public function updatedSearchQuery()
    {
        // 검색어가 변경되면 드롭다운 표시
        $this->showDropdown = true;

        if (strlen($this->searchQuery) >= 1) {
            $this->filteredTenants = array_filter($this->allTenants, function($tenant) {
                return stripos($tenant['name'], $this->searchQuery) !== false ||
                       stripos($tenant['phone'], $this->searchQuery) !== false;
            });
        } else {
            $this->filteredTenants = $this->allTenants;
        }
    }

    public function updatedIndefiniteMoveOut($value)
    {
        // 퇴실일 미정 체크박스가 체크되면 입실일 이후 날짜로 설정
        if ($value) {
            // 입실일이 미래면 입실일과 동일하게, 아니면 오늘 날짜로
            $moveInDate = $this->moveInDate ? \Carbon\Carbon::parse($this->moveInDate) : now();
            $this->moveOutDate = $moveInDate->isFuture() ? $moveInDate->format('Y-m-d') : now()->format('Y-m-d');
        }
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function selectTenant($tenantId)
    {
        $this->selectedTenantId = $tenantId;
        $tenant = collect($this->allTenants)->firstWhere('id', $tenantId);
        if ($tenant) {
            $this->searchQuery = $tenant['name'];
        }
        $this->showDropdown = false;

        // 데이터베이스에서 실제 입주자 정보를 로드하여 단기숙박 정보 및 날짜 설정
        $tenantModel = Tenant::find($tenantId);
        if ($tenantModel) {
            // 단기숙박 정보는 항상 입주자 정보에서 가져옴
            $this->isShortTerm = $tenantModel->is_short_term ?? false;
            $this->shortTermMonthlyRent = $tenantModel->short_term_monthly_rent;
            $this->shortTermDeposit = $tenantModel->short_term_deposit;

            // 퇴실일 미정 정보
            $this->indefiniteMoveOut = $tenantModel->indefinite_move_out ?? false;

            // 입실일과 퇴실일: 입주자가 가지고 있는 경우에만 설정 (이미 설정된 날짜가 없는 경우)
            // 입주자의 날짜가 있으면 사용하되, 모달이 열릴 때 전달된 날짜가 우선
            if ($tenantModel->move_in_date && (!$this->moveInDate || $this->moveInDate === now()->format('Y-m-d'))) {
                $this->moveInDate = $tenantModel->move_in_date->format('Y-m-d');
            }
            if ($tenantModel->move_out_date && (!$this->moveOutDate || $this->moveOutDate === now()->addMonth()->format('Y-m-d'))) {
                $this->moveOutDate = $tenantModel->move_out_date->format('Y-m-d');
            }
        }
    }

    public function getSelectedTenantName()
    {
        if ($this->selectedTenantId) {
            $tenant = collect($this->allTenants)->firstWhere('id', $this->selectedTenantId);
            return $tenant ? $tenant['name'] : '';
        }
        return '';
    }

    public function save()
    {
        if (!$this->selectedTenantId) {
            Notification::make()
                ->danger()
                ->title('입주자를 선택해주세요')
                ->send();
            return;
        }

        $rules = [
            'moveInDate' => 'required|date',
            'moveOutDate' => 'required|date|after_or_equal:moveInDate',
            'paymentStatus' => 'required|in:paid,pending,overdue,waiting',
        ];

        // 단기숙박인 경우 추가 검증
        if ($this->isShortTerm) {
            $rules['shortTermMonthlyRent'] = 'required|integer|min:0';
            $rules['shortTermDeposit'] = 'nullable|integer|min:0';
        }

        $this->validate($rules);

        \Log::info('=== TenantCreateModal save 시작 ===');

        $room = Room::findOrFail($this->roomId);
        $selectedTenant = Tenant::findOrFail($this->selectedTenantId);

        // 날짜 겹침 검증: 같은 호실에 일정이 겹치는 다른 입주자가 있는지 확인
        $moveInDate = \Carbon\Carbon::parse($this->moveInDate);
        $moveOutDate = \Carbon\Carbon::parse($this->moveOutDate);

        $hasOverlap = Tenant::where('room_id', $this->roomId)
            ->where('id', '!=', $this->selectedTenantId)
            ->where(function ($query) use ($moveInDate, $moveOutDate) {
                // 새로운 입주 기간과 겹치는 일정 찾기
                $query->where(function ($q) use ($moveInDate, $moveOutDate) {
                    // 기존 입주자의 입주일이 새 일정 기간 내에 있거나
                    $q->whereBetween('move_in_date', [$moveInDate, $moveOutDate])
                      // 기존 입주자의 퇴실일이 새 일정 기간 내에 있거나
                      ->orWhereBetween('move_out_date', [$moveInDate, $moveOutDate])
                      // 기존 입주자의 기간이 새 일정을 포함하는 경우
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
                ->body('해당 호실에 선택한 날짜와 겹치는 일정이 이미 존재합니다. 날짜를 다시 선택해주세요.')
                ->persistent()
                ->send();
            return;
        }

        \Log::info('기존 입주자 정보:', [
            'id' => $selectedTenant->id,
            'name' => $selectedTenant->name,
            'room_id' => $selectedTenant->room_id,
            'move_in_date' => $selectedTenant->move_in_date,
            'move_out_date' => $selectedTenant->move_out_date,
        ]);

        \Log::info('생성할 새 일정 데이터:', [
            'room_id' => $this->roomId,
            'room_number' => $room->room_number,
            'move_in_date' => $this->moveInDate,
            'move_out_date' => $this->moveOutDate,
            'payment_status' => $this->paymentStatus,
        ]);

        // 대기자인 경우 기존 레코드 업데이트, 그렇지 않으면 새 레코드 생성
        if ($selectedTenant->room_id === null) {
            // 대기자 -> 호실 배정: 기존 레코드 업데이트
            $selectedTenant->update([
                'room_id' => $this->roomId,
                'room_number' => $room->room_number,
                'room_type' => $room->room_type,
                'monthly_rent' => $room->monthly_rent ?? 0,
                'move_in_date' => $this->moveInDate,
                'move_out_date' => $this->moveOutDate,
                'indefinite_move_out' => $this->indefiniteMoveOut,
                'payment_status' => $this->paymentStatus,
                'status' => 'active',
                'is_short_term' => $this->isShortTerm,
                'short_term_monthly_rent' => $this->isShortTerm ? $this->shortTermMonthlyRent : null,
                'short_term_deposit' => $this->isShortTerm ? ($this->shortTermDeposit ?? 0) : null,
            ]);
            $newTenant = $selectedTenant;
        } else {
            // 이미 호실이 있는 경우 -> 새로운 입주자 레코드 생성 (과거 일정 유지)
            $newTenant = Tenant::create([
                'user_id' => $selectedTenant->user_id,
                'branch_id' => $selectedTenant->branch_id,
                'room_id' => $this->roomId,
                'name' => $selectedTenant->name,
                'phone' => $selectedTenant->phone,
                'gender' => $selectedTenant->gender ?? null,
                'room_number' => $room->room_number,
                'room_type' => $room->room_type,
                'monthly_rent' => $room->monthly_rent ?? 0,
                'move_in_date' => $this->moveInDate,
                'move_out_date' => $this->moveOutDate,
                'indefinite_move_out' => $this->indefiniteMoveOut,
                'payment_status' => $this->paymentStatus,
                'status' => 'active',
                'is_blacklisted' => $selectedTenant->is_blacklisted,
                'blacklist_memo' => $selectedTenant->blacklist_memo,
                'is_short_term' => $this->isShortTerm,
                'short_term_monthly_rent' => $this->isShortTerm ? $this->shortTermMonthlyRent : null,
                'short_term_deposit' => $this->isShortTerm ? ($this->shortTermDeposit ?? 0) : null,
            ]);
        }

        \Log::info('새로 생성된 입주자 일정:', [
            'id' => $newTenant->id,
            'name' => $newTenant->name,
            'room_id' => $newTenant->room_id,
            'move_in_date' => $newTenant->move_in_date,
            'move_out_date' => $newTenant->move_out_date,
        ]);

        // 방 상태 업데이트
        $room->update([
            'status' => 'occupied',
            'tenant_name' => $newTenant->name,
            'move_in_date' => $this->moveInDate,
            'move_out_date' => $this->moveOutDate,
        ]);

        Notification::make()
            ->success()
            ->title('입주자가 배정되었습니다')
            ->send();

        // 스케줄러 및 ListTenants를 새로고침 (브라우저 이벤트 사용)
        \Log::info('tenant-created 이벤트 발송');
        $this->dispatch('tenant-created');

        // JavaScript를 통해 페이지 새로고침 트리거
        $this->js('
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent("refresh-tenants"));
                $wire.close();
            }, 100);
        ');
    }

    public function render()
    {
        return view('livewire.tenant-create-modal');
    }
}
