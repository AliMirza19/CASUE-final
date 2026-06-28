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
        // 1. Candidate Applications
        Schema::create('candidate_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->text('manifesto_text');
            $table->string('status')->default('pending'); // pending, patron_shortlisted, hod_rejected, finalized_president
            $table->timestamps();
        });

        // 2. Selection Committees
        Schema::create('selection_committees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hod_id')->constrained('users');
            $table->foreignId('patron_id')->constrained('users');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Committee Members (Faculty + HOD + Patron)
        Schema::create('committee_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_id')->constrained('selection_committees')->onDelete('cascade');
            $table->foreignId('faculty_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 4. Committee Messages
        Schema::create('committee_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_id')->constrained('selection_committees')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->text('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('committee_messages');
        Schema::dropIfExists('committee_members');
        Schema::dropIfExists('selection_committees');
        Schema::dropIfExists('candidate_applications');
    }
};
