<?php
/**
 * Handle password input after 'login username'
 */

namespace Mrchimp\Chimpcom\Actions;

use Hash;
use Auth;
use Session;
use App\User;
use Mrchimp\Chimpcom\Commands\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

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
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $output->error('You are already logged in as '.htmlspecialchars($user->name).'. How did you do that?');
            return;
        }

        $username = Session::get('login_username');
        $password = $input->getArgument('password');
        Session::forget('login_username');
        Session::put('action', 'normal');

        if (!$password) {
            $this->error('No password given. Start again.');
            return;
        }

        if (!$username) {
            $this->error('I forgot your name, sorry. Start again.');
            return;
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
    }

}
