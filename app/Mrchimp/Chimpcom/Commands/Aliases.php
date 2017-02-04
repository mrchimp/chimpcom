<?php
/**
 * List all command aliases
 */

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Models\Alias as ChimpcomAlias;
use Mrchimp\Chimpcom\Format;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * List all command aliases
 */
class Aliases extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('aliases');
        $this->setDescription('View available aliases.');
    }

    /**
     * Run the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $aliases = ChimpcomAlias::all();
        $out = [];

        foreach ($aliases as $alias) {
            $out[] = $alias->name;
            $out[] = ' ➞ ';
            $out[] = $alias->alias;
        }

        $output->write(Format::listToTable($out, 3, true));
    }
}
