<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Models\TeamPosition;
use App\Models\TeamApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function storePosition(Request $request, Idea $idea)
    {
        if ($idea->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'skills_required' => 'nullable|array',
        ]);

        $position = new TeamPosition($validated);
        $position->idea_id = $idea->id;
        $position->save();

        return redirect()->back()->with('success', 'Team position added successfully.');
    }

    public function apply(Request $request, TeamPosition $position)
    {
        $validated = $request->validate([
            'message' => 'nullable|string',
        ]);

        $existing = TeamApplication::where('team_position_id', $position->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'You have already applied for this position.');
        }

        $application = new TeamApplication($validated);
        $application->team_position_id = $position->id;
        $application->user_id = Auth::id();
        $application->status = 'pending';
        $application->save();

        return redirect()->back()->with('success', 'Application submitted successfully.');
    }

    public function updateApplication(Request $request, TeamApplication $application)
    {
        $idea = $application->teamPosition->idea;
        
        if ($idea->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $application->update($validated);

        return redirect()->back()->with('success', 'Application status updated.');
    }
}
