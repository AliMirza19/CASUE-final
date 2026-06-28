<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->enum('media_type', ['photo', 'video', 'highlight'])->default('photo');
            $table->string('file_path')->nullable();
            $table->string('original_filename')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('tagged_reg_number')->nullable();
            $table->string('tagged_role')->nullable();
            $table->string('caption')->nullable();
            $table->bigInteger('file_size')->nullable(); // bytes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_media');
    }
};
