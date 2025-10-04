<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id',
        'room_id',
        'name',
        'phone',
        'room_number',
        'room_type',
        'monthly_rent',
        'move_in_date',
        'last_payment_date',
        'payment_method',
        'payment_status',
        'move_out_date',
        'status',
        'is_blacklisted',
        'blacklist_memo',
    ];

    protected $casts = [
        'move_in_date' => 'date',
        'last_payment_date' => 'date',
        'move_out_date' => 'date',
        'is_blacklisted' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            'paid' => '납부완료',
            'overdue' => '연체',
            'pending' => '미납',
            default => $this->payment_status,
        };
    }

    public function getPaymentStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            'paid' => 'success',
            'overdue' => 'danger',
            'pending' => 'warning',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => '입주중',
            'inactive' => '퇴실',
            default => $this->status,
        };
    }
}
