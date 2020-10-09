<?php

namespace Mrchimp\Chimpcom\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Decide who should make the tea
 */
class Tea extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('tea');
        $this->setDescription('Decide who should make the tea.');
        $this->addArgument(
            'names',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'The names of the potential beverage makers'
        );
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $names = $input->getArgument('names');
        $rand = array_rand($names);
        $name = ucwords($names[$rand]);
        $output->write($name . ' ' . ($name === 'You' ? 'are' : 'is') .  ' on hot beverage duty.');

        return 0;
    }
}
