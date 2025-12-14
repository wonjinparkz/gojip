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
        Schema::create('dashboard_memos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['daily', 'weekly', 'monthly'])->default('daily');
            $table->date('date')->nullable()->comment('For daily memos');
            $table->date('week_start')->nullable()->comment('For weekly memos - Monday of the week');
            $table->string('month', 7)->nullable()->comment('For monthly memos - YYYY-MM format');
            $table->text('content')->nullable();
            $table->boolean('is_collapsed')->default(false);
            $table->timestamps();

            // Create indexes for better query performance
            $table->index(['branch_id', 'user_id', 'type', 'date']);
            $table->index(['branch_id', 'user_id', 'type', 'week_start']);
            $table->index(['branch_id', 'user_id', 'type', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_memos');
    }
};
