<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['idea_id', 'title', 'description', 'skills_required', 'max_applicants', 'is_open'])]
class TeamPosition extends Model
{
    protected function casts(): array
    {
        return [
            'skills_required' => 'array',
            'is_open' => 'boolean',
            'max_applicants' => 'integer',
        ];
    }

    public function idea(): BelongsTo
    {
        return $this->belongsTo(Idea::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(TeamApplication::class);
    }

    public function acceptedApplications(): HasMany
    {
        return $this->hasMany(TeamApplication::class)->where('status', 'accepted');
    }

    public function getIsFulfilledAttribute(): bool
    {
        return $this->acceptedApplications()->count() >= $this->max_applicants;
    }
}
