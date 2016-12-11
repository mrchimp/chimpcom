<?php
/**
 * Get the date
 */

namespace Mrchimp\Chimpcom\Commands;

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
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
        {
        if ($input->getOption('date')) {
            $output->write(date('l jS \of F Y'));
        } else if ($input->getOption('time')) {
            $output->write(date('h:i:s A'));
        } else if ($input->getOption('iso')) {
            $output->write(date('c'));
        } else {
            $output->write(date('l jS \of F Y h:i:s A e'));
        }
    }

}
