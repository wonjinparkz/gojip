<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('mgt_key')->unique();
            $table->string('invoicer_corp_num', 10);
            $table->string('invoicee_corp_num', 10);
            $table->string('nts_confirm_num')->nullable()->index();
            $table->string('state_code', 8)->nullable();
            $table->string('item_name')->default('월 임대료');
            $table->integer('supply_cost');
            $table->integer('tax');
            $table->integer('total_amount');
            $table->date('write_date');
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('sent_to_nts_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_invoices');
    }
};
