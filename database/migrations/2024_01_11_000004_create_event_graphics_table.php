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
        Schema::create('event_graphics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('gd_id')->constrained('users')->onDelete('cascade');
            $table->enum('design_category', ['poster', 'banner', 'social_media']);
            $table->string('image_path', 500)->nullable();
            $table->string('image_link', 500)->nullable();
            $table->enum('status', ['pending_patron', 'approved', 'rejected'])->default('pending_patron');
            $table->text('patron_feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_graphics');
    }
};