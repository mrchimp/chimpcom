<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Chimpcom;
use Mrchimp\Chimpcom\Format;
use Session;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Validator;

/**
 * Takes a username and asks for a password
 * @action register
 */
class Register extends Command
{

    protected function configure()
    {
        $this->setName('register');
        $this->setDescription('Create a new user account');
        $this->addArgument(
            'username',
            InputArgument::REQUIRED,
            'Username for new account.'
        );
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (Auth::check()) {
            $output->error('You\'re already logged in.');

            return 1;
        }

        $username = $input->getArgument('username');

        $validator = Validator::make([
            'name' => $username,
        ], [
            'name' => 'required|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            $output->writeErrors($validator, 'There was a problem with that username:');

            return 2;
        }

        Session::put('register_username', $username);
        Chimpcom::setAction('register');
        $output->usePasswordInput(true);
        $output->alert('Enter a password:');

        return 0;
    }
}
