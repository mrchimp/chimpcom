<?php

namespace App\Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Commands\Command;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Directory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Ls extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('ls');
        $this->setDescription('List directory contents');
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = Directory::current();

        if (!$dir) {
            $output->error('File system unavailable.');

            return 1;
        }

        $dir->load('children');

        if ($dir->children->isEmpty()) {
            $output->write(Format::grey('Nothing here'));
            return 2;
        }

        $bits = [];

        foreach ($dir->children as $child) {
            array_push(
                $bits,
                $child->ownerName(),
                $child->updated_at->format('M j H:i'),
                $child->name
            );
        }

        $output->write(Format::listToTable($bits, 3));

        return 0;
    }
}
