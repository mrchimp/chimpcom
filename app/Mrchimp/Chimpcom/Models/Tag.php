<?php

namespace Mrchimp\Chimpcom\Models;

use Database\Factories\TagFactory;
use Mrchimp\Chimpcom\Models\Memory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected static $tag_regex = '/@([\w\-_]+)\b/m';

    protected $fillable = [
        'tag',
    ];

    /**
     * related memories
     */
    public function memories()
    {
        return $this->morphedByMany(Memory::class, 'taggable');
    }

    /**
     * Get an array of tag names from a string
     */
    public static function fromString(string $input): array
    {
        $output = [];

        preg_match_all(static::$tag_regex, $input, $output);

        return $output[1];
    }

    /**
     * Take an input string and remove tags from it
     */
    public static function stripTagsFromString(string $input): string
    {
        return preg_replace(static::$tag_regex, '', $input);
    }

    protected static function newFactory()
    {
        return TagFactory::new();
    }
}
