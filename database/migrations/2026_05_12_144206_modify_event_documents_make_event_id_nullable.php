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
        Schema::table('event_documents', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
        });
        
        Schema::table('event_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id')->nullable()->change();
            // $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            if (!Schema::hasColumn('event_documents', 'term_id')) {
                $table->foreignId('term_id')->nullable()->after('event_id')->constrained()->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('event_documents', function (Blueprint $table) {
            $table->dropForeign(['term_id']);
            $table->dropColumn('term_id');
            $table->unsignedBigInteger('event_id')->nullable(false)->change();
        });
    }
};
