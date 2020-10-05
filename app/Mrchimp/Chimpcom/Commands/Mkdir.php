<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Mrchimp\Chimpcom\Models\Directory;
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
            null,
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

        if (!Auth::user()->is_admin) {
            $output->error('No.');

            return 2;
        }

        $current = Directory::current();

        if (!$current) {
            $output->error('Filesystem is not available.');
            return 1;
        }

        if ($current->owner_id !== Auth::id()) {
            $output->error('You do not have permission to create a directory here.');
            return 2;
        }

        $filename = $input->getArgument('name');

        $current->appendNode(Directory::create([
            'name' => $this->sanitiseFilename($filename),
            'owner_id' => Auth::id(),
        ]));

        return 0;
    }

    protected function sanitiseFilename($name)
    {
        return Str::slug($name);
    }
}
