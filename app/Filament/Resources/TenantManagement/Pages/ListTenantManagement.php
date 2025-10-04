<?php

namespace App\Filament\Resources\TenantManagement\Pages;

use App\Filament\Resources\TenantManagement\TenantManagementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTenantManagement extends ListRecords
{
    protected static string $resource = TenantManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
