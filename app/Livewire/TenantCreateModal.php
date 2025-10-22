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

    #[On('open-tenant-modal')]
    public function open($roomId, $startDate, $endDate)
    {
        $this->roomId = $roomId;
        $this->moveInDate = $startDate;
        $this->moveOutDate = $endDate;
        $this->selectedTenantId = null;
        $this->searchQuery = '';
        $this->paymentStatus = 'pending';
        $this->showDropdown = false;

        // 현재 호실의 지점 ID 가져오기
        $room = Room::find($roomId);
        $this->branchId = $room ? $room->branch_id : null;

        // 지점의 모든 입주자 로드
        $this->loadTenants();

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
        $this->reset(['roomId', 'moveInDate', 'moveOutDate', 'selectedTenantId', 'searchQuery', 'paymentStatus', 'allTenants', 'filteredTenants', 'branchId', 'showDropdown']);
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

        $this->validate([
            'moveInDate' => 'required|date',
            'moveOutDate' => 'required|date|after_or_equal:moveInDate',
            'paymentStatus' => 'required|in:paid,pending,overdue,waiting',
        ]);

        \Log::info('=== TenantCreateModal save 시작 ===');

        $room = Room::findOrFail($this->roomId);
        $selectedTenant = Tenant::findOrFail($this->selectedTenantId);

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

        // 기존 입주자 정보를 기반으로 새로운 입주자 레코드 생성 (과거 일정 유지)
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
            'payment_status' => $this->paymentStatus,
            'status' => 'active',
            'is_blacklisted' => $selectedTenant->is_blacklisted,
            'blacklist_memo' => $selectedTenant->blacklist_memo,
        ]);

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

        // 스케줄러를 새로고침
        \Log::info('tenant-created 이벤트 발송');
        $this->dispatch('tenant-created');

        // 모달 닫기 (약간의 딜레이 후 닫기)
        $this->js('setTimeout(() => $wire.close(), 100)');
    }

    public function render()
    {
        return view('livewire.tenant-create-modal');
    }
}
