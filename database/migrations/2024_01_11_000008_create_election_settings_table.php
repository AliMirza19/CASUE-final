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
        Schema::create('election_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->constrained('academic_terms')->onDelete('cascade');
            $table->boolean('voting_enabled')->default(false);
            $table->dateTime('voting_start_date')->nullable();
            $table->dateTime('voting_end_date')->nullable();
            $table->timestamps();
            
            $table->unique('term_id', 'unique_term_election');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('election_settings');
    }
};