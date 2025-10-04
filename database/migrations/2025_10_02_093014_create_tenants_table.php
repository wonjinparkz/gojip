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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('room_number')->nullable();
            $table->string('room_type')->nullable();
            $table->integer('monthly_rent')->default(0);
            $table->date('move_in_date')->nullable();
            $table->date('last_payment_date')->nullable();
            $table->string('payment_method')->nullable(); // 카드, 계좌이체, 현금 등
            $table->string('payment_status')->default('pending'); // pending, paid, overdue
            $table->date('move_out_date')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->boolean('is_blacklisted')->default(false);
            $table->text('blacklist_memo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
