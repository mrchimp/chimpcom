<?php

namespace Mrchimp\Chimpcom\Commands;

use App\Mrchimp\Chimpcom\Id;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\ErrorCode;
use Mrchimp\Chimpcom\Commands\Command;
use Mrchimp\Chimpcom\Facades\Format;
use Mrchimp\Chimpcom\Models\Task as TaskModel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Manage tasks
 */
class TaskDone extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('task');
        $this->setDescription('Marks one or more tasks as complete.');
        $this->addUsage('task:done a3 77 2e');
        $this->addRelated('task');
        $this->addRelated('task:new');
        $this->addRelated('task:edit');
        $this->addRelated('task:tag');
        $this->addRelated('project');
        $this->addArgument(
            'content',
            InputArgument::IS_ARRAY,
            'For NEW, this should be a description of the task. ' . Format::nl() . Format::nbsp(2) .
                'For LIST, only show tasks that contain this. ' . Format::nl() . Format::nbsp(2) .
                'For DONE, this is the ID of the task to mark as completed.' . Format::nl() . Format::nbsp(2) .
                'For ADDTAG/REMOVETAG the first word is the ID of the task and subsequent words are tags to add/remove.'
        );
        $this->addOption(
            'force',
            'f',
            null,
            'Bypass confirmation when marking as DONE.'
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
            $output->error(__('chimpcom.must_log_in'));
            $output->setStatusCode(404);

            return ErrorCode::NOT_AUTHORISED;
        }

        return $this->doneTask($input, $output);
    }

    protected function doneTask(InputInterface $input, OutputInterface $output)
    {
        $user = Auth::user();
        $project = $user->activeProject;

        if (!$project) {
            $output->error('No active project. Use `PROJECT LIST` and `PROJECT SET x`.');
            return ErrorCode::NO_ACTIVE_PROJECT;
        }

        $task_id = Id::decode(implode(' ', $input->getArgument('content')));

        $task = TaskModel::where('id', $task_id)
            ->where('project_id', $project->id)
            ->first();

        if (!$task) {
            $output->error(Format::escape('Couldn\'t find that task.'));
            return ErrorCode::MODEL_NOT_FOUND;
        }

        if ($input->getOption('force')) {
            $task->completed = true;
            $task->time_completed = now();
            $task->save();
            $output->alert('Ok.');

            return ErrorCode::SUCCESS;
        } else {
            $output->setAction('done', [
                'task_to_complete' => $task->id,
            ]);
            $output->useQuestionInput();
            $output->alert('Are you sure you want to mark this as complete?' . Format::nl());
            $output->write($task->description . Format::nl(2));
            $output->write('yes/no?');
        }

        return ErrorCode::SUCCESS;
    }
}
