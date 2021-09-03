<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
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
        $this->addOption(
            'force',
            'f',
            null,
            'Force creating the oneliner.'
        );
    }

    /**
     * Run the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));

            return 1;
        }

        $user = Auth::user();

        if (!$user->is_admin) {
            $output->error(__('chimpcom.not_admin'));

            return 2;
        }

        $command = $input->getArgument('command');
        $response = implode(' ', $input->getArgument('response'));

        $oneliners = OnelinerModel::where('command', $command)->get();

        if ($oneliners->isNotEmpty() && !$input->getOption('force')) {
            $output->error('These oneliners already exist. Use --force to create another.');

            $oneliners->each(function ($oneliner) use ($output) {
                $output->write($oneliner->command . ' - ' . $oneliner->response . '<br>');
            });

            return 3;
        }


        OnelinerModel::create([
            'command' => $command,
            'response' => $response,
        ]);

        $output->alert('Ok.');

        return 0;
    }
}
