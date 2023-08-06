<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mrchimp\Chimpcom\Console\Input;
use Mrchimp\Chimpcom\Console\Output;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Facades\Format;
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
        $this->addUsage('list');
        $this->addUsage('set &lt;project_id&gt');
        $this->addUsage('set &lt;project_name&gt');
        $this->addUsage('new &lt;project_name&gt;');
        $this->addRelated('task');
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
            "For the NEW subcommand this should be the project name.<br>" .
                "For the SET or RM subcommands an ID or name can be used."
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
            $output->error(__('chimpcom.must_log_in'));

            return 1;
        }

        switch ($input->getArgument('subcommand')) {
            case 'new':
                return $this->newProject($input, $output);
            case 'set':
                return $this->setProject($input, $output);
            case 'rm':
                return $this->removeProject($input, $output);
            case 'list':
                return $this->listProjects($input, $output);
            default:
                return $this->showCurrentProject($input, $output);
        }
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

    /**
     * Create a new project
     */
    protected function newProject(InputInterface $input, OutputInterface $output): int
    {
        $project_name = $input->getArgument('project');

        $validator = Validator::make([
            'project' => $project_name,
        ], [
            'project' => 'required|alpha_dash'
        ]);

        if ($validator->fails()) {
            $output->writeErrors($validator);

            return 2;
        }

        $project = ProjectModel::create([
            'is_new' => true,
            'name' => $project_name,
            'user_id' => Auth::id(),
        ]);

        Auth::user()->setActiveProject($project);

        $output->write('Creating project "' . $project->name . '"...' . Format::nl());
        $output->write('Please add a description:');
        $output->useQuestionInput();

        Chimpcom::setAction('newproject');

        return 0;
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
        Session::put('projectrm', $project->id);
        Chimpcom::setAction('project_rm');

        return 0;
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

    /**
     * List all user's projects
     */
    protected function listProjects(InputInterface $intput, OutputInterface $output)
    {
        $user = Auth::user();

        // Report result
        if (count($user->projects) === 0) {
            $output->error('No projects.');

            return 0;
        }

        $output_chunks = [];

        foreach ($user->projects as $project) {
            $output_chunks[] = '#' . $project->id . ' <span data-type="autofill" data-autofill="project ' . $project->id . '"> ' . Format::escape($project->name) . ' - ' . count($project->tasks) . ' tasks</span>';
        }

        $output->write(implode(Format::nl(), $output_chunks));

        return 0;
    }
}
