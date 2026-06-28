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
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('father_name')->nullable()->change();
            $table->string('gender')->nullable()->change();
            $table->date('admission_date')->nullable()->change();
            $table->string('nationality')->nullable()->change();
            $table->date('dob')->nullable()->change();
            $table->string('phone_number')->nullable()->change();
            $table->string('domicile_district')->nullable()->change();
            $table->string('domicile_province')->nullable()->change();
            $table->text('mailing_address')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('ssc_degree_name')->nullable()->change();
            $table->string('ssc_board_name')->nullable()->change();
            $table->integer('ssc_total_marks')->nullable()->change();
            $table->integer('ssc_obtained_marks')->nullable()->change();
            $table->string('hssc_degree_name')->nullable()->change();
            $table->string('hssc_degree_nomenclature')->nullable()->change();
            $table->string('hssc_board_name')->nullable()->change();
            $table->integer('hssc_total_marks')->nullable()->change();
            $table->integer('hssc_obtained_marks')->nullable()->change();
        });

        Schema::table('faculty_profiles', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->string('gender')->nullable()->change();
            $table->string('cnic_passport')->nullable()->change();
            $table->date('dob')->nullable()->change();
            $table->string('mobile_number')->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->string('province')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('contract_type')->nullable()->change();
            $table->string('academic_rank')->nullable()->change();
            $table->date('joining_date')->nullable()->change();
            $table->string('highest_degree_name')->nullable()->change();
            $table->string('highest_degree_type')->nullable()->change();
            $table->string('field_of_study')->nullable()->change();
            $table->string('degree_country')->nullable()->change();
            $table->string('university_name')->nullable()->change();
            $table->date('degree_start_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 
    }
};
