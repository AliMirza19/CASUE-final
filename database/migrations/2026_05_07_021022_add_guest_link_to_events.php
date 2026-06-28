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
        Schema::table('events', function (Blueprint $table) {
            $table->string('guest_speaker_profile_link')->nullable()->after('guest_speaker_designation');
        });

        Schema::table('event_items', function (Blueprint $table) {
            $table->dropColumn('unit_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('guest_speaker_profile_link');
        });

        Schema::table('event_items', function (Blueprint $table) {
            $table->decimal('unit_rate', 10, 2)->default(0.00)->after('quantity');
        });
    }
};
