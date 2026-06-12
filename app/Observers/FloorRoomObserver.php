<?php

namespace App\Observers;

use App\Models\FloorRoom;
use App\Models\Room;

class FloorRoomObserver
{
    /**
     * Handle the FloorRoom "saved" event.
     */
    public function saved(FloorRoom $floorRoom): void
    {
        // Only run if room_count has changed or it's a new record (though new records usually start at 0)
        // Actually, if created with >0 count directly, we want this to run.
        if ($floorRoom->room_count > 0) {
            $this->syncRooms($floorRoom);
        }
    }

    protected function syncRooms(FloorRoom $floorRoom): void
    {
        for ($i = 1; $i <= $floorRoom->room_count; $i++) {
            // Room Numbering: Floor + 2-digit index (e.g., 201, 1001)
            $roomNumber = $floorRoom->floor_number . str_pad($i, 2, '0', STR_PAD_LEFT);

            // Check if exists
            $exists = Room::where('branch_id', $floorRoom->branch_id)
                ->where('room_number', $roomNumber)
                ->where('room_type', $floorRoom->room_type)
                ->exists();

            if (!$exists) {
                Room::create([
                    'branch_id' => $floorRoom->branch_id,
                    'room_number' => $roomNumber,
                    'floor' => $floorRoom->floor_number,
                    'room_type' => $floorRoom->room_type,
                    'monthly_rent' => $floorRoom->monthly_rent,
                    'deposit' => 0, // Default deposit
                    'status' => 'available',
                ]);
            }
        }
    }
}
