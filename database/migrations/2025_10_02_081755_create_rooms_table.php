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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('room_number'); // 호실 번호 (예: 201, 202)
            $table->integer('floor'); // 층수
            $table->string('room_type'); // 호실 타입 (스탠다드룸 등)
            $table->integer('monthly_rent'); // 월세
            $table->integer('deposit')->default(0); // 보증금
            $table->string('status')->default('available'); // 상태: available(입주가능), occupied(입주중), maintenance(수리중)
            $table->date('move_in_date')->nullable(); // 입주일
            $table->date('move_out_date')->nullable(); // 퇴실일
            $table->string('tenant_name')->nullable(); // 입주자명
            $table->timestamps();

            $table->unique(['branch_id', 'room_number']); // 같은 지점에 같은 호실번호가 없도록
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
