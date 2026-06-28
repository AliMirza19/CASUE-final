<?php

namespace App\Services;

use App\Models\AcademicTerm;
use App\Models\Event;
use App\Models\User;
use App\Models\ElectionSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SmartTickerService extends BaseAiService
{
    public function getLiveInsights(): array
    {
        // Cache insights for 30 minutes to save API cost
        return Cache::remember('smart_ticker_insights', 1800, function () {
            return $this->generateInsights();
        });
    }

    private function generateInsights(): array
    {
        $activeTerm = AcademicTerm::getActive();
        if (!$activeTerm) {
            return ["Welcome back! Waiting for term activation..."];
        }

        // Gather real-time data
        $totalBudget = $activeTerm->total_budget;
        $totalSpent = Event::where('term_id', $activeTerm->id)->where('status', 'approved')->sum('grand_total');
        $budgetPercent = $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100, 1) : 0;
        
        $pendingEvents = Event::where('term_id', $activeTerm->id)->where('status', 'like', 'pending%')->count();
        $candidates = User::whereHas('candidateProfile', function($q) {
            $q->where('status', 'approved');
        })->count();

        $election = ElectionSetting::where('term_id', $activeTerm->id)->first();
        $electionStatus = "Registration paused";
        if ($election && $election->is_active) {
            if ($election->isRegistrationOpen()) $electionStatus = "Registration open";
            elseif ($election->isVotingActive()) $electionStatus = "Voting is LIVE!";
            elseif ($election->voting_end && now() > $election->voting_end) $electionStatus = "Elections closed";
            else $electionStatus = "Elections upcoming";
        }

        $prompt = "You are the CAUSE AI System Monitor. Based on these stats, generate 4-5 short, punchy, and proactive 'Ticker Insights' (max 80 chars each) for the society dashboard.\n\n" .
                  "Stats:\n" .
                  "- Term Budget: Rs. $totalBudget\n" .
                  "- Budget Utilized: $budgetPercent%\n" .
                  "- Pending Event Reviews: $pendingEvents\n" .
                  "- Approved Election Candidates: $candidates\n" .
                  "- Election Status: $electionStatus\n\n" .
                  "Return a JSON object with a key 'insights' containing an array of strings.";

        $res = $this->callAi($prompt, true);
        
        if ($res['success'] && is_array($res['data'])) {
            // Flatten if nested under 'insights' or other common keys
            $data = $res['data'];
            if (isset($data['insights']) && is_array($data['insights'])) {
                return array_values($data['insights']);
            }
            // Fallback: if it's already a flat array (unlikely in JSON mode but possible)
            if (array_is_list($data)) {
                return $data;
            }
            // Fallback: take the first array found in the object
            foreach ($data as $value) {
                if (is_array($value)) return array_values($value);
            }
        }

        return ["System monitoring operational.", "Review pending events soon.", "Ensure budget items are accurate."];
    }
}
