<?php

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Models\Directory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Pwd extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('pwd');
        $this->setDescription('Print name of current directory.');
        $this->setHelp('There are no additional options for this command');
    }

    /**
     * Run the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = Directory::current();

        $output->write($dir->fullPath());

        return 0;
    }
}
