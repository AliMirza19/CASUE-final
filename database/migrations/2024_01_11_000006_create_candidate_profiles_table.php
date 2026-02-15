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
        Schema::create('candidate_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->text('manifesto');
            $table->string('photo_url', 500)->nullable();
            $table->text('experience')->nullable();
            $table->string('vp_name')->nullable();
            $table->enum('status', ['pending_patron', 'approved', 'rejected'])->default('pending_patron');
            $table->text('patron_feedback')->nullable();
            $table->timestamps();
            
            $table->unique('student_id', 'unique_student_candidate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_profiles');
    }
};