<?php

namespace Mrchimp\Chimpcom\Actions;

use App\Mrchimp\Chimpcom\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Chpass_2 extends Action
{
    protected function configure()
    {
        $this->setName('chpass_2');
        $this->setDescription('Password change stage 2.');
        $this->addArgument(
            'password',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'User\'s password.'
        );
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error('You must log in to use this command.');

            return 1;
        }

        $password = implode(' ', $input->getArgument('password'));

        if (!$password || $password === 'cancel') {
            $output->error('Abandoning.');
            $output->usePasswordInput(false);
            Chimpcom::setAction('normal');
            Session::forget('chpass_1');
            return 0;
        }

        $validator = Validator::make([
            'password' => Session::get('chpass_1'),
            'password_confirmation' => $password
        ], [
            'password' => 'required|confirmed|min:6'
        ]);

        if ($validator->fails()) {
            $output->error('Passwords did not match. Aborting.');
            $output->usePasswordInput(false);
            Chimpcom::setAction('normal');
            Session::forget('chpass_1');
            return 1;
        }

        $user = Auth::user();
        $user->password = Hash::make($password);
        $user->save();

        Session::forget('chpass_1');

        $output->alert('Ok then. All done.');
        Chimpcom::setAction('normal');
        $output->usePasswordInput(false);

        return 0;
    }
}
