<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_decoration_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->text('plan_description')->nullable();
            $table->json('material_list')->nullable();   // [{item, qty, estimated_cost}]
            $table->decimal('estimated_budget', 10, 2)->default(0);
            $table->enum('status', ['not_started', 'in_progress', 'done'])->default('not_started');
            $table->json('setup_photos')->nullable();    // array of file paths
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_decoration_plans');
    }
};
