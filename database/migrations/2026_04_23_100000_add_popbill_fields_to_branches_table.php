<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->string('business_number', 10)->nullable()->after('phone');
            $table->string('ceo_name')->nullable()->after('business_number');
            $table->string('business_address')->nullable()->after('ceo_name');
            $table->string('business_type')->nullable()->after('business_address');
            $table->string('business_class')->nullable()->after('business_type');
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn([
                'business_number',
                'ceo_name',
                'business_address',
                'business_type',
                'business_class',
            ]);
        });
    }
};
