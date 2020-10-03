<?php

/**
 * Save the description for a new project
 */

namespace Mrchimp\Chimpcom\Actions;

use App\Mrchimp\Chimpcom\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Models\Project;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Save the description for a new project
 * @action normal
 */
class Newproject extends Action
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('newproject');
        $this->setDescription('Creates a new project.');
        $this->addArgument(
            'description',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Description for the new project.'
        );
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error('You must be logged in to perform this action.');

            Chimpcom::resetTerminal();

            return 1;
        }

        $user = Auth::user();
        $description = implode(' ', $input->getArgument('description'));

        if (!Session::has('current_project_id')) {
            $output->error('Oops. There was a problem. [Current project not set.]');

            Chimpcom::setAction('normal');

            return 2;
        }

        $project = Project::find(Session::get('current_project_id'));

        if (!$project->id) {
            $output->error('Current project doesn\'t exist.');

            Session::forget('current_project_id');
            Chimpcom::setAction('normal');

            return 3;
        }

        $project->description = $description;
        $project->is_new = false;
        $project->save();

        $project->activeUsers()->save($user);

        $output->alert('Project saved and set as current project.');

        Chimpcom::setAction('normal');

        return 0;
    }
}
