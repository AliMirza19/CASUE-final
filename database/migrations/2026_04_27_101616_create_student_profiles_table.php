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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('roll_no')->unique(); // RollNo (use as primary ID/Reg ID)
            $table->string('father_name');
            $table->string('gender');
            $table->date('admission_date');
            $table->string('nationality');
            $table->string('cnic_number')->nullable();
            $table->string('passport_number')->nullable();
            $table->date('dob');
            $table->string('phone_number');
            $table->string('domicile_district');
            $table->string('domicile_province');
            $table->text('mailing_address');
            $table->string('city');
            
            // SSC Details
            $table->string('ssc_degree_name');
            $table->string('ssc_board_name');
            $table->integer('ssc_total_marks');
            $table->integer('ssc_obtained_marks');
            
            // HSSC Details
            $table->string('hssc_degree_name');
            $table->string('hssc_degree_nomenclature');
            $table->string('hssc_board_name');
            $table->integer('hssc_total_marks');
            $table->integer('hssc_obtained_marks');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
