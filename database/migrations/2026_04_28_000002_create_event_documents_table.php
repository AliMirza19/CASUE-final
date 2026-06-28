<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->enum('doc_type', ['report', 'attendance', 'minutes', 'other'])->default('report');
            $table->string('file_path');
            $table->string('original_filename');
            $table->text('description')->nullable();
            $table->json('visible_to_roles')->default('["president","hod","patron","faculty","admin"]');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_documents');
    }
};
