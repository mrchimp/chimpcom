<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Chimpcom;
use Mrchimp\Chimpcom\Models\Memory;
use Mrchimp\Chimpcom\Format;
use DB;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
// use Mrchimp\Chimpcom\Models\Tag; @todo - add tags

class Show extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('show');
        $this->setDescription('Find a memory by its name.');
        $this->addUsage('chimpcom');
        $this->addRelated('save');
        $this->addRelated('find');
        $this->addRelated('forget');
        $this->addRelated('setpublic');

        $this->addArgument(
            'name',
            InputArgument::OPTIONAL,
            'Name of memories to search for.'
        );

        $this->addOption(
            'public',
            'p',
            null,
            'Shows only public memories.'
        );

        $this->addOption(
            'private',
            'P',
            null,
            'Shows only private memories.'
        );

        $this->addOption(
            'mine',
            'm',
            null,
            'Show only your own memories.'
        );

        $this->addOption(
            'words',
            'w',
            null,
            'Show only the words, not the definitions.'
        );

        $this->addOption(
            'count',
            'c',
            InputOption::VALUE_REQUIRED,
            'Number of results to display.'
        );
    }

    /**
     * Run the command
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = Auth::user();
        $name         = $input->getArgument('name');
        $show_public  = $input->getOption('public');
        $show_private = $input->getOption('private');
        $show_mine    = $input->getOption('mine');
        $show_words   = $input->getOption('words');
        $count        = $input->getOption('count');

        if ($show_words) {
            $memories = DB::select('SELECT DISTINCT name FROM memories');
            $words = [];

            foreach ($memories as $word) {
                $words[] = '<span data-type="autofill" data-autofill="show '.e($word->name).'">'.e($word->name).'</span>';
            }

            $output->write(Format::listToTable($words, 6, false));
            return;
        }

        if ($show_public) {
            $item_type = 'public';
        } else if ($show_private) {
            $item_type = 'private';
        } else if ($show_mine) {
            $item_type = 'mine';
        } else {
            $item_type = 'both';
        }

        $memories = Memory::visibility($item_type)
            ->orderBy('name')
            ->orderBy('id')
            ->with('user');

        if (is_numeric($name)) {
            $memory_id = $name;
            $memories = $memories->where('id', $memory_id);
        } else {
            $memories = $memories->where('name', $name);
        }

        $memories = $memories->get();

        if ($memories->count() === 0) {
            $output->error('I have no recollection of that.');
            return;
        }

        $output->write(Format::memories($memories));
    }
}
