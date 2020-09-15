<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Memory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
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
            'Number of results to display. Default: 20'
        );

        $this->addOption(
            'last',
            'l',
            null,
            'Show latest memories for any word.'
        );
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $show_public = $input->getOption('public');
        $show_private = $input->getOption('private');
        $show_mine = $input->getOption('mine');
        $show_words = $input->getOption('words');
        $count = $input->getOption('count');
        $last = $input->getOption('last');

        if (!$count || !is_numeric($count) || $count < 0) {
            $count = 20;
        }

        if ($show_words) {
            $memories = DB::select('SELECT DISTINCT name FROM memories');
            $words = [];

            foreach ($memories as $word) {
                $words[] = '<span data-type="autofill" data-autofill="show ' . e($word->name) . '">' . e($word->name) . '</span>';
            }

            $output->write(Format::listToTable($words, 6, false));

            return 0;
        }

        if ($show_public) {
            $item_type = 'public';
        } elseif ($show_private) {
            $item_type = 'private';
        } elseif ($show_mine) {
            $item_type = 'mine';
        } else {
            $item_type = 'both';
        }

        $query = Memory::query()
            ->visibility($item_type)
            ->orderBy('name')
            ->orderBy('id')
            ->with('user')
            ->limit($count);

        if ($last) {
            $query->orderBy('created_at');
        } elseif (is_numeric($name)) {
            $memory_id = $name;
            $query->where('id', $memory_id);
        } else {
            $query->where('name', $name);
        }

        $memories = $query->get();

        if ($memories->count() === 0) {
            $output->error('I have no recollection of that.');

            return 1;
        }

        $output->write(Format::memories($memories));

        return 0;
    }
}
