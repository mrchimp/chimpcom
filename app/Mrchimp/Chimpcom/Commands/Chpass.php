<?php

namespace Mrchimp\Chimpcom\Commands;

use Chimpcom;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Change your password
 */
class Chpass extends Command
{
    protected $title = 'Chpass';
    protected $description = 'Change your password.';

    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('chpass');
        $this->setDescription('Change user\'s password');
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error('You must log in to use this command.');

            return 1;
        }

        Chimpcom::setAction('chpass_1');

        $output->usePasswordInput(true);
        $output->alert('Enter your new password. Type cancel to cancel.');

        return 0;
    }
}
