<?php

/**
 * Manage projects
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Chimpcom;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Project as ProjectModel;
use Session;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Validator;

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
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error('You must log in to use this command.');
            return;
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
                return false;
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

            return;
        }

        // Set current project
        if ($subcommand === 'set') {
            $project = $user->projects()
                ->nameOrId($project_id)
                ->first();

            if (!$project) {
                $output->error('That project ID is invalid.');
                return;
            }

            $project->activeUsers()->save($user);
            $output->alert(e($project->name) . ' is now the current project.');

            return;
        }

        // remove project etc
        if ($subcommand === 'rm') {
            $project = $user->projects()
                ->nameOrId($project_id)
                ->first();

            $output->title('Are you sure you want to delete the project `' . e($project->name) . '`?');
            Session::put('projectrm', $project->id);
            Chimpcom::setAction('project_rm');
            return;
        }

        $project = $user->activeProject;

        if (!$project) {
            $output->error('No active project. Use `PROJECTS` and `PROJECT SET x`.');
            return;
        }

        // Show info about current project
        $output->say('Current project: ' . $project->name);
    }

    /**
     * Return tab completion result
     *
     * @param  InputInterface $input
     * @return String
     */
    public function tabcomplete(InputInterface $input)
    {
        $user = Auth::user();

        $subcommand = $input->getArgument('subcommand');

        if ($subcommand === 'set') {
            $projects = $user->projects()->select('name')->get();

            foreach ($projects as $project) {
                if (strrpos($project->name, $this->input->get(2))) {
                    return 'project set ' . $project->name;
                }
            }
        }

        return '';
    }
}
