<?php

/**
 * Handle password input after 'register'
 */

namespace Mrchimp\Chimpcom\Actions;

use Mrchimp\Chimpcom\Actions\Action;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Traits\LogCommandNameOnly;
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
    use LogCommandNameOnly;

    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('register');
        $this->setDescription('Register step 2.');
        $this->addArgument(
            'password',
            InputArgument::OPTIONAL,
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
        $username = $input->getActionData('username');

        if (!$username) {
            $output->error('This should not happen.');
            Chimpcom::delAction($input->getActionId());
            return 1;
        }

        $password = $input->getArgument('password');

        if (!$password) {
            $output->error('No password given. Giving up.');
            Chimpcom::delAction($input->getActionId());
            return 2;
        }

        $output->setAction('register2', [
            'password' => $password,
            'username' => $username,
        ]);
        $output->alert('Enter the same password again:');
        $output->useQuestionInput();
        $output->usePasswordInput(true);

        return 0;
    }
}
