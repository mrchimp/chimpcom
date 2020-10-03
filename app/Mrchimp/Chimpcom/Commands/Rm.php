<?php

namespace Mrchimp\Chimpcom\Commands;

use function PHPSTORM_META\map;
use Symfony\Component\Console\Input\Input;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Rm extends Command
{

    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('rm');
        $this->setDescription('Remove files or directories.');
        $this->addOption(
            'force',
            'f',
            null,
            'Ignore nonexistent files and arguments, never prompt.'
        );
        $this->addOption(
            'recursive',
            'r',
            null,
            'Remove directories and their contents recursively.'
        );
        $this->addArgument(
            'file',
            InputArgument::IS_ARRAY | InputArgument::REQUIRED,
            'File[s] to remove',
            []
        );
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument_str = implode(' ', $input->getArgument('file'));

        if ($argument_str === 'penguin') {
            $output->write('You remove the penguin but another appears in its place.');
            return 0;
        }

        $output->error('rm: cannot remove \'' . e($argument_str) . '\': permission denied');

        return 0;
    }
}
