<?php

namespace Mrchimp\Chimpcom\Commands;

use App\Mrchimp\Chimpcom\Id;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\ErrorCode;
use Mrchimp\Chimpcom\Commands\Command;
use Mrchimp\Chimpcom\Models\Task as TaskModel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Manage tasks
 */
class TaskEdit extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('task:edit');
        $this->setDescription('Update tasks');
        $this->setHelp('Change their description and priority or assign them to a different project.');
        $this->addRelated('task');
        $this->addRelated('task:new');
        $this->addRelated('task:done');
        $this->addRelated('task:tag');
        $this->addRelated('project');
        $this->addArgument(
            'task_id',
            null,
            'The ID of the task to edit.',
        );
        $this->addOption(
            'priority',
            'p',
            InputArgument::OPTIONAL,
            'Priority of the task. Higher is more important. Default is 1.'
        );
        $this->addOption(
            'project',
            null,
            InputArgument::OPTIONAL,
            'Name of project to assign task to.'
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

        return $this->editTask($input, $output);
    }

    /**
     * Additional argument/option validation rules
     */
    protected function rules(InputInterface $input): array
    {
        return [
            'priority' => 'numeric',
        ];
    }

    protected function editTask(InputInterface $input, OutputInterface $output)
    {
        $user = Auth::user();
        $new_project = $input->getOption('project');
        $new_priority = $input->getOption('priority');

        $id = $input->getArgument('task_id');

        if (empty($id)) {
            $output->error('No ID provided.');
            return ErrorCode::INVALID_ARGUMENT;
        }

        if ($new_priority !== null && !is_numeric($new_priority)) {
            $output->error('Priority must be an integer.');
            return ErrorCode::INVALID_ARGUMENT;
        }

        $task_id = Id::decode($id);

        $task = $user->tasks()
            ->where('id', $task_id)
            ->first();

        if (!$task) {
            $output->error('Could not find task.');
            return ErrorCode::MODEL_NOT_FOUND;
        }

        if ($new_project || $new_priority) {
            if ($new_project) {
                $project = $user->projects()->nameOrId($new_project)->first();

                if (!$project) {
                    $output->error('Could not find project.');
                    return ErrorCode::MODEL_NOT_FOUND;
                }

                $task->project()->save($project);
            }

            if ($new_priority) {
                $task->priority = $new_priority;
                $task->save();
            }

            $output->alert('Priority set to ' . $new_priority . ' for task:', true);
            $output->grey($task->description);
            return ErrorCode::SUCCESS;
        }

        $output->setAction('edit_task', [
            'task_to_edit' => $task->id,
        ]);
        $output->editContent($task->description);

        return ErrorCode::SUCCESS;
    }
}
