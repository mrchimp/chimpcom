<?php

/**
 * Handle second password input and create account
 */

namespace Mrchimp\Chimpcom\Actions;

use App\Mrchimp\Chimpcom\Actions\Action;
use Illuminate\Support\Facades\Session;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Handle second password input and prompt for an email address
 *
 * @action register3
 */
class Register2 extends Action
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('register2');
        $this->setDescription('Register step 3.');
        $this->addArgument(
            'password_confirmation',
            InputArgument::REQUIRED,
            'The same password again.'
        );
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = Session::get('register_username');
        $password = Session::get('register_password');
        $password2 = $input->getArgument('password_confirmation');

        if (!$username || !$password) {
            $output->error('This should not happen.');
            Chimpcom::setAction('normal');
            $output->usePasswordInput(false);
            Session::forget('register_username');
            Session::forget('register_password');
            return 1;
        }

        Session::put('register_password2', $password2);
        $output->alert('Your email address:');
        Chimpcom::setAction('register3');
        $output->usePasswordInput(false);

        return 0;
    }
}
