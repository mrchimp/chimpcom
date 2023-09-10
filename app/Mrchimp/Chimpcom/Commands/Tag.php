<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\ErrorCode;
use Mrchimp\Chimpcom\Facades\Format;
use Mrchimp\Chimpcom\Models\Tag as TagModel;
use Mrchimp\Chimpcom\Traits\ManagesProjects;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Tag extends Command
{
    use ManagesProjects;

    protected function configure()
    {
        $this->setName('tag');
        $this->setDescription('View the tags that are used in Notes, Tasks or diary entries.');
        $this->addOption(
            'project',
            'p',
            InputOption::VALUE_REQUIRED,
            'Find only tags related to the given project.'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));

            return ErrorCode::NOT_AUTHORISED;
        }

        return $this->listTags($input, $output);
    }

    public function listTags(InputInterface $input, OutputInterface $output)
    {
        $project_name = $input->getOption('project');
        $project = $this->projectFromName($project_name);

        if (!$project && $project_name) {
            $output->error("Project not found.");
            return ErrorCode::MODEL_NOT_FOUND;
        }

        $tag_names = TagModel::query()
            ->when($project, fn ($query) => $query->forProject($project))
            ->get()
            ->pluck('tag')
            ->values()
            ->toArray();

        if (empty($tag_names)) {
            $output->error('No tags found.');
        } else {
            $output->write(Format::listToTable($tag_names, 4, true));
        }

        return ErrorCode::OK;
    }
}
