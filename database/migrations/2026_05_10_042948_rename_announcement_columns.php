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
        Schema::table('announcements', function (Blueprint $table) {
            if (Schema::hasColumn('announcements', 'image_path')) {
                $table->renameColumn('image_path', 'image_url');
            }
            if (Schema::hasColumn('announcements', 'link')) {
                $table->renameColumn('link', 'link_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            if (Schema::hasColumn('announcements', 'image_url')) {
                $table->renameColumn('image_url', 'image_path');
            }
            if (Schema::hasColumn('announcements', 'link_url')) {
                $table->renameColumn('link_url', 'link');
            }
        });
    }
};
