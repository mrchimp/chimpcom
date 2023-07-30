<?php

namespace Mrchimp\Chimpcom\Traits;

use Mrchimp\Chimpcom\Models\Tag;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasTags
{
    /**
     * Memories can have multiple tags
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Take an array of tag names and attach them to this memory, creating them
     * if needed.
     */
    public function attachTags($tags): void
    {
        foreach ($tags as $tag_name) {
            $tag = Tag::firstOrCreate([
                'tag' => $tag_name,
            ]);

            $this->tags()->save($tag);
        }
    }
}
