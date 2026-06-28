<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AiDecisionSupportService extends BaseAiService
{
    public function analyzeEventBudget(Event $event, string $role = 'patron')
    {
        // Cache the AI analysis to avoid spamming the API and slowing down page reloads.
        $cacheKey = "ai_analysis_event_{$event->id}_{$role}";
        
        return Cache::remember($cacheKey, 3600, function () use ($event, $role) {
            $itemsList = $event->items->map(function ($item) {
                return "- {$item->item_name}: Quantity {$item->quantity}, Total PKR {$item->total_amount}";
            })->implode("\n");

            // Build specialized prompt depending on role
            $prompt = "You are the CAUSE AI Cognitive Core advising a {$role}.\n";
            $prompt .= "Analyze the following event budget for anomalies, overpriced items, or historical context.\n";
            $prompt .= "Event Title: {$event->title}\n";
            $prompt .= "Total Requested: PKR {$event->grand_total}\n";
            $prompt .= "Items:\n{$itemsList}\n\n";

            if ($role === 'patron') {
                $prompt .= "Focus on Market Rate Verification. Are any of these item amounts unusually high for student university events in Pakistan? Provide a brief paragraph identifying any anomalies. If everything looks reasonable, state that no anomalies were found.";
            } else {
                $prompt .= "Focus on Predictive Budgeting. Since historical data for 3 past terms might be simulated, assume typical averages (e.g., tech events cost avg 15k, seminars 10k). Warn the HOD if this budget exceeds expected averages and suggest reductions.";
            }

            $res = $this->callAi($prompt);
            
            if ($res['success']) {
                $text = $res['result'];
                return [
                    'flagged' => str_contains(strtolower($text), 'anomaly') || str_contains(strtolower($text), 'high') || str_contains(strtolower($text), 'exceeds'),
                    'message' => $text,
                    'anomalies' => []
                ];
            }

            return [
                'flagged' => false,
                'message' => 'AI analysis failed to generate. Please review manually.',
                'anomalies' => []
            ];
        });
    }
}
