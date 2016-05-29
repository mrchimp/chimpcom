<?php
/**
 * Memory model
 */

namespace Mrchimp\Chimpcom\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Mrchimp\Chimpcom\Models\Tag as Tag;

/**
 * Memory model
 */
class Memory extends Model
{

  /**
   * Memories can have multiple tags
   */
  public function tags() {
    return $this->morphToMany('Mrchimp\Chimpcom\Models\Tag', 'taggable');
  }

  /**
   * Filter by visibility
   * @param  Builder $query The query to scope
   * @param  string  $type  Memory type. private, private, minr or both.
   * @return Builder        The altered query
   */
  public function scopeVisibility($query, $type = 'both') {
    $user_id = Auth::user()->id;

    switch ($type) {
      case 'public':
        $query->where('public', 1);
        break;
      case 'private':
        $query->where('public', 0)
        ->Where('user_id', $user_id);
        break;
      case 'mine':
        $query->where('user_id', $user_id);
        break;
      default:
        $query->where(function($query) use ($user_id) {
            $query->where('user_id', $user_id)
            ->orWhere('public', 1);
        });
        break;
    }

    return $query;
  }

  /**
   * Checks whether the memory belongs to the current user
   * @return boolean True if memory is users
   */
  public function isMine() {
    if (!Auth::check()) {
      return false;
    }

    $user = Auth::user();
    return $user->id === $this->user->id;
  }

  /**
   * The owner/creator of this memory
   * @return App\User
   */
  public function user() {
    return $this->belongsTo('App\User');
  }

}
