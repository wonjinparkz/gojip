<?php

namespace App\Filament\Resources\PaymentManagement\Pages;

use App\Filament\Resources\PaymentManagement\PaymentManagementResource;
use App\Models\Branch;
use App\Models\CashReceipt;
use App\Models\Room;
use App\Models\Tenant;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class ListPaymentManagement extends ListRecords
{
    protected static string $resource = PaymentManagementResource::class;

    protected string $view = 'filament.resources.payment-management.pages.list-payment-management';

    public array $expandedRooms = [];
    public array $expandedDesktopTenants = [];

    public string $search = '';
    public string $filterProcessStatus = '';
    public string $filterPaymentStatus = '';
    public string $filterPaymentMethod = '';
    public bool $showMobileFilters = false;

    public int $analysisYear = 0;
    public int $rentAdjustment = 0;
    public bool $showRentSimulation = false;

    public function mount(): void
    {
        parent::mount();
        $this->analysisYear = (int) now()->year;
    }

    public function toggleMobileFilters(): void
    {
        $this->showMobileFilters = !$this->showMobileFilters;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterProcessStatus = '';
        $this->filterPaymentStatus = '';
        $this->filterPaymentMethod = '';
    }

    public function getActiveFilterCount(): int
    {
        return (int) ($this->filterProcessStatus !== '')
             + (int) ($this->filterPaymentStatus !== '')
             + (int) ($this->filterPaymentMethod !== '');
    }

    public function toggleRoom(string $roomNumber): void
    {
        if (in_array($roomNumber, $this->expandedRooms)) {
            $this->expandedRooms = array_filter($this->expandedRooms, fn($r) => $r !== $roomNumber);
        } else {
            $this->expandedRooms[] = $roomNumber;
        }
    }

    public function toggleDesktopTenant(int $tenantId): void
    {
        if (in_array($tenantId, $this->expandedDesktopTenants)) {
            $this->expandedDesktopTenants = array_filter($this->expandedDesktopTenants, fn($id) => $id !== $tenantId);
        } else {
            $this->expandedDesktopTenants[] = $tenantId;
        }
    }

    #[On('edit-payment')]
    public function openEditModal(int $tenantId): void
    {
        $this->dispatch('open-payment-modal', tenantId: $tenantId);
    }

    #[On('payment-updated')]
    public function refreshList(): void
    {
        $this->resetTable();
    }

    public function markAsPaid(int $tenantId): void
    {
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            Notification::make()
                ->title('입주자 정보를 찾을 수 없습니다')
                ->danger()
                ->send();
            return;
        }

        $tenant->update([
            'payment_status' => 'paid',
            'actual_payment_date' => now()->toDateString(),
        ]);

        Notification::make()
            ->title('납부 완료 처리되었습니다')
            ->success()
            ->send();
    }

    public function openCashReceiptModal(int $tenantId): void
    {
        $this->dispatch('open-cash-receipt-modal', tenantId: $tenantId);
    }

    public function viewCashReceipt(int $tenantId): void
    {
        $this->dispatch('view-cash-receipt', tenantId: $tenantId);
    }

    public function toggleRentSimulation(): void
    {
        $this->showRentSimulation = !$this->showRentSimulation;
    }

    public function adjustRent(int $amount): void
    {
        $this->rentAdjustment += $amount;
    }

    #[Computed]
    public function currentBranch(): ?Branch
    {
        $branchId = session('current_branch_id');
        return $branchId ? Branch::find($branchId) : null;
    }

    #[Computed]
    public function groupedTenants()
    {
        $branchId = session('current_branch_id');

        $query = Tenant::with(['room', 'latestCashReceipt'])
            ->whereNotNull('room_id')
            ->whereHas('branch', fn($q) => $q->where('user_id', auth()->id()));

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('room', fn($rq) => $rq->where('room_number', 'like', "%{$search}%"));
            });
        }

        if ($this->filterProcessStatus) {
            $status = $this->filterProcessStatus;
            match ($status) {
                'checked_in' => $query->whereHas('room', fn($q) =>
                    $q->whereNotNull('check_in_completed_at')
                      ->whereNull('check_out_completed_at')
                      ->whereNull('cleaning_status')
                ),
                'checked_out' => $query->whereHas('room', fn($q) =>
                    $q->where(fn($q2) =>
                        $q2->whereNotNull('check_out_completed_at')
                           ->orWhereIn('cleaning_status', ['waiting', 'completed'])
                    )
                ),
                'scheduled' => $query->whereHas('room', function ($q) {
                    $q->whereNotNull('move_in_date')
                      ->whereDate('move_in_date', '>=', now()->format('Y-m-d'))
                      ->whereNull('check_in_completed_at')
                      ->whereNull('check_out_completed_at')
                      ->whereNull('cleaning_status');
                }),
                default => null,
            };
        }

        if ($this->filterPaymentStatus) {
            $query->where('payment_status', $this->filterPaymentStatus);
        }

        if ($this->filterPaymentMethod) {
            $query->where('payment_method', $this->filterPaymentMethod);
        }

        $tenants = $query->get();

        $grouped = $tenants->groupBy(fn($t) => $t->room?->room_number ?? 'unknown');

        return $grouped
            ->map(fn($group) => $group->sortBy(fn($t) => $t->process_status_priority)->values())
            ->sortBy(function ($tenants, $roomNumber) {
                preg_match('/(\d+)/', $roomNumber, $matches);
                return isset($matches[1]) ? (int) $matches[1] : PHP_INT_MAX;
            });
    }

    #[Computed]
    public function monthlyIncomeData(): array
    {
        $branchId = session('current_branch_id');

        $query = Tenant::whereNotNull('room_id')
            ->where('payment_status', 'paid')
            ->whereHas('branch', fn($q) => $q->where('user_id', auth()->id()))
            ->whereYear('actual_payment_date', $this->analysisYear)
            ->join('rooms', 'tenants.room_id', '=', 'rooms.id');

        if ($branchId) {
            $query->where('tenants.branch_id', $branchId);
        }

        $results = $query
            ->selectRaw('MONTH(tenants.actual_payment_date) as m, SUM(rooms.monthly_rent) as total')
            ->groupByRaw('MONTH(tenants.actual_payment_date)')
            ->pluck('total', 'm');

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = (int) ($results[$m] ?? 0);
        }

        return $months;
    }

    #[Computed]
    public function profitAnalysisData(): array
    {
        // Derive from grouped tenants to avoid a duplicate query
        $allTenants = $this->groupedTenants->flatten();
        $totalRent = $allTenants->sum(fn($t) => $t->room?->monthly_rent ?? 0);

        $branchId = session('current_branch_id');
        $roomQuery = Room::whereHas('branch', fn($q) => $q->where('user_id', auth()->id()));
        if ($branchId) {
            $roomQuery->where('branch_id', $branchId);
        }
        $totalRoomCount = $roomQuery->count();

        $goshiwonRent = (int) ($totalRent * 0.4);
        $utilities = (int) ($totalRent * 0.08);
        $otherCosts = (int) ($totalRent * 0.05);
        $netProfit = $totalRent - $goshiwonRent - $utilities - $otherCosts;
        $profitRate = $totalRent > 0 ? round(($netProfit / $totalRent) * 100, 2) : 0;

        return [
            'totalRent' => $totalRent,
            'goshiwonRent' => $goshiwonRent,
            'utilities' => $utilities,
            'otherCosts' => $otherCosts,
            'netProfit' => $netProfit,
            'profitRate' => $profitRate,
            'roomCount' => $totalRoomCount,
        ];
    }

    public function getSimulationResult(): array
    {
        $profitData = $this->profitAnalysisData;
        $roomCount = $profitData['roomCount'];

        $monthlyExtra = $roomCount * $this->rentAdjustment;
        $annualExtra = $monthlyExtra * 12;

        $newTotal = $profitData['totalRent'] + $this->rentAdjustment * $roomCount;
        $newNet = $profitData['netProfit'] + $monthlyExtra;
        $newRate = $newTotal > 0 ? round(($newNet / $newTotal) * 100, 2) : 0;

        return [
            'monthlyExtra' => $monthlyExtra,
            'annualExtra' => $annualExtra,
            'newRate' => $newRate,
            'roomCount' => $roomCount,
        ];
    }

    public function getHeading(): string
    {
        $branch = $this->currentBranch;
        return $branch ? "{$branch->name} 수납 관리" : '수납 관리';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('결제 등록')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->action(fn () => $this->dispatch('open-payment-modal')),
        ];
    }
}
