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
            $table->boolean('is_short_term')->default(false)->after('status');
            $table->integer('short_term_monthly_rent')->nullable()->after('is_short_term');
            $table->integer('short_term_deposit')->nullable()->after('short_term_monthly_rent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['is_short_term', 'short_term_monthly_rent', 'short_term_deposit']);
        });
    }
};
