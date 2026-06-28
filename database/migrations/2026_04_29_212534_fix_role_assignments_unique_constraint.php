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
        $indexes = Schema::getIndexes('role_assignments');
        $indexNames = array_column($indexes, 'name');

        Schema::table('role_assignments', function (Blueprint $table) use ($indexNames) {
            // Add a non-unique index for term_id if it doesn't exist
            if (!in_array('role_assignments_term_id_index', $indexNames)) {
                $table->index('term_id', 'role_assignments_term_id_index');
            }
            
            // Drop the old restrictive unique constraint if it still exists
            if (in_array('unique_active_role_per_term', $indexNames)) {
                $table->dropUnique('unique_active_role_per_term');
            }
        });

        // Use a virtual column and index it for conditional uniqueness
        if (!Schema::hasColumn('role_assignments', 'active_only')) {
            DB::statement("ALTER TABLE role_assignments ADD COLUMN active_only TINYINT(1) AS (IF(is_active = 1, 1, NULL)) VIRTUAL");
        }
        
        // Final check for the new unique index name (it might have been dropped in up() if it was the old one)
        // Refresh indexes list
        $indexes = Schema::getIndexes('role_assignments');
        $indexNames = array_column($indexes, 'name');
        
        if (!in_array('unique_active_role_per_term', $indexNames)) {
            DB::statement("ALTER TABLE role_assignments ADD UNIQUE INDEX unique_active_role_per_term (term_id, role, active_only)");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_assignments', function (Blueprint $table) {
            $table->dropIndex('unique_active_role_per_term');
            $table->dropColumn('active_only');
            $table->unique(['term_id', 'role', 'is_active'], 'unique_active_role_per_term');
            $table->dropIndex('role_assignments_term_id_index');
        });
    }
};
