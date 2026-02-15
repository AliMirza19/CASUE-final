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
        // Add comment columns for each approver
        Schema::table('events', function (Blueprint $table) {
            $table->text('president_comments')->nullable()->after('rejection_reason');
            $table->text('patron_comments')->nullable()->after('president_comments');
            $table->text('hod_comments')->nullable()->after('patron_comments');
            $table->text('sa_comments')->nullable()->after('hod_comments');
        });

        // Update the status enum to include new statuses
        // MySQL requires dropping and recreating the enum
        DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM(
            'pending_president',
            'president_approved',
            'revision_needed',
            'pending_patron',
            'pending_hod',
            'pending_sa',
            'approved',
            'rejected',
            'completed'
        ) DEFAULT 'pending_president'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['president_comments', 'patron_comments', 'hod_comments', 'sa_comments']);
        });

        DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM(
            'pending_president',
            'pending_patron',
            'pending_hod',
            'pending_sa',
            'approved',
            'rejected',
            'completed'
        ) DEFAULT 'pending_president'");
    }
};
