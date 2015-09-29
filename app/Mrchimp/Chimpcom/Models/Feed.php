<?php
/**
 * Database model for RSS Feeds
 */

namespace Mrchimp\Chimpcom\Models;

use SimplePie;
use Illuminate\Database\Eloquent\Model;

/**
 * Database model for RSS Feeds
 */
class Feed extends Model
{

    protected $fillable = ['name', 'url'];

    public function user() {
        return $this->belongsTo('app\User');
    }

    public function getFeed() {
        if (!$this->url) {
            return false;
        }

        $feed = new SimplePie();
        $feed->set_feed_url($this->url);
        $feed->set_cache_location(storage_path('simplepie_cache'));
        $feed->init();

        return $feed;
    }

}