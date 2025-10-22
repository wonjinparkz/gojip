<?php

namespace App\Livewire;

use App\Models\Tenant;
use App\Models\Room;
use Livewire\Component;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;

class TenantManagementModal extends Component
{
    public bool $show = false;
    public ?int $editingTenantId = null;

    // Form fields
    public $name = '';
    public $phone = '';
    public $gender = null;
    public $last_payment_date = null;
    public $payment_method = null;
    public $payment_status = 'pending';
    public $is_blacklisted = false;
    public $blacklist_memo = '';

    #[On('open-tenant-management-modal')]
    public function openCreate()
    {
        $this->reset();
        $this->payment_status = 'pending';
        $this->show = true;
    }

    #[On('edit-tenant-management')]
    public function openEdit($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);

        $this->editingTenantId = $tenant->id;
        $this->name = $tenant->name;
        $this->phone = $tenant->phone;
        $this->gender = $tenant->gender;
        $this->last_payment_date = $tenant->last_payment_date?->format('Y-m-d');
        $this->payment_method = $tenant->payment_method;
        $this->payment_status = $tenant->payment_status;
        $this->is_blacklisted = $tenant->is_blacklisted;
        $this->blacklist_memo = $tenant->blacklist_memo ?? '';

        $this->show = true;
    }

    public function updatedPhone($value)
    {
        // 숫자만 추출
        $numbers = preg_replace('/[^0-9]/', '', $value);

        // 자동 포맷팅: xxx-xxxx-xxxx
        if (strlen($numbers) >= 11) {
            $this->phone = substr($numbers, 0, 3) . '-' . substr($numbers, 3, 4) . '-' . substr($numbers, 7, 4);
        } elseif (strlen($numbers) >= 7) {
            $this->phone = substr($numbers, 0, 3) . '-' . substr($numbers, 3, 4) . '-' . substr($numbers, 7);
        } elseif (strlen($numbers) >= 3) {
            $this->phone = substr($numbers, 0, 3) . '-' . substr($numbers, 3);
        } else {
            $this->phone = $numbers;
        }
    }

    public function close()
    {
        $this->show = false;
        $this->reset();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'gender' => 'nullable|in:male,female',
            'last_payment_date' => 'nullable|date',
            'payment_method' => 'nullable|in:card,transfer,cash',
            'payment_status' => 'required|in:paid,pending,overdue,waiting',
            'is_blacklisted' => 'boolean',
            'blacklist_memo' => 'nullable|string|max:65535',
        ]);

        $branchId = session('current_branch_id');
        \Log::info('TenantManagementModal save - Session branch_id:', ['branch_id' => $branchId]);

        // branch_id가 없으면 첫 번째 지점을 자동 설정
        if (!$branchId) {
            $firstBranch = \App\Models\Branch::where('user_id', auth()->id())->first();
            \Log::info('TenantManagementModal save - First branch:', ['branch' => $firstBranch ? $firstBranch->id : null]);

            if ($firstBranch) {
                $branchId = $firstBranch->id;
                session(['current_branch_id' => $branchId]);
                \Log::info('TenantManagementModal save - Set branch in session:', ['branch_id' => $branchId]);
            } else {
                \Log::error('TenantManagementModal save - No branches found for user');
                Notification::make()
                    ->danger()
                    ->title('지점이 없습니다')
                    ->body('입주자를 생성하려면 먼저 지점을 추가해야 합니다.')
                    ->send();
                return;
            }
        }

        $data = [
            'user_id' => auth()->id(),
            'branch_id' => $branchId,
            'name' => $this->name,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'last_payment_date' => $this->last_payment_date,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'is_blacklisted' => $this->is_blacklisted,
            'blacklist_memo' => $this->blacklist_memo,
        ];

        if ($this->editingTenantId) {
            // Update existing tenant
            $tenant = Tenant::findOrFail($this->editingTenantId);
            $tenant->update($data);

            $this->close();
            $this->dispatch('tenant-management-saved');

            Notification::make()
                ->success()
                ->title('입주자 정보가 수정되었습니다')
                ->send();
        } else {
            // Create new tenant
            Tenant::create($data);

            $this->close();
            $this->dispatch('tenant-management-saved');

            Notification::make()
                ->success()
                ->title('입주자가 생성되었습니다')
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.tenant-management-modal');
    }
}
