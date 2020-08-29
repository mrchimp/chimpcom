<?php

/**
 * Handle password input after 'login username'
 */

namespace Mrchimp\Chimpcom\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Mrchimp\Chimpcom\Commands\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Handle password input after 'login username'
 */
class Password extends Command
{

    public function configure()
    {
        $this->setName('password');
        $this->setDescription('Handles password input.');

        $this->addArgument(
            'password',
            InputArgument::REQUIRED,
            'Your password.'
        );
    }

    /**
     * Run the command
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $output->error('You are already logged in as ' . htmlspecialchars($user->name) . '. How did you do that?');
            return 1;
        }

        $username = Session::get('login_username');
        $password = $input->getArgument('password');

        Session::forget('login_username');
        Session::put('action', 'normal');

        if (!$password) {
            $this->error('No password given. Start again.');

            return 1;
        }

        if (!$username) {
            $this->error('I forgot your name, sorry. Start again.');
            return 2;
        }

        if (Auth::attempt([
            'name' => $username,
            'password' => $password
        ], false, true)) {
            $output->getUserDetails();
            $output->alert('Welcome back.');
        } else {
            $output->error('Hmmmm... No.');
        }

        return 0;
    }
}
