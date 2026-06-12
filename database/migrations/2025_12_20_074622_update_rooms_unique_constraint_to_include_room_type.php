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
            // Add a temporary index on branch_id to support the foreign key constraint
            // while we drop the unique index that acts as the index for it.
            $table->index('branch_id', 'temp_branch_id_index');
            
            $table->dropUnique('rooms_branch_id_room_number_unique');
            
            $table->unique(['branch_id', 'room_number', 'room_type'], 'rooms_branch_id_room_number_room_type_unique');
            
            // We can now drop the temp index because the new unique index
            // (branch_id, room_number, room_type) starts with branch_id, so it can support the FK.
            $table->dropIndex('temp_branch_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Same dance for rollback
            $table->index('branch_id', 'temp_branch_id_index');
            
            $table->dropUnique('rooms_branch_id_room_number_room_type_unique');
            
            $table->unique(['branch_id', 'room_number'], 'rooms_branch_id_room_number_unique');
            
            $table->dropIndex('temp_branch_id_index');
        });
    }
};
