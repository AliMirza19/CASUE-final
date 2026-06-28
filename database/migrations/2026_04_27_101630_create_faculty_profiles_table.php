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
        Schema::create('faculty_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title'); // Dr., Prof., etc.
            $table->string('gender');
            $table->string('cnic_passport');
            $table->date('dob');
            $table->string('mobile_number');
            $table->text('address');
            $table->string('province');
            $table->string('city');
            $table->string('contract_type'); // Permanent, Visiting, etc.
            $table->string('academic_rank'); // Assistant Professor, Professor, etc.
            $table->date('joining_date');
            
            // Highest Degree Details
            $table->string('highest_degree_name');
            $table->string('highest_degree_type');
            $table->string('field_of_study');
            $table->string('degree_country');
            $table->string('university_name');
            $table->date('degree_start_date');
            $table->date('degree_end_date')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty_profiles');
    }
};
