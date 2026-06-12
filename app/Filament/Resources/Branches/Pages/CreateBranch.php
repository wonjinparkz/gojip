<?php

namespace App\Filament\Resources\Branches\Pages;

use App\Filament\Resources\Branches\BranchResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class CreateBranch extends CreateRecord
{
    protected static string $resource = BranchResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

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

    protected function handleRecordCreation(array $data): Model
    {
        $record = static::getModel()::create($data);

        Log::info('Branch created successfully.', ['branch_id' => $record->id, 'branch_name' => $record->name]);

        // Prioritize 'floor_settings' from the form if available (Create page reactive repeater)
        if (isset($data['floor_settings']) && is_array($data['floor_settings'])) {
            foreach ($data['floor_settings'] as $setting) {
                // Ensure floor number matches range (sanity check)
                if ($setting['floor_number'] >= $record->start_floor && $setting['floor_number'] <= $record->end_floor) {
                    \App\Models\FloorRoom::create([
                        'branch_id' => $record->id,
                        'floor_number' => $setting['floor_number'],
                        'room_type' => $setting['room_type'] ?? 'Standard',
                        'monthly_rent' => $setting['monthly_rent'] ?? 0,
                        'room_count' => $setting['room_count'] ?? 0,
                        'excluded_room_numbers' => [],
                    ]);
                    Log::info('FloorRoom created from settings.', ['branch_id' => $record->id, 'floor_number' => $setting['floor_number']]);
                }
            }
        } 
        // Fallback to default loop if no settings provided (though form should provide them)
        elseif ($record->start_floor && $record->end_floor) {
            for ($i = $record->start_floor; $i <= $record->end_floor; $i++) {
                \App\Models\FloorRoom::create([
                    'branch_id' => $record->id,
                    'floor_number' => $i,
                    'room_type' => 'Standard',
                    'monthly_rent' => 0,
                    'room_count' => 1,
                    'excluded_room_numbers' => [],
                ]);
                Log::info('FloorRoom created (default loop).', ['branch_id' => $record->id, 'floor_number' => $i]);
            }
        }

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
