<?php

namespace App\Livewire;

use App\Models\Tenant;
use App\Models\Room;
use Livewire\Component;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;

class TenantEditModal extends Component
{
    public bool $show = false;
    public ?int $tenantId = null;
    public ?string $moveInDate = null;
    public ?string $moveOutDate = null;
    public bool $indefiniteMoveOut = false;
    public string $paymentStatus = 'pending';
    public string $tenantName = '';
    public string $roomNumber = '';

    #[On('open-tenant-edit-modal')]
    public function open($tenantId)
    {
        $this->tenantId = $tenantId;
        $tenant = Tenant::with('room')->findOrFail($tenantId);

        // 입주자 정보 로드
        $this->tenantName = $tenant->name;
        $this->roomNumber = $tenant->room_number ?? '';
        $this->moveInDate = $tenant->move_in_date?->format('Y-m-d');
        $this->indefiniteMoveOut = $tenant->indefinite_move_out ?? false;

        // 퇴실일 미정이면 항상 오늘 날짜로 표시
        if ($this->indefiniteMoveOut) {
            $this->moveOutDate = now()->format('Y-m-d');
        } else {
            $this->moveOutDate = $tenant->move_out_date?->format('Y-m-d');
        }

        $this->paymentStatus = $tenant->payment_status ?? 'pending';

        $this->show = true;
    }

    public function updatedIndefiniteMoveOut($value)
    {
        // 퇴실일 미정 체크박스가 체크되면 항상 오늘 날짜로 설정
        if ($value) {
            $this->moveOutDate = now()->format('Y-m-d');
        }
    }

    public function close()
    {
        $this->show = false;
        $this->reset([
            'tenantId',
            'moveInDate',
            'moveOutDate',
            'indefiniteMoveOut',
            'paymentStatus',
            'tenantName',
            'roomNumber',
        ]);
    }

    public function save()
    {
        // 퇴실일 미정이 체크되면 항상 오늘 날짜로 설정
        if ($this->indefiniteMoveOut) {
            $this->moveOutDate = now()->format('Y-m-d');
        }

        $this->validate([
            'moveInDate' => 'required|date',
            'moveOutDate' => 'required|date|after_or_equal:moveInDate',
            'paymentStatus' => 'required|in:paid,pending,overdue,waiting',
        ]);

        $tenant = Tenant::findOrFail($this->tenantId);

        \Log::info('=== TenantEditModal save 시작 ===');
        \Log::info('업데이트 전 입주자 정보:', [
            'id' => $tenant->id,
            'name' => $tenant->name,
            'move_in_date' => $tenant->move_in_date,
            'move_out_date' => $tenant->move_out_date,
            'payment_status' => $tenant->payment_status,
        ]);

        // 입주자 정보 업데이트 (날짜와 결제 상태만)
        $tenant->update([
            'move_in_date' => $this->moveInDate,
            'move_out_date' => $this->moveOutDate,
            'indefinite_move_out' => $this->indefiniteMoveOut,
            'payment_status' => $this->paymentStatus,
        ]);

        // 연결된 방 정보도 업데이트
        if ($tenant->room_id) {
            $room = Room::find($tenant->room_id);
            if ($room) {
                $room->update([
                    'move_in_date' => $this->moveInDate,
                    'move_out_date' => $this->moveOutDate,
                ]);
            }
        }

        $tenant->refresh();
        \Log::info('업데이트 후 입주자 정보:', [
            'id' => $tenant->id,
            'name' => $tenant->name,
            'move_in_date' => $tenant->move_in_date,
            'move_out_date' => $tenant->move_out_date,
            'payment_status' => $tenant->payment_status,
        ]);

        Notification::make()
            ->success()
            ->title('입주자 정보가 수정되었습니다')
            ->send();

        // 스케줄러를 새로고침
        \Log::info('tenant-updated 이벤트 발송');
        $this->dispatch('tenant-updated');

        // 모달 닫기
        $this->js('setTimeout(() => $wire.close(), 100)');
    }

    public function delete()
    {
        $tenant = Tenant::findOrFail($this->tenantId);

        \Log::info('=== TenantEditModal delete 시작 ===');
        \Log::info('삭제할 입주자 정보:', [
            'id' => $tenant->id,
            'name' => $tenant->name,
            'room_id' => $tenant->room_id,
        ]);

        $roomId = $tenant->room_id;

        // 입주자 일정 삭제
        $tenant->delete();

        // 연결된 방 상태를 'available'로 변경
        if ($roomId) {
            $room = Room::find($roomId);
            if ($room) {
                $room->update([
                    'status' => 'available',
                    'tenant_name' => null,
                    'move_in_date' => null,
                    'move_out_date' => null,
                ]);
            }
        }

        \Log::info('입주자 일정 삭제 완료');

        Notification::make()
            ->success()
            ->title('입주자 일정이 삭제되었습니다')
            ->send();

        // 스케줄러를 새로고침
        \Log::info('tenant-updated 이벤트 발송');
        $this->dispatch('tenant-updated');

        // 모달 닫기
        $this->js('setTimeout(() => $wire.close(), 100)');
    }

    public function render()
    {
        return view('livewire.tenant-edit-modal');
    }
}
