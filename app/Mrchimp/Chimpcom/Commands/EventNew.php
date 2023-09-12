<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Carbon\Exceptions\InvalidFormatException;
use Mrchimp\Chimpcom\Commands\Command;
use Mrchimp\Chimpcom\ErrorCode;
use Mrchimp\Chimpcom\Traits\ManagesProjects;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EventNew extends Command
{
    use ManagesProjects;

    protected function configure()
    {
        $this->setName('event:new');
        $this->setDescription('Create a new calendar event.');
        $this->setHelp('Tasks can be optionally be attached to a project.');
        $this->addOption(
            'date',
            'd',
            InputOption::VALUE_REQUIRED,
            'Date of the event. Tip: You may need to put this in quotes if it includes a space.'
        );
        $this->addArgument(
            'description',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Description of the task.'
        );
        $this->addOption(
            'project',
            'p',
            InputArgument::OPTIONAL,
            'Name of project to assign event to.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));

            return ErrorCode::NOT_AUTHORISED;
        }

        return $this->newEvent($input, $output);
    }

    protected function newEvent(InputInterface $input, OutputInterface $output): int
    {
        $user = Auth::user();
        [$words, $tags] = $input->splitWordsAndTags($input->getArgument('description'));
        $project_name = $input->getOption('project');
        $project = $this->projectFromName($project_name);
        $description = implode(' ', $words);
        try {
            $date = $input->dateOption('date');
        } catch (InvalidFormatException $e) {
            $output->error('Invalid date.');
            return 3;
        }

        if (empty($words)) {
            $output->error('Please provide a description of the event.');
            return ErrorCode::INVALID_ARGUMENT;
        }

        if ($project_name && !$project) {
            $output->error('Project not found.');
            return ErrorCode::MODEL_NOT_FOUND;
        }

        $event = Auth::user()->events()->create([
            'description' => $description,
            'date' => $date,
            'project_id' => $project ? $project->id : null,
        ]);

        $event->attachTags($tags);

        $output->alert('Event created.');

        return ErrorCode::OK;
    }
}
