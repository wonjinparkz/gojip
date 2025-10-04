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

        $this->allTenants = $query->get()
            ->map(function ($tenant) {
                return [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'phone' => $tenant->phone ?? '',
                    'current_room' => $tenant->room_number ?? null,
                ];
            })
            ->toArray();

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
            'paymentStatus' => 'required|in:paid,pending,overdue',
        ]);

        \Log::info('=== TenantCreateModal save 시작 ===');

        $room = Room::findOrFail($this->roomId);
        $selectedTenant = Tenant::findOrFail($this->selectedTenantId);

        \Log::info('업데이트 전 입주자 정보:', [
            'id' => $selectedTenant->id,
            'name' => $selectedTenant->name,
            'room_id' => $selectedTenant->room_id,
            'move_in_date' => $selectedTenant->move_in_date,
            'move_out_date' => $selectedTenant->move_out_date,
        ]);

        \Log::info('업데이트 할 데이터:', [
            'room_id' => $this->roomId,
            'room_number' => $room->room_number,
            'move_in_date' => $this->moveInDate,
            'move_out_date' => $this->moveOutDate,
            'payment_status' => $this->paymentStatus,
        ]);

        // 기존 입주자 정보 업데이트
        $selectedTenant->update([
            'room_id' => $this->roomId,
            'room_number' => $room->room_number,
            'move_in_date' => $this->moveInDate,
            'move_out_date' => $this->moveOutDate,
            'payment_status' => $this->paymentStatus,
            'status' => 'active',
        ]);

        $selectedTenant->refresh();
        \Log::info('업데이트 후 입주자 정보:', [
            'id' => $selectedTenant->id,
            'name' => $selectedTenant->name,
            'room_id' => $selectedTenant->room_id,
            'move_in_date' => $selectedTenant->move_in_date,
            'move_out_date' => $selectedTenant->move_out_date,
        ]);

        // 방 상태 업데이트
        $room->update([
            'status' => 'occupied',
            'tenant_name' => $selectedTenant->name,
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
