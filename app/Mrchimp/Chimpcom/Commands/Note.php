<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Illuminate\Support\Facades\DB;
use Mrchimp\Chimpcom\ErrorCode;
use Mrchimp\Chimpcom\Facades\Format;
use Mrchimp\Chimpcom\Models\Memory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Note extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('note');
        $this->setDescription('Find a memory by its name.');
        $this->addRelated('note');
        $this->addRelated('note:save');
        $this->addRelated('note:find');
        $this->addRelated('note:forget');
        $this->addRelated('note:public');
        $this->addRelated('note:tag');
        $this->addRelated('project');
        $this->addRelated('tag');
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
            'number',
            'n',
            InputOption::VALUE_REQUIRED,
            'Number of results to display. Default: 20'
        );

        $this->addOption(
            'random',
            'r',
            null,
            'Order the notes randomly.'
        );

        $this->addOption(
            'date',
            'd',
            null,
            'Order notes by date created. Newest first.',
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
        $number = $input->getOption('number');
        $random = $input->getOption('random');
        $date = $input->getOption('date');
        $project_name = $input->getOption('project');
        $project = null;

        [$names, $tags] = $input->splitWordsAndTags($words);

        \Log::debug('tags', $tags);
        \Log::debug('names', $names);

        if (!$number || !is_numeric($number) || $number < 0) {
            $number = 20;
        }

        if ($show_words) {
            $memories = DB::select('SELECT DISTINCT name FROM memories');
            $words = [];

            foreach ($memories as $word) {
                $words[] = Format::style(Format::escape($word->name), '', [
                    'data-type' => 'autofill',
                    'data-autofill' => 'show ' . Format::escape($word->name),
                ]);
            }

            $output->write(Format::listToTable($words, 6, false));

            return ErrorCode::OK;
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
            ->when($project, function ($query) use ($project) {
                $query->where('project_id', $project->id);
            })
            ->when(!empty($names), function ($query) use ($names) {
                $query->whereIn('name', $names);
            })
            ->with('user', 'tags')
            ->limit($number);

        if ($date) {
            $query->orderBy('created_at', 'DESC');
        } else if ($random) {
            $query->inRandomOrder();
        } else {
            $query->orderBy('name', 'ASC')->orderBy('name', 'ASC');
        }

        if (!empty($tags)) {
            $query->withTags($tags);
        }

        $memories = $query->get();

        if ($memories->count() === 0) {
            $output->error('I have no recollection of that.');

            return ErrorCode::MODEL_NOT_FOUND;
        }

        $output->write(Format::memories($memories));

        return ErrorCode::OK;
    }
}
