<?php

namespace App\Http\Controllers;

use App\Models\AcademicTerm;
use App\Models\Event;
use App\Models\EventDocument;
use App\Models\EventMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArchiveController extends Controller
{
    /**
     * Level 1: Display all Terms
     */
    public function index()
    {
        $terms = AcademicTerm::orderBy('term_code', 'desc')->get();
        return view('archive.index', compact('terms'));
    }

    /**
     * Level 2: Display all 'completed' events under a specific term
     */
    public function showTerm(AcademicTerm $term)
    {
        $events = Event::where('term_id', $term->id)
            ->whereIn('status', ['approved', 'completed'])
            ->get();
            
        $termDocuments = EventDocument::where('term_id', $term->id)
            ->whereNull('event_id')
            ->get();
            
        return view('archive.term_events', compact('term', 'events', 'termDocuments'));
    }

    /**
     * Level 3: Fetch all documents grouped by document_type for a specific event
     */
    public function showEventArchive(Event $event)
    {
        // Ensure event is approved or completed
        if (!in_array($event->status, ['approved', 'completed'])) {
            abort(404, 'Archive only available for approved or completed events.');
        }

        // Fetch documents from EventDocument
        $documents = EventDocument::with('uploader')->where('event_id', $event->id)->get()->groupBy('doc_type');
        
        // Fetch media from EventMedia and merge into 'event_media' section
        $mediaFiles = \App\Models\EventMedia::with('uploader')->where('event_id', $event->id)->get();
        
        if ($mediaFiles->isNotEmpty()) {
            // Ensure the collection is a Base Collection for grouping/merging
            $archiveMedia = $documents->get('event_media', collect());
            
            // Map EventMedia to match the structure expected in the view (original_filename, file_path, etc)
            $mappedMedia = $mediaFiles->map(function($m) {
                $m->doc_type = 'event_media'; // Ensure it has a doc_type for internal logic
                return $m;
            });

            // Merge and put back into the documents collection
            $documents->put('event_media', $archiveMedia->concat($mappedMedia));
        }
        
        return view('archive.event_archive', compact('event', 'documents'));
    }
}
