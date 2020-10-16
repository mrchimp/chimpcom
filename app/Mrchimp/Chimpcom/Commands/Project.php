<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Models\Project as ProjectModel;
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
        $this->addUsage('');
        $this->addUsage('set &lt;project_id&gt');
        $this->addUsage('set &lt;project_name&gt');
        $this->addUsage('new &lt;project_name&gt;');
        $this->addRelated('projects');
        $this->addRelated('newtask');
        $this->addRelated('todo');
        $this->addRelated('done');
        $this->addRelated('priority');
        $this->addArgument(
            'subcommand',
            null,
            'The subcommand to run. Available subcommands are: new, set, rm.'
        );
        $this->addArgument(
            'project',
            null,
            "For the NEW subcommand this should be the project name. " .
                "For the SET or RM subcommands and ID or name can be used."
        );
    }

    /**
     * Run the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error('You must log in to use this command.');

            return 1;
        }

        $user = Auth::user();
        $subcommand = $input->getArgument('subcommand');

        // id or name depending on subcommand
        $project_id = $input->getArgument('project');

        // Create new project
        if ($subcommand === 'new') {
            $validator = Validator::make([
                'project' => $project_id,
            ], [
                'project' => 'required|alpha_dash'
            ]);

            if ($validator->fails()) {
                $output->writeErrors($validator);

                return 2;
            }

            $project = new ProjectModel();
            $project->is_new  = true;
            $project->name    = $project_id;
            $user->projects()->save($project);

            // @todo check current project logic and tidy up this logic
            Session::put('current_project_id', $project->id);

            $user->active_project_id = $project->id;
            $user->save();

            $output->write('Creating project "' . $project->name . '"...<br>');
            $output->write('Please add a description:');
            Chimpcom::setAction('newproject');

            return 0;
        }

        // Set current project
        if ($subcommand === 'set') {
            $project = $user
                ->projects()
                ->nameOrId($project_id)
                ->first();

            if (!$project) {
                $output->error('That project ID is invalid.');
                return 3;
            }

            $project->activeUsers()->save($user);
            $output->alert(e($project->name) . ' is now the current project.');

            return 0;
        }

        // remove project etc
        if ($subcommand === 'rm') {
            $project = $user->projects()
                ->nameOrId($project_id)
                ->first();

            if (!$project) {
                $output->error('Cannot remove that.');
                return 5;
            }

            $output->title('Are you sure you want to delete the project `' . e($project->name) . '`?');
            Session::put('projectrm', $project->id);
            Chimpcom::setAction('project_rm');

            return 0;
        }

        $project = $user->activeProject;

        if (!$project) {
            $output->error('No active project. Use `PROJECTS` and `PROJECT SET x`.');
            return 4;
        }

        // Show info about current project
        $output->say('Current project: ' . $project->name);

        return 0;
    }

    /**
     * Return tab completion options for the current command input
     *
     * @param  Input  $input
     * @return string
     */
    public function tab(InputInterface $input)
    {
        $user = Auth::user();

        $subcommand = $input->getArgument('subcommand');
        $incomplete_name = $input->getArgument('project');

        if ($subcommand === 'set' || $subcommand === 'rm') {
            $projects = $user->projects()->get();

            return $projects
                ->pluck('name')
                ->filter(function ($project_name) use ($incomplete_name) {
                    return Str::startsWith($project_name, $incomplete_name);
                })
                ->transform(function ($item) use ($subcommand) {
                    return 'project ' . $subcommand . ' ' . $item;
                })
                ->values();
        }

        return [];
    }
}
