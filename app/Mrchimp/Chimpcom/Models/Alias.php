<?php

namespace Mrchimp\Chimpcom\Models;

use Database\Factories\AliasFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Database model for command aliases.
 */
class Alias extends Model
{
    use HasFactory;

    public static function lookup(string $cmd_name): string
    {
        $alias = self::where('name', $cmd_name)->take(1)->first();

        return trim($alias ? $alias->alias : $cmd_name);
    }

    protected static function newFactory()
    {
        return AliasFactory::new();
    }
}
