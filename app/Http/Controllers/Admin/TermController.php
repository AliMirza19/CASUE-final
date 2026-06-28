<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicTerm;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function index()
    {
        $terms = AcademicTerm::orderBy('created_at', 'desc')->get();
        
        return view('admin.terms.index', compact('terms'));
    }
    
    public function create()
    {
        $suggestion = AcademicTerm::suggestNextTerm();
        return view('admin.terms.create', compact('suggestion'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'term_name' => 'required|string|max:100',
            'term_code' => 'required|string|max:10',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);
        
        AcademicTerm::create([
            'term_name' => $request->term_name,
            'term_code' => $request->term_code,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'inactive'
        ]);
        
        return redirect()->route('admin.terms.index')
            ->with('success', 'Academic term created successfully!');
    }
    
    public function activate($id)
    {
        // Deactivate all terms first
        AcademicTerm::where('status', 'active')->update(['status' => 'inactive']);
        
        // Activate selected term
        $term = AcademicTerm::findOrFail($id);
        $term->status = 'active';
        $term->save();
        
        return redirect()->route('admin.terms.index')
            ->with('success', 'Term activated successfully!');
    }
    
    public function deactivate($id)
    {
        $term = AcademicTerm::findOrFail($id);
        $term->status = 'inactive';
        $term->save();
        
        return redirect()->route('admin.terms.index')
            ->with('success', 'Term deactivated.');
    }
}
