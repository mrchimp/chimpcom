<?php

/**
 * Handle password input after 'register'
 */

namespace Mrchimp\Chimpcom\Actions;

use App\Mrchimp\Chimpcom\Actions\Action;
use Chimpcom;
use Session;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Handle password input after 'register'
 * @action register2
 * @action normal
 */
class Register extends Action
{
    protected function configure()
    {
        $this->setName('register');
        $this->setDescription('Register step 2.');
        $this->addArgument(
            'password',
            InputArgument::REQUIRED,
            'Password for new account.'
        );
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Session::get('register_username')) {
            $output->error('This should not happen.');
            Chimpcom::setAction('normal');
            return 1;
        }

        $password = $input->getArgument('password');

        if (!$password) {
            $output->error('No password given. Giving up.');
            Chimpcom::setAction('normal');
            Session::forget('register_username');
            return 2;
        }

        Session::put('register_password', $password);
        $output->alert('Enter the same password again:');
        Chimpcom::setAction('register2');
        $output->usePasswordInput(true);

        return 0;
    }
}
