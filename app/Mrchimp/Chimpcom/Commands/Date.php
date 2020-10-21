<?php

namespace Mrchimp\Chimpcom\Commands;

use Carbon\Carbon;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Get the date
 */
class Date extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('date');
        $this->setDescription('Get the full date time.');

        $this->addOption(
            'date',
            'd',
            null,
            'Show just the date.'
        );

        $this->addOption(
            'time',
            't',
            null,
            'Show just the time.'
        );

        $this->addOption(
            'iso',
            'i',
            null,
            'Show just the time in ISO 8601 format.'
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
        $now = Carbon::now();

        if ($input->getOption('date')) {
            $output->write($now->format('l jS \of F Y'));
        } elseif ($input->getOption('time')) {
            $output->write($now->format('H:i:s'));
        } elseif ($input->getOption('iso')) {
            $output->write($now->format('c'));
        } else {
            $output->write($now->format('l jS \of F Y H:i:s e'));
        }

        return 0;
    }
}
