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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // 단기숙박이 아닌 경우 관련 필드 null 처리
        if (!($data['is_short_term'] ?? false)) {
            $data['short_term_monthly_rent'] = null;
            $data['short_term_deposit'] = null;
        }

        return $data;
    }
}
