<?php

namespace Mrchimp\Chimpcom\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'tag',
    ];

    /**
     * related memories
     */
    public function memories()
    {
        return $this->morphedByMany('Mrchimp\Chimpcom\Models\Memory', 'taggable');
    }

    /**
     * Get an array of tag names from a string
     */
    public static function fromString(string $input): array
    {
        $output = [];

        preg_match_all('/[$ ]\#([^ ]+)/m', $input, $output);

        return $output[1];
    }
}
