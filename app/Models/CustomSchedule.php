<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'content',
        'category',
        'schedule_date',
        'is_completed',
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'is_completed' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
