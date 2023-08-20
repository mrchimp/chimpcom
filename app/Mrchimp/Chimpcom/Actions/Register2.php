<?php

/**
 * Handle second password input and create account
 */

namespace Mrchimp\Chimpcom\Actions;

use Mrchimp\Chimpcom\Actions\Action;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Traits\LogCommandNameOnly;
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
    use LogCommandNameOnly;

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
        $username = $input->getActionData('username');
        $password = $input->getActionData('password');
        $password2 = $input->getArgument('password_confirmation');

        if (!$username || !$password) {
            $output->error('This should not happen.');
            $output->usePasswordInput(false);
            Chimpcom::delAction($input->getActionId());
            return 1;
        }

        $output->setAction('register3', [
            'username' => $username,
            'password' => $password,
            'password2' => $password2,
        ]);
        $output->alert('Your email address:');
        $output->usePasswordInput(false);
        $output->useQuestionInput();

        return 0;
    }
}
