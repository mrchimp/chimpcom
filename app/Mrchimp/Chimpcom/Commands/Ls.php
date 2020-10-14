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

        if ($dir->children->isEmpty() && $dir->files->isEmpty()) {
            $output->write(Format::grey('Nothing here'));
            return 2;
        }

        $bits = [];

        foreach ($dir->children as $child) {
            array_push(
                $bits,
                '📁',
                e($child->ownerName()),
                $child->updated_at->format('M j H:i'),
                e($child->name)
            );
        }

        foreach ($dir->files->sortBy('name') as $file) {
            array_push(
                $bits,
                '📄',
                e($file->ownerName()),
                $file->updated_at->format('M j H:i'),
                e($file->name)
            );
        }

        $output->write(Format::listToTable($bits, 4));

        return 0;
    }
}
