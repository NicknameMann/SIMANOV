<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Models\User;
use App\Models\WorkflowStage;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalIdeas = Idea::published()->count();
        $totalUsers = User::count();
        $userIdeas = Idea::where('user_id', $user->id)->latest()->get();
        $userReputation = $user->reputation_points ?? 0;
        $userBadges = $user->badges;

        $recentIdeas = Idea::with(['user', 'stage'])->published()->latest()->take(5)->get();
        $topVotedIdeas = Idea::with(['user', 'stage'])->published()->orderByDesc('upvotes_count')->take(5)->get();

        $stages = WorkflowStage::active()->ordered()->get();
        $stageDistribution = [];
        foreach ($stages as $stage) {
            $stageDistribution[] = [
                'name' => $stage->name,
                'slug' => $stage->slug,
                'color' => $stage->color,
                'count' => Idea::where('current_stage', $stage->slug)->count(),
            ];
        }

        $openPositions = \App\Models\TeamPosition::where('is_open', true)
            ->with(['idea.user'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalIdeas',
            'totalUsers',
            'userIdeas',
            'userReputation',
            'userBadges',
            'recentIdeas',
            'topVotedIdeas',
            'stageDistribution',
            'openPositions'
        ));
    }
}
