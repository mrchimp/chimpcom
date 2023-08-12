<?php

namespace Mrchimp\Chimpcom\Commands;

use App\Mrchimp\Chimpcom\Id;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Facades\Format;
use App\Mrchimp\Chimpcom\ProgressBar;
use Mrchimp\Chimpcom\Commands\Command;
use Mrchimp\Chimpcom\Facades\Chimpcom;
use Illuminate\Support\Facades\Session;
use Mrchimp\Chimpcom\Models\Tag;
use Mrchimp\Chimpcom\Models\Task as TaskModel;
use Mrchimp\Chimpcom\Str;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
        $this->addRelated('project');
        $this->addRelated('priority');
        $this->addArgument(
            'subcommand',
            null,
            'The subcommand to run. Available subcommands are: NEW, LIST, DONE.',
            'list'
        );
        $this->addArgument(
            'content',
            InputArgument::IS_ARRAY,
            'For NEW, this should be a description of the task. For LIST, only tasks that contain this. For DONE, this is the ID of the task to mark as completed.'
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
            'Priority of the task. Higher is more important. Default is 1.',
            1
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

            return 1;
        }

        switch ($input->getArgument('subcommand')) {
            case 'new':
                return $this->newTask($input, $output);
            case 'done':
                return $this->doneTask($input, $output);
            case 'list':
            default:
                return $this->listTasks($input, $output);
        }
    }

    protected function listTasks(InputInterface $input, OutputInterface $output)
    {
        $user = Auth::user();
        $project = $user->activeProject;
        $subcommand = $input->getArgument('subcommand');
        $content = implode(' ', $input->getArgument('content'));
        $priority = $input->getOption('priority');

        // List is the default subcommand so let's prepend the subcommand
        // to the search terms.
        if ($subcommand !== 'list') {
            $content = $subcommand . ' ' . $content;
        }

        if (!$project) {
            $output->error('No active project. Use `PROJECTS` and `PROJECT SET x`.');
            $output->setStatusCode(200);

            return 2;
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

            return 3;
        }

        $output->write(Format::tasks($tasks, $input->getOption('dates'), $show_all_projects));

        if ($total_task_count > $tasks->count()) {
            $output->write(
                Format::nl(2) . 'Showing ' . $tasks->count() . ' tasks of ' . $total_task_count . ' total.'
            );
        }

        return 0;
    }

    protected function newTask(InputInterface $input, OutputInterface $output)
    {
        $user = Auth::user();
        $content = implode(' ', $input->getArgument('content'));
        [$words, $tags] = $input->splitWordsAndTags($content);
        $description = implode(' ', $words);
        $project = $user->activeProject;

        if (!$project) {
            $output->error('No active project. Use `PROJECT LIST` and `PROJECT SET x`.');

            return 1;
        }

        $output->write('Description: ' . Format::escape($description) . Format::nl());

        if (!empty($tags)) {
            $output->write('Tags: ' . implode(', ', $tags) . Format::nl());
        }

        $task = TaskModel::create([
            'description' => $description,
            'project_id' => $project->id,
            'user_id' => $user->id,
            'priority' => $input->getOption('priority'),
            'completed' => 0,
        ]);

        foreach ($tags as $tag_name) {
            $tag = Tag::firstOrCreate([
                'tag' => $tag_name,
            ]);
            $task->tags()->save($tag);
        }

        $output->alert('Task created. Id: ' . Id::encode($task->id));

        return 0;
    }

    protected function doneTask(InputInterface $input, OutputInterface $output)
    {
        $user = Auth::user();
        $project = $user->activeProject;

        if (!$project) {
            $output->error('No active project. Use `PROJECT LIST` and `PROJECT SET x`.');
            return 2;
        }

        $task_id = Id::decode(implode(' ', $input->getArgument('content')));

        $task = TaskModel::where('id', $task_id)
            ->where('project_id', $project->id)
            ->first();

        if (!$task) {
            $output->error(Format::escape('Couldn\'t find that task.'));
            return 3;
        }

        if ($input->getOption('force')) {
            $task->completed = true;
            $task->time_completed = now();
            $task->save();
            $output->alert('Ok.');

            return 0;
        } else {
            Session::put('task_to_complete', $task->id);

            $output->useQuestionInput();
            $output->alert('Are you sure you want to mark this as complete?' . Format::nl());
            $output->write($task->description . Format::nl(2));
            $output->write('yes/no?');

            Chimpcom::setAction('done');
        }

        return 0;
    }
}
