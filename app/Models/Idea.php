<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'title', 'description', 'category', 'status', 'current_stage', 'is_published', 'featured_image', 'tags', 'upvotes_count', 'downvotes_count', 'comments_count'])]
class Idea extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_published' => 'boolean',
            'upvotes_count' => 'integer',
            'downvotes_count' => 'integer',
            'comments_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function teamPositions(): HasMany
    {
        return $this->hasMany(TeamPosition::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(WorkflowStage::class, 'current_stage', 'slug');
    }

    /**
     * Get the net score (upvotes - downvotes).
     */
    public function getScoreAttribute(): int
    {
        return $this->upvotes_count - $this->downvotes_count;
    }

    /**
     * Check if a specific user has voted on this idea.
     */
    public function hasVotedBy(User $user): bool
    {
        return $this->votes()->where('user_id', $user->id)->exists();
    }

    /**
     * Get the vote type of a specific user.
     */
    public function getUserVoteType(User $user): ?string
    {
        return $this->votes()->where('user_id', $user->id)->value('type');
    }

    /**
     * Scope to filter published ideas.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter by stage.
     */
    public function scopeStage($query, string $stage)
    {
        return $query->where('current_stage', $stage);
    }

    /**
     * Scope to order by popularity (most upvotes).
     */
    public function scopePopular($query)
    {
        return $query->orderByDesc('upvotes_count');
    }

    /**
     * Recalculate vote counts from the votes table.
     */
    public function recalculateVotes(): void
    {
        $this->update([
            'upvotes_count' => $this->votes()->where('type', 'upvote')->count(),
            'downvotes_count' => $this->votes()->where('type', 'downvote')->count(),
        ]);
    }
}
