<?php

/**
 * Candyman!
 */

namespace Mrchimp\Chimpcom\Actions;

use Chimpcom;
use Mrchimp\Chimpcom\Actions\Action;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Candyman!
 * @action candyman
 */
class Candyman extends Action
{
    protected function configure()
    {
        $this->setName('candyman');
        $this->setDescription('Maybe say something scary.');
        $this->addArgument(
            'response',
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'User\'s response.'
        );
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (implode(' ', $input->getArgument('response')) === 'candyman') {
            $output->error('AAAAAAAAGH!');
        } else {
            $output->write('Pussy.');
        }

        Chimpcom::delAction($input->getActionId());

        return 0;
    }
}
