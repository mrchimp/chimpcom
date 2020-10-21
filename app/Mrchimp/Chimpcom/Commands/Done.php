<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Mrchimp\Chimpcom\Models\Task;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Mark a task as done
 */
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
        $this->setDescription('Mark a task as complete.');
        $this->addRelated('project');
        $this->addRelated('projects');
        $this->addRelated('newtask');
        $this->addRelated('todo');
        $this->addRelated('priority');
        $this->addArgument(
            'task_id',
            InputArgument::REQUIRED,
            'ID of the task to complete.'
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
        $project = $user->activeProject;

        if (!$project) {
            $output->error('No active project. Use `PROJECTS` and `PROJECT SET x`.');
            return 2;
        }

        $task_id = Chimpcom::decodeId($input->getArgument('task_id'));

        $task = Task::where('id', $task_id)
            ->where('project_id', $project->id)
            ->first();

        if (!$task) {
            $output->error(e('Couldn\'t find that task.'));
            return 3;
        }

        Session::put('task_to_complete', $task->id);

        $output->alert('Are you sure you want to mark this as complete?<br>');
        $output->say($task->description);

        Chimpcom::setAction('done');

        return 0;
    }
}
