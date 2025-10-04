<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'room_number',
        'floor',
        'room_type',
        'monthly_rent',
        'deposit',
        'status',
        'move_in_date',
        'move_out_date',
        'tenant_name',
    ];

    protected $casts = [
        'floor' => 'integer',
        'monthly_rent' => 'integer',
        'deposit' => 'integer',
        'move_in_date' => 'date',
        'move_out_date' => 'date',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'available' => '입주가능',
            'occupied' => '입주중',
            'maintenance' => '수리중',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'available' => 'success',
            'occupied' => 'primary',
            'maintenance' => 'warning',
            default => 'gray',
        };
    }
}
