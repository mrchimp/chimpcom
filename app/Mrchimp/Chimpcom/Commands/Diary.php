<?php

namespace Mrchimp\Chimpcom\Commands;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Mrchimp\Chimpcom\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Facades\Format;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Diary extends Command
{
    const DEFAULT_NUM = 10;

    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('diary');
        $this->setDescription('Create, read and manage diary entries.');
        $this->addUsage('diary new Here is an entry with a @tag in it --project=myproject --date=yesterday');
        $this->addUsage('diary read');
        $this->addUsage('diary list');
        $this->addUsage('diary edit');
        $this->addRelated('project');
        $this->addArgument(
            'subcommand',
            null,
            'The subcommand to run. Available subcommands are: new, read, list, edit.',
            'list'
        );
        $this->addArgument(
            'content',
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'The diary entry.'
        );
        $this->addOption(
            'project',
            'p',
            InputOption::VALUE_REQUIRED,
            'The project to attach the entry to.'
        );
        $this->addOption(
            'date',
            'd',
            InputOption::VALUE_REQUIRED,
            'The date that the entry is for.'
        );
        $this->addOption(
            'num',
            'n',
            InputOption::VALUE_REQUIRED,
            'For the READ and LIST subcommands, the number of entries to show.',
            self::DEFAULT_NUM
        );
    }

    /**
     * Run the command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));

            return 1;
        }

        switch ($input->getArgument('subcommand')) {
            case 'new':
                return $this->newEntry($input, $output);
            case 'read':
                return $this->readEntries($input, $output);
            case 'edit':
                return $this->editEntry($input, $output);
            case 'list':
            default:
                return $this->listEntries($input, $output);
        }
    }

    /**
     * Create an entry
     */
    protected function newEntry(InputInterface $input, OutputInterface $output): int
    {
        try {
            $date = $this->dateFromString($input->getOption('date'));
        } catch (InvalidFormatException $e) {
            $output->error('Invalid date.');
            return 3;
        }

        $words = $input->getArgument('content');
        $project_name = $input->getOption('project');
        $project = $this->projectFromName($project_name);

        if ($project_name && !$project) {
            $output->error('Project not found.');
            return 3;
        }

        [$words, $tags] = $input->splitWordsAndTags($words);

        if (empty($words)) {
            $output->error("You didn't enter any content.");
            return 4;
        }

        $entry = Auth::user()->diaryEntries()->create([
            'content' => implode(' ', $words),
            'project_id' => $project ? $project->id : null,
            'date' => $date,
        ]);

        $entry->attachTags($tags);

        $output->alert('Diary entry saved.');

        return 0;
    }

    /**
     * Read entries
     */
    protected function readEntries(InputInterface $input, OutputInterface $output): int
    {
        try {
            $date = $this->dateFromString($input->getOption('date'));
        } catch (InvalidFormatException $e) {
            $output->error('Invalid date.');
            return 3;
        }
        $project_name = $input->getOption('project');
        $project = $this->projectFromName($project_name);

        if ($project_name && !$project) {
            $output->error('Project not found.');
            return 3;
        }

        $entries = Auth::user()->diaryEntries()
            ->whereDay('date', $date)
            ->get();

        if ($entries->isEmpty()) {
            $output->error('No entry found for that date.');
            return 6;
        }

        $entries->each(function ($entry, $key) use ($output, $entries) {
            $output->write(Format::diaryEntry($entry));


            if ($key !== $entries->count() - 1) {
                $output->write(Format::nl());
            }
        });

        return 0;
    }

    /**
     * List entries
     */
    protected function listEntries(InputInterface $input, OutputInterface $output): int
    {
        $date_str = $input->getOption('date');
        $num = $input->getOption('num');

        try {
            $date = $this->dateFromString($date_str);
        } catch (InvalidFormatException $e) {
            $output->error('Invalid date.');
            return 3;
        }

        $project_name = $input->getOption('project');
        $project = $this->projectFromName($project_name);

        if ($project_name && !$project) {
            $output->error('Project not found.');
            return 3;
        }

        $query = Auth::user()->diaryEntries();

        if ($date_str) {
            $entries = $query
                ->whereDay('date', $date)
                ->orderBy('date', 'asc')
                ->take($num)
                ->get()
                ->reverse();
        } else {
            $entries = $query
                ->orderBy('date', 'desc')
                ->take($num)
                ->get();
        }

        if ($entries->isEmpty()) {
            $output->error('No entry found for that date.');
            return 6;
        }

        $output->write(Format::diaryEntryList($entries));

        return 0;
    }

    /**
     * Edit an entry
     */
    protected function editEntry(InputInterface $input, OutputInterface $output): int
    {
        $output->error('Not implemented yet.');
    }

    protected function dateFromString($date_str): Carbon
    {
        if ($date_str) {
            return Carbon::parse($date_str, 'UTC');
        } else {
            return new Carbon();
        }
    }

    protected function projectFromName($project_name)
    {
        if ($project_name) {
            return Auth::user()
                ->projects()
                ->nameOrId($project_name)
                ->first();
        } else {
            return null;
        }
    }
}
