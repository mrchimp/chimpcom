<?php
/**
 * Create a new witty oneliner
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Models\Oneliner as OnelinerModel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create a new witty oneliner
 */
class Oneliner extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('oneliner');
        $this->setDescription('Add a witty oneliner.');
        $this->addArgument(
            'command',
            InputArgument::REQUIRED,
            'The input to respond to. Must be a single word.'
        );
        $this->addArgument(
            'response',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'The output to respond with.'
        );
    }

    /**
     * Run the command
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error('You must log in to use this command.');
            return;
        }

        $user = Auth::user();

        if (!$user->is_admin) {
            $output->error('No.');
            return;
        }

        $command = $input->getArgument('command');
        $response = implode(' ', $input->getArgument('response'));

        $oneliner = new OnelinerModel();
        $oneliner->command = $command;
        $oneliner->response = $response;
        $oneliner->save();

        $output->alert('Ok.');
    }

}
