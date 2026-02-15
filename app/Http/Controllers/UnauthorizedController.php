<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnauthorizedController extends Controller
{
    /**
     * Show the unauthorized access page.
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('errors.unauthorized', [
            'user' => $user,
            'userRole' => $user ? $user->role : null,
        ]);
    }
}