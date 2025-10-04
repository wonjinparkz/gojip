<?php

namespace App\Filament\Resources\TenantManagement\Pages;

use App\Filament\Resources\TenantManagement\TenantManagementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantManagement extends CreateRecord
{
    protected static string $resource = TenantManagementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
