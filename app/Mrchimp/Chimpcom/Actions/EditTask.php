<?php

namespace Mrchimp\Chimpcom\Actions;

use Auth;
use Chimpcom;
use Mrchimp\Chimpcom\ErrorCode;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EditTask extends Action
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('edit_Task');
        $this->setDescription('Handle task edited content and save it');
        $this->addOption('continue', 'c', null, 'Continue editing');
    }

    /**
     * Run the command
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $task_id = $input->getActionData('task_to_edit');
        $user = Auth::user();
        $project = $user->activeProject;

        Chimpcom::delAction($input->getActionId());

        if (!$project) {
            $output->error('No active project. Use `PROJECT LIST` and `PROJECT SET x`.');
            return ErrorCode::NO_ACTIVE_PROJECT;
        }

        $task = $user->tasks()->where('id', $task_id)->first();

        if (!$task) {
            $output->error('Task not found.');
            return ErrorCode::MODEL_NOT_FOUND;
        }

        $task->description = $input->getContent();
        $task->save();

        $output->alert('Ok.');

        return ErrorCode::OK;
    }
}
