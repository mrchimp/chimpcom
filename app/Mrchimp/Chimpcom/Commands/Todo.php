<?php

/**
 * Current user's todo list
 */

namespace Mrchimp\Chimpcom\Commands;

use App\Mrchimp\Chimpcom\ProgressBar;
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
            $output->error('You must be logged in to use this command.');
            $output->setResponseCode(404);

            return 1;
        }

        $user = Auth::user();
        $project = $user->activeProject;

        if (!$project) {
            $output->error('No active project. Use `PROJECTS` and `SET PROJECT x`.');
            $output->setResponseCode(200);

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

        $tasks = Task::query()
            ->where('user_id', $user->id)
            ->search(implode(' ', $input->getArgument('search')))
            ->forProject($show_all_projects ? null : $user->activeProject->id)
            ->completed($completion)
            ->orderBy('completed', 'ASC')
            ->orderBy('priority', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->take($input->getOption('number'))
            ->get();

        $total_task_count = Task::query()
            ->where('user_id', $user->id)
            ->forProject($show_all_projects ? null : $user->activeProject->id)
            ->completed($completion)
            ->count();

        if ($show_all_projects) {
            $output->write('Showing task from all projects.<br>');
        } else {
            $output->write('Current project: ' . e($project->name) . '<br>');

            $all_count = Task::forProject($user->activeProject->id)->count();
            $completed_count = Task::forProject($user->activeProject->id)->completed(true)->count();
            $completed_str = $completed_count . ' / ' . $all_count . ' tasks complete.';

            $output->write('<br>' . $completed_str . '<br>');
            $output->write(ProgressBar::make($completed_count, $all_count)->toString(20) . '<br><br>');
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
        $output->write('<br>' . $total_task_count . ' tasks.');

        return 0;
    }
}
