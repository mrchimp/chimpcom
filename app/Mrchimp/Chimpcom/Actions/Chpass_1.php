<?php

namespace Mrchimp\Chimpcom\Actions;

use App\Mrchimp\Chimpcom\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Chpass_1 extends Action
{
    protected function configure()
    {
        $this->setName('chpass_1');
        $this->setDescription('Password change stage 1.');
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
            return 0;
        }

        Session::put('chpass_1', $password);

        Chimpcom::setAction('chpass_2');

        $output->usePasswordInput(true);
        $output->alert('Enter password again. Type cancel to cancel.');

        return 0;
    }
}
