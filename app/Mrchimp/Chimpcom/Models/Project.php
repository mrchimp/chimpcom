<?php

/**
 * Chimpcom Todo list Project model
 */

namespace Mrchimp\Chimpcom\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mrchimp\Chimpcom\Models\Task;

/**
 * Chimpcom Todo list Project model
 */
class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_new',
        'name',
        'user_id',
    ];

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

    protected static function newFactory()
    {
        return ProjectFactory::new();
    }
}
