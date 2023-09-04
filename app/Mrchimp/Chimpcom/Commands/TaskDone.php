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
        $this->setName('task:done');
        $this->setDescription('Marks one or more tasks as complete.');
        $this->addUsage('task:done a3 77 2e');
        $this->addRelated('task');
        $this->addRelated('task:new');
        $this->addRelated('task:edit');
        $this->addRelated('task:tag');
        $this->addRelated('project');
        $this->addArgument(
            'content',
            InputArgument::IS_ARRAY | InputArgument::REQUIRED,
            'One or more IDs of tasks to mark as completed.'
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
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!Auth::check()) {
            $output->error(__('chimpcom.must_log_in'));
            $output->setStatusCode(404);

            return ErrorCode::NOT_AUTHORISED;
        }

        return $this->doneTask($input, $output);
    }

    protected function doneTask(InputInterface $input, OutputInterface $output): int
    {
        $user = Auth::user();
        $project = $user->activeProject;

        if (!$project) {
            $output->error('No active project. Use `PROJECT LIST` and `PROJECT SET x`.');
            return ErrorCode::NO_ACTIVE_PROJECT;
        }

        $task_ids = array_map(fn ($id) => Id::decode($id), $input->getArgument('content'));

        $tasks = TaskModel::whereIn('id', $task_ids)
            ->where('project_id', $project->id)
            ->get();

        if ($tasks->isEmpty()) {
            $output->error(Format::escape('Couldn\'t find that task.'));
            return ErrorCode::MODEL_NOT_FOUND;
        }

        if ($input->getOption('force')) {
            $tasks->each(fn ($task) => $task->markAsDone());
            $output->alert($tasks->count() . ' tasks completed.');

            return ErrorCode::SUCCESS;
        } else {
            $output->setAction('done', [
                'tasks_to_complete' => $tasks->pluck('id'),
            ]);
            $output->useQuestionInput();
            $output->alert('Are you sure you want to mark as complete?' . Format::nl());
            $tasks->each(fn ($task) => $output->write($task->description . Format::nl()));
            $output->write(Format::nl() . 'yes/no?');
        }

        return ErrorCode::SUCCESS;
    }
}
