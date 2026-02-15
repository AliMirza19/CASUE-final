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
            // Remove team member columns
            $table->dropColumn(['team_member_1', 'team_member_2', 'team_member_3']);

            // Add guest speaker and faculty mentor columns
            $table->string('guest_speaker_name')->nullable()->after('grand_total');
            $table->string('guest_speaker_designation')->nullable()->after('guest_speaker_name');
            $table->foreignId('faculty_mentor_id')->nullable()->after('guest_speaker_designation')
                  ->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['faculty_mentor_id']);
            $table->dropColumn(['guest_speaker_name', 'guest_speaker_designation', 'faculty_mentor_id']);

            $table->string('team_member_1', 50)->nullable();
            $table->string('team_member_2', 50)->nullable();
            $table->string('team_member_3', 50)->nullable();
        });
    }
};
