<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Mrchimp\Chimpcom\Facades\Format;
use Mrchimp\Chimpcom\Models\Memory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
            'names',
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'Name(s) of memories to search for.'
        );

        $this->addOption(
            'public',
            null,
            null,
            'Shows only public memories.'
        );

        $this->addOption(
            'private',
            null,
            null,
            'Shows only private memories.'
        );

        $this->addOption(
            'project',
            'p',
            InputOption::VALUE_REQUIRED,
            'Show only memories associated with a given project.',
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
        $words = $input->getArgument('names');
        $show_public = $input->getOption('public');
        $show_private = $input->getOption('private');
        $show_mine = $input->getOption('mine');
        $show_words = $input->getOption('words');
        $count = $input->getOption('count');
        $last = $input->getOption('last');
        $project_name = $input->getOption('project');
        $project = null;

        [$names, $tags] = $input->splitWordsAndTags($words);

        \Log::debug('tags', $tags);
        \Log::debug('names', $names);

        if (!$count || !is_numeric($count) || $count < 0) {
            $count = 20;
        }

        if ($show_words) {
            $memories = DB::select('SELECT DISTINCT name FROM memories');
            $words = [];

            foreach ($memories as $word) {
                $words[] = '<span data-type="autofill" data-autofill="show ' . Format::escape($word->name) . '">' . Format::escape($word->name) . '</span>';
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

        if ($project_name) {
            $project = Auth::user()
                ->projects()
                ->nameOrId($project_name)
                ->first();
        }

        $query = Memory::query()
            ->visibility($item_type)
            ->when(!$last, function ($query) {
                $query
                    ->orderBy('name')
                    ->orderBy('id');
            })
            ->when($project, function ($query) use ($project) {
                $query->where('project_id', $project->id);
            })
            ->with('user', 'tags')
            ->limit($count);

        if ($last) {
            $query->orderBy('created_at', 'DESC');
        } elseif (is_numeric(Arr::first($names))) { // @todo Does this use hexids?
            $memory_id = $names;
            $query->whereIn('id', $memory_id);
        } elseif (!empty($names)) {
            $query->whereIn('name', $names);
        }

        if (!empty($tags)) {
            $query->withTags($tags);
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
