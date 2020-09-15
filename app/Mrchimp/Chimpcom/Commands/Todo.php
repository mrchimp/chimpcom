<?php

/**
 * Current user's todo list
 */

namespace Mrchimp\Chimpcom\Commands;

use Illuminate\Support\Facades\Auth;
use Mrchimp\Chimpcom\Format;
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
            'searchterm',
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
            'completed',
            'c',
            null,
            'List completed tasks.'
        );
        $this->addOption(
            'num_tasks',
            'n',
            InputOption::VALUE_REQUIRED,
            'Number of tasks to show.',
            10
        );
    }

    /**
     * Run the command
     *
     * @todo add ability to show dates
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Auth::check()) {
            $output->error('You must be logged in to use this command.');

            return 1;
        }

        $user = Auth::user();

        $project = $user->activeProject;

        if (!$project) {
            $output->error('No active project. Use `PROJECTS` and `SET PROJECT x`.');

            return 2;
        }

        $show_all_items    = $input->getOption('all');
        $show_all_projects = $input->getOption('allprojects');
        $show_completed    = $input->getOption('completed');

        if ($show_all_items) {
            $completion = null;
        } elseif ($show_completed) {
            $completion = true;
        } else {
            $completion = false;
        }

        $tasks = Task::where('user_id', $user->id);

        if ($show_all_projects) {
            $output->write('Showing task from all projects.<br>');
        } else {
            $output->write('Current project: ' . e($project->name) . '<br>');
        }

        $num_tasks = $input->getOption('num_tasks');

        $search_term = implode(' ', $input->getArgument('searchterm'));

        $tasks = $tasks->search($search_term)
            ->forProject($show_all_projects ? null : $user->activeProject->id)
            ->completed($completion)
            ->orderBy('completed', 'ASC')
            ->orderBy('priority', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->take($num_tasks)
            ->get();

        if (!$tasks) {
            $output->alert('Nothing to do!');
            return 3;
        }

        $total_task_count = Task::where('user_id', $user->id)
            ->forProject($show_all_projects ? null : $user->activeProject->id)
            ->completed($completion)
            ->count();

        if (!$show_all_projects) {
            $all_count = Task::forProject($user->activeProject->id)->count();
            $completed_count = Task::forProject($user->activeProject->id)->completed(true)->count();
            $chunks = 20;
            $done_chunks = ($completed_count / $all_count) * $chunks;
            $completed_str = $completed_count . ' / ' . $all_count . ' tasks complete.';

            $done_pips = '';
            for ($i=0; $i < $done_chunks; $i++) {
                $done_pips .= '▰';
            }

            $not_done_pips = '';
            for ($i=0; $i < $chunks - $done_chunks; $i++) {
                $not_done_pips .= '▱';
            }

            $output->write('<br>' . $completed_str . '<br>' . $done_pips . Format::grey($not_done_pips) . '<br><br>');
        }

        $output->write(Format::tasks($tasks));
        $output->write('<br>' . $total_task_count . ' tasks.');

        return 0;
    }
}
