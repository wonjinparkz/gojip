<?php

namespace App\Livewire;

use App\Models\Tenant;
use Livewire\Attributes\On;
use Livewire\Component;

class PaymentManagementModal extends Component
{
    public bool $isOpen = false;
    public ?int $tenantId = null;
    public ?Tenant $tenant = null;

    // Form fields
    public ?int $payment_due_day = null;
    public ?string $actual_payment_date = null;
    public ?string $payment_method = null;
    public ?string $payment_status = null;

    protected $rules = [
        'payment_due_day' => 'nullable|integer|min:1|max:31',
        'actual_payment_date' => 'nullable|date',
        'payment_method' => 'nullable|string|in:card,transfer,cash',
        'payment_status' => 'nullable|string|in:paid,pending,overdue,waiting',
    ];

    #[On('open-payment-modal')]
    public function openModal(?int $tenantId = null): void
    {
        $this->reset(['payment_due_day', 'actual_payment_date', 'payment_method', 'payment_status']);

        if ($tenantId) {
            $this->tenantId = $tenantId;
            $this->tenant = Tenant::with('room')->find($tenantId);

            if ($this->tenant) {
                $this->payment_due_day = $this->tenant->payment_due_day;
                $this->actual_payment_date = $this->tenant->actual_payment_date?->format('Y-m-d');
                $this->payment_method = $this->tenant->payment_method;
                $this->payment_status = $this->tenant->payment_status;
            }
        }

        $this->isOpen = true;
    }

    #[On('edit-payment')]
    public function editPayment(int $tenantId): void
    {
        $this->openModal($tenantId);
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->reset(['tenantId', 'tenant', 'payment_due_day', 'actual_payment_date', 'payment_method', 'payment_status']);
    }

    public function save(): void
    {
        $this->validate();

        if (!$this->tenant) {
            $this->closeModal();
            return;
        }

        $this->tenant->update([
            'payment_due_day' => $this->payment_due_day,
            'actual_payment_date' => $this->actual_payment_date ?: null,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
        ]);

        $this->dispatch('payment-updated');
        $this->closeModal();

        session()->flash('message', '수납 정보가 저장되었습니다.');
    }

    public function render()
    {
        return view('livewire.payment-management-modal');
    }
}
