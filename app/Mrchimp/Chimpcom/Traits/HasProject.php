<?php

namespace App\Mrchimp\Chimpcom\Traits;

use Illuminate\Contracts\Database\Query\Builder;
use Mrchimp\Chimpcom\Models\Project;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasProject
{
    /**
     * That project that this relates to
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Filter projects by project ID
     */
    public function scopeForProject(Builder $query, int $value = null): void
    {
        if (!is_null($value)) {
            $query->where('project_id', $value);
        }
    }
}
