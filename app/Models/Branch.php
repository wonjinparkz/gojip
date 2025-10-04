<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'start_floor',
        'end_floor',
    ];

    protected $casts = [
        'start_floor' => 'integer',
        'end_floor' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function floorRooms(): HasMany
    {
        return $this->hasMany(FloorRoom::class);
    }

    public function extraRooms(): HasMany
    {
        return $this->hasMany(ExtraRoom::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
