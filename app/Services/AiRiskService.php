<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiRiskService extends BaseAiService
{
    public function assessEventRisk(Event $event): array
    {
        $items = $event->items()->get();
        $budgetSummary = "";
        foreach ($items as $item) {
            $budgetSummary .= "- {$item->item_name}: Rs. {$item->unit_price} x {$item->quantity} = Rs. {$item->total_amount}\n";
        }

        $prompt = "You are the CAUSE AI Risk Auditor. Analyze the following event proposal for potential risks, anomalies, or concerns (e.g., suspiciously high costs, date conflicts, vague descriptions, or logistical issues).\n\n" .
                  "Event Title: {$event->title}\n" .
                  "Description: {$event->description}\n" .
                  "Proposed Budget (Total: Rs. {$event->grand_total}):\n$budgetSummary\n\n" .
                  "Return a JSON object with:\n" .
                  "1. 'risk_level': 'Low', 'Medium', or 'High'\n" .
                  "2. 'flags': An array of specific concern strings\n" .
                  "3. 'suggestions': A brief string on how to mitigate identified risks.\n\n" .
                  "Format: {\"risk_level\": \"...\", \"flags\": [\"...\"], \"suggestions\": \"...\"}";

        $res = $this->callAi($prompt, true);
        return $res['success'] ? $res['data'] : [
            'risk_level' => 'Unknown',
            'flags' => ['Failed to perform AI analysis'],
            'suggestions' => 'Please review manually.'
        ];
    }
}
