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
        Schema::create('faculty_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Faculty Specific Info
            $table->string('sr_no')->nullable();
            $table->string('title'); // Dr., Mr., Ms., etc.
            $table->enum('gender', ['M', 'F', 'Other'])->nullable();
            $table->date('dob');
            $table->string('province');
            $table->string('city');
            $table->string('address');
            $table->string('contract_type'); // Permanent, Visiting, etc.
            $table->string('academic_rank'); // Professor, Lecturer, etc.
            $table->date('joining_date');
            $table->date('leaving_date')->nullable();
            
            // Highest Degree Details
            $table->string('degree_name');
            $table->string('degree_type');
            $table->string('field_of_study');
            $table->string('degree_awarding_country');
            $table->string('university_name');
            $table->date('degree_start_date');
            $table->date('degree_end_date');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty_details');
    }
};
