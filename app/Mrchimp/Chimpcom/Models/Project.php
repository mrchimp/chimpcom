<?php

/**
 * Chimpcom Todo list Project model
 */

namespace Mrchimp\Chimpcom\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mrchimp\Chimpcom\Models\Task;

/**
 * Chimpcom Todo list Project model
 */
class Project extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo('app\User');
    }

    public function activeUsers(): HasMany
    {
        return $this->hasMany('app\User', 'active_project_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany('Mrchimp\Chimpcom\Models\Task');
    }

    public function delete(): ?bool
    {
        Task::where('project_id', $this->id)->delete();

        return parent::delete();
    }

    public function scopeNameOrId(Builder $query, $identifier): void
    {
        if (is_numeric($identifier)) {
            $query->where('id', $identifier);
        } else {
            $query->where('name', $identifier);
        }
    }
}
