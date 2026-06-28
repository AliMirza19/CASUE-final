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
        Schema::table('election_settings', function (Blueprint $table) {
            if (Schema::hasColumn('election_settings', 'voting_start_date')) {
                $table->renameColumn('voting_start_date', 'voting_start');
            }
            if (Schema::hasColumn('election_settings', 'voting_end_date')) {
                $table->renameColumn('voting_end_date', 'voting_end');
            }
            if (!Schema::hasColumn('election_settings', 'registration_start')) {
                $table->dateTime('registration_start')->after('term_id')->nullable();
            }
            if (!Schema::hasColumn('election_settings', 'registration_end')) {
                $table->dateTime('registration_end')->after('registration_start')->nullable();
            }
            if (!Schema::hasColumn('election_settings', 'is_active')) {
                $table->boolean('is_active')->default(false);
            }
            if (Schema::hasColumn('election_settings', 'voting_enabled')) {
                $table->dropColumn('voting_enabled');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('election_settings', function (Blueprint $table) {
            if (Schema::hasColumn('election_settings', 'voting_start')) {
                $table->renameColumn('voting_start', 'voting_start_date');
            }
            if (Schema::hasColumn('election_settings', 'voting_end')) {
                $table->renameColumn('voting_end', 'voting_end_date');
            }
            if (Schema::hasColumn('election_settings', 'registration_start')) {
                $table->dropColumn('registration_start');
            }
            if (Schema::hasColumn('election_settings', 'registration_end')) {
                $table->dropColumn('registration_end');
            }
            if (Schema::hasColumn('election_settings', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (!Schema::hasColumn('election_settings', 'voting_enabled')) {
                $table->boolean('voting_enabled')->default(false)->after('term_id');
            }
        });
    }
};
