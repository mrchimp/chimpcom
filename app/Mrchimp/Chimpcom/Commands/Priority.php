<?php

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Facades\Chimpcom;
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
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));

            return 1;
        }

        $user = Auth::user();
        $task_id = Chimpcom::decodeId($input->getArgument('task_id'));
        $priority = $input->getArgument('priority');

        if (!is_numeric($priority)) {
            $output->error('Priority should be an integer.');

            return 2;
        }

        $task = Task::where('id', $task_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$task) {
            $output->error(e('Couldn\'t find that task, or it\'s not yours to edit.'));

            return 4;
        }

        $task->priority = (int) $priority;

        $task->save();
        $output->alert('Ok.');

        return 0;
    }
}
