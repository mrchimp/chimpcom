<?php

namespace Mrchimp\Chimpcom\Commands;

use App\Mrchimp\Chimpcom\Id;
use App\Mrchimp\Chimpcom\ProgressBar;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Mrchimp\Chimpcom\Str;
use Mrchimp\Chimpcom\ErrorCode;
use Mrchimp\Chimpcom\Commands\Command;
use Mrchimp\Chimpcom\Facades\Format;
use Mrchimp\Chimpcom\Models\Tag;
use Mrchimp\Chimpcom\Models\Task as TaskModel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Manage tasks
 */
class Task extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('task');
        $this->setDescription('Lists tasks on the current project.');
        $this->setHelp('By default only incomplete tasks from the current project are shown.');
        $this->addRelated('task:new');
        $this->addRelated('task:done');
        $this->addRelated('task:edit');
        $this->addRelated('task:tag');
        $this->addRelated('project');
        $this->addArgument(
            'content',
            InputArgument::IS_ARRAY,
            'Only show tasks that contain this text. ' . Format::nl() . Format::nbsp(2)
        );
        $this->addOption(
            'all',
            'a',
            null,
            'List complete and incomplete tasks.'
        );
        $this->addOption(
            'allprojects',
            null,
            null,
            'List tasks from all of your projects.'
        );
        $this->addOption(
            'complete',
            'c',
            null,
            'List completed tasks.'
        );
        $this->addOption(
            'dates',
            'd',
            null,
            'Show creation and completion dates.'
        );
        $this->addOption(
            'number',
            'n',
            InputOption::VALUE_REQUIRED,
            'Number of tasks to show.',
            10
        );
        $this->addOption(
            'priority',
            'p',
            InputArgument::OPTIONAL,
            'Show tasks of this priority.'
        );
        $this->addOption(
            'project',
            null,
            InputArgument::OPTIONAL,
            'Show tasks in this project.'
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

        return $this->listTasks($input, $output);
    }

    protected function listTasks(InputInterface $input, OutputInterface $output)
    {
        $user = Auth::user();
        $project = $user->activeProject;
        $content = implode(' ', $input->getArgument('content'));
        $priority = $input->getOption('priority');

        if (!$project) {
            $output->error('No active project. Use `PROJECTS` and `PROJECT SET x`.');
            $output->setStatusCode(200);

            return ErrorCode::NO_ACTIVE_PROJECT;
        }

        if ($input->getOption('all')) {
            $completion = null;
        } elseif ($input->getOption('complete')) {
            $completion = true;
        } else {
            $completion = false;
        }

        $show_all_projects = $input->getOption('allprojects');
        $total_task_count = 0;
        [$words, $tags] = Str::splitWordsAndTags($content);

        if (!empty($words)) {
            $output->write('Searching for: "' . Format::escape(implode(' ', $words)) . '"' . Format::nl());
        }

        if (!empty($tags)) {
            $output->write('Searching for tags: ' . Format::escape(implode(' ', $tags)) . Format::nl());
        }

        $tasks = TaskModel::query()
            ->where('user_id', $user->id)
            ->when(!empty($tags), fn ($query) => $query->withTags($tags))
            ->when(!empty($words), fn ($query) => $query->search(implode(' ', $words)))
            ->when(!$show_all_projects, function ($query) use ($user) {
                $query->forProject($user->activeProject->id);
            })
            ->when($priority, fn ($query) => $query->where('priority', $priority))
            ->completed($completion)
            ->orderBy('completed', 'ASC')
            ->orderBy('priority', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->tap(function ($query) use (&$total_task_count) {
                $total_task_count = $query->count();
            })
            ->take($input->getOption('number'))
            ->get();

        if ($show_all_projects) {
            $output->write('Showing tasks from all projects.' . Format::nl());
        } else {
            $output->write('Current project: ' . Format::escape($project->name) . Format::nl());

            $all_count = TaskModel::forProject($user->activeProject->id)->count();
            $completed_count = TaskModel::forProject($user->activeProject->id)->completed(true)->count();
            $completed_str = $completed_count . ' / ' . $all_count . ' tasks complete.';

            $output->write(Format::nl() . $completed_str . Format::nl());
            $output->write(ProgressBar::make($completed_count, $all_count)->toString(20) . Format::nl() . Format::nl());
        }

        if ($tasks->isEmpty()) {
            if ($completed_count > 0) {
                $output->alert('All done!');
            } else {
                $output->alert('Nothing to do! Use TASK NEW to create a task.');
            }

            return ErrorCode::SUCCESS;
        }

        $output->write(Format::tasks($tasks, $input->getOption('dates'), $show_all_projects));

        if ($total_task_count > $tasks->count()) {
            $output->write(
                Format::nl(2) . 'Showing ' . $tasks->count() . ' tasks of ' . $total_task_count . ' total.'
            );
        }

        return ErrorCode::SUCCESS;
    }
}
