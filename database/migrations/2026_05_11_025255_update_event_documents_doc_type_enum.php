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
        DB::statement("ALTER TABLE event_documents MODIFY COLUMN doc_type ENUM('financial_report', 'approval_form', 'general_documentation', 'poster_graphic') DEFAULT 'general_documentation'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE event_documents MODIFY COLUMN doc_type ENUM('report', 'attendance', 'minutes', 'other') DEFAULT 'report'");
    }
};
