<?php

namespace Mrchimp\Chimpcom\Commands;

use App\Mrchimp\Chimpcom\ProgressBar;
use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Facades\Format;
use Mrchimp\Chimpcom\Models\Task;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Current user's todo list
 */
class Todo extends Command
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('todo');
        $this->setDescription('Lists tasks on the current project.');
        $this->setHelp('By default only incomplete tasks from the current project are shown.');
        $this->addRelated('project');
        $this->addRelated('projects');
        $this->addRelated('newtask');
        $this->addRelated('done');
        $this->addRelated('priority');
        $this->addArgument(
            'search',
            InputArgument::IS_ARRAY,
            'Show only tasks that contain this.'
        );
        $this->addOption(
            'all',
            'a',
            null,
            'List complete and incomplete tasks.'
        );
        $this->addOption(
            'allprojects',
            'p',
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

        $user = Auth::user();
        $project = $user->activeProject;

        if (!$project) {
            $output->error('No active project. Use `PROJECTS` and `SET PROJECT x`.');
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

        $tasks = Task::query()
            ->where('user_id', $user->id)
            ->search(implode(' ', $input->getArgument('search')))
            ->when($show_all_projects, function ($query) use ($user) {
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

            $all_count = Task::forProject($user->activeProject->id)->count();
            $completed_count = Task::forProject($user->activeProject->id)->completed(true)->count();
            $completed_str = $completed_count . ' / ' . $all_count . ' tasks complete.';

            $output->write(Format::nl() . $completed_str . Format::nl());
            $output->write(ProgressBar::make($completed_count, $all_count)->toString(20) . Format::nl() . Format::nl());
        }

        if ($tasks->isEmpty()) {
            if ($completed_count > 0) {
                $output->alert('All done!');
            } else {
                $output->alert('Nothing to do! Use NEWTASK to create a task.');
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
}
