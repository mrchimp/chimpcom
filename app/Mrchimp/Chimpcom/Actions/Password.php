<?php

/**
 * Handle password input after 'login username'
 */

namespace Mrchimp\Chimpcom\Actions;

use App\Mrchimp\Chimpcom\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Mrchimp\Chimpcom\Models\Message;
use Mrchimp\Chimpcom\Traits\LogCommandNameOnly;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Handle password input after 'login username'
 */
class Password extends Action
{
    use LogCommandNameOnly;

    /**
     * Configure the command
     *
     * @return void
     */
    public function configure()
    {
        $this->setName('password');
        $this->setDescription('Handles password input.');

        $this->addArgument(
            'password',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
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
        $password = implode(' ', $input->getArgument('password'));

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

        if (!Auth::attempt([
            'name' => $username,
            'password' => $password
        ], false, true)) {
            $output->error('Hmmmm... No.');
            $this->log->info('User ' . e($username) . ' failed to log in.');
            return 3;
        }

        $output->getUserDetails();
        $output->alert('Welcome back.');

        $unread_count = Auth::user()->messages()->whereUnread()->count();

        if ($unread_count > 0) {
            $output->write(
                '<br>You have ' . $unread_count . ' unread message' . ($unread_count > 1 ? 's' : '') . '. ' .
                'Use the command <code>mail</code> to read ' . ($unread_count > 1 ? 'them' : 'it') .  '.'
            );
        }

        $this->log->info('User ' . e($username) . ' logged in.');

        return 0;
    }
}
