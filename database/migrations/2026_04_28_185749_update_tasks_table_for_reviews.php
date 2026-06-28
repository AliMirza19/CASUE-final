<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify ENUM column directly via raw DB statement for MariaDB/MySQL
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('pending', 'in_progress', 'completed', 'approved', 'rejected') DEFAULT 'pending'");
        
        Schema::table('tasks', function (Blueprint $table) {
            $table->text('submission_notes')->nullable()->after('status');
            $table->text('feedback')->nullable()->after('submission_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('submission_notes');
            $table->dropColumn('feedback');
        });
        
        // Revert ENUM
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending'");
    }
};
