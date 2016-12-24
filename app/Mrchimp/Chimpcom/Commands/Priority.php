<?php
/**
 * Set the priority of todo items
 */

namespace Mrchimp\Chimpcom\Commands;

use Auth;
use Chimpcom;
use Mrchimp\Chimpcom\Models\Memory;
use Mrchimp\Chimpcom\Models\Task;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Set the priority of todo items
 */
class Priority extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('priority');
        $this->setDescription('Set the priority of todo tasks.');
        $this->addRelated('project');
        $this->addRelated('projects');
        $this->addRelated('newtask');
        $this->addRelated('todo');
        $this->addRelated('done');
        $this->addArgument(
            'task_id',
            InputArgument::REQUIRED,
            'ID of the task.'
        );
        $this->addArgument(
            'priority',
            InputArgument::REQUIRED,
            'Priority to set the task to.'
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
            $output->error('You must be logged in to use this command.');
            return false;
        }

        $user = Auth::user();
        $task_id = Chimpcom::decodeId($input->getArgument('task_id'));
        $priority = (int)$input->getArgument('priority');

        if (!is_numeric($priority)) {
            $output->error('Priority should be an integer.');
            return false;
        }

        $project = $user->activeProject();

        if (!$project) {
            $output->error('No active project. User `PROJECTS` and `PROJECT SET x`.');
            return;
        }

        $task = Task::where('id', $task_id)
                    ->where('user_id', $user->id)
                    ->first();

        if (!$task) {
            $output->error('Couldn\'t find that task, or it\'s not yours to edit.');
            return false;
        }

        $task->priority = $priority;

        if ($task->save()) {
            $output->alert('Ok.');
        } else {
            $output->error('There was a problem. Try again?');
        }
    }
}
