<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'room_number',
        'floor',
        'room_type',
        'room_category',
        'window_structure',
        'gender',
        'monthly_rent',
        'deposit',
        'status',
        'move_in_date',
        'move_out_date',
        'tenant_name',
        'check_in_completed_at',
        'check_out_completed_at',
        'cleaning_status',
    ];

    protected $casts = [
        'floor' => 'integer',
        'monthly_rent' => 'integer',
        'deposit' => 'integer',
        'move_in_date' => 'date',
        'move_out_date' => 'date',
        'check_in_completed_at' => 'datetime',
        'check_out_completed_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * 현재 입주 중인 입주자 (입주일이 오늘 이전, 퇴실일이 없거나 오늘 이후 또는 퇴실일 미정)
     */
    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class)
            ->where(function($q) {
                $q->whereNull('move_out_date')
                  ->orWhere('indefinite_move_out', true)
                  ->orWhereDate('move_out_date', '>=', now());
            })
            ->whereDate('move_in_date', '<=', now())
            ->orderBy('move_in_date', 'desc');
    }

    /**
     * 미래 입주 예정자 (입실일이 미래인 입주자)
     */
    public function futureTenants(): HasMany
    {
        return $this->hasMany(Tenant::class)
            ->whereDate('move_in_date', '>', now())
            ->where(function($query) {
                $query->whereNull('move_out_date')
                    ->orWhere('indefinite_move_out', true)
                    ->orWhereDate('move_out_date', '>=', now());
            })
            ->orderBy('move_in_date', 'asc');
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
