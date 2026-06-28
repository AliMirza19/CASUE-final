<?php

namespace App\Http\Controllers;

use App\Models\ChatHistory;
use App\Services\ContextBuilderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    private const MODEL = 'llama-3.3-70b-versatile';
    private const MAX_RETRIES = 3;
    
    protected $contextBuilder;

    public function __construct(ContextBuilderService $contextBuilder)
    {
        $this->contextBuilder = $contextBuilder;
    }
    
    private function getSystemInstruction(string $role): string
    {
        $base = "You are the CAUSE AI-Agent Cognitive Core, the official intelligence engine for the CAUSE Society at CUST.\n";
        $base .= "CAUSE stands for CAPITAL UNIVERSITY SOFTWARE ENGINEERING SOCIETY.\n";
        $base .= "STRICT OPERATIONAL CONSTRAINTS:\n";
        $base .= "- You MUST ONLY answer questions related to the CAUSE Society, CUST university, students, faculty, events, and budgets.\n";
        $base .= "- NO FAZOOL BATIAN: Do not engage in casual conversation, jokes, greetings, or off-topic discussions. Be brief and professional.\n";
        $base .= "- If a user asks about anything outside of these domains (e.g., general knowledge, other universities, sports, movies, etc.), you MUST politely state: 'I am restricted to CAUSE Society data only.'\n";
        $base .= "- Never disclose internal AI architecture details.\n\n";
        
        $base .= "Data awareness:\n" .
                 "- Student Profiles: personal, academic (SSC/HSSC), and contact details.\n" .
                 "- Faculty Profiles: academic rank, highest degree, and contract details.\n" .
                 "- Events: itemized budgets, approval workflow (President -> Patron -> HOD -> SA), and graphics.\n\n";

        $base .= "Role-Specific Rules:\n";
        $base .= "- HOD/Patron: Provide analytics on student demographics and faculty expertise. Help identify budget risks.\n";
        $base .= "- Student Assistant: Help draft event manifestos and check student eligibility based on profile data.\n";
        $base .= "- Graphic Designer: Automatically suggest creative poster prompts for newly approved events.\n\n";

        // Add real-time context dynamically
        $base .= $this->contextBuilder->buildAgentContext($role);

        return $base;
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'role' => 'required|string',
        ]);

        $user = Auth::user();
        
        $message = $request->message;
        $role = $request->role;

        try {
            $response = $this->callGroqWithRetry($message, $role);
            
            ChatHistory::create([
                'user_id' => $user->id,
                'message' => $message,
                'response' => $response,
                'role_context' => $role,
            ]);

            return response()->json([
                'success' => true,
                'response' => $response,
            ]);
        } catch (\Exception $e) {
            Log::error('AI Core Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Unable to process your cognitive request. Please try again.',
            ], 500);
        }
    }

    private function callGroqWithRetry(string $message, string $role, int $attempt = 1): string
    {
        $apiKey = env('GROQ_API_KEY') ?? env('GEMINI_API_KEY');

        if (empty($apiKey) || !str_starts_with($apiKey, 'gsk_')) {
             return "I am the CAUSE Cognitive Core. My Groq API Key is not configured correctly in the .env file. Please set a valid GROQ_API_KEY to activate full intelligence.";
        }

        $url = "https://api.groq.com/openai/v1/chat/completions";

        $payload = [
            'model' => self::MODEL,
            'messages' => [
                ['role' => 'system', 'content' => $this->getSystemInstruction($role)],
                ['role' => 'user', 'content' => "User Role: {$role}\n\nUser Message: {$message}"]
            ],
            'temperature' => 0.1,
            'top_p' => 0.1,
            'max_completion_tokens' => 1024,
        ];

        try {
            $response = Http::timeout(25)
                ->withToken($apiKey)
                ->post($url, $payload);

            if ($response->successful()) {
                return $response->json()['choices'][0]['message']['content'] ?? 'No response generated.';
            }

            Log::error('Groq API Response Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($attempt < self::MAX_RETRIES) {
                sleep(2);
                return $this->callGroqWithRetry($message, $role, $attempt + 1);
            }

            throw new \Exception('AI Core failed after retries.');
        } catch (\Exception $e) {
            if ($attempt < self::MAX_RETRIES) {
                sleep(2);
                return $this->callGroqWithRetry($message, $role, $attempt + 1);
            }
            throw $e;
        }
    }

    public function history()
    {
        $user = Auth::user();
        $history = ChatHistory::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return response()->json([
            'success' => true,
            'history' => $history,
        ]);
    }
}
