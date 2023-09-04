<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Illuminate\Support\Str;
use Mrchimp\Chimpcom\Facades\Format;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Manage projects
 */
class ProjectSet extends Command
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName('project:set');
        $this->setDescription('Sets the active project.');
        $this->addUsage('project:set projectname');
        $this->addRelated('task');
        $this->addRelated('priority');
        $this->addRelated('project');
        $this->addRelated('project:new');
        $this->addRelated('project:rm');
        $this->addArgument(
            'project',
            null,
            "Project ID or name can be used."
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

        return $this->setProject($input, $output);
    }

    /**
     * Return tab completion options for the current command input
     */
    public function tab(InputInterface $input)
    {
        $incomplete_name = $input->getArgument('project');
        $projects = Auth::user()->projects()->get();

        return $projects
            ->pluck('name')
            ->filter(function ($project_name) use ($incomplete_name) {
                return Str::startsWith($project_name, $incomplete_name);
            })
            ->transform(function ($item) {
                return 'project:set ' . $item;
            })
            ->values();
    }

    /**
     * Set the user's current project
     */
    protected function setProject(InputInterface $input, OutputInterface $output): int
    {
        $project_id = $input->getArgument('project');

        $project = Auth::user()
            ->projects()
            ->nameOrId($project_id)
            ->first();

        if (!$project) {
            $output->error('That project ID is invalid.');
            return 3;
        }

        Auth::user()->setActiveProject($project);
        $output->alert(Format::escape($project->name) . ' is now the current project.');

        return 0;
    }
}
