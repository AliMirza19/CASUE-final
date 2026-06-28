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
        DB::statement("ALTER TABLE role_assignments MODIFY COLUMN role ENUM(
            'hod',
            'patron',
            'president',
            'sa',
            'vc',
            'gd',
            'smt',
            'doc',
            'photo',
            'video',
            'deco'
        ) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Careful, down might fail if data exists
        DB::statement("ALTER TABLE role_assignments MODIFY COLUMN role ENUM(
            'hod',
            'patron'
        ) NOT NULL");
    }
};
