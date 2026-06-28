<?php

namespace App\Http\Controllers\Video;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
        public function index()
    {
        $announcements = \App\Models\Announcement::with('creator')->latest()->take(6)->get();
        return view('dashboards.video', compact('announcements'));
    }

    public function overview()
    {
        $user   = Auth::user();
        $termId = $user->current_term_id;

        $approvedEvents = Event::where('status', 'approved')
            ->where('term_id', $termId)
            ->orderBy('expected_date', 'asc')
            ->get();

        $myVideos = EventMedia::with('event')
            ->where('uploaded_by', $user->id)
            ->whereIn('media_type', ['video', 'highlight'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalEvents    = $approvedEvents->count();
        $totalVideos    = $myVideos->where('media_type', 'video')->count();
        $totalHighlights = $myVideos->where('media_type', 'highlight')->count();

        $announcements = \App\Models\Announcement::with('creator')
            ->latest()->take(5)->get();

        return view('video.overview', compact(
            'approvedEvents', 'myVideos', 'totalEvents', 'totalVideos', 'totalHighlights', 'announcements'
        ));
    }

    public function upload(Request $request, $eventId)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('video.overview');
        }

        $request->validate([
            'video'              => 'required|file|mimes:mp4,mov,avi,mkv,webm|max:512000', // 500MB
            'media_type'         => 'required|in:video,highlight',
            'tagged_reg_number'  => 'nullable|string|max:50',
            'tagged_role'        => 'nullable|string|max:100',
            'caption'            => 'nullable|string|max:255',
        ]);

        $user  = Auth::user();
        $event = Event::findOrFail($eventId);
        $file  = $request->file('video');
        $path  = $file->store('event-videos', 'public');

        EventMedia::create([
            'event_id'           => $event->id,
            'uploaded_by'        => $user->id,
            'media_type'         => $request->media_type,
            'file_path'          => $path,
            'original_filename'  => $file->getClientOriginalName(),
            'tagged_reg_number'  => $request->tagged_reg_number,
            'tagged_role'        => $request->tagged_role,
            'caption'            => $request->caption,
            'file_size'          => $file->getSize(),
        ]);

        return redirect()->route('video.dashboard')
            ->with('success', 'Video uploaded successfully!');
    }

    public function destroy($id)
    {
        $media = EventMedia::where('uploaded_by', Auth::id())->findOrFail($id);
        if ($media->file_path) {
            Storage::disk('public')->delete($media->file_path);
        }
        $media->delete();
        return back()->with('success', 'Video deleted.');
    }

    public function profile()
    {
        return view('video.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate(['name' => 'required|string|max:255', 'contact_number' => 'nullable|string|max:20']);
        $user->update($request->only('name', 'contact_number'));
        return back()->with('success', 'Profile updated.');
    }
}
