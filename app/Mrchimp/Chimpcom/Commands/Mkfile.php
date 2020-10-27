<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Models\Directory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Mkfile extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('mkfile');
        $this->setDescription('Create a file.');
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
            $output->error(__('chimpcom.must_log_in'));

            return 1;
        }

        $dir = Directory::current();

        if (!$dir->belongsToUser(Auth::user())) {
            $output->error('You do not have permission to create a file here.');
            return 3;
        }

        $filename = $input->getArgument('filename');

        if ($dir->children->firstWhere('name', $filename)) {
            $output->error('A directory with that name already exists.');
            $output->setStatusCode(422);
            return 4;
        }

        $dir->files()->create([
            'name' => $filename,
            'owner_id' => Auth::id(),
            'content' => '',
        ]);

        $output->write('Ok.');

        return 0;
    }
}
