<?php

namespace Mrchimp\Chimpcom\Commands;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Facades\Format;
use Mrchimp\Chimpcom\Traits\ManagesProjects;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiaryRead extends Command
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
        $this->setDescription('Create, read and manage diary entries.');
        $this->addUsage('diary new Here is an entry with a @tag in it --project=myproject --date=yesterday');
        $this->addRelated('diary:read');
        $this->addRelated('diary:list');
        $this->addRelated('diary:edit');
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

        return $this->readEntries($input, $output);
    }

    /**
     * Read entries
     */
    protected function readEntries(InputInterface $input, OutputInterface $output): int
    {
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
}
