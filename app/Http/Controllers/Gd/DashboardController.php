<?php

namespace App\Http\Controllers\Gd;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventGraphic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $announcements = \App\Models\Announcement::with('creator')->latest()->take(6)->get();
        return view('dashboards.gd', compact('announcements'));
    }

    public function overview()
    {
        $user = Auth::user();
        $termId = $user->current_term_id;
        
        // Approved events that need graphics
        $approvedEvents = Event::with('student')
            ->where('status', 'approved')
            ->where('term_id', $termId)
            ->orderBy('expected_date', 'asc')
            ->get();
        
        // My uploaded graphics
        $myGraphics = EventGraphic::with('event')
            ->where('gd_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Stats
        $totalApprovedEvents = $approvedEvents->count();
        $totalGraphicsUploaded = $myGraphics->count();
        $pendingApproval = $myGraphics->where('status', 'pending_patron')->count();
        $approvedGraphics = $myGraphics->where('status', 'approved')->count();
        
        $announcements = \App\Models\Announcement::with('creator')->latest()->take(6)->get();
        
        return view('gd.overview', compact(
            'approvedEvents',
            'myGraphics',
            'totalApprovedEvents',
            'totalGraphicsUploaded',
            'pendingApproval',
            'approvedGraphics',
            'announcements'
        ));
    }
    
    public function uploadDesign($eventId)
    {
        $event = Event::with('graphics')->findOrFail($eventId);
        
        return view('gd.upload-design', compact('event'));
    }
    
    public function saveDesign(Request $request, $eventId)
    {
        $request->validate([
            'design_category' => 'required|in:poster,banner,social_media',
            'image' => 'nullable|image|max:5120', // 5MB max
            'image_link' => 'nullable|url'
        ]);
        
        if (!$request->hasFile('image') && !$request->image_link) {
            return back()->with('error', 'Please upload an image or provide a link.');
        }
        
        $user = Auth::user();
        $event = Event::findOrFail($eventId);
        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('graphics', 'public');
        }
        
        EventGraphic::create([
            'event_id' => $eventId,
            'gd_id' => $user->id,
            'design_category' => $request->design_category,
            'image_path' => $imagePath,
            'image_link' => $request->image_link,
            'status' => 'pending_patron'
        ]);
        
        return redirect()->route('gd.dashboard')
            ->with('success', 'Design uploaded successfully! Awaiting patron approval.');
    }
    
    public function viewFeedback($id)
    {
        $graphic = EventGraphic::with(['event', 'designer'])->findOrFail($id);
        
        // Ensure this graphic belongs to the current user
        if ($graphic->gd_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this graphic.');
        }
        
        return view('gd.view-feedback', compact('graphic'));
    }

    public function getAiPersona(Request $request)
    {
        $request->validate(['event_id' => 'required|exists:events,id']);
        $event = Event::findOrFail($request->event_id);
        
        $aiService = app(\App\Services\AiCreativeEngineService::class);
        return response()->json($aiService->generateVisualPersona($event->title, $event->description));
    }

    public function getAiCopy(Request $request)
    {
        $request->validate(['event_id' => 'required|exists:events,id']);
        $event = Event::findOrFail($request->event_id);
        
        $aiService = app(\App\Services\AiCreativeEngineService::class);
        return response()->json($aiService->generateSocialMediaCopy($event->title, $event->description));
    }
}
