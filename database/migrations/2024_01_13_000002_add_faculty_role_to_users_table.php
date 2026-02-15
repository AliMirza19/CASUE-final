<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add 'faculty' to the role enum in users table.
     */
    public function up(): void
    {
        // MySQL requires ALTER TABLE to modify ENUM
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'hod', 'patron', 'president', 'student', 'sa', 'vc', 'gd', 'faculty') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First update any faculty users to a different role
        DB::table('users')->where('role', 'faculty')->update(['role' => 'patron']);
        
        // Then remove faculty from enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'hod', 'patron', 'president', 'student', 'sa', 'vc', 'gd') NOT NULL");
    }
};
