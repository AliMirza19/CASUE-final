<?php

namespace App\Http\Controllers\President;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index()
    {
        $president = Auth::user();
        
        // Get all students who have messaged or been messaged by president
        $studentIds = DB::table('messages')
            ->where('sender_id', $president->id)
            ->orWhere('receiver_id', $president->id)
            ->distinct()
            ->pluck('sender_id')
            ->merge(
                DB::table('messages')
                    ->where('sender_id', $president->id)
                    ->orWhere('receiver_id', $president->id)
                    ->distinct()
                    ->pluck('receiver_id')
            )
            ->unique()
            ->filter(function($id) use ($president) {
                return $id !== $president->id;
            });

        $students = User::where('role', 'student')
            ->whereIn('id', $studentIds)
            ->get()
            ->map(function($student) use ($president) {
                $student->unread_count = Message::getUnreadCount($president->id, $student->id);
                $student->last_message = Message::where(function($query) use ($student, $president) {
                    $query->where(function($q) use ($student, $president) {
                        $q->where('sender_id', $student->id)
                          ->where('receiver_id', $president->id);
                    })->orWhere(function($q) use ($student, $president) {
                        $q->where('sender_id', $president->id)
                          ->where('receiver_id', $student->id);
                    });
                })->latest()->first();
                return $student;
            })
            ->sortByDesc(function($student) {
                return $student->last_message?->created_at;
            });

        return view('president.messages.index', compact('students'));
    }

    public function conversation($studentId)
    {
        $president = Auth::user();
        $student = User::where('role', 'student')->findOrFail($studentId);
        
        $messages = Message::getConversation($president->id, $student->id);
        Message::markAsRead($student->id, $president->id);

        return view('president.messages.conversation', [
            'student' => $student,
            'messages' => $messages
        ]);
    }

    public function sendMessage(Request $request, $studentId)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $president = Auth::user();
        $student = User::where('role', 'student')->findOrFail($studentId);

        $message = Message::create([
            'sender_id' => $president->id,
            'receiver_id' => $student->id,
            'message_text' => $request->message,
            'is_read' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => $message->load('sender', 'receiver')
        ]);
    }

    public function fetchMessages($studentId)
    {
        $president = Auth::user();
        $student = User::where('role', 'student')->findOrFail($studentId);

        $messages = Message::getConversation($president->id, $student->id);
        Message::markAsRead($student->id, $president->id);

        return response()->json(['messages' => $messages]);
    }
}
