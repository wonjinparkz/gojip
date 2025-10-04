<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FloorRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'floor_number',
        'room_type',
        'monthly_rent',
        'room_count',
        'excluded_room_numbers',
    ];

    protected $casts = [
        'floor_number' => 'integer',
        'monthly_rent' => 'integer',
        'room_count' => 'integer',
        'excluded_room_numbers' => 'array',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
