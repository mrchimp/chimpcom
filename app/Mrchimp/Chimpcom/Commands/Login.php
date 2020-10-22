<?php

namespace Mrchimp\Chimpcom\Commands;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Accepts a username and asks for a password. Action logging in happens in actions/password
 * @action password
 */
class Login extends Command
{

    public function configure()
    {
        $this->setName('login');
        $this->setDescription('Log in to the system.');

        $this->addArgument(
            'username',
            InputArgument::REQUIRED,
            'Username to log in as.'
        );
    }

    /**
     * Run the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $output->alert('You are already logged in as ' . htmlspecialchars($user->name) . '.');
            return 1;
        }

        $username = $input->getArgument('username');

        $user = User::where('name', $username)->get();

        // User doesn't exist
        if (count($user) === 0) {
            $output->error('You fail. The username ' . htmlspecialchars($username) . ' does not exist.
                            Create a new account by using the register command.');

            $output->cFill('register ' . $username);

            $output->usePasswordInput(false);

            Session::put('action', 'normal');

            return 3;
        }

        Session::put('login_username', trim($username));

        $output->alert('Password:');
        $output->usePasswordInput();

        Session::put('action', 'password');

        return 0;
    }
}
