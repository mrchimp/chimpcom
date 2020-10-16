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

        if ($dir->lister) {
            $lister = new $dir->lister;
            $items = $lister->list();
        } else {
            $dir->load('children');

            if ($dir->children->isEmpty() && $dir->files->isEmpty()) {
                $output->write(Format::grey('Nothing here'));
                return 2;
            }

            $items = $this->getItems($dir);
        }

        $output->write(Format::listToTable($items, 4));

        return 0;
    }

    protected function getItems($dir)
    {
        $bits = [];

        foreach ($dir->children as $child) {
            array_push(
                $bits,
                ...$child->lsArray()
            );
        }

        foreach ($dir->files->sortBy('name') as $file) {
            array_push(
                $bits,
                ...$file->lsArray()
            );
        }

        return $bits;
    }
}
