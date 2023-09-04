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
class ProjectRm extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('project:rm');
        $this->setDescription('Removes a project.');
        $this->addUsage('project:rm projectname');
        $this->addRelated('task');
        $this->addRelated('priority');
        $this->addRelated('project');
        $this->addRelated('project:list');
        $this->addRelated('project:new');
        $this->addRelated('project:set');
        $this->addArgument(
            'project',
            null,
            "ID or name of project to delete."
        );
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

        return $this->removeProject($input, $output);
    }

    /**
     * Return tab completion options for the current command input
     */
    public function tab(InputInterface $input): string
    {
        $user = Auth::user();
        $incomplete_name = $input->getArgument('project');
        $projects = $user->projects()->get();

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
     * Remove a project
     */
    protected function removeProject(InputInterface $input, OutputInterface $output): int
    {
        $project_id = $input->getArgument('project');

        $project = Auth::user()->projects()
            ->nameOrId($project_id)
            ->first();

        if (!$project) {
            $output->error('Cannot remove that.');
            return 5;
        }

        $output->title('Are you sure you want to delete the project `' . Format::escape($project->name) . '`?');
        $output->useQuestionInput();
        $output->setAction('project_rm', [
            'projectrm' => $project->id,
        ]);

        return 0;
    }
}
