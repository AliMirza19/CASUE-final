<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\AcademicTerm;
use App\Models\Announcement;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    /**
     * Display the faculty dashboard with overview stats.
     */
    public function index()
    {
        $announcements = \App\Models\Announcement::with('creator')->latest()->take(6)->get();
        return view('faculty.dashboard', compact('announcements'));
    }

    public function overview(): View
    {
        $activeTerm = AcademicTerm::getActive();
        
        $stats = [
            'approved_events' => 0,
            'upcoming_events' => 0,
            'total_societies' => 0,
            'announcements' => 0,
        ];
        
        $recentEvents = collect();
        $announcements = collect();
        
        if ($activeTerm) {
            // Approved events count
            $stats['approved_events'] = Event::where('term_id', $activeTerm->id)
                ->where('status', 'approved')
                ->count();
            
            // Upcoming events (approved with future date)
            $stats['upcoming_events'] = Event::where('term_id', $activeTerm->id)
                ->where('status', 'approved')
                ->where('expected_date', '>=', now()->toDateString())
                ->count();
            
            // Recent approved events (last 5)
            $recentEvents = Event::where('term_id', $activeTerm->id)
                ->where('status', 'approved')
                ->with('student')
                ->orderBy('expected_date', 'desc')
                ->limit(5)
                ->get();
        }
        
        // Get announcements for faculty
        $announcements = Announcement::getActiveForRole('faculty');
        $stats['announcements'] = $announcements->count();
        
        return view('faculty.overview', compact('stats', 'recentEvents', 'announcements', 'activeTerm'));
    }
    
    /**
     * Display all approved events with filtering.
     */
    public function events(Request $request): View
    {
        $activeTerm = AcademicTerm::getActive();
        $filter = $request->get('filter', 'all');
        $search = $request->get('search', '');
        
        $query = Event::query();
        
        if ($activeTerm) {
            $query->where('term_id', $activeTerm->id);
        }
        
        $query->where('status', 'approved');
        
        // Apply filter
        if ($filter === 'upcoming') {
            $query->where('expected_date', '>=', now()->toDateString());
        } elseif ($filter === 'past') {
            $query->where('expected_date', '<', now()->toDateString());
        }
        
        // Apply search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $events = $query->with('student')
            ->orderBy('expected_date', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        return view('faculty.events', compact('events', 'filter', 'search', 'activeTerm'));
    }
    
    /**
     * Display single event details.
     */
    public function showEvent(int $id): View
    {
        $event = Event::with(['student', 'items', 'graphics', 'volunteers'])
            ->where('status', 'approved')
            ->findOrFail($id);
        
        return view('faculty.event-detail', compact('event'));
    }
    
    /**
     * Display societies directory (placeholder).
     */
    public function societies(): View
    {
        return view('faculty.societies');
    }
    
    /**
     * Display profile page.
     */
    public function profile(): View
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return view('profile.show', compact('user'));
    }
    
    /**
     * Update profile (email/password).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        // Check if updating email
        if ($request->has('email') && $request->email !== $user->email) {
            $request->validate([
                'email' => 'required|email|unique:users,email,' . $user->id,
            ]);
            
            $user->email = $request->email;
            $user->save();
            
            return redirect()->route('faculty.profile')
                ->with('success', 'Email updated successfully.');
        }
        
        // Check if changing password
        if ($request->filled('current_password')) {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ]);
            
            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->route('faculty.profile')
                    ->with('error', 'Current password is incorrect.');
            }
            
            $user->password = Hash::make($request->new_password);
            $user->password_changed = true;
            $user->save();
            
            return redirect()->route('faculty.profile')
                ->with('success', 'Password changed successfully.');
        }
        
        return redirect()->route('faculty.profile');
    }
}
