<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    /**
     * Show the form for creating a new announcement.
     */
    public function create()
    {
        // Only allow HOD, President, and Patron
        if (!Auth::user()->isAppointedHod() && Auth::user()->role !== 'president' && !Auth::user()->isAppointedPatron()) {
            abort(403, 'Unauthorized action.');
        }

        return view('announcements.create');
    }

    /**
     * Store a newly created announcement in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isAppointedHod() && Auth::user()->role !== 'president' && !Auth::user()->isAppointedPatron()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image_url' => 'nullable|url|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'link_url' => 'nullable|url|max:255',
            'target_role' => 'nullable|string',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $imageUrl = $request->input('image_url');
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('announcements', 'public');
            $imageUrl = Storage::url($path);
        }

        Announcement::create([
            'user_id' => Auth::id(),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'image_url' => $imageUrl,
            'link_url' => $request->input('link_url'),
            'target_role' => $request->input('target_role'),
            'expires_at' => $request->input('expires_at'),
        ]);

        return redirect()->back()->with('success', 'Announcement posted successfully!');
    }

    public function edit(Announcement $announcement)
    {
        if ($announcement->user_id !== Auth::id()) {
            abort(403);
        }
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        if ($announcement->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image_url' => 'nullable|url|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'link_url' => 'nullable|url|max:255',
        ]);

        $imageUrl = $request->input('image_url', $announcement->image_url);
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('announcements', 'public');
            $imageUrl = Storage::url($path);
        }

        $announcement->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'image_url' => $imageUrl,
            'link_url' => $request->input('link_url'),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Announcement updated!');
    }

    public function destroy(Announcement $announcement)
    {
        if ($announcement->user_id !== Auth::id()) {
            abort(403);
        }
        $announcement->delete();
        return redirect()->back()->with('success', 'Announcement deleted!');
    }
}
