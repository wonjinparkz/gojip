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
        'gender',
        'room_number',
        'room_type',
        'monthly_rent',
        'move_in_date',
        'last_payment_date',
        'payment_method',
        'payment_status',
        'move_out_date',
        'indefinite_move_out',
        'status',
        'is_blacklisted',
        'blacklist_memo',
    ];

    protected $casts = [
        'move_in_date' => 'date',
        'last_payment_date' => 'date',
        'move_out_date' => 'date',
        'indefinite_move_out' => 'boolean',
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
            'waiting' => '대기',
            default => $this->payment_status,
        };
    }

    public function getPaymentStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            'paid' => 'success',
            'overdue' => 'danger',
            'pending' => 'warning',
            'waiting' => 'gray',
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

    /**
     * 대시보드 일정 정보를 기반으로 한 입주자 처리 상태
     */
    public function getProcessStatusAttribute(): string
    {
        // Room 정보가 없으면 기본 상태
        if (!$this->room_id || !$this->room) {
            return 'pending';
        }

        $room = $this->room;

        // 퇴실 완료 확인
        if ($room->check_out_completed_at) {
            return 'checked_out';
        }

        // 청소 상태 확인 (청소 대기 또는 완료)
        if (in_array($room->cleaning_status, ['waiting', 'completed'])) {
            return 'checked_out';
        }

        // 입실 완료 확인
        if ($room->check_in_completed_at) {
            return 'checked_in';
        }

        // 입실 예정
        if ($this->move_in_date && $this->move_in_date->isFuture()) {
            return 'scheduled';
        }

        // 기본 상태 (입주 대기)
        return 'pending';
    }

    /**
     * 처리 상태 라벨
     */
    public function getProcessStatusLabelAttribute(): string
    {
        return match($this->process_status) {
            'checked_in' => '입실자',
            'checked_out' => '퇴실자',
            'scheduled' => '입실예정',
            'pending' => '대기',
            default => '-',
        };
    }

    /**
     * 처리 상태 색상
     */
    public function getProcessStatusColorAttribute(): string
    {
        return match($this->process_status) {
            'checked_in' => 'success',
            'checked_out' => 'danger',
            'scheduled' => 'warning',
            'pending' => 'gray',
            default => 'gray',
        };
    }
}
