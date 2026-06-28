<?php

namespace App\Http\Controllers\Doc;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
        public function index()
    {
        $announcements = \App\Models\Announcement::with('creator')->latest()->take(6)->get();
        return view('dashboards.doc', compact('announcements'));
    }

    public function overview()
    {
        $user   = Auth::user();
        $termId = $user->current_term_id;

        $approvedEvents = Event::where('status', 'approved')
            ->where('term_id', $termId)
            ->orderBy('expected_date', 'asc')
            ->get();

        $myDocs = EventDocument::with('event')
            ->where('uploaded_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalDocs   = $myDocs->count();
        $financialCount = $myDocs->where('doc_type', 'financial_report')->count();
        $generalCount   = $myDocs->where('doc_type', 'general_documentation')->count();

        $announcements = \App\Models\Announcement::with('creator')
            ->latest()->take(5)->get();

        return view('doc.overview', compact(
            'approvedEvents', 'myDocs', 'totalDocs', 'financialCount', 'generalCount', 'announcements'
        ));
    }

    public function upload(Request $request, $eventId)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('doc.overview');
        }

        $request->validate([
            'document'    => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:20480',
            'doc_type'    => 'required|in:financial_report,approval_form,general_documentation,poster_graphic',
            'description' => 'nullable|string|max:500',
        ]);

        $user  = Auth::user();
        $event = Event::findOrFail($eventId);
        $file  = $request->file('document');
        $path  = $file->store('event-documents', 'public');

        EventDocument::create([
            'event_id'         => $event->id,
            'uploaded_by'      => $user->id,
            'doc_type'         => $request->doc_type,
            'file_path'        => $path,
            'original_filename'=> $file->getClientOriginalName(),
            'description'      => $request->description,
            'visible_to_roles' => ['president', 'hod', 'patron', 'faculty', 'admin'],
        ]);

        return redirect()->route('doc.dashboard')
            ->with('success', 'Document uploaded successfully!');
    }

    public function download($id)
    {
        $doc = EventDocument::findOrFail($id);
        $userRole = Auth::user()->role;

        if (!$doc->isVisibleTo($userRole) && $doc->uploaded_by !== Auth::id()) {
            abort(403, 'You do not have permission to download this document.');
        }

        return Storage::disk('public')->download($doc->file_path, $doc->original_filename);
    }

    public function destroy($id)
    {
        $doc = EventDocument::where('uploaded_by', Auth::id())->findOrFail($id);
        Storage::disk('public')->delete($doc->file_path);
        $doc->delete();
        return back()->with('success', 'Document deleted.');
    }

    public function profile()
    {
        return view('doc.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate(['name' => 'required|string|max:255', 'contact_number' => 'nullable|string|max:20']);
        $user->update($request->only('name', 'contact_number'));
        return back()->with('success', 'Profile updated.');
    }
}
