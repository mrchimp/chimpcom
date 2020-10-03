<?php

namespace Mrchimp\Chimpcom\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Flip a coin
 */
class Coin extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('coin');
        $this->setDescription('Simulate a coin flip.');
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
        $output->write((mt_rand(0, 1) ? 'Heads' : 'Tails'));

        return 0;
    }
}
