<?php

namespace Mrchimp\Chimpcom\Actions;

use DB;
use Auth;
use Session;
use App\User;
use Chimpcom;
use Mrchimp\Chimpcom\Commands\Command;
use Mrchimp\Chimpcom\Booleanate;
use Mrchimp\Chimpcom\Models\Task;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Done extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('done');
        $this->setDescription('Mark a task as completed.');
        $this->addArgument(
            'confirmation',
            InputArgument::REQUIRED,
            'A yes or no-like answer.'
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
            $output->error('You must be logged in to use this command.');
            return false;
        }

        $user = Auth::user();
        $confirmation = $input->getArgument('confirmation');
        $confirmed = Booleanate::isAffirmative($confirmation);
        $project = $user->activeProject;

        if (!$project) {
            $output->error('No active project. Use `PROJECTS` and `PROJECT SET x`.');
            return;
        }

        if (!$confirmed) {
            $output->write('Fair enough.');
            Chimpcom::setAction();
            Session::forget('task_to_complete');
            return;
        }

        $task = Task::where('id', Session::get('task_to_complete'))
                    ->where('project_id', $project->id)
                    ->first();

        if (!$task) {
            $output->error('Couldn\'t find that task.');
            return;
        }

        $task->completed = true;
        $task->time_completed = DB::raw('now()');
        $task->save();

        Chimpcom::setAction();
        $output->alert('Ok.');
    }

}
