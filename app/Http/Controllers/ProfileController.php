<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use App\Notifications\UserProfileUpdatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;

class ProfileController extends Controller
{
    /**
     * Show the user profile page.
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Update the user profile details.
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = $path;
        }

        $user->update($data);

        // Notify Admins
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new UserProfileUpdatedNotification($user));

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the user password.
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The provided password does not match our records.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
            'password_changed' => true,
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    /**
     * Switch user active role.
     */
    public function switchRole(Request $request)
    {
        $request->validate([
            'role' => 'required|string',
        ]);

        $role = $request->input('role');
        $user = Auth::user();

        // Validate allowed roles to switch to
        $allowedRoles = [];
        
        // President team members can switch to student
        $presidentTeam = ['president', 'sa', 'vc', 'gd', 'photo', 'video', 'smt', 'doc', 'deco'];
        if (in_array($user->role, $presidentTeam)) {
            $allowedRoles[] = 'student';
            $allowedRoles[] = $user->role;
        }

        // HOD / Patron can switch to faculty
        if ($user->role === 'faculty') {
            if ($user->isAppointedHod()) {
                $allowedRoles[] = 'hod';
            }
            if ($user->isAppointedPatron()) {
                $allowedRoles[] = 'patron';
            }
            $allowedRoles[] = 'faculty';
        }

        if (!in_array($role, $allowedRoles)) {
            return back()->with('error', 'Unauthorized role switch.');
        }

        session(['active_role' => $role]);

        return redirect('/')->with('success', 'Switched to ' . ucfirst($role) . ' dashboard.');
    }
}
