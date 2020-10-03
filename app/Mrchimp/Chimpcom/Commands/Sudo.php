<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Sudo extends Command
{

    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('sudo');
        $this->setDescription('Execute a command as another user');
        $this->addArgument(
            'command',
            InputArgument::IS_ARRAY,
            'Command to execute'
        );
    }

    /**
     * Run the command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = Auth::check() ? Auth::user()->name : 'Guest';

        if (!Auth::check() || !Auth::user()->is_admin) {
            $output->error(e($name) . ' is not in the sudoers file. ');
            $output->error('This incident will be reported to the relevant authorities.');
        } else {
            $output->write('You have permission but sudo is not available at this time.');
        }

        return 0;
    }
}
