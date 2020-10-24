<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Models\Directory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Rmdir extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('rmdir');
        $this->setDescription('Remove direcories.');
        $this->addOption(
            'force',
            'f',
            null,
            'Ignore nonexistent files and arguments, never prompt.'
        );
        $this->addArgument(
            'dirname',
            InputArgument::REQUIRED,
            'File to remove'
        );
    }


    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error('You must log in to use this command.');

            return 1;
        }

        $dirname = $input->getArgument('dirname');

        $current = Directory::current();

        $directory = $current->children->firstWhere('name', $dirname);

        if (!$directory) {
            if ($dirname === 'penguin') {
                $output->write('You remove the penguin but another appears in its place.');
                return 0;
            }

            $output->error('Directory does not exist.');
            return 3;
        }

        if (!$directory->belongsToUser(Auth::user()) && !Auth::user()->is_admin) {
            $output->error('You do not have permission to remove that directory.');
            return 4;
        }

        if ($directory->children->isNotEmpty() || $directory->files->isNotEmpty()) {
            $output->error('That directory is not empty.');
            return 5;
        }

        $directory->delete();

        $output->write('Ok.');

        return 0;
    }
}
