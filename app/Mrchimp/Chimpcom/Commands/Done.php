<?php
/**
 * Give credit where it's due
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Session;
use Chimpcom;
use Mrchimp\Chimpcom\Format;
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
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error('You must log in to use this command.');
            return false;
        }

        $user = Auth::user();
        $project = $user->activeProject;

        if (!$project) {
            $output->error('No active project. Use `PROJECTS` and `PROJECT SET x`.');
            return;
        }

        $task_id = Chimpcom::decodeId($input->getArgument('task_id'));

        $task = Task::where('id', $task_id)
                    ->where('project_id', $project->id)
                    ->first();

        if (!$task) {
            $output->error('Couldn\'t find that task.');
            return false;
        }

        Session::set('task_to_complete', $task->id);

        $output->alert('Are you sure you want to delete this task?<br>');
        $output->say($task->description);

        Chimpcom::setAction('done');
    }

}
