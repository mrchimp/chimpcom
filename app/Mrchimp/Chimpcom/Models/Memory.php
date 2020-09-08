<?php
/**
 * Memory model
 */

namespace Mrchimp\Chimpcom\Models;

use App\User;
use Auth;
use Illuminate\Database\Eloquent\Model;

/**
 * Memory model
 */
class Memory extends Model
{
    /**
     * Memories can have multiple tags
     */
    public function tags()
    {
        return $this->morphToMany('Mrchimp\Chimpcom\Models\Tag', 'taggable');
    }

    /**
     * The owner/creator of this memory
     * @return App\User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Filter by visibility
     * @param  [type] $query [description]
     * @param  string $type  [description]
     * @return [type]        [description]
     */
    public function scopeVisibility($query, $type = 'both')
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

        return $query;
    }

    /**
     * Filter memories by fuzzy searching name and description
     *
     * @param  Query $query
     * @param  String $search_str
     * @return Query
     */
    public function scopeSearch($query, $search_str)
    {
        return $query->where(function ($query) use ($search_str) {
            $query->where('name', 'LIKE', $search_str)
                ->orWhere('content', 'LIKE', $search_str);
        });
    }

    /**
     * Checks whether the memory belongs to the current user
     * @return boolean True if memory is users
     */
    public function isMine()
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        return $user->id === $this->user_id;
    }
}
