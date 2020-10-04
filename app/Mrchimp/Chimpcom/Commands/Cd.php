<?php

namespace Mrchimp\Chimpcom\Commands;

use Mrchimp\Chimpcom\Exceptions\InvalidPathException;
use Mrchimp\Chimpcom\Models\Directory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Change directory
 */
class Cd extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('cd');
        $this->setDescription('Change the shell working directory.');
        $this->setHelp('Change the current directory to DIR. The default DIR is the value of the HOME shell variable.');

        $this->addArgument(
            'dir',
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'The directory to change to.'
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
        $dir = $input->getArgument('dir');
        $path = implode(' ', $dir);

        if (!$dir) {
            $default = Directory::default();

            if ($default) {
                $default->setCurrent();
            }

            $output->write('You are home.');

            return 0;
        }

        switch ($path) {
            case 'penguin':
                $output->write('You are inside a penguin. It is dark.');
                return 0;
            case 'c:':
            case 'C:':
                $output->write('What d\'you think this is, Windows?');
                return 0;
            case '.':
                $output->write('You remain here.');
                return 0;
            default:
                try {
                    $target = Directory::fromPath($path);
                } catch (InvalidPathException $e) {
                    $output->error($e->getMessage());

                    return 1;
                }

                if (!$target) {
                    $output->error('No such file or directory');
                    return 2;
                }

                $target->setCurrent();
                $output->write('You go.');

                return 0;
        }
    }
}
