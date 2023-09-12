<?php

namespace Mrchimp\Chimpcom\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Builder;
use Mrchimp\Chimpcom\Models\Memory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected static $tag_regex = '/@([\w\-_]+)\b/m';

    protected $fillable = [
        'tag',
    ];

    /**
     * related memories
     */
    public function memories()
    {
        return $this->morphedByMany(Memory::class, 'taggable');
    }

    /**
     * related tasks
     */
    public function tasks()
    {
        return $this->morphedByMany(Task::class, 'taggable');
    }

    /**
     * related memories
     */
    public function diaryEntries()
    {
        return $this->morphedByMany(DiaryEntry::class, 'taggable');
    }

    /**
     * Related events
     */
    public function events()
    {
        return $this->morphedByMany(Event::class, 'taggable');
    }

    /**
     * Filter tags to those related to a given project
     */
    public function scopeForProject(Builder $query, Project $project): void
    {
        $query->whereHas('diaryEntries', function ($query) use ($project) {
            $query->whereHas('project', function ($query) use ($project) {
                $query->where('id', $project->id);
            });
        })->orWhereHas('memories', function ($query) use ($project) {
            $query->whereHas('project', function ($query) use ($project) {
                $query->where('id', $project->id);
            });
        })->orWhereHas('tasks', function ($query) use ($project) {
            $query->whereHas('project', function ($query) use ($project) {
                $query->where('id', $project->id);
            });
        })->orWhereHas('events', function ($query) use ($project) {
            $query->whereHas('project', function ($query) use ($project) {
                $query->where('id', $project->id);
            });
        });
    }

    /**
     * Get an array of tag names from a string
     */
    public static function fromString(string $input): array
    {
        $output = [];

        preg_match_all(static::$tag_regex, $input, $output);

        return $output[1];
    }

    protected static function newFactory()
    {
        return TagFactory::new();
    }
}
