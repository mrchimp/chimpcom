<?php

namespace App\Mrchimp\Chimpcom\Actions;

use Mrchimp\Chimpcom\Commands\Command;
use Mrchimp\Chimpcom\Facades\Chimpcom;

class Action extends Command
{
    /**
     * Check if an action exists by name
     */
    public static function exists(string $name = null)
    {
        if (is_null($name)) {
            $name = Chimpcom::currentActionName();
        }

        return !!config('chimpcom.actions.' . strtolower($name));
    }
}
