<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_receipts', function (Blueprint $table) {
            $table->string('popbill_mgt_key')->nullable()->after('receipt_number');
            $table->string('confirm_num')->nullable()->after('popbill_mgt_key');
            $table->string('trade_date', 8)->nullable()->after('confirm_num');
            $table->string('state_code', 8)->nullable()->after('trade_date');
            $table->string('popbill_item_key')->nullable()->after('state_code');
            $table->timestamp('issued_at')->nullable()->after('popbill_item_key');

            $table->index('popbill_mgt_key');
        });
    }

    public function down(): void
    {
        Schema::table('cash_receipts', function (Blueprint $table) {
            $table->dropIndex(['popbill_mgt_key']);
            $table->dropColumn([
                'popbill_mgt_key',
                'confirm_num',
                'trade_date',
                'state_code',
                'popbill_item_key',
                'issued_at',
            ]);
        });
    }
};
