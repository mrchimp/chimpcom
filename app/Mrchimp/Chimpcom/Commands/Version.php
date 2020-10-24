<?php

namespace Mrchimp\Chimpcom\Commands;

use Chimpcom;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Print Chimpcom version number
 */
class Version extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('version');
        $this->setDescription('Print Chimpcom version number.');
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Chimpcom ' . Chimpcom::getVersion());
dump('whatup');
        return 0;
    }
}
