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
        // 1. Recalculate each item's total_amount
        $items = \App\Models\EventItem::all();
        foreach ($items as $item) {
            $item->total_amount = $item->quantity * $item->unit_rate;
            $item->save();
        }

        // 2. Recalculate each event's grand_total
        $events = \App\Models\Event::all();
        foreach ($events as $event) {
            $event->updateGrandTotal();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed as this is a data correction migration
    }
};
