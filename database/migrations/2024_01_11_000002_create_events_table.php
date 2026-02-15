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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('term_id')->constrained('academic_terms')->onDelete('cascade');
            $table->date('expected_date');
            $table->string('venue');
            $table->decimal('grand_total', 15, 2)->default(0.00);
            $table->string('team_member_1', 50)->nullable();
            $table->string('team_member_2', 50)->nullable();
            $table->string('team_member_3', 50)->nullable();
            $table->enum('status', [
                'pending_president',
                'pending_patron', 
                'pending_hod',
                'pending_sa',
                'approved',
                'rejected',
                'completed'
            ])->default('pending_president');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};