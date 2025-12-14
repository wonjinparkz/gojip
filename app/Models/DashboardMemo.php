<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class DashboardMemo extends Model
{
    protected $fillable = [
        'branch_id',
        'user_id',
        'type',
        'date',
        'week_start',
        'month',
        'content',
        'is_collapsed',
    ];

    protected $casts = [
        'date' => 'date',
        'week_start' => 'date',
        'is_collapsed' => 'boolean',
    ];

    /**
     * Get the user that owns the memo.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the branch that owns the memo.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Scope a query to only include daily memos.
     */
    public function scopeDaily($query, $date = null)
    {
        $date = $date ?? now()->toDateString();
        return $query->where('type', 'daily')->where('date', $date);
    }

    /**
     * Scope a query to only include weekly memos.
     */
    public function scopeWeekly($query, $weekStart = null)
    {
        $weekStart = $weekStart ?? now()->startOfWeek()->toDateString();
        return $query->where('type', 'weekly')->where('week_start', $weekStart);
    }

    /**
     * Scope a query to only include monthly memos.
     */
    public function scopeMonthly($query, $month = null)
    {
        $month = $month ?? now()->format('Y-m');
        return $query->where('type', 'monthly')->where('month', $month);
    }

    /**
     * Scope a query for a specific branch.
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope a query for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get or create a memo for the current user, branch, and date/period.
     */
    public static function getOrCreate($userId, $branchId, $type, $date = null)
    {
        $attributes = [
            'user_id' => $userId,
            'branch_id' => $branchId,
            'type' => $type,
        ];

        switch ($type) {
            case 'daily':
                $attributes['date'] = $date ?? now()->toDateString();
                break;
            case 'weekly':
                $attributes['week_start'] = $date ?? now()->startOfWeek()->toDateString();
                break;
            case 'monthly':
                $attributes['month'] = $date ?? now()->format('Y-m');
                break;
        }

        return static::firstOrCreate($attributes);
    }
}
