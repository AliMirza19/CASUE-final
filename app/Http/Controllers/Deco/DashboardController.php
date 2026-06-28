<?php

namespace App\Http\Controllers\Deco;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventDecorationPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
        public function index()
    {
        $announcements = \App\Models\Announcement::with('creator')->latest()->take(6)->get();
        return view('dashboards.deco', compact('announcements'));
    }

    public function overview()
    {
        $user   = Auth::user();
        $termId = $user->current_term_id;

        $approvedEvents = Event::where('status', 'approved')
            ->where('term_id', $termId)
            ->orderBy('expected_date', 'asc')
            ->get();

        $myPlans = EventDecorationPlan::with('event')
            ->where('created_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPlans   = $myPlans->count();
        $donePlans    = $myPlans->where('status', 'done')->count();
        $inProgress   = $myPlans->where('status', 'in_progress')->count();
        $totalBudget  = $myPlans->sum('estimated_budget');

        $announcements = \App\Models\Announcement::with('creator')
            ->latest()->take(5)->get();

        return view('deco.overview', compact(
            'approvedEvents', 'myPlans', 'totalPlans', 'donePlans', 'inProgress', 'totalBudget', 'announcements'
        ));
    }

    public function createPlan(Request $request, $eventId)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('deco.overview');
        }

        $request->validate([
            'plan_description' => 'nullable|string',
            'estimated_budget' => 'nullable|numeric|min:0',
            'status'           => 'required|in:not_started,in_progress,done',
            'notes'            => 'nullable|string|max:1000',
            'materials'        => 'nullable|array',
            'materials.*.item' => 'required|string|max:200',
            'materials.*.qty'  => 'required|integer|min:1',
            'materials.*.cost' => 'nullable|numeric|min:0',
            'setup_photos'     => 'nullable|array',
            'setup_photos.*'   => 'image|max:5120',
        ]);

        $user  = Auth::user();
        $event = Event::findOrFail($eventId);

        $photoPaths = [];
        if ($request->hasFile('setup_photos')) {
            foreach ($request->file('setup_photos') as $photo) {
                $photoPaths[] = $photo->store('deco-photos', 'public');
            }
        }

        EventDecorationPlan::create([
            'event_id'         => $event->id,
            'created_by'       => $user->id,
            'plan_description' => $request->plan_description,
            'material_list'    => $request->materials ?? [],
            'estimated_budget' => $request->estimated_budget ?? 0,
            'status'           => $request->status,
            'setup_photos'     => $photoPaths,
            'notes'            => $request->notes,
        ]);

        return redirect()->route('deco.dashboard')
            ->with('success', 'Decoration plan created successfully!');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:not_started,in_progress,done']);
        $plan = EventDecorationPlan::where('created_by', Auth::id())->findOrFail($id);
        $plan->update(['status' => $request->status]);
        return back()->with('success', 'Status updated!');
    }

    public function show($id)
    {
        $plan = EventDecorationPlan::with('event')->findOrFail($id);
        return view('deco.plan-detail', compact('plan'));
    }

    public function profile()
    {
        return view('deco.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate(['name' => 'required|string|max:255', 'contact_number' => 'nullable|string|max:20']);
        $user->update($request->only('name', 'contact_number'));
        return back()->with('success', 'Profile updated.');
    }
}
