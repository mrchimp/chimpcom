<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Str;
use Mrchimp\Chimpcom\Exceptions\InvalidPathException;
use Mrchimp\Chimpcom\Filesystem\Path;
use Mrchimp\Chimpcom\Filesystem\RootDirectory;
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
        $path_string = implode(' ', $dir);

        if (!$dir) {
            $home = Directory::home();

            if ($home) {
                $home->setCurrent();
            }

            $output->write('You are home.');

            return 0;
        }

        switch ($path_string) {
            case 'c:':
            case 'C:':
                $output->write(e('What d\'you think this is, Windows?'));
                return 0;
            case '.':
                $output->write('You remain here.');
                return 0;
            default:
                try {
                    $path = Path::make($path_string);
                } catch (InvalidPathException $e) {
                    $output->error($e->getMessage());

                    return 1;
                }

                if ($path->isRoot()) {
                    (new RootDirectory)->setCurrent();
                    $output->write('You go.');
                    return 0;
                }

                if (!$path->exists()) {
                    if ($path_string === 'penguin') {
                        $output->write('You are inside a penguin. It is dark.');
                        return 0;
                    }

                    $output->error('No such file or directory');
                    return 2;
                }

                if (!$path->isDirectory()) {
                    $output->error('Target is not a directory.');
                    return 3;
                }

                $path->target()->setCurrent();
                $output->write('You go.');

                return 0;
        }
    }

    /**
     * Return tab completion options for the current command input
     *
     * @param  Input  $input
     * @return string
     */
    public function tab(InputInterface $input)
    {
        $path = Path::make(implode(' ', $input->getArgument('dir')));
        $filename = $path->last();
        $current = Directory::current();

        $options = $current
            ->children
            ->filter(function ($child) use ($filename) {
                return Str::startsWith($child->name, $filename);
            });

        if ($options->isEmpty()) {
            return [];
        }

        return $options
            ->pluck('name')
            ->transform(function ($item) {
                return 'cd ' . $item;
            })
            ->values();
    }
}
