<?php

namespace App\Http\Controllers\Hod;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\RoleAssignment;
use App\Models\AcademicTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Display the chat interface for HOD.
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            // Get active term
            $activeTerm = AcademicTerm::getActive();
            $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
            
            // Get current Patron for this term
            $patronAssignment = RoleAssignment::getCurrentPatron($termId);
            $patron = $patronAssignment ? $patronAssignment->user : null;
            
            if (!$patron) {
                return redirect()->route('hod.dashboard')
                    ->with('error', 'No Patron assigned for the current term. Please assign a Patron first.');
            }
            
            // Get conversation messages
            $messages = Message::getConversation($user->id, $patron->id);
            
            // Mark messages from Patron as read when opening chat
            Message::markAsRead($patron->id, $user->id);
            
            // Get unread count (should be 0 after marking as read)
            $unreadCount = Message::getUnreadCount($user->id, $patron->id);
            
            // Debug info
            Log::info('HOD Chat Loaded', [
                'hod_id' => $user->id,
                'hod_name' => $user->name,
                'patron_id' => $patron->id,
                'patron_name' => $patron->name,
                'messages_count' => $messages->count(),
                'unread_count' => $unreadCount,
                'term_id' => $termId
            ]);
            
            return view('hod.chat', compact('patron', 'messages', 'unreadCount', 'activeTerm'));
            
        } catch (\Exception $e) {
            Log::error('HOD Chat Error: ' . $e->getMessage());
            return redirect()->route('hod.dashboard')
                ->with('error', 'Error loading chat. Please try again.');
        }
    }
    
    /**
     * Send a message to Patron.
     */
    public function send(Request $request)
    {
        // DEBUG: Log when send method is called
        Log::info('HOD Chat Send - Method Called', [
            'request_all' => $request->all(),
            'user_id' => Auth::id(),
            'user_authenticated' => Auth::check(),
            'ip' => $request->ip()
        ]);
        
        try {
            $request->validate([
                'message' => 'required|string|max:1000',
            ]);
            
            Log::info('HOD Chat Send - Validation Passed');
            
            $user = Auth::user();
            
            // Get active term
            $activeTerm = AcademicTerm::getActive();
            $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
            
            Log::info('HOD Chat Send - Term Found', ['term_id' => $termId]);
            
            // Get current Patron for this term
            $patronAssignment = RoleAssignment::getCurrentPatron($termId);
            
            if (!$patronAssignment) {
                Log::warning('HOD Chat Send - No Patron Assigned', ['term_id' => $termId]);
                return response()->json([
                    'success' => false,
                    'message' => 'No Patron assigned for the current term.'
                ], 400);
            }
            
            Log::info('HOD Chat Send - Creating Message', [
                'sender_id' => $user->id,
                'receiver_id' => $patronAssignment->user_id,
                'message' => trim($request->message)
            ]);
            
            // Create message
            $message = Message::create([
                'sender_id' => $user->id,
                'receiver_id' => $patronAssignment->user_id,
                'message_text' => trim($request->message),
                'is_read' => false
            ]);
            
            Log::info('HOD Chat Send - Message Created Successfully', ['message_id' => $message->id]);
            
            // Load relationships for response
            $message->load(['sender', 'receiver']);
            
            Log::info('Message Sent by HOD', [
                'message_id' => $message->id,
                'from' => $user->name,
                'to' => $patronAssignment->user->name,
                'text' => $message->message_text
            ]);
            
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'text' => $message->message_text,
                    'sender_name' => $message->sender->name,
                    'is_own' => true,
                    'formatted_time' => $message->formatted_time,
                    'read_status' => $message->read_status,
                    'created_at' => $message->created_at->toISOString()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('HOD Chat Send - Exception Caught', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error sending message. Please try again.'
            ], 500);
        }
    }
    
    /**
     * Get messages between HOD and Patron.
     */
    public function getMessages(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Get active term
            $activeTerm = AcademicTerm::getActive();
            $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
            
            // Get current Patron for this term
            $patronAssignment = RoleAssignment::getCurrentPatron($termId);
            
            if (!$patronAssignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Patron assigned for the current term.'
                ], 400);
            }
            
            // Get conversation
            $messages = Message::getConversation($user->id, $patronAssignment->user_id);
            
            // Mark messages from Patron as read
            Message::markAsRead($patronAssignment->user_id, $user->id);
            
            // Format messages for frontend
            $formattedMessages = $messages->map(function ($message) use ($user) {
                return [
                    'id' => $message->id,
                    'message_text' => $message->message_text,
                    'sender_name' => $message->sender->name,
                    'is_own_message' => $message->sender_id === $user->id,
                    'formatted_time' => $message->formatted_time,
                    'read_status' => $message->read_status,
                    'created_at' => $message->created_at->toISOString()
                ];
            });
            
            return response()->json([
                'success' => true,
                'messages' => $formattedMessages,
                'count' => $messages->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get Messages Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading messages.'
            ], 500);
        }
    }
}