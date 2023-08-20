<?php

namespace Mrchimp\Chimpcom\Actions;

use Mrchimp\Chimpcom\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Traits\LogCommandNameOnly;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Chpass_1 extends Action
{
    use LogCommandNameOnly;

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
            Chimpcom::delAction($input->getActionId());
            $output->error('Abandoning.');
            $output->usePasswordInput(false);
            return 0;
        }

        $output->setAction('chpass_2', [
            'chpass_1' => $password
        ]);
        $output->usePasswordInput();
        $output->useQuestionInput();
        $output->alert('Enter password again. Type cancel to cancel.');

        return 0;
    }
}
