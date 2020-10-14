<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Mrchimp\Chimpcom\Models\Directory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Mkdir extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('mkdir');
        $this->setDescription('Make directory.');
        $this->addArgument(
            'name',
            InputArgument::REQUIRED,
            'Name of directory to create.',
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

        $current = Directory::current();

        if (!$current) {
            $output->error('Filesystem is not available.');
            return 1;
        }

        if (!$current->belongsToUser(Auth::user()) && !Auth::user()->is_admin) {
            $output->error('You do not have permission to create a directory here.');

            return 2;
        }

        $filename = $input->getArgument('name');

        if ($current->files->firstWhere('name', $filename)) {
            $output->error('A file with that name already exists.');
            $output->setResponseCode(422);

            return 3;
        }

        $current->appendNode(Directory::create([
            'name' => $this->sanitiseFilename($filename),
            'owner_id' => Auth::id(),
        ]));

        $output->alert('Ok.');

        return 0;
    }

    protected function sanitiseFilename($name)
    {
        return Str::slug($name);
    }
}
