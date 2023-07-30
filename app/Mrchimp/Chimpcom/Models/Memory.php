<?php

/**
 * Memory model
 */

namespace Mrchimp\Chimpcom\Models;

use App\Mrchimp\Chimpcom\Traits\HasProject;
use App\User;
use Database\Factories\MemoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Traits\HasTags;

/**
 * Memory model
 */
class Memory extends Model
{
    use HasFactory, HasProject, HasTags;

    protected $fillable = [
        'name',
        'content',
        'user_id',
        'public',
    ];

    /**
     * The owner/creator of this memory
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Filter by visibility
     */
    public function scopeVisibility(Builder $query, string $type = 'both'): void
    {
        if (Auth::check()) {
            $user_id = Auth::user()->id;
        } else {
            if ($type !== 'mine') {
                $type = 'public';
            }
        }

        switch ($type) {
            case 'public':
                $query->where('public', 1);
                break;
            case 'private':
                $query->where('public', 0)
                    ->where('user_id', $user_id);
                break;
            case 'mine':
                $query->where('user_id', $user_id);
                break;
            default:
                $query->where(function ($query) use ($user_id) {
                    $query->where('user_id', $user_id)
                        ->orWhere('public', 1);
                });
                break;
        }
    }

    /**
     * Filter memories by fuzzy searching name and description
     */
    public function scopeSearch(Builder $query, string $search_str): void
    {
        $query->where(function ($query) use ($search_str) {
            $query->where('name', 'LIKE', $search_str)
                ->orWhere('content', 'LIKE', $search_str);
        });
    }

    /**
     * Checks whether the memory belongs to the current user
     */
    public function isMine(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        return (int) $user->id === (int) $this->user_id;
    }

    protected static function newFactory()
    {
        return MemoryFactory::new();
    }
}
