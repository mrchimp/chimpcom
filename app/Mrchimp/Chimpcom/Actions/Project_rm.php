<?php
/**
 * Delete a project
 */

namespace Mrchimp\Chimpcom\Actions;

use Auth;
use Chimpcom;
use Session;
use Mrchimp\Chimpcom\Booleanate;
use Mrchimp\Chimpcom\Format;
use Mrchimp\Chimpcom\Models\Project;
use Mrchimp\Chimpcom\Commands\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Delete a project
 */
class Project_rm extends Command
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
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error('You must be logged in to perform this action.');
            return false;
        }

        Chimpcom::setAction('normal');
        $user = Auth::user();

        $argument = $input->getArgument('confirmation');

        if (!Booleanate::isAffirmative($argument)) {
            $output->error('Fair enough.');
            return;
        }

        $project = Project::where('id', Session::get('projectrm'))->first();
        Session::forget('projectrm');

        if (!$project) {
            $output->error('No active project.');
            return;
        }

        if ($project->user_id !== $user->id) {
            $output->error('That isn\'t yours to delete.');
            return;
        }

        $project->delete();

        $output->alert('Ok. It\'s gone.');
    }

}
