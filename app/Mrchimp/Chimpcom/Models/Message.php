<?php

/**
 * Database model for Chimpcom Messages
 */

namespace Mrchimp\Chimpcom\Models;

use Database\Factories\MessageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Database model for Chimpcom Messages
 */
class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'recipient_id',
        'author_id',
        'has_been_read',
    ];

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

    protected static function newFactory()
    {
        return MessageFactory::new();
    }

    public function scopeWhereUnread(Builder $query)
    {
        $query->where('has_been_read', false);
    }
}
