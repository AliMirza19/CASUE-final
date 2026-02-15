<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration removes 'hod' and 'patron' from the user role enum.
     * HOD and Patron should ONLY be assigned through role_assignments table.
     * All users should have base roles: admin, student, faculty, president, sa, vc, gd
     */
    public function up(): void
    {
        // First, convert any existing hod/patron users to faculty
        DB::table('users')
            ->whereIn('role', ['hod', 'patron'])
            ->update(['role' => 'faculty']);
        
        // Update the enum to remove hod and patron
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'admin',
            'student',
            'faculty',
            'president',
            'sa',
            'vc',
            'gd'
        ) NOT NULL DEFAULT 'student'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add hod and patron back to enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'admin',
            'hod',
            'patron',
            'president',
            'student',
            'faculty',
            'sa',
            'vc',
            'gd'
        ) NOT NULL DEFAULT 'student'");
    }
};
