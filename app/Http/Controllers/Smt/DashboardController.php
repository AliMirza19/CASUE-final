<?php

namespace App\Http\Controllers\Smt;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventSocialLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
        public function index()
    {
        $announcements = \App\Models\Announcement::with('creator')->latest()->take(6)->get();
        return view('dashboards.smt', compact('announcements'));
    }

    public function overview()
    {
        $user   = Auth::user();
        $termId = $user->current_term_id;

        $approvedEvents = Event::where('status', 'approved')
            ->where('term_id', $termId)
            ->orderBy('expected_date', 'asc')
            ->get();

        $myLinks = EventSocialLink::with('event')
            ->where('posted_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalLinks     = $myLinks->count();
        $published      = $myLinks->where('status', 'published')->count();
        $scheduled      = $myLinks->where('status', 'scheduled')->count();

        $announcements = \App\Models\Announcement::with('creator')
            ->latest()->take(5)->get();

        return view('smt.overview', compact(
            'approvedEvents', 'myLinks', 'totalLinks', 'published', 'scheduled', 'announcements'
        ));
    }

    public function addLink(Request $request, $eventId)
    {
        $request->validate([
            'platform' => 'required|in:instagram,linkedin,facebook,twitter,youtube,whatsapp',
            'post_url' => 'required|url|max:2048',
            'status'   => 'required|in:draft,scheduled,published',
            'notes'    => 'nullable|string|max:500',
            'posted_at'=> 'nullable|date',
        ]);

        EventSocialLink::create([
            'event_id'  => $eventId,
            'posted_by' => Auth::id(),
            'platform'  => $request->platform,
            'post_url'  => $request->post_url,
            'status'    => $request->status,
            'notes'     => $request->notes,
            'posted_at' => $request->posted_at,
        ]);

        return redirect()->route('smt.dashboard')
            ->with('success', 'Social media link added successfully!');
    }

    public function destroy($id)
    {
        EventSocialLink::where('posted_by', Auth::id())->findOrFail($id)->delete();
        return back()->with('success', 'Link removed.');
    }

    public function profile()
    {
        return view('smt.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate(['name' => 'required|string|max:255', 'contact_number' => 'nullable|string|max:20']);
        $user->update($request->only('name', 'contact_number'));
        return back()->with('success', 'Profile updated.');
    }
}
