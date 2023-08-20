<?php

namespace Mrchimp\Chimpcom\Actions;

use Mrchimp\Chimpcom\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Traits\LogCommandNameOnly;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Chpass_2 extends Action
{
    use LogCommandNameOnly;

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
            Chimpcom::delAction($input->getActionId());
            return 0;
        }

        $validator = Validator::make([
            'password' => $input->getActionData('chpass_1'),
            'password_confirmation' => $password
        ], [
            'password' => 'required|confirmed|min:6'
        ]);

        if ($validator->fails()) {
            $output->error('Passwords did not match. Aborting.');
            $output->usePasswordInput(false);
            Chimpcom::delAction($input->getActionId());
            return 1;
        }

        $user = Auth::user();
        $user->password = Hash::make($password);
        $user->save();

        $output->alert('Ok then. All done.');
        Chimpcom::delAction($input->getActionId());
        $output->usePasswordInput(false);

        return 0;
    }
}
