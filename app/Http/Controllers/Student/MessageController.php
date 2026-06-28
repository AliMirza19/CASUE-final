<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $student = Auth::user();
        $president = User::where('role', 'president')->first();
        
        if (!$president) {
            return view('student.messages.index', [
                'president' => null,
                'messages' => collect([]),
                'error' => 'No president is currently assigned.'
            ]);
        }

        $messages = Message::getConversation($student->id, $president->id);
        Message::markAsRead($president->id, $student->id);

        return view('student.messages.index', [
            'president' => $president,
            'messages' => $messages
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $student = Auth::user();
        $president = User::where('role', 'president')->first();

        if (!$president) {
            return response()->json(['error' => 'No president found'], 404);
        }

        $message = Message::create([
            'sender_id' => $student->id,
            'receiver_id' => $president->id,
            'message_text' => $request->message,
            'is_read' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => $message->load('sender', 'receiver')
        ]);
    }

    public function fetchMessages()
    {
        $student = Auth::user();
        $president = User::where('role', 'president')->first();

        if (!$president) {
            return response()->json(['messages' => []]);
        }

        $messages = Message::getConversation($student->id, $president->id);
        Message::markAsRead($president->id, $student->id);

        return response()->json(['messages' => $messages]);
    }
}
