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
        $dir->load('children');

        if (!$dir) {
            $output->error('File system unavailable.');

            return 1;
        }

        if ($dir->children->isEmpty()) {
            $output->write(Format::grey('Nothing here'));
            return 2;
        }

        $dir->children->each(function ($child) use ($output) {
            $output->write(
                $child->ownerName() . ' ' .
                $child->updated_at->format('M j H:i') . ' ' .
                $child->name .
                '<br>'
            );
        });

        return 0;
    }
}
