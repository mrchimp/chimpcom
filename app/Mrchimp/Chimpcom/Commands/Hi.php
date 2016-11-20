<?php
/**
 * Your basic common or garden Chimpcom function
 */

namespace Mrchimp\Chimpcom\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mrchimp\Chimpcom\Chimpcom;

/**
 * Your basic common or garden Chimpcom command
 */
class Hi extends Command
{

    protected function configure()
    {
        $this->setName('hi');
        $this->setDescription('Displays a welcome message.');
        $this->setHelp('There are no additional options for this command');
        $this->addUsage('');
        $this->addUsage('--there');

    }

    /**
     * Run the command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write(Chimpcom::welcomeMessage());
    }

}
