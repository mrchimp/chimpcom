<?php

/**
 * Delete a project
 */

namespace Mrchimp\Chimpcom\Actions;

use App\Mrchimp\Chimpcom\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Mrchimp\Chimpcom\Booleanate;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Models\Project;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Delete a project
 */
class Project_rm extends Action
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('projectrm');
        $this->setDescription('Remove a project.');
        $this->addArgument(
            'confirmation',
            InputArgument::REQUIRED,
            'A yes or no-like answer'
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

            return 1;
        }

        Chimpcom::setAction('normal');
        $user = Auth::user();

        $argument = $input->getArgument('confirmation');

        if (!Booleanate::isAffirmative($argument)) {
            $output->error('Fair enough.');

            return 0;
        }

        $project = Project::where('id', Session::get('projectrm'))->first();

        Session::forget('projectrm');

        if (!$project) {
            $output->error('No active project.');
            return 0;
        }

        if ((int) $project->user_id !== (int) $user->id) {
            $output->error('That isn\'t yours to delete.');

            return 2;
        }

        $project->delete();

        $output->alert(e('Ok. It\'s gone.'));

        return 0;
    }
}
