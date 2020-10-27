<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Models\Directory;
use Mrchimp\Chimpcom\Parsedown;
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
        $this->setDescription('Concatenate files and print on the standard output.<br><br>I\'m Catman.');
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

        $file = $dir->files->firstWhere('name', $input->getArgument('filename'));

        if (!$file) {
            $output->error('File does not exist.');
            return 3;
        }

        if (!$file->belongsToUser(Auth::user())) {
            $output->error('You do not own that file.');
            return 4;
        }

        $parsedown = new Parsedown();
        $parsedown->setSafeMode(true);
        $parsedown->setMarkupEscaped(true);
        $html = $parsedown->text($file->content);
        $output->write('<div class="markdown">');
        $output->write($html);
        $output->write('</div>');

        return 0;
    }
}
