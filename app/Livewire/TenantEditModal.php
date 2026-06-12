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
    public bool $isShortTerm = false;
    public ?int $shortTermMonthlyRent = null;
    public ?int $shortTermDeposit = null;

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

        // 퇴실일 미정이면 입력 필드는 비우고(placeholder 표시), DB에도 null 유지
        if ($this->indefiniteMoveOut) {
            $this->moveOutDate = null;
        } else {
            $this->moveOutDate = $tenant->move_out_date?->format('Y-m-d');
        }

        $this->paymentStatus = $tenant->payment_status ?? 'pending';
        $this->isShortTerm = $tenant->is_short_term ?? false;
        $this->shortTermMonthlyRent = $tenant->short_term_monthly_rent;
        $this->shortTermDeposit = $tenant->short_term_deposit;

        $this->show = true;
    }

    public function updatedIndefiniteMoveOut($value)
    {
        // 퇴실일 미정 체크 시 값을 비워 placeholder(----.--. --.) 가 보이도록 함
        if ($value) {
            $this->moveOutDate = null;
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
            'isShortTerm',
            'shortTermMonthlyRent',
            'shortTermDeposit',
        ]);
    }

    public function save()
    {
        $rules = [
            'moveInDate' => 'required|date',
            'moveOutDate' => $this->indefiniteMoveOut
                ? 'nullable'
                : 'required|date|after_or_equal:moveInDate',
            'paymentStatus' => 'required|in:paid,pending,overdue,waiting',
        ];

        // 단기숙박인 경우 추가 검증
        if ($this->isShortTerm) {
            $rules['shortTermMonthlyRent'] = 'required|integer|min:0';
            $rules['shortTermDeposit'] = 'nullable|integer|min:0';
        }

        $this->validate($rules);

        $tenant = Tenant::findOrFail($this->tenantId);

        \Log::info('=== TenantEditModal save 시작 ===');
        \Log::info('업데이트 전 입주자 정보:', [
            'id' => $tenant->id,
            'name' => $tenant->name,
            'move_in_date' => $tenant->move_in_date,
            'move_out_date' => $tenant->move_out_date,
            'payment_status' => $tenant->payment_status,
        ]);

        $effectiveMoveOutDate = $this->indefiniteMoveOut ? null : $this->moveOutDate;

        // 입주자 정보 업데이트 (날짜와 결제 상태, 단기숙박 정보)
        $tenant->update([
            'move_in_date' => $this->moveInDate,
            'move_out_date' => $effectiveMoveOutDate,
            'indefinite_move_out' => $this->indefiniteMoveOut,
            'payment_status' => $this->paymentStatus,
            'is_short_term' => $this->isShortTerm,
            'short_term_monthly_rent' => $this->isShortTerm ? $this->shortTermMonthlyRent : null,
            'short_term_deposit' => $this->isShortTerm ? ($this->shortTermDeposit ?? 0) : null,
        ]);

        // 연결된 방 정보도 업데이트
        if ($tenant->room_id) {
            $room = Room::find($tenant->room_id);
            if ($room) {
                $room->update([
                    'move_in_date' => $this->moveInDate,
                    'move_out_date' => $effectiveMoveOutDate,
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

        // JavaScript를 통해 페이지 새로고침 트리거
        $this->js('
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent("refresh-tenants"));
                $wire.close();
            }, 100);
        ');
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

        // 입주자 일정 삭제 대신 호실 배정 해제 및 대기자 목록으로 복귀
        // 입실일과 퇴실일은 유지
        $tenant->update([
            'room_id' => null,
            'room_number' => null,
            'payment_status' => 'waiting',
        ]);

        // 연결된 방 상태를 'vacant'로 변경
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

        \Log::info('입주자 일정 삭제 완료 - 대기자 목록으로 이동');

        Notification::make()
            ->success()
            ->title('입주자 일정이 삭제되어 대기자 목록으로 이동되었습니다')
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
