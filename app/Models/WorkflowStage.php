<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'order', 'auto_advance_threshold', 'color', 'icon', 'is_active'])]
class WorkflowStage extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'auto_advance_threshold' => 'integer',
            'order' => 'integer',
        ];
    }

    public function ideas(): HasMany
    {
        return $this->hasMany(Idea::class, 'current_stage', 'slug');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
