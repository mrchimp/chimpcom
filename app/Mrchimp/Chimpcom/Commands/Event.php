<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Commands\Command;
use Mrchimp\Chimpcom\ErrorCode;
use Mrchimp\Chimpcom\Facades\Format;
use Mrchimp\Chimpcom\Traits\ManagesProjects;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Event extends Command
{
    use ManagesProjects;

    protected function configure()
    {
        $this->setName('event');
        $this->setDescription('List calendar events.');
        $this->addRelated('event:new');
        $this->addArgument(
            'content',
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'Only show events that contain this text or tags.'
        );
        $this->addOption(
            'project',
            'p',
            InputOption::VALUE_OPTIONAL,
            'Only show events attached to this project.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));

            return ErrorCode::NOT_AUTHORISED;
        }

        return $this->listEvents($input, $output);
    }

    protected function listEvents(InputInterface $input, OutputInterface $output)
    {
        $user = Auth::user();
        $project_name = $input->getOption('project');
        $project = $this->projectFromName($project_name);
        $content = $input->getArgument('content');
        [$words, $tags] = $input->splitWordsAndTags($content);

        if ($project_name && !$project) {
            $output->error('Project not found.');
            return ErrorCode::MODEL_NOT_FOUND;
        }

        $events = $user->events()
            ->when(!empty($tags), fn ($query) => $query->withTags($tags))
            ->when(!empty($words), fn ($query) => $query->search(implode(' ', $words)))
            ->when($project, function ($query) use ($project) {
                $query->where('project_id', $project->id);
            })
            ->future()
            ->get();

        if ($events->isEmpty()) {
            $output->error('No events found.');
            return ErrorCode::MODEL_NOT_FOUND;
        }

        $output->write(Format::events($events));

        return ErrorCode::OK;
    }
}
