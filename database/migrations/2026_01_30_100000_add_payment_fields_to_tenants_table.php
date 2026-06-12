<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->integer('payment_due_day')->nullable()->after('last_payment_date'); // 매월 결제일 (1~31)
            $table->date('actual_payment_date')->nullable()->after('payment_due_day'); // 실제 결제일
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['payment_due_day', 'actual_payment_date']);
        });
    }
};
