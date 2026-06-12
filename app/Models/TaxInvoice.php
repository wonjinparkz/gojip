<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'mgt_key',
        'invoicer_corp_num',
        'invoicee_corp_num',
        'nts_confirm_num',
        'state_code',
        'item_name',
        'supply_cost',
        'tax',
        'total_amount',
        'write_date',
        'issued_at',
        'sent_to_nts_at',
    ];

    protected $casts = [
        'write_date' => 'date',
        'issued_at' => 'datetime',
        'sent_to_nts_at' => 'datetime',
        'supply_cost' => 'integer',
        'tax' => 'integer',
        'total_amount' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public static function generateMgtKey(): string
    {
        return 'TI'.now()->format('YmdHis').strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }
}
