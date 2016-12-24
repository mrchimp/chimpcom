<?php
/**
 * Log out of Chimpcom
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use App\User;
use Session;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Log out of Chimpcom
 */
class Logout extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('logout');
        $this->setDescription('End the current users session.');
    }

    /**
     * Run the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error('You\'re not logged in.');
            return;
        }

        Auth::logout();
        $output->getUserDetails();
        $output->alert('You are now logged out.');
    }
}
