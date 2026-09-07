<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Models\WorkflowStage;
use Illuminate\Http\Request;

class IdeaController extends Controller
{
    /**
     * Display a listing of published ideas with filters.
     */
    public function index(Request $request)
    {
        $query = Idea::with(['user', 'stage', 'teamPositions' => fn($q) => $q->where('is_open', true)])
            ->published();

        // Search query
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->category($request->category);
        }

        // Filter by stage
        if ($request->filled('stage')) {
            $query->stage($request->stage);
        }

        // Eager load current user's vote
        if (auth()->check()) {
            $query->with(['votes' => fn($q) => $q->where('user_id', auth()->id())]);
        }

        // Sort
        $sort = $request->get('sort', 'popular');
        switch ($sort) {
            case 'latest':
            case 'new':
                $query->orderByDesc('created_at');
                break;
            case 'top':
                $query->orderByRaw('(upvotes_count - downvotes_count) DESC');
                break;
            case 'most_commented':
                $query->orderByDesc('comments_count');
                break;
            case 'popular':
            case 'hot':
            default:
                $query->orderByDesc('upvotes_count')->orderByDesc('created_at');
                break;
        }

        $ideas = $query->paginate(12)->withQueryString();

        // Attach user vote attribute
        if (auth()->check()) {
            $ideas->getCollection()->transform(function ($idea) {
                $idea->current_user_vote = $idea->votes->first()?->type;
                return $idea;
            });
        }

        $stages = WorkflowStage::active()
            ->ordered()
            ->withCount(['ideas' => fn($q) => $q->published()])
            ->get();

        // Sidebar widgets data
        $trendingIdeas = Idea::with(['user', 'stage'])
            ->published()
            ->orderByDesc('upvotes_count')
            ->take(5)
            ->get();

        $openPositions = \App\Models\TeamPosition::where('is_open', true)
            ->with(['idea.user'])
            ->latest()
            ->take(4)
            ->get();

        $topContributors = \App\Models\User::withCount('badges')
            ->orderByDesc('reputation_points')
            ->take(5)
            ->get();

        $totalIdeasCount = Idea::published()->count();
        $totalUsersCount = \App\Models\User::count();

        return view('ideas.index', compact(
            'ideas',
            'stages',
            'trendingIdeas',
            'openPositions',
            'topContributors',
            'totalIdeasCount',
            'totalUsersCount'
        ));
    }

    /**
     * Show the form for creating a new idea.
     */
    public function create()
    {
        return view('ideas.create');
    }

    /**
     * Store a newly created idea.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'category' => 'required|in:teknologi,sosial,pendidikan,bisnis,lainnya',
            'tags' => 'nullable|array|max:5',
            'tags.*' => 'string|max:50',
        ]);

        $idea = auth()->user()->ideas()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'tags' => $validated['tags'] ?? null,
            'is_published' => true,
            'current_stage' => 'ideation',
            'status' => 'published',
        ]);

        // Award reputation points for creating an idea
        auth()->user()->increment('reputation_points', 10);

        return redirect()->route('ideas.show', $idea)
            ->with('success', 'Ide inovasi berhasil dipublikasikan! 🎉');
    }

    /**
     * Display the specified idea.
     */
    public function show(Idea $idea)
    {
        $idea->load([
            'user',
            'stage',
            'comments' => function ($query) {
                $query->whereNull('parent_id')
                    ->with(['user', 'replies.user'])
                    ->latest();
            },
            'teamPositions.applications.user',
        ]);

        $userVoteType = null;
        if (auth()->check()) {
            $userVoteType = $idea->getUserVoteType(auth()->user());
        }

        $stages = WorkflowStage::active()->ordered()->get();

        return view('ideas.show', compact('idea', 'userVoteType', 'stages'));
    }

    /**
     * Show the form for editing the specified idea.
     */
    public function edit(Idea $idea)
    {
        if ($idea->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('ideas.edit', compact('idea'));
    }

    /**
     * Update the specified idea.
     */
    public function update(Request $request, Idea $idea)
    {
        if ($idea->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'category' => 'required|in:teknologi,sosial,pendidikan,bisnis,lainnya',
            'tags' => 'nullable|array|max:5',
            'tags.*' => 'string|max:50',
        ]);

        $idea->update($validated);

        return redirect()->route('ideas.show', $idea)
            ->with('success', 'Ide berhasil diperbarui! ✅');
    }

    /**
     * Remove the specified idea (soft delete).
     */
    public function destroy(Idea $idea)
    {
        if ($idea->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $idea->delete();

        return redirect()->route('ideas.index')
            ->with('success', 'Ide berhasil dihapus.');
    }
}
