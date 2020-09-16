<?php

/**
 * Database model for Chimpcom Messages
 */

namespace Mrchimp\Chimpcom\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Database model for Chimpcom Messages
 */
class Message extends Model
{
    public function tags(): MorphToMany
    {
        return $this->morphToMany('Mrchimp\Chimpcom\Models\Tag', 'taggable');
    }

    public function author(): HasOne
    {
        return $this->hasOne('App\User', 'id', 'author_id');
    }

    public function recipient(): HasOne
    {
        return $this->hasOne('App\User', 'id', 'recipient_id');
    }
}
