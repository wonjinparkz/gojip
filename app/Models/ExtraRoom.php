<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtraRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'room_type',
        'monthly_rent',
        'room_count',
    ];

    protected $casts = [
        'monthly_rent' => 'integer',
        'room_count' => 'integer',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
