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
        Schema::create('student_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Basic Info (already in users: name, father_name, cnic, email, reg_id as roll_no, contact_number as phone)
            // Additional Info
            $table->enum('gender', ['M', 'F']);
            $table->date('admission_date');
            $table->string('nationality');
            $table->string('passport_number')->nullable();
            $table->date('dob');
            $table->string('domicile_district');
            $table->string('domicile_province');
            $table->text('mailing_address');
            $table->string('city');
            
            // SSC Academic Info
            $table->string('ssc_degree_name');
            $table->string('ssc_board_name');
            $table->integer('ssc_total_marks');
            $table->integer('ssc_obtained_marks');
            
            // HSSC Academic Info
            $table->string('hssc_degree_name');
            $table->enum('hssc_nomenclature', ['1', '2', '3']); // 1=Int-Math, 2=A-Math, 3=Pre-Med
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
        Schema::dropIfExists('student_details');
    }
};
