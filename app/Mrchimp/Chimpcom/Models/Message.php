<?php

/**
 * Database model for Chimpcom Messages
 */

namespace Mrchimp\Chimpcom\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Database model for Chimpcom Messages
 */
class Message extends Model
{

  public function tags() {
    return $this->morphToMany('Mrchimp\Chimpcom\Models\Tag', 'taggable');
  }

}