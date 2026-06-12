<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('extra_rooms', function (Blueprint $table) {
            $table->string('room_category')->nullable()->after('room_type');
            $table->string('window_structure')->nullable()->after('room_category');
            $table->string('gender')->nullable()->after('window_structure');
        });
    }

    public function down(): void
    {
        Schema::table('extra_rooms', function (Blueprint $table) {
            $table->dropColumn(['room_category', 'window_structure', 'gender']);
        });
    }
};
