<?php
/**
 * List all command aliases
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Models\Alias as ChimpcomAlias;
use Mrchimp\Chimpcom\Format;

/**
 * List all command aliases
 */
class Aliases extends AbstractCommand
{
    protected $title = 'Alias';
    protected $description = 'View available aliases.';
    protected $usage = 'aliases';


    /**
     * Run the example
     */
    public function process() {
        $aliases = ChimpcomAlias::all();
        $output = [];

        foreach ($aliases as $alias) {
            $output[] = $alias->name;
            $output[] = ' ➞ ';
            $output[] = $alias->alias;
        }

        $this->response->say(Format::listToTable($output, 3));
    }
}
