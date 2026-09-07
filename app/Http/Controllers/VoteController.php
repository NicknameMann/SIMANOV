<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Models\Vote;
use App\Models\WorkflowStage;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    /**
     * Toggle vote on an idea (upvote/downvote).
     */
    public function store(Request $request, Idea $idea)
    {
        $request->validate([
            'type' => 'required|in:upvote,downvote',
        ]);

        $user = auth()->user();
        $existingVote = Vote::where('user_id', $user->id)
            ->where('idea_id', $idea->id)
            ->first();

        if ($existingVote) {
            if ($existingVote->type === $request->type) {
                // Same vote type: remove vote (toggle off)
                $existingVote->delete();

                // Remove reputation points
                if ($request->type === 'upvote') {
                    $idea->user->decrement('reputation_points', 5);
                }
            } else {
                // Different vote type: change vote
                $existingVote->update(['type' => $request->type]);

                // Adjust reputation
                if ($request->type === 'upvote') {
                    $idea->user->increment('reputation_points', 10); // +5 for removing downvote, +5 for new upvote
                } else {
                    $idea->user->decrement('reputation_points', 10);
                }
            }
        } else {
            // New vote
            Vote::create([
                'user_id' => $user->id,
                'idea_id' => $idea->id,
                'type' => $request->type,
            ]);

            // Award reputation
            $user->increment('reputation_points', 1); // +1 for voting
            if ($request->type === 'upvote') {
                $idea->user->increment('reputation_points', 5); // +5 to idea owner
            }
        }

        // Recalculate cached vote counts
        $idea->recalculateVotes();
        $idea->refresh();

        // Check auto-advance to next workflow stage
        $this->checkAutoAdvance($idea);

        // Get updated user vote type
        $userVoteType = Vote::where('user_id', $user->id)
            ->where('idea_id', $idea->id)
            ->value('type');

        return response()->json([
            'upvotes_count' => $idea->upvotes_count,
            'downvotes_count' => $idea->downvotes_count,
            'score' => $idea->score,
            'user_vote_type' => $userVoteType,
        ]);
    }

    /**
     * Check if idea should auto-advance to next workflow stage.
     */
    private function checkAutoAdvance(Idea $idea): void
    {
        $currentStage = WorkflowStage::where('slug', $idea->current_stage)->first();

        if (!$currentStage || !$currentStage->auto_advance_threshold) {
            return;
        }

        if ($idea->upvotes_count >= $currentStage->auto_advance_threshold) {
            $nextStage = WorkflowStage::active()
                ->where('order', '>', $currentStage->order)
                ->orderBy('order')
                ->first();

            if ($nextStage) {
                $idea->update(['current_stage' => $nextStage->slug]);
            }
        }
    }
}
