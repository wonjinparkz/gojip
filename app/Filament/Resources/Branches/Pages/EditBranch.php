<?php

namespace App\Filament\Resources\Branches\Pages;

use App\Filament\Resources\Branches\BranchResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBranch extends EditRecord
{
    protected static string $resource = BranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // phone_numbers 배열을 phone 필드로 변환
        if (isset($data['phone_numbers']) && is_array($data['phone_numbers'])) {
            $phones = array_filter(array_map(function($item) {
                return trim($item['number'] ?? '');
            }, $data['phone_numbers']));

            $data['phone'] = implode(',', $phones);
            unset($data['phone_numbers']);
        }

        return $data;
    }
}
