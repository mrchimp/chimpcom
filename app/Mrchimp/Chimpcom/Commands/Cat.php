<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Exceptions\InvalidPathException;
use Mrchimp\Chimpcom\Models\Directory;
use Mrchimp\Chimpcom\Models\File;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Cat extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('cat');
        $this->setDescription('Concatenate files and print on the standard output.');
        $this->addArgument(
            'filename',
            InputArgument::REQUIRED,
            'Name of file to create.'
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
            $output->error('You must log in to use this command.');

            return 1;
        }

        $dir = Directory::current();

        if (!$dir) {
            $output->error('File system not available.');
            return 2;
        }

        $file = $dir->files->firstWhere('name', $input->getArgument('filename'));

        if (!$file) {
            $output->error('File does not exist.');
            return 3;
        }

        if (!$file->belongsToUser(Auth::user())) {
            $output->error('You do not own that file.');
            return 4;
        }

        $output->write(e($file->content));

        return 0;
    }
}
