<?php

namespace App\Http\Controllers\Patron;

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
     * Display the chat interface for Patron.
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            // Get active term
            $activeTerm = AcademicTerm::getActive();
            $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
            
            // Get current HOD for this term
            $hodAssignment = RoleAssignment::getCurrentHod($termId);
            $hod = $hodAssignment ? $hodAssignment->user : null;
            
            if (!$hod) {
                return redirect()->route('patron.dashboard')
                    ->with('error', 'No HOD assigned for the current term.');
            }
            
            // Get conversation messages
            $messages = Message::getConversation($user->id, $hod->id);
            
            // Mark messages from HOD as read when opening chat
            Message::markAsRead($hod->id, $user->id);
            
            // Get unread count (should be 0 after marking as read)
            $unreadCount = Message::getUnreadCount($user->id, $hod->id);
            
            // Debug info
            Log::info('Patron Chat Loaded', [
                'patron_id' => $user->id,
                'patron_name' => $user->name,
                'hod_id' => $hod->id,
                'hod_name' => $hod->name,
                'messages_count' => $messages->count(),
                'unread_count' => $unreadCount,
                'term_id' => $termId
            ]);
            
            return view('patron.chat', compact('hod', 'messages', 'unreadCount', 'activeTerm'));
            
        } catch (\Exception $e) {
            Log::error('Patron Chat Error: ' . $e->getMessage());
            return redirect()->route('patron.dashboard')
                ->with('error', 'Error loading chat. Please try again.');
        }
    }
    
    /**
     * Send a message to HOD.
     */
    public function send(Request $request)
    {
        // DEBUG: Log when send method is called
        Log::info('Patron Chat Send - Method Called', [
            'request_all' => $request->all(),
            'user_id' => Auth::id(),
            'user_authenticated' => Auth::check(),
            'ip' => $request->ip()
        ]);
        
        try {
            $request->validate([
                'message' => 'required|string|max:1000',
            ]);
            
            Log::info('Patron Chat Send - Validation Passed');
            
            $user = Auth::user();
            
            // Get active term
            $activeTerm = AcademicTerm::getActive();
            $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
            
            Log::info('Patron Chat Send - Term Found', ['term_id' => $termId]);
            
            // Get current HOD for this term
            $hodAssignment = RoleAssignment::getCurrentHod($termId);
            
            if (!$hodAssignment) {
                Log::warning('Patron Chat Send - No HOD Assigned', ['term_id' => $termId]);
                return response()->json([
                    'success' => false,
                    'message' => 'No HOD assigned for the current term.'
                ], 400);
            }
            
            Log::info('Patron Chat Send - Creating Message', [
                'sender_id' => $user->id,
                'receiver_id' => $hodAssignment->user_id,
                'message' => trim($request->message)
            ]);
            
            // Create message
            $message = Message::create([
                'sender_id' => $user->id,
                'receiver_id' => $hodAssignment->user_id,
                'message_text' => trim($request->message),
                'is_read' => false
            ]);
            
            Log::info('Patron Chat Send - Message Created Successfully', ['message_id' => $message->id]);
            
            // Load relationships for response
            $message->load(['sender', 'receiver']);
            
            Log::info('Message Sent by Patron', [
                'message_id' => $message->id,
                'from' => $user->name,
                'to' => $hodAssignment->user->name,
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
            Log::error('Patron Chat Send - Exception Caught', [
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
     * Get messages between Patron and HOD.
     */
    public function getMessages(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Get active term
            $activeTerm = AcademicTerm::getActive();
            $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;
            
            // Get current HOD for this term
            $hodAssignment = RoleAssignment::getCurrentHod($termId);
            
            if (!$hodAssignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'No HOD assigned for the current term.'
                ], 400);
            }
            
            // Get conversation
            $messages = Message::getConversation($user->id, $hodAssignment->user_id);
            
            // Mark messages from HOD as read
            Message::markAsRead($hodAssignment->user_id, $user->id);
            
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

    /**
     * Generate Meeting Minutes via AI for the chat history.
     */
    public function summarize()
    {
        try {
            $user = Auth::user();
            $termId = $user->current_term_id;
            
            $hodAssignment = RoleAssignment::getCurrentHod($termId);
            if (!$hodAssignment) {
                return response()->json(['success' => false, 'message' => 'No HOD assigned.']);
            }

            $aiService = app(\App\Services\AiMeetingMinutesService::class);
            return response()->json($aiService->generateMinutes($user->id, $hodAssignment->user_id));

        } catch (\Exception $e) {
            Log::error('Summarize Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to summarize.'], 500);
        }
    }
}