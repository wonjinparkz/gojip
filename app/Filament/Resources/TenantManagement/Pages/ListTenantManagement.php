<?php

namespace App\Filament\Resources\TenantManagement\Pages;

use App\Filament\Resources\TenantManagement\TenantManagementResource;
use App\Models\Branch;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTenantManagement extends ListRecords
{
    protected static string $resource = TenantManagementResource::class;

    public function getHeading(): string
    {
        $branchId = session('current_branch_id');
        $branch = Branch::find($branchId);

        return $branch ? "{$branch->name} 입주자 관리" : '입주자 관리';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('입주자 추가하기')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }
}
