<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'address',
        'phone',
        'start_floor',
        'end_floor',
    ];

    protected $casts = [
        'start_floor' => 'integer',
        'end_floor' => 'integer',
    ];

    protected $appends = ['phone_numbers'];

    /**
     * 전화번호 배열을 콤마로 구분된 문자열로 변환하여 저장하고,
     * 읽을 때는 다시 배열로 변환
     */
    protected function phoneNumbers(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->phone)) {
                    return [['number' => '']];
                }

                $phones = explode(',', $this->phone);
                return array_map(function($phone) {
                    return ['number' => trim($phone)];
                }, $phones);
            },
            set: function ($value) {
                if (is_array($value)) {
                    $phones = array_filter(array_map(function($item) {
                        return trim($item['number'] ?? '');
                    }, $value));

                    return ['phone' => implode(',', $phones)];
                }
                return ['phone' => $value];
            }
        );
    }

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
