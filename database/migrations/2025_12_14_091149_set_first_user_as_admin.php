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
        // Set the first user as admin
        $user = \App\Models\User::orderBy('id')->first();
        if ($user) {
            $user->update(['is_admin' => true]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset all admins
        \App\Models\User::query()->update(['is_admin' => false]);
    }
};
