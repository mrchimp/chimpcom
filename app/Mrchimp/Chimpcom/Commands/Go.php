<?php

/**
 * Go to a given URL
 */

namespace Mrchimp\Chimpcom\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Go to a given URL
 */
class Go extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('go');
        $this->setDescription('Go to a given url.');
        $this->addArgument('url', InputArgument::REQUIRED, 'URl to go to.');
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
        $url = $input->getArgument('url');

        if (substr($url, 0, 4) !== 'http') {
            $url = 'http://' . $url;
        }

        $output->redirect($url);

        return 0;
    }
}
