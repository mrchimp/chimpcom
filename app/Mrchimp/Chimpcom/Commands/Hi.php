<?php
/**
 * Your basic common or garden Chimpcom function
 */

namespace Mrchimp\Chimpcom\Commands;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mrchimp\Chimpcom\Chimpcom;

/**
 * Your basic common or garden Chimpcom function
 */
class Hi extends SymfonyCommand
{

    protected function configure()
    {
        $this->name = 'Hi';
    }

    /**
     * Run the command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write(Chimpcom::welcomeMessage());
    }

}
