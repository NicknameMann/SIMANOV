<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'slug', 'description', 'icon', 'criteria_type', 'criteria_threshold'])]
class Badge extends Model
{
    protected function casts(): array
    {
        return [
            'criteria_threshold' => 'integer',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('awarded_at')->withTimestamps();
    }
}
