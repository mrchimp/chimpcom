<?php

/**
 * Chimpcom oneliner model
 */

namespace Mrchimp\Chimpcom\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Database model for Chimpcom Oneliners. These are simple text responses
 * to commands. They map strings to other strings rather than classes/functions.
 */
class Oneliner extends Model
{
    protected $fillable = [
        'command',
        'response',
    ];
}
