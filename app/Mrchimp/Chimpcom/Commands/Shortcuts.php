<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Facades\Format;
use Mrchimp\Chimpcom\Models\Shortcut;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Shortcuts extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('shortcuts');
        $this->setDescription('List all available shortcuts.');
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $shortcuts = Shortcut::query()
            ->whereNull('user_id')
            ->orWhere('user_id', Auth::id())
            ->get();

        if (count($shortcuts) === 0) {
            $output->error('There are currently no shortcuts.');

            return 1;
        }

        foreach ($shortcuts as $shortcut) {
            $output->write(Format::escape($shortcut->name) . ' - ' . Format::escape($shortcut->url) . Format::nl());
        }

        return 0;
    }
}
