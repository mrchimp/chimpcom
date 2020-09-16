<?php

/**
 * Todo list task
 */

namespace Mrchimp\Chimpcom\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Todo list task
 */
class Task extends Model
{
    /**
     * The user that made this task
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    /**
     * The project that this task is for
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo('Mrchimp\Chimpcom\Models\Project');
    }

    /**
     * Filter tasks by a search word
     */
    public function scopeSearch(Builder $query, string $search_term = null): void
    {
        if ($search_term) {
            $query->where('description', 'LIKE', '%'.$search_term.'%');
        }
    }

    /**
     * Filter complete or incomplete tasks
     */
    public function scopeCompleted(Builder $query, bool $value = null): void
    {
        if (!is_null($value)) {
            $query->where('completed', $value);
        }
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
