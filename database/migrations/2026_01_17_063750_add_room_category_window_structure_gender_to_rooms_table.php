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
            $table->string('room_category')->nullable()->after('room_type'); // 호실 유형: 원룸, 샤워룸, 미니룸 등
            $table->string('window_structure')->nullable()->after('room_category'); // 창 구조: 외창, 내창
            $table->string('gender')->nullable()->after('window_structure'); // 남녀 구분: 남성, 여성, 혼합
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['room_category', 'window_structure', 'gender']);
        });
    }
};
