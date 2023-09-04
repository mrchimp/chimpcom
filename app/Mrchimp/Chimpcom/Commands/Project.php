<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Mrchimp\Chimpcom\Facades\Format;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Manage projects
 */
class Project extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('project');
        $this->setDescription('Sets the active project or shows details of the active project.');
        $this->addUsage('project');
        $this->addRelated('task');
        $this->addRelated('priority');
        $this->addRelated('project:list');
        $this->addRelated('project:new');
        $this->addRelated('project:set');
        $this->addRelated('project:rm');
    }

    /**
     * Run the command
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));

            return 1;
        }

        return $this->showCurrentProject($input, $output);
    }

    /**
     * Show the name of the user's active project
     */
    protected function showCurrentProject(InputInterface $input, OutputInterface $output): int
    {
        $project = Auth::user()->activeProject;

        if (!$project) {
            $output->error('No active project. Use `PROJECT LIST` and `PROJECT SET x`.');
            return 4;
        }

        $output->say('Current project: ' . $project->name);
        return 0;
    }
}
