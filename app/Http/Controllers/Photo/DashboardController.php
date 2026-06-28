<?php

namespace App\Http\Controllers\Photo;

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
        return view('dashboards.photo', compact('announcements'));
    }

    public function overview()
    {
        $user   = Auth::user();
        $termId = $user->current_term_id;

        $approvedEvents = Event::where('status', 'approved')
            ->where('term_id', $termId)
            ->orderBy('expected_date', 'asc')
            ->get();

        $myPhotos = EventMedia::with('event')
            ->where('uploaded_by', $user->id)
            ->where('media_type', 'photo')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalEvents  = $approvedEvents->count();
        $totalPhotos  = $myPhotos->count();
        $thisMonth    = $myPhotos->filter(fn($m) => $m->created_at->isCurrentMonth())->count();

        $announcements = \App\Models\Announcement::with('creator')
            ->latest()->take(5)->get();

        return view('photo.overview', compact(
            'approvedEvents', 'myPhotos', 'totalEvents', 'totalPhotos', 'thisMonth', 'announcements'
        ));
    }

    public function upload(Request $request, $eventId)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('photo.overview');
        }

        $request->validate([
            'photos'                 => 'required|array|min:1',
            'photos.*'               => 'image|max:10240', // 10MB each
            'tagged_reg_number'      => 'nullable|string|max:50',
            'tagged_role'            => 'nullable|string|max:100',
            'caption'                => 'nullable|string|max:255',
        ]);

        $user  = Auth::user();
        $event = Event::findOrFail($eventId);
        $count = 0;

        foreach ($request->file('photos') as $photo) {
            $path = $photo->store('event-photos', 'public');
            EventMedia::create([
                'event_id'           => $event->id,
                'uploaded_by'        => $user->id,
                'media_type'         => 'photo',
                'file_path'          => $path,
                'original_filename'  => $photo->getClientOriginalName(),
                'tagged_reg_number'  => $request->tagged_reg_number,
                'tagged_role'        => $request->tagged_role,
                'caption'            => $request->caption,
                'file_size'          => $photo->getSize(),
            ]);
            $count++;
        }

        return redirect()->route('photo.dashboard')
            ->with('success', "{$count} photo(s) uploaded successfully!");
    }

    public function destroy($id)
    {
        $media = EventMedia::where('uploaded_by', Auth::id())->findOrFail($id);
        if ($media->file_path) {
            Storage::disk('public')->delete($media->file_path);
        }
        $media->delete();

        return back()->with('success', 'Photo deleted successfully.');
    }

    public function profile()
    {
        return view('photo.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name'           => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
        ]);
        $user->update($request->only('name', 'contact_number'));
        return back()->with('success', 'Profile updated.');
    }
}
