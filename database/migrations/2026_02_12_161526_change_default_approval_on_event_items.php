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
        Schema::table('event_items', function (Blueprint $table) {
            $table->boolean('is_approved_by_patron')->default(true)->change();
        });

        // Also update existing items to be approved by default
        \Illuminate\Support\Facades\DB::table('event_items')->update([
            'is_approved_by_patron' => true,
            'is_approved_by_hod' => true
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_items', function (Blueprint $table) {
            $table->boolean('is_approved_by_patron')->default(false)->change();
        });
    }
};
