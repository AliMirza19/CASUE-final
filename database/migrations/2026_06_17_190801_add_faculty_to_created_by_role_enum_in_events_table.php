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
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE events MODIFY COLUMN created_by_role ENUM('student', 'president', 'patron', 'gd', 'photo', 'video', 'smt', 'doc', 'deco', 'faculty', 'hod', 'vc', 'sa', 'admin') DEFAULT 'student'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE events MODIFY COLUMN created_by_role ENUM('student', 'president', 'patron', 'gd', 'photo', 'video', 'smt', 'doc', 'deco') DEFAULT 'student'");
    }
};
