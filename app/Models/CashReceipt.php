<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'receipt_number',
        'popbill_mgt_key',
        'confirm_num',
        'trade_date',
        'state_code',
        'popbill_item_key',
        'revoke_mgt_key',
        'revoke_confirm_num',
        'issued_at',
        'canceled_at',
        'cancel_memo',
        'issued_date',
        'amount',
        'receipt_type',
        'identifier_number',
        'payment_method',
    ];

    protected $casts = [
        'issued_date' => 'date',
        'issued_at' => 'datetime',
        'canceled_at' => 'datetime',
        'amount' => 'integer',
    ];

    public function isCanceled(): bool
    {
        return $this->canceled_at !== null;
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function getReceiptTypeLabelAttribute(): string
    {
        return match($this->receipt_type) {
            'personal' => '개인 소득공제',
            'business' => '사업자 지출증빙',
            default => $this->receipt_type,
        };
    }

    public static function generateReceiptNumber(): string
    {
        return \Illuminate\Support\Facades\DB::transaction(function () {
            $prefix = 'CR' . now()->format('Ymd');
            $latest = static::where('receipt_number', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderBy('receipt_number', 'desc')
                ->first();

            $seq = $latest ? (int) substr($latest->receipt_number, -4) + 1 : 1;

            return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
        });
    }

    public static function generatePopbillMgtKey(): string
    {
        // 팝빌 문서상 24자 이내 영문/숫자 권장. 충돌 방지를 위해 timestamp+random 사용.
        return 'CB'.now()->format('YmdHis').strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }
}
