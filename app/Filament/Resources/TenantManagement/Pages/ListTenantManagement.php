<?php

namespace App\Filament\Resources\TenantManagement\Pages;

use App\Filament\Resources\TenantManagement\TenantManagementResource;
use App\Models\Branch;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListTenantManagement extends ListRecords
{
    protected static string $resource = TenantManagementResource::class;

    protected string $view = 'filament.resources.tenant-management.pages.list-tenant-management';

    public function getHeading(): string
    {
        $branchId = session('current_branch_id');
        $branch = Branch::find($branchId);

        return $branch ? "{$branch->name} 입주자 관리" : '입주자 관리';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('입주자 추가하기')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->action(fn () => $this->dispatch('open-tenant-management-modal')),
        ];
    }
}
