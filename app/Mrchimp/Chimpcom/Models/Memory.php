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
   * @param  [type] $query [description]
   * @param  string $type  [description]
   * @return [type]        [description]
   */
  public function scopeVisibility($query, $type = 'both') {
    $user_id = Auth::user()->id;

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
        $query->where('user_id', $user_id)
              ->orWhere('public', 1);
        break;
    }

    return $query;
  }

  public function isMine() {
    if (!Auth::check()) {
      return false;
    }

    $user = Auth::user();
    return $user->id === $this->user->id;
  }

  public function user() {
    return $this->belongsTo('App\User');
  }

}