<?php
/**
 * Database model for RSS Feeds
 */

namespace Mrchimp\Chimpcom\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Database model for RSS Feeds
 */
class Feed extends Model
{

    public function user() {
        return $this->belongsTo('app\User');
    }

}