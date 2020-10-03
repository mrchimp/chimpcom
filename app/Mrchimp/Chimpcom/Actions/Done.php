<?php

namespace Mrchimp\Chimpcom\Actions;

use App\Mrchimp\Chimpcom\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Mrchimp\Chimpcom\Booleanate;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Models\Task;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Done extends Action
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
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error('You must be logged in to use this command.');

            return 1;
        }

        $user = Auth::user();
        $confirmation = $input->getArgument('confirmation');
        $confirmed = Booleanate::isAffirmative($confirmation);
        $project = $user->activeProject;

        if (!$project) {
            $output->error('No active project. Use `PROJECTS` and `PROJECT SET x`.');

            return 2;
        }

        if (!$confirmed) {
            $output->write('Fair enough.');
            Chimpcom::setAction();
            Session::forget('task_to_complete');

            return 0;
        }

        $task = Task::where('id', Session::get('task_to_complete'))
            ->where('project_id', $project->id)
            ->first();

        if (!$task) {
            $output->error('Couldn\'t find that task.');

            return 3;
        }

        $task->completed = true;
        $task->time_completed = DB::raw('now()');
        $task->save();

        Chimpcom::setAction();

        $output->alert('Ok.');

        return 0;
    }
}
