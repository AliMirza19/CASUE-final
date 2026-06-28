<?php

namespace App\Http\Controllers\Patron;

use App\Http\Controllers\Controller;
use App\Models\AcademicTerm;
use App\Models\ElectionSetting;
use Illuminate\Http\Request;

class ElectionSettingController extends Controller
{
    public function index()
    {
        $activeTerm = AcademicTerm::getActive();
        
        if (!$activeTerm) {
            return redirect()->route('patron.dashboard')->with('error', 'No active academic term found.');
        }

        $settings = ElectionSetting::firstOrCreate(
            ['term_id' => $activeTerm->id],
            [
                'is_active' => false,
                'registration_start' => null,
                'registration_end' => null,
                'voting_start' => null,
                'voting_end' => null,
            ]
        );

        return view('patron.election.settings', compact('settings', 'activeTerm'));
    }

    public function update(Request $request)
    {
        $activeTerm = AcademicTerm::getActive();
        
        if (!$activeTerm) {
            return redirect()->back()->with('error', 'No active academic term found.');
        }

        $request->validate([
            'registration_start' => 'nullable|date',
            'registration_end' => 'nullable|date|after_or_equal:registration_start',
            'voting_start' => 'nullable|date|after:registration_end',
            'voting_end' => 'nullable|date|after_or_equal:voting_start',
            'is_active' => 'boolean',
        ]);

        $settings = ElectionSetting::updateOrCreate(
            ['term_id' => $activeTerm->id],
            [
                'registration_start' => $request->registration_start,
                'registration_end' => $request->registration_end,
                'voting_start' => $request->voting_start,
                'voting_end' => $request->voting_end,
                'is_active' => $request->has('is_active'),
            ]
        );

        return redirect()->route('patron.election.settings')->with('success', 'Election settings updated successfully!');
    }
}
