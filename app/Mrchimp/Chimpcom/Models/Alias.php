<?php

namespace Mrchimp\Chimpcom\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Database model for command aliases.
 */
class Alias extends Model
{
    public static function lookup(string $cmd_name): string
    {
        $alias = self::where('name', $cmd_name)->take(1)->first();

        return ($alias ? $alias->alias : $cmd_name);
    }
}
