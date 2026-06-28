<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->enum('created_by_role', ['student', 'president', 'patron', 'gd', 'photo', 'video', 'smt', 'doc', 'deco'])
                ->default('student')
                ->after('student_id')
                ->comment('Role of the user who created this event');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('created_by_role');
        });
    }
};
