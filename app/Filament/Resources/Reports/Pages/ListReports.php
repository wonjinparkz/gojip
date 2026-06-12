<?php

namespace App\Filament\Resources\Reports\Pages;

use App\Filament\Resources\Reports\ReportsResource;
use App\Models\Branch;
use App\Models\Room;
use App\Models\Tenant;
use Filament\Resources\Pages\ListRecords;
use Livewire\Attributes\Computed;

class ListReports extends ListRecords
{
    protected static string $resource = ReportsResource::class;

    protected string $view = 'filament.resources.reports.pages.list-reports';

    #[Computed]
    public function currentBranch(): ?Branch
    {
        $branchId = session('current_branch_id');
        return $branchId ? Branch::find($branchId) : null;
    }

    #[Computed]
    public function roomStats(): array
    {
        $branchId = session('current_branch_id');

        $baseQuery = Room::whereHas('branch', fn($q) => $q->where('user_id', auth()->id()));
        if ($branchId) {
            $baseQuery->where('branch_id', $branchId);
        }

        $totalRooms = (clone $baseQuery)->count();

        $occupiedRooms = (clone $baseQuery)
            ->whereNotNull('check_in_completed_at')
            ->whereNull('check_out_completed_at')
            ->where(function ($q) {
                $q->whereNull('cleaning_status')
                  ->orWhereNotIn('cleaning_status', ['waiting', 'completed']);
            })
            ->count();

        $vacantRooms = $totalRooms - $occupiedRooms;

        // 만료 예정 (30일 이내) 및 긴급 (7일 이내) - 단일 쿼리로 처리
        $tenantQuery = Tenant::whereNotNull('room_id')
            ->whereHas('branch', fn($q) => $q->where('user_id', auth()->id()))
            ->whereNotNull('move_out_date')
            ->where('indefinite_move_out', false)
            ->whereDate('move_out_date', '>=', now());

        if ($branchId) {
            $tenantQuery->where('branch_id', $branchId);
        }

        $expiringTenants = $tenantQuery
            ->whereDate('move_out_date', '<=', now()->addDays(30))
            ->selectRaw('COUNT(*) as total_30, SUM(CASE WHEN move_out_date <= ? THEN 1 ELSE 0 END) as total_7', [now()->addDays(7)->toDateString()])
            ->first();

        $expiringRooms = (int) ($expiringTenants->total_30 ?? 0);
        $expiringWithin7Days = (int) ($expiringTenants->total_7 ?? 0);

        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;
        $vacancyRate = $totalRooms > 0 ? round(($vacantRooms / $totalRooms) * 100, 1) : 0;

        return [
            'totalRooms' => $totalRooms,
            'occupiedRooms' => $occupiedRooms,
            'vacantRooms' => $vacantRooms,
            'expiringRooms' => $expiringRooms,
            'expiringWithin7Days' => $expiringWithin7Days,
            'occupancyRate' => $occupancyRate,
            'vacancyRate' => $vacancyRate,
        ];
    }

    #[Computed]
    public function monthlyOccupancyTrend(): array
    {
        $branchId = session('current_branch_id');
        $data = [];

        // 총 호실 수는 루프 밖에서 한 번만 조회
        $roomQuery = Room::whereHas('branch', fn($q) => $q->where('user_id', auth()->id()));
        if ($branchId) {
            $roomQuery->where('branch_id', $branchId);
        }
        $totalRooms = $roomQuery->count();

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $year = $date->year;
            $month = $date->month;
            $endOfMonth = $date->copy()->endOfMonth()->toDateString();

            $occupiedQuery = Room::whereHas('branch', fn($q) => $q->where('user_id', auth()->id()))
                ->whereNotNull('check_in_completed_at')
                ->whereDate('check_in_completed_at', '<=', $endOfMonth)
                ->where(function ($q) use ($endOfMonth) {
                    $q->whereNull('check_out_completed_at')
                      ->orWhereDate('check_out_completed_at', '>', $endOfMonth);
                })
                ->where(function ($q) {
                    $q->whereNull('cleaning_status')
                      ->orWhereNotIn('cleaning_status', ['waiting', 'completed']);
                });

            if ($branchId) {
                $occupiedQuery->where('branch_id', $branchId);
            }

            $occupiedRooms = $occupiedQuery->count();
            $rate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 2) : 0;

            $data[] = [
                'year' => $year,
                'month' => $month,
                'label' => $year . '년 ' . $month . '월',
                'rate' => $rate,
                'occupiedRooms' => $occupiedRooms,
                'totalRooms' => $totalRooms,
            ];
        }

        return $data;
    }

    public function getHeading(): string
    {
        $branch = $this->currentBranch;
        return $branch ? "{$branch->name} 운영 리포트" : '운영 리포트';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
