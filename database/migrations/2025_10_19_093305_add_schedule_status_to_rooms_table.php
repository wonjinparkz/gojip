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
        Schema::table('rooms', function (Blueprint $table) {
            $table->dateTime('check_in_completed_at')->nullable()->after('move_in_date');
            $table->dateTime('check_out_completed_at')->nullable()->after('move_out_date');
            $table->string('cleaning_status')->nullable()->after('status'); // waiting, completed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['check_in_completed_at', 'check_out_completed_at', 'cleaning_status']);
        });
    }
};
