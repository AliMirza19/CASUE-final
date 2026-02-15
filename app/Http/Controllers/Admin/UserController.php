<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AcademicTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        if ($request->role) {
            $query->where('role', $request->role);
        }
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('reg_id', 'like', '%' . $request->search . '%');
            });
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.users.index', compact('users'));
    }
    
    public function create()
    {
        $terms = AcademicTerm::orderBy('created_at', 'desc')->get();
        
        return view('admin.users.create', compact('terms'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'reg_id' => 'required|string|max:50|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:admin,hod,patron,president,student,sa,vc,gd',
            'current_term_id' => 'required|exists:academic_terms,id'
        ]);
        
        User::create([
            'reg_id' => $request->reg_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('123456'), // Default password
            'role' => $request->role,
            'password_changed' => false,
            'current_term_id' => $request->current_term_id
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully! Default password: 123456');
    }
    
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $terms = AcademicTerm::orderBy('created_at', 'desc')->get();
        
        return view('admin.users.edit', compact('user', 'terms'));
    }
    
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'reg_id' => 'required|string|max:50|unique:users,reg_id,' . $id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,hod,patron,president,student,sa,vc,gd',
            'current_term_id' => 'required|exists:academic_terms,id'
        ]);
        
        $user->update([
            'reg_id' => $request->reg_id,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'current_term_id' => $request->current_term_id
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }
    
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $user->password = Hash::make('123456');
        $user->password_changed = false;
        $user->save();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Password reset to 123456');
    }
    
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->role === 'admin') {
            return back()->with('error', 'Cannot delete admin users!');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }
}
