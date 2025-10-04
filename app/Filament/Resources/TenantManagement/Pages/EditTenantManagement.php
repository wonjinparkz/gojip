<?php

namespace App\Filament\Resources\TenantManagement\Pages;

use App\Filament\Resources\TenantManagement\TenantManagementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTenantManagement extends EditRecord
{
    protected static string $resource = TenantManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
