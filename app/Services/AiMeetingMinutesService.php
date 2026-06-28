<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiMeetingMinutesService extends BaseAiService
{
    public function generateMinutes(int $user1Id, int $user2Id): array
    {
        // Get last 50 messages between the two users
        $messages = Message::where(function($query) use ($user1Id, $user2Id) {
            $query->where('sender_id', $user1Id)
                  ->where('receiver_id', $user2Id);
        })->orWhere(function($query) use ($user1Id, $user2Id) {
            $query->where('sender_id', $user2Id)
                  ->where('receiver_id', $user1Id);
        })->orderBy('created_at', 'asc')->take(50)->get();

        if ($messages->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No chat history found to summarize.'
            ];
        }

        $chatLog = "";
        foreach ($messages as $msg) {
            $sender = $msg->sender->name;
            $text = $msg->message_text;
            $chatLog .= "[$sender]: $text\n";
        }

        $prompt = "You are the CAUSE AI Cognitive Core. Please read the following chat log between the HOD and Patron, and extract the 'Action Items' or key decisions made during this conversation. Format as a neat bulleted list.\n\nChat Log:\n" . $chatLog;

        $res = $this->callAi($prompt);

        if ($res['success']) {
            return [
                'success' => true,
                'summary' => $res['result']
            ];
        }

        return [
            'success' => false,
            'message' => 'AI Summary generation failed.'
        ];
    }
}
