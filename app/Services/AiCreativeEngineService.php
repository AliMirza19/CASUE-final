<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiCreativeEngineService extends BaseAiService
{
    // Temperature inherited from BaseAiService for consistency

    public function generateVisualPersona(string $eventTitle, string $description): array
    {
        $prompt = "You are the CAUSE AI Creative Engine. For the following event, suggest a 'Theme Persona' (e.g., Cyberpunk, Minimalist, Corporate, Vibrant, etc.) and explain why it fits. Also provide a color palette suggestion.\n\nEvent Title: $eventTitle\nDescription: $description";

        return $this->callAi($prompt);
    }

    public function generateSocialMediaCopy(string $eventTitle, string $description): array
    {
        $prompt = "You are the CAUSE AI Creative Engine. Generate a catchy social media caption and relevant hashtags for the following event.\n\nEvent Title: $eventTitle\nDescription: $description";

        return $this->callAi($prompt);
    }
}
