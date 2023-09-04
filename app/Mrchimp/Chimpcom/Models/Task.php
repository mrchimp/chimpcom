<?php

/**
 * Task
 */

namespace Mrchimp\Chimpcom\Models;

use App\Mrchimp\Chimpcom\Traits\HasProject;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mrchimp\Chimpcom\Traits\HasTags;

/**
 * Task
 */
class Task extends Model
{
    use HasFactory, HasProject, HasTags;

    protected $fillable = [
        'description',
        'user_id',
        'project_id',
        'priority',
        'completed',
    ];

    /**
     * The user that made this task
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Filter tasks by a search word
     */
    public function scopeSearch(Builder $query, string $search_term = null): void
    {
        if ($search_term) {
            $query->where('description', 'LIKE', '%' . $search_term . '%');
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

    /**
     * Mark the task as done
     */
    public function markAsDone(): void
    {
        $this->completed = true;
        $this->time_completed = now();
        $this->save();
    }

    protected static function newFactory()
    {
        return TaskFactory::new();
    }
}
