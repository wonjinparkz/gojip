<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('business_number', 10)->nullable()->after('phone');
            $table->string('corp_name')->nullable()->after('business_number');
            $table->string('corp_ceo_name')->nullable()->after('corp_name');
            $table->string('corp_address')->nullable()->after('corp_ceo_name');
            $table->string('corp_business_type')->nullable()->after('corp_address');
            $table->string('corp_business_class')->nullable()->after('corp_business_type');
            $table->string('email')->nullable()->after('corp_business_class');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'business_number',
                'corp_name',
                'corp_ceo_name',
                'corp_address',
                'corp_business_type',
                'corp_business_class',
                'email',
            ]);
        });
    }
};
