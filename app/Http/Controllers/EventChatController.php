<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\ChatGroupMember;
use App\Models\ChatMessage;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all groups the user is a member of
        $groups = $user->chatGroups()
            ->with(['event', 'messages' => function($q) {
                $q->latest()->limit(1);
            }])
            ->get();

        return view('chat.index', compact('groups'));
    }

    public function show($id)
    {
        $group = ChatGroup::with(['event', 'messages.user', 'members.user'])->findOrFail($id);
        
        // Check if user is a member
        if (!$group->members()->where('user_id', Auth::id())->exists()) {
            abort(403, 'You are not a member of this group.');
        }

        $groups = Auth::user()->chatGroups()->with('event')->get();

        return view('chat.index', compact('group', 'groups'));
    }

    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'nullable|string|max:5000',
            'image' => 'nullable|image|max:10240', // 10MB max
        ]);

        if (!$request->message && !$request->hasFile('image')) {
            return back()->with('error', 'Message or image is required.');
        }

        $group = ChatGroup::findOrFail($id);

        // Check if user is a member
        if (!$group->members()->where('user_id', Auth::id())->exists()) {
            abort(403);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
        }

        $chatMessage = ChatMessage::create([
            'chat_group_id' => $group->id,
            'user_id' => Auth::id(),
            'message' => $request->message ?? '',
            'image_path' => $imagePath,
        ]);

        // Notify other group members
        $sender = Auth::user();
        foreach ($group->members as $member) {
            if ($member->user_id !== $sender->id) {
                $member->user->notify(new \App\Notifications\NewGroupMessage($chatMessage, $sender, $group));
            }
        }

        return back()->with('success', 'Message sent.');
    }

    public function annotate(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|string', // Base64 image
        ]);

        if (Auth::user()->role !== 'president') {
            return response()->json(['error' => 'Only the President can annotate images.'], 403);
        }

        $message = ChatMessage::findOrFail($id);
        
        // Save the base64 image
        $imageData = $request->image;
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
            $type = strtolower($type[1]); // png, jpg, etc

            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                return response()->json(['error' => 'Invalid image type.'], 400);
            }

            $imageData = base64_decode($imageData);

            if ($imageData === false) {
                return response()->json(['error' => 'Base64 decode failed.'], 400);
            }
        } else {
            return response()->json(['error' => 'Invalid data stream.'], 400);
        }

        $imageName = 'annotated_' . time() . '_' . $id . '.png';
        Storage::disk('public')->put('chat_annotations/' . $imageName, $imageData);

        // Create a NEW message with the annotated image as feedback
        ChatMessage::create([
            'chat_group_id' => $message->chat_group_id,
            'user_id' => Auth::id(),
            'message' => 'Mistakes marked in the image below:',
            'image_path' => 'chat_annotations/' . $imageName,
        ]);

        return response()->json(['success' => true]);
    }

    public function getMessages($id)
    {
        $group = ChatGroup::with('messages.user')->findOrFail($id);
        
        // Check if user is a member
        if (!$group->members()->where('user_id', Auth::id())->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $formattedMessages = $group->messages->map(function ($msg) {
            return [
                'id' => $msg->id,
                'user_id' => $msg->user_id,
                'user_name' => $msg->user->name,
                'user_role' => strtoupper($msg->user->role),
                'message' => $msg->message,
                'image_url' => $msg->image_path ? Storage::url($msg->image_path) : null,
                'time' => $msg->created_at->format('H:i'),
                'is_own' => $msg->user_id == Auth::id()
            ];
        });

        return response()->json([
            'success' => true,
            'messages' => $formattedMessages
        ]);
    }
}
