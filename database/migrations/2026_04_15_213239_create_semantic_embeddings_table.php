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
        Schema::create('semantic_embeddings', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // e.g., 'event', 'candidate'
            $table->unsignedBigInteger('entity_id');
            $table->text('content_text'); // The original text that was embedded
            $table->json('embedding'); // Storing vector array as JSON since MySQL 8 native vectors might not be available
            $table->timestamps();
            
            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semantic_embeddings');
    }
};
