<?php

namespace Mrchimp\Chimpcom\Actions;

use Auth;
use Chimpcom;
use App\Mrchimp\Chimpcom\Id;
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
        $id = $input->getActionData('task_to_edit');
        $task_id = Id::decode($id);
        $user = Auth::user();
        $project = $user->activeProject;

        Chimpcom::delAction($input->getActionId());

        if (!$project) {
            $output->error('No active project. Use `PROJECT LIST` and `PROJECT SET x`.');
            return 1;
        }

        $task = $user->tasks()->where('id', $task_id)->first();

        if (!$task) {
            $output->error('Task not found.');
            return 2;
        }

        $task->description = $input->getContent();
        $task->save();

        $output->alert('Ok.');

        return 0;
    }
}
