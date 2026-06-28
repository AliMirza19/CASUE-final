<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseAiService
{
    protected string $model = 'llama-3.3-70b-versatile';
    protected float $temperature = 0.1;
    protected float $top_p = 0.1;

    /**
     * Call Groq API for a response.
     */
    protected function callAi(string $prompt, bool $jsonMode = false): array
    {
        $apiKey = env('GROQ_API_KEY') ?? env('GEMINI_API_KEY'); // Fallback if user just set GEMINI_API_KEY with a Groq key
        
        if (empty($apiKey) || !str_starts_with($apiKey, 'gsk_')) {
            return [
                'success' => false,
                'message' => 'AI Core is offline (Valid GROQ_API_KEY required).'
            ];
        }

        $url = "https://api.groq.com/openai/v1/chat/completions";

        $messages = [
            ['role' => 'system', 'content' => "You are the CAUSE AI Cognitive Core. STRICT RULES: Only answer questions related to CAUSE Society, CUST university, students, faculty, events, and budgets. No 'fazool batian' (small talk, jokes, or off-topic chatter). Be extremely concise and professional."],
            ['role' => 'user', 'content' => $prompt]
        ];

        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => $this->temperature,
            'top_p' => $this->top_p,
            'max_completion_tokens' => 1024,
        ];

        if ($jsonMode) {
            $payload['response_format'] = ['type' => 'json_object'];
        }

        try {
            $response = Http::timeout(30)
                ->withToken($apiKey)
                ->post($url, $payload);

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'] ?? '';
                
                if ($jsonMode) {
                    $cleaned = preg_replace('/```json\n?|\n?```/', '', $content);
                    $data = json_decode(trim($cleaned), true);
                    return [
                        'success' => true,
                        'data' => $data ?: []
                    ];
                }

                return [
                    'success' => true,
                    'result' => $content
                ];
            }
            
            Log::error('Groq API Error Response', ['body' => $response->body()]);
        } catch (\Exception $e) {
            Log::error('AI Service Exception', ['message' => $e->getMessage()]);
        }

        return [
            'success' => false,
            'message' => 'Failed to reach AI Core.'
        ];
    }
}
