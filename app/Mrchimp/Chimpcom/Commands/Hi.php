<?php

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Chimpcom;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Your basic common or garden Chimpcom command
 */
class Hi extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('hi');
        $this->setDescription('Displays a welcome message.');
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
        $output->write(Chimpcom::welcomeMessage());

        return 0;
    }
}
