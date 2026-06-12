<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsHistory extends Model
{
    protected $table = 'sms_histories';

    protected $fillable = [
        'branch_id',
        'recipient',
        'room',
        'phone',
        'content',
        'type',
        'status',
        'cost',
        'sent_at',
        'scheduled_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];
}
