<?php
/**
 * Log in to Chimpcom
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Session;
use App\User;
use Mrchimp\Chimpcom\Format;
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
            null,
            'Username to log in as.'
        );
    }

    /**
     * Run the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $output->alert('You are already logged in as '.htmlspecialchars($user->name).'.');
            return;
        }

        $username = $input->getArgument('username');

        if (!$username) {
            $output->alert('Provide a username.');
            return;
        }

        $user = User::where('name', $username)->get();

        // User doesn't exist
        if (count($user) === 0) {
            $output->error('You fail. The username '.htmlspecialchars($username).' does not exist.
                            Create a new account by using the register command.');
            // $this->response->cFill("register $username"); // @todo
            $output->usePasswordInput(false);
            Session::set('action', 'normal');
            return;
        }

        Session::set('login_username', trim($username));

        $output->alert('Password:');
        $output->usePasswordInput();
        Session::set('action', 'password');
    }
}
