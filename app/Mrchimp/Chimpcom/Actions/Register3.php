<?php

namespace Mrchimp\Chimpcom\Actions;

use Auth;
use Session;
use Validator;
use Chimpcom;
use App\User;
use Mrchimp\Chimpcom\Commands\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Handle email input and create an account
 */
class Register3 extends Command
{
    protected function configure()
    {
        $this->setName('register3');
        $this->setDescription('Register step 4.');
        $this->addArgument(
            'email',
            InputArgument::REQUIRED,
            'Email for new account.'
        );
    }

    /**
     * Run the command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username  = Session::get('register_username');
        $password  = Session::get('register_password');
        $password2 = Session::get('register_password2');
        $email     = $input->getArgument('email');

        $data = [
            'name'                  => $username,
            'email'                 => $email,
            'password'              => $password,
            'password_confirmation' => $password2
        ];

        $validator = Validator::make($data, [
            'name'     => 'required|max:255|unique:users',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6'
        ]);

        if ($validator->fails()) {
            $output->writeErrors($validator, 'Something went wrong. Please try again.');
            Chimpcom::setAction('normal');
            return false;
        }

        Auth::login($this->create($data));

        Session::forget('register_username');
        Session::forget('register_password');
        Session::forget('register_password2');

        $output->write('Hello, ' . e($data['name']) . '! Welcome to Chimpcom.');
        Chimpcom::setAction('normal');
        $output->usePasswordInput(false);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

}
