<?php

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Mrchimp\Chimpcom\Facades\Format;
use Mrchimp\Chimpcom\Models\Project;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create projects
 */
class ProjectNew extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('project:new');
        $this->setDescription('Create a project.');
        $this->addUsage('project:new &lt;project_name&gt;');
        $this->addRelated('task');
        $this->addRelated('priority');
        $this->addRelated('project');
        $this->addRelated('project:set');
        $this->addRelated('project:rm');
        $this->addArgument(
            'project_name',
            InputArgument::REQUIRED,
            "The project name."
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

        return $this->newProject($input, $output);
    }

    /**
     * Create a new project
     */
    protected function newProject(InputInterface $input, OutputInterface $output): int
    {
        $project_name = $input->getArgument('project_name');

        $validator = Validator::make([
            'project' => $project_name,
        ], [
            'project' => 'alpha_dash'
        ]);

        if ($validator->fails()) {
            $output->writeErrors($validator);

            return 2;
        }

        $project = Project::create([
            'is_new' => true,
            'name' => $project_name,
            'user_id' => Auth::id(),
        ]);

        Auth::user()->setActiveProject($project);

        $output->setAction('newproject');
        $output->write('Creating project "' . $project->name . '"...' . Format::nl());
        $output->write('Please add a description:');
        $output->useQuestionInput();

        return 0;
    }
}
