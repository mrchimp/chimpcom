<?php

/**
 * Get current username
 */

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Get current username
 */
class Whoami extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('whoami');
        $this->setDescription('Print the current user name.');
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (Auth::check()) {
            $output->write(Auth::user()->name);
        } else {
            $output->write('Guest');
        }

        return 0;
    }
}
