<?php

namespace Mrchimp\Chimpcom\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Tabtest extends Command
{
    protected function configure()
    {
        $this->setName('tabtest');
        $this->setDescription('Tests tab completion');
        $this->addArgument('command', InputArgument::REQUIRED, 'Number of results to return');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('This is for testing the tabcompletion feature. <br>');
        $output->write('To try it out yourself, type <code>tabtest c</code> and press tab a few times.');

        return 0;
    }

    /**
     * Return tab completion options for the current command input
     *
     * @param  Input  $input
     * @return string
     */
    public function tab(InputInterface $input)
    {
        $command = $input->getArgument('command');

        switch ($command) {
            case 'a':
                return ['tabtest animal'];
            case 'b':
                return ['tabtest baboon', 'tabtest bushbaby'];
            case 'c':
                return ['tabtest cat', 'tabtest crocodile', 'tabtest cute dog'];
            default:
                return [];
        }
    }
}
