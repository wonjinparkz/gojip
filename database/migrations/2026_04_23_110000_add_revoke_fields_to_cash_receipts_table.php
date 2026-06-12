<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_receipts', function (Blueprint $table) {
            $table->string('revoke_mgt_key')->nullable()->after('popbill_item_key');
            $table->string('revoke_confirm_num')->nullable()->after('revoke_mgt_key');
            $table->timestamp('canceled_at')->nullable()->after('issued_at');
            $table->string('cancel_memo')->nullable()->after('canceled_at');
        });
    }

    public function down(): void
    {
        Schema::table('cash_receipts', function (Blueprint $table) {
            $table->dropColumn(['revoke_mgt_key', 'revoke_confirm_num', 'canceled_at', 'cancel_memo']);
        });
    }
};
