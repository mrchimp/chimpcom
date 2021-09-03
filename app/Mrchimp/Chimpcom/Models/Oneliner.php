<?php

/**
 * Chimpcom oneliner model
 */

namespace Mrchimp\Chimpcom\Models;

use Database\Factories\OnelinerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Database model for Chimpcom Oneliners. These are simple text responses
 * to commands. They map strings to other strings rather than classes/functions.
 */
class Oneliner extends Model
{
    use HasFactory;

    protected $fillable = [
        'command',
        'response',
    ];

    protected static function newFactory()
    {
        return OnelinerFactory::new();
    }
}
