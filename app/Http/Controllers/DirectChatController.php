<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\RoleAssignment;
use App\Models\AcademicTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DirectChatController extends Controller
{
    /**
     * Display the direct chat interface.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $activeTerm = AcademicTerm::getActive();
        $termId = $activeTerm ? $activeTerm->id : $user->current_term_id;

        // Determine who this user can chat with
        $contacts = $this->getAvailableContacts($user, $termId);
        
        $selectedUserId = $request->query('user_id');
        $selectedUser = null;
        $messages = collect();

        if ($selectedUserId) {
            $selectedUser = User::findOrFail($selectedUserId);
            $messages = Message::getConversation($user->id, $selectedUser->id);
            Message::markAsRead($selectedUser->id, $user->id);
        }

        return view('chat.direct', compact('contacts', 'selectedUser', 'messages', 'activeTerm'));
    }

    /**
     * Get list of users available for chat based on current user's role.
     */
    private function getAvailableContacts($user, $termId)
    {
        $teamRoles = ['gd', 'photo', 'video', 'smt', 'doc', 'deco', 'vc', 'sa'];
        $contacts = collect();
        
        // 1. Check if user has a team lead role assigned for this term
        $userAssignment = RoleAssignment::where('user_id', $user->id)
            ->where('term_id', $termId)
            ->where('is_active', true)
            ->first();
            
        $effectiveRole = $userAssignment ? $userAssignment->role : $user->role;

        if ($effectiveRole === 'president') {
            // President can chat with all team leads and Admin
            $targetRoles = ['gd', 'photo', 'video', 'smt', 'doc', 'deco', 'vc', 'sa', 'admin'];
            
            // Get all active assignments for target roles
            $assignments = RoleAssignment::where('term_id', $termId)
                ->whereIn('role', $targetRoles)
                ->with('user')
                ->get();

            foreach ($assignments as $a) {
                if (!$a->user || $a->user_id === $user->id) continue;
                $contacts->push([
                    'id' => $a->user->id,
                    'name' => $a->user->name,
                    'role_name' => $a->user->getDisplayRole($a->role),
                    'role' => $a->role,
                    'unread_count' => Message::getUnreadCount($user->id, $a->user->id)
                ]);
            }

            // Also add Admin if not already there
            $admin = User::where('role', 'admin')->first();
            if ($admin && $admin->id !== $user->id) {
                $contacts->push([
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'role_name' => 'Administrator',
                    'role' => 'admin',
                    'unread_count' => Message::getUnreadCount($user->id, $admin->id)
                ]);
            }

        } elseif (in_array($effectiveRole, $teamRoles) || $effectiveRole === 'admin') {
            // Team leads and Admin can chat with the President
            $presidentAssignment = RoleAssignment::where('term_id', $termId)
                ->where('role', 'president')
                ->with('user')
                ->first();
            
            $president = $presidentAssignment ? $presidentAssignment->user : User::where('role', 'president')->first();
            
            if ($president && $president->id !== $user->id) {
                $contacts->push([
                    'id' => $president->id,
                    'name' => $president->name,
                    'role_name' => 'President',
                    'role' => 'president',
                    'unread_count' => Message::getUnreadCount($user->id, $president->id)
                ]);
            }

            // If admin, also allow chatting with HOD and Patron
            if ($effectiveRole === 'admin') {
                $hod = RoleAssignment::getCurrentHod($termId);
                if ($hod) {
                    $contacts->push([
                        'id' => $hod->user->id,
                        'name' => $hod->user->name,
                        'role_name' => 'HOD',
                        'role' => 'hod',
                        'unread_count' => Message::getUnreadCount($user->id, $hod->user->id)
                    ]);
                }
                
                $patron = RoleAssignment::getCurrentPatron($termId);
                if ($patron) {
                    $contacts->push([
                        'id' => $patron->user->id,
                        'name' => $patron->user->name,
                        'role_name' => 'Patron',
                        'role' => 'patron',
                        'unread_count' => Message::getUnreadCount($user->id, $patron->user->id)
                    ]);
                }
            }
        }

        // 3. Always include anyone the user has an existing conversation with
        $conversations = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->get();
            
        $existingUserIds = $conversations->pluck('sender_id')
            ->merge($conversations->pluck('receiver_id'))
            ->unique()
            ->reject(fn($id) => $id === $user->id);
            
        if ($existingUserIds->isNotEmpty()) {
            $existingUsers = User::whereIn('id', $existingUserIds)->get();
            foreach ($existingUsers as $u) {
                // If not already in contacts, add them
                if (!$contacts->contains('id', $u->id)) {
                    $contacts->push([
                        'id' => $u->id,
                        'name' => $u->name,
                        'role_name' => $u->getDisplayRole(),
                        'role' => $u->role,
                        'unread_count' => Message::getUnreadCount($user->id, $u->id)
                    ]);
                }
            }
        }

        return $contacts->unique('id')->values();
    }

    /**
     * Send a direct message.
     */
    public function sendMessage(Request $request, $userId)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000',
            ]);

            $sender = Auth::user();
            $receiver = User::findOrFail($userId);

            $message = Message::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'message_text' => trim($request->message),
                'is_read' => false
            ]);

            // Notify receiver
            $receiver->notify(new \App\Notifications\NewDirectMessage($message, $sender));

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'text' => $message->message_text,
                    'sender_name' => $sender->name,
                    'is_own' => true,
                    'formatted_time' => $message->formatted_time,
                    'read_status' => $message->read_status,
                    'created_at' => $message->created_at->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Direct Chat Send Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error sending message.'], 500);
        }
    }

    /**
     * Get messages for a conversation (for polling).
     */
    public function getMessages($userId)
    {
        try {
            $user = Auth::user();
            $messages = Message::getConversation($user->id, $userId);
            
            // Mark as read
            Message::markAsRead($userId, $user->id);

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
                'messages' => $formattedMessages
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading messages.'], 500);
        }
    }
}
