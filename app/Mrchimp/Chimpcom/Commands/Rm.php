<?php

namespace Mrchimp\Chimpcom\Commands;

use function PHPSTORM_META\map;

use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Models\Directory;

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
        $this->setDescription('Remove files.');
        $this->addOption(
            'force',
            'f',
            null,
            'Ignore nonexistent files and arguments, never prompt.'
        );
        $this->addArgument(
            'filename',
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
            $output->error(__('chimpcom.must_log_in'));

            return 1;
        }

        $filename = $input->getArgument('filename');

        $dir = Directory::current();

        $file = $dir->files->firstWhere('name', $filename);

        if (!$file) {
            if ($filename === 'penguin') {
                $output->write('You remove the penguin but another appears in its place.');
                return 0;
            }

            $output->error('File does not exist.');
            return 3;
        }

        if (!$file->belongsToUser(Auth::user())) {
            $output->error('You do not own that file.');
            return 4;
        }

        $file->delete();

        $output->write('Ok.');

        return 0;
    }
}
