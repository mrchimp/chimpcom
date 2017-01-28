<?php
/**
 * Print Chimpcom version number
 */

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
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Chimpcom ' . Chimpcom::getVersion() . ', Laravel v' . app()::VERSION);
    }

}
