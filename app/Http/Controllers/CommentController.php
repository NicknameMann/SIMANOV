<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Idea $idea)
    {
        $validated = $request->validate([
            'body' => 'required|string|min:3',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $comment = new Comment($validated);
        $comment->user_id = Auth::id();
        $comment->idea_id = $idea->id;
        $comment->save();

        $idea->increment('comments_count');
        Auth::user()->increment('reputation_points', 2);

        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan! 💬');
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $idea = $comment->idea;
        $comment->delete();

        if ($idea) {
            $idea->decrement('comments_count');
        }

        return redirect()->back()->with('success', 'Komentar berhasil dihapus.');
    }
}
