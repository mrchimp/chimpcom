<?php
/**
 * Database model for Shortcuts
 */

namespace Mrchimp\Chimpcom\Models;

use Database\Factories\ShortcutFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Database model for Shortcuts.
 *
 * Short cuts are urls with parts that will be replaced
 * by the command parameters
 */
class Shortcut extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return ShortcutFactory::new();
    }
}
