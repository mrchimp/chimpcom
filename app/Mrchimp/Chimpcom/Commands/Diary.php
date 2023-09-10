<?php

namespace Mrchimp\Chimpcom\Commands;

use Carbon\Carbon;
use Mrchimp\Chimpcom\Str;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Facades\Format;
use Mrchimp\Chimpcom\Traits\ManagesProjects;
use Carbon\Exceptions\InvalidFormatException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Diary extends Command
{
    use ManagesProjects;

    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('diary');
        $this->setDescription('List diary entries.');
        $this->addUsage('diary --project=myproject --date=yesterday');
        $this->addUsage('diary:read');
        $this->addUsage('diary:list');
        $this->addUsage('diary:edit');
        $this->addUsage('diary:graph');
        $this->addRelated('project');
        $this->addRelated('tag');
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
            10
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

        return $this->listEntries($input, $output);
    }

    /**
     * List entries
     */
    protected function listEntries(InputInterface $input, OutputInterface $output): int
    {
        $date_str = $input->getOption('date');
        $num = $input->getOption('num');

        try {
            $date = $input->dateOption('date');
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
}
